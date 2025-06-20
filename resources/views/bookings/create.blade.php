@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Create New Booking</h5>
                    </div>

                    <div class="card-body">
                        @if(session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                {{ session('warning') }}
                                @if(session('show_force_booking'))
                                    <hr>
                                    <form method="POST" action="{{ route('bookings.store') }}" class="d-inline">
                                        @csrf
                                        @foreach(old() as $key => $value)
                                            @if(is_array($value))
                                                @foreach($value as $arrayKey => $arrayValue)
                                                    <input type="hidden" name="{{ $key }}[{{ $arrayKey }}]" value="{{ $arrayValue }}">
                                                @endforeach
                                            @else
                                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                            @endif
                                        @endforeach
                                        <input type="hidden" name="force_booking" value="1">
                                        <button type="submit" class="btn btn-sm btn-warning">Proceed Anyway</button>
                                    </form>
                                @endif
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('bookings.store') }}" id="bookingForm">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="vehicle_id" class="form-label">Vehicle</label>
                                    <select class="form-select @error('vehicle_id') is-invalid @enderror" name="vehicle_id"
                                        id="vehicle_id" required>
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" 
                                                data-km="{{ $vehicle->current_km }}"
                                                data-fuel="{{ $vehicle->fuel_consumption }}"
                                                data-needs-service="{{ $vehicle->needs_service ? '1' : '0' }}"
                                                {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->name }} - {{ $vehicle->license_plate }}
                                                ({{ ucfirst($vehicle->type) }})
                                                @if($vehicle->needs_service)
                                                    ⚠️ Needs Service
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted" id="vehicle-info"></small>
                                </div>

                                <div class="col-md-6">
                                    <label for="driver_id" class="form-label">Driver</label>
                                    <select class="form-select @error('driver_id') is-invalid @enderror" name="driver_id"
                                        required>
                                        <option value="">Select Driver</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                                {{ $driver->name }} - {{ $driver->license_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('driver_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="purpose" class="form-label">Purpose</label>
                                <textarea class="form-control @error('purpose') is-invalid @enderror" name="purpose"
                                    rows="3" required>{{ old('purpose') }}</textarea>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="destination" class="form-label">Destination</label>
                                <input type="text" class="form-control @error('destination') is-invalid @enderror"
                                    name="destination" value="{{ old('destination') }}" required>
                                @error('destination')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">Start Date & Time</label>
                                    <input type="datetime-local"
                                        class="form-control @error('start_date') is-invalid @enderror" name="start_date"
                                        value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="end_date" class="form-label">End Date & Time</label>
                                    <input type="datetime-local"
                                        class="form-control @error('end_date') is-invalid @enderror" name="end_date"
                                        value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- New fields for estimation -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="estimated_km" class="form-label">Estimated Distance (KM)</label>
                                    <input type="number" class="form-control @error('estimated_km') is-invalid @enderror"
                                        name="estimated_km" id="estimated_km" value="{{ old('estimated_km') }}" min="1">
                                    @error('estimated_km')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="estimated_fuel" class="form-label">Estimated Fuel (Liters)</label>
                                    <input type="number" step="0.01" class="form-control @error('estimated_fuel') is-invalid @enderror"
                                        name="estimated_fuel" id="estimated_fuel" value="{{ old('estimated_fuel') }}" min="0" readonly>
                                    @error('estimated_fuel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Auto-calculated based on vehicle consumption</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="approver_level_1" class="form-label">Level 1 Approver</label>
                                    <select class="form-select @error('approver_level_1') is-invalid @enderror"
                                        name="approver_level_1" required>
                                        <option value="">Select Approver</option>
                                        @foreach($approvers->where('level', 1) as $approver)
                                            <option value="{{ $approver->id }}" {{ old('approver_level_1') == $approver->id ? 'selected' : '' }}>
                                                {{ $approver->name }} - {{ $approver->region->name ?? 'All Regions' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('approver_level_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="approver_level_2" class="form-label">Level 2 Approver</label>
                                    <select class="form-select @error('approver_level_2') is-invalid @enderror"
                                        name="approver_level_2" required>
                                        <option value="">Select Approver</option>
                                        @foreach($approvers->where('level', 2) as $approver)
                                            <option value="{{ $approver->id }}" {{ old('approver_level_2') == $approver->id ? 'selected' : '' }}>
                                                {{ $approver->name }} - {{ $approver->region->name ?? 'All Regions' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('approver_level_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Booking
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const vehicleSelect = document.getElementById('vehicle_id');
            const vehicleInfo = document.getElementById('vehicle-info');
            const estimatedKm = document.getElementById('estimated_km');
            const estimatedFuel = document.getElementById('estimated_fuel');

            vehicleSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                if (selected.value) {
                    const currentKm = selected.dataset.km;
                    const fuelConsumption = selected.dataset.fuel;
                    const needsService = selected.dataset.needsService === '1';
                    
                    let infoText = `Current KM: ${parseInt(currentKm).toLocaleString()}`;
                    if (needsService) {
                        infoText += ' | ⚠️ This vehicle needs service';
                        vehicleInfo.classList.add('text-warning');
                    } else {
                        vehicleInfo.classList.remove('text-warning');
                    }
                    vehicleInfo.textContent = infoText;
                    
                    // Store fuel consumption for calculation
                    vehicleSelect.dataset.fuelConsumption = fuelConsumption;
                    calculateFuel();
                } else {
                    vehicleInfo.textContent = '';
                }
            });

            estimatedKm.addEventListener('input', calculateFuel);

            function calculateFuel() {
                const km = parseFloat(estimatedKm.value) || 0;
                const consumption = parseFloat(vehicleSelect.dataset.fuelConsumption) || 0;
                
                if (km > 0 && consumption > 0) {
                    const fuel = km / consumption;
                    estimatedFuel.value = fuel.toFixed(2);
                } else {
                    estimatedFuel.value = '';
                }
            }
        });
    </script>
    @endpush
@endsection