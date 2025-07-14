@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="h-screen bg-gray-100 overflow-hidden relative">
    <!-- Hidden checkbox for CSS-only toggle -->
    <input type="checkbox" id="authToggle" class="hidden">

    <div class="h-full flex relative">
        <!-- Left Side - Auth Forms -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-1 sm:p-2 lg:p-4 transition-all duration-1000 ease-out transform" id="formContainer">
            <div class="w-full max-w-sm sm:max-w-md h-full flex items-center justify-center">
                <!-- Form Container -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-3 sm:p-4 lg:p-6 w-full max-h-[95vh] flex flex-col overflow-hidden relative z-10">
                    <!-- Logo -->
                    <div class="text-center mb-2 sm:mb-3 flex-shrink-0">
                        <div class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-primary-light rounded-full mb-1 sm:mb-2">
                            <!-- Login Icon (visible by default) -->
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-primary login-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <!-- Register Icon (hidden by default) -->
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-primary register-icon hidden" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                            </svg>
                        </div>
                        <h2 class="text-base sm:text-lg lg:text-xl font-bold text-gray-800 auth-title">
                            <span class="login-title">Welcome Back!</span>
                            <span class="register-title hidden">Create Account</span>
                        </h2>
                        <p class="text-gray-600 mt-0.5 sm:mt-1 text-xs sm:text-sm auth-subtitle">
                            <span class="login-subtitle">Please sign in to continue</span>
                            <span class="register-subtitle hidden">Get started with your account</span>
                        </p>
                    </div>

                    <!-- Form -->
                    <div class="flex-1 min-h-0 overflow-y-auto">
                        <form id="authForm" action="{{ route('login.post') }}" method="POST" class="space-y-1.5 sm:space-y-2 h-full flex flex-col">
                            @csrf
                            <!-- Hidden field for intended URL -->
                            @if(request()->has('intended'))
                            <input type="hidden" name="intended" value="{{ request()->get('intended') }}">
                            @endif
                            <!-- Server Error Message -->
                            @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative mb-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <h3 class="text-sm font-medium text-red-800">Login Error</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Name Field (Register only) -->
                            <div class="name-field transition-all duration-500 ease-out hidden opacity-0 translate-y-5">
                                <label for="name" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                                    Full Name
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        class="w-full px-3 py-2 sm:px-3 sm:py-2.5 rounded-lg border border-gray-300 focus:ring-primary focus:border-transparent transition-colors text-sm"
                                        placeholder="John Doe"
                                    >
                                </div>
                                <p class="mt-1 text-xs sm:text-sm text-red-500 name-error hidden">
                                    Nama harus minimal 2 karakter
                                </p>
                            </div>

                            <!-- Email Field -->
                            <div>
                                <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                                    Email Address
                                </label>
                                <div class="relative">
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        class="w-full px-3 py-2 sm:px-3 sm:py-2.5 pr-10 rounded-lg border border-gray-300 focus:ring-primary focus:border-transparent transition-colors text-sm"
                                        placeholder="you@example.com"
                                    >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 absolute right-3 top-1/2 transform -translate-y-1/2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                </div>
                                <p class="mt-1 text-xs sm:text-sm text-red-500 email-error hidden">
                                    Masukkan alamat email yang valid
                                </p>
                            </div>

                            <!-- Password Field -->
                            <div>
                                <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                                    Password
                                </label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        required
                                        class="w-full px-3 py-2 sm:px-3 sm:py-2.5 pr-10 rounded-lg border border-gray-300 focus:ring-primary focus:border-transparent transition-colors text-sm"
                                        placeholder="••••••••"
                                    >
                                    <button type="button" class="toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none" data-target="password">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                        </svg>
                                    </button>
                                </div>
                                <p class="mt-1 text-xs sm:text-sm text-red-500 password-error hidden">
                                    Password harus minimal 8 karakter
                                </p>
                            </div>

                            <!-- Confirm Password Field (Register only) -->
                            <div class="confirm-password-field transition-all duration-500 ease-out hidden opacity-0 translate-y-5">
                                <label for="password_confirmation" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                                    Confirm Password
                                </label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        class="w-full px-3 py-2 sm:px-3 sm:py-2.5 pr-10 rounded-lg border border-gray-300 focus:ring-primary focus:border-transparent transition-colors text-sm"
                                        placeholder="••••••••"
                                    >
                                    <button type="button" class="toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none" data-target="password_confirmation">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                        </svg>
                                    </button>
                                </div>
                                <p class="mt-1 text-xs sm:text-sm text-red-500 confirm-password-error hidden">
                                    Password tidak sama
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-auto pt-2 sm:pt-3">
                                <button
                                    type="submit"
                                    class="w-full bg-primary text-white py-2 sm:py-2.5 rounded-lg font-semibold hover:bg-primary-dark focus:ring-4 focus:ring-primary focus:ring-opacity-50 transition-colors text-sm"
                                >
                                    <span class="login-text">Sign In</span>
                                    <span class="register-text hidden">Create Account</span>
                                </button>

                                <!-- Form Switch -->
                                <p class="mt-3 sm:mt-4 text-center text-gray-600 text-xs sm:text-sm">
                                    <span class="login-text">Don't have an account?</span>
                                    <span class="register-text hidden">Already have an account?</span>
                                    <label for="authToggle" class="ml-1 text-primary hover:text-primary-dark font-semibold cursor-pointer">
                                        <span class="login-text">Sign up</span>
                                        <span class="register-text hidden">Sign in</span>
                                    </label>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Image -->
        <div class="hidden lg:block lg:w-1/2 h-full relative transition-all duration-1000 ease-out transform" id="imageContainer">
            <!-- Login Image - Travel Theme -->
            <div class="absolute inset-0 bg-cover bg-center transition-all duration-700 ease-out login-image"
            style="background-image: url('https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&q=80')">
                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                    <div class="text-center text-white px-8 xl:px-12">
                        <h2 class="text-3xl xl:text-4xl font-bold mb-4 xl:mb-6">Welcome Back, Explorer!</h2>
                        <p class="text-lg xl:text-xl leading-relaxed">
                            Sign in to continue planning your next adventure and discover amazing destinations.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Register Image - Travel Theme -->
            <div class="absolute inset-0 bg-cover bg-center transition-all duration-700 ease-out opacity-0 register-image"
            style="background-image: url('https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&q=80')">
                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                    <div class="text-center text-white px-8 xl:px-12">
                        <h2 class="text-3xl xl:text-4xl font-bold mb-4 xl:mb-6">Start Your Journey!</h2>
                        <p class="text-lg xl:text-xl leading-relaxed">
                            Join thousands of travelers and create unforgettable memories around the world.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Background Animation - Travel Theme -->
        <div class="lg:hidden absolute inset-0 -z-10">
            <div class="absolute inset-0 bg-cover bg-center transition-all duration-700 ease-out login-image"
            style="background-image: url('https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&q=80')">
                <div class="absolute inset-0 bg-gray-100 bg-opacity-90"></div>
            </div>

            <div class="absolute inset-0 bg-cover bg-center transition-all duration-700 ease-out opacity-0 register-image"
            style="background-image: url('https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&q=80')">
                <div class="absolute inset-0 bg-gray-100 bg-opacity-90"></div>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS-only toggle functionality */
