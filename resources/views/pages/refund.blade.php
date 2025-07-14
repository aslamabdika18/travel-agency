@extends('layouts.app')

@section('title', 'Refund Request')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Refund Request</h1>
            <p class="text-gray-600">Request a refund for your booking based on our refund policy.</p>
        </div>

        @if(!config('app.refund_enabled', env('REFUND_ENABLED', true)))
        <!-- Refund Disabled Notice -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Refund Service Temporarily Disabled</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>The refund service is currently disabled for maintenance. Please try again later or contact our customer support for assistance.</p>
                    </div>
                </div>
            </div>
        </div>
        @else

        <!-- Refund Policy Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-blue-900 mb-3">Refund Policy</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="font-medium text-green-700">30+ days before departure</span>
                    </div>
                    <p class="text-sm text-gray-600">100% refund</p>
                </div>
                <div class="bg-white p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        <span class="font-medium text-yellow-700">15-29 days before departure</span>
                    </div>
                    <p class="text-sm text-gray-600">50% refund</p>
                </div>
                <div class="bg-white p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <div class="w-3 h-3 bg-orange-500 rounded-full mr-2"></div>
                        <span class="font-medium text-orange-700">7-14 days before departure</span>
                    </div>
                    <p class="text-sm text-gray-600">25% refund</p>
                </div>
                <div class="bg-white p-4 rounded-lg">
                    <div class="flex items-center mb-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        <span class="font-medium text-red-700">Less than 7 days</span>
                    </div>
                    <p class="text-sm text-gray-600">No refund</p>
                </div>
            </div>
        </div>

        <!-- Booking Selection -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Booking for Refund</h2>
            
            <!-- Search Booking -->
            <div class="mb-4">
                <label for="booking-search" class="block text-sm font-medium text-gray-700 mb-2">
                    Search by Booking Reference
                </label>
                <div class="flex gap-2">
                    <input type="text" 
                           id="booking-search" 
                           placeholder="Enter booking reference (e.g., BK-2024-001)"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <button type="button" 
                            id="search-booking-btn"
                            class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors">
                        Search
                    </button>
                </div>
            </div>

            <!-- Eligible Bookings List -->
            <div id="eligible-bookings" class="space-y-4">
                <!-- Bookings will be loaded here -->
            </div>
        </div>

        <!-- Refund Form -->
        <div id="refund-form-container" class="bg-white rounded-lg shadow-sm p-6 hidden">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Refund Request Form</h2>
            
            <form id="refund-form">
                <!-- Selected Booking Info -->
                <div id="selected-booking-info" class="bg-gray-50 p-4 rounded-lg mb-6">
                    <!-- Booking details will be shown here -->
                </div>

                <!-- Refund Details -->
                <div id="refund-details" class="bg-blue-50 p-4 rounded-lg mb-6">
                    <!-- Refund calculation will be shown here -->
                </div>

                <!-- Reason -->
                <div class="mb-6">
                    <label for="refund-reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for Refund (Optional)
                    </label>
                    <textarea id="refund-reason" 
                              name="reason"
                              rows="4" 
                              placeholder="Please provide a reason for your refund request..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                    <p class="text-sm text-gray-500 mt-1">Minimum 10 characters, maximum 500 characters</p>
                </div>

                <!-- Confirmation -->
                <div class="mb-6">
                    <label class="flex items-start">
                        <input type="checkbox" 
                               id="refund-confirm" 
                               name="confirm"
                               class="mt-1 mr-3 h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <span class="text-sm text-gray-700">
                            I understand and agree to the refund policy. I confirm that I want to proceed with this refund request.
                            <strong class="text-red-600">This action cannot be undone.</strong>
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4">
                    <button type="submit" 
                            id="submit-refund-btn"
                            class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="btn-text">Process Refund</span>
                        <span class="btn-loading hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                    <button type="button" 
                            id="cancel-refund-btn"
                            class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Refund History -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Refund History</h2>
            <div id="refund-history" class="space-y-4">
                <!-- Refund history will be loaded here -->
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Refund Request Submitted</h3>
            <p class="text-sm text-gray-500 mb-4" id="success-message">
                Your refund request has been processed successfully.
            </p>
            <button type="button" 
                    id="close-success-modal"
                    class="w-full px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="error-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Error</h3>
            <p class="text-sm text-gray-500 mb-4" id="error-message">
                An error occurred while processing your request.
            </p>
            <button type="button" 
                    id="close-error-modal"
                    class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/refund-handler.js') }}"></script>
@endpush