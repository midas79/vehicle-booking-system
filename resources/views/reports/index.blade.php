@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <!-- Filter Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Generate Booking Report</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('reports.index') }}" id="filterForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" 
                                        class="form-control @error('start_date') is-invalid @enderror"
                                        name="start_date" 
                                        value="{{ $startDate }}" 
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" 
                                        class="form-control @error('end_date') is-invalid @enderror"
                                        name="end_date" 
                                        value="{{ $endDate }}" 
                                        required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> View Report
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Export Options -->
                @if(isset($bookings) && count($bookings) > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Export Options</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('reports.export') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" name="end_date" value="{{ $endDate }}">
                            
                            <div class="d-flex gap-2">
                                <button type="submit" name="format" value="excel" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export to Excel
                                </button>
                                <button type="submit" name="format" value="csv" class="btn btn-primary">
                                    <i class="fas fa-file-csv"></i> Export to CSV
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Report Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Report Preview</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($bookings) && count($bookings) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Booking Number</th>
                                            <th>Requester</th>
                                            <th>Vehicle</th>
                                            <th>Driver</th>
                                            <th>Purpose</th>
                                            <th>Destination</th>
                                            <th>Schedule</th>
                                            <th>Status</th>
                                            <th>Approvals</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bookings as $booking)
                                            @php
                                                $approval1 = $booking->approvals->where('level', 1)->first();
                                                $approval2 = $booking->approvals->where('level', 2)->first();
                                            @endphp
                                            <tr>
                                                <td>{{ $booking->booking_number }}</td>
                                                <td>{{ $booking->user->name }}</td>
                                                <td>
                                                    {{ $booking->vehicle->name }}<br>
                                                    <small class="text-muted">{{ $booking->vehicle->license_plate }}</small>
                                                </td>
                                                <td>{{ $booking->driver->name }}</td>
                                                <td>{{ Str::limit($booking->purpose, 30) }}</td>
                                                <td>{{ $booking->destination }}</td>
                                                <td>
                                                    <small>
                                                        {{ $booking->start_date->format('d/m/Y H:i') }}<br>
                                                        {{ $booking->end_date->format('d/m/Y H:i') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge 
                                                        @if($booking->status == 'approved') bg-success
                                                        @elseif($booking->status == 'rejected') bg-danger
                                                        @elseif($booking->status == 'completed') bg-info
                                                        @else bg-warning
                                                        @endif">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>
                                                        <div>
                                                            <strong>L1:</strong> {{ $approval1 ? $approval1->approver->name : '-' }}
                                                            @if($approval1)
                                                                <span class="text-{{ $approval1->status == 'approved' ? 'success' : ($approval1->status == 'rejected' ? 'danger' : 'warning') }}">
                                                                    ({{ ucfirst($approval1->status) }})
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <strong>L2:</strong> {{ $approval2 ? $approval2->approver->name : '-' }}
                                                            @if($approval2)
                                                                <span class="text-{{ $approval2->status == 'approved' ? 'success' : ($approval2->status == 'rejected' ? 'danger' : 'warning') }}">
                                                                    ({{ ucfirst($approval2->status) }})
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $bookings->links() }}
                            </div>

                            <!-- Summary -->
                            <div class="mt-3 text-muted">
                                Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} results
                            </div>
                        @else
                            <div class="text-center py-5">
                                <p class="text-muted">No bookings found for the selected date range.</p>
                                <p class="text-muted small">Please select a date range and click "View Report" to see the data.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection