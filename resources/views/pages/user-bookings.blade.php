@extends('layouts.app')

@section('title', 'My Bookings - Aceh Tour Adventure')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')
    <div class="min-h-screen bg-neutral-light font-poppins">

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pt-20">

            <!-- Profile Section -->
            <div class="bg-gradient-to-br from-accent to-primary-dark text-white p-6 rounded-lg mx-6 mt-6 shadow-md">
                <div class="flex flex-col md:flex-row items-center">
                    @if (auth()->user()->profile_photo_path)
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile"
                            class="w-20 h-20 rounded-full border-4 border-white shadow-md">
                    @else
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Profile"
                            class="w-20 h-20 rounded-full border-4 border-white shadow-md">
                    @endif
                    <div class="md:ml-6 mt-4 md:mt-0 text-center md:text-left">
                        <h2 class="text-2xl font-bold">{{ auth()->user()->name ?? 'User Name' }}</h2>
                        <p class="text-indigo-100">{{ auth()->user()->email ?? 'user@example.com' }}</p>
                        <div class="flex justify-center md:justify-start mt-3 space-x-3">
                            <div class="bg-white bg-opacity-20 px-4 py-2 rounded-full flex items-center">
                                <i class="fas fa-passport mr-2"></i>
                                <span>Member since
                                    {{ auth()->user()->created_at ? auth()->user()->created_at->format('Y') : '2020' }}</span>
                            </div>
                            <div class="bg-white bg-opacity-20 px-4 py-2 rounded-full flex items-center">
                                <i class="fas fa-globe-americas mr-2"></i>
                                <span>{{ $bookings->count() }} Trips</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Navigation Tabs -->
            <section class="nav-tabs bg-white py-3 sm:py-4 md:py-6 sticky top-0 z-20 shadow-md mx-6 mt-6 rounded-lg">
                <div class="w-full mx-auto px-5 sm:px-8 lg:px-10">
                    <div
                        class="flex justify-center overflow-x-auto space-x-4 sm:space-x-5 md:space-x-7 lg:space-x-9 pb-2 scrollbar-hide pl-1">
                        <button data-tab="dashboard"
                            class="nav-tab text-primary font-medium hover:text-primary-dark whitespace-nowrap border-b-2 border-primary px-2 py-2 text-xs sm:text-sm md:text-base flex items-center transition">
                            <i class="fas fa-home mr-2"></i> Dashboard
                        </button>
                        <button data-tab="bookings"
                            class="nav-tab text-secondary font-medium hover:text-primary-dark whitespace-nowrap px-2 py-2 text-xs sm:text-sm md:text-base flex items-center transition">
                            <i class="fas fa-suitcase mr-2"></i> My Bookings
                        </button>

                        <button data-tab="settings"
                            class="nav-tab text-secondary font-medium hover:text-primary-dark whitespace-nowrap px-2 py-2 text-xs sm:text-sm md:text-base flex items-center transition">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </button>
                        <button data-tab="help"
                            class="nav-tab text-secondary font-medium hover:text-primary-dark whitespace-nowrap px-2 py-2 text-xs sm:text-sm md:text-base flex items-center transition">
                            <i class="fas fa-question-circle mr-2"></i> Help
                        </button>
                    </div>
                </div>
            </section>

            <!-- Dashboard Content -->
            <div id="dashboard-content">
                <!-- Statistics Cards -->
                <div class="flex justify-center mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-4xl w-full px-6">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-accent-light text-accent">
                                    <i class="fas fa-calendar-check text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-secondary">Total Bookings</p>
                                    <p class="text-2xl font-semibold text-secondary-dark">{{ $bookings->count() ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-check-circle text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-secondary">Completed Trips</p>
                                    <p class="text-2xl font-semibold text-secondary-dark">
                                        {{ $bookings->where('status', 'completed')->count() ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                    <i class="fas fa-clock text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-secondary">Upcoming Trips</p>
                                    <p class="text-2xl font-semibold text-secondary-dark">
                                        {{ $bookings->where('status', 'paid')->count() ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mx-6 mt-6">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-secondary-dark mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button class="flex items-center p-4 bg-primary-light rounded-lg hover:bg-primary hover:text-white transition">
                                <i class="fas fa-plus text-primary mr-3"></i>
                                <span class="text-primary-dark font-medium">Book New Trip</span>
                            </button>
                            <button class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                                <i class="fas fa-search text-green-600 mr-3"></i>
                                <span class="text-green-700 font-medium">Browse Packages</span>
                            </button>
                            <button class="flex items-center p-4 bg-accent-light rounded-lg hover:bg-accent hover:text-white transition">
                                <i class="fas fa-headset text-accent mr-3"></i>
                                <span class="text-accent-dark font-medium">Contact Support</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Recent Activity -->
                <div class="mx-6 mt-6">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-secondary-dark mb-4">Recent Activity</h3>
                        <div class="space-y-4">
                            @if ($bookings && $bookings->count() > 0)
                                @foreach ($bookings->take(3) as $booking)
                                    <div class="flex items-center p-3 bg-neutral-light rounded-lg">
                                        <div class="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center">
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="font-medium text-secondary-dark">
                                                {{ $booking->travel_package->name ?? 'Travel Package' }}</p>
                                            <p class="text-sm text-secondary">
                                                {{ $booking->created_at->format('M d, Y') ?? 'Recent' }}</p>
                                        </div>
                                        <span
                                            class="px-3 py-1 text-xs font-medium rounded-full
                                        @if ($booking->status == 'paid') bg-green-100 text-green-800
                                        @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-neutral-light text-secondary-dark @endif">
                                            {{ ucfirst($booking->status ?? 'Unknown') }}
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-suitcase-rolling text-4xl text-neutral mb-4"></i>
                                    <p class="text-secondary">No recent activity</p>
                                    <p class="text-sm text-neutral-dark">Start booking your first trip!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Bookings Content -->
            <div id="bookings-content" class="hidden bg-white rounded-lg shadow-sm mx-6 mt-6 p-6">
                <!-- Header Section -->
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-4">
                    <h3 class="text-xl font-semibold text-secondary-dark">My Travel Bookings</h3>
                    <button
                        class="filter-btn px-4 py-2 text-sm font-medium rounded-lg bg-primary-light text-primary-dark border border-primary-light hover:bg-primary hover:text-white transition">
                        <i class="fas fa-list mr-2"></i> All Bookings
                    </button>
                </div>
                    <div class="text-sm text-secondary">
                        Showing {{ $bookings->count() }} of {{ $bookings->total() ?? $bookings->count() }} bookings
                    </div>
                </div>

                <!-- Booking Cards -->
                <div class="grid gap-6 lg:grid-cols-2">
                    @if ($bookings->count() > 0)
                        @foreach ($bookings as $booking)
                            <div
                                class="bg-white rounded-xl shadow-md p-6 transition duration-300 ease-in-out hover:-translate-y-0.5 hover:shadow-xl cursor-pointer">
                                <div class="flex flex-col md:flex-row md:justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-secondary-dark">
                                            {{ $booking->travelPackage->name ?? 'Travel Package' }}</h4>
                                        <div class="flex flex-wrap mt-2 gap-2">
                                            @php
                                                $statusClass =
                                                    'text-xs font-medium px-2 py-1 rounded-full bg-amber-100 text-amber-800';
                                                $statusIcon = 'fas fa-clock';
                                                if (
                                                    $booking->status === 'confirmed' ||
                                                    $booking->status === 'completed'
                                                ) {
                                                    $statusClass =
                                                        'text-xs font-medium px-2 py-1 rounded-full bg-emerald-100 text-emerald-800';
                                                    $statusIcon = 'fas fa-check-circle';
                                                } elseif ($booking->status === 'cancelled') {
                                                    $statusClass =
                                                        'text-xs font-medium px-2 py-1 rounded-full bg-red-100 text-red-800';
                                                    $statusIcon = 'fas fa-times-circle';
                                                }
                                            @endphp
                                            <span class="{{ $statusClass }}">
                                                <i class="{{ $statusIcon }} mr-1"></i> {{ ucfirst($booking->status) }}
                                            </span>
                                            <span class="text-sm text-secondary bg-neutral-light px-2 py-1 rounded-full">
                                                <i class="far fa-calendar mr-1"></i> Booked:
                                                {{ $booking->created_at ? $booking->created_at->format('d M Y') : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-4 md:mt-0">
                                        <p class="text-2xl font-bold text-primary">Rp
                                            {{ number_format((float)$booking->total_amount, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-secondary">Travel Date</p>
                                        <p class="font-medium">
                                            {{ $booking->booking_date ? $booking->booking_date->format('d M Y') : 'Not set' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-secondary">Travelers</p>
                                        <p class="font-medium">{{ $booking->total_participants }}
                                            {{ $booking->total_participants > 1 ? 'People' : 'Person' }}</p>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between items-center">
                                    <div class="flex items-center">
                                @if ($booking->travelPackage && $booking->travelPackage->media->count() > 0)
                                    <img src="{{ $booking->travelPackage->media->first()->getUrl() }}"
                                        alt="{{ $booking->travelPackage->name }}"
                                        class="w-12 h-12 rounded-lg object-cover mr-3">
                                @else
                                    <img src="https://images.unsplash.com/photo-1539367628448-4bc5c9d171c8?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80"
                                        alt="Travel" class="w-12 h-12 rounded-lg object-cover mr-3">
                                @endif
                                <div>
                                    <p class="text-xs text-secondary">Package</p>
                                    <p class="text-sm font-medium text-secondary-dark">
                                        {{ $booking->travelPackage->name ?? 'Travel Package' }}</p>
                                </div>
                            </div>
                                    <a href="{{ route('booking.show', $booking->id) }}"
                                        class="bg-primary-light text-primary px-4 py-2 rounded-lg font-medium hover:bg-primary hover:text-white transition">
                                        <i class="fas fa-eye mr-2"></i> View Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- No Bookings State -->
                        <div class="col-span-full bg-white rounded-xl shadow-md text-center p-8 md:p-12">
                            <div class="max-w-md mx-auto">
                                <svg class="w-24 h-24 text-neutral mx-auto mb-6" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                    </path>
                                </svg>
                                <h3 class="text-xl font-bold text-secondary-dark mb-2">No Bookings Found</h3>
                                <p class="text-secondary mb-6">You haven't made any bookings with us yet. Start exploring
                                    our amazing travel packages and plan your next adventure!</p>
                                <a href="{{ route('travel-packages') }}"
                                    class="inline-flex items-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary hover:bg-primary-dark hover:shadow-lg transition duration-150">
                                    <i class="fas fa-plus mr-2"></i>
                                    Book Now - Explore Packages
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if ($bookings->hasPages())
                    <div class="mt-8 flex justify-center">
                        <nav class="flex items-center space-x-1">
                            @if ($bookings->onFirstPage())
                                <button class="px-3 py-1 rounded-full text-neutral-dark cursor-not-allowed">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            @else
                                <a href="{{ $bookings->previousPageUrl() }}"
                                    class="px-3 py-1 rounded-full text-secondary hover:bg-neutral hover:text-primary">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @endif

                            @foreach ($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                                @if ($page == $bookings->currentPage())
                                    <button
                                        class="px-3 py-1 rounded-full bg-primary text-white">{{ $page }}</button>
                                @else
                                    <a href="{{ $url }}"
                                        class="px-3 py-1 rounded-full text-secondary-dark hover:bg-neutral hover:text-primary">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($bookings->hasMorePages())
                                <a href="{{ $bookings->nextPageUrl() }}"
                                    class="px-3 py-1 rounded-full text-secondary hover:bg-neutral hover:text-primary">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <button class="px-3 py-1 rounded-full text-neutral-dark cursor-not-allowed">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            @endif
                        </nav>
                    </div>
                @endif
            </div>

            <!-- Settings Content -->
            <div id="settings-content" class="hidden mx-6 mt-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Profile Settings -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-secondary-dark mb-6">Profile Settings</h3>
                            <form class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-secondary mb-2">Full Name</label>
                                        <input type="text" value="{{ Auth::user()->name ?? '' }}"
                                            class="w-full px-3 py-2 border border-neutral rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-secondary mb-2">Email</label>
                                        <input type="email" value="{{ Auth::user()->email ?? '' }}"
                                            class="w-full px-3 py-2 border border-neutral rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-2">Contact Number</label>
                                    <input type="tel" value="{{ Auth::user()->contact ?? '' }}" placeholder="+62 xxx-xxxx-xxxx"
                                        class="w-full px-3 py-2 border border-neutral rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-2">Address</label>
                                    <textarea rows="3" placeholder="Your address"
                                        class="w-full px-3 py-2 border border-neutral rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                                </div>
                                <button type="submit"
                                    class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark hover:shadow-md transition">
                                    <i class="fas fa-save mr-2"></i> Save Changes
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Quick Settings -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h4 class="font-semibold text-secondary-dark mb-4">Security</h4>
                            <div class="space-y-3">
                                <button
                                    class="w-full text-left px-4 py-3 bg-neutral-light rounded-lg hover:bg-neutral hover:text-secondary-dark transition">
                                    <i class="fas fa-key text-secondary mr-3"></i>
                                    <span class="text-sm text-secondary-dark">Change Password</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Content -->
            <div id="help-content" class="hidden mx-6 mt-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- FAQ Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-secondary-dark mb-6">Frequently Asked Questions</h3>
                        <div class="space-y-4">
                            <div class="border border-neutral rounded-lg">
                                <button
                                    class="w-full text-left px-4 py-3 font-medium text-secondary-dark hover:bg-neutral hover:text-primary transition">
                                    How do I book a travel package?
                                    <i class="fas fa-chevron-down float-right mt-1"></i>
                                </button>
                            </div>
                            <div class="border border-neutral rounded-lg">
                                <button
                                    class="w-full text-left px-4 py-3 font-medium text-secondary-dark hover:bg-neutral hover:text-primary transition">
                                    Can I cancel my booking?
                                    <i class="fas fa-chevron-down float-right mt-1"></i>
                                </button>
                            </div>
                            <div class="border border-neutral rounded-lg">
                                <button
                                    class="w-full text-left px-4 py-3 font-medium text-secondary-dark hover:bg-neutral hover:text-primary transition">
                                    What payment methods are accepted?
                                    <i class="fas fa-chevron-down float-right mt-1"></i>
                                </button>
                            </div>
                            <div class="border border-neutral rounded-lg">
                                <button
                                    class="w-full text-left px-4 py-3 font-medium text-secondary-dark hover:bg-neutral hover:text-primary transition">
                                    How do I modify my booking?
                                    <i class="fas fa-chevron-down float-right mt-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Support -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-secondary-dark mb-6">Contact Support</h3>
                            <div class="space-y-4">
                                <div class="flex items-center p-4 bg-primary-light rounded-lg">
                                    <i class="fas fa-phone text-primary-dark mr-4"></i>
                                    <div>
                                        <p class="font-medium text-secondary-dark">Phone Support</p>
                                        <p class="text-sm text-secondary">+62 21 1234 5678</p>
                                        <p class="text-xs text-secondary">Available 24/7</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-4 bg-accent-light rounded-lg">
                                    <i class="fas fa-envelope text-accent-dark mr-4"></i>
                                    <div>
                                        <p class="font-medium text-secondary-dark">Email Support</p>
                                        <p class="text-sm text-secondary">support@travelagency.com</p>
                                        <p class="text-xs text-secondary">Response within 24 hours</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-4 bg-accent-light rounded-lg">
                                    <i class="fab fa-whatsapp text-accent mr-4"></i>
                                    <div>
                                        <p class="font-medium text-secondary-dark">WhatsApp</p>
                                        <p class="text-sm text-secondary">+62 812 3456 7890</p>
                                        <p class="text-xs text-secondary">Quick response</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h4 class="font-semibold text-secondary-dark mb-4">Send us a message</h4>
                            <form class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-2">Subject</label>
                                    <input type="text" placeholder="How can we help you?"
                                        class="w-full px-3 py-2 border border-neutral rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-2">Message</label>
                                    <textarea rows="4" placeholder="Describe your issue or question..."
                                        class="w-full px-3 py-2 border border-neutral rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                                </div>
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark hover:shadow-md transition">
                                    <i class="fas fa-paper-plane mr-2"></i> Send Message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- User Bookings JavaScript - Moved to external file for better maintainability -->
    @vite('resources/js/user-bookings.js')
@endpush
