@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Record Vehicle Usage</h5>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Booking:</strong> {{ $booking->booking_number }}<br>
                            <strong>Vehicle:</strong> {{ $booking->vehicle->name }}
                            ({{ $booking->vehicle->license_plate }})<br>
                            <strong>Driver:</strong> {{ $booking->driver->name }}
                        </div>

                        <form method="POST" action="{{ route('vehicle-usage.store', $booking) }}">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="start_km" class="form-label">Start KM</label>
                                    <input type="number" class="form-control @error('start_km') is-invalid @enderror"
                                        name="start_km" value="{{ old('start_km') }}" required>
                                    @error('start_km')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="end_km" class="form-label">End KM</label>
                                    <input type="number" class="form-control @error('end_km') is-invalid @enderror"
                                        name="end_km" value="{{ old('end_km') }}" required>
                                    @error('end_km')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="fuel_used" class="form-label">Fuel Used (Liters)</label>
                                <input type="number" step="0.01"
                                    class="form-control @error('fuel_used') is-invalid @enderror" name="fuel_used"
                                    value="{{ old('fuel_used') }}" required>
                                @error('fuel_used')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" name="notes"
                                    rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Usage
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection