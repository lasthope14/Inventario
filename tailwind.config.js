// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                inter: ['Inter', 'sans-serif'],
            },
            colors: {
                'hidroobras': {
                    50: '#e6f3ff',
                    100: '#b3d9ff',
                    200: '#80bfff',
                    300: '#4da5ff',
                    400: '#1a8bff',
                    500: '#0076ce',
                    600: '#0055a4',
                    700: '#004480',
                    800: '#00335c',
                    900: '#002238',
                },
                'accent': {
                    50: '#e6f9fc',
                    100: '#b3ecf5',
                    200: '#80dfee',
                    300: '#4dd2e7',
                    400: '#48CAE4',
                    500: '#1ab7d8',
                    600: '#0ea5c7',
                    700: '#0b93b6',
                    800: '#0881a5',
                    900: '#056f94',
                },
            },
            animation: {
                'fade-in': 'fadeIn 0.6s ease-in-out',
                'slide-up': 'slideUp 0.8s ease-out',
                'bounce-gentle': 'bounceGentle 2s infinite',
                'float': 'float 3s ease-in-out infinite',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(30px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                bounceGentle: {
                    '0%, 100%': { transform: 'translateY(-5px)' },
                    '50%': { transform: 'translateY(0)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
            },
            screens: {
                'xs': '475px',
            },
            boxShadow: {
                'hidroobras': '0 4px 14px 0 rgba(0, 118, 206, 0.15)',
                'hidroobras-lg': '0 10px 25px -3px rgba(0, 118, 206, 0.1)',
                'card': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                'card-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
            },
        },
    },
    plugins: [forms, typography],
};
