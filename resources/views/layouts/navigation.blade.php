<!-- Script para aplicar tema inmediatamente -->
<script>
(function() {
    // Aplicar tema inmediatamente sin esperar DOMContentLoaded
    const savedTheme = localStorage.getItem('app-theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
})();
</script>

<!-- Navbar -->
<nav class="navbar navbar-expand-xl navbar-light bg-white shadow-sm">
    <div class="container-fluid px-3 px-xl-5">
        <!-- Logo (siempre visible) -->
        <a class="navbar-brand py-2 logo-container" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="logo-img logo-light" id="navbar-logo-light" style="height: 55px; width: auto;">
            <img src="{{ asset('assets/logo-white.png') }}" alt="Logo" class="logo-img logo-dark" id="navbar-logo-dark" style="height: 55px; width: auto; display: none;">
        </a>
        
        <!-- Script inmediato para configurar logos -->
        <script>
        (function() {
            const savedTheme = localStorage.getItem('app-theme') || 'light';
            const logoLight = document.getElementById('navbar-logo-light');
            const logoDark = document.getElementById('navbar-logo-dark');
            
            if (logoLight && logoDark && savedTheme === 'dark') {
                logoLight.style.display = 'none';
                logoDark.style.display = 'block';
            }
        })();
        </script>
            
        <!-- Toggler for mobile -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar content for desktop -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Navigation Links -->
            <ul class="navbar-nav mb-2 mb-xl-0 align-items-center">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link px-3 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home me-2"></i><span>{{ __('Inicio') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('inventarios.index') }}" class="nav-link px-3 {{ request()->routeIs('inventarios.*') ? 'active' : '' }}">
                        <i class="fas fa-box me-2"></i><span>{{ __('Inventario') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('movimientos.index') }}" class="nav-link px-3 {{ request()->routeIs('movimientos.*') ? 'active' : '' }}">
                        <i class="fas fa-exchange-alt me-2"></i><span>{{ __('Movimientos') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mantenimientos.index') }}" class="nav-link px-3 {{ request()->routeIs('mantenimientos.*') ? 'active' : '' }}">
                        <i class="fas fa-tools me-2"></i><span>{{ __('Mantenimientos') }}</span>
                    </a>
                </li>
                @if(auth()->user()->role->name === 'administrador')
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-cog me-2"></i><span>{{ __('Administración') }}</span>
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item py-2" href="{{ route('categorias.index') }}"><i class="fas fa-tags me-2"></i>{{ __('Categorías') }}</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('proveedores.index') }}"><i class="fas fa-truck me-2"></i>{{ __('Proveedores') }}</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('ubicaciones.index') }}"><i class="fas fa-map-marker-alt me-2"></i>{{ __('Ubicaciones') }}</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('empleados.index') }}"><i class="fas fa-users me-2"></i>{{ __('Empleados') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.users.index') }}"><i class="fas fa-user-cog me-2"></i>{{ __('Gestión de Usuarios') }}</a></li>
                    </ul>
                </li>
                @endif
            </ul>

            <!-- Right Side Navigation -->
            <ul class="navbar-nav align-items-center ms-auto">
                <!-- Notifications -->
                <li class="nav-item dropdown px-2">
                    <a class="nav-link position-relative notification-icon p-0" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span id="notification-count" class="position-absolute translate-middle badge rounded-pill bg-danger" style="display: none;">
                            0
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm notifications-dropdown" aria-labelledby="notificationsDropdown">
                        <li><h6 class="dropdown-header">Notificaciones</h6></li>
                        <div class="notifications-content">
                            <!-- El contenido se cargará dinámicamente aquí -->
                        </div>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li><button id="markAllAsReadDesktop" class="dropdown-item text-center py-2">Marcar todas como leídas</button></li>
                    </ul>
                </li>
                <!-- User Menu -->
                <li class="nav-item dropdown px-2">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-2"></i><span>{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2"></i>{{ __('Perfil') }}</a></li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li>
                            <button class="dropdown-item py-2 theme-toggle-btn" id="themeToggle" title="Cambiar tema" style="font-size: inherit; line-height: inherit;">
                                <i class="fas fa-sun me-2" id="theme-icon-desktop"></i>
                                <span id="theme-text-desktop">Tema claro</span>
                            </button>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="dropdown-item py-2" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>{{ __('Cerrar Sesión') }}
                                </a>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- Offcanvas menu for mobile -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
             aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menú</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <!-- User Info -->
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div class="flex-grow-1">
                        <a href="{{ route('profile.edit') }}" class="text-decoration-none text-dark">
                            <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                            <small class="text-muted">{{ Auth::user()->email }}</small>
                        </a>
                    </div>
                </div>
                <!-- Navigation Links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home me-3"></i>{{ __('Inicio') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('inventarios.index') }}" class="nav-link {{ request()->routeIs('inventarios.*') ? 'active' : '' }}">
                            <i class="fas fa-box me-3"></i>{{ __('Inventario') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('movimientos.index') }}" class="nav-link {{ request()->routeIs('movimientos.*') ? 'active' : '' }}">
                            <i class="fas fa-exchange-alt me-3"></i>{{ __('Movimientos') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('mantenimientos.index') }}" class="nav-link {{ request()->routeIs('mantenimientos.*') ? 'active' : '' }}">
                            <i class="fas fa-tools me-3"></i>{{ __('Mantenimientos') }}
                        </a>
                    </li>
                    @if(auth()->user()->role->name === 'administrador')
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle" href="#adminSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="adminSubmenu">
                            <i class="fas fa-cog me-3"></i>{{ __('Administración') }}
                        </a>
                        <div class="collapse" id="adminSubmenu">
                            <ul class="navbar-nav ms-3 mt-2">
                                <li><a class="nav-link py-2" href="{{ route('categorias.index') }}"><i class="fas fa-tags me-2"></i>{{ __('Categorías') }}</a></li>
                                <li><a class="nav-link py-2" href="{{ route('proveedores.index') }}"><i class="fas fa-truck me-2"></i>{{ __('Proveedores') }}</a></li>
                                <li><a class="nav-link py-2" href="{{ route('ubicaciones.index') }}"><i class="fas fa-map-marker-alt me-2"></i>{{ __('Ubicaciones') }}</a></li>
                                <li><a class="nav-link py-2" href="{{ route('empleados.index') }}"><i class="fas fa-users me-2"></i>{{ __('Empleados') }}</a></li>
                                <li><a class="nav-link py-2" href="{{ route('admin.users.index') }}"><i class="fas fa-user-cog me-2"></i>{{ __('Gestión de Usuarios') }}</a></li>
                            </ul>
                        </div>
                    </li>
                    @endif
                </ul>

                <hr>

                <!-- Theme Toggle -->
                <button class="d-flex align-items-center mb-3 btn btn-link text-decoration-none text-dark p-0 w-100 text-start theme-toggle-btn-mobile" id="themeToggleMobile">
                    <i class="fas fa-sun me-3" id="theme-icon-mobile"></i>
                    <span id="theme-text-mobile">Tema claro</span>
                </button>

                <!-- Notifications -->
                <a href="#" class="d-flex align-items-center mb-3 text-decoration-none text-dark" id="openNotificationsPanel">
                    <i class="fas fa-bell me-3"></i>
                    <span>Notificaciones</span>
                    <span id="notification-count-mobile" class="badge bg-danger rounded-pill ms-auto" style="display: none;">
                        0
                    </span>
                </a>
                
                <!-- Panel deslizable para notificaciones -->
                <div id="notificationsPanel" class="notifications-panel">
                    <div class="notifications-panel-header">
                        <h5>Notificaciones</h5>
                        <button id="closeNotificationsPanel" class="btn-close" aria-label="Close"></button>
                    </div>
                    <div class="notifications-panel-body">
                        <div class="notifications-content-mobile">
                            <!-- El contenido de las notificaciones se cargará aquí dinámicamente -->
                        </div>
                    </div>
                    <div class="notifications-panel-footer">
                        <button id="markAllAsRead" class="btn btn-primary w-100">Marcar todas como leídas</button>
                    </div>
                </div>
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="d-flex align-items-center text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt me-3"></i>{{ __('Cerrar Sesión') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Estilos -->
<style>
    .navbar {
        position: relative;
        height: 65px;
        overflow: visible;
        padding-top: 0;
        padding-bottom: 0;
    }
        
    .navbar-brand {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        margin-right: auto;
        margin-left: auto;
    }

    .navbar-collapse {
        justify-content: flex-start;
    }
    .nav-link {
        color: #333;
        font-weight: 500;
        transition: color 0.3s ease;
        padding: 0.5rem 1rem;
    }

    .nav-link:hover, .nav-link.active {
        color: #007bff;
    }

    .navbar-nav {
        margin-top: 8px;
    }

    .navbar-nav.align-items-center {
        margin-top: 20px;
        margin-left: auto;
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
    }

    .dropdown-item {
        color: #333;
        transition: background-color 0.3s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    #themeToggle, #themeToggleMobile {
        font-size: 1.2rem;
        color: #6c757d;
        transition: color 0.3s ease;
    }

    #themeToggle:hover, #themeToggleMobile:hover {
        color: #007bff;
    }

    .notifications-dropdown {
        width: 300px;
        max-height: 400px;
        overflow-y: auto;
    }

    .notifications-content .dropdown-item,
    .notifications-content-mobile .dropdown-item {
        white-space: normal;
    }

    .notification-icon {
        font-size: 1.2rem;
        line-height: 1;
    }

    #notification-count, #notification-count-mobile {
        top: 0px;
        right: -16px;
        font-size: 0.65rem;
        padding: 0.25em 0.4em;
        min-width: 1.5em;
        height: 1.5em;
        line-height: 1.1;
        border-radius: 50%;
    }
    

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    #notification-count:not(:empty), #notification-count-mobile:not(:empty) {
        animation: pulse 2s infinite;
    }

    /* Media queries ajustadas para el cambio a vista móvil en 1213px */
    @media (min-width: 1213px) {
        .navbar-expand-xl {
            flex-wrap: nowrap;
            justify-content: flex-start;
        }
        .navbar-expand-xl .navbar-toggler {
            display: none;
        }
        .navbar-expand-xl .navbar-collapse {
            display: flex !important;
            flex-basis: auto;
        }
        .offcanvas {
            display: none;
        }
    }

    @media (max-width: 1212.98px) {
        .navbar-expand-xl .navbar-collapse {
            display: none !important;
        }
        .navbar-expand-xl .navbar-toggler {
            display: block;
        }
        .navbar-brand {
            margin-left: 0;
        }
        .offcanvas {
            display: block;
        }
    }

    /* Ajustes adicionales para la vista móvil */
    @media (max-width: 1212.98px) {
        .navbar-nav {
            padding-top: 0.5rem;
        }
        .nav-item {
            padding: 0.25rem 0;
        }
        .navbar-nav .nav-link {
            padding: 0.5rem 1rem;
        }
        .navbar-brand img {
            height: 35px;
        }
    }

    @media (max-width: 575.98px) {
        .navbar-brand img {
            height: 30px;
        }
    }

    .logo-container {
        position: relative;
        margin-right: 20px;
        top: -5px;
    }

    .navbar-collapse {
        justify-content: flex-start;
        margin-left: 0;
    }

    /* Estilos para el offcanvas */
    .offcanvas {
        width: 250px;
    }

    .offcanvas-header {
        border-bottom: 1px solid #dee2e6;
    }

    .offcanvas-body .nav-link {
        padding: 0.75rem 1.25rem;
        color: #333;
    }

    .offcanvas-body .nav-link:hover, .offcanvas-body .nav-link.active {
        background-color: #f8f9fa;
        color: #007bff;
    }

    .offcanvas-body .navbar-nav {
        margin-top: 0;
    }
    /* Estilos para móvil */
    @media (max-width: 1212.98px) {
        .offcanvas {
            width: 280px;
        }

        .offcanvas-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .offcanvas-body .nav-link {
            padding: 0.75rem 0;
            font-size: 1rem;
            color: #333;
            transition: all 0.3s ease;
        }

        .offcanvas-body .nav-link:hover,
        .offcanvas-body .nav-link.active {
            color: #007bff;
            background-color: rgba(0, 123, 255, 0.1);
            padding-left: 0.5rem;
        }

        .offcanvas-body .navbar-nav {
            margin-top: 0;
        }

        #adminSubmenu .nav-link {
            font-size: 0.9rem;
            padding: 0.5rem 0;
        }

        .form-check-input {
            cursor: pointer;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.25em 0.6em;
        }
    }

    /* Animación para el toggle del tema */
    .form-check-input {
        transition: background-position 0.15s ease-in-out;
    }

    /* Logo transitions */
    .logo-img {
        transition: opacity 0.2s ease-in-out;
    }

    .logo-container {
        position: relative;
        display: inline-block;
    }
    /* Estilos para el panel de notificaciones */
    .notifications-panel {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #fff;
        height: 80vh;
        transform: translateY(100%);
        transition: transform 0.3s ease-out;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    }

    .notifications-panel.active {
        transform: translateY(0);
    }

    .notifications-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .notifications-panel-body {
        flex-grow: 1;
        overflow-y: auto;
        padding: 1rem;
    }

    .notifications-panel-footer {
        padding: 1rem;
        border-top: 1px solid #e9ecef;
    }

    .notifications-content-mobile .dropdown-item {
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 0;
    }

    .notifications-content-mobile .dropdown-item:last-child {
        border-bottom: none;
    }

    /* Overlay para el fondo cuando el panel está abierto */
    .notifications-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.5);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease-out, visibility 0.3s ease-out;
    }

    .notifications-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    /* Theme Toggle Styles */
    .theme-toggle-btn {
        border: none !important;
        background: none !important;
        color: #333 !important;
        font-size: inherit !important;
        line-height: inherit !important;
        font-weight: inherit !important;
        transition: all 0.15s ease;
        width: 100% !important;
        text-align: left !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        padding: 0.5rem 1rem !important;
    }

    .theme-toggle-btn:hover {
        color: #333 !important;
        background-color: #f8f9fa !important;
    }

    .theme-toggle-btn:focus {
        box-shadow: none !important;
        outline: none !important;
        background-color: #f8f9fa !important;
    }

    .theme-toggle-btn i {
        font-size: inherit !important;
        width: auto !important;
    }

    /* Dark theme button styles */
    [data-bs-theme="dark"] .theme-toggle-btn {
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .theme-toggle-btn:hover {
        color: #f8fafc !important;
        background-color: #475569 !important;
    }

    [data-bs-theme="dark"] .theme-toggle-btn:focus {
        background-color: #475569 !important;
    }

    /* Mobile theme toggle styles */
    .theme-toggle-btn-mobile {
        color: #333 !important;
        transition: all 0.15s ease;
        text-decoration: none !important;
    }

    .theme-toggle-btn-mobile:hover {
        color: #333 !important;
        background-color: rgba(0,0,0,0.05) !important;
    }

    [data-bs-theme="dark"] .theme-toggle-btn-mobile {
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .theme-toggle-btn-mobile:hover {
        color: #f8fafc !important;
        background-color: rgba(255,255,255,0.1) !important;
    }

    /* Dark Theme Styles - Contraste Mejorado */
    [data-bs-theme="dark"] .navbar {
        background-color: #1e293b !important;
        border-bottom: 1px solid #475569;
    }

    [data-bs-theme="dark"] .navbar-brand,
    [data-bs-theme="dark"] .nav-link {
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .nav-link:hover,
    [data-bs-theme="dark"] .nav-link:focus {
        color: #e2e8f0 !important;
    }

    [data-bs-theme="dark"] .navbar-toggler {
        border-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .navbar-toggler:focus {
        box-shadow: 0 0 0 0.25rem rgba(248, 250, 252, 0.25);
    }

    [data-bs-theme="dark"] .dropdown-menu {
        background-color: #334155;
        border-color: #475569;
    }

    [data-bs-theme="dark"] .dropdown-item {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .dropdown-item:hover,
    [data-bs-theme="dark"] .dropdown-item:focus {
        background-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .dropdown-divider {
        border-color: #475569;
    }

    [data-bs-theme="dark"] .dropdown-header {
        color: #cbd5e1;
    }

    [data-bs-theme="dark"] .offcanvas {
        background-color: #1e293b;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .offcanvas-header {
        border-bottom: 1px solid #475569;
    }

    [data-bs-theme="dark"] .offcanvas-title {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    [data-bs-theme="dark"] .offcanvas-body .nav-link {
        color: #f8fafc;
        border-radius: 0.375rem;
        margin-bottom: 0.25rem;
    }

    [data-bs-theme="dark"] .offcanvas-body .nav-link:hover {
        background-color: #334155;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .offcanvas-body .nav-link.active {
        background-color: #3b82f6;
        color: #ffffff;
    }

    /* Notification dropdown */
    [data-bs-theme="dark"] .notification-dropdown {
        background-color: #334155 !important;
        border-color: #475569 !important;
        max-height: 400px;
        overflow-y: auto;
    }

    [data-bs-theme="dark"] .notification-item {
        border-bottom: 1px solid #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .notification-item:hover {
        background-color: #475569;
    }

    [data-bs-theme="dark"] .notification-item:last-child {
        border-bottom: none;
    }

    [data-bs-theme="dark"] .notification-title {
        color: #f8fafc;
        font-weight: 600;
    }

    [data-bs-theme="dark"] .notification-time {
        color: #cbd5e1;
        font-size: 0.75rem;
    }

    [data-bs-theme="dark"] .notification-body {
        color: #e2e8f0;
    }

    [data-bs-theme="dark"] .badge {
        background-color: #dc2626 !important;
        color: #ffffff !important;
    }



    /* Active navigation states */
    [data-bs-theme="dark"] .nav-link.active {
        color: #60a5fa !important;
        font-weight: 600;
    }

    [data-bs-theme="dark"] .dropdown-item.active {
        background-color: #3b82f6;
        color: #ffffff;
    }

    /* Additional dark theme styles for navigation */
    body.dark-theme .navbar-brand {
        color: #ffffff !important;
    }

    body.dark-theme .navbar-toggler {
        border-color: #404040 !important;
    }

    body.dark-theme .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.85%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
    }

    body.dark-theme .offcanvas-header {
        border-bottom-color: #404040 !important;
    }

    body.dark-theme .offcanvas-body {
        background-color: #1e1e1e !important;
        color: #e0e0e0 !important;
    }

    body.dark-theme .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%) !important;
    }

    body.dark-theme .nav-link.active {
        color: #66b3ff !important;
        background-color: rgba(102, 179, 255, 0.1) !important;
    }

    body.dark-theme .dropdown-toggle::after {
        border-top-color: #e0e0e0 !important;
    }

    body.dark-theme .dropdown-divider {
        border-color: #404040 !important;
    }

    body.dark-theme .dropdown-header {
        color: #adb5bd !important;
    }

    body.dark-theme .theme-toggle-btn-mobile {
        color: #ffffff !important;
    }

    body.dark-theme .theme-toggle-btn-mobile:hover {
        color: #e0e0e0 !important;
    }

    body.dark-theme .notifications-panel {
        background-color: #1e1e1e !important;
        border-color: #404040 !important;
        color: #e0e0e0 !important;
    }

    body.dark-theme .notifications-panel-header {
        border-bottom-color: #404040 !important;
        background-color: #1e1e1e !important;
    }

    body.dark-theme .notifications-panel-body {
        background-color: #1e1e1e !important;
    }

    body.dark-theme .notifications-panel-footer {
        border-top-color: #404040 !important;
        background-color: #1e1e1e !important;
    }

    body.dark-theme .notifications-dropdown {
        background-color: #2d2d2d !important;
        border-color: #404040 !important;
    }

    body.dark-theme .notification-icon {
        color: #e0e0e0 !important;
    }

    body.dark-theme .notification-icon:hover {
        color: #66b3ff !important;
    }

    body.dark-theme .badge.bg-danger {
        background-color: #dc3545 !important;
    }
    
    
</style>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const NOTIFICATION_UPDATE_INTERVAL = 300000; // 5 minutos
    let lastNotificationCheck = 0;
    let notificationCount = 0;

    // Inicializar todos los dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
    dropdownElementList.forEach(function (dropdownToggleEl) {
        new bootstrap.Dropdown(dropdownToggleEl);
    });

    // Notifications
    function updateNotificationBadge(count) {
        const $countDesktop = $('#notification-count');
        const $countMobile = $('#notification-count-mobile');
        if (count > 0) {
            $countDesktop.text(count > 99 ? '99+' : count).show();
            $countMobile.text(count > 99 ? '99+' : count).show();
        } else {
            $countDesktop.hide();
            $countMobile.hide();
        }
    }

    function checkNewNotifications() {
        const now = Date.now();
        if (now - lastNotificationCheck < NOTIFICATION_UPDATE_INTERVAL) {
            return Promise.resolve(notificationCount);
        }

        return $.get('/check-notifications')
            .then(function(data) {
                lastNotificationCheck = now;
                notificationCount = data.count;
                updateNotificationBadge(notificationCount);
                return notificationCount;
            });
    }

    function getNotifications() {
        return $.get('/get-notifications')
            .then(function(notificationsHtml) {
                $('.notifications-content, .notifications-content-mobile').html(notificationsHtml);
            });
    }

    function updateNotifications() {
        checkNewNotifications()
            .then(function(count) {
                if (count > 0) {
                    return getNotifications();
                } else {
                    $('.notifications-content, .notifications-content-mobile').html('<li><a class="dropdown-item py-2" href="#">No hay notificaciones nuevas</a></li>');
                }
            });
    }

    updateNotifications();
    setInterval(updateNotifications, NOTIFICATION_UPDATE_INTERVAL);

    $(document).on('click', '.notifications-content .dropdown-item, .notifications-content-mobile .dropdown-item', function(e) {
        e.preventDefault();
        var notificationId = $(this).data('notification-id');
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
            },
            error: function(xhr, status, error) {
                console.error('Error marking notification as read:', error);
            }
        });
    });

    // Panel de notificaciones para móvil
    const openPanelBtn = document.getElementById('openNotificationsPanel');
    const closeBtn = document.getElementById('closeNotificationsPanel');
    const panel = document.getElementById('notificationsPanel');
    const body = document.body;

    function openPanel() {
        panel.classList.add('active');
        body.insertAdjacentHTML('beforeend', '<div class="notifications-overlay"></div>');
        setTimeout(() => {
            document.querySelector('.notifications-overlay').classList.add('active');
        }, 10);
        updateNotifications();
    }

    function closePanel() {
        panel.classList.remove('active');
        const overlay = document.querySelector('.notifications-overlay');
        overlay.classList.remove('active');
        setTimeout(() => {
            overlay.remove();
        }, 300);
    }

    openPanelBtn.addEventListener('click', openPanel);
    closeBtn.addEventListener('click', closePanel);

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('notifications-overlay')) {
            closePanel();
        }
    });

    // Marcar todas las notificaciones como leídas
    function markAllAsRead() {
        $.ajax({
            url: '/mark-all-notifications-as-read',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    notificationCount = 0;
                    updateNotificationBadge(0);
                    $('.notifications-content, .notifications-content-mobile').html('<li><a class="dropdown-item py-2" href="#">No hay notificaciones nuevas</a></li>');
                    // Opcional: mostrar un mensaje de éxito
                    alert('Todas las notificaciones han sido marcadas como leídas');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error marking all notifications as read:', error);
                // Opcional: mostrar un mensaje de error
                alert('Hubo un error al marcar las notificaciones como leídas');
            }
        });
    }

    // Asignar la función markAllAsRead a los botones correspondientes
    $('#markAllAsReadDesktop, #markAllAsRead').click(markAllAsRead);

    // Theme Toggle Functionality
    function initThemeToggle() {
        
        const themeToggle = document.getElementById('themeToggle');
        const themeToggleMobile = document.getElementById('themeToggleMobile');
        const html = document.documentElement;

        // Migrar usuarios que tengan la clave antigua 'darkMode'
        const oldDarkMode = localStorage.getItem('darkMode');
        if (oldDarkMode !== null) {
            const migratedTheme = oldDarkMode === 'true' ? 'dark' : 'light';
            localStorage.setItem('app-theme', migratedTheme);
            localStorage.removeItem('darkMode');

        }

        // Get saved theme from localStorage or default to light
        const savedTheme = localStorage.getItem('app-theme') || 'light';
        
        function updateIcons(theme) {
            const desktopIcon = document.getElementById('theme-icon-desktop');
            const desktopText = document.getElementById('theme-text-desktop');
            const mobileIcon = document.getElementById('theme-icon-mobile');
            const mobileText = document.getElementById('theme-text-mobile');
            
            if (theme === 'dark') {
                if (desktopIcon) {
                    desktopIcon.className = 'fas fa-moon me-2';
                }
                if (desktopText) {
                    desktopText.textContent = 'Tema oscuro';
                }
                if (mobileIcon) {
                    mobileIcon.className = 'fas fa-moon me-3';
                }
                if (mobileText) {
                    mobileText.textContent = 'Tema oscuro';
                }
            } else {
                if (desktopIcon) {
                    desktopIcon.className = 'fas fa-sun me-2';
                }
                if (desktopText) {
                    desktopText.textContent = 'Tema claro';
                }
                if (mobileIcon) {
                    mobileIcon.className = 'fas fa-sun me-3';
                }
                if (mobileText) {
                    mobileText.textContent = 'Tema claro';
                }
            }
        }
        
        function setTheme(theme) {

            
            // Cambiar el atributo data-bs-theme en html (Bootstrap 5.3+ estándar)
            html.setAttribute('data-bs-theme', theme);
            
            // Cambiar logo dinámicamente
            const logoLight = document.getElementById('navbar-logo-light');
            const logoDark = document.getElementById('navbar-logo-dark');
            
            if (logoLight && logoDark) {
                if (theme === 'dark') {
                    logoLight.style.display = 'none';
                    logoDark.style.display = 'block';
                } else {
                    logoLight.style.display = 'block';
                    logoDark.style.display = 'none';
                }
            }
            
            // Actualizar iconos
            updateIcons(theme);
            
            // Guardar en localStorage
            localStorage.setItem('app-theme', theme);
            

        }

        function toggleTheme() {
            // Obtener el tema actual del localStorage en lugar del DOM
            const currentTheme = localStorage.getItem('app-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        }

        // Set initial theme
        setTheme(savedTheme);

        // Add event listeners
        if (themeToggle) {
            themeToggle.addEventListener('click', function(e) {
                e.preventDefault();
                toggleTheme();
            });
        }
        
        if (themeToggleMobile) {
            themeToggleMobile.addEventListener('click', function(e) {
                e.preventDefault();
                toggleTheme();
            });
        }
        

    }

    // Initialize theme toggle
    initThemeToggle();
});
</script>