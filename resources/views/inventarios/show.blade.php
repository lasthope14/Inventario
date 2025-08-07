@extends('layouts.app')

@section('title', 'Detalle del Equipo - ' . $inventario->nombre)

@section('content')
<div class="container-fluid py-4">
    <!-- Contenedor Bootstrap principal -->
    <div class="container-fluid px-0">
        
        <!-- Header Section -->
        <div class="row mb-4 mx-0">
            <div class="col-12 px-0">
                <div class="card">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #007bff; border-radius: 50%; color: white;">
                                    <i class="fas fa-box" style="font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">{{ $inventario->nombre }}</h2>
                                    <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Información detallada del equipo</p>
                                </div>
                            </div>
                            <div>
                                @php
                                    $estadoPrincipal = $ubicaciones->first()->estado ?? 'disponible';
                                    $statusConfig = match($estadoPrincipal) {
                                        'disponible' => ['bg' => '#e8f5e8', 'color' => '#2d5a2d', 'icon' => 'fas fa-check-circle', 'iconColor' => '#28a745', 'text' => 'Disponible'],
                                        'en uso' => ['bg' => '#e3f2fd', 'color' => '#1565c0', 'icon' => 'fas fa-user-clock', 'iconColor' => '#1976d2', 'text' => 'En Uso'],
                                        'en mantenimiento' => ['bg' => '#fff8e1', 'color' => '#e65100', 'icon' => 'fas fa-tools', 'iconColor' => '#ff9800', 'text' => 'Mantenimiento'],
                                        'dado de baja' => ['bg' => '#ffebee', 'color' => '#c62828', 'icon' => 'fas fa-times-circle', 'iconColor' => '#d32f2f', 'text' => 'Dado de Baja'],
                                        'robado' => ['bg' => '#fafafa', 'color' => '#424242', 'icon' => 'fas fa-exclamation-triangle', 'iconColor' => '#757575', 'text' => 'Robado'],
                                        default => ['bg' => '#f5f5f5', 'color' => '#424242', 'icon' => 'fas fa-question-circle', 'iconColor' => '#757575', 'text' => ucfirst(str_replace('_', ' ', $estadoPrincipal))]
                                    };
                                @endphp
                                <span class="badge px-3 py-2 d-flex align-items-center status-badge" style="background-color: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['color'] }}; font-size: 0.9rem; gap: 0.5rem; border: 1px solid {{ $statusConfig['iconColor'] }}; font-weight: 600;">
                                    <i class="{{ $statusConfig['icon'] }}" style="font-size: 1rem; color: {{ $statusConfig['iconColor'] }};"></i>
                                    {{ $statusConfig['text'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <style>
                            /* Estilos para tema oscuro */
                            [data-bs-theme="dark"] .card {
                                background-color: #1e293b;
                                border-color: #475569;
                                color: #f8fafc;
                            }
                            
                            [data-bs-theme="dark"] .card-header {
                                background-color: #334155 !important;
                                border-color: #475569;
                                color: #f8fafc;
                            }
                            
                            [data-bs-theme="dark"] .status-badge {
                                background-color: rgba(255, 255, 255, 0.1) !important;
                                color: #f8fafc !important;
                                border-color: rgba(255, 255, 255, 0.2) !important;
                            }
                            
                            /* Estados específicos para tema oscuro */
                            [data-bs-theme="dark"] .status-badge:has(.fa-check-circle) {
                                background-color: rgba(34, 197, 94, 0.2) !important;
                                color: #86efac !important;
                                border-color: #22c55e !important;
                            }
                            
                            [data-bs-theme="dark"] .status-badge:has(.fa-user-clock) {
                                background-color: rgba(59, 130, 246, 0.2) !important;
                                color: #93c5fd !important;
                                border-color: #3b82f6 !important;
                            }
                            
                            [data-bs-theme="dark"] .status-badge:has(.fa-tools) {
                                background-color: rgba(245, 158, 11, 0.2) !important;
                                color: #fbbf24 !important;
                                border-color: #f59e0b !important;
                            }
                            
                            [data-bs-theme="dark"] .status-badge:has(.fa-times-circle) {
                                background-color: rgba(239, 68, 68, 0.2) !important;
                                color: #fca5a5 !important;
                                border-color: #ef4444 !important;
                            }
                            
                            [data-bs-theme="dark"] .status-badge:has(.fa-exclamation-triangle) {
                                background-color: rgba(156, 163, 175, 0.2) !important;
                                color: #d1d5db !important;
                                border-color: #9ca3af !important;
                            }
                            
                            /* Información cards para tema oscuro */
                            [data-bs-theme="dark"] .info-card {
                                background-color: #334155 !important;
                                border-color: #475569 !important;
                                color: #f8fafc !important;
                            }
                            
                            [data-bs-theme="dark"] .info-card small {
                                color: #94a3b8 !important;
                            }
                            
                            [data-bs-theme="dark"] .info-card .info-value {
                                color: #f8fafc !important;
                            }
                            
                            /* Botones para tema oscuro */
                            [data-bs-theme="dark"] .btn-primary {
                                background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                                border-color: #3b82f6;
                            }
                            
                            [data-bs-theme="dark"] .btn-primary:hover {
                                background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
                                border-color: #1d4ed8;
                            }
                            
                            [data-bs-theme="dark"] .btn-warning {
                                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                                border-color: #f59e0b;
                                color: #1f2937;
                            }
                            
                            [data-bs-theme="dark"] .btn-warning:hover {
                                background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
                                border-color: #d97706;
                                color: #1f2937;
                            }
                            
                            [data-bs-theme="dark"] .btn-outline-secondary {
                                background-color: #374151;
                                border-color: #6b7280;
                                color: #f9fafb;
                            }
                            
                            [data-bs-theme="dark"] .btn-outline-secondary:hover {
                                background-color: #4b5563;
                                border-color: #9ca3af;
                                color: #f9fafb;
                            }
                            
                            /* Imagen container para tema oscuro */
                            [data-bs-theme="dark"] .image-container {
                                background-color: #374151 !important;
                                border-color: #6b7280 !important;
                            }
                            
                            [data-bs-theme="dark"] .no-image-placeholder {
                                background-color: #374151 !important;
                                border-color: #6b7280 !important;
                            }
                            
                            [data-bs-theme="dark"] .no-image-placeholder i {
                                color: #9ca3af !important;
                            }
                            
                            [data-bs-theme="dark"] .no-image-placeholder h4 {
                                color: #d1d5db !important;
                            }
                            
                            [data-bs-theme="dark"] .no-image-placeholder p {
                                color: #9ca3af !important;
                            }
                        </style>
                        <div class="row">
                            <!-- Information Section (50%) -->
                            <div class="col-lg-6 col-md-6">
                                <!-- Equipment Details -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 info-card" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;">
                                            <div class="me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; background-color: #007bff; border-radius: 50%; color: white;">
                                                <i class="fas fa-barcode" style="font-size: 0.9rem;"></i>
                                            </div>
                                            <div>
                                                <small style="color: #6c757d; font-size: 0.8rem;">Código</small>
                                                <div class="info-value" style="color: #212529; font-size: 0.9rem; font-weight: 600;">{{ $inventario->codigo_unico ?? $inventario->codigo }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 info-card" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;">
                                            <div class="me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; background-color: #28a745; border-radius: 50%; color: white;">
                                                <i class="fas fa-layer-group" style="font-size: 0.9rem;"></i>
                                            </div>
                                            <div>
                                                <small style="color: #6c757d; font-size: 0.8rem;">Categoría</small>
                                                <div class="info-value" style="color: #212529; font-size: 0.9rem; font-weight: 600;">{{ $inventario->categoria->nombre ?? 'Sin categoría' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 info-card" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;">
                                            <div class="me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; background-color: #17a2b8; border-radius: 50%; color: white;">
                                                <i class="fas fa-map-marker-alt" style="font-size: 0.9rem;"></i>
                                            </div>
                                            <div>
                                                <small style="color: #6c757d; font-size: 0.8rem;">Ubicación</small>
                                                <div class="info-value" style="color: #212529; font-size: 0.9rem; font-weight: 600;">{{ $ubicaciones->first()->ubicacion->nombre ?? 'Sin ubicación' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 info-card" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;">
                                            <div class="me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; background-color: #ffc107; border-radius: 50%; color: #212529;">
                                                <i class="fas fa-dollar-sign" style="font-size: 0.9rem;"></i>
                                            </div>
                                            <div>
                                                <small style="color: #6c757d; font-size: 0.8rem;">Valor</small>
                                                <div class="info-value" style="color: #212529; font-size: 0.9rem; font-weight: 600;">${{ number_format($inventario->valor_unitario * $cantidadTotal, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Additional Information -->
                                @if($inventario->marca || $inventario->numero_serie)
                                <div class="row g-3 mb-4">
                                    @if($inventario->marca)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 info-card" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;">
                                            <div class="me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; background-color: #6f42c1; border-radius: 50%; color: white;">
                                                <i class="fas fa-tag" style="font-size: 0.9rem;"></i>
                                            </div>
                                            <div>
                                                <small style="color: #6c757d; font-size: 0.8rem;">Marca</small>
                                                <div class="info-value" style="color: #212529; font-size: 0.9rem; font-weight: 600;">{{ $inventario->marca }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if($inventario->numero_serie)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 info-card" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;">
                                            <div class="me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; background-color: #fd7e14; border-radius: 50%; color: white;">
                                                <i class="fas fa-hashtag" style="font-size: 0.9rem;"></i>
                                            </div>
                                            <div>
                                                <small style="color: #6c757d; font-size: 0.8rem;">Serie</small>
                                                <div class="info-value" style="color: #212529; font-size: 0.9rem; font-weight: 600;">{{ $inventario->numero_serie }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endif
                                
                                <!-- Actions - Centered between columns -->
                                <div class="d-flex justify-content-center mt-4 mb-3">
                                    <div class="d-flex gap-3 flex-wrap justify-content-center">
                                        @if(auth()->user()->role->name === 'administrador' || auth()->user()->role->name === 'almacenista')
                                            <a href="{{ route('movimientos.create', ['inventario_id' => $inventario->id]) }}" class="btn btn-primary" style="border-radius: 8px; min-width: 140px; padding: 10px 16px;">
                                                <i class="fas fa-exchange-alt me-2"></i>Movimiento
                                            </a>
                                            <a href="{{ route('mantenimientos.create', ['inventario_id' => $inventario->id]) }}" class="btn btn-warning" style="border-radius: 8px; min-width: 140px; padding: 10px 16px;">
                                                <i class="fas fa-tools me-2"></i>Mantenimiento
                                            </a>
                                        @endif
                                        <a href="{{ route('inventarios.edit', $inventario->id) }}" class="btn btn-outline-secondary" style="border-radius: 8px; min-width: 140px; padding: 10px 16px;">
                                            <i class="fas fa-edit me-2"></i>Editar
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Image Section (50%) -->
                            <div class="col-lg-6 col-md-6">
                                <div class="text-center">
                                    @php
                                        $imageUrl = null;
                                        if($inventario->imagen_principal && file_exists(storage_path('app/public/' . $inventario->imagen_principal))) {
                                            $imageUrl = asset('storage/' . $inventario->imagen_principal);
                                        }
                                        elseif($inventario->getFirstMediaUrl('imagenes') && $inventario->getMedia('imagenes')->count() > 0) {
                                            $media = $inventario->getMedia('imagenes')->first();
                                            if(file_exists($media->getPath())) {
                                                $imageUrl = $inventario->getFirstMediaUrl('imagenes');
                                            }
                                        }
                                        elseif($inventario->imagen && file_exists(storage_path('app/public/inventario_imagenes/' . $inventario->imagen))) {
                                            $imageUrl = asset('storage/inventario_imagenes/' . $inventario->imagen);
                                        }
                                    @endphp
                                    @if($imageUrl)
                                        <div class="image-container" style="border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden; background-color: #f8f9fa; min-height: 450px; display: flex; align-items: center; justify-content: center;">
                                            <img src="{{ $imageUrl }}" alt="{{ $inventario->nombre }}" class="img-fluid" style="width: 100%; height: 450px; object-fit: cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center no-image-placeholder" style="height: 450px; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;">
                                            <div class="text-center">
                                                <i class="fas fa-image" style="color: #6c757d; font-size: 3rem; margin-bottom: 1rem;"></i>
                                                <h4 style="color: #6c757d; font-size: 1.2rem;">Sin imagen disponible</h4>
                                                <p style="color: #6c757d; font-size: 0.9rem;">No se ha adjuntado imagen para este equipo</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Contenedor Bootstrap principal -->
    <div class="container-fluid px-0">
        
        <!-- Contenedor 1: Identificación del Equipo -->
        <div class="row mb-4 mx-0">
            <div class="col-12 px-0">
                <div class="card">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                        <div class="d-flex align-items-center">
                            <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #007bff; border-radius: 50%; color: white;">
                                <i class="fas fa-id-card" style="font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Identificación del Equipo</h2>
                                <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Datos básicos y especificaciones técnicas</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 h-100 info-card" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <i class="fas fa-building" style="color: #007bff; font-size: 1.2rem;"></i>
                                        </div>
                                        <h4 class="mb-0 info-value" style="color: #212529; font-size: 1.1rem;">Información Corporativa</h4>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Propietario</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">{{ $inventario->propietario ?? 'HIDROOBRAS' }}</span>
                                            </div>
                                        </div>
                                        <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Proveedor</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">{{ $inventario->proveedor->nombre ?? 'No especificado' }}</span>
                                            </div>
                                        </div>
                                        <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Categoría</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">{{ $inventario->categoria->nombre ?? 'No especificada' }}</span>
                                            </div>
                                        </div>
                                        <div class="p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Última Inspección</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">{{ $inventario->fecha_inspeccion ? $inventario->fecha_inspeccion->format('d/m/Y') : 'Pendiente' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="p-3 h-100 info-card" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <i class="fas fa-cogs" style="color: #28a745; font-size: 1.2rem;"></i>
                                        </div>
                                        <h4 class="mb-0 info-value" style="color: #212529; font-size: 1.1rem;">Especificaciones Técnicas</h4>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Número de Serie</span>
                                                <span class="badge bg-secondary" style="font-size: 0.7rem;">{{ $inventario->numero_serie ?? 'No especificado' }}</span>
                                            </div>
                                        </div>
                                        <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Marca</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">{{ $inventario->marca ?? 'No especificada' }}</span>
                                            </div>
                                        </div>
                                        <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Modelo</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">{{ $inventario->modelo ?? 'No especificado' }}</span>
                                            </div>
                                        </div>
                                        <div class="p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Descripción</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">{{ Str::limit($inventario->descripcion, 30) ?? 'No especificada' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor 2: Información Financiera -->
        <div class="row mb-4 mx-0">
            <div class="col-12 px-0">
                <div class="card">
                    <div class="card-header info-card" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                        <div class="d-flex align-items-center">
                            <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #28a745; border-radius: 50%; color: white;">
                                <i class="fas fa-chart-line" style="font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 info-value" style="color: #212529; font-size: 1.5rem;">Información Financiera</h2>
                                <p class="mb-0 info-value" style="color: #212529; font-size: 0.9rem;">Detalles económicos y de adquisición</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 h-100 info-card" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <i class="fas fa-dollar-sign" style="color: #28a745; font-size: 1.2rem;"></i>
                                        </div>
                                        <h4 class="mb-0 info-value" style="color: #212529; font-size: 1.1rem;">Valores y Costos</h4>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Valor Unitario</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">${{ number_format($inventario->valor_unitario, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Cantidad Total</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">{{ $cantidadTotal }}</span>
                                            </div>
                                        </div>
                                        <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Valor Total</span>
                                                <span class="badge bg-success" style="font-size: 0.8rem;">${{ number_format($inventario->valor_unitario * $cantidadTotal, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Depreciación</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">{{ $inventario->depreciacion ?? 'No calculada' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="p-3 h-100 info-card" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <i class="fas fa-receipt" style="color: #17a2b8; font-size: 1.2rem;"></i>
                                        </div>
                                        <h4 class="mb-0 info-value" style="color: #212529; font-size: 1.1rem;">Datos de Adquisición</h4>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Fecha de Compra</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">{{ $inventario->fecha_compra ? $inventario->fecha_compra->format('d/m/Y') : 'No registrada' }}</span>
                                            </div>
                                        </div>
                                        <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Número de Factura</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">{{ $inventario->numero_factura ?? 'No registrado' }}</span>
                                            </div>
                                        </div>
                                        <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Proveedor</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">{{ $inventario->proveedor->nombre ?? $inventario->propietario ?? 'No registrado' }}</span>
                                            </div>
                                        </div>
                                        <div class="p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Estado Financiero</span>
                                                <span class="badge" style="background-color: {{ $inventario->valor_unitario > 0 ? '#28a745' : '#6c757d' }}; color: white; font-size: 0.8rem;">{{ $inventario->valor_unitario > 0 ? 'Valorizado' : 'Sin valorizar' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor 3: Documentación Visual -->
        <div class="row mb-4 mx-0">
            <div class="col-12 px-0">
                <div class="card">
                    <div class="card-header info-card" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                        <div class="d-flex align-items-center">
                            <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #ffc107; border-radius: 50%; color: #212529;">
                                <i class="fas fa-images" style="font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 info-value" style="color: #212529; font-size: 1.5rem;">Galería de Imágenes</h2>
                                <p class="mb-0 info-value" style="color: #212529; font-size: 0.9rem;">Fotografías del equipo y documentación visual</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @php
                            $imagenes = $inventario->getMedia('imagenes');
                            $totalImagenes = $imagenes->count();
                        @endphp
                        
                        @if($totalImagenes > 0)
                            <div class="row g-3">
                                @foreach($imagenes as $index => $imagen)
                                    <div class="col-md-6 col-sm-6">
                                        <div class="position-relative image-container info-card" style="border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden; background-color: #f8f9fa; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">
                                            <img src="{{ asset('storage/inventario_imagenes/' . $imagen->file_name) }}" alt="Imagen {{ $index + 1 }}" class="img-fluid" style="width: 100%; height: 200px; object-fit: cover; cursor: pointer;" onclick="openImageModal('{{ asset('storage/inventario_imagenes/' . $imagen->file_name) }}', '{{ $inventario->nombre }} - Imagen {{ $index + 1 }}')">
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-primary" style="font-size: 0.7rem;">{{ $index + 1 }}/{{ $totalImagenes }}</span>
                                            </div>
                                            <div class="position-absolute bottom-0 start-0 end-0 p-2" style="background: linear-gradient(transparent, rgba(0,0,0,0.7)); color: white;">
                                                <small class="d-block text-truncate">{{ $inventario->nombre }}</small>
                                                <small class="text-muted">Clic para ampliar</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5 no-image-placeholder info-card" style="border: 2px dashed #dee2e6; border-radius: 12px; background-color: #f8f9fa;">
                                <i class="fas fa-camera" style="color: #6c757d; font-size: 3rem; margin-bottom: 1rem;"></i>
                                <h4 class="info-value" style="color: #6c757d; font-size: 1.2rem;">Sin imágenes disponibles</h4>
                                <p class="info-value" style="color: #6c757d; font-size: 0.9rem;">No se han adjuntado fotografías para este equipo</p>
                                <small class="info-value" style="color: #adb5bd;">Las imágenes ayudan a identificar y documentar el estado del equipo</small>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor 4: Bitácora de Observaciones -->
        <div class="row mb-4 mx-0">
            <div class="col-12 px-0">
                <div class="card" style="border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <div class="card-header" style="background-color: #f8f9fa; border-radius: 15px 15px 0 0; border: none;">
                        <div class="d-flex align-items-center">
                            <div class="me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background-color: rgba(108,117,125,0.1); border-radius: 50%; color: #007bff; backdrop-filter: blur(10px);">
                                <i class="fas fa-history" style="font-size: 1.4rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2 class="mb-1" style="color: #212529; font-size: 1.5rem; font-weight: 600;">Bitácora de Observaciones</h2>
                                <p class="mb-0" style="color: #6c757d; font-size: 1rem;">Registro cronológico de eventos y observaciones</p>
                            </div>
                            @php
                                $observacionesArray = [];
                                if($inventario->observaciones) {
                                    // Limpiar caracteres especiales y dividir por asteriscos o saltos de línea
                                    $texto = str_replace(['_x000D_', '\r', '\n'], ' ', $inventario->observaciones);
                                    $texto = preg_replace('/\s+/', ' ', $texto); // Normalizar espacios
                                    
                                    // Dividir por asteriscos (*) que preceden a fechas
                                    $observacionesArray = preg_split('/\*(?=\d{1,2}\/\d{1,2}\/\d{4})/', $texto);
                                    
                                    // Si no hay asteriscos, intentar dividir por fechas directamente
                                    if(count($observacionesArray) <= 1) {
                                        $observacionesArray = preg_split('/(\d{1,2}\/\d{1,2}\/\d{4})/', $texto, -1, PREG_SPLIT_DELIM_CAPTURE);
                                        // Reagrupar fechas con su contenido
                                        $temp = [];
                                        for($i = 1; $i < count($observacionesArray); $i += 2) {
                                            if(isset($observacionesArray[$i]) && isset($observacionesArray[$i+1])) {
                                                $temp[] = $observacionesArray[$i] . ' ' . trim($observacionesArray[$i+1]);
                                            }
                                        }
                                        $observacionesArray = $temp;
                                    }
                                    
                                    // Limpiar y filtrar observaciones
                                    $observacionesArray = array_map(function($obs) {
                                        $obs = trim($obs);
                                        $obs = ltrim($obs, '*'); // Remover asterisco inicial si existe
                                        return trim($obs);
                                    }, $observacionesArray);
                                    
                                    $observacionesArray = array_filter($observacionesArray, function($obs) {
                                        return !empty(trim($obs)) && preg_match('/\d{1,2}\/\d{1,2}\/\d{4}/', $obs);
                                    });
                                    
                                    // Ordenar por fecha (más reciente primero)
                                    usort($observacionesArray, function($a, $b) {
                                        $fechaA = null;
                                        $fechaB = null;
                                        
                                        if(preg_match('/(\d{1,2}\/\d{1,2}\/\d{4})/', $a, $matchA)) {
                                            $fechaA = $matchA[1];
                                        }
                                        if(preg_match('/(\d{1,2}\/\d{1,2}\/\d{4})/', $b, $matchB)) {
                                            $fechaB = $matchB[1];
                                        }
                                        
                                        if($fechaA && $fechaB) {
                                            try {
                                                $dateA = \Carbon\Carbon::createFromFormat('d/m/Y', $fechaA);
                                                $dateB = \Carbon\Carbon::createFromFormat('d/m/Y', $fechaB);
                                                return $dateB->timestamp - $dateA->timestamp; // Más reciente primero
                                            } catch (Exception $e) {
                                                return 0;
                                            }
                                        }
                                        return 0;
                                    });
                                }
                                $totalObservaciones = count($observacionesArray);
                            @endphp
                            <div class="d-flex align-items-center">
                                <span class="badge" style="background-color: rgba(255,255,255,0.2); color: white; font-size: 0.9rem; padding: 8px 12px; border-radius: 20px; backdrop-filter: blur(10px);">{{ $totalObservaciones }} eventos</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 2rem; background-color: #f8f9fa;">
                        @if($totalObservaciones > 0)
                            <div class="timeline-wrapper" style="position: relative;">
                                <!-- Línea de tiempo vertical -->
                                <div class="timeline-line" style="position: absolute; left: 30px; top: 0; bottom: 0; width: 3px; background: linear-gradient(to bottom, #667eea, #764ba2); border-radius: 2px;"></div>
                                
                                <div class="timeline-container" style="max-height: 500px; overflow-y: auto; padding-right: 10px;">
                                    @foreach($observacionesArray as $index => $observacion)
                                        @php
                                            $observacion = trim($observacion);
                                            if(empty($observacion)) continue;
                                            
                                            // Parsear la observación para extraer fecha y contenido
                                            $fechaMatch = [];
                                            $contenido = $observacion;
                                            $fecha = null;
                                            $fechaFormateada = 'Sin fecha';
                                            $tipo = 'general';
                                            $icono = 'fas fa-sticky-note';
                                            $color = '#6c757d';
                                            $colorFondo = '#f8f9fa';
                                            
                                            // Buscar patrón de fecha al inicio (formato dd/mm/yyyy)
                                             if(preg_match('/^(\d{1,2}\/\d{1,2}\/\d{4})\s*[-:]?\s*(.*)/', $observacion, $fechaMatch)) {
                                                 $fecha = $fechaMatch[1];
                                                 $contenido = trim($fechaMatch[2]);
                                                 
                                                 // Limpiar contenido de caracteres especiales adicionales
                                                 $contenido = str_replace(['_x000D_', '\r', '\n'], ' ', $contenido);
                                                 $contenido = preg_replace('/\s+/', ' ', $contenido);
                                                 $contenido = trim($contenido);
                                                 
                                                 // Formatear fecha
                                                 try {
                                                     $fechaObj = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha);
                                                     $fechaFormateada = $fechaObj->format('d M Y');
                                                 } catch (Exception $e) {
                                                     $fechaFormateada = $fecha;
                                                 }
                                             }
                                            
                                            // Determinar tipo de evento basado en palabras clave
                                             $contenidoLower = strtolower($contenido);
                                             if(strpos($contenidoLower, 'compra') !== false || strpos($contenidoLower, 'adquisición') !== false || strpos($contenidoLower, 'adquiere') !== false) {
                                                 $tipo = 'Compra';
                                                 $icono = 'fas fa-shopping-cart';
                                                 $color = '#28a745';
                                                 $colorFondo = '#d4edda';
                                             } elseif(strpos($contenidoLower, 'se lleva') !== false || strpos($contenidoLower, 'se envia') !== false || strpos($contenidoLower, 'se envía') !== false || strpos($contenidoLower, 'envio') !== false || strpos($contenidoLower, 'envío') !== false) {
                                                 $tipo = 'Envío a Obra';
                                                 $icono = 'fas fa-truck';
                                                 $color = '#007bff';
                                                 $colorFondo = '#d1ecf1';
                                             } elseif(strpos($contenidoLower, 'sale de obra') !== false || strpos($contenidoLower, 'devolucion') !== false || strpos($contenidoLower, 'devolución') !== false || strpos($contenidoLower, 'retorna') !== false) {
                                                 $tipo = 'Retorno de Obra';
                                                 $icono = 'fas fa-undo';
                                                 $color = '#dc3545';
                                                 $colorFondo = '#f8d7da';
                                             } elseif(strpos($contenidoLower, 'inspeccion') !== false || strpos($contenidoLower, 'inspección') !== false || strpos($contenidoLower, 'se inspecciona') !== false || strpos($contenidoLower, 'certificacion') !== false || strpos($contenidoLower, 'certificación') !== false) {
                                                 $tipo = 'Inspección';
                                                 $icono = 'fas fa-search';
                                                 $color = '#17a2b8';
                                                 $colorFondo = '#d1ecf1';
                                             } elseif(strpos($contenidoLower, 'mantenimiento') !== false || strpos($contenidoLower, 'reparación') !== false || strpos($contenidoLower, 'servicio') !== false || strpos($contenidoLower, 'limpieza') !== false || strpos($contenidoLower, 'recertificacion') !== false) {
                                                 $tipo = 'Mantenimiento';
                                                 $icono = 'fas fa-tools';
                                                 $color = '#ffc107';
                                                 $colorFondo = '#fff3cd';
                                             } elseif(strpos($contenidoLower, 'sale de mantenimiento') !== false || strpos($contenidoLower, 'termina mantenimiento') !== false) {
                                                 $tipo = 'Fin Mantenimiento';
                                                 $icono = 'fas fa-check-circle';
                                                 $color = '#28a745';
                                                 $colorFondo = '#d4edda';
                                             } elseif(strpos($contenidoLower, 'movimiento') !== false || strpos($contenidoLower, 'traslado') !== false || strpos($contenidoLower, 'transferencia') !== false) {
                                                 $tipo = 'Movimiento';
                                                 $icono = 'fas fa-exchange-alt';
                                                 $color = '#6f42c1';
                                                 $colorFondo = '#e2d9f3';
                                             } else {
                                                 $tipo = 'Observación';
                                                 $icono = 'fas fa-sticky-note';
                                                 $color = '#6c757d';
                                                 $colorFondo = '#f8f9fa';
                                             }
                                        @endphp
                                        
                                        <div class="timeline-item" style="position: relative; margin-bottom: 2rem; margin-left: 60px;">
                                            <!-- Marcador de la línea de tiempo -->
                                            <div class="timeline-marker" style="position: absolute; left: -45px; top: 10px; width: 20px; height: 20px; background-color: {{ $color }}; border: 4px solid white; border-radius: 50%; box-shadow: 0 2px 8px rgba(0,0,0,0.15); z-index: 2;"></div>
                                            
                                            <!-- Contenido del evento -->
                                            <div class="timeline-content" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-left: 4px solid {{ $color }};">
                                                <!-- Encabezado del evento -->
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3" style="width: 40px; height: 40px; background-color: {{ $color }}; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                                                            <i class="{{ $icono }}" style="font-size: 1rem;"></i>
                                                        </div>
                                                        <div>
                                                            <h5 class="mb-1" style="color: #212529; font-size: 1.1rem; font-weight: 600;">{{ $tipo }}</h5>
                                                            <p class="mb-0" style="color: #6c757d; font-size: 0.9rem;">{{ $fechaFormateada }}</p>
                                                        </div>
                                                    </div>
                                                    <span class="badge" style="background-color: {{ $color }}; color: white; font-size: 0.8rem; padding: 6px 12px; border-radius: 20px;">Evento #{{ $totalObservaciones - $index }}</span>
                                                </div>
                                                
                                                <!-- Descripción del evento -->
                                                <div class="event-description" style="background-color: {{ $colorFondo }}; border-radius: 8px; padding: 1rem; border-left: 3px solid {{ $color }};">
                                                    <p class="mb-0" style="color: #212529; font-size: 1rem; line-height: 1.6;">{{ $contenido }}</p>
                                                </div>
                                                
                                                <!-- Hora relativa (si hay fecha) -->
                                                @if($fecha)
                                                    @php
                                                        try {
                                                            $fechaObj = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha) ?: \Carbon\Carbon::createFromFormat('Y-m-d', $fecha);
                                                            $tiempoRelativo = $fechaObj->diffForHumans();
                                                        } catch (Exception $e) {
                                                            $tiempoRelativo = null;
                                                        }
                                                    @endphp
                                                    @if(isset($tiempoRelativo))
                                                        <div class="mt-2">
                                                            <small class="text-muted" style="font-style: italic;"><i class="fas fa-clock me-1"></i>{{ $tiempoRelativo }}</small>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-clipboard-list" style="color: #6c757d; font-size: 4rem; opacity: 0.5;"></i>
                                </div>
                                <h4 style="color: #6c757d; font-size: 1.3rem; font-weight: 600; margin-bottom: 0.5rem;">Sin eventos registrados</h4>
                                <p style="color: #6c757d; font-size: 1rem; margin-bottom: 0;">No se han registrado observaciones para este equipo</p>
                                <div class="mt-3">
                                    <small class="text-muted">Los eventos aparecerán aquí cuando se agreguen observaciones</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor 5: Historial y Documentación -->
        <div class="row mb-4 mx-0">
            <div class="col-12 px-0">
                <div class="card">
                    <div class="card-header info-card" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                        <div class="d-flex align-items-center">
                            <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #17a2b8; border-radius: 50%; color: white;">
                                <i class="fas fa-history" style="font-size: 1.2rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h2 class="mb-1 info-value" style="color: #212529; font-size: 1.5rem;">Registro de Actividades</h2>
                                <p class="mb-0 info-value" style="color: #6c757d; font-size: 0.9rem;">Movimientos, mantenimientos y documentos del equipo</p>
                            </div>
                            @php
                                $totalRegistros = $movimientos->count() + $inventario->mantenimientos->count() + $documentos->count();
                            @endphp
                            <span class="badge bg-secondary ms-2" style="font-size: 0.8rem;">{{ $totalRegistros }}</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                                <!-- Navegación por pestañas -->
                                <ul class="nav nav-tabs mb-4 d-flex" id="historyTabs" role="tablist" style="border-bottom: 2px solid #e9ecef;">
                                    <li class="nav-item flex-fill" role="presentation">
                                        <button class="nav-link active text-center w-100" id="movements-tab" data-bs-toggle="tab" data-bs-target="#movements" type="button" role="tab" aria-controls="movements" aria-selected="true" style="color: #212529; border: none; border-bottom: 3px solid #007bff; background: none; font-weight: 600; padding: 16px 20px;">
                                            <i class="fas fa-exchange-alt me-2" style="color: #007bff; font-size: 1.1rem;"></i>
                                            Movimientos
                                            <span class="badge ms-2" style="background-color: #007bff; color: white; font-size: 0.75rem;">{{ $movimientos->count() }}</span>
                                        </button>
                                    </li>
                                    <li class="nav-item flex-fill" role="presentation">
                                        <button class="nav-link text-center w-100" id="maintenance-tab" data-bs-toggle="tab" data-bs-target="#maintenance" type="button" role="tab" aria-controls="maintenance" aria-selected="false" style="color: #212529; border: none; background: none; font-weight: 600; padding: 16px 20px; border-bottom: 3px solid transparent; transition: all 0.3s ease;">
                                            <i class="fas fa-tools me-2" style="color: #28a745; font-size: 1.1rem;"></i>
                                            Mantenimientos
                                            <span class="badge ms-2" style="background-color: #f8f9fa; color: #495057; border: 1px solid #dee2e6; font-size: 0.75rem;">{{ $inventario->mantenimientos->count() }}</span>
                                        </button>
                                    </li>
                                    <li class="nav-item flex-fill" role="presentation">
                                        <button class="nav-link text-center w-100" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab" aria-controls="documents" aria-selected="false" style="color: #212529; border: none; background: none; font-weight: 600; padding: 16px 20px; border-bottom: 3px solid transparent; transition: all 0.3s ease;">
                                            <i class="fas fa-file-alt me-2" style="color: #6f42c1; font-size: 1.1rem;"></i>
                                            Documentos
                                            <span class="badge ms-2" style="background-color: #f8f9fa; color: #495057; border: 1px solid #dee2e6; font-size: 0.75rem;">{{ $documentos->count() }}</span>
                                        </button>
                                    </li>
                                </ul>
                                
                                <style>
                                .nav-tabs .nav-link:hover {
                                    border-bottom-color: #007bff !important;
                                    color: #007bff !important;
                                }
                                .nav-tabs .nav-link.active {
                                    border-bottom-color: #007bff !important;
                                    background-color: transparent !important;
                                }
                                #maintenance-tab:hover {
                                    border-bottom-color: #28a745 !important;
                                }
                                #documents-tab:hover {
                                    border-bottom-color: #6f42c1 !important;
                                }
                                </style>

                                <!-- Contenido de las pestañas -->
                                <div class="tab-content" id="historyTabsContent">
                                    <!-- Pestaña de Movimientos -->
                                    <div class="tab-pane fade show active" id="movements" role="tabpanel" aria-labelledby="movements-tab">
                                        @if($movimientos->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover" style="border: 1px solid #dee2e6;">
                                                    <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                                        <tr>
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center;">Fecha</th>
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center;">Tipo de Movimiento</th>
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center;">Ubicación Origen</th>
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center;">Ubicación Destino</th>
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center;">Cantidad</th>
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center;">Responsable</th>
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center;">Motivo</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($movimientos as $movimiento)
                                                        <tr style="border-bottom: 1px solid #e9ecef;">
                                                            <td style="padding: 16px; color: #212529; text-align: center; font-weight: 500;">{{ $movimiento->fecha_movimiento ? $movimiento->fecha_movimiento->format('d/m/Y H:i') : 'N/A' }}</td>
                                                            <td style="padding: 16px; text-align: center;">
                                                                @php
                                                                    $tipoTexto = '';
                                                                    $colorBadge = '#6c757d';
                                                                    switch($movimiento->tipo_movimiento) {
                                                                        case 'entrada':
                                                                            $tipoTexto = 'Entrada';
                                                                            $colorBadge = '#28a745';
                                                                            break;
                                                                        case 'salida':
                                                                            $tipoTexto = 'Salida';
                                                                            $colorBadge = '#dc3545';
                                                                            break;
                                                                        case 'transferencia':
                                                                            $tipoTexto = 'Transferencia';
                                                                            $colorBadge = '#007bff';
                                                                            break;
                                                                        case 'ajuste':
                                                                            $tipoTexto = 'Ajuste';
                                                                            $colorBadge = '#ffc107';
                                                                            break;
                                                                        default:
                                                                            $tipoTexto = 'Movimiento';
                                                                            $colorBadge = '#6c757d';
                                                                    }
                                                                @endphp
                                                                <span class="badge" style="background-color: {{ $colorBadge }}; color: white; font-weight: 500; padding: 6px 12px; border-radius: 6px;">
                                                                    {{ $tipoTexto }}
                                                                </span>
                                                            </td>
                                                            <td style="padding: 16px; color: #212529; text-align: center;">
                                                                @php
                                                                    $ubicacionOrigen = \App\Models\Ubicacion::find($movimiento->ubicacion_origen);
                                                                @endphp
                                                                {{ $ubicacionOrigen ? $ubicacionOrigen->nombre : ($movimiento->ubicacion_origen ?? 'N/A') }}
                                                            </td>
                                                            <td style="padding: 16px; color: #212529; text-align: center;">
                                                                @php
                                                                    $ubicacionDestino = \App\Models\Ubicacion::find($movimiento->ubicacion_destino);
                                                                @endphp
                                                                {{ $ubicacionDestino ? $ubicacionDestino->nombre : ($movimiento->ubicacion_destino ?? 'N/A') }}
                                                            </td>
                                                            <td style="padding: 16px; color: #212529; font-weight: 600; text-align: center;">{{ $movimiento->cantidad }}</td>
                                                            <td style="padding: 16px; color: #212529; text-align: center;">{{ $movimiento->usuarioDestino->nombre ?? 'N/A' }}</td>
                                                            <td style="padding: 16px; color: #212529; text-align: center; max-width: 250px;">{{ Str::limit($movimiento->motivo ?? 'Sin especificar', 60) }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-5" style="background-color: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
                                                <i class="fas fa-exchange-alt" style="color: #6c757d; font-size: 3rem; margin-bottom: 1rem;"></i>
                                                <h5 style="color: #495057; font-weight: 600; margin-bottom: 0.5rem;">Sin movimientos registrados</h5>
                                                <p style="color: #6c757d; margin: 0;">No se han registrado movimientos para este equipo</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Pestaña de Mantenimientos -->
                                    <div class="tab-pane fade" id="maintenance" role="tabpanel" aria-labelledby="maintenance-tab">
                                        @if($inventario->mantenimientos->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover" style="border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden; margin-bottom: 0;">
                                                    <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                                        <tr>
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center; border-right: 1px solid #dee2e6;">Tipo</th>
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center; border-right: 1px solid #dee2e6;">Descripción</th>
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center; border-right: 1px solid #dee2e6;">Fecha Inicio</th>
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center; border-right: 1px solid #dee2e6;">Fecha Fin</th>
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center; border-right: 1px solid #dee2e6;">Costo</th>
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center;">Proveedor</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($inventario->mantenimientos as $mantenimiento)
                                                        <tr style="border-bottom: 1px solid #f1f3f4;">
                                                            <td style="padding: 16px; text-align: center; border-right: 1px solid #f1f3f4; vertical-align: middle;">
                                                                @php
                                                                    $tipoColor = '#28a745';
                                                                    $tipoTexto = 'General';
                                                                    $tipoIcon = 'fas fa-tools';
                                                                    switch(strtolower($mantenimiento->tipo ?? '')) {
                                                                        case 'preventivo':
                                                                            $tipoColor = '#28a745';
                                                                            $tipoTexto = 'Preventivo';
                                                                            $tipoIcon = 'fas fa-calendar-check';
                                                                            break;
                                                                        case 'correctivo':
                                                                            $tipoColor = '#dc3545';
                                                                            $tipoTexto = 'Correctivo';
                                                                            $tipoIcon = 'fas fa-wrench';
                                                                            break;
                                                                        case 'predictivo':
                                                                            $tipoColor = '#007bff';
                                                                            $tipoTexto = 'Predictivo';
                                                                            $tipoIcon = 'fas fa-chart-line';
                                                                            break;
                                                                        default:
                                                                            $tipoColor = '#6c757d';
                                                                            $tipoTexto = 'General';
                                                                            $tipoIcon = 'fas fa-tools';
                                                                    }
                                                                @endphp
                                                                <div class="d-flex align-items-center justify-content-center">
                                                                    <i class="{{ $tipoIcon }}" style="color: {{ $tipoColor }}; font-size: 1.2rem; margin-right: 8px;"></i>
                                                                    <span class="badge" style="background-color: {{ $tipoColor }}; color: white; font-weight: 600; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem;">
                                                                        {{ $tipoTexto }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td style="padding: 16px; border-right: 1px solid #f1f3f4; vertical-align: middle;">
                                                                <div style="color: #212529; font-weight: 500; font-size: 0.95rem; line-height: 1.4;">
                                                                    {{ $mantenimiento->descripcion ?? 'Sin descripción' }}
                                                                </div>
                                                            </td>
                                                            <td style="padding: 16px; text-align: center; border-right: 1px solid #f1f3f4; vertical-align: middle;">
                                                                <span style="color: #212529; font-weight: 500; font-size: 0.9rem;">
                                                                    {{ $mantenimiento->fecha_inicio ? $mantenimiento->fecha_inicio->format('d/m/Y') : 'No especificada' }}
                                                                </span>
                                                            </td>
                                                            <td style="padding: 16px; text-align: center; border-right: 1px solid #f1f3f4; vertical-align: middle;">
                                                                @if($mantenimiento->fecha_fin)
                                                                    <span style="color: #28a745; font-weight: 600; font-size: 0.9rem;">
                                                                        {{ $mantenimiento->fecha_fin->format('d/m/Y') }}
                                                                    </span>
                                                                @else
                                                                    <span class="badge" style="background-color: #ffc107; color: #212529; font-weight: 500; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem;">
                                                                        En proceso
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td style="padding: 16px; text-align: center; border-right: 1px solid #f1f3f4; vertical-align: middle;">
                                                                <span style="color: #212529; font-weight: 600; font-size: 0.9rem;">
                                                                    ${{ number_format($mantenimiento->costo ?? 0, 2) }}
                                                                </span>
                                                            </td>
                                                            <td style="padding: 16px; text-align: center; vertical-align: middle;">
                                                                <span style="color: #212529; font-size: 0.9rem;">
                                                                    {{ $mantenimiento->proveedor_servicio ?? 'No especificado' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-5" style="background-color: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
                                                <i class="fas fa-tools" style="color: #6c757d; font-size: 3rem; margin-bottom: 1rem;"></i>
                                                <h5 style="color: #495057; font-weight: 600; margin-bottom: 0.5rem;">Sin mantenimientos registrados</h5>
                                                <p style="color: #6c757d; margin: 0;">No se han registrado mantenimientos para este equipo</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Pestaña de Documentos -->
                                    <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                                        <div class="row justify-content-center">
                                            <div class="col-md-8">
                                                <div class="card" style="border: 1px solid #dee2e6; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 12px;">
                                                    <div class="card-body text-center" style="padding: 40px;">
                                                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; width: 120px; height: 120px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);">
                                                            <i class="fas fa-server" style="color: white; font-size: 3rem;"></i>
                                                        </div>
                                                        <h4 style="color: #212529; font-weight: 700; margin-bottom: 16px; font-size: 1.5rem;">Documentación en NAS</h4>
                                                        <p style="color: #6c757d; margin-bottom: 32px; font-size: 1rem; line-height: 1.6;">Toda la documentación técnica de este equipo está centralizada en nuestro servidor NAS. Escanea el código QR para acceder directamente a los archivos.</p>
                                                        
                                                        <!-- QR Code Container -->
                                                        <div style="background-color: #f8f9fa; border: 2px solid #dee2e6; border-radius: 12px; padding: 24px; margin-bottom: 24px; display: inline-block;">
                                                            @php
                                                                // Generar URL del NAS para este equipo específico
                                                                $nasUrl = 'http://nas.empresa.local/documentos/inventario/' . $inventario->id;
                                                            @endphp
                                                            <div id="qrcode-{{ $inventario->id }}" style="margin: 0 auto;"></div>
                                                        </div>
                                                        
                                                        <div class="row g-3 text-start">
                                                            <div class="col-md-6">
                                                                <div class="d-flex align-items-center p-3" style="background-color: #f8f9fa; border-radius: 8px; border-left: 4px solid #28a745;">
                                                                    <i class="fas fa-file-pdf" style="color: #dc3545; font-size: 1.5rem; margin-right: 12px;"></i>
                                                                    <div>
                                                                        <h6 style="color: #212529; font-weight: 600; margin: 0; font-size: 0.9rem;">Manuales</h6>
                                                                        <small style="color: #6c757d;">Guías técnicas y de usuario</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="d-flex align-items-center p-3" style="background-color: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">
                                                                    <i class="fas fa-receipt" style="color: #28a745; font-size: 1.5rem; margin-right: 12px;"></i>
                                                                    <div>
                                                                        <h6 style="color: #212529; font-weight: 600; margin: 0; font-size: 0.9rem;">Facturas</h6>
                                                                        <small style="color: #6c757d;">Comprobantes de compra</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="d-flex align-items-center p-3" style="background-color: #f8f9fa; border-radius: 8px; border-left: 4px solid #ffc107;">
                                                                    <i class="fas fa-shield-alt" style="color: #ffc107; font-size: 1.5rem; margin-right: 12px;"></i>
                                                                    <div>
                                                                        <h6 style="color: #212529; font-weight: 600; margin: 0; font-size: 0.9rem;">Garantías</h6>
                                                                        <small style="color: #6c757d;">Certificados de garantía</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="d-flex align-items-center p-3" style="background-color: #f8f9fa; border-radius: 8px; border-left: 4px solid #6f42c1;">
                                                                    <i class="fas fa-id-card" style="color: #007bff; font-size: 1.5rem; margin-right: 12px;"></i>
                                                                    <div>
                                                                        <h6 style="color: #212529; font-weight: 600; margin: 0; font-size: 0.9rem;">Hoja de Vida</h6>
                                                                        <small style="color: #6c757d;">Historial completo del equipo</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mt-4">
                                                            <a href="{{ $nasUrl }}" target="_blank" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; font-weight: 600; padding: 12px 32px; border-radius: 8px; text-decoration: none; transition: all 0.3s ease; font-size: 0.95rem;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 16px rgba(102, 126, 234, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                                                <i class="fas fa-external-link-alt me-2" style="font-size: 0.9rem;"></i>Acceder al NAS
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Script para generar QR -->
                                        <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const qrContainer = document.getElementById('qrcode-{{ $inventario->id }}');
                                                if (qrContainer) {
                                                    QRCode.toCanvas(qrContainer, '{{ $nasUrl }}', {
                                                        width: 200,
                                                        height: 200,
                                                        margin: 2,
                                                        color: {
                                                            dark: '#212529',
                                                            light: '#ffffff'
                                                        }
                                                    }, function (error) {
                                                        if (error) {
                                                            console.error('Error generando QR:', error);
                                                            qrContainer.innerHTML = '<div style="width: 200px; height: 200px; background-color: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; color: #6c757d;"><i class="fas fa-qrcode" style="font-size: 3rem;"></i></div>';
                                                        }
                                                    });
                                                }
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal para mostrar imágenes -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true" style="backdrop-filter: blur(12px); background: rgba(0, 0, 0, 0.8);">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content" style="background: rgba(0, 0, 0, 0.95); border: none; border-radius: 0;">
            <div class="modal-header" style="background: rgba(0, 0, 0, 0.8); border-bottom: 1px solid rgba(255, 255, 255, 0.1); position: absolute; top: 0; left: 0; right: 0; z-index: 1050; padding: 1rem 1.5rem;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-image text-white me-2" style="font-size: 1.2rem;"></i>
                    <h5 class="modal-title mb-0 text-white" id="imageModalLabel" style="font-weight: 500;">Imagen del Equipo</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0" style="background: transparent; position: relative; height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden;" onclick="closeModalOnBackdrop(event)">
                <div class="image-viewer-container" style="position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                    <img id="modalImage" src="" alt="" style="max-height: 90vh; max-width: 90vw; object-fit: contain; transition: none; cursor: grab; user-select: none; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);" draggable="false" onclick="event.stopPropagation()">
                </div>
            </div>
            <div class="modal-footer" style="background: rgba(0, 0, 0, 0.8); border-top: 1px solid rgba(255, 255, 255, 0.1); position: absolute; bottom: 0; left: 0; right: 0; z-index: 1050; justify-content: center; padding: 1rem;">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-light btn-sm" onclick="zoomImage(-0.3)" title="Alejar">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <button type="button" class="btn btn-outline-light btn-sm" onclick="resetImageView()" title="Ajustar a pantalla">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-light btn-sm" onclick="zoomImage(0.3)" title="Acercar">
                        <i class="fas fa-search-plus"></i>
                    </button>
                    <button type="button" class="btn btn-outline-light btn-sm" onclick="rotateImage()" title="Rotar 90°">
                        <i class="fas fa-redo"></i>
                    </button>
                    <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal" title="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentZoom = 1;
let currentRotation = 0;
let isDragging = false;
let startX, startY, translateX = 0, translateY = 0;
let imageLoaded = false;

function openImageModal(imageUrl, title) {
    const img = document.getElementById('modalImage');
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    
    img.src = imageUrl;
    document.getElementById('imageModalLabel').textContent = title;
    
    img.onload = function() {
        imageLoaded = true;
        resetImageView();
    };
    
    modal.show();
}

function zoomImage(delta) {
    if (!imageLoaded) return;
    
    const newZoom = currentZoom + delta;
    if (newZoom < 0.2) {
        currentZoom = 0.2;
    } else if (newZoom > 8) {
        currentZoom = 8;
    } else {
        currentZoom = newZoom;
    }
    
    updateImageTransform();
    updateCursor();
}

function rotateImage() {
    if (!imageLoaded) return;
    
    currentRotation = (currentRotation + 90) % 360;
    updateImageTransform();
}

function resetImageView() {
    currentZoom = 1;
    currentRotation = 0;
    translateX = 0;
    translateY = 0;
    updateImageTransform();
    updateCursor();
}

function updateImageTransform() {
    const img = document.getElementById('modalImage');
    img.style.transform = `translate(${translateX}px, ${translateY}px) scale(${currentZoom}) rotate(${currentRotation}deg)`;
}

function updateCursor() {
    const img = document.getElementById('modalImage');
    img.style.cursor = currentZoom > 1 ? 'grab' : 'default';
}

function closeModalOnBackdrop(event) {
    if (event.target === event.currentTarget) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('imageModal'));
        if (modal) {
            modal.hide();
        }
    }
}

// Funcionalidad de arrastre mejorada
document.addEventListener('DOMContentLoaded', function() {
    const modalImage = document.getElementById('modalImage');
    const imageModal = document.getElementById('imageModal');
    
    // Arrastre con mouse
    modalImage.addEventListener('mousedown', function(e) {
        if (currentZoom > 1 && imageLoaded) {
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
            modalImage.style.cursor = 'grabbing';
            e.preventDefault();
            e.stopPropagation();
        }
    });
    
    document.addEventListener('mousemove', function(e) {
        if (isDragging && imageLoaded) {
            e.preventDefault();
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            updateImageTransform();
        }
    });
    
    document.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            updateCursor();
        }
    });
    
    // Zoom con rueda del mouse mejorado
    modalImage.addEventListener('wheel', function(e) {
        if (!imageLoaded) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        const delta = e.deltaY > 0 ? -0.2 : 0.2;
        zoomImage(delta);
    });
    
    // Soporte táctil para dispositivos móviles
    let initialDistance = 0;
    let initialZoom = 1;
    
    modalImage.addEventListener('touchstart', function(e) {
        if (!imageLoaded) return;
        
        if (e.touches.length === 2) {
            // Pinch to zoom
            const touch1 = e.touches[0];
            const touch2 = e.touches[1];
            initialDistance = Math.hypot(
                touch2.clientX - touch1.clientX,
                touch2.clientY - touch1.clientY
            );
            initialZoom = currentZoom;
        } else if (e.touches.length === 1 && currentZoom > 1) {
            // Arrastre táctil
            isDragging = true;
            const touch = e.touches[0];
            startX = touch.clientX - translateX;
            startY = touch.clientY - translateY;
        }
        e.preventDefault();
    });
    
    modalImage.addEventListener('touchmove', function(e) {
        if (!imageLoaded) return;
        
        if (e.touches.length === 2) {
            // Pinch to zoom
            const touch1 = e.touches[0];
            const touch2 = e.touches[1];
            const currentDistance = Math.hypot(
                touch2.clientX - touch1.clientX,
                touch2.clientY - touch1.clientY
            );
            const scale = currentDistance / initialDistance;
            currentZoom = Math.max(0.2, Math.min(8, initialZoom * scale));
            updateImageTransform();
        } else if (e.touches.length === 1 && isDragging) {
            // Arrastre táctil
            const touch = e.touches[0];
            translateX = touch.clientX - startX;
            translateY = touch.clientY - startY;
            updateImageTransform();
        }
        e.preventDefault();
    });
    
    modalImage.addEventListener('touchend', function(e) {
        isDragging = false;
        updateCursor();
    });
    
    // Teclas de acceso rápido
    document.addEventListener('keydown', function(e) {
        if (!imageModal.classList.contains('show') || !imageLoaded) return;
        
        switch(e.key) {
            case 'Escape':
                bootstrap.Modal.getInstance(imageModal).hide();
                break;
            case '+':
            case '=':
                e.preventDefault();
                zoomImage(0.3);
                break;
            case '-':
                e.preventDefault();
                zoomImage(-0.3);
                break;
            case '0':
                e.preventDefault();
                resetImageView();
                break;
            case 'r':
            case 'R':
                e.preventDefault();
                rotateImage();
                break;
        }
    });
    
    // Reset cuando se cierra el modal
    imageModal.addEventListener('hidden.bs.modal', function() {
        imageLoaded = false;
        resetImageView();
    });
    
    // Prevenir el menú contextual en la imagen
    modalImage.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });
});
</script>

<style>
.timeline-container {
    scrollbar-width: thin;
    scrollbar-color: #6c757d #f8f9fa;
}

.timeline-container::-webkit-scrollbar {
    width: 6px;
}

.timeline-container::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 3px;
}

.timeline-container::-webkit-scrollbar-thumb {
    background: #6c757d;
    border-radius: 3px;
}

.timeline-container::-webkit-scrollbar-thumb:hover {
    background: #495057;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transition: box-shadow 0.15s ease-in-out;
}

.accordion-button:not(.collapsed) {
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: transparent;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    isolation: isolate;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.075);
}

.badge {
    font-weight: 500;
}

.btn {
    font-weight: 500;
}

.btn:hover {
    transform: translateY(-1px);
    transition: transform 0.15s ease-in-out;
}

/* Dark theme styles */
[data-bs-theme="dark"] .timeline-container {
    scrollbar-color: #adb5bd #343a40;
}

[data-bs-theme="dark"] .timeline-container::-webkit-scrollbar-track {
    background: #343a40;
}

[data-bs-theme="dark"] .timeline-container::-webkit-scrollbar-thumb {
    background: #adb5bd;
}

[data-bs-theme="dark"] .timeline-container::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
}

[data-bs-theme="dark"] .nav-tabs .nav-link:hover {
    border-color: #495057 #495057 #343a40;
}

[data-bs-theme="dark"] .nav-tabs .nav-link.active {
    color: #e9ecef;
    background-color: #212529;
    border-color: #495057 #495057 #212529;
}

[data-bs-theme="dark"] .table-hover tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.075);
}

[data-bs-theme="dark"] .table-light {
    background-color: #343a40 !important;
    color: #e9ecef !important;
}

[data-bs-theme="dark"] .table {
    color: #e9ecef;
}

[data-bs-theme="dark"] .table th,
[data-bs-theme="dark"] .table td {
    border-color: #495057;
}

@media (max-width: 768px) {
    .header-title {
        font-size: 2rem !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .col-md-3 .p-3 {
        margin-top: 1rem;
    }
}
</style>
@endsection