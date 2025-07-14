<footer class="bg-secondary-dark text-neutral pt-10 sm:pt-16 pb-6 sm:pb-8">
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            <!-- Company Info -->
            <div>
                <div class="flex items-center mb-4">
                    <img class="h-8 sm:h-10 w-auto" src="{{ asset('images/logo.png') }}" alt="Logo">
                    <span class="ml-2 sm:ml-3 text-lg sm:text-xl font-bold text-primary">Sumatra Tour Travel</span>
                </div>
                <p class="text-neutral-light text-sm sm:text-base mb-4">
                    Discover the untouched beauty of Indonesia's hidden paradises with our expertly crafted tours.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-neutral-light hover:text-primary transition-colors duration-300 text-sm sm:text-base">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-neutral-light hover:text-primary transition-colors duration-300 text-sm sm:text-base">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-neutral-light hover:text-primary transition-colors duration-300 text-sm sm:text-base">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-neutral-light hover:text-primary transition-colors duration-300 text-sm sm:text-base">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-base sm:text-lg font-semibold text-primary mb-3 sm:mb-4">Quick Links</h3>
                <ul class="space-y-1 sm:space-y-2">
                    <li>
                        <a href="{{ route('home') }}" class="text-neutral-light hover:text-primary transition-colors duration-300 text-sm sm:text-base">
                            <i class="fas fa-chevron-right text-xs mr-2"></i> Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="text-neutral-light hover:text-primary transition-colors duration-300 text-sm sm:text-base">
                            <i class="fas fa-chevron-right text-xs mr-2"></i> About Us
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('travel-packages') }}" class="text-neutral-light hover:text-primary transition-colors duration-300 text-sm sm:text-base">
                            <i class="fas fa-chevron-right text-xs mr-2"></i> Tour Packages
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="text-neutral-light hover:text-primary transition-colors duration-300 text-sm sm:text-base">
                            <i class="fas fa-chevron-right text-xs mr-2"></i> Contact Us
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-base sm:text-lg font-semibold text-primary mb-3 sm:mb-4">Legal</h3>
                <ul class="space-y-1 sm:space-y-2">
                    <li>
                        <a href="{{ route('terms') }}" class="text-neutral-light hover:text-primary transition-colors duration-300 text-sm sm:text-base">
                            <i class="fas fa-chevron-right text-xs mr-2"></i> Terms & Conditions
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('privacy') }}" class="text-neutral-light hover:text-primary transition-colors duration-300 text-sm sm:text-base">
                            <i class="fas fa-chevron-right text-xs mr-2"></i> Privacy Policy
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-neutral-light hover:text-primary transition-colors duration-300 text-sm sm:text-base">
                            <i class="fas fa-chevron-right text-xs mr-2"></i> Refund Policy
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-neutral-light hover:text-primary transition-colors duration-300 text-sm sm:text-base">
                            <i class="fas fa-chevron-right text-xs mr-2"></i> Cookie Policy
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h3 class="text-base sm:text-lg font-semibold text-primary mb-3 sm:mb-4">Contact Us</h3>
                <ul class="space-y-2 sm:space-y-3">
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-3 text-primary"></i>
                        <span class="text-neutral-light text-sm sm:text-base">Pulau Banyak Street No. 123, Banda Aceh, Indonesia</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-phone-alt mt-1 mr-3 text-primary"></i>
                        <span class="text-neutral-light text-sm sm:text-base">+62 812 3456 7890</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-envelope mt-1 mr-3 text-primary"></i>
                        <span class="text-neutral-light text-sm sm:text-base">info@sumatratourtravel.com</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-clock mt-1 mr-3 text-primary"></i>
                        <span class="text-neutral-light text-sm sm:text-base">Mon-Sat: 9:00 AM - 6:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="border-neutral-dark my-6 sm:my-8">

        <div class="flex flex-col md:flex-row justify-between items-center">
            <p class="text-neutral-light text-xs sm:text-sm text-center md:text-left">
                &copy; {{ date('Y') }} Sumatra Tour Travel. All rights reserved.
            </p>
        </div>
    </div>
</footer>