/* When checkbox is checked (register mode) */
#authToggle:checked ~ div .login-icon,
#authToggle:checked ~ div .login-text,
#authToggle:checked ~ div .login-title,
#authToggle:checked ~ div .login-subtitle {
    display: none;
}

#authToggle:checked ~ div .login-image {
    opacity: 0;
    transform: scale(1);
}

#authToggle:checked ~ div .register-icon,
#authToggle:checked ~ div .register-text,
#authToggle:checked ~ div .register-title,
#authToggle:checked ~ div .register-subtitle {
    display: inline;
}

#authToggle:checked ~ div .register-image {
    opacity: 1;
    transform: scale(1);
}

#authToggle:checked ~ div .name-field,
#authToggle:checked ~ div .confirm-password-field {
    display: block;
    opacity: 1;
    transform: translateY(0);
}

/* Slide transition from left to right */
#authToggle:checked ~ div #formContainer {
    transform: translateX(100%);
}

#authToggle:checked ~ div #imageContainer {
    transform: translateX(-100%);
}

/* Default state (login mode) */
.register-icon,
.register-text,
.register-title,
.register-subtitle {
    display: none;
}

.name-field,
.confirm-password-field {
    display: none;
}

.register-image {
    opacity: 0;
    transform: scale(1);
}

.login-image {
    opacity: 1;
    transform: scale(1);
}
</style>

<script>
    // Define routes for the login JavaScript
    const loginRoute = '{{ route("login.post") }}';
    const registerRoute = '{{ route("register.post") }}';
</script>
@vite(['resources/js/login-routes.js', 'resources/js/login.js'])
@endsection
