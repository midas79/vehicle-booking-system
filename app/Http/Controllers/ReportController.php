<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Vehicle;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        $bookings = collect(); // Initialize as empty collection

        // If dates are provided in request, fetch bookings
        if ($request->has(['start_date', 'end_date'])) {
            $bookings = Booking::with(['user', 'vehicle', 'driver', 'approvals.approver'])
                ->where(function ($query) use ($startDate, $endDate) {
                    // Get bookings that start OR end within the date range
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        // Also include bookings that span across the date range
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
                })
                ->orderBy('start_date', 'desc')
                ->paginate(10)
                ->withQueryString();
        }

        return view('reports.index', compact('bookings', 'startDate', 'endDate'));
    }

    public function vehicleReport(Request $request)
    {
        $vehicles = Vehicle::with(['services', 'usages'])->get();

        $vehicleData = $vehicles->map(function ($vehicle) {
            $totalUsages = $vehicle->usages->count();
            $totalDistance = $vehicle->usages->sum(function ($usage) {
                return $usage->end_km - $usage->start_km;
            });
            $totalFuel = $vehicle->usages->sum('fuel_used');

            return [
                'vehicle' => $vehicle,
                'current_km' => $vehicle->getCurrentKm(),
                'total_distance' => $totalDistance,
                'total_fuel' => $totalFuel,
                'avg_fuel_consumption' => $vehicle->getAverageFuelConsumption(),
                'last_service' => $vehicle->services()->where('status', 'completed')->latest('actual_date')->first(),
                'next_service' => $vehicle->services()->where('status', 'scheduled')->oldest('scheduled_date')->first(),
                'total_service_cost' => $vehicle->services()->where('status', 'completed')->sum('cost')
            ];
        });

        return view('reports.vehicles', compact('vehicleData'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $format = $request->input('format', 'excel');

        if ($format === 'csv') {
            return $this->exportCsv($request);
        }

        return $this->exportExcel($request);
    }

    private function exportCsv(Request $request)
    {
        $bookings = $this->getBookings($request);
        $filename = 'booking_report_' . date('YmdHis') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($file, [
                'Booking Number',
                'Requester',
                'Vehicle',
                'License Plate',
                'Driver',
                'Purpose',
                'Destination',
                'Start Date',
                'End Date',
                'Status',
                'Approver Level 1',
                'Status L1',
                'Approver Level 2',
                'Status L2',
                'Created At'
            ]);

            // Data
            foreach ($bookings as $booking) {
                $approval1 = $booking->approvals->where('level', 1)->first();
                $approval2 = $booking->approvals->where('level', 2)->first();

                fputcsv($file, [
                    $booking->booking_number,
                    $booking->user->name,
                    $booking->vehicle->name,
                    $booking->vehicle->license_plate,
                    $booking->driver->name,
                    $booking->purpose,
                    $booking->destination,
                    $booking->start_date->format('Y-m-d H:i'),
                    $booking->end_date->format('Y-m-d H:i'),
                    ucfirst($booking->status),
                    $approval1 ? $approval1->approver->name : '-',
                    $approval1 ? ucfirst($approval1->status) : '-',
                    $approval2 ? $approval2->approver->name : '-',
                    $approval2 ? ucfirst($approval2->status) : '-',
                    $booking->created_at->format('Y-m-d H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel(Request $request)
    {
        $bookings = $this->getBookings($request);
        $filename = 'booking_report_' . date('YmdHis') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $content = $this->generateExcelContent($bookings);

        return response($content, 200, $headers);
    }

    private function getBookings(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        return Booking::with(['user', 'vehicle', 'driver', 'approvals.approver'])
            ->where(function ($query) use ($startDate, $endDate) {
                // Get bookings that start OR end within the date range
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    // Also include bookings that span across the date range
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
            })
            ->orderBy('start_date', 'desc')
            ->get();
    }

    private function generateExcelContent($bookings)
    {
        $html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head><meta charset="UTF-8">';
        $html .= '<style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid black; padding: 8px; text-align: left; }
            th { background-color: #4CAF50; color: white; font-weight: bold; }
            tr:nth-child(even) { background-color: #f2f2f2; }
        </style></head><body>';

        $html .= '<h2>Booking Report</h2>';
        $html .= '<p>Period: ' . request()->start_date . ' to ' . request()->end_date . '</p>';

        $html .= '<table>';
        $html .= '<tr>
            <th>Booking Number</th>
            <th>Requester</th>
            <th>Vehicle</th>
            <th>License Plate</th>
            <th>Driver</th>
            <th>Purpose</th>
            <th>Destination</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Approver L1</th>
            <th>Status L1</th>
            <th>Approver L2</th>
            <th>Status L2</th>
            <th>Created At</th>
        </tr>';

        foreach ($bookings as $booking) {
            $approval1 = $booking->approvals->where('level', 1)->first();
            $approval2 = $booking->approvals->where('level', 2)->first();

            $html .= '<tr>';
            $html .= '<td>' . $booking->booking_number . '</td>';
            $html .= '<td>' . $booking->user->name . '</td>';
            $html .= '<td>' . $booking->vehicle->name . '</td>';
            $html .= '<td>' . $booking->vehicle->license_plate . '</td>';
            $html .= '<td>' . $booking->driver->name . '</td>';
            $html .= '<td>' . $booking->purpose . '</td>';
            $html .= '<td>' . $booking->destination . '</td>';
            $html .= '<td>' . $booking->start_date->format('Y-m-d H:i') . '</td>';
            $html .= '<td>' . $booking->end_date->format('Y-m-d H:i') . '</td>';
            $html .= '<td>' . ucfirst($booking->status) . '</td>';
            $html .= '<td>' . ($approval1 ? $approval1->approver->name : '-') . '</td>';
            $html .= '<td>' . ($approval1 ? ucfirst($approval1->status) : '-') . '</td>';
            $html .= '<td>' . ($approval2 ? $approval2->approver->name : '-') . '</td>';
            $html .= '<td>' . ($approval2 ? ucfirst($approval2->status) : '-') . '</td>';
            $html .= '<td>' . $booking->created_at->format('Y-m-d H:i') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '<p>Total Records: ' . $bookings->count() . '</p>';
        $html .= '</body></html>';

        return $html;
    }
}