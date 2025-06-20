@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Vehicle Monitoring Dashboard</h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            @foreach($vehicleData as $data)
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">{{ $data['vehicle']->name }}
                                                ({{ $data['vehicle']->license_plate }})</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>Current KM:</strong></p>
                                                    <h4>{{ number_format($data['current_km']) }}</h4>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>Total Distance:</strong></p>
                                                    <h4>{{ number_format($data['total_distance']) }} km</h4>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>Fuel Consumption:</strong></p>
                                                    <p>{{ number_format($data['total_fuel'], 2) }} L</p>
                                                    <p class="text-muted">Avg: {{ $data['avg_fuel_consumption'] }} km/L</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>Service Cost:</strong></p>
                                                    <p>Rp {{ number_format($data['total_service_cost']) }}</p>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-12">
                                                    <p class="mb-1"><strong>Last Service:</strong></p>
                                                    @if($data['last_service'])
                                                        <p>{{ $data['last_service']->service_date->format('d/m/Y') }}
                                                            at {{ number_format($data['last_service']->end_km) }} km</p>
                                                        <p class="text-muted">{{ number_format($data['km_since_last_service']) }} km
                                                            since last service</p>
                                                    @else
                                                        <p class="text-muted">No service record</p>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($data['next_service'])
                                                <div class="alert alert-warning mt-3 mb-0">
                                                    <small><strong>Next Service:</strong>
                                                        {{ $data['next_service']->next_service_date->format('d/m/Y') }}
                                                        or at {{ number_format($data['next_service']->next_service_km) }} km</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection