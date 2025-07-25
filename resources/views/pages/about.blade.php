@extends('layouts.app')

@section('title', 'About Us - Aceh Tour Adventure')

@section('content')
    <!-- Hero Section -->
    <section class="relative h-72 sm:h-80 md:h-96 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1596097155664-4f5c49ba1b69?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80')">
        <div class="absolute inset-0 bg-secondary-dark opacity-50"></div>
        <div class="relative h-full flex items-center justify-center text-center px-4">
            <div class="animate-on-scroll">
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2 sm:mb-4">About Sumatra Tour Travel</h1>
                <p class="text-lg sm:text-xl text-white max-w-3xl">Discover the story behind Aceh's premier sustainable tourism company</p>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="py-12 sm:py-16 md:py-20 bg-neutral">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-12 items-center">
                <div class="mb-12 lg:mb-0 animate-on-scroll">
                    <h2 class="text-2xl sm:text-3xl font-bold text-primary mb-4 sm:mb-6">
                        Our Story
                    </h2>
                    <p class="text-secondary mb-4 sm:mb-6 text-base sm:text-lg">
                        Sumatra Tour Travel was founded in 2005 by a group of passionate local guides who wanted to share the beauty of Indonesia's hidden gems with the world. What started as a small operation has grown into a trusted name in sustainable tourism across the archipelago.
                    </p>
                    <p class="text-secondary mb-4 sm:mb-6 text-base sm:text-lg">
                        Our journey began with a simple mission: to create authentic travel experiences that benefit both visitors and local communities. Over the years, we've remained true to this vision while expanding our offerings to showcase more of Indonesia's incredible diversity.
                    </p>
                    <p class="text-secondary text-base sm:text-lg">
                        Today, we're proud to be the leading tour operator PT Sumatra Tour Travel for the surrounding regions, with a team of experienced guides who are passionate about conservation, cultural preservation, and creating unforgettable experiences for our guests.
                    </p>
                </div>
                <div class="relative animate-on-scroll animation-delay-300">
                    <img
                        src="https://images.unsplash.com/photo-1540541338287-41700207dee6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                        alt="Our Story"
                        class="rounded-lg shadow-xl w-full h-auto"
                    />
                    <div class="absolute -bottom-6 -left-6 bg-primary text-neutral p-4 rounded-lg shadow-lg hidden md:block animate-on-scroll animation-delay-600">
                        <p class="font-bold text-lg">Since 2005</p>
                        <p class="text-sm">Creating memories</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Mission Section -->
    <section class="py-12 sm:py-16 md:py-20 bg-neutral-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10 sm:mb-16 animate-on-scroll">
                <h2 class="text-2xl sm:text-3xl font-bold text-primary-dark mb-2 sm:mb-4">
                    Our Mission & Values
                </h2>
                <p class="text-lg sm:text-xl text-secondary-dark max-w-3xl mx-auto">
                    Guided by principles that prioritize people, planet, and authentic experiences
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md animate-on-scroll">
                    <div class="inline-flex items-center justify-center w-14 sm:w-16 h-14 sm:h-16 bg-primary-light rounded-full mb-3 sm:mb-4">
                        <i class="fas fa-globe-asia text-xl sm:text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-2">Sustainable Tourism</h3>
                    <p class="text-gray-600 text-sm sm:text-base">
                        We're committed to minimizing our environmental footprint and supporting conservation efforts across the islands we visit. Our tours are designed with sustainability at their core.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md animate-on-scroll animation-delay-300">
                    <div class="inline-flex items-center justify-center w-14 sm:w-16 h-14 sm:h-16 bg-primary-light rounded-full mb-3 sm:mb-4">
                        <i class="fas fa-hands-helping text-xl sm:text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-2">Community Support</h3>
                    <p class="text-gray-600 text-sm sm:text-base">
                        We believe tourism should benefit local communities. We employ local guides, support small businesses, and contribute to community development projects.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md animate-on-scroll animation-delay-600">
                    <div class="inline-flex items-center justify-center w-14 sm:w-16 h-14 sm:h-16 bg-primary-light rounded-full mb-3 sm:mb-4">
                        <i class="fas fa-compass text-xl sm:text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-2">Authentic Experiences</h3>
                    <p class="text-gray-600 text-sm sm:text-base">
                        We go beyond typical tourist routes to offer genuine cultural exchanges and immersive adventures that connect you with the real Sumatra.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Team Section -->
    {{-- <section class="py-12 sm:py-16 md:py-20 bg-neutral">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10 sm:mb-16 animate-on-scroll">
                <h2 class="text-2xl sm:text-3xl font-bold text-primary-dark mb-2 sm:mb-4">
                    Meet Our Team
                </h2>
                <p class="text-lg sm:text-xl text-secondary-dark max-w-3xl mx-auto">
                    The passionate individuals behind your unforgettable experiences
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                <!-- Team Member 1 -->
                <div class="bg-neutral-light rounded-lg overflow-hidden shadow-md animate-on-scroll">
                    <div class="h-48 sm:h-56 md:h-64 bg-gray-300"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-1">Ahmad Rizki</h3>
                        <p class="text-primary font-medium mb-2 sm:mb-3 text-sm sm:text-base">Founder & CEO</p>
                        <p class="text-secondary mb-3 sm:mb-4 text-sm sm:text-base">
                            A native of Aceh with over 20 years of experience in tourism and a passion for marine conservation.
                        </p>
                        <div class="flex space-x-3">
                            <a href="#" class="text-secondary hover:text-primary transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="text-secondary hover:text-primary transition-colors">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="text-secondary hover:text-primary transition-colors">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Team Member 2 -->
                <div class="bg-neutral-light rounded-lg overflow-hidden shadow-md animate-on-scroll animation-delay-300">
                    <div class="h-48 sm:h-56 md:h-64 bg-gray-300"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-1">Siti Nuraini</h3>
                        <p class="text-primary font-medium mb-2 sm:mb-3 text-sm sm:text-base">Operations Manager</p>
                        <p class="text-secondary mb-3 sm:mb-4 text-sm sm:text-base">
                            With a background in hospitality management, Siti ensures every tour runs smoothly from start to finish.
                        </p>
                        <div class="flex space-x-3">
                            <a href="#" class="text-secondary hover:text-primary transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="text-secondary hover:text-primary transition-colors">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="text-secondary hover:text-primary transition-colors">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Team Member 3 -->
                <div class="bg-white rounded-lg overflow-hidden shadow-md animate-on-scroll animation-delay-600">
                    <div class="h-48 sm:h-56 md:h-64 bg-gray-300"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-1">Budi Santoso</h3>
                        <p class="text-primary font-medium mb-2 sm:mb-3 text-sm sm:text-base">Lead Guide</p>
                        <p class="text-gray-600 mb-3 sm:mb-4 text-sm sm:text-base">
                            A certified diving instructor and wildlife expert with intimate knowledge of the PT Sumatra Tour Travel ecosystem.
                        </p>
                        <div class="flex space-x-3">
                            <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Team Member 4 -->
                <div class="bg-white rounded-lg overflow-hidden shadow-md animate-on-scroll animation-delay-900">
                    <div class="h-48 sm:h-56 md:h-64 bg-gray-300"></div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg sm:text-xl font-bold text-secondary-dark mb-1">Maya Putri</h3>
                        <p class="text-primary font-medium mb-2 sm:mb-3 text-sm sm:text-base">Customer Relations</p>
                        <p class="text-gray-600 mb-3 sm:mb-4 text-sm sm:text-base">
                            Fluent in five languages, Maya ensures clear communication and exceptional service for all our guests.
                        </p>
                        <div class="flex space-x-3">
                            <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- Achievements Section -->
    {{-- <section class="py-12 sm:py-16 md:py-20 bg-neutral-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10 sm:mb-16 animate-on-scroll">
                <h2 class="text-2xl sm:text-3xl font-bold text-primary-dark mb-2 sm:mb-4">
                    Our Achievements
                </h2>
                <p class="text-lg sm:text-xl text-secondary-dark max-w-3xl mx-auto">
                    Recognition of our commitment to excellence and sustainability
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 md:gap-8">
                <div class="bg-neutral p-4 sm:p-6 rounded-lg shadow-md text-center animate-on-scroll">
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold text-primary mb-1 sm:mb-2">15+</div>
                    <p class="text-secondary-dark font-medium text-sm sm:text-base">Years of Experience</p>
                </div>

                <div class="bg-neutral p-4 sm:p-6 rounded-lg shadow-md text-center animate-on-scroll animation-delay-300">
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold text-primary mb-1 sm:mb-2">10k+</div>
                    <p class="text-secondary-dark font-medium text-sm sm:text-base">Happy Travelers</p>
                </div>

                <div class="bg-neutral p-4 sm:p-6 rounded-lg shadow-md text-center animate-on-scroll animation-delay-600">
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold text-primary mb-1 sm:mb-2">25+</div>
                    <p class="text-secondary-dark font-medium text-sm sm:text-base">Tour Destinations</p>
                </div>

                <div class="bg-neutral p-4 sm:p-6 rounded-lg shadow-md text-center animate-on-scroll animation-delay-900">
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold text-primary mb-1 sm:mb-2">12</div>
                    <p class="text-secondary-dark font-medium text-sm sm:text-base">Industry Awards</p>
                </div>
            </div>

            <div class="mt-10 sm:mt-16 grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8 items-center">
                <div class="grayscale hover:grayscale-0 transition-all duration-300 animate-on-scroll">
                    <img src="{{ asset('placeholder-travel.svg') }}" alt="Award 1" class="h-12 sm:h-16 mx-auto">
                </div>
                <div class="grayscale hover:grayscale-0 transition-all duration-300 animate-on-scroll animation-delay-300">
                    <img src="{{ asset('placeholder-travel.svg') }}" alt="Award 2" class="h-12 sm:h-16 mx-auto">
                </div>
                <div class="grayscale hover:grayscale-0 transition-all duration-300 animate-on-scroll animation-delay-600">
                    <img src="{{ asset('placeholder-travel.svg') }}" alt="Award 3" class="h-12 sm:h-16 mx-auto">
                </div>
                <div class="grayscale hover:grayscale-0 transition-all duration-300 animate-on-scroll animation-delay-900">
                    <img src="{{ asset('placeholder-travel.svg') }}" alt="Award 4" class="h-12 sm:h-16 mx-auto">
                </div>
            </div>
        </div>
    </section> --}}

    <!-- CTA Section -->
    <section
        class="py-12 sm:py-16 md:py-20 bg-cover bg-center relative"
        style="background-image: url('https://images.unsplash.com/photo-1540541338287-41700207dee6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80')"
    >
        <div class="absolute inset-0 bg-secondary-dark opacity-70"></div>
        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-4 sm:mb-6 animate-on-scroll">
                Join Us on an Unforgettable Journey
            </h2>
            <p class="text-base sm:text-lg md:text-xl text-white mb-6 sm:mb-8 animate-on-scroll animation-delay-300">
                Experience the beauty of Indonesia with guides who are passionate about sharing their homeland
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4 animate-on-scroll animation-delay-600">
                <a
                    href="{{ route('travel-packages') }}"
                    class="bg-primary hover:bg-primary-dark text-white font-bold py-2 sm:py-3 px-6 sm:px-8 rounded-full transition duration-300 transform hover:scale-105 shadow-md text-sm sm:text-base"
                >
                    Explore Packages
                </a>
                <a
                    href="{{ route('contact') }}"
                    class="bg-transparent hover:bg-white/10 text-white font-bold py-2 sm:py-3 px-6 sm:px-8 border-2 border-white rounded-full transition duration-300 transform hover:scale-105 shadow-md text-sm sm:text-base"
                >
                    Contact Us
                </a>
            </div>
        </div>
    </section>
@endsection
