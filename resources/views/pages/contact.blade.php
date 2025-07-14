@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
    <!-- Hero Section -->
    <section
        class="relative pt-24 sm:pt-28 md:pt-32 pb-16 sm:pb-18 md:pb-20 bg-cover bg-center h-72 sm:h-80 md:h-96"
        style="background-image: url('https://images.unsplash.com/photo-1596386461350-326ccb383e9f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');"
    >
        <div class="absolute inset-0 bg-secondary-dark opacity-70"></div>
        <div class="relative z-10 w-full mx-auto px-4 sm:px-6 md:px-0 text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-4 sm:mb-6 animate-fade-in">
                Contact Us
            </h1>
            <p class="text-lg sm:text-xl text-white max-w-3xl mx-auto animate-fade-in animation-delay-300">
                Have questions or ready to plan your adventure? We're here to help!
            </p>
        </div>
    </section>

    <!-- Contact Info Section -->
    <section class="py-12 sm:py-16 md:py-20 bg-neutral">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-center">
                <!-- Contact Information -->
                <div class="mb-8 lg:mb-0 animate-on-scroll max-w-2xl w-full bg-white rounded-lg shadow-lg p-6 sm:p-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-primary mb-4 sm:mb-6 text-center">
                        Get in Touch
                    </h2>
                    <p class="text-base sm:text-lg text-secondary mb-6 sm:mb-8 text-justify">
                        Whether you have questions about our tour packages, need help planning a custom itinerary, or want to provide feedback about your experience, our team is ready to assist you. Reach out through any of the channels below, and we'll get back to you as soon as possible.
                    </p>

                    <div class="space-y-4 sm:space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 inline-flex items-center justify-center h-10 sm:h-12 w-10 sm:w-12 rounded-full bg-primary-light text-primary">
                                <i class="fas fa-map-marker-alt text-lg sm:text-xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <h3 class="text-base sm:text-lg font-medium text-secondary-dark">Office Address</h3>
                                <p class="mt-1 text-sm sm:text-base text-secondary text-justify">
                                    Jalan Singkil Raya No. 45<br>
                                    Aceh Singkil, Aceh 24785<br>
                                    Indonesia
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 inline-flex items-center justify-center h-10 sm:h-12 w-10 sm:w-12 rounded-full bg-primary-light text-primary">
                                <i class="fas fa-phone-alt text-lg sm:text-xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <h3 class="text-base sm:text-lg font-medium text-secondary-dark">Phone</h3>
                                <p class="mt-1 text-sm sm:text-base text-secondary text-justify">
                                    +62 812 3456 7890 (Reservations)<br>
                                    +62 821 9876 5432 (Customer Support)
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 inline-flex items-center justify-center h-10 sm:h-12 w-10 sm:w-12 rounded-full bg-primary-light text-primary">
                                <i class="fas fa-envelope text-lg sm:text-xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <h3 class="text-base sm:text-lg font-medium text-secondary-dark">Email</h3>
                                <p class="mt-1 text-sm sm:text-base text-secondary text-justify">
                                    info@sumatratourtravel.com<br>
                                    booking@sumatratourtravel.com
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 inline-flex items-center justify-center h-10 sm:h-12 w-10 sm:w-12 rounded-full bg-primary-light text-primary">
                                <i class="fas fa-clock text-lg sm:text-xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <h3 class="text-base sm:text-lg font-medium text-secondary-dark">Business Hours</h3>
                                <p class="mt-1 text-sm sm:text-base text-secondary text-justify">
                                    Monday - Friday: 8:00 AM - 6:00 PM<br>
                                    Saturday: 9:00 AM - 3:00 PM<br>
                                    Sunday: Closed
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 sm:mt-8">
                        <h3 class="text-base sm:text-lg font-medium text-secondary-dark mb-3 sm:mb-4">Follow Us</h3>
                        <div class="flex space-x-3 sm:space-x-4">
                            <a href="#" class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-primary-light flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors duration-300">
                                <i class="fab fa-facebook-f text-sm sm:text-base"></i>
                            </a>
                            <a href="#" class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-primary-light flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors duration-300">
                                <i class="fab fa-instagram text-sm sm:text-base"></i>
                            </a>
                            <a href="#" class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-primary-light flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors duration-300">
                                <i class="fab fa-twitter text-sm sm:text-base"></i>
                            </a>
                            <a href="#" class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-primary-light flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors duration-300">
                                <i class="fab fa-youtube text-sm sm:text-base"></i>
                            </a>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-12 sm:py-16 md:py-20 bg-neutral-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-12 animate-on-scroll">
                <h2 class="text-2xl sm:text-3xl font-bold text-primary-dark mb-2 sm:mb-4">
                    Find Us
                </h2>
                <p class="text-lg sm:text-xl text-secondary-dark max-w-3xl mx-auto text-center">
                    Visit our main office in Aceh Singkil, the gateway to your Sumatran adventure
                </p>
            </div>

            <div class="rounded-lg overflow-hidden shadow-xl h-64 sm:h-80 md:h-96 animate-on-scroll">
                <!-- Google Maps embed for Aceh Singkil -->
                <iframe class="w-full h-full border-0" 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127504.42574672113!2d97.70976287072754!3d2.3602756574312285!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30250f5d6f5ef031%3A0x8aa25a60df3e6ab4!2sKab.%20Aceh%20Singkil%2C%20Aceh!5e0!3m2!1sid!2sid!4v1699234567890!5m2!1sid!2sid" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-12 sm:py-16 md:py-20 bg-neutral">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-12 animate-on-scroll">
                <h2 class="text-2xl sm:text-3xl font-bold text-primary-dark mb-2 sm:mb-4">
                    Frequently Asked Questions
                </h2>
                <p class="text-lg sm:text-xl text-secondary-dark max-w-3xl mx-auto text-center">
                    Quick answers to common questions
                </p>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="space-y-4 sm:space-y-6">
                    <!-- FAQ Item 1 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden animate-on-scroll">
                        <button class="faq-toggle w-full flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 text-left">
                            <span class="text-base sm:text-lg font-medium text-secondary-dark">How do I book a tour?</span>
                            <i class="fas fa-chevron-down text-primary text-sm sm:text-base transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content px-4 sm:px-6 pb-3 sm:pb-4 hidden">
                            <p class="text-sm sm:text-base text-secondary text-justify">
                                You can book a tour through our website by selecting your desired package and following the booking process, or you can contact our reservations team directly by phone or email. We recommend booking at least 2-3 months in advance for peak season travel.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden animate-on-scroll animation-delay-300">
                        <button class="faq-toggle w-full flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 text-left">
                            <span class="text-base sm:text-lg font-medium text-secondary-dark">What payment methods do you accept?</span>
                            <i class="fas fa-chevron-down text-primary text-sm sm:text-base transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content px-4 sm:px-6 pb-3 sm:pb-4 hidden">
                            <p class="text-sm sm:text-base text-secondary text-justify">
                                We process all payments securely through Midtrans payment gateway, which supports various payment methods including credit/debit cards, bank transfers, and e-wallets. All transactions are encrypted and secure.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden animate-on-scroll animation-delay-600">
                        <button class="faq-toggle w-full flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 text-left">
                            <span class="text-base sm:text-lg font-medium text-secondary-dark">What is your cancellation policy?</span>
                            <i class="fas fa-chevron-down text-primary text-sm sm:text-base transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content px-4 sm:px-6 pb-3 sm:pb-4 hidden">
                            <p class="text-sm sm:text-base text-secondary text-justify">
                                Our standard cancellation policy allows for a full refund if cancelled 30 days or more before the tour start date. Cancellations made 15-29 days before receive a 50% refund, and cancellations less than 15 days before the tour are non-refundable. We recommend purchasing travel insurance for unexpected circumstances.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden animate-on-scroll animation-delay-900">
                        <button class="faq-toggle w-full flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 text-left">
                            <span class="text-base sm:text-lg font-medium text-secondary-dark">Can you arrange custom tours?</span>
                            <i class="fas fa-chevron-down text-primary text-sm sm:text-base transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content px-4 sm:px-6 pb-3 sm:pb-4 hidden">
                            <p class="text-sm sm:text-base text-secondary text-justify">
                                Yes, we specialize in creating custom itineraries tailored to your interests, timeframe, and budget. Contact our team with your requirements, and we'll design a personalized experience for you or your group.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 sm:py-16 md:py-20 bg-primary">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-4 sm:mb-6 animate-on-scroll">
                Ready to Start Your Adventure?
            </h2>
            <p class="text-lg sm:text-xl text-white/90 max-w-3xl mx-auto mb-6 sm:mb-8 animate-on-scroll animation-delay-300 text-center">
                Browse our tour packages or contact us to create your custom journey
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4 animate-on-scroll animation-delay-600">
                <a
                    href="{{ route('travel-packages') }}"
                    class="bg-white hover:bg-neutral-light text-primary font-bold py-2 sm:py-3 px-6 sm:px-8 text-sm sm:text-base rounded-full transition duration-300 transform hover:scale-105 shadow-md"
                >
                    View Packages
                </a>
                <a
                    href="tel:+6281234567890"
                    class="bg-transparent hover:bg-white/10 text-white font-bold py-2 sm:py-3 px-6 sm:px-8 text-sm sm:text-base border-2 border-white rounded-full transition duration-300 transform hover:scale-105 shadow-md"
                >
                    Call Us Now
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Page JavaScript -->
    @vite('resources/js/contact.js')
@endsection
