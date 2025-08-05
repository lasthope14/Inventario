<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS para consistencia -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

        <style>
            /* Dark theme styles for guest layout */
            [data-bs-theme="dark"] body {
                background-color: #121212 !important;
                color: #e0e0e0 !important;
            }

            [data-bs-theme="dark"] .bg-gray-100 {
                background-color: #121212 !important;
            }

            [data-bs-theme="dark"] .bg-white {
                background-color: #1e1e1e !important;
            }

            [data-bs-theme="dark"] .text-gray-900 {
                color: #e0e0e0 !important;
            }

            [data-bs-theme="dark"] .text-gray-500 {
                color: #adb5bd !important;
            }

            [data-bs-theme="dark"] .shadow-md {
                box-shadow: 0 4px 6px -1px rgba(255, 255, 255, 0.1), 0 2px 4px -1px rgba(255, 255, 255, 0.06) !important;
            }

            body.dark-theme {
                background-color: #121212 !important;
                color: #e0e0e0 !important;
            }

            body.dark-theme .bg-gray-100 {
                background-color: #121212 !important;
            }

            body.dark-theme .bg-white {
                background-color: #1e1e1e !important;
            }

            body.dark-theme .text-gray-900 {
                color: #e0e0e0 !important;
            }

            body.dark-theme .text-gray-500 {
                color: #adb5bd !important;
            }

            body.dark-theme .shadow-md {
                box-shadow: 0 4px 6px -1px rgba(255, 255, 255, 0.1), 0 2px 4px -1px rgba(255, 255, 255, 0.06) !important;
            }

            body.dark-theme .form-control {
                background-color: #2d2d2d !important;
                border-color: #404040 !important;
                color: #e0e0e0 !important;
            }

            body.dark-theme .btn-primary {
                background-color: #0d6efd !important;
                border-color: #0d6efd !important;
            }

            body.dark-theme .card {
                background-color: #1e1e1e !important;
                border-color: #404040 !important;
                color: #e0e0e0 !important;
            }

            body.dark-theme a {
                color: #66b3ff !important;
            }

            body.dark-theme a:hover {
                color: #4da3ff !important;
            }
        </style>
      
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <script>
            // Apply saved theme on page load
            document.addEventListener('DOMContentLoaded', function() {
                const savedTheme = localStorage.getItem('app-theme') || 'light';
                const html = document.documentElement;
                const body = document.body;
                
                html.setAttribute('data-bs-theme', savedTheme);
                
                if (savedTheme === 'dark') {
                    body.classList.add('dark-theme');
                } else {
                    body.classList.remove('dark-theme');
                }
            });
        </script>
    </body>
</html>
