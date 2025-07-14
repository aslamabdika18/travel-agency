@extends('layouts.app')

@section('title', isset($travelPackage) ? $travelPackage->name : 'Travel Package Detail')

@section('content')
    <!-- Meta tag for travel package slug -->
    <meta name="travel-package-slug" content="{{ $slug ?? '' }}">

    <script>
        // Set global variables for external JavaScript
        window.travelPackageConfig = {
            slug: '{{ $slug ?? '' }}',
            shouldScrollToBooking: {{ isset($bookingIntent) && $bookingIntent ? 'true' : 'false' }} ||
                {{ session('url.intended') == url()->current() ? 'true' : 'false' }} ||
                window.location.hash === '#booking',
            isGuest: {{ auth()->guest() ? 'true' : 'false' }},
            loginUrl: '{{ route('auth', ['intended' => url()->current() . '#booking']) }}'
        };
    </script>
    @if (isset($travelPackage))
        <!-- Dynamic content when package exists -->
        <!-- Hero Section -->
        <section
            class="relative pt-16 sm:pt-20 md:pt-24 lg:pt-32 pb-8 sm:pb-12 md:pb-16 lg:pb-20 bg-cover bg-center min-h-[70vh] sm:min-h-[80vh] lg:min-h-screen flex items-center"
            style="background-image: url('{{ $travelPackage->featured_image }}');">
            <div class="absolute inset-0 bg-secondary-dark opacity-70"></div>
            <div class="relative z-10 w-full mx-auto px-5 sm:px-8 lg:px-10">
                <div
                    class="flex flex-col lg:flex-row justify-between items-start lg:items-center max-w-7xl mx-auto gap-6 lg:gap-10">
                    <div class="flex-1 w-full lg:pr-10 px-2 sm:px-3">
                        <h1
                            class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold text-white mb-3 sm:mb-4 md:mb-6 animate-fade-in leading-tight">
                            {{ $travelPackage->name }}
                        </h1>
                        <div class="flex items-center mb-4 sm:mb-6 ml-1">
                            <i class="fas fa-map-marker-alt text-primary mr-3 text-base sm:text-lg md:text-xl"></i>
                            <span
                                class="text-white text-base sm:text-lg md:text-xl lg:text-2xl">{{ $travelPackage->location }}</span>
                        </div>
                    </div>
                    <div
                        class="bg-white/10 backdrop-blur-md p-5 sm:p-7 rounded-lg border border-white/20 w-full lg:w-auto lg:min-w-[300px] xl:min-w-[340px] animate-fade-in animation-delay-300 mx-1 sm:mx-2">
                        <div class="text-white mb-3 text-sm sm:text-base px-1">Starting from</div>
                        <div class="text-2xl sm:text-3xl font-bold text-white mb-4 px-1">Rp
                            {{ number_format((float)$travelPackage->price, 0, ',', '.') }}</div>
                        <div class="flex items-center text-white mb-5 px-1">
                            <i class="fas fa-clock mr-3"></i>
                            <span class="text-sm sm:text-base">{{ $travelPackage->duration }} Days</span>
                        </div>
                        @auth
                            <a href="#booking"
                                class="block w-full bg-primary hover:bg-primary-dark text-white text-center font-bold py-3 px-7 rounded-lg transition duration-300 transform hover:scale-105 text-sm sm:text-base">
                                Book Now
                            </a>
                        @else
                            <a href="{{ route('auth', ['intended' => url()->current()]) }}"
                                class="block w-full bg-primary hover:bg-primary-dark text-white text-center font-bold py-3 px-7 rounded-lg transition duration-300 transform hover:scale-105 text-sm sm:text-base">
                                Book Now
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>
    @else
        <!-- Static demo content -->
        <!-- Hero Section -->
        <section
            class="relative pt-16 sm:pt-20 md:pt-24 lg:pt-32 pb-8 sm:pb-12 md:pb-16 lg:pb-20 bg-cover bg-center min-h-[70vh] sm:min-h-[80vh] lg:min-h-screen flex items-center"
            style="background-image: url('https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');">
            <div class="absolute inset-0 bg-secondary-dark opacity-70"></div>
            <div class="relative z-10 w-full mx-auto px-5 sm:px-8 lg:px-10">
                <div
                    class="flex flex-col lg:flex-row justify-between items-start lg:items-center max-w-7xl mx-auto gap-6 lg:gap-10">
                    <div class="flex-1 w-full lg:pr-10 px-2 sm:px-3">
                        <h1
                            class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold text-white mb-3 sm:mb-4 animate-fade-in leading-tight">
                            Banyak Islands Explorer
                        </h1>
                        <div class="flex items-center mb-4 sm:mb-6 ml-1">
                            <i class="fas fa-map-marker-alt text-primary mr-3 text-base sm:text-lg"></i>
                            <span class="text-white text-base sm:text-lg md:text-xl">Banyak Islands, Aceh</span>
                        </div>
                    </div>
                    <div
                        class="bg-white/10 backdrop-blur-md p-5 sm:p-7 rounded-lg border border-white/20 w-full lg:w-auto lg:min-w-[300px] xl:min-w-[340px] animate-fade-in animation-delay-300 mx-1 sm:mx-2">
                        <div class="text-white mb-3 px-1">Starting from</div>
                        <div class="text-3xl font-bold text-white mb-4 px-1">Rp {{ number_format((float)5500000, 0, ',', '.') }}
                        </div>
                        <div class="flex items-center text-white mb-5 px-1">
                            <i class="fas fa-clock mr-3"></i>
                            <span>5 Days</span>
                        </div>
                        <a href="#booking"
                            class="block w-full bg-primary hover:bg-primary-dark text-white text-center font-bold py-3 px-7 rounded-lg transition duration-300 transform hover:scale-105">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Navigation Tabs -->
    <section class="bg-neutral-light py-3 sm:py-4 md:py-6 sticky top-0 z-20 shadow-md">
        <div class="w-full mx-auto px-5 sm:px-8 lg:px-10">
            <div class="flex overflow-x-auto space-x-4 sm:space-x-5 md:space-x-7 lg:space-x-9 pb-2 scrollbar-hide pl-1">
                <a href="#overview"
                    class="text-primary-dark font-medium hover:text-primary whitespace-nowrap border-b-2 border-primary px-2 py-2 text-xs sm:text-sm md:text-base">Overview</a>
                <a href="#itinerary"
                    class="text-secondary-dark font-medium hover:text-primary whitespace-nowrap px-2 py-2 text-xs sm:text-sm md:text-base">Itinerary</a>
                <a href="#inclusions"
                    class="text-secondary-dark font-medium hover:text-primary whitespace-nowrap px-2 py-2 text-xs sm:text-sm md:text-base">Inclusions
                    & Exclusions</a>
                <a href="#gallery"
                    class="text-secondary-dark font-medium hover:text-primary whitespace-nowrap px-2 py-2 text-xs sm:text-sm md:text-base">Gallery</a>
                <a href="#reviews"
                    class="text-secondary-dark font-medium hover:text-primary whitespace-nowrap px-2 py-2 text-xs sm:text-sm md:text-base">Reviews</a>
                <a href="#booking"
                    class="text-secondary-dark font-medium hover:text-primary whitespace-nowrap px-2 py-2 text-xs sm:text-sm md:text-base">Booking</a>
            </div>
        </div>
    </section>

    <!-- Overview Section -->
    <section id="overview" class="py-6 sm:py-8 md:py-12 lg:py-16 bg-neutral">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-3 lg:gap-8 xl:gap-12">
                <div class="lg:col-span-2 animate-on-scroll">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-primary-dark mb-3 sm:mb-4 md:mb-6">
                        Overview
                    </h2>

                    @if (isset($travelPackage))
                        <div class="prose prose-sm sm:prose-base lg:prose-lg max-w-none text-secondary">
                            {!! $travelPackage->description !!}
                        </div>
                    @else
                        <div class="prose prose-sm sm:prose-base lg:prose-lg max-w-none text-secondary">
                            <p>Discover the pristine beaches and vibrant marine life of the Banyak Islands, a hidden
                                paradise off the west coast of Sumatra. This 5-day adventure takes you through the
                                archipelago's most beautiful spots, offering a perfect blend of relaxation, adventure, and
                                cultural immersion.</p>

                            <p>The Banyak Islands, meaning "Many Islands" in Indonesian, consist of 99 small islands known
                                for their white sandy beaches, crystal-clear waters, and diverse coral reefs. This tour is
                                designed for travelers seeking an off-the-beaten-path experience in one of Indonesia's most
                                unspoiled regions.</p>

                            <p>Throughout your journey, you'll stay in comfortable beachfront accommodations, enjoy fresh
                                seafood and local cuisine, and be guided by experienced local guides who are passionate
                                about sharing their homeland with visitors.</p>

                            <p>Whether you're snorkeling among colorful coral gardens, relaxing on secluded beaches, or
                                connecting with local island communities, this tour promises an unforgettable experience in
                                a tropical paradise that few travelers have discovered.</p>
                        </div>
                    @endif

                    <div class="mt-4 sm:mt-6 md:mt-8 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
                        <div class="bg-white p-3 sm:p-4 md:p-6 rounded-lg shadow-md text-center">
                            <div
                                class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 bg-primary-light rounded-full mb-2 sm:mb-3 md:mb-4">
                                <i class="fas fa-calendar-alt text-sm sm:text-base md:text-lg lg:text-xl text-primary"></i>
                            </div>
                            <h3 class="font-bold text-secondary-dark mb-1 text-xs sm:text-sm md:text-base">Duration</h3>
                            <p class="text-secondary text-xs sm:text-sm">5 Days / 4 Nights</p>
                        </div>

                        <div class="bg-white p-3 sm:p-4 md:p-6 rounded-lg shadow-md text-center">
                            <div
                                class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 bg-primary-light rounded-full mb-2 sm:mb-3 md:mb-4">
                                <i class="fas fa-users text-sm sm:text-base md:text-lg lg:text-xl text-primary"></i>
                            </div>
                            <h3 class="font-bold text-secondary-dark mb-1 text-xs sm:text-sm md:text-base">Group Size</h3>
                            <p class="text-secondary text-xs sm:text-sm">Max 12 People</p>
                        </div>

                        <div class="bg-white p-3 sm:p-4 md:p-6 rounded-lg shadow-md text-center">
                            <div
                                class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 bg-primary-light rounded-full mb-2 sm:mb-3 md:mb-4">
                                <i class="fas fa-language text-sm sm:text-base md:text-lg lg:text-xl text-primary"></i>
                            </div>
                            <h3 class="font-bold text-secondary-dark mb-1 text-xs sm:text-sm md:text-base">Languages</h3>
                            <p class="text-secondary text-xs sm:text-sm">English, Indonesian</p>
                        </div>

                        <div class="bg-white p-3 sm:p-4 md:p-6 rounded-lg shadow-md text-center">
                            <div
                                class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 bg-primary-light rounded-full mb-2 sm:mb-3 md:mb-4">
                                <i class="fas fa-hiking text-sm sm:text-base md:text-lg lg:text-xl text-primary"></i>
                            </div>
                            <h3 class="font-bold text-secondary-dark mb-1 text-xs sm:text-sm md:text-base">Activity Level
                            </h3>
                            <p class="text-secondary text-xs sm:text-sm">Moderate</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 lg:mt-0 animate-on-scroll animation-delay-300">
                    <div class="bg-white rounded-lg shadow-xl overflow-hidden lg:sticky lg:top-24">
                        <div class="p-4 sm:p-6">
                            <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-4 sm:mb-6">Highlights</h3>
                            <ul class="space-y-3 sm:space-y-4">
                                <li class="flex">
                                    <i class="fas fa-check-circle text-primary mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                    <span class="text-secondary text-sm sm:text-base">Island hopping to 5 different islands
                                        in the Banyak archipelago</span>
                                </li>
                                <li class="flex">
                                    <i class="fas fa-check-circle text-primary mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                    <span class="text-secondary text-sm sm:text-base">Snorkeling in pristine coral reefs
                                        with diverse marine life</span>
                                </li>
                                <li class="flex">
                                    <i class="fas fa-check-circle text-primary mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                    <span class="text-secondary text-sm sm:text-base">Beach camping under the stars on a
                                        private island</span>
                                </li>
                                <li class="flex">
                                    <i class="fas fa-check-circle text-primary mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                    <span class="text-secondary text-sm sm:text-base">Cultural exchange with local fishing
                                        communities</span>
                                </li>
                                <li class="flex">
                                    <i class="fas fa-check-circle text-primary mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                    <span class="text-secondary text-sm sm:text-base">Fresh seafood barbecues on the
                                        beach</span>
                                </li>
                                <li class="flex">
                                    <i class="fas fa-check-circle text-primary mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                    <span class="text-secondary text-sm sm:text-base">Optional surfing at world-class
                                        breaks (for experienced surfers)</span>
                                </li>
                                <li class="flex">
                                    <i class="fas fa-check-circle text-primary mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                                    <span class="text-secondary text-sm sm:text-base">Turtle conservation activities
                                        (seasonal)</span>
                                </li>
                            </ul>
                        </div>
                        <div class="bg-neutral-light p-6">
                            <h3 class="text-xl font-bold text-secondary-dark mb-4">Share This Tour</h3>
                            <div class="flex space-x-4">
                                <a href="#"
                                    class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white hover:bg-blue-700 transition-colors">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#"
                                    class="h-10 w-10 rounded-full bg-sky-500 flex items-center justify-center text-white hover:bg-sky-600 transition-colors">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#"
                                    class="h-10 w-10 rounded-full bg-red-600 flex items-center justify-center text-white hover:bg-red-700 transition-colors">
                                    <i class="fab fa-pinterest"></i>
                                </a>
                                <a href="#"
                                    class="h-10 w-10 rounded-full bg-green-600 flex items-center justify-center text-white hover:bg-green-700 transition-colors">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Itinerary Section -->
    <section id="itinerary" class="py-8 sm:py-12 md:py-16 bg-neutral-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2
                class="text-xl sm:text-2xl md:text-3xl font-bold text-primary-dark mb-6 sm:mb-8 md:mb-12 text-center animate-on-scroll">
                Tour Itinerary
            </h2>

            <div class="max-w-4xl mx-auto">
                @if (isset($travelPackage) && $travelPackage->itineraries->count() > 0)
                    @foreach ($travelPackage->itineraries as $index => $itinerary)
                        <!-- Day {{ $itinerary->day }} -->
                        <div class="relative animate-on-scroll {{ $index > 0 ? 'animation-delay-' . 300 * $index : '' }}">
                            <!-- Timeline line -->
                            @if (!$loop->last)
                                <div class="absolute left-4 sm:left-6 md:left-8 top-0 bottom-0 w-0.5 bg-primary-light">
                                </div>
                            @endif

                            <!-- Day content -->
                            <div class="relative flex items-start {{ !$loop->last ? 'mb-6 sm:mb-8 md:mb-12' : '' }}">
                                <div
                                    class="flex-shrink-0 h-8 w-8 sm:h-12 sm:w-12 md:h-16 md:w-16 rounded-full bg-primary flex items-center justify-center text-white font-bold text-xs sm:text-sm md:text-base z-10">
                                    <span class="hidden sm:inline">Day {{ $itinerary->day }}</span>
                                    <span class="sm:hidden">{{ $itinerary->day }}</span>
                                </div>
                                <div class="ml-4 sm:ml-6 md:ml-8 bg-white p-3 sm:p-4 md:p-6 rounded-lg shadow-md">
                                    <h3 class="text-base sm:text-lg md:text-xl font-bold text-secondary-dark mb-2">
                                        {{ $itinerary->activity }}</h3>
                                    @if (isset($travelPackage->itineraryByDay[$itinerary->day]))
                                        <ul class="space-y-3 text-secondary">
                                            @foreach ($travelPackage->itineraryByDay[$itinerary->day] as $detail)
                                                <li class="flex">
                                                    <i class="fas fa-circle text-xs text-primary mt-1.5 mr-3"></i>
                                                    <span>{{ $detail['activity'] }}</span>
                                                </li>
                                                @if (!empty($detail['note']))
                                                    <li class="flex pl-6 text-sm text-secondary-light italic">
                                                        <span>{{ $detail['note'] }}</span>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-white p-6 rounded-lg shadow-md text-center">
                        <p class="text-secondary">Itinerary details will be available soon.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Inclusions & Exclusions Section -->
    <section id="inclusions" class="py-16 bg-neutral">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-primary-dark mb-12 text-center animate-on-scroll">
                What's Included & Not Included
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Inclusions -->
                <div class="bg-white p-8 rounded-lg shadow-xl animate-on-scroll">
                    <h3 class="text-2xl font-bold text-primary mb-6 flex items-center">
                        <i class="fas fa-check-circle text-primary mr-3"></i> Included
                    </h3>
                    @if (isset($travelPackage) && count($travelPackage->includesList) > 0)
                        <ul class="space-y-4">
                            @foreach ($travelPackage->includesList as $include)
                                <li class="flex">
                                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                    <span class="text-secondary">{{ $include }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-secondary">Inclusion details will be available soon.</p>
                    @endif
                </div>

                <!-- Exclusions -->
                <div class="bg-white p-8 rounded-lg shadow-xl animate-on-scroll animation-delay-300">
                    <h3 class="text-2xl font-bold text-secondary-dark mb-6 flex items-center">
                        <i class="fas fa-times-circle text-red-500 mr-3"></i> Not Included
                    </h3>
                    @if (isset($travelPackage) && count($travelPackage->excludesList) > 0)
                        <ul class="space-y-4">
                            @foreach ($travelPackage->excludesList as $exclude)
                                <li class="flex">
                                    <i class="fas fa-times text-red-500 mt-1 mr-3"></i>
                                    <span class="text-secondary">{{ $exclude }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-secondary">Exclusion details will be available soon.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="py-6 sm:py-8 md:py-12 lg:py-16 bg-neutral-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2
                class="text-xl sm:text-2xl md:text-3xl font-bold text-primary-dark mb-6 sm:mb-8 md:mb-12 text-center animate-on-scroll">
                Tour Gallery
            </h2>

            @if (isset($travelPackage) && count($travelPackage->galleryImages) > 0)
                <!-- Dynamic Gallery Slider from Database -->
                <div class="gallery-slider-container relative">
                    <div class="swiper gallery-swiper">
                        <div class="swiper-wrapper">
                            @foreach ($travelPackage->galleryImages as $image)
                                <div class="swiper-slide">
                                    <div class="rounded-lg overflow-hidden shadow-md h-40 sm:h-48 md:h-56 lg:h-64 xl:h-72 cursor-pointer"
                                        data-lightbox-src="{{ $image['url'] }}">
                                        <img src="{{ $image['medium'] ?? $image['url'] }}"
                                            alt="Gallery Image {{ $loop->iteration }}"
                                            class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                                            loading="lazy">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Navigation buttons -->
                        <div class="swiper-button-next text-primary hover:text-primary-dark"></div>
                        <div class="swiper-button-prev text-primary hover:text-primary-dark"></div>

                        <!-- Pagination -->
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            @else
                <!-- Fallback Static Gallery -->
                <div class="gallery-slider-container relative">
                    <div class="swiper gallery-swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="rounded-lg overflow-hidden shadow-md h-40 sm:h-48 md:h-56 lg:h-64 xl:h-72 cursor-pointer"
                                    data-lightbox-src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80">
                                    <img src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                                        alt="Beach view"
                                        class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                                        loading="lazy">
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="rounded-lg overflow-hidden shadow-md h-48 sm:h-56 md:h-64 lg:h-72 cursor-pointer"
                                    data-lightbox-src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80">
                                    <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                                        alt="Underwater scene"
                                        class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                                        loading="lazy">
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="rounded-lg overflow-hidden shadow-md h-48 sm:h-56 md:h-64 lg:h-72 cursor-pointer"
                                    data-lightbox-src="https://images.unsplash.com/photo-1583468982228-19f19164aee3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80">
                                    <img src="https://images.unsplash.com/photo-1583468982228-19f19164aee3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80"
                                        alt="Island view"
                                        class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                                        loading="lazy">
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="rounded-lg overflow-hidden shadow-md h-48 sm:h-56 md:h-64 lg:h-72 cursor-pointer"
                                    data-lightbox-src="https://images.unsplash.com/photo-1596392927852-2a18c336fb78?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80">
                                    <img src="https://images.unsplash.com/photo-1596392927852-2a18c336fb78?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80"
                                        alt="Jungle trek"
                                        class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                                        loading="lazy">
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="rounded-lg overflow-hidden shadow-md h-48 sm:h-56 md:h-64 lg:h-72 cursor-pointer"
                                    data-lightbox-src="https://images.unsplash.com/photo-1551352912-484163ad5be9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80">
                                    <img src="https://images.unsplash.com/photo-1551352912-484163ad5be9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                                        alt="Cultural experience"
                                        class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                                        loading="lazy">
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="rounded-lg overflow-hidden shadow-md h-48 sm:h-56 md:h-64 lg:h-72 cursor-pointer"
                                    data-lightbox-src="https://images.unsplash.com/photo-1540541338287-41700207dee6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80">
                                    <img src="https://images.unsplash.com/photo-1540541338287-41700207dee6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                                        alt="Beach camping"
                                        class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                                        loading="lazy">
                                </div>
                            </div>
                        </div>

                        <!-- Navigation buttons -->
                        <div class="swiper-button-next text-primary hover:text-primary-dark"></div>
                        <div class="swiper-button-prev text-primary hover:text-primary-dark"></div>

                        <!-- Pagination -->
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Reviews Section -->
    <section id="reviews" class="py-8 sm:py-12 md:py-16 bg-neutral">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2
                class="text-xl sm:text-2xl md:text-3xl font-bold text-primary-dark mb-6 sm:mb-8 md:mb-12 text-center animate-on-scroll">
                Traveler Reviews
            </h2>

            <div class="mb-6 sm:mb-8 md:mb-12 bg-white rounded-lg shadow-xl p-4 sm:p-6 md:p-8 animate-on-scroll">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6">
                    <div class="flex items-center mb-4 sm:mb-0">
                        <div class="h-12 w-12 sm:h-16 sm:w-16 rounded-full bg-gray-300 mr-3 sm:mr-4"></div>
                        <div>
                            <h4 class="font-bold text-secondary-dark text-sm sm:text-base">Overall Rating</h4>
                            <div class="flex items-center">
                                <div class="flex text-yellow-400 mr-2 text-sm sm:text-base">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="text-secondary-dark font-medium text-sm sm:text-base">4.8 out of 5</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <div class="text-secondary-dark font-medium text-sm sm:text-base">Based on 24 reviews</div>
                        <a href="#" class="text-primary hover:underline text-sm sm:text-base">Write a Review</a>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div class="bg-neutral-light p-3 sm:p-4 rounded-lg">
                        <div class="text-secondary-dark font-medium mb-2 text-xs sm:text-sm">Value for Money</div>
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="bg-neutral-light p-3 sm:p-4 rounded-lg">
                        <div class="text-secondary-dark font-medium mb-2 text-xs sm:text-sm">Accommodation</div>
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                    </div>
                    <div class="bg-neutral-light p-3 sm:p-4 rounded-lg">
                        <div class="text-secondary-dark font-medium mb-2 text-xs sm:text-sm">Activities</div>
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                    <div class="bg-neutral-light p-3 sm:p-4 rounded-lg">
                        <div class="text-secondary-dark font-medium mb-2 text-xs sm:text-sm">Guide Quality</div>
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <!-- Review 1 -->
                <div class="bg-white p-6 rounded-lg shadow-md animate-on-scroll">
                    <div class="flex items-center mb-4">
                        <div class="h-12 w-12 rounded-full bg-gray-300 mr-4"></div>
                        <div>
                            <h4 class="font-bold text-secondary-dark">Sarah Johnson</h4>
                            <p class="text-secondary text-sm">United States | Traveled April 2023</p>
                        </div>
                    </div>
                    <div class="flex text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <h5 class="font-bold text-secondary-dark mb-2">A paradise that exceeded expectations!</h5>
                    <p class="text-secondary mb-4">
                        Our Banyak Islands tour was absolutely incredible! The beaches were pristine, the snorkeling was
                        world-class, and our guide was knowledgeable and friendly. The beach camping night was magical -
                        falling asleep to the sound of waves and waking up to the most beautiful sunrise. The food was fresh
                        and delicious throughout the trip. I can't recommend this experience enough!
                    </p>
                </div>

                <!-- Review 2 -->
                <div class="bg-white p-6 rounded-lg shadow-md animate-on-scroll animation-delay-300">
                    <div class="flex items-center mb-4">
                        <div class="h-12 w-12 rounded-full bg-gray-300 mr-4"></div>
                        <div>
                            <h4 class="font-bold text-secondary-dark">David Chen</h4>
                            <p class="text-secondary text-sm">Singapore | Traveled February 2023</p>
                        </div>
                    </div>
                    <div class="flex text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <h5 class="font-bold text-secondary-dark mb-2">Great tour with minor accommodation issues</h5>
                    <p class="text-secondary mb-4">
                        The Banyak Islands are truly a hidden gem with incredible marine life and beautiful beaches. Our
                        guide Budi was excellent and very knowledgeable about the local ecosystem. The only reason I'm not
                        giving 5 stars is that the accommodation on Balai Island was more basic than expected. That said,
                        the camping night was a highlight of the trip, and the overall experience was fantastic. Would
                        recommend for adventurous travelers!
                    </p>
                </div>

                <!-- Review 3 -->
                <div class="bg-white p-6 rounded-lg shadow-md animate-on-scroll animation-delay-600">
                    <div class="flex items-center mb-4">
                        <div class="h-12 w-12 rounded-full bg-gray-300 mr-4"></div>
                        <div>
                            <h4 class="font-bold text-secondary-dark">Emma Wilson</h4>
                            <p class="text-secondary text-sm">Australia | Traveled March 2023</p>
                        </div>
                    </div>
                    <div class="flex text-yellow-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <h5 class="font-bold text-secondary-dark mb-2">Unforgettable island adventure</h5>
                    <p class="text-secondary mb-4">
                        This tour was the highlight of our Indonesia trip! The Banyak Islands are absolutely stunning and
                        still relatively untouched by tourism. We saw incredible marine life while snorkeling, including
                        reef sharks, turtles, and countless colorful fish. The cultural visit to the fishing village was
                        fascinating and gave us insight into local life. Our guide was professional and friendly, and the
                        food was delicious. Highly recommend for anyone looking to get off the beaten path!
                    </p>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="#"
                    class="inline-block bg-primary-light hover:bg-primary text-primary hover:text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                    View All 24 Reviews
                </a>
            </div>
        </div>
    </section>

    <!-- Booking Section -->
    <section id="booking" class="py-8 sm:py-12 md:py-16 bg-neutral-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2
                class="text-xl sm:text-2xl md:text-3xl font-bold text-primary-dark mb-6 sm:mb-8 md:mb-12 text-center animate-on-scroll">
                Book This Tour
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 xl:gap-12">
                <!-- Booking Form -->
                <div class="lg:col-span-2 bg-white p-4 sm:p-6 lg:p-8 rounded-lg shadow-xl animate-on-scroll">
                    <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-secondary-dark mb-4 sm:mb-6">Booking Form</h3>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L10 11.414l2.707-2.707a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Mohon perbaiki kesalahan berikut:</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Success Message -->
                    @if (session('success'))
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Error Message -->
                    @if (session('error'))
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L10 11.414l2.707-2.707a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif



                    @auth
                        <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                    @else
                        <div class="text-center py-8 sm:py-12 md:py-16">
                            <div class="mb-6">
                                <i class="fas fa-lock text-4xl sm:text-5xl md:text-6xl text-secondary mb-4 sm:mb-6"></i>
                                <h4 class="text-xl sm:text-2xl md:text-3xl font-bold text-secondary-dark mb-3">Login Required</h4>
                                <p class="text-base sm:text-lg text-secondary mb-6 sm:mb-8 max-w-md mx-auto">Please log in to your account to make a booking and view pricing details.</p>
                                <a href="{{ route('auth', ['intended' => url()->current() . '#booking']) }}"
                                    class="inline-block bg-primary hover:bg-primary-dark text-white font-bold py-3 sm:py-4 px-6 sm:px-8 rounded-lg transition duration-300 text-base sm:text-lg transform hover:scale-105 shadow-lg">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Login to Book
                                </a>
                            </div>
                        </div>
                    @endauth

                    @auth
                        <!-- Step Indicator (2 steps only) -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div id="step-indicator-1" class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white text-sm font-bold mr-2">
                                        1
                                    </div>
                                    <span class="text-sm font-medium text-primary">Personal Form</span>
                                </div>
                                <div class="flex-1 h-1 bg-gray-200 mx-4">
                                    <div id="progress-bar-1" class="h-full bg-primary transition-all duration-300" style="width: 50%"></div>
                                </div>
                                <div class="flex items-center">
                                    <div id="step-indicator-2" class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-500 text-sm font-bold mr-2">
                                        2
                                    </div>
                                    <span class="text-sm font-medium text-gray-500">Konfirmasi & Pembayaran</span>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('booking.store') }}" method="POST" id="bookingForm" class="space-y-6">
                            @csrf
                            <input type="hidden" name="travel_package_id" value="{{ $travelPackage->id ?? 1 }}">
                            <input type="hidden" id="total_price" name="total_price" value="0">

                            <!-- Step 1: Booking Information -->
                            <div id="step1" class="booking-step">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                                    <div>
                                        <label for="name"
                                            class="block text-xs sm:text-sm font-medium text-secondary-dark mb-1 sm:mb-2">Full
                                            Name</label>
                                        <input type="text" id="name" name="name"
                                            value="{{ auth()->user()->name ?? old('name', '') }}"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm sm:text-base"
                                            required>
                                    </div>
                                    <div>
                                        <label for="contact"
                                            class="block text-xs sm:text-sm font-medium text-secondary-dark mb-1 sm:mb-2">Contact
                                            Number</label>
                                        <input type="tel" id="contact" name="contact"
                                            value="{{ auth()->user()->contact ?? old('contact', '') }}"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm sm:text-base"
                                            required>
                                        <p class="text-xs text-secondary mt-1">Will be used for booking communication</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                                    <div>
                                        <label for="booking_date"
                                            class="block text-xs sm:text-sm font-medium text-secondary-dark mb-1 sm:mb-2">Travel
                                            Date</label>
                                        <input type="date" id="booking_date" name="booking_date"
                                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                            value="{{ old('booking_date', '') }}"
                                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-sm sm:text-base"
                                            required>
                                    </div>
                                    <div>
                                        <label for="person_count"
                                            class="block text-sm font-medium text-secondary-dark mb-2">Number of
                                            Participants</label>
                                        <select id="person_count" name="person_count"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                            required>
                                            <option value="" disabled>Select number of participants</option>
                                            @php
                                                $basePersonCount = $travelPackage->base_person_count ?? 1;
                                                $maxCapacity = $travelPackage->capacity ?? 12;
                                            @endphp
                                            @for ($i = $basePersonCount; $i <= $maxCapacity; $i++)
                                                <option value="{{ $i }}" {{ $i == $basePersonCount ? 'selected' : '' }}>
                                                    {{ $i }} {{ $i == 1 ? 'Person' : 'People' }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label for="special_requests"
                                        class="block text-sm font-medium text-secondary-dark mb-2">Special Requests <span
                                            class="text-gray-400">(Optional)</span></label>
                                    <textarea id="special_requests" name="special_requests" rows="4"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                        placeholder="Dietary needs, accessibility requirements, or special occasions?">{{ old('special_requests', '') }}</textarea>
                                </div>

                                <div class="mb-6">
                                    <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                                        <input id="terms" name="terms" type="checkbox"
                                            class="w-5 h-5 border border-gray-300 rounded focus:ring-2 focus:ring-primary text-primary mt-0.5"
                                            required>
                                        <label for="terms" class="text-sm text-secondary leading-relaxed">
                                            I agree to the <a href="{{ route('terms') }}"
                                                class="text-primary hover:underline font-medium" target="_blank">Terms
                                                and Conditions</a> and <a href="{{ route('privacy') }}"
                                                class="text-primary hover:underline font-medium" target="_blank">Privacy
                                                Policy</a>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <button type="button" id="next-to-step2"
                                        class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition duration-300 transform hover:scale-105 shadow-md text-sm sm:text-base">
                                        Continue to Confirmation
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Payment Confirmation -->
                            <div id="step2" class="booking-step hidden">
                                <h3 class="text-xl font-bold text-secondary-dark mb-6">Confirm Your Booking</h3>

                                <!-- Booking Summary -->
                                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                                    <h4 class="font-bold text-secondary-dark mb-4">Booking Details</h4>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-secondary">Name:</span>
                                            <span id="confirm-name" class="font-medium text-secondary-dark"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-secondary">Contact:</span>
                                            <span id="confirm-contact" class="font-medium text-secondary-dark"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-secondary">Travel Date:</span>
                                            <span id="confirm-date" class="font-medium text-secondary-dark"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-secondary">Number of Participants:</span>
                                            <span id="confirm-person-count" class="font-medium text-secondary-dark"></span>
                                        </div>
                                        <div id="confirm-special-requests-container" class="hidden">
                                            <div class="flex justify-between">
                                                <span class="text-secondary">Special Requests:</span>
                                                <span id="confirm-special-requests" class="font-medium text-secondary-dark"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price Summary -->
                                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                                    <h4 class="font-bold text-secondary-dark mb-4">Price Summary</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span id="confirm-base-price-label" class="text-secondary">Base Price</span>
                                            <span id="confirm-base-price" class="font-medium text-secondary-dark"></span>
                                        </div>
                                        <div id="confirm-additional-person-cost">
                                            <!-- Additional person cost will be inserted here -->
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-secondary">Tax</span>
                                            <span id="confirm-tax-price" class="font-medium text-secondary-dark"></span>
                                        </div>
                                        <div class="border-t border-gray-200 pt-2 flex justify-between">
                                            <span id="confirm-total-label" class="font-bold text-secondary-dark">Total</span>
                                            <span id="confirm-total-price" class="font-bold text-primary"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex space-x-4">
                                    <button type="button" id="back-to-step1"
                                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition duration-300">
                                        Back
                                    </button>
                                    <button type="button" id="next-to-step3"
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition duration-300 transform hover:scale-105 shadow-md">
                                        <i class="fas fa-credit-card mr-2"></i>Pay Now
                                    </button>
                                </div>
                            </div>

                            <!-- Step 3 has been removed - using Midtrans Snap Redirect instead -->
                        </form>
                    @endauth
                </div>

                <!-- Booking Info -->
                <div class="animate-on-scroll animation-delay-300">
                    <!-- Price Details moved to Step 2 confirmation -->
                    <div class="bg-white p-4 sm:p-6 rounded-lg shadow-xl mb-6 sm:mb-8 hidden" id="price-details-sidebar">
                        <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-3 sm:mb-4">Price Details</h3>
                        <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                            <div class="flex justify-between">
                                <span id="base-price-label" class="text-secondary text-sm sm:text-base">Base Price (1 person)</span>
                                <span id="base-price" class="font-medium text-secondary-dark text-sm sm:text-base">Rp
                                    {{ number_format((float)(isset($travelPackage) ? $travelPackage->price : 0), 0, ',', '.') }}</span>
                            </div>
                            <div id="additional-person-cost">
                                <!-- Additional person cost will be inserted here by JavaScript -->
                            </div>
                            <div class="flex justify-between">
                                <span class="text-secondary text-sm sm:text-base">Tax (11%)</span>
                                <span id="tax-price" class="font-medium text-secondary-dark text-sm sm:text-base">Rp
                                    {{ number_format((float)((isset($travelPackage) ? $travelPackage->price : 0) * 2 * 0.11), 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-secondary text-sm sm:text-base">Equipment Rental</span>
                                <span class="font-medium text-secondary-dark text-sm sm:text-base">Included</span>
                            </div>
                            <div class="border-t border-gray-200 pt-2 sm:pt-3 flex justify-between">
                                <span id="total-label" class="font-bold text-secondary-dark text-sm sm:text-base">Total
                                    (for 2 people)</span>
                                <span id="total-price" class="font-bold text-primary text-sm sm:text-base">Rp
                                    {{ number_format((float)((isset($travelPackage) ? $travelPackage->price : 0) * 2 * 1.11), 0, ',', '.') }}</span>
                            </div>
                        </div>
                        {{-- <p class="text-xs sm:text-sm text-secondary">
                            * Group discounts available for bookings of 6 or more people
                        </p> --}}
                    </div>

                    {{-- <div class="bg-white p-4 sm:p-6 rounded-lg shadow-xl mb-6 sm:mb-8">
                        <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-3 sm:mb-4">Upcoming Departures</h3>
                        <div class="space-y-3 sm:space-y-4">
                            @php
                                $departures = [
                                    [
                                        'date' => \Carbon\Carbon::now()->addDays(15)->format('F d, Y'),
                                        'spots' => 5,
                                        'status' => 'Available'
                                    ],
                                    [
                                        'date' => \Carbon\Carbon::now()->addDays(30)->format('F d, Y'),
                                        'spots' => 8,
                                        'status' => 'Available'
                                    ],
                                    [
                                        'date' => \Carbon\Carbon::now()->addDays(45)->format('F d, Y'),
                                        'spots' => 0,
                                        'status' => 'Fully Booked'
                                    ],
                                    [
                                        'date' => \Carbon\Carbon::now()->addDays(60)->format('F d, Y'),
                                        'spots' => 10,
                                        'status' => 'Available'
                                    ],
                                ];
                            @endphp

                            @foreach ($departures as $index => $departure)
                                <div class="flex justify-between items-center {{ $index < count($departures) - 1 ? 'pb-3 border-b border-gray-200' : '' }}">
                                    <div>
                                        <div class="font-medium text-secondary-dark">{{ $departure['date'] }}</div>
                                        <div class="text-sm text-secondary">{{ $departure['spots'] }} spots left</div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $departure['status'] == 'Available' ? 'bg-primary-light text-primary-dark' : 'bg-secondary-light text-secondary-dark' }}">
                                        {{ $departure['status'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div> --}}

                    <div class="bg-white p-6 rounded-lg shadow-xl">
                        <h3 class="text-xl font-bold text-secondary-dark mb-4">Need Help?</h3>
                        <p class="text-secondary mb-4">Our travel experts are here to help you plan the perfect trip.
                            Contact us for personalized assistance.</p>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-primary mr-3"></i>
                                <span class="text-secondary-dark">+62 812-3456-7890</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-primary mr-3"></i>
                                <span class="text-secondary-dark">info@adventuretravel.com</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-primary mr-3"></i>
                                <span class="text-secondary-dark">Mon-Fri: 9AM-6PM WIB</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-2 sm:p-4">
        <div class="relative max-w-full max-h-full sm:max-w-4xl">
            <img id="lightbox-image" src="" alt="Gallery Image"
                class="max-w-full max-h-full object-contain rounded-lg">
            <button onclick="closeLightbox()"
                class="absolute top-2 right-2 sm:top-4 sm:right-4 text-white text-xl sm:text-2xl md:text-3xl hover:text-gray-300 transition-colors bg-black bg-opacity-50 rounded-full w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <style>
        /* Custom Swiper Styles */
        .gallery-swiper {
            padding-bottom: 40px;
        }

        .gallery-swiper .swiper-slide {
            width: auto;
            margin-right: 15px;
        }

        .gallery-swiper .swiper-button-next,
        .gallery-swiper .swiper-button-prev {
            color: #3B82F6;
            background: rgba(255, 255, 255, 0.9);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            margin-top: -18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .gallery-swiper .swiper-button-next:after,
        .gallery-swiper .swiper-button-prev:after {
            font-size: 14px;
            font-weight: bold;
        }

        .gallery-swiper .swiper-pagination {
            bottom: 8px;
        }

        .gallery-swiper .swiper-pagination-bullet {
            background: #3B82F6;
            opacity: 0.5;
            width: 8px;
            height: 8px;
        }

        .gallery-swiper .swiper-pagination-bullet-active {
            opacity: 1;
        }

        /* Responsive breakpoints */
        @media (max-width: 640px) {
            .gallery-swiper {
                padding-bottom: 35px;
            }

            .gallery-swiper .swiper-slide {
                width: 260px;
                margin-right: 12px;
            }

            .gallery-swiper .swiper-button-next,
            .gallery-swiper .swiper-button-prev {
                width: 32px;
                height: 32px;
                margin-top: -16px;
            }

            .gallery-swiper .swiper-button-next:after,
            .gallery-swiper .swiper-button-prev:after {
                font-size: 12px;
            }
        }

        @media (min-width: 641px) and (max-width: 768px) {
            .gallery-swiper .swiper-slide {
                width: 300px;
                margin-right: 16px;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .gallery-swiper .swiper-slide {
                width: 340px;
                margin-right: 20px;
            }

            .gallery-swiper .swiper-button-next,
            .gallery-swiper .swiper-button-prev {
                width: 40px;
                height: 40px;
                margin-top: -20px;
            }

            .gallery-swiper .swiper-button-next:after,
            .gallery-swiper .swiper-button-prev:after {
                font-size: 16px;
            }
        }

        @media (min-width: 1025px) {
            .gallery-swiper {
                padding-bottom: 50px;
            }

            .gallery-swiper .swiper-slide {
                width: 380px;
                margin-right: 24px;
            }

            .gallery-swiper .swiper-button-next,
            .gallery-swiper .swiper-button-prev {
                width: 44px;
                height: 44px;
                margin-top: -22px;
            }

            .gallery-swiper .swiper-button-next:after,
            .gallery-swiper .swiper-button-prev:after {
                font-size: 18px;
            }
        }

        /* Lightbox styles */
        #lightbox {
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        #lightbox img {
            border-radius: 8px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transition: opacity 0.3s ease;
        }

        /* Additional responsive utilities */
        @media (max-width: 640px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            /* Improve touch targets */
            button,
            .btn,
            a {
                min-height: 44px;
                min-width: 44px;
            }

            /* Better spacing for mobile */
            .space-y-6>*+* {
                margin-top: 1.5rem;
            }

            .space-y-8>*+* {
                margin-top: 2rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            /* Smaller text for very small screens */
            .text-sm {
                font-size: 0.8rem;
            }

            .text-base {
                font-size: 0.9rem;
            }
        }

        /* Improve scrolling performance */
        * {
            -webkit-overflow-scrolling: touch;
        }

        /* Prevent horizontal scroll */
        body {
            overflow-x: hidden;
        }

        /* Better focus states for accessibility */
        button:focus,
        input:focus,
        textarea:focus,
        select:focus {
            outline: 2px solid #3B82F6;
            outline-offset: 2px;
        }

        /* Optimize images */
        img {
            max-width: 100%;
            height: auto;
        }

        /* Loading state for images */
        img.lazy {
            opacity: 0;
            transition: opacity 0.3s;
        }

        img.lazy.loaded {
            opacity: 1;
        }
    </style>
@endpush

@push('scripts')
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <!-- Pass data from backend to JavaScript -->
    <script>
        window.travelPackageData = {
            basePersonCount: {{ $travelPackage->base_person_count ?? 1 }},
            capacity: {{ $travelPackage->capacity ?? 12 }},
            price: {{ $travelPackage->price ?? 0 }},
            additionalPersonPrice: {{ $travelPackage->additional_person_price ?? 0 }},
            taxPercentage: {{ $travelPackage->tax_percentage ?? 0 }}
        };
    </script>

    <!-- Travel Package Detail JavaScript -->
    @vite(['resources/js/travel-package-config.js', 'resources/js/travel-package-detail.js'])

    <!-- All JavaScript functionality has been moved to external files -->
@endpush
