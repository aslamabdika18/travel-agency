import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        screens: {
            'xs': '480px',
            ...defaultTheme.screens,
        },
        extend: {
            fontFamily: {
                sans: ['Poppins', 'Figtree', ...defaultTheme.fontFamily.sans],
                poppins: ['Poppins', 'sans-serif'],
            },
            colors: {
                primary: {
                    DEFAULT: '#E6B54A', // Soft gold - lebih lembut dan tidak terlalu mencolok dibanding gold asli
                    light: '#F2D791',   // Light gold - lebih lembut untuk hover dan background
                    dark: '#C99A31',    // Dark gold - lebih hangat dan tidak terlalu kontras
                },
                secondary: {
                    DEFAULT: '#6D5D4B', // Warm taupe - lebih netral dan tidak terlalu gelap dibanding brown
                    light: '#9C8E7E',   // Light taupe - lebih lembut untuk hover dan aksen
                    dark: '#4E4238',    // Dark taupe - lebih gelap tapi tidak terlalu kontras
                },
                neutral: {
                    DEFAULT: '#FFFFFF', // Pure white - tetap untuk background yang bersih
                    light: '#F7F7F7',   // Lighter gray - lebih lembut untuk section alternatif
                    dark: '#E8E8E8',    // Medium gray - lebih lembut untuk border dan divider
                },
                accent: {
                    DEFAULT: '#4A9B8F', // Soft teal - lebih lembut dan tidak terlalu mencolok
                    light: '#C5E0DD',   // Light teal - lebih lembut untuk hover dan background
                    dark: '#3A7A70',    // Dark teal - lebih gelap tapi tidak terlalu kontras
                }
            },
            animation: {
                'fadeIn': 'fadeIn 0.3s ease-in-out',
                'bounce-custom': 'bounce 2s infinite',
            },
            transitionDuration: {
                '400': '400ms',
                '600': '600ms',
                '800': '800ms',
                '900': '900ms',
            },
            transitionDelay: {
                '300': '300ms',
                '600': '600ms',
                '900': '900ms',
            },
            minWidth: {
                '48': '12rem',
            },
        },
    },
    plugins: [],
};
