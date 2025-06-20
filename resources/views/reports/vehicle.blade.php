{{-- resources/views/reports/vehicles.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Vehicle Monitoring Report</h2>

        <div class="row">
            @foreach($vehicleData as $data)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ $data['vehicle']->name }} - {{ $data['vehicle']->license_plate }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Usage Information</h6>
                                    <p>Current KM: <strong>{{ number_format($data['current_km']) }}</strong></p>
                                    <p>Total Distance: <strong>{{ number_format($data['total_distance']) }} km</strong></p>
                                    <p>Total Fuel Used: <strong>{{ number_format($data['total_fuel'], 2) }} L</strong></p>
                                    <p>Avg Consumption: <strong>{{ $data['avg_fuel_consumption'] }} km/L</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Service Information</h6>
                                    @if($data['last_service'])
                                        <p>Last Service: <strong>{{ $data['last_service']->actual_date->format('d/m/Y') }}</strong>
                                        </p>
                                    @endif
                                    @if($data['next_service'])
                                        <p>Next Service:
                                            <strong
                                                class="{{ $data['next_service']->scheduled_date < now() ? 'text-danger' : '' }}">
                                                {{ $data['next_service']->scheduled_date->format('d/m/Y') }}
                                            </strong>
                                        </p>
                                    @endif
                                    <p>Total Service Cost: <strong>Rp {{ number_format($data['total_service_cost']) }}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection