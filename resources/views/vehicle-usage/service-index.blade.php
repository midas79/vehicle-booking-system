@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <!-- Service Alerts -->
                @if($overdueServices->count() > 0)
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Overdue Services</h6>
                        <div class="row">
                            @foreach($overdueServices as $reminder)
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <strong>{{ $reminder['vehicle']->name }}</strong>
                                            ({{ $reminder['vehicle']->license_plate }})<br>
                                            <small>Current: {{ number_format($reminder['current_km']) }} km |
                                                {{ $reminder['status_text'] }}</small>
                                        </div>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="scheduleService({{ $reminder['vehicle']->id }})">
                                            Schedule Now
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($upcomingServices->count() > 0)
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-clock"></i> Upcoming Services</h6>
                        <div class="row">
                            @foreach($upcomingServices as $reminder)
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <strong>{{ $reminder['vehicle']->name }}</strong>
                                            ({{ $reminder['vehicle']->license_plate }})<br>
                                            <small>Current: {{ number_format($reminder['current_km']) }} km |
                                                {{ $reminder['status_text'] }}</small>
                                        </div>
                                        <button class="btn btn-sm btn-warning"
                                            onclick="scheduleService({{ $reminder['vehicle']->id }})">
                                            Schedule
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Service Management</h5>
                        <div>
                            <button class="btn btn-info btn-sm me-2" data-bs-toggle="modal"
                                data-bs-target="#serviceStatusModal">
                                <i class="fas fa-info-circle"></i> All Vehicle Status
                            </button>
                            <a href="{{ route('vehicle-usage.create-service') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Service Record
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Service History Table -->
                        <h6 class="mb-3">Service History</h6>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Service Date</th>
                                        <th>Vehicle</th>
                                        <th>Service Type</th>
                                        <th>Vendor</th>
                                        <th>KM at Service</th>
                                        <th>Cost</th>
                                        <th>Next Service</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($services as $service)
                                        <tr>
                                            <td>{{ $service->service_date->format('d/m/Y') }}</td>
                                            <td>
                                                {{ $service->vehicle->name }}<br>
                                                <small class="text-muted">{{ $service->vehicle->license_plate }}</small>
                                            </td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $service->service_type)) }}</td>
                                            <td>{{ $service->service_vendor }}</td>
                                            <td>{{ number_format($service->start_km) }} km</td>
                                            <td>Rp {{ number_format($service->service_cost) }}</td>
                                            <td>
                                                @if($service->next_service_date)
                                                    {{ $service->next_service_date->format('d/m/Y') }}
                                                @endif
                                                @if($service->next_service_km)
                                                    <br><small>or {{ number_format($service->next_service_km) }} km</small>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No service records found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $services->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Status Modal -->
    <div class="modal fade" id="serviceStatusModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">All Vehicle Service Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Vehicle</th>
                                    <th>Current KM</th>
                                    <th>Last Service</th>
                                    <th>Next Service</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overdueServices->merge($upcomingServices)->merge($okServices) as $reminder)
                                    <tr>
                                        <td>
                                            {{ $reminder['vehicle']->name }}<br>
                                            <small class="text-muted">{{ $reminder['vehicle']->license_plate }}</small>
                                        </td>
                                        <td>{{ number_format($reminder['current_km']) }} km</td>
                                        <td>
                                            @if($reminder['last_service'])
                                                {{ number_format($reminder['last_service_km']) }} km<br>
                                                <small>{{ $reminder['last_service']->service_date->format('d/m/Y') }}</small>
                                            @else
                                                <span class="text-muted">No record</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($reminder['next_service_km']) }} km</td>
                                        <td>
                                            <span class="badge bg-{{ $reminder['status_color'] }}">
                                                {{ $reminder['status_text'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function scheduleService(vehicleId) {
            // Redirect to create service with pre-selected vehicle
            window.location.href = "{{ route('vehicle-usage.create-service') }}?vehicle_id=" + vehicleId;
        }
    </script>
@endsection