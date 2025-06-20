
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Record Vehicle Service</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vehicle-usage.store-service') }}">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="vehicle_id" class="form-label">Vehicle</label>
                                    <select class="form-control @error('vehicle_id') is-invalid @enderror" 
                                        name="vehicle_id" id="vehicle_id" required>
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" 
                                                data-current-km="{{ $vehicle->current_km }}"
                                                {{ (old('vehicle_id') ?? $selectedVehicleId ?? '') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->name }} ({{ $vehicle->license_plate }})
                                                - Current: {{ number_format($vehicle->current_km) }} km
                                            </option>
                                        @endforeach
                                    </select>
                                @error('vehicle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="service_type" class="form-label">Service Type</label>
                                <select class="form-control @error('service_type') is-invalid @enderror" 
                                    name="service_type" required>
                                    <option value="">Select Type</option>
                                    <option value="routine">Routine Service</option>
                                    <option value="repair">Repair</option>
                                    <option value="tire_change">Tire Change</option>
                                    <option value="oil_change">Oil Change</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('service_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="service_date" class="form-label">Service Date</label>
                                <input type="date" class="form-control @error('service_date') is-invalid @enderror"
                                    name="service_date" value="{{ old('service_date', date('Y-m-d')) }}" required>
                                @error('service_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="service_vendor" class="form-label">Service Vendor</label>
                                <input type="text" class="form-control @error('service_vendor') is-invalid @enderror"
                                    name="service_vendor" value="{{ old('service_vendor') }}" required>
                                @error('service_vendor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="start_km" class="form-label">KM at Service</label>
                                <input type="number" class="form-control @error('start_km') is-invalid @enderror"
                                    name="start_km" value="{{ old('start_km') }}" required>
                                @error('start_km')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="end_km" class="form-label">KM after Service</label>
                                <input type="number" class="form-control @error('end_km') is-invalid @enderror"
                                    name="end_km" value="{{ old('end_km') }}" required>
                                @error('end_km')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="service_cost" class="form-label">Service Cost (Rp)</label>
                                <input type="number" step="0.01" class="form-control @error('service_cost') is-invalid @enderror"
                                    name="service_cost" value="{{ old('service_cost') }}" required>
                                @error('service_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="next_service_date" class="form-label">Next Service Date</label>
                                <input type="date" class="form-control @error('next_service_date') is-invalid @enderror"
                                    name="next_service_date" value="{{ old('next_service_date') }}">
                                @error('next_service_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="next_service_km" class="form-label">Next Service at KM</label>
                                <input type="number" class="form-control @error('next_service_km') is-invalid @enderror"
                                    name="next_service_km" value="{{ old('next_service_km') }}">
                                @error('next_service_km')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('vehicle-usage.service-index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Service Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('vehicle_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (selected.value) {
        const currentKm = selected.dataset.currentKm;
        document.getElementById('start_km').value = currentKm;
        document.getElementById('end_km').value = currentKm;
    }
});

// Trigger change event on load if vehicle is pre-selected
window.addEventListener('load', function() {
    const vehicleSelect = document.getElementById('vehicle_id');
    if (vehicleSelect.value) {
        vehicleSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection