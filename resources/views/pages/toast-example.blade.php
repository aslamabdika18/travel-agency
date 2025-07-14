@extends('layouts.app')

@section('title', 'Toast Notification Examples')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <h1 class="text-2xl font-bold text-primary mb-6">Toast Notification Examples</h1>
                
                <p class="mb-6 text-secondary-dark">
                    This page demonstrates how to use toast notifications in Laravel applications. 
                    Click the buttons below to see different types of notifications.
                </p>
                
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-secondary-dark">1. Server-side Notifications (PHP)</h2>
                    <p class="mb-4 text-secondary">
                        These notifications are sent from the server using session flash messages.
                    </p>
                    
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('toast.success') }}" class="btn btn-primary">
                            Success
                        </a>
                        <a href="{{ route('toast.error') }}" class="btn bg-red-500 hover:bg-red-600 text-white">
                            Error
                        </a>
                        <a href="{{ route('toast.warning') }}" class="btn bg-yellow-500 hover:bg-yellow-600 text-white">
                            Warning
                        </a>
                        <a href="{{ route('toast.info') }}" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                            Information
                        </a>
                    </div>
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-200 space-y-4">
                    <h2 class="text-xl font-semibold text-secondary-dark">2. Client-side Notifications (JavaScript)</h2>
                    <p class="mb-4 text-secondary">
                        These notifications are triggered directly from JavaScript without page reload.
                    </p>
                    
                    <div class="flex flex-wrap gap-3">
                        <button onclick="window.toast.success('Operation completed successfully!')" class="btn btn-primary">
                            Success
                        </button>
                        <button onclick="window.toast.error('An error occurred while processing the request!')" class="btn bg-red-500 hover:bg-red-600 text-white">
                            Error
                        </button>
                        <button onclick="window.toast.warning('Warning! This is a warning message.')" class="btn bg-yellow-500 hover:bg-yellow-600 text-white">
                            Warning
                        </button>
                        <button onclick="window.toast.info('Information: This is an informational message.')" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                            Information
                        </button>
                    </div>
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h2 class="text-xl font-semibold text-secondary-dark mb-4">Usage Examples</h2>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-secondary-dark mb-2">In Controller:</h3>
                        <pre class="bg-gray-800 text-white p-4 rounded overflow-x-auto"><code>// Redirect with notification
return redirect()->route('home')
    ->with('toast_success', 'Operation completed successfully!');

// Or for error
return back()
    ->with('toast_error', 'An error occurred!');</code></pre>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg mt-4">
                        <h3 class="text-lg font-medium text-secondary-dark mb-2">In JavaScript:</h3>
                        <pre class="bg-gray-800 text-white p-4 rounded overflow-x-auto"><code>// Display success notification
window.toast.success('Success message');

// Display error notification
window.toast.error('Error message');

// Display warning notification
window.toast.warning('Warning message');

// Display info notification
window.toast.info('Information message');</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection