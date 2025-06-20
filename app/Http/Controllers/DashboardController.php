<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Vehicle usage statistics for current month
        $vehicleUsageData = Booking::select(
            DB::raw('DATE(start_date) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->where('status', 'approved')
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Vehicle type distribution
        $vehicleTypeData = Vehicle::select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->get();

        // Booking status distribution
        $bookingStatusData = Booking::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        $serviceAlerts = Vehicle::with([
            'usages' => function ($query) {
                $query->latest('end_km');
            }
        ])->get()->filter(function ($vehicle) {
            return $vehicle->service_status !== 'ok';
        })->count();

        return view('dashboard', compact('vehicleUsageData', 'vehicleTypeData', 'bookingStatusData', 'serviceAlerts'));
    }

    public function monthlyTrend()
    {
        $data = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $values = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = date('F', mktime(0, 0, 0, $i, 1));
            $monthData = $data->firstWhere('month', $i);
            $values[] = $monthData ? $monthData->total : 0;
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }
}