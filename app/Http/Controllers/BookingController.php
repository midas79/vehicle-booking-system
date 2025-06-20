<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;
use App\Models\Approval;
use App\Models\VehicleUsage;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class BookingController extends Controller
{
    protected $logService;

    public function __construct(ActivityLogService $logService)
    {
        $this->logService = $logService;
    }

    public function index(Request $request)
    {
        $query = Booking::with(['vehicle', 'driver', 'approvals.approver', 'vehicleUsage']);

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected', 'completed'])) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('start_date', [$request->from_date, $request->to_date]);
        }

        // If user is approver, show bookings they need to approve
        if (Auth::user()->isApprover() && !Auth::user()->isAdmin()) {
            $query->whereHas('approvals', function ($q) {
                $q->where('approver_id', Auth::id());
            });
        }

        $bookings = $query->latest()->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        // Get available vehicles with their last usage info
        $vehicles = Vehicle::where('status', 'available')
            ->with([
                'usages' => function ($query) {
                    $query->latest('end_km')->limit(1);
                }
            ])
            ->get()
            ->map(function ($vehicle) {
                $lastUsage = $vehicle->usages->first();
                $vehicle->current_km = $lastUsage ? $lastUsage->end_km : 0;

                // Check if vehicle needs service
                $lastService = $vehicle->usages()
                    ->where('usage_type', 'service')
                    ->latest('service_date')
                    ->first();

                if ($lastService) {
                    $vehicle->needs_service = false;
                    if ($lastService->next_service_date && $lastService->next_service_date <= now()) {
                        $vehicle->needs_service = true;
                    }
                    if ($lastService->next_service_km && $vehicle->current_km >= $lastService->next_service_km) {
                        $vehicle->needs_service = true;
                    }
                } else {
                    $vehicle->needs_service = false;
                }

                return $vehicle;
            });

        $drivers = Driver::where('status', 'available')->get();
        $approvers = User::where('role', 'approver')->orderBy('level')->get();

        return view('bookings.create', compact('vehicles', 'drivers', 'approvers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'purpose' => 'required|string',
            'destination' => 'required|string',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'approver_level_1' => 'required|exists:users,id',
            'approver_level_2' => 'required|exists:users,id|different:approver_level_1',
            'estimated_km' => 'nullable|integer|min:1',
            'estimated_fuel' => 'nullable|numeric|min:0'
        ]);

        // Check if vehicle needs service
        $vehicle = Vehicle::find($validated['vehicle_id']);
        $lastService = VehicleUsage::where('vehicle_id', $vehicle->id)
            ->where('usage_type', 'service')
            ->latest('service_date')
            ->first();

        if ($lastService) {
            $currentKm = VehicleUsage::where('vehicle_id', $vehicle->id)
                ->max('end_km') ?? 0;

            $needsService = false;
            if ($lastService->next_service_date && $lastService->next_service_date <= now()) {
                $needsService = true;
            }
            if ($lastService->next_service_km && $currentKm >= $lastService->next_service_km) {
                $needsService = true;
            }

            if ($needsService && !$request->has('force_booking')) {
                return back()->withInput()
                    ->with('warning', 'Selected vehicle needs service. Please consider using another vehicle or schedule service first.')
                    ->with('show_force_booking', true);
            }
        }

        DB::transaction(function () use ($validated, $request) {
            // Create booking
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'vehicle_id' => $validated['vehicle_id'],
                'driver_id' => $validated['driver_id'],
                'purpose' => $validated['purpose'],
                'destination' => $validated['destination'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'estimated_km' => $validated['estimated_km'] ?? null,
                'estimated_fuel' => $validated['estimated_fuel'] ?? null,
                'status' => 'pending'
            ]);

            // Create approval records
            Approval::create([
                'booking_id' => $booking->id,
                'approver_id' => $validated['approver_level_1'],
                'level' => 1,
                'status' => 'pending'
            ]);

            Approval::create([
                'booking_id' => $booking->id,
                'approver_id' => $validated['approver_level_2'],
                'level' => 2,
                'status' => 'pending'
            ]);

            // Update vehicle and driver status
            Vehicle::find($validated['vehicle_id'])->update(['status' => 'in_use']);
            Driver::find($validated['driver_id'])->update(['status' => 'on_duty']);

            // Log activity
            $this->logService->log('create_booking', 'Booking', $booking->id, 'Created booking ' . $booking->booking_number);
        });

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully');
    }

    public function approve(Booking $booking): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $approval = $booking->approvals()
                ->where('status', 'pending')
                ->orderBy('level')
                ->first();
        } elseif ($user->isApprover()) {
            $approval = $booking->approvals()
                ->where('approver_id', $user->id)
                ->where('status', 'pending')
                ->first();

            // Check if previous level is approved
            if ($approval && $approval->level > 1) {
                $previousApproval = $booking->approvals()
                    ->where('level', $approval->level - 1)
                    ->first();

                if (!$previousApproval || $previousApproval->status !== 'approved') {
                    return back()->with('error', 'Previous level approval is required.');
                }
            }
        } else {
            abort(403);
        }

        if (!$approval) {
            return back()->with('error', 'You are not authorized to approve this booking');
        }

        $approval->update([
            'status' => 'approved',
            'approved_at' => now()
        ]);

        // Check if all approvals are done
        if ($booking->isFullyApproved()) {
            $booking->update(['status' => 'approved']);
        }

        $this->logService->log('approve_booking', 'Booking', $booking->id, 'Approved booking ' . $booking->booking_number);

        return back()->with('success', 'Booking approved successfully');
    }

    public function reject(Booking $booking, Request $request): RedirectResponse
    {
        $request->validate([
            'notes' => 'required|string|max:255'
        ]);

        $user = Auth::user();

        $approval = $booking->approvals()
            ->where('approver_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$approval && !$user->isAdmin()) {
            return back()->with('error', 'You are not authorized to reject this booking');
        }

        if ($user->isAdmin() && !$approval) {
            $approval = $booking->approvals()
                ->where('status', 'pending')
                ->first();
        }

        $approval->update([
            'status' => 'rejected',
            'notes' => $request->notes,
            'approved_at' => now()
        ]);

        $booking->update(['status' => 'rejected']);

        // Release vehicle and driver
        $booking->vehicle->update(['status' => 'available']);
        $booking->driver->update(['status' => 'available']);

        $this->logService->log('reject_booking', 'Booking', $booking->id, 'Rejected booking ' . $booking->booking_number);

        return back()->with('success', 'Booking rejected');
    }
}