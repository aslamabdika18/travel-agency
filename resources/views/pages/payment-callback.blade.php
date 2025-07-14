@extends('layouts.app')

@section('title', 'Payment Processing - Aceh Tour Adventure')

@section('content')
<div class="bg-gray-100 py-12 md:py-16 min-h-screen flex items-center">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Processing Header -->
            <div class="bg-blue-50 p-6 sm:p-8 text-center border-b border-blue-100">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                    <svg class="w-10 h-10 text-blue-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-blue-800 mb-2">Processing Your Payment</h1>
                <p class="text-blue-700 text-lg">Please wait while we verify your payment...</p>
            </div>
            
            <!-- Processing Information -->
            <div class="p-6 sm:p-8">
                <div class="mb-8 text-center">
                    <p class="text-secondary text-lg mb-2">This page will automatically redirect you once your payment is confirmed.</p>
                    <p class="text-secondary">Please do not close this window or refresh the page.</p>
                </div>
                
                <!-- Progress Bar -->
                <div class="mb-8">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full progress-bar" style="width: 0%"></div>
                    </div>
                    <p class="text-center text-sm text-secondary mt-2 progress-text">Connecting to payment gateway...</p>
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
                            <p class="text-sm text-secondary">Payment Date</p>
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
                    <h2 class="text-xl font-bold text-secondary-dark mb-4">Processing Payment</h2>
                    <p class="text-secondary">Please wait while we process your payment...</p>
                </div>
                @endif
                
                <!-- What to expect -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h2 class="text-lg font-bold text-secondary-dark mb-3">What happens next?</h2>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-secondary">Once your payment is confirmed, you'll be redirected to the success page.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-secondary">You'll receive a confirmation email with your booking details.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-secondary">Your booking will be confirmed and your travel voucher will be available in your account.</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Troubleshooting -->
                <div class="text-center">
                    <p class="text-secondary mb-4">If you're not redirected within 5 minutes, please contact our support team.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('home') }}" class="border border-gray-300 hover:bg-gray-50 text-secondary font-medium py-2 px-4 rounded-md transition duration-300 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Return to Home
                        </a>
                        <a href="{{ route('contact') }}" class="border border-gray-300 hover:bg-gray-50 text-secondary font-medium py-2 px-4 rounded-md transition duration-300 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Define routes for the payment callback JavaScript
    const paymentSuccessRoute = '{{ route("payment-success") }}';
    const paymentErrorRoute = '{{ route("payment-error") }}';
</script>
@vite(['resources/js/payment-callback-routes.js', 'resources/js/payment-callback-handler.js'])
@endsection
@endsection