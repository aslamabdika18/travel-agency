<nav class="fixed w-full top-0 z-50 transition-all duration-300 bg-neutral bg-opacity-95 backdrop-blur-sm shadow-lg py-2" id="navbar">
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-10 sm:h-12">
            <!-- Logo Section -->
            <div class="flex items-center">
                <img class="h-7 xs:h-8 sm:h-9 md:h-10 w-auto" src="{{ asset('images/logo.png') }}" alt="Logo">
                <span class="ml-2 xs:ml-3 sm:ml-4 text-sm xs:text-base sm:text-lg md:text-xl font-bold text-primary truncate max-w-[100px] xs:max-w-[120px] sm:max-w-[160px] md:max-w-none">
                    Sumatra Tour Travel
                </span>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-1 lg:space-x-4">
                <a href="{{ route('home') }}" class="relative text-secondary-dark hover:text-primary px-2 lg:px-3 py-2 text-sm lg:text-base font-medium transition-all duration-200 group rounded-md hover:bg-neutral-light/50 {{ request()->routeIs('home') ? 'text-primary' : '' }}">
                    Home
                    <span class="absolute bottom-0 left-1/2 w-0 h-[2px] bg-primary transition-all duration-300 ease-out transform -translate-x-1/2 group-hover:w-full {{ request()->routeIs('home') ? 'w-full' : '' }}"></span>
                </a>

                <a href="{{ route('travel-packages') }}" class="relative text-secondary-dark hover:text-primary px-2 lg:px-3 py-2 text-sm lg:text-base font-medium transition-all duration-200 group rounded-md hover:bg-neutral-light/50 {{ request()->routeIs('travel-packages') ? 'text-primary' : '' }}">
                    Travel Packages
                    <span class="absolute bottom-0 left-1/2 w-0 h-[2px] bg-primary transition-all duration-300 ease-out transform -translate-x-1/2 group-hover:w-full {{ request()->routeIs('travel-packages') ? 'w-full' : '' }}"></span>
                </a>

                <a href="{{ route('about') }}" class="relative text-secondary-dark hover:text-primary px-2 lg:px-3 py-2 text-sm lg:text-base font-medium transition-all duration-200 group rounded-md hover:bg-neutral-light/50 {{ request()->routeIs('about') ? 'text-primary' : '' }}">
                    About Us
                    <span class="absolute bottom-0 left-1/2 w-0 h-[2px] bg-primary transition-all duration-300 ease-out transform -translate-x-1/2 group-hover:w-full {{ request()->routeIs('about') ? 'w-full' : '' }}"></span>
                </a>

                <a href="{{ route('contact') }}" class="relative text-secondary-dark hover:text-primary px-2 lg:px-3 py-2 text-sm lg:text-base font-medium transition-all duration-200 group rounded-md hover:bg-neutral-light/50 {{ request()->routeIs('contact') ? 'text-primary' : '' }}">
                    Contact
                    <span class="absolute bottom-0 left-1/2 w-0 h-[2px] bg-primary transition-all duration-300 ease-out transform -translate-x-1/2 group-hover:w-full {{ request()->routeIs('contact') ? 'w-full' : '' }}"></span>
                </a>
            </div>

            <!-- User Menu / Login Button -->
            <div class="flex items-center">
                @auth
                    <div class="relative user-menu hidden md:block">
                        <button id="userMenuButton" class="flex items-center space-x-1 sm:space-x-2 text-secondary-dark hover:text-primary focus:outline-none p-1.5 rounded-md hover:bg-neutral-light/50 transition-all duration-200">
                            <span class="hidden sm:block text-sm lg:text-base truncate max-w-[80px] md:max-w-[120px] lg:max-w-none">{{ Auth::user()->name }}</span>
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-primary-light flex items-center justify-center text-primary shadow-sm">
                                <i class="fas fa-user text-xs sm:text-sm"></i>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200 group-hover:rotate-180"></i>
                        </button>
                        <div id="userMenu" class="dropdown-menu hidden py-1">
                            @if(Auth::user()->hasRole('customer'))
                                <a href="{{ route('user-bookings') }}" class="block px-4 py-2.5 text-sm text-secondary-dark hover:bg-primary-light hover:text-primary transition-colors duration-200">
                                    <i class="fas fa-calendar-alt mr-2"></i> My Bookings
                                </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2.5 text-sm text-secondary-dark hover:bg-primary-light hover:text-primary transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('auth') }}" class="hidden md:flex bg-primary hover:bg-primary-dark text-white font-medium py-1.5 xs:py-2 px-3 xs:px-4 text-xs xs:text-sm rounded-full transition duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                        <i class="fas fa-sign-in-alt mr-1 xs:mr-2"></i> <span>Login</span>
                    </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button id="mobileMenuButton" class="ml-2 xs:ml-3 sm:ml-4 md:hidden text-secondary-dark hover:text-primary focus:outline-none p-1 rounded-md hover:bg-neutral-light transition-colors duration-200">
                    <svg class="h-6 w-6 xs:h-7 xs:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="md:hidden hidden pt-4 pb-2 border-t mt-2 border-neutral-dark transform transition-all duration-300 ease-in-out opacity-0 max-h-0 overflow-hidden shadow-lg rounded-b-lg bg-white">
            <div class="flex flex-col space-y-3 px-4">
                <a href="{{ route('home') }}" class="text-secondary-dark hover:text-primary py-2.5 px-3 text-sm font-medium flex items-center rounded-md transition-all duration-200 hover:bg-neutral-light {{ request()->routeIs('home') ? 'text-primary bg-neutral-light' : '' }}">
                    <i class="fas fa-home mr-3 w-5 text-center"></i> Home
                    @if(request()->routeIs('home'))
                        <span class="ml-2 w-1.5 h-1.5 rounded-full bg-primary"></span>
                    @endif
                </a>
                <a href="{{ route('travel-packages') }}" class="text-secondary-dark hover:text-primary py-2.5 px-3 text-sm font-medium flex items-center rounded-md transition-all duration-200 hover:bg-neutral-light {{ request()->routeIs('travel-packages') ? 'text-primary bg-neutral-light' : '' }}">
                    <i class="fas fa-suitcase mr-3 w-5 text-center"></i> Travel Packages
                    @if(request()->routeIs('travel-packages'))
                        <span class="ml-2 w-1.5 h-1.5 rounded-full bg-primary"></span>
                    @endif
                </a>
                <a href="{{ route('about') }}" class="text-secondary-dark hover:text-primary py-2.5 px-3 text-sm font-medium flex items-center rounded-md transition-all duration-200 hover:bg-neutral-light {{ request()->routeIs('about') ? 'text-primary bg-neutral-light' : '' }}">
                    <i class="fas fa-info-circle mr-3 w-5 text-center"></i> About Us
                    @if(request()->routeIs('about'))
                        <span class="ml-2 w-1.5 h-1.5 rounded-full bg-primary"></span>
                    @endif
                </a>
                <a href="{{ route('contact') }}" class="text-secondary-dark hover:text-primary py-2.5 px-3 text-sm font-medium flex items-center rounded-md transition-all duration-200 hover:bg-neutral-light {{ request()->routeIs('contact') ? 'text-primary bg-neutral-light' : '' }}">
                    <i class="fas fa-envelope mr-3 w-5 text-center"></i> Contact
                    @if(request()->routeIs('contact'))
                        <span class="ml-2 w-1.5 h-1.5 rounded-full bg-primary"></span>
                    @endif
                </a>

                @auth
                    <!-- User Menu for Mobile -->
                    <div class="border-t border-neutral-dark pt-3 mt-3">
                        <div class="text-xs font-semibold text-secondary uppercase tracking-wide mb-3 px-3 py-1 bg-neutral-light rounded-md">
                            <i class="fas fa-user mr-2"></i> {{ Auth::user()->name }}
                        </div>

                        @if(Auth::user()->hasRole('customer'))
                            <a href="{{ route('user-bookings') }}" class="text-secondary-dark hover:text-primary py-2.5 px-3 text-sm font-medium flex items-center rounded-md transition-all duration-200 hover:bg-neutral-light {{ request()->routeIs('user-bookings') ? 'text-primary bg-neutral-light' : '' }}">
                                <i class="fas fa-calendar-alt mr-3 w-5 text-center"></i> My Bookings
                                @if(request()->routeIs('user-bookings'))
                                    <span class="ml-2 w-1.5 h-1.5 rounded-full bg-primary"></span>
                                @endif
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="w-full text-left text-secondary-dark hover:text-primary py-2.5 px-3 text-sm font-medium flex items-center rounded-md transition-all duration-200 hover:bg-neutral-light">
                                <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i> Logout
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Login Button for Mobile -->
                    <div class="border-t border-neutral-dark pt-3 mt-3">
                        <a href="{{ route('auth') }}" class="bg-primary hover:bg-primary-dark text-white font-medium py-2.5 px-5 text-sm rounded-full transition duration-300 flex items-center justify-center shadow-md hover:shadow-lg transform hover:scale-105">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login / Register
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

@push('scripts')
    @vite('resources/js/navbar.js')
@endpush
