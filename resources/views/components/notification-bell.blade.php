@if(auth()->user()->hasRole(['approver', 'admin']))
    @php
        $pendingCount = \App\Models\Booking::where('status', 'pending')->count();
    @endphp

    @if($pendingCount > 0)
        <div class="me-3">
            <a href="{{ route('bookings.index') }}" class="relative inline-flex items-center text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
                <span
                    class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                    {{ $pendingCount }}
                </span>
            </a>
        </div>
    @endif
@endif