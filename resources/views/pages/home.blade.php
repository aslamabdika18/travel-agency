@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <!-- Hero Section -->
    <section id="home" class="hero-section relative h-screen flex items-center justify-center text-center text-white" style="background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-position: center; background-repeat: no-repeat;" loading="lazy">
        <!-- Overlay gradient -->
        <div class="absolute inset-0 bg-black opacity-40"></div>
        <!-- Main content -->
        <div class="relative z-10 px-3 sm:px-6 lg:px-8 max-w-4xl mx-auto">
            <!-- Main heading -->
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 animate-fade-in">Discover the Untouched Beauty with PT Sumatra Tour Travel</h1>
            <!-- Description -->
            <p class="text-white text-sm sm:text-base md:text-lg lg:text-xl mb-6 sm:mb-8">Your journey to paradise starts here with customized tours to Indonesia's hidden gems.</p>
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                <a href="{{ route('travel-packages') }}" class="bg-primary hover:bg-primary-dark text-white font-bold py-1.5 sm:py-2 md:py-2.5 px-3 sm:px-4 md:px-5 rounded-full transition duration-300 transform hover:scale-105 text-xs sm:text-sm md:text-base">
                    <span>Explore Tour Packages</span>
                </a>
            </div>
        </div>
        <!-- Scroll indicator -->
        <div class="absolute bottom-6 sm:bottom-8 md:bottom-10 lg:bottom-12 left-0 right-0 flex justify-center">
            <a href="#about" class="text-white animate-bounce">
                <i class="fas fa-chevron-down text-lg sm:text-xl md:text-2xl lg:text-3xl"></i>
            </a>
        </div>
    </section>
    <!-- About Section -->
    <section id="about" class="py-6 sm:py-10 md:py-14 lg:py-16 bg-neutral">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-10 md:gap-12 lg:gap-16 items-center">
                <!-- Text Content -->
                <div class="order-2 lg:order-1 text-center lg:text-left space-y-6">
                    <h2 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-primary-dark leading-tight">Why Choose PT Sumatra Tour Travel?</h2>
                    <p class="text-sm sm:text-base md:text-lg text-secondary-dark leading-relaxed max-w-none lg:max-w-lg xl:max-w-xl">With over 15 years of experience in crafting unforgettable journeys, we specialize in showcasing the untouched beauty of Indonesia's hidden paradises. Our local expertise ensures authentic experiences that go beyond typical tourist routes.</p>

                    <!-- Features List -->
                    <div class="space-y-4 sm:space-y-5">
                        <div class="flex items-start justify-center lg:justify-start">
                            <div class="flex-shrink-0 rounded-full p-2">
                                <i class="fas fa-check-circle text-primary text-base sm:text-lg md:text-xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-4 text-left">
                                <p class="text-secondary-dark font-medium text-sm sm:text-base md:text-lg leading-relaxed">Local guides with intimate knowledge of the islands</p>
                            </div>
                        </div>
                        <div class="flex items-start justify-center lg:justify-start">
                            <div class="flex-shrink-0 rounded-full p-2">
                                <i class="fas fa-check-circle text-primary text-base sm:text-lg md:text-xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-4 text-left">
                                <p class="text-secondary-dark font-medium text-sm sm:text-base md:text-lg leading-relaxed">Sustainable tourism practices that support local communities</p>
                            </div>
                        </div>
                        <div class="flex items-start justify-center lg:justify-start">
                            <div class="flex-shrink-0 rounded-full p-2">
                                <i class="fas fa-check-circle text-primary text-base sm:text-lg md:text-xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-4 text-left">
                                <p class="text-secondary-dark font-medium text-sm sm:text-base md:text-lg leading-relaxed">Fully customizable itineraries tailored to your preferences</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Content -->
                <div class="order-1 lg:order-2 relative mx-auto lg:mx-0 w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-none">
                    <div class="relative overflow-hidden rounded-xl sm:rounded-2xl shadow-xl">
                        <img src="https://images.unsplash.com/photo-1559128010-7c1ad6e1b6a5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2073&q=80"
                        alt="PT Sumatra Tour Travel"
                        class="w-full h-56 sm:h-64 md:h-72 lg:h-80 xl:h-96 object-cover transition-transform duration-300 hover:scale-105"
                        loading="lazy">
                    </div>

                    <!-- Experience Badge - Moved outside image container -->
                    <div class="absolute -bottom-4 -right-4 sm:-bottom-5 sm:-right-5 bg-primary text-white p-3 sm:p-4 md:p-5 rounded-xl shadow-lg transform transition-all duration-300 hover:scale-105 z-10">
                        <p class="font-bold text-sm sm:text-base md:text-lg lg:text-xl">15+ Years</p>
                        <p class="text-xs sm:text-sm md:text-base opacity-90">of experience</p>
                    </div>

                    <!-- Decorative Elements -->
                    <div class="absolute -top-3 -left-3 sm:-top-4 sm:-left-4 w-10 h-10 sm:w-12 sm:h-12 bg-primary/20 rounded-full hidden sm:block"></div>
                    <div class="absolute -bottom-3 -left-3 sm:-bottom-4 sm:-left-4 w-8 h-8 sm:w-10 sm:h-10 bg-secondary/20 rounded-full hidden sm:block"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- Tour Packages Section -->
    <section id="packages" class="py-6 sm:py-10 md:py-14 lg:py-16 bg-neutral-light">
        <div class="max-w-7xl mx-auto px-3 xs:px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-4 xs:mb-6 sm:mb-8 md:mb-12 animate-on-scroll">
                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-primary-dark mb-2 sm:mb-3 md:mb-4">Our Bestselling Travel Packages</h2>
                <p class="text-sm sm:text-base md:text-lg text-secondary-dark max-w-xs sm:max-w-lg md:max-w-2xl lg:max-w-3xl mx-auto">Carefully curated experiences that combine adventure, relaxation, and cultural immersion.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                @forelse($travelPackages as $index => $package)
                <!-- Package {{ $index + 1 }} -->
                <div class="animate-on-scroll {{ $index > 0 ? 'animation-delay-'.(300 * $index) : '' }}">
                    <x-travel-package-card
                        :package="$package"
                        :isBestseller="$index === 0"
                        :isEcoTour="$index === 1"
                    />
                </div>
                @empty
                <!-- Fallback if no travel packages available -->
                <div class="col-span-1 sm:col-span-2 lg:col-span-3 text-center py-6 sm:py-8">
                    <p class="text-base sm:text-lg text-secondary">No travel packages available at the moment. Please check back later.</p>
                </div>
                @endforelse
            </div>
            <div class="text-center mt-6 sm:mt-8 md:mt-10 lg:mt-12 animate-on-scroll animation-delay-900">
                <a href="{{ route('travel-packages') }}" class="inline-flex items-center justify-center px-3 xs:px-4 sm:px-5 md:px-6 py-1.5 xs:py-2 sm:py-2.5 md:py-3 border border-transparent text-xs xs:text-sm sm:text-base font-medium rounded-full text-primary-dark bg-neutral hover:bg-neutral-light border-primary-dark transition duration-300 transform hover:scale-105">
                    View All Packages
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-6 sm:py-10 md:py-14 lg:py-16 bg-neutral">
        <div class="w-full max-w-7xl mx-auto px-3 xs:px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-4 xs:mb-6 sm:mb-8 md:mb-12 animate-on-scroll">
                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-primary-dark mb-2 xs:mb-3 sm:mb-4">Why Travel With Us</h2>
                <p class="text-sm sm:text-base md:text-lg text-secondary-dark max-w-xs xs:max-w-sm sm:max-w-2xl md:max-w-3xl mx-auto leading-relaxed">We're committed to making your journey safe, enjoyable, and unforgettable.</p>
            </div>

            <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-4 xs:gap-5 sm:gap-6 md:gap-8">
                <!-- Feature 1: Safety First -->
                <div class="bg-white p-4 xs:p-5 sm:p-6 md:p-8 rounded-xl shadow-md text-center animate-on-scroll hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 group">
                    <div class="inline-flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 lg:w-24 lg:h-24 bg-primary/10 rounded-full mb-3 sm:mb-4 md:mb-5 group-hover:bg-primary/20 transition-colors duration-300">
                        <i class="fas fa-shield-alt text-lg sm:text-xl md:text-2xl lg:text-3xl text-primary group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                    <h3 class="text-sm sm:text-base md:text-lg font-bold text-secondary-dark mb-2 sm:mb-3">Safety First</h3>
                    <p class="text-xs sm:text-sm md:text-base text-secondary leading-relaxed">
                        Your safety is our top priority with trained guides and quality equipment.
                    </p>
                </div>

                <!-- Feature 2: Fast Booking -->
                <div class="bg-white p-4 xs:p-5 sm:p-6 md:p-8 rounded-xl shadow-md text-center animate-on-scroll animation-delay-300 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 group">
                    <div class="inline-flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 lg:w-24 lg:h-24 bg-primary/10 rounded-full mb-3 sm:mb-4 md:mb-5 group-hover:bg-primary/20 transition-colors duration-300">
                        <i class="fas fa-bolt text-lg sm:text-xl md:text-2xl lg:text-3xl text-primary group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                    <h3 class="text-sm sm:text-base md:text-lg font-bold text-secondary-dark mb-2 sm:mb-3">Fast Booking</h3>
                    <p class="text-xs sm:text-sm md:text-base text-secondary leading-relaxed">
                        Simple and secure booking process with instant confirmation.
                    </p>
                </div>

                <!-- Feature 3: Best Price -->
                <div class="bg-white p-4 xs:p-5 sm:p-6 md:p-8 rounded-xl shadow-md text-center animate-on-scroll animation-delay-600 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 group">
                    <div class="inline-flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 lg:w-24 lg:h-24 bg-primary/10 rounded-full mb-3 sm:mb-4 md:mb-5 group-hover:bg-primary/20 transition-colors duration-300">
                        <i class="fas fa-money-bill-wave text-lg sm:text-xl md:text-2xl lg:text-3xl text-primary group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                    <h3 class="text-sm sm:text-base md:text-lg font-bold text-secondary-dark mb-2 sm:mb-3">Best Price</h3>
                    <p class="text-xs sm:text-sm md:text-base text-secondary leading-relaxed">
                        Competitive pricing with no hidden fees and best value guarantees.
                    </p>
                </div>

                <!-- Feature 4: Private Transport -->
                <div class="bg-white p-4 xs:p-5 sm:p-6 md:p-8 rounded-xl shadow-md text-center animate-on-scroll animation-delay-900 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 group">
                    <div class="inline-flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 lg:w-24 lg:h-24 bg-primary/10 rounded-full mb-3 sm:mb-4 md:mb-5 group-hover:bg-primary/20 transition-colors duration-300">
                        <i class="fas fa-car text-lg sm:text-xl md:text-2xl lg:text-3xl text-primary group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                    <h3 class="text-sm sm:text-base md:text-lg font-bold text-secondary-dark mb-2 sm:mb-3">Private Transport</h3>
                    <p class="text-xs sm:text-sm md:text-base text-secondary leading-relaxed">
                        Comfortable and reliable transportation throughout your journey.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================= -->
    <!-- IMPORTANT: DO NOT DELETE - TESTIMONIALS SECTION START -->
    <!-- This section contains customer testimonials and ratings -->
    <!-- ========================================= -->
    {{-- <section class="py-6 sm:py-10 md:py-14 lg:py-16 bg-neutral-light">
        <div class="w-full max-w-7xl mx-auto px-3 xs:px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-4 xs:mb-6 sm:mb-8 md:mb-12 animate-on-scroll">
                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-primary-dark mb-2 sm:mb-3 md:mb-4">
                    What Our Travelers Say
                </h2>
                <p class="text-sm sm:text-base md:text-lg text-secondary-dark max-w-xs sm:max-w-lg md:max-w-2xl lg:max-w-3xl mx-auto">
                    Don't just take our word for it - hear from our satisfied travelers
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 xs:gap-6 sm:gap-8 max-w-7xl mx-auto">
                @for ($i = 0; $i < 3; $i++)
                    <div class="bg-neutral p-4 xs:p-5 sm:p-6 rounded-lg shadow-md animate-on-scroll hover:shadow-lg transition duration-300">
                        <!-- IMPORTANT: DO NOT DELETE - Testimonial Rating -->
                        <div class="flex text-yellow-400 mb-2 xs:mb-3 sm:mb-4 text-xs xs:text-sm sm:text-base">
                            @for ($j = 0; $j < 5; $j++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <!-- End Testimonial Rating -->
                        <p class="text-secondary mb-4 xs:mb-5 sm:mb-6 text-xs xs:text-sm sm:text-base">
                            "Our trip with PT Sumatra Tour Travel was absolutely incredible! The guides were knowledgeable and friendly, and the itinerary was perfectly balanced. We'll definitely be booking with Sumatra Tour Travel again!"
                        </p>
                        <div class="flex items-center">
                            <div class="w-8 h-8 xs:w-10 xs:h-10 sm:w-12 sm:h-12 bg-gray-300 rounded-full mr-2 xs:mr-3 sm:mr-4"></div>
                            <div>
                                <h4 class="font-bold text-secondary-dark text-xs xs:text-sm sm:text-base">Sarah Johnson</h4>
                                <p class="text-secondary text-2xs xs:text-xs sm:text-sm">Traveled April 2023</p>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </section> --}}
    <!-- ========================================= -->
    <!-- IMPORTANT: DO NOT DELETE - TESTIMONIALS SECTION END -->
    <!-- ========================================= -->

    <!-- FAQ Section -->
    <section class="py-6 sm:py-10 md:py-14 lg:py-16 bg-neutral-light">
        <div class="w-full max-w-7xl mx-auto px-3 xs:px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-6 xs:mb-8 sm:mb-12 md:mb-16 animate-on-scroll">
                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-primary-dark mb-2 sm:mb-3 md:mb-4">
                    Frequently Asked Questions
                </h2>
                <p class="text-sm sm:text-base md:text-lg text-secondary-dark max-w-xs sm:max-w-lg md:max-w-2xl lg:max-w-3xl mx-auto">
                    Find answers to common questions about our tours
                </p>
            </div>

            <div class="space-y-2 xs:space-y-3 sm:space-y-4 max-w-4xl mx-auto">
                @php
                    $faqs = [
                        [
                            'question' => 'What is the best time to visit with PT Sumatra Tour Travel?',
                            'answer' => 'The best time to visit is during the dry season from April to October when the weather is sunny and the seas are calm, perfect for island hopping and water activities.'
                        ],
                        [
                            'question' => 'Are your tours suitable for families with children?',
                            'answer' => 'Yes, many of our tours are family-friendly. We can customize itineraries to accommodate families with children of different ages, ensuring activities suitable for everyone.'
                        ],
                        [
                            'question' => 'What should I pack for the trip?',
                            'answer' => 'We recommend packing light, breathable clothing, swimwear, sun protection (hat, sunglasses, sunscreen), insect repellent, comfortable walking shoes, and a small backpack for day trips.'
                        ],
                        [
                            'question' => 'Do I need a visa to visit Indonesia?',
                            'answer' => 'Many countries are eligible for visa-free entry or visa on arrival for tourism purposes. We recommend checking the latest visa requirements based on your nationality before traveling.'
                        ],
                        [
                            'question' => 'How do I book a tour with you?',
                            'answer' => 'You can book directly through our website by selecting your preferred package and following the booking process. Alternatively, you can contact us via email or phone for personalized assistance.'
                        ]
                    ];
                @endphp

                @foreach ($faqs as $index => $faq)
                    <div class="bg-neutral rounded-lg shadow-md overflow-hidden animate-on-scroll hover:shadow-lg transition duration-300">
                        <button
                            class="w-full flex justify-between items-center p-2.5 xs:p-3.5 sm:p-5 text-left focus:outline-none"
                            onclick="toggleFaq({{ $index }})"
                        >
                            <span class="font-bold text-secondary-dark text-sm sm:text-base pr-2 xs:pr-3 sm:pr-4">{{ $faq['question'] }}</span>
                            <i class="fas fa-plus text-primary transition-transform duration-300 text-xs sm:text-sm" id="faq-icon-{{ $index }}"></i>
                        </button>
                        <div class="px-2.5 xs:px-3.5 sm:px-5 pb-2.5 xs:pb-3.5 sm:pb-5 hidden" id="faq-answer-{{ $index }}">
                            <p class="text-secondary text-xs sm:text-sm">{{ $faq['answer'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<!-- Home Page JavaScript -->
@vite('resources/js/home.js')
@endpush
