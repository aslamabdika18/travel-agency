@extends('layouts.app')

@section('title', 'Terms and Conditions - Aceh Tour Adventure')

@section('content')
<div class="bg-gray-100 py-12 md:py-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-10 md:mb-14">
            <h1 class="text-3xl md:text-4xl font-bold text-primary-dark mb-4">Terms and Conditions</h1>
            <p class="text-secondary text-lg max-w-3xl mx-auto">
                Please read these terms and conditions carefully before using our services.
            </p>
            <div class="mt-6 text-sm text-secondary">
                <p>Last Updated: January 1, 2023</p>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6 md:p-8">
                <!-- Table of Contents -->
                <div class="mb-8 p-4 bg-neutral-light rounded-lg">
                    <h2 class="text-xl font-bold text-secondary-dark mb-4">Table of Contents</h2>
                    <ol class="list-decimal list-inside space-y-1 text-secondary">
                        <li><a href="#agreement" class="text-primary hover:underline">Agreement to Terms</a></li>
                        <li><a href="#services" class="text-primary hover:underline">Our Services</a></li>
                        <li><a href="#bookings" class="text-primary hover:underline">Bookings and Payments</a></li>
                        <li><a href="#cancellations" class="text-primary hover:underline">Cancellations and Refunds</a></li>
                        <li><a href="#conduct" class="text-primary hover:underline">User Conduct</a></li>
                        <li><a href="#liability" class="text-primary hover:underline">Limitation of Liability</a></li>
                        <li><a href="#privacy" class="text-primary hover:underline">Privacy Policy</a></li>
                        <li><a href="#changes" class="text-primary hover:underline">Changes to Terms</a></li>
                        <li><a href="#contact" class="text-primary hover:underline">Contact Us</a></li>
                    </ol>
                </div>

                <!-- Agreement to Terms -->
                <section id="agreement" class="mb-10">
                    <h2 class="text-2xl font-bold text-secondary-dark mb-4">1. Agreement to Terms</h2>
                    <div class="prose prose-lg text-secondary">
                        <p>
                            By accessing or using the Aceh Tour Adventure website, mobile application, or any of our services, you agree to be bound by these Terms and Conditions. If you disagree with any part of the terms, you may not access our services.
                        </p>
                        <p>
                            These Terms constitute a legally binding agreement between you (the "User") and Aceh Tour Adventure ("we," "us," or "our") regarding your use of our travel services, website, and mobile applications (collectively, the "Services").
                        </p>
                    </div>
                </section>

                <!-- Our Services -->
                <section id="services" class="mb-10">
                    <h2 class="text-2xl font-bold text-secondary-dark mb-4">2. Our Services</h2>
                    <div class="prose prose-lg text-secondary">
                        <p>
                            Aceh Tour Adventure provides travel planning, booking, and management services for destinations primarily in Aceh, Indonesia. Our services include but are not limited to:
                        </p>
                        <ul class="list-disc pl-6 space-y-2">
                            <li>Curated travel packages and tours</li>
                            <li>Accommodation bookings</li>
                            <li>Transportation arrangements</li>
                            <li>Activity and excursion bookings</li>
                            <li>Custom travel itinerary planning</li>
                            <li>Travel guide services</li>
                        </ul>
                        <p>
                            We reserve the right to modify, suspend, or discontinue any aspect of our Services at any time without prior notice.
                        </p>
                    </div>
                </section>

                <!-- Bookings and Payments -->
                <section id="bookings" class="mb-10">
                    <h2 class="text-2xl font-bold text-secondary-dark mb-4">3. Bookings and Payments</h2>
                    <div class="prose prose-lg text-secondary">
                        <p>
                            When making a booking through our Services, you agree to provide accurate and complete information. You are responsible for ensuring that all travel documentation, including passports, visas, and health requirements, are valid and up-to-date.
                        </p>
                        <p>
                            Payment terms:
                        </p>
                        <ul class="list-disc pl-6 space-y-2">
                            <li>A deposit of 30% is required to confirm your booking</li>
                            <li>Full payment is due 30 days before the start date of your tour</li>
                            <li>For bookings made less than 30 days before the tour start date, full payment is required at the time of booking</li>
                            <li>All payments are processed securely through our payment partners</li>
                            <li>Prices are subject to change until your booking is confirmed</li>
                        </ul>
                        <p>
                            By making a payment, you authorize us to charge the applicable fees to your chosen payment method.
                        </p>
                    </div>
                </section>

                <!-- Cancellations and Refunds -->
                <section id="cancellations" class="mb-10">
                    <h2 class="text-2xl font-bold text-secondary-dark mb-4">4. Cancellations and Refunds</h2>
                    <div class="prose prose-lg text-secondary">
                        <p>
                            Our cancellation policy is as follows:
                        </p>
                        <ul class="list-disc pl-6 space-y-2">
                            <li>Cancellations made 60 days or more before the tour start date: Full refund minus a 10% administrative fee</li>
                            <li>Cancellations made 30-59 days before the tour start date: 50% refund</li>
                            <li>Cancellations made 15-29 days before the tour start date: 25% refund</li>
                            <li>Cancellations made less than 15 days before the tour start date: No refund</li>
                        </ul>
                        <p>
                            Special circumstances:
                        </p>
                        <ul class="list-disc pl-6 space-y-2">
                            <li>In case of natural disasters, political unrest, or other force majeure events, we may offer rescheduling options or credits for future travel</li>
                            <li>If we cancel a tour, you will receive a full refund or the option to reschedule</li>
                            <li>Travel insurance is strongly recommended to cover unexpected cancellations</li>
                        </ul>
                        <p>
                            All refund requests must be submitted in writing to our customer service team.
                        </p>
                    </div>
                </section>

                <!-- User Conduct -->
                <section id="conduct" class="mb-10">
                    <h2 class="text-2xl font-bold text-secondary-dark mb-4">5. User Conduct</h2>
                    <div class="prose prose-lg text-secondary">
                        <p>
                            When using our Services, you agree to:
                        </p>
                        <ul class="list-disc pl-6 space-y-2">
                            <li>Comply with all applicable laws and regulations</li>
                            <li>Respect local customs, traditions, and environments</li>
                            <li>Treat our staff, guides, and other travelers with courtesy and respect</li>
                            <li>Follow safety instructions and guidelines provided by our guides and staff</li>
                            <li>Not engage in any illegal, harmful, or disruptive behavior</li>
                        </ul>
                        <p>
                            We reserve the right to refuse service, terminate accounts, or cancel bookings if you engage in conduct that we determine, in our sole discretion, to be harmful, offensive, or in violation of these Terms.
                        </p>
                    </div>
                </section>

                <!-- Limitation of Liability -->
                <section id="liability" class="mb-10">
                    <h2 class="text-2xl font-bold text-secondary-dark mb-4">6. Limitation of Liability</h2>
                    <div class="prose prose-lg text-secondary">
                        <p>
                            To the maximum extent permitted by law, Aceh Tour Adventure and its affiliates, officers, employees, agents, partners, and licensors shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from:
                        </p>
                        <ul class="list-disc pl-6 space-y-2">
                            <li>Your access to or use of or inability to access or use the Services</li>
                            <li>Any conduct or content of any third party on the Services</li>
                            <li>Any content obtained from the Services</li>
                            <li>Unauthorized access, use, or alteration of your transmissions or content</li>
                        </ul>
                        <p>
                            Travel involves inherent risks, and while we take all reasonable precautions to ensure your safety, we cannot guarantee your safety during activities or tours. By booking with us, you acknowledge and accept these risks.
                        </p>
                    </div>
                </section>

                <!-- Privacy Policy -->
                <section id="privacy" class="mb-10">
                    <h2 class="text-2xl font-bold text-secondary-dark mb-4">7. Privacy Policy</h2>
                    <div class="prose prose-lg text-secondary">
                        <p>
                            Your privacy is important to us. Our <a href="{{ route('privacy') }}" class="text-primary hover:underline">Privacy Policy</a> explains how we collect, use, and protect your personal information. By using our Services, you consent to the data practices described in our Privacy Policy.
                        </p>
                    </div>
                </section>

                <!-- Changes to Terms -->
                <section id="changes" class="mb-10">
                    <h2 class="text-2xl font-bold text-secondary-dark mb-4">8. Changes to Terms</h2>
                    <div class="prose prose-lg text-secondary">
                        <p>
                            We reserve the right to modify these Terms at any time. We will provide notice of significant changes by updating the "Last Updated" date at the top of these Terms and/or by other means as determined by us.
                        </p>
                        <p>
                            Your continued use of our Services after any changes to these Terms constitutes your acceptance of the new Terms. If you do not agree to the modified terms, you should discontinue your use of our Services.
                        </p>
                    </div>
                </section>

                <!-- Contact Us -->
                <section id="contact" class="mb-6">
                    <h2 class="text-2xl font-bold text-secondary-dark mb-4">9. Contact Us</h2>
                    <div class="prose prose-lg text-secondary">
                        <p>
                            If you have any questions about these Terms, please contact us at:
                        </p>
                        <div class="mt-4 bg-neutral-light p-4 rounded-lg">
                            <p class="font-medium">Aceh Tour Adventure</p>
                            <p>Jl. Teuku Umar No. 123, Banda Aceh, Indonesia</p>
                            <p>Email: info@acehtouradventure.com</p>
                            <p>Phone: +62 651 123456</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <!-- Back to Top Button -->
        <div class="text-center mt-10">
            <a href="#" class="inline-flex items-center text-primary hover:text-primary-dark font-medium">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                </svg>
                Back to Top
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Terms Page JavaScript -->
@vite('resources/js/terms.js')
@endsection