<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - @yield('title', 'Home')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            width: 100%;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }

        /* Custom text size */
        .text-2xs {
            font-size: 0.65rem;
            line-height: 1rem;
        }

        /* Custom breakpoint */
        @media (min-width: 480px) {
            /* Typography */
            .xs\:text-xs { font-size: 0.75rem; line-height: 1rem; }
            .xs\:text-sm { font-size: 0.875rem; line-height: 1.25rem; }
            .xs\:text-base { font-size: 1rem; line-height: 1.5rem; }
            .xs\:text-lg { font-size: 1.125rem; line-height: 1.75rem; }
            .xs\:text-xl { font-size: 1.25rem; line-height: 1.75rem; }

            /* Margins */
            .xs\:mt-0 { margin-top: 0; }
            .xs\:mt-1 { margin-top: 0.25rem; }
            .xs\:mb-1 { margin-bottom: 0.25rem; }
            .xs\:mb-1\.5 { margin-bottom: 0.375rem; }
            .xs\:mb-2 { margin-bottom: 0.5rem; }
            .xs\:mb-3 { margin-bottom: 0.75rem; }
            .xs\:mb-4 { margin-bottom: 1rem; }
            .xs\:mb-5 { margin-bottom: 1.25rem; }
            .xs\:mb-6 { margin-bottom: 1.5rem; }
            .xs\:mb-8 { margin-bottom: 2rem; }
            .xs\:mb-10 { margin-bottom: 2.5rem; }

            .xs\:mr-1 { margin-right: 0.25rem; }
            .xs\:mr-1\.5 { margin-right: 0.375rem; }
            .xs\:mr-2 { margin-right: 0.5rem; }

            .xs\:mx-1 { margin-left: 0.25rem; margin-right: 0.25rem; }
            .xs\:mx-2 { margin-left: 0.5rem; margin-right: 0.5rem; }
            .xs\:mx-3 { margin-left: 0.75rem; margin-right: 0.75rem; }

            /* Padding */
            .xs\:p-3 { padding: 0.75rem; }
            .xs\:p-4 { padding: 1rem; }
            .xs\:p-5 { padding: 1.25rem; }
            .xs\:p-6 { padding: 1.5rem; }

            .xs\:py-0\.5 { padding-top: 0.125rem; padding-bottom: 0.125rem; }
            .xs\:py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
            .xs\:py-1\.5 { padding-top: 0.375rem; padding-bottom: 0.375rem; }
            .xs\:py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
            .xs\:py-2\.5 { padding-top: 0.625rem; padding-bottom: 0.625rem; }

            .xs\:px-1 { padding-left: 0.25rem; padding-right: 0.25rem; }
            .xs\:px-1\.5 { padding-left: 0.375rem; padding-right: 0.375rem; }
            .xs\:px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
            .xs\:px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
            .xs\:px-4 { padding-left: 1rem; padding-right: 1rem; }
            .xs\:px-5 { padding-left: 1.25rem; padding-right: 1.25rem; }

            /* Sizing */
            .xs\:h-12 { height: 3rem; }
            .xs\:h-16 { height: 4rem; }
            .xs\:h-18 { height: 4.5rem; }
            .xs\:h-20 { height: 5rem; }
            .xs\:h-44 { height: 11rem; }

            .xs\:w-3 { width: 0.75rem; }
            .xs\:w-3\.5 { width: 0.875rem; }
            .xs\:w-4 { width: 1rem; }

            .xs\:h-3 { height: 0.75rem; }
            .xs\:h-3\.5 { height: 0.875rem; }
            .xs\:h-4 { height: 1rem; }

            /* Spacing */
            .xs\:space-y-0\.5 > :not([hidden]) ~ :not([hidden]) {
                --tw-space-y-reverse: 0;
                margin-top: calc(0.125rem * calc(1 - var(--tw-space-y-reverse)));
                margin-bottom: calc(0.125rem * var(--tw-space-y-reverse));
            }

            .xs\:space-y-1 > :not([hidden]) ~ :not([hidden]) {
                --tw-space-y-reverse: 0;
                margin-top: calc(0.25rem * calc(1 - var(--tw-space-y-reverse)));
                margin-bottom: calc(0.25rem * var(--tw-space-y-reverse));
            }

            .xs\:space-x-2 > :not([hidden]) ~ :not([hidden]) {
                --tw-space-x-reverse: 0;
                margin-right: calc(0.5rem * var(--tw-space-x-reverse));
                margin-left: calc(0.5rem * calc(1 - var(--tw-space-x-reverse)));
            }

            .xs\:space-x-3 > :not([hidden]) ~ :not([hidden]) {
                --tw-space-x-reverse: 0;
                margin-right: calc(0.75rem * var(--tw-space-x-reverse));
                margin-left: calc(0.75rem * calc(1 - var(--tw-space-x-reverse)));
            }

            /* Positioning */
            .xs\:bottom-1 { bottom: 0.25rem; }
            .xs\:bottom-2 { bottom: 0.5rem; }
            .xs\:bottom-3 { bottom: 0.75rem; }
            .xs\:bottom-4 { bottom: 1rem; }

            .xs\:left-2 { left: 0.5rem; }
            .xs\:left-3 { left: 0.75rem; }
            .xs\:left-4 { left: 1rem; }

            /* Display */
            .xs\:inline { display: inline; }
            .xs\:block { display: block; }
            .xs\:flex { display: flex; }
            .xs\:hidden { display: none; }

            /* Grid */
            .xs\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

            /* Max Width */
            .xs\:max-w-xs { max-width: 20rem; }
            .xs\:max-w-sm { max-width: 24rem; }
            .xs\:max-w-md { max-width: 28rem; }
            .xs\:max-w-lg { max-width: 32rem; }
            .xs\:max-w-xl { max-width: 36rem; }
            .xs\:max-w-2xl { max-width: 42rem; }
        }

        * {
            box-sizing: border-box;
        }

        .text-shadow {
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .animate-fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        .animation-delay-300 {
            animation-delay: 300ms;
        }

        .animation-delay-600 {
            animation-delay: 600ms;
        }

        .animation-delay-900 {
            animation-delay: 900ms;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-bounce {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* animate-on-scroll styles are now handled in app.css for consistency */
    </style>
    @stack('styles')
</head>
<body class="font-poppins antialiased bg-neutral text-secondary-dark m-0 p-0 min-h-screen w-full overflow-x-hidden box-border">
    <!-- Layout khusus untuk halaman autentikasi tanpa navbar dan footer -->
    <main>
        @yield('content')
    </main>

    <!-- Scripts loaded via Vite -->
    @stack('scripts')
</body>
</html>
