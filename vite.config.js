import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/navbar.js',
                'resources/js/travel-package-detail.js',
                'resources/js/travel-package-config.js',
                'resources/js/user-bookings.js',
                'resources/js/payment-callback.js',
                'resources/js/payment-callback-routes.js',
                'resources/js/payment-callback-handler.js',
                'resources/js/privacy.js',
                'resources/js/login.js',
                'resources/js/login-routes.js',
                'resources/js/payment-success.js',
                'resources/js/filament-app.js',
                'resources/js/home.js',
                'resources/js/terms.js',
                'resources/js/contact.js'
            ],
            refresh: true,
        }),
    ],
});
