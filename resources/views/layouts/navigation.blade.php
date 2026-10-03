<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/logo.png') }}" class="block h-9 w-auto" alt="Logo">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-blue-500 font-bold' : 'text-gray-700' }}">
                        {{ __('Home') }}
                    </a>
                </div>
            </div>

            @auth
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 relative">
                <button onclick="document.getElementById('dropdownMenu').classList.toggle('hidden')" 
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                    <div>{{ Auth::user()->name }}</div>
                    <div class="ms-1">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 1.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>

                <!-- Dropdown Content -->
                <div id="dropdownMenu" class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg hidden">
                    @php
                        // Profile Edit Route
                        $profileEditRoute = auth()->user()->role === 'adopter' ? 'adopter.profile.edit' : 'shelter.profile.edit';

                        // Logout Route Selection
                        $logoutRoute = null;
                        if (auth()->user()->role === 'adopter') {
                            $logoutRoute = route('adopter.logout');
                        } elseif (auth()->user()->role === 'shelter_staff') {
                            $logoutRoute = route('shelter_staff.logout');
                        }
                    @endphp

                    <a href="{{ route($profileEditRoute) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        {{ __('Profile') }}
                    </a>

                    <!-- Logout Form -->
                    @if($logoutRoute)
                        <form method="POST" action="{{ $logoutRoute }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    @else
                        <p class="text-red-500 text-sm px-4 py-2">Logout route not found</p>
                    @endif
                </div>
            </div>
            @endauth

            <!-- Hamburger Menu for Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ url('/') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                {{ __('Home') }}
            </a>
        </div>

        @auth
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route($profileEditRoute) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    {{ __('Profile') }}
                </a>

                <!-- Logout Form -->
                @if($logoutRoute)
                    <form method="POST" action="{{ $logoutRoute }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                @else
                    <p class="text-red-500 text-sm px-4 py-2">Logout route not found</p>
                @endif
            </div>
        </div>
        @endauth
    </div>
</nav>
