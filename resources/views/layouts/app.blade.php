<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/apple-touch-icon.png') }}">
    <meta name="msapplication-TileImage" content="{{ asset('assets/favicon.png') }}">
    <meta name="msapplication-TileColor" content="#006D95">
    <meta name="theme-color" content="#006D95">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">

    <!-- Bootstrap CSS (Última versión) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <style>
        /* Estilos Generales */
        body {
            font-family: 'Figtree', 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 10px;
            transition: box-shadow 0.3s ease-in-out, transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table td {
            font-size: 0.875rem;
        }

        /* Dark Mode Styles - Contraste Mejorado */
        [data-bs-theme="dark"] {
            --bs-body-bg: #0f172a;
            --bs-body-color: #f8fafc;
            --bs-border-color: #475569;
            --bs-secondary-bg: #1e293b;
        }

        [data-bs-theme="dark"] body {
            background-color: #0f172a !important;
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .bg-light {
            background-color: #1e293b !important;
        }

        [data-bs-theme="dark"] .bg-white {
            background-color: #1e293b !important;
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .card {
            background-color: #1e293b;
            border-color: #475569;
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .table {
            --bs-table-bg: #1e293b;
            --bs-table-color: #f8fafc;
            --bs-table-border-color: #475569;
        }

        [data-bs-theme="dark"] .table-light {
            --bs-table-bg: #334155;
            --bs-table-color: #f8fafc;
        }

        [data-bs-theme="dark"] .form-control {
            background-color: #334155;
            border-color: #475569;
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .form-control:focus {
            background-color: #334155;
            border-color: #3b82f6;
            color: #f8fafc;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4);
        }

        [data-bs-theme="dark"] .form-select {
            background-color: #334155;
            border-color: #475569;
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .btn-outline-primary {
            color: #60a5fa;
            border-color: #60a5fa;
        }

        [data-bs-theme="dark"] .btn-outline-primary:hover {
            background-color: #60a5fa;
            color: #0f172a;
        }

        [data-bs-theme="dark"] .btn-outline-secondary {
            color: #e2e8f0;
            border-color: #64748b;
        }

        [data-bs-theme="dark"] .btn-outline-secondary:hover {
            background-color: #64748b;
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .btn-outline-danger {
            color: #f87171;
            border-color: #f87171;
        }

        [data-bs-theme="dark"] .btn-outline-danger:hover {
            background-color: #f87171;
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #334155;
            border-color: #475569;
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .dropdown-item {
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #475569;
            color: #f8fafc;
        }

        [data-bs-theme="dark"] .text-muted {
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .text-dark {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .border {
            border-color: #475569 !important;
        }

        [data-bs-theme="dark"] .shadow {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.6) !important;
        }

        [data-bs-theme="dark"] .shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.4) !important;
        }

        [data-bs-theme="dark"] .alert-info {
            background-color: #1e3a8a;
            border-color: #3b82f6;
            color: #e0f2fe;
        }

        [data-bs-theme="dark"] .alert-success {
            background-color: #166534;
            border-color: #22c55e;
            color: #f0fdf4;
        }

        [data-bs-theme="dark"] .alert-warning {
            background-color: #92400e;
            border-color: #f59e0b;
            color: #fffbeb;
        }

        [data-bs-theme="dark"] .alert-danger {
            background-color: #991b1b;
            border-color: #ef4444;
            color: #fef2f2;
        }

        /* Fixed header dark mode */
        [data-bs-theme="dark"] .fixed-header {
            background-color: #1e293b !important;
            border-bottom: 1px solid #475569;
        }

        /* Alternative dark theme using body class for better compatibility */
        body.dark-theme {
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }

        body.dark-theme .bg-light {
            background-color: #0f172a !important;
        }

        body.dark-theme .bg-white {
            background-color: #1e293b !important;
            color: #f8fafc !important;
        }

        body.dark-theme .navbar {
            background-color: #1e293b !important;
            border-bottom: 1px solid #475569;
        }

        body.dark-theme .card {
            background-color: #1e293b !important;
            border-color: #475569 !important;
            color: #f8fafc !important;
        }

        body.dark-theme .table {
            --bs-table-bg: #1e293b;
            --bs-table-color: #f8fafc;
            --bs-table-border-color: #475569;
            background-color: #1e293b !important;
            color: #f8fafc !important;
        }

        body.dark-theme .table th,
        body.dark-theme .table td {
            border-color: #475569 !important;
            color: #f8fafc !important;
        }

        body.dark-theme .form-control {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #f8fafc !important;
        }

        body.dark-theme .form-select {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #f8fafc !important;
        }

        body.dark-theme .dropdown-menu {
            background-color: #334155 !important;
            border-color: #475569 !important;
        }

        body.dark-theme .dropdown-item {
            color: #ffffff !important;
        }

        body.dark-theme .dropdown-item:hover {
            background-color: #475569 !important;
        }

        body.dark-theme .nav-link {
            color: #ffffff !important;
        }

        body.dark-theme .nav-link:hover {
            color: #f8fafc !important;
        }

        body.dark-theme .text-dark {
            color: #f8fafc !important;
        }

        body.dark-theme .text-muted {
            color: #cbd5e1 !important;
        }

        body.dark-theme .border {
            border-color: #475569 !important;
        }

        /* Additional dark theme styles for common elements */
        body.dark-theme .btn-primary {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        body.dark-theme .btn-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
        }

        body.dark-theme .btn-success {
            background-color: #198754 !important;
            border-color: #198754 !important;
        }

        body.dark-theme .btn-danger {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }

        body.dark-theme .btn-warning {
            background-color: #fd7e14 !important;
            border-color: #fd7e14 !important;
            color: #000 !important;
        }

        body.dark-theme .btn-info {
            background-color: #0dcaf0 !important;
            border-color: #0dcaf0 !important;
            color: #000 !important;
        }

        body.dark-theme .btn-light {
            background-color: #f8f9fa !important;
            border-color: #f8f9fa !important;
            color: #000 !important;
        }

        body.dark-theme .btn-dark {
            background-color: #212529 !important;
            border-color: #212529 !important;
        }

        body.dark-theme .btn-outline-primary {
            color: #66b3ff !important;
            border-color: #66b3ff !important;
        }

        body.dark-theme .btn-outline-primary:hover {
            background-color: #66b3ff !important;
            color: #000 !important;
        }

        body.dark-theme .btn-outline-secondary {
            color: #adb5bd !important;
            border-color: #6c757d !important;
        }

        body.dark-theme .btn-outline-secondary:hover {
            background-color: #6c757d !important;
            color: #fff !important;
        }

        body.dark-theme .btn-outline-danger {
            color: #ff6b6b !important;
            border-color: #ff6b6b !important;
        }

        body.dark-theme .btn-outline-danger:hover {
            background-color: #ff6b6b !important;
            color: #fff !important;
        }

        body.dark-theme .form-control:focus {
            background-color: #334155 !important;
            border-color: #3b82f6 !important;
            color: #f8fafc !important;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4) !important;
        }

        body.dark-theme .form-select:focus {
            background-color: #334155 !important;
            border-color: #3b82f6 !important;
            color: #f8fafc !important;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4) !important;
        }

        body.dark-theme .input-group-text {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #f8fafc !important;
        }

        body.dark-theme .list-group-item {
            background-color: #1e293b !important;
            border-color: #475569 !important;
            color: #f8fafc !important;
        }

        body.dark-theme .list-group-item:hover {
            background-color: #334155 !important;
        }

        body.dark-theme .modal-content {
            background-color: #1e293b !important;
            border-color: #475569 !important;
            color: #f8fafc !important;
        }

        body.dark-theme .modal-header {
            border-bottom-color: #475569 !important;
        }

        body.dark-theme .modal-footer {
            border-top-color: #475569 !important;
        }

        body.dark-theme .pagination .page-link {
            background-color: #1e293b !important;
            border-color: #475569 !important;
            color: #f8fafc !important;
        }

        body.dark-theme .pagination .page-link:hover {
            background-color: #334155 !important;
            border-color: #cbd5e1 !important;
            color: #f8fafc !important;
        }

        body.dark-theme .pagination .page-item.active .page-link {
            background-color: #007bff !important;
            border-color: #007bff !important;
        }

        body.dark-theme .breadcrumb {
            background-color: #1e293b !important;
        }

        body.dark-theme .breadcrumb-item a {
            color: #66b3ff !important;
        }

        body.dark-theme .breadcrumb-item.active {
            color: #cbd5e1 !important;
        }

        body.dark-theme .badge {
            background-color: #475569 !important;
            color: #f8fafc !important;
        }

        body.dark-theme .badge.bg-primary {
            background-color: #0d6efd !important;
        }

        body.dark-theme .badge.bg-secondary {
            background-color: #6c757d !important;
        }

        body.dark-theme .badge.bg-success {
            background-color: #198754 !important;
        }

        body.dark-theme .badge.bg-danger {
            background-color: #dc3545 !important;
        }

        body.dark-theme .badge.bg-warning {
            background-color: #fd7e14 !important;
            color: #000 !important;
        }

        body.dark-theme .badge.bg-info {
            background-color: #0dcaf0 !important;
            color: #000 !important;
        }

        body.dark-theme .badge.bg-light {
            background-color: #f8f9fa !important;
            color: #000 !important;
        }

        body.dark-theme .badge.bg-dark {
            background-color: #212529 !important;
        }

        body.dark-theme .alert {
            border-color: #475569 !important;
        }

        body.dark-theme .alert-primary {
            background-color: #1e3a8a !important;
            border-color: #3b82f6 !important;
            color: #e0f2fe !important;
        }

        body.dark-theme .alert-secondary {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #cbd5e1 !important;
        }

        body.dark-theme .alert-success {
            background-color: #166534 !important;
            border-color: #22c55e !important;
            color: #f0fdf4 !important;
        }

        body.dark-theme .alert-danger {
            background-color: #991b1b !important;
            border-color: #ef4444 !important;
            color: #fef2f2 !important;
        }

        body.dark-theme .alert-warning {
            background-color: #92400e !important;
            border-color: #f59e0b !important;
            color: #fffbeb !important;
        }

        body.dark-theme .alert-info {
            background-color: #1e3a8a !important;
            border-color: #3b82f6 !important;
            color: #e0f2fe !important;
        }

        body.dark-theme .alert-light {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #cbd5e1 !important;
        }

        body.dark-theme .alert-dark {
            background-color: #1a1a1a !important;
            border-color: #333 !important;
            color: #f8fafc !important;
        }

        /* Header and main content */
        body.dark-theme header {
            background-color: #1e293b !important;
            color: #f8fafc !important;
        }

        body.dark-theme main {
            background-color: #0f172a !important;
        }

        body.dark-theme .container-fluid {
            background-color: transparent !important;
        }

        /* Notifications Badge Animation */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        #notification-count {
            animation: pulse 2s infinite;
        }
        /* Estilos para el header fijo en app.blade.php */
.app-content {
    padding-top: 65px;
}

.fixed-header {
    position: fixed;
    top: 65px; /* Altura del navbar */
    left: 0;
    right: 0;
    z-index: 1040;
    background-color: #fff;
}

.header-content {
    margin: 0;
    border-radius: 0;
}

.content-wrapper {
    padding-top: 60px; /* Altura del header fijo */
}

@media (max-width: 767px) {
    .fixed-header {
        top: 56px; /* Altura del navbar en móviles */
    }
}

    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-vh-100 bg-light">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="container-fluid py-6 px-4">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="container-fluid py-4">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
    
    @stack('scripts')

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar dropdowns
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl)
        });

        function checkNewNotifications() {
            $.get('/check-notifications', function(data) {
                const $count = $('#notification-count, #notification-count-mobile');
                $count.text(data.count > 99 ? '99+' : data.count);
                $count.toggle(data.count > 0);
                
                $.get('/get-notifications', function(notificationsHtml) {
                    $('.notifications-content, .notifications-content-mobile').html(notificationsHtml);
                });
            });
        }

        checkNewNotifications();
        setInterval(checkNewNotifications, 300000); // 5 minutos

        $(document).on('click', '.notification-item a', function(e) {
            e.preventDefault();
            var notificationId = $(this).closest('.notification-item').data('notification-id');
            var link = $(this).attr('href');

            $.ajax({
                url: '/notifications/' + notificationId + '/mark-as-read',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.success) {
                        window.location.href = link;
                    }
                }
            });
        });

        // El sistema de tema está manejado completamente por navigation.blade.php
        // No duplicar funcionalidad aquí para evitar conflictos
    });
</script>
</body>
</html>