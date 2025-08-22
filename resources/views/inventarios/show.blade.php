@extends('layouts.app')

@section('title', 'Detalle del Equipo - ' . $inventario->nombre)

@section('content')
<div id="top"></div>
<div class="container-fluid py-2">

    
    <!-- Contenedor Bootstrap principal -->
    <div class="container-fluid px-0">
        <!-- Header Section -->
        <div class="row mb-4 mx-0">
            <div class="col-12 px-0">
                <div class="card shadow-sm">
                    <div class="card-header position-relative" style="background: #ffffff; padding: 1.5rem; padding-bottom: 1.5rem;">
                        
                        <!-- Botones de Acción -->
                        <div class="position-absolute" style="top: 10px; right: 15px; z-index: 10;">
                            <div class="d-flex gap-2">
                                @if(auth()->user()->role->name === 'administrador' || auth()->user()->role->name === 'almacenista')
                                    <a href="{{ route('inventarios.edit', $inventario->id) }}" class="quick-action-btn text-decoration-none" style="border-radius: 10px; padding: 8px 16px; font-weight: 500; background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; transition: all 0.3s ease; font-size: 0.9rem;">
                                        <i class="fas fa-edit me-2" style="color: #007bff;"></i>Editar
                                    </a>
                                @endif
                                <a href="{{ route('inventarios.index') }}" id="volverInventarioBtn" class="quick-action-btn text-decoration-none" style="border-radius: 10px; padding: 8px 16px; font-weight: 500; background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; transition: all 0.3s ease; font-size: 0.9rem;">
                                    <i class="fas fa-arrow-left me-2" style="color: #6c757d;"></i>Volver
                                </a>
                            </div>
                        </div>
                        
                        <!-- QR Code Centrado -->
                        <div class="position-absolute" style="top: 10px; left: 50%; transform: translateX(-50%); z-index: 10;">
                            @if($inventario->qr_code)
                                <img src="{{ asset('storage/' . $inventario->qr_code) }}" alt="QR Personalizado" style="width: 120px; height: 120px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                            @else
                                @php
                                    $nasUrl = 'http://nas.empresa.local/documentos/inventario/' . $inventario->id;
                                @endphp
                                <div id="qrcode-header-{{ $inventario->id }}" style="width: 120px; height: 120px;"></div>
                            @endif
                        </div>
                        
                        <!-- Fila Principal del Header -->
        <div class="row align-items-center">
                            <!-- Columna Izquierda: Información Principal -->
                            <div class="col-lg-6 col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h1 class="mb-0" style="color: #212529; font-size: 2.25rem; font-weight: 700; line-height: 1.2;">{{ $inventario->nombre }}</h1>
                                        
                                        <!-- Contenedor móvil para botones y QR (solo visible en móviles) -->
                                        <div class="mobile-actions d-none">
                                            <div class="d-flex gap-2">
                                                @if(auth()->user()->role->name === 'administrador' || auth()->user()->role->name === 'almacenista')
                                                    <a href="{{ route('inventarios.edit', $inventario->id) }}" class="quick-action-btn text-decoration-none" style="border-radius: 10px; padding: 8px 16px; font-weight: 500; background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; transition: all 0.3s ease; font-size: 0.9rem;">
                                                        <i class="fas fa-edit me-2" style="color: #007bff;"></i>Editar
                                                    </a>
                                                @endif
                                                <a href="{{ route('inventarios.index') }}" id="volverInventarioBtn" class="quick-action-btn text-decoration-none" style="border-radius: 10px; padding: 8px 16px; font-weight: 500; background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; transition: all 0.3s ease; font-size: 0.9rem;">
                                                    <i class="fas fa-arrow-left me-2" style="color: #6c757d;"></i>Volver
                                                </a>
                                            </div>
                                            <div>
                                                @if($inventario->qr_code)
                                                    <img src="{{ asset('storage/' . $inventario->qr_code) }}" alt="QR Personalizado" style="width: 80px; height: 80px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                                                @else
                                                    @php
                                                        $nasUrl = 'http://nas.empresa.local/documentos/inventario/' . $inventario->id;
                                                    @endphp
                                                    <div id="qrcode-mobile-{{ $inventario->id }}" style="width: 80px; height: 80px;"></div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Columna Derecha: Estados -->
                            <div class="col-lg-6 col-md-6">
                                <div class="text-end">
                                    @php
                                        // Obtener todos los estados únicos de las ubicaciones
                                        $estadosUnicos = $ubicaciones ? $ubicaciones->pluck('estado')->filter()->unique()->values() : collect(['disponible']);
                                        
                                        // Función para obtener configuración de estado
                                        function getStatusConfig($estado) {
                                            return match($estado) {
                                                'disponible' => ['bg' => '#d4edda', 'color' => '#155724', 'icon' => 'fas fa-check-circle', 'iconColor' => '#28a745', 'text' => 'Disponible'],
                                                'en uso' => ['bg' => '#cce7ff', 'color' => '#004085', 'icon' => 'fas fa-user-clock', 'iconColor' => '#007bff', 'text' => 'En Uso'],
                                                'en mantenimiento' => ['bg' => '#fff3cd', 'color' => '#856404', 'icon' => 'fas fa-tools', 'iconColor' => '#ffc107', 'text' => 'Mantenimiento'],
                                                'dado de baja' => ['bg' => '#f8d7da', 'color' => '#721c24', 'icon' => 'fas fa-times-circle', 'iconColor' => '#dc3545', 'text' => 'Dado de Baja'],
                                                'robado' => ['bg' => '#e2e3e5', 'color' => '#383d41', 'icon' => 'fas fa-exclamation-triangle', 'iconColor' => '#6c757d', 'text' => 'Robado'],
                                                default => ['bg' => '#f8f9fa', 'color' => '#495057', 'icon' => 'fas fa-question-circle', 'iconColor' => '#6c757d', 'text' => ucfirst(str_replace('_', ' ', $estado))]
                                            };
                                        }
                                    @endphp
                                    
                                    @if($estadosUnicos->count() == 1)
                                        @php $statusConfig = getStatusConfig($estadosUnicos->first()); @endphp
                                        <div class="d-inline-flex align-items-center px-3 py-2 rounded-pill status-badge" style="background-color: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['color'] }}; font-size: 0.9rem; gap: 0.5rem; border: 2px solid {{ $statusConfig['iconColor'] }}; font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                            <i class="{{ $statusConfig['icon'] }}" style="font-size: 1rem; color: {{ $statusConfig['iconColor'] }};"></i>
                                            {{ $statusConfig['text'] }}
                                        </div>
                                    @else
                                        <!-- Múltiples estados -->
                                        <div class="d-flex flex-wrap justify-content-end gap-1">
                                            @foreach($estadosUnicos as $estado)
                                                @php $statusConfig = getStatusConfig($estado); @endphp
                                                <span class="badge d-flex align-items-center px-2 py-1 rounded-pill" style="background-color: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['color'] }}; font-size: 0.75rem; gap: 0.3rem; border: 1px solid {{ $statusConfig['iconColor'] }}; font-weight: 600;">
                                                    <i class="{{ $statusConfig['icon'] }}" style="font-size: 0.8rem; color: {{ $statusConfig['iconColor'] }};"></i>
                                                    {{ $statusConfig['text'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                

                            </div>

                        </div>
                        

                    </div>
                    
                    <!-- Línea divisora debajo del QR -->
                    <div style="border-bottom: 1px solid #dee2e6;"></div>
                    
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
                            
                            /* Sección de botones de acción para tema oscuro */
                            [data-bs-theme="dark"] .card-body:has(.btn-primary) {
                                background-color: #334155 !important;
                                border-color: #475569 !important;
                            }
                            
                            [data-bs-theme="dark"] .btn-outline-primary {
                                background-color: transparent;
                                border-color: #3b82f6;
                                color: #3b82f6;
                            }
                            
                            [data-bs-theme="dark"] .btn-outline-primary:hover {
                                background-color: #3b82f6;
                                border-color: #3b82f6;
                                color: #ffffff;
                            }
                        </style>
                        <div class="row g-4">
                            <!-- Information Section (Left Column) -->
                            <div class="col-xl-8 col-lg-7">
                                <!-- Equipment Details -->
                                <div class="row g-3 mb-4">
                                    <!-- Primera fila: Categoría, Marca y Serial -->
                                    <div class="col-lg-4 col-md-6">
                                        <div class="d-flex align-items-center p-3 info-card" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; height: 100%;">
                                            <div class="me-3">
                                                <i class="fas fa-layer-group" style="font-size: 1.5rem; color: #28a745;"></i>
                                            </div>
                                            <div>
                                                <small style="color: #6c757d; font-size: 0.8rem;">Categoría</small>
                                                <div class="info-value" style="color: #212529; font-size: 0.9rem; font-weight: 600;">{{ $inventario->categoria->nombre ?? 'Sin categoría' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="d-flex align-items-center p-3 info-card" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; height: 100%;">
                                            <div class="me-3">
                                                <i class="fas fa-tag" style="font-size: 1.5rem; color: #6f42c1;"></i>
                                            </div>
                                            <div>
                                                <small style="color: #6c757d; font-size: 0.8rem;">Marca</small>
                                                <div class="info-value" style="color: #212529; font-size: 0.9rem; font-weight: 600;">{{ $inventario->marca ?? 'No especificada' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-12">
                                        <div class="d-flex align-items-center p-3 info-card" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; height: 100%;">
                                            <div class="me-3">
                                                <i class="fas fa-hashtag" style="font-size: 1.5rem; color: #fd7e14;"></i>
                                            </div>
                                            <div>
                                                <small style="color: #6c757d; font-size: 0.8rem;">Serial</small>
                                                <div class="info-value" style="color: #212529; font-size: 0.9rem; font-weight: 600;">{{ $inventario->numero_serie ?? 'No especificado' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Segunda fila: Ubicaciones ocupando todo el ancho -->
                                    <div class="col-12">
                                        <div class="p-4 info-card" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #17a2b8; border-radius: 50%; color: white;">
                                                    <i class="fas fa-map-marker-alt" style="font-size: 1.2rem;"></i>
                                                </div>
                                                <div>
                                                    <h5 style="color: #212529; font-size: 1.1rem; font-weight: 600; margin: 0;">Distribución por Ubicaciones</h5>
                                                    <div class="info-value" style="color: #6c757d; font-size: 0.9rem; font-weight: 500; margin-top: 2px;">{{ $ubicaciones ? $ubicaciones->count() : 0 }} ubicación(es) asignada(s)</div>
                                                </div>
                                            </div>
                                            @if($ubicaciones && $ubicaciones->count() > 0)
                                                <div class="row g-3">
                                                    @foreach($ubicaciones as $ubicacion)
                                                        <div class="col-12 col-sm-6 col-lg-4">
                                                            <div class="d-flex align-items-center justify-content-between p-3" style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; min-height: 70px; transition: all 0.2s ease; overflow: hidden;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                                    <div class="flex-grow-1" style="min-width: 0; overflow: hidden;">
                                                        <div style="font-weight: 600; color: #212529; font-size: 0.95rem; line-height: 1.3; word-break: break-word; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis;">
                                                            <i class="fas fa-building me-2" style="color: #17a2b8; font-size: 0.85rem;"></i>{{ $ubicacion->nombre }}
                                                        </div>
                                                        <div style="color: #6c757d; font-size: 0.8rem; line-height: 1.2; font-weight: 500; overflow: hidden; text-overflow: ellipsis;">
                                                            <i class="fas fa-cubes me-1" style="color: #28a745; font-size: 0.75rem;"></i>Cantidad: <strong>{{ $ubicacion->pivot->cantidad }}</strong>
                                                        </div>
                                                    </div>
                                                                <div class="ms-2 flex-shrink-0" style="max-width: 120px;">
                                                                    @php
                                                                        $estadoConfig = match($ubicacion->pivot->estado) {
                                                                            'disponible' => ['bg' => '#28a745', 'text' => 'Disponible'],
                                                                            'en uso' => ['bg' => '#ffc107', 'text' => 'En Uso'],
                                                                            'en mantenimiento' => ['bg' => '#6c757d', 'text' => 'Mantenimiento'],
                                                                            'dado de baja' => ['bg' => '#dc3545', 'text' => 'Dado de Baja'],
                                                                            'robado' => ['bg' => '#343a40', 'text' => 'Robado'],
                                                                            default => ['bg' => '#6c757d', 'text' => ucfirst(str_replace('_', ' ', $ubicacion->pivot->estado))]
                                                                        };
                                                                    @endphp
                                                                    <span class="badge" style="background-color: {{ $estadoConfig['bg'] }}; color: white; font-size: 0.85rem; padding: 6px 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; display: inline-block; font-weight: 600; border-radius: 6px;">
                                                                        {{ $estadoConfig['text'] }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-4">
                                                    <div class="p-4" style="background-color: #f8f9fa; border: 1px dashed #dee2e6; border-radius: 8px;">
                                                        <i class="fas fa-map-marker-alt mb-3" style="font-size: 2rem; color: #6c757d; opacity: 0.6;"></i>
                                                        <h6 style="color: #6c757d; font-size: 0.95rem; font-weight: 600; margin: 0;">Sin ubicaciones asignadas</h6>
                                                        <p style="color: #adb5bd; font-size: 0.8rem; margin: 8px 0 0 0;">Este elemento aún no ha sido asignado a ninguna ubicación</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Tercera fila: Código y Valor -->
                                <div class="row g-3 mb-4">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="d-flex align-items-center p-3 info-card" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; height: 100%;">
                                            <div class="me-3">
                                                <i class="fas fa-barcode" style="font-size: 1.5rem; color: #007bff;"></i>
                                            </div>
                                            <div>
                                                <small style="color: #6c757d; font-size: 0.8rem;">Código</small>
                                                <div class="info-value" style="color: #212529; font-size: 0.9rem; font-weight: 600;">{{ $inventario->codigo_unico ?? $inventario->codigo }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="d-flex align-items-center p-3 info-card" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; height: 100%;">
                                            <div class="me-3">
                                                <i class="fas fa-dollar-sign" style="font-size: 1.5rem; color: #ffc107;"></i>
                                            </div>
                                            <div>
                                                <small style="color: #6c757d; font-size: 0.8rem;">Valor</small>
                                                <div class="info-value" style="color: #212529; font-size: 0.9rem; font-weight: 600;">${{ number_format($inventario->valor_unitario ?? 0, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                

                            </div>
                            
                            <!-- Right Column: Image and QR -->
                            <div class="col-xl-4 col-lg-5">
                                <!-- Main Image -->
                                <div class="mb-3">
                                    @php
                                        $imageUrl = null;
                                        if($inventario->imagen_principal && file_exists(storage_path('app/public/' . $inventario->imagen_principal))) {
                                            $imageUrl = asset('storage/' . $inventario->imagen_principal);
                                        } elseif($inventario->getFirstMediaUrl('imagenes') && $inventario->getMedia('imagenes')->count() > 0) {
                                            $imageUrl = $inventario->getFirstMediaUrl('imagenes');
                                        }
                                    @endphp
                                    
                                    @if($imageUrl)
                                        <div class="image-container position-relative" style="border: 2px solid #e9ecef; border-radius: 12px; overflow: hidden; background-color: #ffffff; height: 600px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'" onclick="openImageModal('{{ $imageUrl }}', '{{ $inventario->nombre }}')">
                                            <img src="{{ $imageUrl }}" alt="{{ $inventario->nombre }}" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center no-image-placeholder" style="height: 600px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 2px dashed #dee2e6; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                            <div class="text-center">
                                                <i class="fas fa-image" style="color: #adb5bd; font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                                <h4 style="color: #6c757d; font-size: 1rem; margin-bottom: 0.25rem;">Sin imagen</h4>
                                                <p style="color: #adb5bd; font-size: 0.8rem; margin: 0;">No hay imagen disponible</p>
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
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Tipo de Propiedad</span>
                                                <div class="d-flex align-items-center">
                                                    @if($inventario->tipo_propiedad == 'alquiler')
                                                        <i class="fas fa-handshake me-2" style="color: #ffc107; font-size: 0.9rem;"></i>
                                                        <span style="color: #000; font-size: 0.85rem; font-weight: 500; line-height: 1;">ALQUILER</span>
                                                    @else
                                                        <i class="fas fa-home me-2" style="color: #28a745; font-size: 0.9rem;"></i>
                                                        <span style="color: #000; font-size: 0.85rem; font-weight: 500; line-height: 1;">PROPIO</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="info-value" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Proveedor</span>
                                                <span class="info-value" style="color: #212529; font-size: 0.9rem;">{{ $inventario->proveedor->nombre ?? 'No especificado' }}</span>
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

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Contenedor 4: Bitácora de Observaciones -->
        <div class="row mb-4 mx-0">
            <div class="col-12 px-0">
                @php
                    $observacionesArray = [];
                    if($inventario->observaciones) {
                        // Limpiar caracteres especiales pero preservar saltos de línea
                        $texto = str_replace(['_x000D_'], '', $inventario->observaciones);
                        $texto = str_replace(['\r\n', '\r', '\n'], '\n', $texto);
                        
                        // Verificar si las observaciones tienen formato de fecha (bitácora histórica)
                        $tieneFechas = preg_match('/\d{1,2}\/\d{1,2}\/\d{4}/', $texto);
                        
                        if($tieneFechas) {
                            // Procesamiento para observaciones con formato de fecha
                            $observacionesArray = preg_split('/[\n\r]*\*(?=\d{1,2}\/\d{1,2}\/\d{4})/', $texto);
                            
                            // Si no funciona, intentar dividir por saltos de línea y luego filtrar
                            if(count($observacionesArray) <= 1) {
                                $lineas = explode('\n', $texto);
                                $observacionesArray = [];
                                $observacionActual = '';
                                
                                foreach($lineas as $linea) {
                                    $linea = trim($linea);
                                    if(empty($linea)) continue;
                                    
                                    // Si la línea empieza con * y tiene fecha, es una nueva observación
                                    if(preg_match('/^\*?\d{1,2}\/\d{1,2}\/\d{4}/', $linea)) {
                                        if(!empty($observacionActual)) {
                                            $observacionesArray[] = $observacionActual;
                                        }
                                        $observacionActual = ltrim($linea, '*');
                                    } else {
                                        // Continuar la observación actual
                                        if(!empty($observacionActual)) {
                                            $observacionActual .= ' ' . $linea;
                                        }
                                    }
                                }
                                
                                // Agregar la última observación
                                if(!empty($observacionActual)) {
                                    $observacionesArray[] = $observacionActual;
                                }
                            }
                            
                            // Limpiar y filtrar observaciones con fecha
                            $observacionesArray = array_map(function($obs) {
                                $obs = trim($obs);
                                $obs = ltrim($obs, '*'); // Remover asterisco inicial si existe
                                $obs = preg_replace('/\s+/', ' ', $obs); // Normalizar espacios
                                return trim($obs);
                            }, $observacionesArray);
                            
                            // Filtrar observaciones válidas (que tengan fecha y contenido)
                            $observacionesArray = array_filter($observacionesArray, function($obs) {
                                return !empty(trim($obs)) && preg_match('/\d{1,2}\/\d{1,2}\/\d{4}/', $obs) && strlen(trim($obs)) > 10;
                            });
                            
                            // Remover duplicados
                            $observacionesArray = array_unique($observacionesArray);
                            
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
                        } else {
                            // Procesamiento para observaciones simples (sin fechas)
                            // Dividir por saltos de línea y limpiar
                            $lineas = explode('\n', $texto);
                            $observacionesArray = array_map(function($linea) {
                                return trim($linea);
                            }, $lineas);
                            
                            // Filtrar líneas vacías
                            $observacionesArray = array_filter($observacionesArray, function($obs) {
                                return !empty(trim($obs));
                            });
                            
                            // Si solo hay una línea, tratarla como una observación única
                            if(count($observacionesArray) <= 1 && !empty(trim($texto))) {
                                $observacionesArray = [trim($texto)];
                            }
                        }
                    }
                    $totalObservaciones = count($observacionesArray);
                @endphp
                
                <div class="accordion" id="bitacoraAccordion">
                    <div class="accordion-item" style="border-radius: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: none;">
                        <h2 class="accordion-header" id="headingBitacora">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBitacora" aria-expanded="false" aria-controls="collapseBitacora" style="background-color: #f8f9fa; border-radius: 15px; border: none; padding: 1.5rem 2rem;">
                                <div class="d-flex align-items-center w-100">
                                    <div class="me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background-color: rgba(108,117,125,0.1); border-radius: 50%; color: #007bff;">
                                        <i class="fas fa-history" style="font-size: 1.4rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h2 class="mb-1" style="color: #212529; font-size: 1.5rem; font-weight: 600;">Bitácora de Observaciones</h2>
                                        <p class="mb-0" style="color: #6c757d; font-size: 1rem;">Registro cronológico de eventos y observaciones</p>
                                    </div>
                                    <div class="d-flex align-items-center me-3">
                                        <span class="badge bg-primary" style="font-size: 0.9rem; padding: 8px 12px; border-radius: 20px;">{{ $totalObservaciones }} eventos</span>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapseBitacora" class="accordion-collapse collapse" aria-labelledby="headingBitacora" data-bs-parent="#bitacoraAccordion">
                            <div class="accordion-body" style="padding: 2rem; background-color: #f8f9fa;">
                        @if($totalObservaciones > 0)
                            <div class="accordion" id="observacionesAccordion">
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
                                        
                                    <div class="accordion-item" style="border: 1px solid #dee2e6; border-radius: 8px; margin-bottom: 8px; overflow: hidden;">
                                        <h2 class="accordion-header" id="heading{{ $index }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}" style="background-color: {{ $colorFondo }}; border: none; padding: 16px 20px;">
                                                <div class="d-flex align-items-center w-100">
                                                    <div class="me-3" style="width: 35px; height: 35px; background-color: {{ $color }}; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                                                        <i class="{{ $icono }}" style="font-size: 0.9rem;"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="mb-1" style="color: #212529; font-size: 1rem; font-weight: 600;">{{ $tipo }}</h6>
                                                                <small style="color: #6c757d; font-size: 0.85rem;">{{ $fechaFormateada }}</small>
                                                            </div>
                                                            <span class="badge" style="background-color: {{ $color }}; color: white; font-size: 0.75rem; padding: 4px 8px; border-radius: 12px; margin-right: 20px;">Evento #{{ $totalObservaciones - $index }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#observacionesAccordion">
                                            <div class="accordion-body" style="padding: 20px; background-color: white;">
                                                <!-- Descripción del evento -->
                                                <div class="event-description" style="background-color: {{ $colorFondo }}; border-radius: 8px; padding: 16px; border-left: 4px solid {{ $color }};">
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
                                                        <div class="mt-3 pt-3" style="border-top: 1px solid #dee2e6;">
                                                            <small class="text-muted" style="font-style: italic;"><i class="fas fa-clock me-1"></i>{{ $tiempoRelativo }}</small>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
                                $totalRegistros = $movimientos->count() + $inventario->mantenimientos->count();
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
                                </style>

                                <!-- Contenido de las pestañas -->
                                <div class="tab-content" id="historyTabsContent">
                                    <!-- Pestaña de Movimientos -->
                                    <div class="tab-pane fade show active" id="movements" role="tabpanel" aria-labelledby="movements-tab">
                                        
                                        <!-- Botón Nuevo Movimiento -->
                                        @if(auth()->user()->role->name === 'administrador' || auth()->user()->role->name === 'almacenista')
                                            <div class="d-flex justify-content-end mb-3">
                                                <a href="{{ route('movimientos.create', ['inventario_id' => $inventario->id]) }}" class="quick-action-btn text-decoration-none" style="border-radius: 8px; padding: 12px 20px; background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; font-weight: 500; transition: all 0.3s ease;">
                                    <i class="fas fa-plus me-2" style="color: #007bff;"></i>Nuevo Movimiento
                                </a>
                                            </div>
                                        @endif
                                        
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
                                                            @if(auth()->user()->role->name === 'administrador')
                                                                <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center;">Acciones</th>
                                                            @endif
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
                                                            @if(auth()->user()->role->name === 'administrador')
                                                                <td style="padding: 16px; text-align: center;">
                                                                    <div class="btn-group" role="group">
                                                                        <a href="{{ route('movimientos.show', $movimiento) }}" class="btn btn-info btn-sm" style="border-radius: 6px 0 0 6px; padding: 8px 12px; font-size: 0.8rem;" title="Ver detalles">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                        <a href="{{ route('movimientos.edit', $movimiento) }}" class="btn btn-warning btn-sm" style="border-radius: 0; padding: 8px 12px; font-size: 0.8rem;" title="Editar">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                        <form action="{{ route('movimientos.destroy', $movimiento) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este movimiento?')">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 0 6px 6px 0; padding: 8px 12px; font-size: 0.8rem;" title="Eliminar">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            @endif
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
                                        
                                        <!-- Botón Nuevo Mantenimiento -->
                                        @if(auth()->user()->role->name === 'administrador' || auth()->user()->role->name === 'almacenista')
                                            <div class="d-flex justify-content-end mb-3">
                                                <a href="{{ route('mantenimientos.create', ['inventario_id' => $inventario->id]) }}" class="quick-action-btn text-decoration-none" style="border-radius: 8px; padding: 12px 20px; background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; font-weight: 500; transition: all 0.3s ease;">
                                    <i class="fas fa-plus me-2" style="color: #ffc107;"></i>Nuevo Mantenimiento
                                </a>
                                            </div>
                                        @endif
                                        
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
                                                            <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center; border-right: 1px solid #dee2e6;">Proveedor</th>
                                                            @if(auth()->user()->role->name === 'administrador' || auth()->user()->role->name === 'tecnico')
                                                                <th style="color: #212529; font-weight: 600; padding: 16px; text-align: center;">Acciones</th>
                                                            @endif
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
                                                            <td style="padding: 16px; text-align: center; vertical-align: middle; border-right: 1px solid #f1f3f4;">
                                                                <span style="color: #212529; font-size: 0.9rem;">
                                                                    {{ $mantenimiento->proveedor_servicio ?? 'No especificado' }}
                                                                </span>
                                                            </td>
                                                            @if(auth()->user()->role->name === 'administrador' || auth()->user()->role->name === 'tecnico')
                                                                <td style="padding: 16px; text-align: center; vertical-align: middle;">
                                                                    <div class="btn-group" role="group">
                                                                        <a href="{{ route('mantenimientos.show', $mantenimiento) }}" class="btn btn-info btn-sm" style="border-radius: 6px 0 0 6px; padding: 8px 12px; font-size: 0.8rem;" title="Ver detalles">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                        <a href="{{ route('mantenimientos.edit', $mantenimiento) }}" class="btn btn-warning btn-sm" style="border-radius: 0; padding: 8px 12px; font-size: 0.8rem;" title="Editar">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                        <form action="{{ route('mantenimientos.destroy', $mantenimiento) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este mantenimiento?')">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 0 6px 6px 0; padding: 8px 12px; font-size: 0.8rem;" title="Eliminar">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            @endif
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


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de enlace de documentación (si existe) -->
            @if($inventario->enlace_documentacion)
            <div class="row mb-4 mx-0">
                <div class="col-12 px-0">
                    <div class="card" style="border: 1px solid #dee2e6; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 12px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                        <div class="card-header" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 12px 12px 0 0; padding: 20px; border: none;">
                            <div class="d-flex align-items-center">
                                <div style="background-color: rgba(255,255,255,0.2); border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                                    <i class="fas fa-external-link-alt" style="color: white; font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <h4 style="margin: 0; font-weight: 700; font-size: 1.4rem;">Enlace de Documentación</h4>
                                    <p style="margin: 0; opacity: 0.9; font-size: 0.9rem;">Acceso directo a documentación externa</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="padding: 30px;">
                            <div class="text-center">
                                <a href="{{ $inventario->enlace_documentacion }}" target="_blank" class="btn btn-primary" style="font-weight: 600; padding: 12px 24px; border-radius: 8px; font-size: 0.9rem; width: 100%; max-width: 300px;">
                                    <i class="fas fa-external-link-alt me-2" style="font-size: 0.8rem;"></i>Acceder a Documentación
                                </a>
                                <small class="text-muted d-block mt-2" style="word-break: break-all;">{{ $inventario->enlace_documentacion }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

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

/* Responsividad mejorada para el layout principal */
@media (min-width: 1610px) {
    /* Mantener layout de dos columnas para resoluciones mayores a 1609px */
    .row.g-4 > .col-xl-6 {
        flex: 0 0 50%;
        max-width: 50%;
        min-height: auto;
    }
    
    .image-container,
    .no-image-placeholder {
        height: auto !important;
        min-height: 400px !important;
        max-height: 600px !important;
        aspect-ratio: 4/3;
    }
    
    .image-container img {
        width: 100%;
        height: auto !important;
        min-height: 400px !important;
        max-height: 600px !important;
        object-fit: contain;
        aspect-ratio: 4/3;
    }
    
    /* Ajustar header para evitar superposición del QR en resoluciones muy grandes */
    .card-header {
        padding-bottom: 120px !important;
    }
    
    /* Mantener botones en posición segura */
    .position-absolute[style*="right: 15px"] {
        top: 20px !important;
        right: 25px !important;
    }
    
    /* Mantener QR centrado */
    .position-absolute[style*="left: 50%"] {
        top: 20px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
    }
    
    /* Mantener tamaño estándar del QR en resoluciones muy grandes */
    .position-absolute img,
    .position-absolute div[id*="qrcode"] {
        width: 120px !important;
        height: 120px !important;
    }
}

/* Breakpoint para resoluciones grandes (1345px - 1609px) */
@media (max-width: 1609px) and (min-width: 1345px) {
    /* Mantener layout horizontal pero ajustar proporciones */
    .row.g-4 > .col-xl-8 {
        flex: 0 0 65%;
        max-width: 65%;
    }
    
    .row.g-4 > .col-xl-4 {
        flex: 0 0 35%;
        max-width: 35%;
    }
    
    /* Ajustar grid de información */
    .col-lg-4 {
        flex: 0 0 50%;
        max-width: 50%;
    }
    
    .col-lg-4:nth-child(3) {
        flex: 0 0 100%;
        max-width: 100%;
        margin-top: 1rem;
    }
    
    /* Ajustar imagen */
    .image-container,
    .no-image-placeholder {
        min-height: 400px !important;
        max-height: 500px;
    }
    
    .image-container img {
        min-height: 400px !important;
        max-height: 500px !important;
    }
    
    /* Ajustar header para evitar superposición del QR */
    .card-header {
        padding-bottom: 120px !important;
    }
    
    /* Ajustar posición del QR para evitar superposición */
    .position-absolute[style*="left: 50%"] {
        top: 15px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
    }
    
    /* Ajustar tamaño del QR en estas resoluciones */
    .position-absolute img,
    .position-absolute div[id*="qrcode"] {
        width: 110px !important;
        height: 110px !important;
    }
}

/* Breakpoint para resoluciones medianas-altas (1189px - 1344px) */
@media (max-width: 1344px) and (min-width: 1189px) {
    /* Mantener layout horizontal pero ajustar proporciones */
    .row.g-4 > .col-xl-8 {
        flex: 0 0 70%;
        max-width: 70%;
    }
    
    .row.g-4 > .col-xl-4 {
        flex: 0 0 30%;
        max-width: 30%;
    }
    
    /* Ajustar grid de información */
    .col-lg-4 {
        flex: 0 0 50%;
        max-width: 50%;
    }
    
    .col-lg-4:nth-child(3) {
        flex: 0 0 100%;
        max-width: 100%;
        margin-top: 1rem;
    }
    
    /* Ajustar imagen */
    .image-container,
    .no-image-placeholder {
        min-height: 380px !important;
        max-height: 480px;
    }
    
    .image-container img {
        min-height: 380px !important;
        max-height: 480px !important;
    }
    
    /* Ajustar header */
    .card-header {
        padding-bottom: 110px !important;
    }
    
    /* Ajustar tamaño del QR */
    .position-absolute img,
    .position-absolute div[id*="qrcode"] {
        width: 105px !important;
        height: 105px !important;
    }
}

/* Breakpoint crítico para resoluciones medianas (992px - 1188px) */
@media (max-width: 1188px) and (min-width: 992px) {
    /* Cambiar a layout vertical */
    .row.g-4 > .col-xl-8,
    .row.g-4 > .col-xl-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    /* Reorganizar información en 2 columnas */
    .col-lg-4 {
        flex: 0 0 50%;
        max-width: 50%;
        margin-bottom: 1rem;
    }
    
    .col-lg-4:nth-child(odd) {
        padding-right: 0.75rem;
    }
    
    .col-lg-4:nth-child(even) {
        padding-left: 0.75rem;
    }
    
    /* Ajustar imagen para layout vertical */
    .image-container,
    .no-image-placeholder {
        min-height: 350px !important;
        max-height: 450px;
        margin-bottom: 2rem;
    }
    
    .image-container img {
        min-height: 350px !important;
        max-height: 450px !important;
    }
    
    /* Ajustar ubicaciones */
    .col-12.col-sm-6.col-lg-4 {
        flex: 0 0 50%;
        max-width: 50%;
    }
    
    /* Ajustar header */
    .card-header {
        padding-bottom: 80px !important;
    }
    
    /* Ajustar botones en header */
    .position-absolute[style*="right: 15px"] {
        top: 15px !important;
        right: 20px !important;
    }
    
    /* Ajustar QR centrado en header */
    .position-absolute[style*="left: 50%"] {
        top: 15px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
    }
    
    .position-absolute img,
    .position-absolute div[id*="qrcode"] {
        width: 100px !important;
        height: 100px !important;
    }
}

/* Breakpoint para tablets grandes (850px - 991px) */
@media (max-width: 991px) and (min-width: 850px) {
    /* Layout completamente vertical */
    .row.g-4 > .col-xl-8,
    .row.g-4 > .col-xl-4,
    .col-lg-4,
    .col-lg-6 {
        flex: 0 0 100% !important;
        max-width: 100% !important;
        margin-bottom: 1rem;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
    
    /* Imagen más pequeña */
    .image-container,
    .no-image-placeholder {
        min-height: 320px !important;
        max-height: 420px;
    }
    
    .image-container img {
        min-height: 320px !important;
        max-height: 420px !important;
    }
    
    /* Ubicaciones en una sola columna */
    .col-12.col-sm-6.col-lg-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    /* Ajustar header para tablets */
    .card-header {
        padding-bottom: 105px !important;
    }
    
    .position-absolute img,
    .position-absolute div[id*="qrcode"] {
        width: 95px !important;
        height: 95px !important;
    }
    
    /* Prevenir desbordamiento de texto */
    .info-value,
    .badge {
        word-wrap: break-word;
        overflow-wrap: break-word;
        max-width: 100%;
    }
    
    /* Ocultar el contenedor de botones y QR del header en tablets grandes */
    .position-absolute[style*="top: 10px"] {
        display: none !important;
    }
    
    /* Mostrar botones y QR debajo del título en tablets grandes */
    .mobile-actions {
        display: flex !important;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        margin-top: 0.75rem;
        margin-bottom: 0 !important;
        padding-top: 0.75rem;
        padding-bottom: 0 !important;
        border-top: 1px solid #e9ecef;
    }
    
    /* QR primero, centrado */
    .mobile-actions > div:last-child {
        order: 1;
        display: flex;
        justify-content: center;
    }
    
    /* Botones después del QR */
    .mobile-actions .d-flex {
        order: 2;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    /* Ajustar tamaño del QR para tablets grandes */
    .mobile-actions img,
    .mobile-actions div[id*="qrcode"] {
        width: 95px !important;
        height: 95px !important;
        margin: 0 auto;
    }
}

/* Breakpoint para tablets pequeñas (769px - 849px) */
@media (max-width: 849px) and (min-width: 769px) {
    /* Layout completamente vertical */
    .row.g-4 > .col-xl-8,
    .row.g-4 > .col-xl-4,
    .col-lg-4,
    .col-lg-6 {
        flex: 0 0 100% !important;
        max-width: 100% !important;
        margin-bottom: 1rem;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
    
    /* Imagen más pequeña */
    .image-container,
    .no-image-placeholder {
        min-height: 300px !important;
        max-height: 400px;
    }
    
    .image-container img {
        min-height: 300px !important;
        max-height: 400px !important;
    }
    
    /* Ubicaciones en una sola columna */
    .col-12.col-sm-6.col-lg-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    /* Ajustar header para tablets */
    .card-header {
        padding-bottom: 100px !important;
    }
    
    .position-absolute img,
    .position-absolute div[id*="qrcode"] {
        width: 90px !important;
        height: 90px !important;
    }
    
    /* Prevenir desbordamiento de texto */
    .info-value,
    .badge {
        word-wrap: break-word;
        overflow-wrap: break-word;
        max-width: 100%;
    }
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
    
    /* Layout móvil - todo en una columna */
    .col-md-4 {
        flex: 0 0 100% !important;
        max-width: 100% !important;
        margin-bottom: 0.75rem;
    }
    
    .image-container,
    .no-image-placeholder {
        min-height: 200px !important;
        max-height: 250px;
    }
    
    .image-container img {
        min-height: 200px !important;
        max-height: 250px !important;
    }
    
    /* Ajustes específicos para el header y QR en móviles */
    .card-header {
        padding: 0.75rem !important;
        padding-bottom: 0.75rem !important;
        position: relative;
        background: #ffffff !important;
    }
    
    /* Ocultar la columna derecha vacía en móviles */
    .card-header .row .col-lg-6.col-md-6:last-child {
        display: none !important;
    }
    
    /* Hacer que la columna izquierda ocupe todo el ancho */
    .card-header .row .col-lg-6.col-md-6:first-child {
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }
    
    /* Ajustar el row del header */
    .card-header .row {
        margin: 0 !important;
        gap: 0 !important;
    }
    
    /* Ocultar el contenedor de botones y QR del header en móviles */
    .position-absolute[style*="top: 10px"] {
        display: none !important;
    }
    
    /* Mostrar botones y QR debajo del título en móviles */
    .mobile-actions {
        display: flex !important;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        margin-top: 0.75rem;
        margin-bottom: 0 !important;
        padding-top: 0.75rem;
        padding-bottom: 0 !important;
        border-top: 1px solid #e9ecef;
    }
    
    /* QR primero, centrado */
    .mobile-actions > div:last-child {
        order: 1;
        display: flex;
        justify-content: center;
    }
    
    /* Botones después del QR */
    .mobile-actions .d-flex {
        order: 2;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    /* Ajustar tamaño del QR para móviles */
    .mobile-actions img,
    .mobile-actions div[id*="qrcode"] {
        width: 80px !important;
        height: 80px !important;
        margin: 0 auto;
    }
    
    /* Resetear y ajustar el contenedor principal */
    .card-header .col-lg-6.col-md-6:first-child {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Contenedor del icono y título - más específico */
    .card-header .d-flex.align-items-center {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Icono - selector más específico */
    .card-header .d-flex.align-items-center > div:first-child {
        width: 28px !important;
        height: 28px !important;
        min-width: 28px !important;
        margin-right: 0.4rem !important;
        flex-shrink: 0 !important;
    }
    
    .card-header .d-flex.align-items-center > div:first-child i {
        font-size: 0.95rem !important;
    }
    
    /* Título principal */
    .card-header .d-flex.align-items-center h1 {
        font-size: 1.3rem !important;
        margin-bottom: 0.2rem !important;
        line-height: 1.2 !important;
    }
    
    /* Contenedor del título y badges */
    .card-header .flex-grow-1 {
        min-width: 0 !important;
        overflow: hidden !important;
    }
    
    /* Ajustar botones para móviles */
    .quick-action-btn {
        padding: 6px 12px !important;
        font-size: 0.8rem !important;
    }
    
    /* Mejoras responsivas para móviles */
    .info-card {
        padding: 1rem !important;
    }
    
    .info-card h5 {
        font-size: 1rem !important;
    }
    
    .info-card .info-value {
        font-size: 0.85rem !important;
    }
    
    /* Ajustes para las tarjetas de ubicación */
    .col-12.col-sm-6.col-lg-4 {
        margin-bottom: 0.75rem;
    }
    
    .col-12.col-sm-6.col-lg-4 > div {
        min-height: 60px !important;
        padding: 0.75rem !important;
    }
    
    .col-12.col-sm-6.col-lg-4 .flex-grow-1 > div:first-child {
        font-size: 0.9rem !important;
    }
    
    .col-12.col-sm-6.col-lg-4 .flex-grow-1 > div:last-child {
        font-size: 0.75rem !important;
    }
    
    /* Ajustes para iconos en móviles */
    .me-3[style*="width: 40px"] {
        width: 35px !important;
        height: 35px !important;
    }
    
    .me-3[style*="width: 35px"] {
        width: 30px !important;
        height: 30px !important;
    }
    
    /* Badges más pequeños en móviles */
    .badge {
        font-size: 0.55rem !important;
        padding: 1px 4px !important;
    }
    
    /* Eliminar espaciado problemático de Bootstrap */
    .row.g-4 {
        margin: 0 !important;
        --bs-gutter-x: 0 !important;
        --bs-gutter-y: 0 !important;
    }
    
    .row.g-4 > * {
        padding: 0 0.5rem !important;
        margin-bottom: 0 !important;
    }
    
    /* Corregir alineación del header principal */
    .card-header .row {
        margin: 0 !important;
        --bs-gutter-x: 0 !important;
        --bs-gutter-y: 0 !important;
    }
    
    .card-header .row > * {
        padding: 0 !important;
    }
}

@media (max-width: 576px) {
    /* Ajustes específicos para pantallas muy pequeñas */
    .row.g-3 {
        margin: 0 -0.5rem;
    }
    
    .row.g-3 > * {
        padding: 0 0.5rem;
    }
    
    .card-body {
        padding: 0.75rem !important;
    }
    
    .info-card {
        padding: 0.75rem !important;
    }
    
    /* Hacer que las ubicaciones ocupen todo el ancho en móviles pequeños */
    .col-12.col-sm-6.col-lg-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    /* Ajustes adicionales para el header en pantallas muy pequeñas */
    .card-header {
        padding: 0.75rem !important;
        padding-bottom: 80px !important;
    }
    
    /* QR más pequeño en pantallas muy pequeñas */
    .position-absolute img,
    .position-absolute div[id*="qrcode"] {
        width: 70px !important;
        height: 70px !important;
    }
    
    /* Botones más compactos */
    .quick-action-btn {
        padding: 4px 8px !important;
        font-size: 0.75rem !important;
    }
    
    /* Ajustar gap entre elementos */
    .position-absolute .d-flex.flex-column {
        gap: 0.75rem !important;
    }
    
    /* Ajustar el espaciado del contenido */
    .d-flex.align-items-center.mb-4 {
        margin-bottom: 1rem !important;
    }
    
    .d-flex.align-items-center.mb-4 h5 {
        font-size: 0.95rem !important;
    }
    
    .d-flex.align-items-center.mb-4 .info-value {
        font-size: 0.8rem !important;
    }
}

/* Estilos para botones de acción rápida */
.quick-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
    border-radius: 8px;
    font-weight: 500;
}

.quick-action-btn:hover {
    background-color: #e9ecef !important;
    border-color: #adb5bd !important;
    color: #495057 !important;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.quick-action-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.quick-action-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
}
</style>

<script>

// Navegación simple usando historial del navegador
document.addEventListener('DOMContentLoaded', function() {
    const volverBtn = document.getElementById('volverInventarioBtn');
    
    if (volverBtn) {
        volverBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Verificar si venimos de la vista categoria
            if (sessionStorage.getItem('from_categoria_view') === 'true') {
                const categoriaReturnUrl = sessionStorage.getItem('categoria_return_url');
                if (categoriaReturnUrl) {
                    // Ir a la URL de categoria con filtros
                    window.location.href = categoriaReturnUrl;
                } else {
                    // Fallback: usar historial del navegador
                    history.back();
                }
                // Limpiar las marcas de categoria
                sessionStorage.removeItem('from_categoria_view');
                sessionStorage.removeItem('categoria_return_url');
            }
            // Verificar si venimos de la lista de inventarios
            else if (sessionStorage.getItem('from_inventarios_list') === 'true') {
                // Usar el historial del navegador para volver (mantiene el estado)
                history.back();
                // Limpiar la marca
                sessionStorage.removeItem('from_inventarios_list');
            } else {
                // Si no venimos de ninguna vista específica, ir directamente
                window.location.href = '/inventarios';
            }
        });
    }
    
    // Generar QR code para móviles si no hay imagen personalizada
    @if(!$inventario->qr_code)
    if (document.getElementById('qrcode-mobile-{{ $inventario->id }}')) {
        // Cargar librería QRCode.js si no está cargada
        if (typeof QRCode === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
            script.onload = function() {
                generateMobileQR();
            };
            document.head.appendChild(script);
        } else {
            generateMobileQR();
        }
        
        function generateMobileQR() {
            QRCode.toCanvas(document.getElementById('qrcode-mobile-{{ $inventario->id }}'), '{{ $nasUrl }}', {
                width: 80,
                height: 80,
                margin: 1,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                }
            });
        }
    }
    @endif
});
</script>

@endsection