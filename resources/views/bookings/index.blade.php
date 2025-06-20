@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Vehicle Bookings</h5>
                        <div>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> New Booking
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Filter Section -->
                        <div class="mb-3">
                            <form method="GET" action="{{ route('bookings.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="from_date" class="form-control form-control-sm" 
                                        value="{{ request('from_date') }}" placeholder="From Date">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="to_date" class="form-control form-control-sm" 
                                        value="{{ request('to_date') }}" placeholder="To Date">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                                    <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Booking #</th>
                                        <th>Requester</th>
                                        <th>Vehicle</th>
                                        <th>Driver</th>
                                        <th>Destination</th>
                                        <th>Date</th>
                                        <th>Est. KM/Fuel</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->booking_number }}</td>
                                            <td>{{ $booking->user->name }}</td>
                                            <td>
                                                {{ $booking->vehicle->name }}<br>
                                                <small class="text-muted">{{ $booking->vehicle->license_plate }}</small>
                                            </td>
                                            <td>{{ $booking->driver->name }}</td>
                                            <td>
                                                {{ Str::limit($booking->destination, 30) }}<br>
                                                <small class="text-muted">{{ Str::limit($booking->purpose, 30) }}</small>
                                            </td>
                                            <td>
                                                {{ $booking->start_date->format('d/m/Y H:i') }}<br>
                                                <small class="text-muted">to
                                                    {{ $booking->end_date->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($booking->estimated_km)
                                                    {{ number_format($booking->estimated_km) }} km<br>
                                                    <small class="text-muted">{{ number_format($booking->estimated_fuel, 1) }} L</small>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($booking->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($booking->status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($booking->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-secondary">Completed</span>
                                                @endif

                                                <br>
                                                @foreach($booking->approvals as $approval)
                                                    <small>
                                                        L{{ $approval->level }}:
                                                        @if($approval->status == 'pending')
                                                            <i class="fas fa-clock text-warning"></i>
                                                        @elseif($approval->status == 'approved')
                                                            <i class="fas fa-check text-success"></i>
                                                        @else
                                                            <i class="fas fa-times text-danger"></i>
                                                        @endif
                                                    </small>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if(auth()->user()->isApprover())
                                                    @php
                                                        $myApproval = $booking->approvals
                                                            ->where('approver_id', auth()->id())
                                                            ->where('status', 'pending')
                                                            ->first();
                                                    @endphp

                                                    @if($myApproval)
                                                        @php
                                                            $canApprove = true;
                                                            if ($myApproval->level > 1) {
                                                                $previousApproval = $booking->approvals
                                                                    ->where('level', $myApproval->level - 1)
                                                                    ->first();
                                                                $canApprove = $previousApproval && $previousApproval->status == 'approved';
                                                            }
                                                        @endphp

                                                        @if($canApprove)
                                                            <form action="{{ route('bookings.approve', $booking) }}" method="POST"
                                                                class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm"
                                                                    onclick="return confirm('Are you sure you want to approve this booking?')">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                                data-bs-target="#rejectModal{{ $booking->id }}">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @else
                                                            <small class="text-muted">Waiting L{{ $myApproval->level - 1 }}</small>
                                                        @endif
                                                    @endif
                                                @endif

                                                @if(auth()->user()->isAdmin())
                                                    @if(auth()->user()->isAdmin() && $booking->status == 'approved' && !$booking->vehicleUsage)
                                                        <a href="{{ route('vehicle-usage.create', $booking) }}"
                                                            class="btn btn-info btn-sm" title="Record Usage">
                                                            <i class="fas fa-road"></i>
                                                        </a>
                                                    @elseif($booking->vehicleUsage)
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-check"></i> Recorded
                                                        </span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $booking->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('bookings.reject', $booking) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Reject Booking</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="notes" class="form-label">Reason for
                                                                    rejection</label>
                                                                <textarea class="form-control" name="notes" rows="3"
                                                                    required placeholder="Please provide a reason for rejection"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Reject</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No bookings found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection