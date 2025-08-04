@extends('layouts.app')

@section('title', 'Verifikasi Email')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-6 px-4 sm:py-12 sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-sm sm:max-w-md">
        <div class="text-center">
            <img class="mx-auto h-10 w-auto sm:h-12" src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}">
            <h2 class="mt-4 text-2xl sm:mt-6 sm:text-3xl font-extrabold text-gray-900">
                Verifikasi Email Anda
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Kami telah mengirim link verifikasi ke email Anda
            </p>
        </div>
    </div>

    <div class="mt-6 mx-auto w-full max-w-sm sm:mt-8 sm:max-w-md">
        <div class="bg-white py-6 px-4 shadow rounded-lg sm:py-8 sm:px-10 sm:rounded-lg">
            <div class="text-center">
                <!-- Email Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 sm:h-16 sm:w-16 rounded-full bg-blue-100 mb-4 sm:mb-6">
                    <svg class="h-6 w-6 sm:h-8 sm:w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>

                <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">
                    Periksa Email Anda
                </h3>
                
                <p class="text-sm text-gray-600 mb-4 sm:mb-6 leading-relaxed">
                    Kami telah mengirim link verifikasi ke <strong class="break-all">{{ Auth::user()->email }}</strong>. 
                    Silakan klik link tersebut untuk mengaktifkan akun Anda.
                </p>

                <div class="space-y-3 sm:space-y-4">
                    <!-- Resend Verification Email Form -->
                    <form method="POST" action="{{ route('verification.send') }}" id="resend-form">
                        @csrf
                        <button type="submit" 
                                class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                                id="resend-btn">
                            <span id="resend-text" class="text-center">Kirim Ulang Email Verifikasi</span>
                            <span id="resend-loading" class="hidden flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mengirim...
                            </span>
                        </button>
                    </form>

                    <!-- Logout Form -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex justify-center py-2.5 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Logout
                        </button>
                    </form>
                </div>

                <!-- Help Text -->
                <div class="mt-4 sm:mt-6 text-xs sm:text-sm text-gray-500">
                    <p class="font-medium mb-2">Tidak menerima email?</p>
                    <ul class="mt-2 space-y-1 text-left">
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Periksa folder spam/junk email Anda</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Pastikan email <span class="break-all font-medium">{{ Auth::user()->email }}</span> benar</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Tunggu beberapa menit sebelum mencoba lagi</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const resendForm = document.getElementById('resend-form');
    const resendBtn = document.getElementById('resend-btn');
    const resendText = document.getElementById('resend-text');
    const resendLoading = document.getElementById('resend-loading');
    
    let cooldownTime = 0;
    let cooldownInterval;

    // Handle form submission
    resendForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (cooldownTime > 0) {
            return;
        }
        
        // Show loading state
        resendBtn.disabled = true;
        resendText.classList.add('hidden');
        resendLoading.classList.remove('hidden');
        
        // Submit form via fetch
        fetch(resendForm.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast('success', data.message);
                
                // Start cooldown
                startCooldown(60); // 60 seconds cooldown
            } else {
                showToast('error', data.message || 'Terjadi kesalahan');
                resetButton();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Terjadi kesalahan saat mengirim email');
            resetButton();
        });
    });
    
    function resetButton() {
        resendBtn.disabled = false;
        resendText.classList.remove('hidden');
        resendLoading.classList.add('hidden');
    }
    
    function startCooldown(seconds) {
        cooldownTime = seconds;
        updateButtonText();
        
        cooldownInterval = setInterval(() => {
            cooldownTime--;
            updateButtonText();
            
            if (cooldownTime <= 0) {
                clearInterval(cooldownInterval);
                resetButton();
            }
        }, 1000);
    }
    
    function updateButtonText() {
        if (cooldownTime > 0) {
            resendBtn.disabled = true;
            resendText.textContent = `Kirim Ulang dalam ${cooldownTime}s`;
            resendText.classList.remove('hidden');
            resendLoading.classList.add('hidden');
        } else {
            resendText.textContent = 'Kirim Ulang Email Verifikasi';
        }
    }
    
    function showToast(type, message) {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `fixed top-4 left-4 right-4 sm:top-4 sm:right-4 sm:left-auto z-50 p-3 sm:p-4 rounded-md shadow-lg max-w-sm mx-auto sm:mx-0 text-sm sm:text-base ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        toast.textContent = message;
        
        // Add animation
        toast.style.transform = 'translateY(-20px)';
        toast.style.opacity = '0';
        toast.style.transition = 'all 0.3s ease-in-out';
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        }, 10);
        
        // Remove toast after 5 seconds
        setTimeout(() => {
            toast.style.transform = 'translateY(-20px)';
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 5000);
    }
});
</script>
@endpush
@endsection