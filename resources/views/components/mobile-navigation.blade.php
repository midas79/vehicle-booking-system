<div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
    <div class="pt-2 pb-3 space-y-1">
        <!-- Dashboard -->
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
        </x-responsive-nav-link>

        <!-- Bookings -->
        @if(auth()->user()->hasRole(['admin', 'approver']))
            <x-responsive-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.*')">
                {{ __('Bookings') }}
                @php
                    $pendingCount = \App\Models\Booking::where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span
                        class="ms-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                        {{ $pendingCount }}
                    </span>
                @endif
            </x-responsive-nav-link>
        @endif

        <!-- Admin Menu -->
        @if(auth()->user()->hasRole('admin'))
            <!-- Vehicle Usage -->
            <x-responsive-nav-link :href="route('vehicle-usage.index')" :active="request()->routeIs('vehicle-usage.*') && !request()->routeIs('vehicle-usage.service*', 'vehicle-usage.monitoring')">
                {{ __('Vehicle Usage') }}
            </x-responsive-nav-link>

            <!-- Vehicle Management Section -->
            <div class="pt-2 pb-2">
                <div class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    {{ __('Vehicle Management') }}
                </div>
                <x-responsive-nav-link :href="route('vehicles.index')" :active="request()->routeIs('vehicles.*')">
                    {{ __('Vehicle List') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('vehicle-usage.service-index')"
                    :active="request()->routeIs('vehicle-usage.service*')">
                    {{ __('Service Records') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('vehicle-usage.monitoring')"
                    :active="request()->routeIs('vehicle-usage.monitoring')">
                    {{ __('Vehicle Monitoring') }}
                </x-responsive-nav-link>
            </div>

            <!-- Reports -->
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                {{ __('Reports') }}
            </x-responsive-nav-link>

            <!-- User Management Section -->
            <div class="pt-2 pb-2">
                <div class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    {{ __('User Management') }}
                </div>
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    {{ __('Users') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('drivers.index')" :active="request()->routeIs('drivers.*')">
                    {{ __('Drivers') }}
                </x-responsive-nav-link>
            </div>
        @endif
    </div>

    <!-- User Info & Settings -->
    <div class="pt-4 pb-1 border-t border-gray-200">
        <div class="px-4">
            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            <div class="font-medium text-xs text-gray-400">
                Role: {{ Auth::user()->roles->first()->name ?? 'User' }}
            </div>
        </div>

        <div class="mt-3 space-y-1">
            <x-responsive-nav-link :href="route('profile.edit')">
                {{ __('Profile') }}
            </x-responsive-nav-link>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</div>