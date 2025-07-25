@extends('layouts.app')

@section('title', 'Travel Packages')

@section('content')
    <!-- Hero Section -->
    <section
        class="relative pt-20 sm:pt-24 md:pt-32 pb-12 sm:pb-16 md:pb-20 bg-cover bg-center min-h-screen flex items-center"
        style="background-image: url('https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');"
    >
        <div class="absolute inset-0 bg-secondary-dark opacity-70"></div>
        <div class="relative z-10 w-full mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 sm:mb-6 animate-fade-in leading-tight">
                Discover PT Sumatra Tour Travel Paradise
            </h1>
            <p class="text-lg sm:text-xl md:text-2xl text-white/90 mb-6 sm:mb-8 max-w-4xl mx-auto animate-fade-in animation-delay-300 leading-relaxed">
                Escape to the pristine beauty with PT Sumatra Tour Travel and Aceh Singkil. Experience untouched coral reefs, crystal-clear waters, and secluded beaches where time stands still. Your tropical paradise adventure awaits in Indonesia's best-kept secret.
            </p>
            <div class="max-w-3xl mx-auto animate-fade-in animation-delay-600">
                <p class="text-base sm:text-lg md:text-xl text-white font-medium italic">
                    "Where 99 islands create endless possibilities for your perfect getaway."
                </p>
            </div>
        </div>
    </section>
    <!-- Packages Section -->
    <section id="packages" class="py-12 sm:py-16 md:py-20 bg-neutral">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-10 md:mb-12">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary-dark mb-3 sm:mb-4 animate-on-scroll">
                    PT Sumatra Tour Travel Packages
                </h2>
                <p class="text-lg sm:text-xl md:text-2xl text-secondary-dark max-w-3xl mx-auto animate-on-scroll animation-delay-300 leading-relaxed">
                    Discover the magic with PT Sumatra Tour Travel and Aceh Singkil with our exclusive island-hopping adventures and marine experiences
                </p>
            </div>

            <!-- Package Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                @if(isset($travelPackages) && count($travelPackages) > 0)
                    @foreach($travelPackages as $package)
                        <div class="animate-on-scroll">
                            <x-travel-package-card
                                :package="$package"
                                :isBestseller="false"
                                :isEcoTour="false"
                            />
                        </div>
                    @endforeach
                @else
                    <!-- Placeholder Packages for Demo -->
                    <!-- Package 1 -->
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 animate-on-scroll">
                        <div class="relative">
                            <img
                                src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                                alt="PT Sumatra Tour Travel Explorer"
                                class="w-full h-48 sm:h-52 md:h-56 lg:h-64 object-cover"
                            >
                            <div class="absolute top-3 sm:top-4 right-3 sm:right-4 bg-primary text-white text-xs sm:text-sm font-bold px-2 sm:px-3 py-1 rounded-full">
                                5 Days
                            </div>
                        </div>
                        <div class="p-4 sm:p-5 md:p-6">
                            <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-2">
                                PT Sumatra Tour Travel Explorer
                            </h3>
                            <div class="flex items-center mb-3 sm:mb-4">
                                <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                <span class="text-secondary text-sm sm:text-base">PT Sumatra Tour Travel, Aceh</span>
                            </div>
                            <p class="text-secondary mb-3 sm:mb-4 line-clamp-3 text-sm sm:text-base">
                                Discover the pristine beaches and vibrant marine life with PT Sumatra Tour Travel. This package includes island hopping, snorkeling, and cultural experiences with local communities.
                            </p>
                            <div class="flex items-center justify-between mb-3 sm:mb-4">
                                <!-- IMPORTANT: DO NOT DELETE - Package Rating -->
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span class="text-secondary-dark font-medium text-sm sm:text-base">4.8 (24)</span>
                                </div>
                                <!-- End Package Rating -->
                                <div class="text-primary-dark font-bold text-sm sm:text-base">
                                    Rp 5.500.000
                                </div>
                            </div>
                            <a
                                href="#"
                                class="block w-full bg-primary hover:bg-primary-dark text-white text-center font-bold py-2 sm:py-3 px-4 rounded-lg transition duration-300 text-sm sm:text-base"
                            >
                                View Details
                            </a>
                        </div>
                    </div>

                    <!-- Package 2 -->
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 animate-on-scroll animation-delay-300">
                        <div class="relative">
                            <img
                                src="https://images.unsplash.com/photo-1544644181-1484b3fdfc32?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                                alt="Nias Surfing Adventure"
                                class="w-full h-48 sm:h-52 md:h-56 lg:h-64 object-cover"
                            >
                            <div class="absolute top-3 sm:top-4 right-3 sm:right-4 bg-primary text-white text-xs sm:text-sm font-bold px-2 sm:px-3 py-1 rounded-full">
                                7 Days
                            </div>
                        </div>
                        <div class="p-4 sm:p-5 md:p-6">
                            <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-2">
                                Nias Surfing Adventure
                            </h3>
                            <div class="flex items-center mb-3 sm:mb-4">
                                <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                <span class="text-secondary text-sm sm:text-base">Nias Island, North Sumatra</span>
                            </div>
                            <p class="text-secondary mb-3 sm:mb-4 line-clamp-3 text-sm sm:text-base">
                                Ride the legendary waves of Nias Island, home to some of the world's best surf breaks. This package includes accommodation, surf lessons, equipment rental, and cultural excursions.
                            </p>
                            <div class="flex items-center justify-between mb-3 sm:mb-4">
                                <!-- IMPORTANT: DO NOT DELETE - Package Rating -->
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span class="text-secondary-dark font-medium text-sm sm:text-base">4.9 (36)</span>
                                </div>
                                <!-- End Package Rating -->
                                <div class="text-primary-dark font-bold text-sm sm:text-base">
                                    Rp 8.750.000
                                </div>
                            </div>
                            <a
                                href="#"
                                class="block w-full bg-primary hover:bg-primary-dark text-white text-center font-bold py-2 sm:py-3 px-4 rounded-lg transition duration-300 text-sm sm:text-base"
                            >
                                View Details
                            </a>
                        </div>
                    </div>

                    <!-- Package 3 -->
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 animate-on-scroll animation-delay-600">
                        <div class="relative">
                            <img
                                src="https://images.unsplash.com/photo-1604999333679-b86d54738315?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                                alt="Lake Toba Cultural Tour"
                                class="w-full h-48 sm:h-52 md:h-56 lg:h-64 object-cover"
                            >
                            <div class="absolute top-3 sm:top-4 right-3 sm:right-4 bg-primary text-white text-xs sm:text-sm font-bold px-2 sm:px-3 py-1 rounded-full">
                                4 Days
                            </div>
                        </div>
                        <div class="p-4 sm:p-5 md:p-6">
                            <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-2">
                                Lake Toba Cultural Tour
                            </h3>
                            <div class="flex items-center mb-3 sm:mb-4">
                                <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                <span class="text-secondary text-sm sm:text-base">Lake Toba, North Sumatra</span>
                            </div>
                            <p class="text-secondary mb-3 sm:mb-4 line-clamp-3 text-sm sm:text-base">
                                Immerse yourself in the rich Batak culture while exploring the world's largest volcanic lake. This tour includes traditional village visits, cultural performances, and scenic boat rides.
                            </p>
                            <div class="flex items-center justify-between mb-3 sm:mb-4">
                                <!-- IMPORTANT: DO NOT DELETE - Package Rating -->
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span class="text-secondary-dark font-medium text-sm sm:text-base">4.7 (19)</span>
                                </div>
                                <!-- End Package Rating -->
                                <div class="text-primary-dark font-bold text-sm sm:text-base">
                                    Rp 3.950.000
                                </div>
                            </div>
                            <a
                                href="#"
                                class="block w-full bg-primary hover:bg-primary-dark text-white text-center font-bold py-2 sm:py-3 px-4 rounded-lg transition duration-300 text-sm sm:text-base"
                            >
                                View Details
                            </a>
                        </div>
                    </div>

                    <!-- Package 4 -->
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 animate-on-scroll">
                        <div class="relative">
                            <img
                                src="https://images.unsplash.com/photo-1583468982228-19f19164aee3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80"
                                alt="Mentawai Islands Surf Camp"
                                class="w-full h-64 object-cover"
                            >
                            <div class="absolute top-4 right-4 bg-primary text-white text-sm font-bold px-3 py-1 rounded-full">
                                10 Days
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-secondary-dark mb-2">
                                Mentawai Islands Surf Camp
                            </h3>
                            <div class="flex items-center mb-4">
                                <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                <span class="text-secondary">Mentawai Islands, West Sumatra</span>
                            </div>
                            <p class="text-secondary mb-4 line-clamp-3">
                                Experience world-class surfing at the legendary Mentawai Islands. This all-inclusive package features beachfront accommodation, daily boat trips to premium surf spots, and professional guides.
                            </p>
                            <div class="flex items-center justify-between mb-4">
                                <!-- IMPORTANT: DO NOT DELETE - Package Rating -->
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span class="text-secondary-dark font-medium">4.9 (42)</span>
                                </div>
                                <!-- End Package Rating -->
                                <div class="text-primary-dark font-bold">
                                    Rp 12.500.000
                                </div>
                            </div>
                            <a
                                href="#"
                                class="block w-full bg-primary hover:bg-primary-dark text-white text-center font-bold py-2 px-4 rounded-lg transition duration-300"
                            >
                                View Details
                            </a>
                        </div>
                    </div>

                    <!-- Fallback Package 5 -->
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 animate-on-scroll animation-delay-300">
                        <div class="relative">
                            <img
                                src="https://images.unsplash.com/photo-1551352912-484163ad5be9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                                alt="Aceh Cultural Heritage"
                                class="w-full h-48 sm:h-52 md:h-56 lg:h-64 object-cover"
                            >
                            <div class="absolute top-3 sm:top-4 right-3 sm:right-4 bg-primary text-white text-xs sm:text-sm font-bold px-2 sm:px-3 py-1 rounded-full">
                                3 Days
                            </div>
                        </div>
                        <div class="p-4 sm:p-5 md:p-6">
                            <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-2">
                                Aceh Cultural Heritage
                            </h3>
                            <div class="flex items-center mb-3 sm:mb-4">
                                <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                <span class="text-secondary text-sm sm:text-base">Banda Aceh, Aceh</span>
                            </div>
                            <p class="text-secondary mb-3 sm:mb-4 line-clamp-3 text-sm sm:text-base">
                                Explore the rich history and unique culture of Aceh. Visit the iconic Baiturrahman Grand Mosque, tsunami memorials, traditional markets, and enjoy authentic Acehnese cuisine.
                            </p>
                            <div class="flex items-center justify-between mb-3 sm:mb-4">
                                <!-- IMPORTANT: DO NOT DELETE - Package Rating -->
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span class="text-secondary-dark font-medium text-sm sm:text-base">4.6 (15)</span>
                                </div>
                                <!-- End Package Rating -->
                                <div class="text-primary-dark font-bold text-sm sm:text-base">
                                    Rp 2.750.000
                                </div>
                            </div>
                            <a
                                href="#"
                                class="block w-full bg-primary hover:bg-primary-dark text-white text-center font-bold py-2 sm:py-3 px-4 rounded-lg transition duration-300 text-sm sm:text-base"
                            >
                                View Details
                            </a>
                        </div>
                    </div>

                    <!-- Fallback Package 6 -->
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 animate-on-scroll animation-delay-600">
                        <div class="relative">
                            <img
                                src="https://images.unsplash.com/photo-1596392927852-2a18c336fb78?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80"
                                alt="Sumatra Wildlife Safari"
                                class="w-full h-48 sm:h-52 md:h-56 lg:h-64 object-cover"
                            >
                            <div class="absolute top-3 sm:top-4 right-3 sm:right-4 bg-primary text-white text-xs sm:text-sm font-bold px-2 sm:px-3 py-1 rounded-full">
                                6 Days
                            </div>
                        </div>
                        <div class="p-4 sm:p-5 md:p-6">
                            <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-2">
                                Sumatra Wildlife Safari
                            </h3>
                            <div class="flex items-center mb-3 sm:mb-4">
                                <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                <span class="text-secondary text-sm sm:text-base">Gunung Leuser National Park, Aceh</span>
                            </div>
                            <p class="text-secondary mb-3 sm:mb-4 line-clamp-3 text-sm sm:text-base">
                                Trek through the lush rainforests of Gunung Leuser National Park in search of orangutans, elephants, and other endangered wildlife. Includes guided hikes, river tubing, and jungle camping.
                            </p>
                            <div class="flex items-center justify-between mb-3 sm:mb-4">
                                <!-- IMPORTANT: DO NOT DELETE - Package Rating -->
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span class="text-secondary-dark font-medium text-sm sm:text-base">4.8 (31)</span>
                                </div>
                                <!-- End Package Rating -->
                                <div class="text-primary-dark font-bold text-sm sm:text-base">
                                    Rp 7.250.000
                                </div>
                            </div>
                            <a
                                href="#"
                                class="block w-full bg-primary hover:bg-primary-dark text-white text-center font-bold py-2 sm:py-3 px-4 rounded-lg transition duration-300 text-sm sm:text-base"
                            >
                                View Details
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            <div class="mt-8 sm:mt-10 md:mt-12 flex justify-center">
                @if(isset($travelPackages) && method_exists($travelPackages, 'links'))
                    {{ $travelPackages->links() }}
                @else
                    <nav class="flex items-center space-x-1 sm:space-x-2">
                        <a href="#" class="px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 border border-gray-300 rounded-md text-secondary-dark hover:bg-gray-50 text-xs sm:text-sm md:text-base">
                            Previous
                        </a>
                        <a href="#" class="px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 border border-primary bg-primary text-white rounded-md text-xs sm:text-sm md:text-base">
                            1
                        </a>
                        <a href="#" class="px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 border border-gray-300 rounded-md text-secondary-dark hover:bg-gray-50 text-xs sm:text-sm md:text-base">
                            2
                        </a>
                        <a href="#" class="px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 border border-gray-300 rounded-md text-secondary-dark hover:bg-gray-50 text-xs sm:text-sm md:text-base">
                            3
                        </a>
                        <span class="px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 text-secondary-dark text-xs sm:text-sm md:text-base">
                            ...
                        </span>
                        <a href="#" class="px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 border border-gray-300 rounded-md text-secondary-dark hover:bg-gray-50 text-xs sm:text-sm md:text-base">
                            Next
                        </a>
                    </nav>
                @endif
            </div>
        </div>
    </section>

    <!-- Destinations Section -->
    <section id="destinations" class="py-12 sm:py-16 md:py-20 bg-neutral-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-10 md:mb-12">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary-dark mb-3 sm:mb-4 animate-on-scroll">
                    PT Sumatra Tour Travel Highlights
                </h2>
                <p class="text-lg sm:text-xl md:text-2xl text-secondary-dark max-w-3xl mx-auto animate-on-scroll animation-delay-300 leading-relaxed">
                    Explore the most spectacular spots with PT Sumatra Tour Travel and Aceh Singkil region
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                <!-- Destination 1 -->
                <div class="relative rounded-lg overflow-hidden h-64 sm:h-72 md:h-80 group animate-on-scroll">
                    <img
                        src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                        alt="PT Sumatra Tour Travel"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-secondary-dark to-transparent opacity-70"></div>
                    <div class="absolute bottom-0 left-0 p-4 sm:p-5 md:p-6">
                        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-white mb-1 sm:mb-2">Pulau Tailana</h3>
                        <p class="text-white/90 mb-2 sm:mb-3 md:mb-4 text-sm sm:text-base">Crystal clear waters & white sand beaches</p>
                        <a
                            href="#"
                            class="inline-flex items-center text-white font-medium hover:text-primary transition-colors text-sm sm:text-base"
                        >
                            Explore Packages <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Destination 2 -->
                <div class="relative rounded-lg overflow-hidden h-64 sm:h-72 md:h-80 group animate-on-scroll animation-delay-300">
                    <img
                        src="https://images.unsplash.com/photo-1544644181-1484b3fdfc32?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                        alt="Nias Island"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-secondary-dark to-transparent opacity-70"></div>
                    <div class="absolute bottom-0 left-0 p-4 sm:p-5 md:p-6">
                        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-white mb-1 sm:mb-2">Pulau Bangkaru</h3>
                        <p class="text-white/90 mb-2 sm:mb-3 md:mb-4 text-sm sm:text-base">Turtle sanctuary & pristine coral reefs</p>
                        <a
                            href="#"
                            class="inline-flex items-center text-white font-medium hover:text-primary transition-colors text-sm sm:text-base"
                        >
                            Explore Packages <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Destination 3 -->
                <div class="relative rounded-lg overflow-hidden h-64 sm:h-72 md:h-80 group animate-on-scroll animation-delay-600">
                    <img
                        src="https://images.unsplash.com/photo-1604999333679-b86d54738315?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                        alt="Lake Toba"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-secondary-dark to-transparent opacity-70"></div>
                    <div class="absolute bottom-0 left-0 p-4 sm:p-5 md:p-6">
                        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-white mb-1 sm:mb-2">Aceh Singkil</h3>
                        <p class="text-white/90 mb-2 sm:mb-3 md:mb-4 text-sm sm:text-base">Gateway to paradise & mangrove forests</p>
                        <a
                            href="#"
                            class="inline-flex items-center text-white font-medium hover:text-primary transition-colors text-sm sm:text-base"
                        >
                            Explore Packages <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================= -->
    <!-- IMPORTANT: DO NOT DELETE - TESTIMONIALS SECTION START -->
    <!-- This section contains customer testimonials and ratings -->
    <!-- ========================================= -->
    <section id="testimonials" class="py-12 sm:py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-10 md:mb-12">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-primary-dark mb-3 sm:mb-4 animate-on-scroll">
                    What Our Travelers Say
                </h2>
                <p class="text-lg sm:text-xl md:text-2xl text-secondary-dark max-w-3xl mx-auto animate-on-scroll animation-delay-300 leading-relaxed">
                    Real experiences from our satisfied customers
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white p-4 sm:p-5 md:p-6 rounded-lg shadow-md animate-on-scroll">
                    <div class="flex items-center mb-3 sm:mb-4">
                        <img
                            src="https://images.unsplash.com/photo-1494790108755-2616b612b786?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80"
                            alt="Sarah Johnson"
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover mr-3 sm:mr-4"
                        >
                        <div>
                            <h4 class="font-semibold text-primary-dark text-sm sm:text-base">Sarah Johnson</h4>
                            <p class="text-xs sm:text-sm text-secondary-dark">Adventure Traveler</p>
                        </div>
                    </div>
                    <!-- IMPORTANT: DO NOT DELETE - Testimonial Rating -->
                    <div class="flex mb-3 sm:mb-4">
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                    </div>
                    <!-- End Testimonial Rating -->
                    <p class="text-secondary-dark italic text-sm sm:text-base leading-relaxed mb-3 sm:mb-4">
                        "Pulau Banyak is truly a hidden paradise! The snorkeling at Tailana Island was phenomenal, and the turtle watching at Bangkaru was a once-in-a-lifetime experience. Pure magic!"
                    </p>
                    <p class="text-primary font-medium text-sm sm:text-base">PT Sumatra Tour Travel Explorer</p>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white p-4 sm:p-5 md:p-6 rounded-lg shadow-md animate-on-scroll animation-delay-300">
                    <div class="flex items-center mb-3 sm:mb-4">
                        <img
                            src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80"
                            alt="Mark Wilson"
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover mr-3 sm:mr-4"
                        >
                        <div>
                            <h4 class="font-semibold text-primary-dark text-sm sm:text-base">Mark Wilson</h4>
                            <p class="text-xs sm:text-sm text-secondary-dark">Surf Enthusiast</p>
                        </div>
                    </div>
                    <div class="flex mb-3 sm:mb-4">
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                    </div>
                    <p class="text-secondary-dark italic text-sm sm:text-base leading-relaxed mb-3 sm:mb-4">
                        "The island-hopping adventure from Aceh Singkil with PT Sumatra Tour Travel was beyond amazing! Each island had its own unique charm, and the marine life diversity was absolutely stunning. Highly recommended!"
                    </p>
                    <p class="text-primary font-medium text-sm sm:text-base">Aceh Singkil Island Hopping</p>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white p-4 sm:p-5 md:p-6 rounded-lg shadow-md animate-on-scroll animation-delay-600">
                    <div class="flex items-center mb-3 sm:mb-4">
                        <img
                            src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80"
                            alt="Aiko Tanaka"
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover mr-3 sm:mr-4"
                        >
                        <div>
                            <h4 class="font-semibold text-primary-dark text-sm sm:text-base">Aiko Tanaka</h4>
                            <p class="text-xs sm:text-sm text-secondary-dark">Cultural Explorer</p>
                        </div>
                    </div>
                    <!-- IMPORTANT: DO NOT DELETE - Testimonial Rating -->
                    <div class="flex mb-3 sm:mb-4">
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                    </div>
                    <!-- End Testimonial Rating -->
                    <p class="text-secondary-dark italic text-sm sm:text-base leading-relaxed mb-3 sm:mb-4">
                        "The combination of pristine nature and local Acehnese culture in Singkil region was incredible. The mangrove tours and traditional fishing village visits gave us authentic insights into local life."
                    </p>
                    <p class="text-primary font-medium text-sm sm:text-base">Singkil Cultural & Nature Tour</p>
                </div>
            </div>
        </div>
    </section>
    <!-- ========================================= -->
    <!-- IMPORTANT: DO NOT DELETE - TESTIMONIALS SECTION END -->
    <!-- ========================================= -->
@endsection
