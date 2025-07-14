@extends('layouts.app')

@section('title', 'Payment Failed - Aceh Tour Adventure')

@section('content')
<div class="bg-gray-100 py-12 md:py-16 min-h-screen flex items-center">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Error Header -->
            <div class="bg-red-50 p-6 sm:p-8 text-center border-b border-red-100">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-red-800 mb-2">Payment Failed</h1>
                <p class="text-red-700 text-lg">We couldn't process your payment.</p>
            </div>
            
            <!-- Error Details -->
            <div class="p-6 sm:p-8">
                <div class="mb-8 text-center">
                    <p class="text-secondary text-lg mb-2">Don't worry, your booking has not been lost!</p>
                    <p class="text-secondary">You can try again or choose a different payment method.</p>
                </div>
                
                <!-- Error Information -->
                <div class="border border-gray-200 rounded-lg p-6 mb-8">
                    <h2 class="text-xl font-bold text-secondary-dark mb-4">What Happened?</h2>
                    
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    <span class="font-medium">Error:</span> Your payment was declined by your bank or payment provider.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <p class="text-secondary">Common reasons for payment failure include:</p>
                        <ul class="list-disc pl-5 space-y-2 text-secondary">
                            <li>Insufficient funds in your account</li>
                            <li>Incorrect card details entered</li>
                            <li>Card expired or blocked for online transactions</li>
                            <li>Transaction flagged by your bank's security system</li>
                            <li>Payment limit exceeded</li>
                            <li>Technical issues with the payment gateway</li>
                        </ul>
                    </div>
                </div>
                
                @if($booking)
                <!-- Booking Summary -->
                <div class="border border-gray-200 rounded-lg p-6 mb-8">
                    <h2 class="text-xl font-bold text-secondary-dark mb-4">Booking Summary</h2>
                    
                    <div class="flex items-center mb-6">
                        @if($booking->travelPackage && $booking->travelPackage->media->isNotEmpty())
                            <img src="{{ $booking->travelPackage->media->first()->getUrl() }}" alt="{{ $booking->travelPackage->name }}" class="w-20 h-20 object-cover rounded-lg mr-4">
                        @else
                            <div class="w-20 h-20 bg-gray-200 rounded-lg mr-4 flex items-center justify-center">
                                <span class="text-gray-500 text-xs">No Image</span>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-bold text-secondary-dark">{{ $booking->travelPackage->name ?? 'Package Name Not Available' }}</h3>
                            <p class="text-secondary text-sm">{{ $booking->travelPackage->duration ?? 'Duration Not Available' }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-secondary">Booking Reference</p>
                            <p class="font-medium text-secondary-dark">{{ $booking->booking_reference ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary">Attempted Payment Date</p>
                            <p class="font-medium text-secondary-dark">{{ $payment ? $payment->created_at->format('F d, Y') : ($booking->created_at ? $booking->created_at->format('F d, Y') : 'N/A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary">Travel Date</p>
                            <p class="font-medium text-secondary-dark">{{ $booking->booking_date ? $booking->booking_date->format('F d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary">Travelers</p>
                            <p class="font-medium text-secondary-dark">{{ $booking->person_count ?? 0 }} {{ ($booking->person_count ?? 0) > 1 ? 'Persons' : 'Person' }}</p>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        @if($booking->price_breakdown)
                            @php $breakdown = $booking->price_breakdown; @endphp
                            <div class="flex justify-between mb-2">
                                <span class="text-secondary">Base Price ({{ $booking->person_count ?? 0 }} persons)</span>
                                <span class="text-secondary">Rp {{ number_format((float)($breakdown['base_price'] ?? 0), 0, ',', '.') }}</span>
                            </div>
                            @if(isset($breakdown['additional_price']) && $breakdown['additional_price'] > 0)
                            <div class="flex justify-between mb-2">
                                <span class="text-secondary">Additional Price</span>
                                <span class="text-secondary">Rp {{ number_format((float)$breakdown['additional_price'], 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if(isset($breakdown['tax']) && $breakdown['tax'] > 0)
                            <div class="flex justify-between mb-2">
                                <span class="text-secondary">Taxes & Fees</span>
                                <span class="text-secondary">Rp {{ number_format((float)$breakdown['tax'], 0, ',', '.') }}</span>
                            </div>
                            @endif
                        @else
                            <div class="flex justify-between mb-2">
                                <span class="text-secondary">Package Price</span>
                                <span class="text-secondary">Rp {{ number_format((float)($booking->total_price ?? 0), 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-bold text-secondary-dark">
                            <span>Total Amount</span>
                            <span>Rp {{ number_format((float)($booking->total_price ?? 0), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @else
                <!-- Fallback when no booking data -->
                <div class="border border-gray-200 rounded-lg p-6 mb-8 text-center">
                    <h2 class="text-xl font-bold text-secondary-dark mb-4">Booking Details Not Available</h2>
                    <p class="text-secondary">We couldn't retrieve your booking information at this time.</p>
                </div>
                @endif
                
                <!-- What to do next -->
                <div class="bg-blue-50 rounded-lg p-6 mb-8">
                    <h2 class="text-lg font-bold text-blue-800 mb-3">What to do next?</h2>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-blue-700">Check with your bank to ensure there are no restrictions on your card.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-blue-700">Try again with the same payment method or use a different card.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-blue-700">Contact our support team if you continue to experience issues.</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    @if($booking && $booking->travelPackage)
                        <a href="{{ route('travel-package-detail', $booking->travelPackage->slug) }}#booking" class="flex-1 bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-md transition duration-300 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Try Payment Again
                        </a>
                    @else
                        <a href="{{ route('travel-packages') }}" class="flex-1 bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-md transition duration-300 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Browse Packages
                        </a>
                    @endif
                    <a href="{{ route('contact') }}" class="flex-1 border border-gray-300 hover:bg-gray-50 text-secondary font-bold py-3 px-6 rounded-md transition duration-300 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Alternative Payment Methods -->
        <div class="mt-8 bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6 sm:p-8">
                <h2 class="text-xl font-bold text-secondary-dark mb-4">Alternative Payment Methods</h2>
                <p class="text-secondary mb-6">Payment is processed securely through Midtrans payment gateway:</p>
                
                <div class="border border-gray-200 rounded-lg p-6 text-center">
                    <div class="flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-secondary-dark">Secure Payment Gateway</h3>
                    </div>
                    <p class="text-sm text-secondary">All payments are processed through Midtrans secure payment system</p>
                </div>
            </div>
        </div>
        
        <!-- Support Information -->
        <div class="mt-8 text-center">
            <p class="text-secondary mb-2">Need help with your payment?</p>
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