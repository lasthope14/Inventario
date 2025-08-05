<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Hidroobras') }} - Sistema de Gestión de Inventario</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
        }
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            padding: 0.5rem 1rem;
        }
        .navbar-brand img {
            height: 50px;
        }
        .hero {
            background: linear-gradient(rgba(0, 86, 179, 0.5), rgba(0, 168, 232, 0.5)), url('{{ asset('assets/banner.jpg') }}') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 150px 0;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
        }
        .hero-content {
            background: rgba(0, 0, 0, 0.3);
            padding: 30px;
            border-radius: 10px;
        }
        .feature-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            cursor: pointer;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #0056b3;
            background: #e6f2ff;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 1rem;
        }
        .btn-custom {
            background-color: #00a8e8;
            border: none;
            color: white;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .section-title {
            position: relative;
            margin-bottom: 30px;
            padding-bottom: 15px;
            text-align: center;
        }
        .section-title::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            width: 50px;
            height: 2px;
            background-color: #0056b3;
            transform: translateX(-50%);
            transition: all 0.3s ease;
        }
        .feature-card:hover .section-title::after {
            width: 100px;
        }
        footer {
            background-color: #333;
            color: #fff;
            padding: 20px 0;
        }
        .nav-item {
            display: flex;
            align-items: center;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        /* Estilos específicos para móviles */
        @media (max-width: 991px) {
            .navbar-brand img {
                height: 40px;
            }
        
            .navbar-collapse {
                padding: 1rem;
                margin-top: 0.5rem;
            }
        
            .nav-item {
                margin: 0.25rem 0;
            }
        
            .nav-item .btn,
            .nav-item .nav-link {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
                width: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0.25rem 0;
            }
        
            .nav-item.me-2 {
                margin-right: 0 !important;
            }
        
            .btn-outline-primary {
                margin-bottom: 0.5rem;
            }
        
            .hero {
                padding: 80px 0;
            }
        
            .hero-content {
                padding: 20px;
            }
        
            .hero-content h1 {
                font-size: 1.8rem;
            }
        
            .hero-content p {
                font-size: 1rem;
            }
        
            .btn-lg {
                padding: 0.75rem 1.5rem !important;
                font-size: 1rem !important;
            }
        }
        
        /* Ajustes adicionales para pantallas muy pequeñas */
        @media (max-width: 576px) {
            .navbar-brand img {
                height: 35px;
            }
        
            .nav-item .btn,
            .nav-item .nav-link {
                font-size: 0.85rem;
                padding: 0.4rem 0.8rem;
            }
        
            .nav-item {
                margin: 0.15rem 0;
            }
        
            .hero-content h1 {
                font-size: 1.5rem;
            }
        
            .hero-content p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('assets/logo.png') }}" alt="Hidroobras Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="nav-link btn btn-custom text-white">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                        @else
                            <li class="nav-item me-2">
                                <a href="{{ route('login') }}" class="nav-link btn btn-outline-primary">
                                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                                </a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="nav-link btn btn-custom text-white">
                                        <i class="fas fa-user-plus"></i> Registrarse
                                    </a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="hero-content text-center">
                        <h1 class="display-4 fw-bold mb-4">Gestión de Inventario</h1>
                        <p class="lead mb-5">Optimiza la gestión de herramientas, materiales y equipos con nuestra plataforma integral</p>
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-light btn-lg px-5 py-3">
                                <i class="fas fa-tachometer-alt me-2"></i> Acceder al Sistema
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 py-3">
                                <i class="fas fa-rocket me-2"></i> Comenzar Ahora
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <h2 class="section-title mb-5">Características Principales</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card p-4 h-100">
                        <div class="feature-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h3 class="h5 mb-3 text-center">Control de Inventario</h3>
                        <p class="text-muted">Gestiona eficientemente el inventario de herramientas y equipos de Hidroobras con actualizaciones en tiempo real.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card p-4 h-100">
                        <div class="feature-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <h3 class="h5 mb-3 text-center">Seguimiento de Movimientos</h3>
                        <p class="text-muted">Registra y monitorea todos los movimientos de activos dentro de la empresa para una mayor transparencia.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card p-4 h-100">
                        <div class="feature-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h3 class="h5 mb-3 text-center">Mantenimiento Preventivo</h3>
                        <p class="text-muted">Programa y gestiona el mantenimiento de equipos para optimizar su rendimiento y vida útil.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container text-center">
            <p>&copy; {{ date('Y') }} Hidroobras. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>