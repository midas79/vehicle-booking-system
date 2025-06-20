@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Total Vehicles</h5>
                        <h2 class="mb-0">{{ \App\Models\Vehicle::count() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Active Bookings</h5>
                        <h2 class="mb-0">{{ \App\Models\Booking::where('status', 'approved')->count() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Pending Approvals</h5>
                        <h2 class="mb-0">{{ \App\Models\Booking::where('status', 'pending')->count() }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <!-- Vehicle Usage Chart -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Vehicle Usage This Month</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="vehicleUsageChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Vehicle Type Distribution -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Vehicle Type Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="vehicleTypeChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Booking Status Chart -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Booking Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="bookingStatusChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Monthly Trend -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Monthly Booking Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyTrendChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Vehicle Usage Chart
        const vehicleUsageCtx = document.getElementById('vehicleUsageChart').getContext('2d');
        new Chart(vehicleUsageCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($vehicleUsageData->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d/m'))) !!},
                datasets: [{
                    label: 'Bookings',
                    data: {!! json_encode($vehicleUsageData->pluck('total')) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Vehicle Type Chart
        const vehicleTypeCtx = document.getElementById('vehicleTypeChart').getContext('2d');
        new Chart(vehicleTypeCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($vehicleTypeData->pluck('type')) !!},
                datasets: [{
                    data: {!! json_encode($vehicleTypeData->pluck('total')) !!},
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Booking Status Chart
        const bookingStatusCtx = document.getElementById('bookingStatusChart').getContext('2d');
        new Chart(bookingStatusCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($bookingStatusData->pluck('status')->map(fn($s) => ucfirst($s))) !!},
                datasets: [{
                    label: 'Count',
                    data: {!! json_encode($bookingStatusData->pluck('total')) !!},
                    backgroundColor: {!! json_encode($bookingStatusData->pluck('status')->map(function ($status) {
        return match ($status) {
            'pending' => 'rgba(255, 205, 86, 0.8)',
            'approved' => 'rgba(75, 192, 192, 0.8)',
            'rejected' => 'rgba(255, 99, 132, 0.8)',
            'completed' => 'rgba(54, 162, 235, 0.8)',
            default => 'rgba(201, 203, 207, 0.8)'
        };
    })) !!}
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Monthly Trend Chart - Using static data for now
        const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');

        // Get monthly data from controller
        @php
            $monthlyData = \App\Models\Booking::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $labels = [];
            $values = [];

            for ($i = 1; $i <= 12; $i++) {
                $labels[] = date('F', mktime(0, 0, 0, $i, 1));
                $monthData = $monthlyData->firstWhere('month', $i);
                $values[] = $monthData ? $monthData->total : 0;
            }
        @endphp

        new Chart(monthlyTrendCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Total Bookings',
                    data: {!! json_encode($values) !!},
                    backgroundColor: 'rgba(153, 102, 255, 0.8)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
@endsection