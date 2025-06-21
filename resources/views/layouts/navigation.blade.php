<nav x-data="{ open: false, vehicleDropdownOpen: false }" class="bg-gray-800 border-b border-gray-700 relative z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center text-white">
                        <i class="fas fa-car text-2xl text-blue-400 mr-3"></i>
                        <span class="font-semibold text-xl text-white">Vehicle Booking System</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out
                              {{ request()->routeIs('dashboard')
    ? 'border-blue-400 text-white'
    : 'border-transparent text-gray-300 hover:text-white hover:border-gray-300' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Dashboard
                    </a>

                    @if(auth()->user()->isAdmin() || auth()->user()->isApprover())
                                    <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out
                                                  {{ request()->routeIs('bookings.*')
                        ? 'border-blue-400 text-white'
                        : 'border-transparent text-gray-300 hover:text-white hover:border-gray-300' }}">
                                        <i class="fas fa-calendar-check mr-2"></i>
                                        Bookings
                                    </a>
                    @endif

                    @if(auth()->user()->isAdmin())
                                    <!-- Vehicle Management Dropdown -->
                                    <div class="relative flex items-center">
                                        <button @click="vehicleDropdownOpen = !vehicleDropdownOpen"
                                            @click.away="vehicleDropdownOpen = false" type="button"
                                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out
                                                           {{ request()->routeIs('vehicle-usage.*')
                        ? 'border-blue-400 text-white'
                        : 'border-transparent text-gray-300 hover:text-white hover:border-gray-300' }}">
                                            <i class="fas fa-truck mr-2"></i>
                                            Vehicle Management
                                            <svg class="ml-2 -mr-0.5 h-4 w-4 transition-transform duration-200"
                                                :class="{'rotate-180': vehicleDropdownOpen}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu -->
                                        <div x-show="vehicleDropdownOpen" x-cloak x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute left-0 mt-12 w-56 rounded-md shadow-lg bg-gray-700 ring-1 ring-black ring-opacity-5 z-[100]">
                                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="vehicle-menu">
                                                <a href="{{ route('vehicle-usage.index') }}"
                                                    class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-600 hover:text-white transition duration-150 ease-in-out
                                                              {{ request()->routeIs('vehicle-usage.index') ? 'bg-gray-600 text-white' : '' }}" role="menuitem">
                                                    <i class="fas fa-road mr-3 w-4 inline-block"></i>
                                                    Vehicle Usage
                                                </a>
                                                <a href="{{ route('vehicle-usage.service-index') }}"
                                                    class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-600 hover:text-white transition duration-150 ease-in-out
                                                              {{ request()->routeIs('vehicle-usage.service-index') ? 'bg-gray-600 text-white' : '' }}" role="menuitem">
                                                    <i class="fas fa-wrench mr-3 w-4 inline-block"></i>
                                                    Service Management
                                                </a>
                                                <a href="{{ route('vehicle-usage.monitoring') }}"
                                                    class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-600 hover:text-white transition duration-150 ease-in-out
                                                              {{ request()->routeIs('vehicle-usage.monitoring') ? 'bg-gray-600 text-white' : '' }}" role="menuitem">
                                                    <i class="fas fa-chart-line mr-3 w-4 inline-block"></i>
                                                    Vehicle Monitoring
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('reports.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out
                                                  {{ request()->routeIs('reports.*')
                        ? 'border-blue-400 text-white'
                        : 'border-transparent text-gray-300 hover:text-white hover:border-gray-300' }}">
                                        <i class="fas fa-file-excel mr-2"></i>
                                        Reports
                                    </a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 text-sm leading-4 font-medium rounded-md text-gray-300 hover:text-white bg-gray-700 hover:bg-gray-600 focus:outline-none focus:bg-gray-600 transition ease-in-out duration-150">
                            <i class="fas fa-user mr-2"></i>
                            <span class="text-white">{{ Auth::user()->name }}</span>
                            <span class="ml-2 px-2 py-1 text-xs bg-blue-600 text-white rounded">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                            <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                            <i class="fas fa-user-edit mr-2 text-gray-400"></i>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="flex items-center">
                                <i class="fas fa-sign-out-alt mr-2 text-gray-400"></i>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gray-800">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out
                      {{ request()->routeIs('dashboard')
    ? 'border-blue-400 text-white bg-gray-900'
    : 'border-transparent text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-tachometer-alt mr-2"></i>
                Dashboard
            </a>

            @if(auth()->user()->isAdmin() || auth()->user()->isApprover())
                    <a href="{{ route('bookings.index') }}"
                        class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out
                                  {{ request()->routeIs('bookings.*')
                ? 'border-blue-400 text-white bg-gray-900'
                : 'border-transparent text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300' }}">
                        <i class="fas fa-calendar-check mr-2"></i>
                        Bookings
                    </a>
            @endif

            @if(auth()->user()->isAdmin())
                    <div class="border-t border-gray-700 pt-2">
                        <div class="px-4 py-2 text-xs text-gray-400 uppercase">Vehicle Management</div>
                        <a href="{{ route('vehicle-usage.index') }}"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out
                                      {{ request()->routeIs('vehicle-usage.index')
                ? 'border-blue-400 text-white bg-gray-900'
                : 'border-transparent text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300' }}">
                            <i class="fas fa-road mr-2"></i>
                            Vehicle Usage
                        </a>
                        <a href="{{ route('vehicle-usage.service-index') }}"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out
                                      {{ request()->routeIs('vehicle-usage.service-*')
                ? 'border-blue-400 text-white bg-gray-900'
                : 'border-transparent text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300' }}">
                            <i class="fas fa-wrench mr-2"></i>
                            Service Management
                        </a>
                        <a href="{{ route('vehicle-usage.monitoring') }}"
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out
                                      {{ request()->routeIs('vehicle-usage.monitoring')
                ? 'border-blue-400 text-white bg-gray-900'
                : 'border-transparent text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300' }}">
                            <i class="fas fa-chart-line mr-2"></i>
                            Vehicle Monitoring
                        </a>
                    </div>

                    <a href="{{ route('reports.index') }}"
                        class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out
                                  {{ request()->routeIs('reports.*')
                ? 'border-blue-400 text-white bg-gray-900'
                : 'border-transparent text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300' }}">
                        <i class="fas fa-file-excel mr-2"></i>
                        Reports
                    </a>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-700">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
                <span class="inline-block mt-1 px-2 py-1 text-xs bg-blue-600 text-white rounded">
                    {{ ucfirst(Auth::user()->role) }}
                </span>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}"
                    class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out border-transparent text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300">
                    <i class="fas fa-user-edit mr-2"></i>
                    Profile
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                        class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out border-transparent text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Log Out
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>