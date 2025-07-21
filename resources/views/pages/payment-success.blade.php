@extends('layouts.app')

@section('title', 'Payment Successful - Aceh Tour Adventure')

@section('content')
<div class="bg-gray-100 py-12 md:py-16 min-h-screen flex items-center">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Success Header -->
            <div class="bg-green-50 p-6 sm:p-8 text-center border-b border-green-100">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-green-800 mb-2">Payment Successful!</h1>
                <p class="text-green-700 text-lg">Your booking has been confirmed.</p>
            </div>

            <!-- Booking Details -->
            <div class="p-6 sm:p-8">
                <div class="mb-8 text-center">
                    <p class="text-secondary text-lg mb-2">Terima kasih telah memesan dengan Aceh Tour Adventure!</p>
                    @if($booking && $booking->user)
                        <p class="text-secondary">Email konfirmasi telah dikirim ke <span class="font-medium">{{ $booking->user->email }}</span></p>
                    @else
                        <p class="text-secondary">Email konfirmasi akan dikirim ke alamat email terdaftar Anda.</p>
                    @endif
                </div>

                <!-- Link ke Notifikasi -->
                @if(Auth::check())
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                </svg>
                                <div>
                                    <h4 class="text-blue-800 font-medium">Notifikasi Tersimpan</h4>
                                    <p class="text-blue-700 text-sm mt-1">Detail pembayaran telah disimpan di notifikasi Anda.</p>
                                </div>
                            </div>
                            <a href="{{ route('notifications') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                Lihat Notifikasi
                            </a>
                        </div>
                    </div>
                @endif

                @if($booking)
                    <!-- Booking Summary -->
                    <div class="border border-gray-200 rounded-lg p-6 mb-8">
                        <h2 class="text-xl font-bold text-secondary-dark mb-4">Booking Summary</h2>

                    @if($booking && $booking->travelPackage)
                        <div class="flex items-center mb-6">
                            @if($booking->travelPackage->media->isNotEmpty())
                                <img src="{{ $booking->travelPackage->media->first()->getUrl() }}" alt="{{ $booking->travelPackage->name }}" class="w-20 h-20 object-cover rounded-lg mr-4">
                            @else
                                <div class="w-20 h-20 bg-gray-200 rounded-lg mr-4 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-bold text-secondary-dark">{{ $booking->travelPackage->name }}</h3>
                                <p class="text-secondary text-sm">{{ $booking->travelPackage->duration }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center mb-6">
                            <div class="w-20 h-20 bg-gray-200 rounded-lg mr-4 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-secondary-dark">Travel Package</h3>
                                <p class="text-secondary text-sm">Package details not available</p>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-secondary">Booking Reference</p>
                            <p class="font-medium text-secondary-dark">{{ $booking->booking_reference ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary">Payment Date</p>
                            <p class="font-medium text-secondary-dark">
                                @if($payment && $payment->payment_date)
                                    {{ $payment->payment_date->format('F d, Y') }}
                                @else
                                    {{ now()->format('F d, Y') }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary">Travel Date</p>
                            <p class="font-medium text-secondary-dark">
            @if($booking && $booking->booking_date)
                                    {{ $booking->booking_date->format('F d, Y') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary">Travelers</p>
                            <p class="font-medium text-secondary-dark">
                                @if($booking && $booking->person_count)
                                    {{ $booking->person_count }} {{ $booking->person_count > 1 ? 'Persons' : 'Person' }}
                                @else
                                    N/A
                                @endif
                            </p>
        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        @if($booking)
                            <div class="flex justify-between mb-2">
                                <span class="text-secondary">Base Price ({{ $booking->person_count ?? 1 }} {{ ($booking->person_count ?? 1) > 1 ? 'persons' : 'person' }})</span>
                                <span class="text-secondary">{{ $booking->formatted_base_price }}</span>
                            </div>
                            @if($booking->additional_price > 0)
                                <div class="flex justify-between mb-2">
                                    <span class="text-secondary">Additional Price</span>
                                    <span class="text-secondary">{{ $booking->formatted_additional_price }}</span>
                                </div>
                            @endif
                            @if($booking->tax_amount > 0)
                                <div class="flex justify-between mb-2">
                                    <span class="text-secondary">Tax & Fees</span>
                                    <span class="text-secondary">{{ $booking->formatted_tax_amount }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-bold text-secondary-dark">
                                <span>Total Amount</span>
                                <span>{{ $booking->getFormattedTotalPrice() }}</span>
                            </div>
                        @else
                            <div class="flex justify-between font-bold text-secondary-dark">
                                <span>Total Amount</span>
                                <span>N/A</span>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- No Booking Data -->
                    <div class="border border-gray-200 rounded-lg p-6 mb-8 text-center">
                        <div class="text-gray-400 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-600 mb-2">Booking Details Not Available</h3>
                        <p class="text-gray-500">We couldn't retrieve your booking details at this time. Please check your email for confirmation or contact our support team.</p>
                    </div>
                @endif

                @if($payment)
                    <!-- Payment Information -->
                <div class="border border-gray-200 rounded-lg p-6 mb-8">
                    <h2 class="text-xl font-bold text-secondary-dark mb-4">Payment Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-secondary">Payment Reference</p>
                            <p class="font-medium text-secondary-dark">{{ $payment->payment_reference ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary">Transaction ID</p>
                            <p class="font-medium text-secondary-dark">{{ $payment->gateway_transaction_id ?? $payment->transaction_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary">Payment Status</p>
                            <p class="font-medium {{ ($payment && $payment->isPaid()) ? 'text-green-600' : 'text-yellow-600' }}">
                                @if($payment)
                                    {{ ucfirst($payment->payment_status) }}
                                @else
                                    Unknown
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary">Amount Paid</p>
                            <p class="font-medium text-secondary-dark">
                                @if($payment && $payment->total_price)
                                    {{ formatRupiah($payment->total_price) }}
                                @elseif($booking && $booking->total_price)
                                    {{ $booking->getFormattedTotalPrice() }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                @else
                    <!-- No Payment Data -->
                    <div class="border border-gray-200 rounded-lg p-6 mb-8 text-center">
                        <div class="text-gray-400 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-600 mb-2">Payment Information Not Available</h3>
                        <p class="text-gray-500">Payment details are being processed. You will receive a confirmation email shortly.</p>
                    </div>
                @endif

                <!-- Next Steps -->
                <div class="bg-blue-50 rounded-lg p-6 mb-8">
                    <h2 class="text-lg font-bold text-blue-800 mb-3">What's Next?</h2>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-blue-700">Check your email for a detailed booking confirmation and voucher.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-blue-700">Our travel consultant will contact you within 24 hours to discuss your trip details.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-blue-700">You can view your booking details anytime in your account dashboard.</span>
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('user-bookings') }}" class="flex-1 bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-md transition duration-300 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                       View My Bookings
                    </a>
                    @if($booking && $booking->travelPackage)
                        <a href="{{ route('travel-package-detail', $booking->travelPackage->slug) }}" class="flex-1 border border-gray-300 hover:bg-gray-50 text-secondary font-bold py-3 px-6 rounded-md transition duration-300 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Return to Package
                        </a>
                    @else
                        <a href="{{ route('travel-packages') }}" class="flex-1 border border-gray-300 hover:bg-gray-50 text-secondary font-bold py-3 px-6 rounded-md transition duration-300 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Browse Packages
                        </a>
                    @endif
                </div>
            </div>
        </div>


        <!-- Support Information -->
        <div class="mt-8 text-center">
            <p class="text-secondary mb-2">Need help with your booking?</p>
            <p class="text-secondary">
                Contact our support team at
                <a href="mailto:support@acehtouradventure.com" class="text-primary hover:underline">support@acehtouradventure.com</a>
                or call
                <a href="tel:+6265123456" class="text-primary hover:underline">+62 651 123456</a>
            </p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Payment Success JavaScript -->
@vite('resources/js/payment-success.js')
@endsection
