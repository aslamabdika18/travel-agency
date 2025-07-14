@extends('layouts.app')

@section('title', 'Booking Details - Aceh Tour Adventure')

@section('content')
<div class="bg-gray-100 py-12 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Booking Status Banner -->
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        <span class="font-bold">Booking Confirmed</span> - Your booking has been confirmed and is ready for your adventure!
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Booking Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-secondary-dark">Booking #{{ $booking->booking_reference ?? 'N/A' }}</h1>
                <p class="text-secondary">Booked on {{ $booking->created_at ? $booking->created_at->format('F j, Y') : 'N/A' }}</p>
            </div>
            <div class="mt-4 md:mt-0 flex flex-wrap gap-3">
                <a href="#" class="inline-flex items-center px-4 py-2 border border-primary text-primary bg-white rounded-md hover:bg-primary hover:text-white transition duration-150 text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download Voucher
                </a>
                <a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 text-secondary bg-white rounded-md hover:bg-gray-50 transition duration-150 text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Email Voucher
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Package Details -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-secondary-dark mb-4">Package Details</h2>
                        
                        <div class="flex items-start">
                            @if($booking->travelPackage && $booking->travelPackage->media->isNotEmpty())
                                <img src="{{ $booking->travelPackage->media->first()->url }}" alt="{{ $booking->travelPackage->name }}" class="w-24 h-24 object-cover rounded-lg mr-4">
                            @else
                                <div class="w-24 h-24 bg-gray-200 rounded-lg mr-4 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-bold text-secondary-dark text-lg">{{ $booking->travelPackage->name ?? 'Package Not Available' }}</h3>
                                <p class="text-secondary text-sm mb-2">{{ $booking->travelPackage->duration ?? 'N/A' }}</p>
                                <div class="flex items-center text-sm text-secondary">
                                    <svg class="w-4 h-4 text-primary mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $booking->travelPackage->location ?? 'Aceh, Indonesia' }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-secondary">Travel Date</p>
                                    @if($booking->booking_date)
                                        @php
                                            $startDate = \Carbon\Carbon::parse($booking->booking_date);
                                            $duration = $booking->travelPackage->duration ?? '1 Day';
                                            $days = (int) filter_var($duration, FILTER_SANITIZE_NUMBER_INT);
                                            $endDate = $startDate->copy()->addDays($days - 1);
                                        @endphp
                                        <p class="font-medium text-secondary-dark">{{ $startDate->format('F j, Y') }} - {{ $endDate->format('F j, Y') }}</p>
                                    @else
                                        <p class="font-medium text-secondary-dark">Not specified</p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm text-secondary">Travelers</p>
                                    <p class="font-medium text-secondary-dark">{{ $booking->person_count ?? 1 }} {{ ($booking->person_count ?? 1) > 1 ? 'People' : 'Person' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-secondary">Tour Guide</p>
                                    <p class="font-medium text-secondary-dark">{{ $booking->travelPackage->guide_name ?? 'Will be assigned' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-secondary">Language</p>
                                    <p class="font-medium text-secondary-dark">{{ $booking->travelPackage->languages ?? 'English, Indonesian' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Itinerary Summary -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-secondary-dark mb-4">Itinerary Summary</h2>
                        
                        <div class="space-y-6">
                            @if($booking->travelPackage && $booking->travelPackage->itineraries && $booking->travelPackage->itineraries->count() > 0)
                                @foreach($booking->travelPackage->itineraries as $index => $itinerary)
                                <div class="flex">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-light flex items-center justify-center mr-4">
                                        <span class="text-primary font-bold">{{ $index + 1 }}</span>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-secondary-dark">{{ $itinerary->title }}</h3>
                                        <p class="text-secondary">{{ $itinerary->description }}</p>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-secondary">Detailed itinerary will be provided closer to your travel date.</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-6">
                            <a href="#" class="text-primary hover:text-primary-dark font-medium flex items-center">
                                View Full Itinerary
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Accommodation Details -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-secondary-dark mb-4">Accommodation</h2>
                        
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <img src="/images/hotels/banda-aceh-hotel.jpg" alt="Grand Aceh Hotel" class="w-20 h-20 object-cover rounded-lg mr-4">
                                <div>
                                    <h3 class="font-bold text-secondary-dark">Grand Aceh Hotel</h3>
                                    <p class="text-secondary text-sm mb-1">Banda Aceh (Night 1)</p>
                                    <div class="flex items-center">
                                        <div class="flex text-yellow-400">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-xs text-secondary ml-1">5-star hotel</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <img src="/images/hotels/sabang-resort.jpg" alt="Sabang Beach Resort" class="w-20 h-20 object-cover rounded-lg mr-4">
                                <div>
                                    <h3 class="font-bold text-secondary-dark">Sabang Beach Resort</h3>
                                    <p class="text-secondary text-sm mb-1">Sabang (Nights 2-4)</p>
                                    <div class="flex items-center">
                                        <div class="flex text-yellow-400">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-xs text-secondary ml-1">4-star resort</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 text-sm text-secondary">
                            <p>Room Type: Deluxe Double Room with Ocean View</p>
                            <p>Amenities: Free Wi-Fi, Breakfast included, Air conditioning, Private bathroom</p>
                        </div>
                    </div>
                </div>
                
                <!-- Traveler Information -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-secondary-dark mb-4">Traveler Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-bold text-secondary-dark">Lead Traveler</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <p class="text-sm text-secondary">Name</p>
                                        <p class="font-medium text-secondary-dark">{{ $booking->user->name ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-secondary">Email</p>
                                        <p class="font-medium text-secondary-dark">{{ $booking->user->email ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-secondary">Contact Number</p>
                                        <p class="font-medium text-secondary-dark">{{ $booking->user->contact ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-secondary">Nationality</p>
                                        <p class="font-medium text-secondary-dark">{{ $booking->user->nationality ?? 'Not specified' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            @if(($booking->person_count ?? 1) > 1)
                            <div class="border-t border-gray-200 pt-4">
                                <h3 class="font-bold text-secondary-dark">Additional Travelers</h3>
                                <div class="mt-2 text-secondary">
                                    <p>{{ ($booking->person_count ?? 1) - 1 }} additional {{ (($booking->person_count ?? 1) - 1) > 1 ? 'travelers' : 'traveler' }} will be traveling with you.</p>
                                    <p class="text-sm mt-1">Details will be collected before departure.</p>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <h3 class="font-bold text-secondary-dark mb-2">Special Requests</h3>
                            @if($booking->special_requests)
                                <p class="text-secondary">{{ $booking->special_requests }}</p>
                            @else
                                <p class="text-secondary italic">No special requests specified.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Payment Information -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-secondary-dark mb-4">Payment Information</h2>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-secondary">Payment Status</span>
                                @php
                                    $paymentStatus = $booking->payment->payment_status ?? $booking->status ?? 'pending';
                                    $statusClass = match($paymentStatus) {
                                        'Paid', 'completed' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'failed', 'cancelled' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                    $statusText = match($paymentStatus) {
                                        'Paid' => 'Paid in Full',
                                        'completed' => 'Completed',
                                        'pending' => 'Pending Payment',
                                        'failed' => 'Payment Failed',
                                        'cancelled' => 'Cancelled',
                                        default => ucfirst($paymentStatus)
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-secondary">Payment Method</span>
                                <span class="text-secondary-dark font-medium">{{ $booking->payment->payment_method ?? 'Midtrans Payment Gateway' }}</span>
                            </div>
                            
                            @if($booking->payment && $booking->payment->transaction_id)
                            <div class="flex justify-between">
                                <span class="text-secondary">Transaction ID</span>
                                <span class="text-secondary-dark font-medium">{{ $booking->payment->transaction_id }}</span>
                            </div>
                            @endif
                            
                            <div class="flex justify-between">
                                <span class="text-secondary">Payment Date</span>
                                <span class="text-secondary-dark font-medium">
                                    {{ $booking->payment && $booking->payment->created_at ? $booking->payment->created_at->format('F j, Y') : ($booking->created_at ? $booking->created_at->format('F j, Y') : 'N/A') }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-secondary">Package Price ({{ $booking->person_count ?? 1 }} {{ ($booking->person_count ?? 1) > 1 ? 'People' : 'Person' }})</span>
                                <span class="text-secondary">Rp {{ number_format((float)($booking->base_price ?? 0), 0, ',', '.') }}</span>
                            </div>
                            @if(($booking->additional_price ?? 0) > 0)
                            <div class="flex justify-between mb-2">
                                <span class="text-secondary">Additional Services</span>
                                <span class="text-secondary">Rp {{ number_format((float)$booking->additional_price, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if(($booking->tax_amount ?? 0) > 0)
                            <div class="flex justify-between mb-2">
                                <span class="text-secondary">Taxes & Fees</span>
                                <span class="text-secondary">Rp {{ number_format((float)$booking->tax_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between font-bold text-secondary-dark">
                                <span>Total Amount</span>
                                <span>Rp {{ number_format((float)($booking->total_price ?? 0), 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <a href="#" class="text-primary hover:text-primary-dark font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                                Download Receipt
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Cancellation Policy -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-secondary-dark mb-4">Cancellation Policy</h2>
                        
                        <div class="space-y-3 text-secondary text-sm">
                            <p>• Free cancellation up to 30 days before departure</p>
                            <p>• 50% refund for cancellations 15-29 days before departure</p>
                            <p>• 25% refund for cancellations 7-14 days before departure</p>
                            <p>• No refund for cancellations less than 7 days before departure</p>
                        </div>
                        
                        <div class="mt-4">
                            <a href="#" class="text-primary hover:text-primary-dark font-medium">View Full Policy</a>
                        </div>
                    </div>
                </div>
                
                <!-- Need Help -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-secondary-dark mb-4">Need Help?</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-primary mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-secondary-dark">Phone Support</p>
                                    <p class="text-secondary text-sm">+62 651 123456</p>
                                    <p class="text-secondary text-sm">Available 24/7</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-primary mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-secondary-dark">Email Support</p>
                                    <p class="text-secondary text-sm">support@acehtouradventure.com</p>
                                    <p class="text-secondary text-sm">Response within 24 hours</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-primary mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-secondary-dark">WhatsApp</p>
                                    <p class="text-secondary text-sm">+62 812 3456 7890</p>
                                    <p class="text-secondary text-sm">Quick response during business hours</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <a href="{{ route('contact') }}" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-md transition duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="mt-8 flex flex-wrap gap-4">
            <a href="{{ route('user-bookings') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-secondary bg-white rounded-md hover:bg-gray-50 transition duration-150 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to My Bookings
            </a>
            <a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 text-secondary bg-white rounded-md hover:bg-gray-50 transition duration-150 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                FAQ
            </a>
            <a href="#" class="inline-flex items-center px-4 py-2 border border-primary text-primary bg-white rounded-md hover:bg-primary hover:text-white transition duration-150 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Write a Review
            </a>
        </div>
    </div>
</div>
@endsection