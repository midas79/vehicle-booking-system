<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\VehicleUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleUsageController extends Controller
{
    // Method index tidak perlu diubah
    public function index(Request $request)
    {
        $query = VehicleUsage::with(['vehicle', 'booking.driver']);

        // Filter by type if specified
        if ($request->has('type') && in_array($request->type, ['trip', 'service', 'maintenance'])) {
            $query->where('usage_type', $request->type);
        }

        $usages = $query->latest()->paginate(15);

        // Calculate monthly statistics
        $monthlyStats = [
            'total_distance' => VehicleUsage::whereMonth('created_at', now()->month)
                ->where('usage_type', 'trip')
                ->sum(DB::raw('end_km - start_km')),
            'total_fuel' => VehicleUsage::whereMonth('created_at', now()->month)
                ->where('usage_type', 'trip')
                ->sum('fuel_used'),
            'service_count' => VehicleUsage::whereMonth('created_at', now()->month)
                ->where('usage_type', 'service')
                ->count(),
        ];

        $monthlyStats['avg_efficiency'] = $monthlyStats['total_fuel'] > 0
            ? $monthlyStats['total_distance'] / $monthlyStats['total_fuel']
            : 0;

        return view('vehicle-usage.index', compact('usages', 'monthlyStats'));
    }

    // Method create untuk record usage dari booking
    public function create(Booking $booking)
    {
        // Pastikan booking sudah approved dan belum ada usage record
        if ($booking->status !== 'approved') {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking must be approved before recording usage.');
        }

        if ($booking->vehicleUsage()->exists()) {
            return redirect()->route('bookings.index')
                ->with('error', 'Usage already recorded for this booking.');
        }

        return view('vehicle-usage.create', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'start_km' => 'required|integer|min:0',
            'end_km' => 'required|integer|gt:start_km',
            'fuel_used' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated, $booking) {
            VehicleUsage::create([
                'booking_id' => $booking->id,
                'vehicle_id' => $booking->vehicle_id,
                'usage_type' => 'trip',
                'start_km' => $validated['start_km'],
                'end_km' => $validated['end_km'],
                'fuel_used' => $validated['fuel_used'],
                'notes' => $validated['notes']
            ]);

            $booking->update(['status' => 'completed']);
            $booking->vehicle->update(['status' => 'available']);
            $booking->driver->update(['status' => 'available']);
        });

        return redirect()->route('bookings.index')
            ->with('success', 'Vehicle usage recorded successfully');
    }

    // Service management methods
    public function serviceIndex()
    {
        // Get actual service records
        $services = VehicleUsage::with('vehicle')
            ->whereIn('usage_type', ['service', 'maintenance'])
            ->latest('service_date')
            ->paginate(10);

        // Get all vehicles untuk service reminders
        $vehicles = Vehicle::with([
            'usages' => function ($query) {
                $query->latest('end_km');
            }
        ])->get();

        // Calculate service reminders
        $serviceReminders = $vehicles->map(function ($vehicle) {
            return [
                'vehicle' => $vehicle,
                'current_km' => $vehicle->current_km,
                'last_service_km' => $vehicle->last_service_km,
                'km_since_service' => $vehicle->km_since_last_service,
                'next_service_km' => $vehicle->next_service_km,
                'km_until_service' => $vehicle->km_until_next_service,
                'status' => $vehicle->service_status,
                'status_color' => $vehicle->service_status_color,
                'status_text' => $vehicle->service_status_text,
                'last_service' => $vehicle->usages()
                    ->where('usage_type', 'service')
                    ->latest('service_date')
                    ->first()
            ];
        })->sortBy('km_until_service');

        // Separate overdue and upcoming
        $overdueServices = $serviceReminders->filter(fn($item) => $item['status'] === 'overdue');
        $upcomingServices = $serviceReminders->filter(fn($item) => $item['status'] === 'due_soon');
        $okServices = $serviceReminders->filter(fn($item) => $item['status'] === 'ok');

        return view('vehicle-usage.service-index', compact(
            'services',
            'overdueServices',
            'upcomingServices',
            'okServices'
        ));
    }

    public function createService(Request $request)
    {
        $vehicles = Vehicle::where('status', 'available')->get();
        $selectedVehicleId = $request->get('vehicle_id');

        return view('vehicle-usage.create-service', compact('vehicles', 'selectedVehicleId'));
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_type' => 'required|string',
            'service_date' => 'required|date',
            'start_km' => 'required|integer|min:0',
            'end_km' => 'required|integer|gte:start_km',
            'service_cost' => 'required|numeric|min:0',
            'service_vendor' => 'required|string',
            'next_service_date' => 'nullable|date|after:service_date',
            'next_service_km' => 'nullable|integer|gt:end_km',
            'notes' => 'nullable|string'
        ]);

        $validated['usage_type'] = 'service';

        VehicleUsage::create($validated);

        // Update vehicle last service date
        Vehicle::find($validated['vehicle_id'])->update([
            'last_service_date' => $validated['service_date']
        ]);

        return redirect()->route('vehicle-usage.service-index')
            ->with('success', 'Service record created successfully');
    }

    // Vehicle monitoring
    public function monitoring()
    {
        $vehicles = Vehicle::with([
            'usages' => function ($query) {
                $query->latest('created_at');
            }
        ])->get();

        $vehicleData = $vehicles->map(function ($vehicle) {
            $trips = $vehicle->usages->where('usage_type', 'trip');
            $services = $vehicle->usages->whereIn('usage_type', ['service', 'maintenance']);

            $totalDistance = $trips->sum(function ($usage) {
                return $usage->end_km - $usage->start_km;
            });

            $totalFuel = $trips->sum('fuel_used');
            $currentKm = $vehicle->usages->max('end_km') ?? 0;

            $lastService = $services->sortByDesc('service_date')->first();

            $nextService = null;
            if ($lastService && $lastService->next_service_date) {
                $nextService = $lastService;
            }

            return [
                'vehicle' => $vehicle,
                'current_km' => $currentKm,
                'total_distance' => $totalDistance,
                'total_fuel' => $totalFuel,
                'avg_fuel_consumption' => $totalFuel > 0 ? round($totalDistance / $totalFuel, 2) : 0,
                'last_service' => $lastService,
                'next_service' => $nextService,
                'total_service_cost' => $services->sum('service_cost'),
                'km_since_last_service' => $lastService ? ($currentKm - $lastService->end_km) : $currentKm
            ];
        });

        return view('vehicle-usage.monitoring', compact('vehicleData'));
    }
}