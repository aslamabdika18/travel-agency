@extends('layouts.app')

@section('title', 'Unauthorized Access - Aceh Tour Adventure')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col items-center justify-center px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Unauthorized Access</h2>
                <p class="text-gray-600">
                    You don't have permission to access this page. This area is restricted to authorized users only.
                </p>
            </div>
            
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-700 mb-2">Possible reasons:</h3>
                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                        <li>You are not logged in</li>
                        <li>Your account doesn't have the required permissions</li>
                        <li>Your session may have expired</li>
                        <li>You're trying to access a restricted area</li>
                    </ul>
                </div>
                
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('auth') }}" class="flex-1 bg-primary hover:bg-primary-dark text-white text-center py-3 px-4 rounded-lg font-medium transition duration-300">
                        Sign In
                    </a>
                    <a href="{{ route('home') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 text-center py-3 px-4 rounded-lg font-medium transition duration-300">
                        Go to Homepage
                    </a>
                </div>
                
                <p class="text-sm text-gray-500 text-center mt-6">
                    If you believe this is an error, please contact our support team for assistance.
                </p>
            </div>
        </div>
    </div>
    
    <div class="mt-8 animate-on-scroll">
        <a href="{{ route('contact') }}" class="text-primary hover:text-primary-dark font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            Need help? Contact our support team
        </a>
    </div>
</div>
@endsection