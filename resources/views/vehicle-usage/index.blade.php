
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Vehicle Usage Records</h5>
                        <div>
                            <a href="{{ route('vehicle-usage.create-service') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-wrench"></i> Add Service Record
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">Total Distance This Month</h6>
                                        <h3>{{ number_format($monthlyStats['total_distance'] ?? 0) }} km</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">Total Fuel Used</h6>
                                        <h3>{{ number_format($monthlyStats['total_fuel'] ?? 0, 2) }} L</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-dark">
                                    <div class="card-body">
                                        <h6 class="card-title">Service This Month</h6>
                                        <h3>{{ $monthlyStats['service_count'] ?? 0 }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">Avg Fuel Efficiency</h6>
                                        <h3>{{ number_format($monthlyStats['avg_efficiency'] ?? 0, 2) }} km/L</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Tabs -->
                        <ul class="nav nav-tabs mb-3">
                            <li class="nav-item">
                                <a class="nav-link {{ !request('type') ? 'active' : '' }}"
                                    href="{{ route('vehicle-usage.index') }}">All Records</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('type') == 'trip' ? 'active' : '' }}"
                                    href="{{ route('vehicle-usage.index', ['type' => 'trip']) }}">Trips Only</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('type') == 'service' ? 'active' : '' }}"
                                    href="{{ route('vehicle-usage.index', ['type' => 'service']) }}">Services Only</a>
                            </li>
                        </ul>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Vehicle</th>
                                        <th>Type</th>
                                        <th>Start KM</th>
                                        <th>End KM</th>
                                        <th>Distance</th>
                                        <th>Fuel/Cost</th>
                                        <th>Driver/Vendor</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($usages as $usage)
                                        <tr>
                                            <td>{{ $usage->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                {{ $usage->vehicle->name }}<br>
                                                <small class="text-muted">{{ $usage->vehicle->license_plate }}</small>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $usage->usage_type == 'trip' ? 'primary' : 'warning' }}">
                                                    {{ ucfirst($usage->usage_type) }}
                                                </span>
                                                @if($usage->usage_type == 'service')
                                                    <br><small>{{ $usage->service_type }}</small>
                                                @endif
                                            </td>
                                            <td>{{ number_format($usage->start_km) }}</td>
                                            <td>{{ number_format($usage->end_km) }}</td>
                                            <td>{{ number_format($usage->distance) }} km</td>
                                            <td>
                                                @if($usage->usage_type == 'trip')
                                                    {{ $usage->fuel_used }} L<br>
                                                    <small class="text-muted">{{ $usage->fuel_efficiency }} km/L</small>
                                                @else
                                                    Rp {{ number_format($usage->service_cost ?? 0) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($usage->usage_type == 'trip' && $usage->booking)
                                                    {{ $usage->booking->driver->name }}
                                                @elseif($usage->usage_type == 'service')
                                                    {{ $usage->service_vendor }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($usage->notes)
                                                    <small>{{ Str::limit($usage->notes, 30) }}</small>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No usage records found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $usages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection