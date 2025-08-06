@extends('layouts.app')

@section('title', 'Detalle del Equipo - ' . $inventario->nombre)

@section('content')
<div class="container-fluid py-4">
    <!-- Header Profesional -->
    <div class="header-card">
        <div class="header-main">
            <div class="row align-items-center">
                <!-- Imagen del equipo -->
                <div class="col-md-3">
                    <div class="equipment-image">
                        @php
                            $imageUrl = null;
                            // Prioridad 1: Campo imagen_principal (ruta correcta)
                            if($inventario->imagen_principal && file_exists(storage_path('app/public/' . $inventario->imagen_principal))) {
                                $imageUrl = asset('storage/' . $inventario->imagen_principal);
                            }
                            // Prioridad 2: Spatie Media Library - colección 'imagenes' (verificar si archivo existe)
                            elseif($inventario->getFirstMediaUrl('imagenes') && $inventario->getMedia('imagenes')->count() > 0) {
                                $media = $inventario->getMedia('imagenes')->first();
                                if(file_exists($media->getPath())) {
                                    $imageUrl = $inventario->getFirstMediaUrl('imagenes');
                                }
                            }
                            // Prioridad 3: Campo imagen (legacy)
                            elseif($inventario->imagen && file_exists(storage_path('app/public/inventario_imagenes/' . $inventario->imagen))) {
                                $imageUrl = asset('storage/inventario_imagenes/' . $inventario->imagen);
                            }
                        @endphp
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $inventario->nombre }}" class="img-fluid rounded" style="width: 100%; height: 200px; object-fit: cover; border: 2px solid #e9ecef;">
                        @else
                            <div class="no-image d-flex flex-column align-items-center justify-content-center" style="width: 100%; height: 200px; border: 2px dashed #dee2e6; border-radius: 8px; background-color: #f8f9fa;">
                                <i class="fas fa-camera" style="color: #6c757d; font-size: 3rem; margin-bottom: 1rem;"></i>
                                <span style="color: #6c757d; font-weight: 500;">Sin imagen disponible</span>
                                <small class="text-muted mt-1">Pendiente de captura</small>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Información del título -->
                <div class="col-md-6">
                    <div class="header-title-section">
                        <h1 class="header-title">{{ $inventario->nombre }}</h1>
                        <div class="header-badges">
                            <span class="header-badge">
                                <i class="fas fa-barcode me-1" style="color: #007bff;"></i>
                                {{ $inventario->codigo_unico }}
                            </span>
                            <span class="header-badge">
                                <i class="fas fa-layer-group me-1" style="color: #28a745;"></i>
                                {{ $inventario->categoria->nombre ?? 'Sin categoría' }}
                            </span>
                            @if($inventario->marca)
                            <span class="header-badge">
                                <i class="fas fa-tag me-1" style="color: #ffc107;"></i>
                                {{ $inventario->marca }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Estadísticas centradas -->
                <div class="col-md-3">
                    <div class="header-stats-center">
                        <div class="stat-item-center">
                            <div class="stat-icon-inline">
                                @php
                                    $estadoPrincipal = $ubicaciones->first()->estado ?? 'disponible';
                                @endphp
                                <i class="fas fa-circle" style="color: 
                                    @if($estadoPrincipal == 'disponible') #28a745
                                    @elseif($estadoPrincipal == 'en uso') #007bff
                                    @elseif($estadoPrincipal == 'en mantenimiento') #ffc107
                                    @elseif($estadoPrincipal == 'dado de baja') #dc3545
                                    @elseif($estadoPrincipal == 'robado') #6f42c1
                                    @else #6c757d @endif;"></i>
                            </div>
                            <div class="stat-number">{{ ucfirst(str_replace('_', ' ', $estadoPrincipal)) }}</div>
                            <div class="stat-label">Estado</div>
                        </div>
                        <div class="stat-item-center">
                            <div class="stat-icon-inline">
                                <i class="fas fa-map-marker-alt" style="color: #17a2b8;"></i>
                            </div>
                            <div class="stat-number">{{ $ubicaciones->first()->ubicacion->nombre ?? 'Sin ubicación' }}</div>
                            <div class="stat-label">Ubicación</div>
                        </div>
                        <div class="stat-item-center">
                            <div class="stat-icon-inline">
                                <i class="fas fa-dollar-sign" style="color: #28a745;"></i>
                            </div>
                            <div class="stat-number">${{ number_format($inventario->valor_unitario * $cantidadTotal, 2) }}</div>
                            <div class="stat-label">Valor Total</div>
                        </div>
                        <div class="stat-item-center">
                            <div class="stat-icon-inline">
                                <i class="fas fa-user" style="color: #6f42c1;"></i>
                            </div>
                            <div class="stat-number">{{ $ubicaciones->first()->empleado->nombre ?? 'Sin asignar' }}</div>
                            <div class="stat-label">Responsable</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="header-actions mt-3">
                @if(auth()->user()->role->name === 'administrador' || auth()->user()->role->name === 'almacenista')
                    <a href="{{ route('movimientos.create', ['inventario_id' => $inventario->id]) }}" class="btn btn-primary">
                        <i class="fas fa-exchange-alt me-1" style="color: white;"></i> Nuevo Movimiento
                    </a>
                    <a href="{{ route('mantenimientos.create', ['inventario_id' => $inventario->id]) }}" class="btn btn-warning">
                        <i class="fas fa-tools me-1" style="color: #212529;"></i> Nuevo Mantenimiento
                    </a>
                @endif
                <a href="{{ route('inventarios.edit', $inventario->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-edit me-1" style="color: #6c757d;"></i> Editar
                </a>
            </div>
        </div>
    </div>




    <!-- Capítulo 1: Identificación del Equipo -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="border: 1px solid #e9ecef; background-color: #ffffff;">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                    <div class="d-flex align-items-center">
                        <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #6c757d; border-radius: 50%; color: white;">
                            <span style="font-size: 1.2rem; font-weight: bold;">1</span>
                        </div>
                        <div>
                            <h2 class="mb-1" style="color: #495057; font-size: 1.5rem;">Identificación del Equipo</h2>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">Datos básicos y especificaciones técnicas</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-building" style="color: #6c757d; font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="mb-0" style="color: #495057; font-size: 1.1rem;">Información Corporativa</h4>
                                </div>
                                <div class="space-y-2">
                                    <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Propietario</span>
                                            <span style="color: #495057; font-size: 0.9rem;">{{ $inventario->propietario ?? 'HIDROOBRAS' }}</span>
                                        </div>
                                    </div>
                                    <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Proveedor</span>
                                            <span style="color: #495057; font-size: 0.9rem;">{{ $inventario->proveedor->nombre ?? 'No especificado' }}</span>
                                        </div>
                                    </div>
                                    <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Categoría</span>
                                            <span style="color: #495057; font-size: 0.9rem;">{{ $inventario->categoria->nombre ?? 'No especificada' }}</span>
                                        </div>
                                    </div>
                                    <div class="p-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Fecha de Adquisición</span>
                                            <span style="color: #495057; font-size: 0.9rem;">{{ $inventario->fecha_adquisicion ? $inventario->fecha_adquisicion->format('d/m/Y') : 'No especificada' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="p-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-cogs" style="color: #6c757d; font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="mb-0" style="color: #495057; font-size: 1.1rem;">Especificaciones Técnicas</h4>
                                </div>
                                <div class="space-y-2">
                                    <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Número de Serie</span>
                                            <span class="badge bg-secondary" style="font-size: 0.7rem;">{{ $inventario->numero_serie ?? 'No especificado' }}</span>
                                        </div>
                                    </div>
                                    <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Marca</span>
                                            <span style="color: #495057; font-size: 0.9rem;">{{ $inventario->marca ?? 'No especificada' }}</span>
                                        </div>
                                    </div>
                                    <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Modelo</span>
                                            <span style="color: #495057; font-size: 0.9rem;">{{ $inventario->modelo ?? 'No especificado' }}</span>
                                        </div>
                                    </div>
                                    <div class="p-2">
                                        <span class="text-muted d-block" style="font-size: 0.8rem; text-transform: uppercase; margin-bottom: 0.5rem;">Descripción</span>
                                        <p class="mb-0" style="color: #495057; font-size: 0.9rem; line-height: 1.4;">{{ $inventario->descripcion ?? 'Sin descripción disponible' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Capítulo 2: Situación Financiera y Ubicación -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="border: 1px solid #e9ecef; background-color: #ffffff;">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                    <div class="d-flex align-items-center">
                        <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #6c757d; border-radius: 50%; color: white;">
                            <span style="font-size: 1.2rem; font-weight: bold;">2</span>
                        </div>
                        <div>
                            <h2 class="mb-1" style="color: #495057; font-size: 1.5rem;">Situación Financiera y Ubicación</h2>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">Valoración económica y distribución geográfica</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-dollar-sign" style="color: #6c757d; font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="mb-0" style="color: #495057; font-size: 1.1rem;">Valoración Económica</h4>
                                </div>
                                <div class="text-center p-3 mb-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #ffffff;">
                                    <h3 class="mb-1" style="color: #495057; font-size: 1.8rem;">${{ number_format(($inventario->valor_unitario ?? 0) * ($inventario->cantidad ?? 1), 2) }}</h3>
                                    <p class="text-muted mb-0" style="font-size: 0.8rem; text-transform: uppercase;">Valor Total del Activo</p>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="p-2" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #ffffff;">
                                            <span class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase; margin-bottom: 0.3rem;">Valor Unitario</span>
                                            <span style="color: #495057; font-size: 0.9rem;">${{ number_format($inventario->valor_unitario ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #ffffff;">
                                            <span class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase; margin-bottom: 0.3rem;">Cantidad</span>
                                            <span style="color: #495057; font-size: 0.9rem;">{{ $inventario->cantidad ?? 1 }} unidades</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="p-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-map-marker-alt" style="color: #6c757d; font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="mb-0" style="color: #495057; font-size: 1.1rem;">Ubicación Actual</h4>
                                </div>
                                @php
                                    $ubicacionActual = $inventario->ubicacion_actual ?? $inventario->movimientos->last()->ubicacion_destino ?? 'Sin ubicación';
                                    $estadoActual = $inventario->estado ?? 'disponible';
                                    $statusConfig = match($estadoActual) {
                                        'disponible' => ['color' => '#27ae60', 'icon' => 'fa-check-circle', 'text' => 'Disponible'],
                                        'en uso' => ['color' => '#3498db', 'icon' => 'fa-cogs', 'text' => 'En Operación'],
                                        'en mantenimiento' => ['color' => '#f39c12', 'icon' => 'fa-wrench', 'text' => 'En Mantenimiento'],
                                        'fuera de servicio' => ['color' => '#e74c3c', 'icon' => 'fa-exclamation-triangle', 'text' => 'Fuera de Servicio'],
                                        default => ['color' => '#95a5a6', 'icon' => 'fa-question-circle', 'text' => 'Sin Estado']
                                    };
                                @endphp
                                <div class="text-center p-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #ffffff;">
                                    <h3 class="mb-2" style="color: #495057; font-size: 1.3rem;">{{ $ubicacionActual }}</h3>
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <i class="fas {{ $statusConfig['icon'] }} me-2" style="color: #6c757d; font-size: 1rem;"></i>
                                        <span class="badge" style="background-color: #6c757d; color: white; font-size: 0.8rem;">{{ $statusConfig['text'] }}</span>
                                    </div>
                                    <div class="p-2" style="background-color: #f8f9fa; border-radius: 4px;">
                                        <small class="text-muted">{{ $inventario->cantidad ?? 1 }} unidades registradas</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Capítulo 3: Documentación Visual -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="border: 1px solid #e9ecef; background-color: #ffffff;">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                    <div class="d-flex align-items-center">
                        <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #6c757d; border-radius: 50%; color: white;">
                            <span style="font-size: 1.2rem; font-weight: bold;">3</span>
                        </div>
                        <div>
                            <h2 class="mb-1" style="color: #495057; font-size: 1.5rem;">Documentación Visual</h2>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">Registro fotográfico y evidencias</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Imagen Principal -->
                        <div class="col-md-8">
                            <div class="p-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-camera" style="color: #6c757d; font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="mb-0" style="color: #495057; font-size: 1.1rem;">Vista Principal del Equipo</h4>
                                </div>
                                @php
                                    $mainImageUrl = null;
                                    // Prioridad 1: Campo imagen_principal (ruta correcta)
                                    if($inventario->imagen_principal && file_exists(storage_path('app/public/' . $inventario->imagen_principal))) {
                                        $mainImageUrl = asset('storage/' . $inventario->imagen_principal);
                                    }
                                    // Prioridad 2: Spatie Media Library - colección 'imagenes' (verificar si archivo existe)
                                    elseif($inventario->getFirstMediaUrl('imagenes') && $inventario->getMedia('imagenes')->count() > 0) {
                                        $media = $inventario->getMedia('imagenes')->first();
                                        if(file_exists($media->getPath())) {
                                            $mainImageUrl = $inventario->getFirstMediaUrl('imagenes');
                                        }
                                    }
                                    // Prioridad 3: Campo imagen (legacy)
                                    elseif($inventario->imagen && file_exists(storage_path('app/public/inventario_imagenes/' . $inventario->imagen))) {
                                        $mainImageUrl = asset('storage/inventario_imagenes/' . $inventario->imagen);
                                    }
                                @endphp
                                @if($mainImageUrl)
                                    <div class="position-relative">
                                        <img src="{{ $mainImageUrl }}" 
                                             class="img-fluid w-100" 
                                             style="height: 300px; object-fit: cover; cursor: pointer; border: 1px solid #e9ecef; border-radius: 8px;"
                                             onclick="openImageModal('{{ $mainImageUrl }}', '{{ $inventario->nombre }}')"
                                             alt="{{ $inventario->nombre }}">
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge" style="background-color: #28a745; color: white; font-size: 0.7rem;">
                                                Principal
                                            </span>
                                        </div>
                                        <div class="position-absolute bottom-0 start-0 end-0 p-2" style="background: rgba(0,0,0,0.5); border-radius: 0 0 8px 8px;">
                                            <p class="text-white mb-0" style="font-size: 0.9rem;">{{ $inventario->nombre }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center" style="height: 300px; border: 2px dashed #dee2e6; border-radius: 8px; background-color: #f8f9fa;">
                                        <div class="text-center">
                                            <i class="fas fa-camera" style="color: #6c757d; font-size: 4rem; margin-bottom: 1.5rem;"></i>
                                            <h5 style="color: #495057; font-weight: 600;">Imagen no disponible</h5>
                                            <p class="text-muted mb-0">Pendiente de captura fotográfica</p>
                                            <small class="text-muted">Las imágenes se almacenan en storage/inventario_imagenes</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Galería Adicional -->
                        <div class="col-md-4">
                            <div class="p-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-images" style="color: #6c757d; font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="mb-0" style="color: #495057; font-size: 1.1rem;">Galería Adicional</h4>
                                </div>
                                <div class="space-y-3">
                                    <!-- Imagen Secundaria (Campo Legacy) -->
                                    <div class="mb-3">
                                        @php
                                            $secondaryImageUrl = null;
                                            // Prioridad 1: Campo imagen_secundaria (ruta correcta)
                                            if($inventario->imagen_secundaria && file_exists(storage_path('app/public/' . $inventario->imagen_secundaria))) {
                                                $secondaryImageUrl = asset('storage/' . $inventario->imagen_secundaria);
                                            }
                                            // Prioridad 2: Segunda imagen de Spatie Media Library
                                            elseif($inventario->getMedia('imagenes')->count() > 1) {
                                                $media = $inventario->getMedia('imagenes')->skip(1)->first();
                                                if(file_exists($media->getPath())) {
                                                    $secondaryImageUrl = $media->getUrl();
                                                }
                                            }
                                        @endphp
                                        @if($secondaryImageUrl)
                                            <div class="position-relative">
                                                <img src="{{ $secondaryImageUrl }}" 
                                                     class="img-fluid w-100" 
                                                     style="height: 140px; object-fit: cover; cursor: pointer; border: 1px solid #e9ecef; border-radius: 8px;"
                                                     onclick="openImageModal('{{ $secondaryImageUrl }}', '{{ $inventario->nombre }} - Vista Secundaria')">
                                                <div class="position-absolute top-0 end-0 m-1">
                                                    <span class="badge" style="background-color: #17a2b8; color: white; font-size: 0.7rem;">Secundaria</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center" style="height: 140px; border: 1px dashed #dee2e6; border-radius: 8px; background-color: #ffffff;">
                                                <div class="text-center">
                                                    <i class="fas fa-plus-circle" style="color: #dee2e6; font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                                                    <p class="mb-0 small text-muted">Imagen Secundaria</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <!-- Tercera imagen adicional -->
                                    <div>
                                        @if($inventario->getMedia('imagenes')->count() > 2)
                                            @php
                                                $media = $inventario->getMedia('imagenes')->skip(2)->first();
                                            @endphp
                                            @if(file_exists($media->getPath()))
                                                <div class="position-relative">
                                                    <img src="{{ $media->getUrl() }}" 
                                                         class="img-fluid w-100" 
                                                         style="height: 140px; object-fit: cover; cursor: pointer; border: 1px solid #e9ecef; border-radius: 8px;"
                                                         onclick="openImageModal('{{ $media->getUrl() }}', '{{ $inventario->nombre }} - Vista Adicional')">
                                                    <div class="position-absolute top-0 end-0 m-1">
                                                        <span class="badge" style="background-color: #6c757d; color: white; font-size: 0.7rem;">Detalles</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-center" style="height: 140px; border: 1px dashed #dee2e6; border-radius: 8px; background-color: #ffffff;">
                                                    <div class="text-center">
                                                        <i class="fas fa-plus-circle" style="color: #dee2e6; font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                                                        <p class="mb-0 small text-muted">Vista Adicional</p>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <div class="d-flex align-items-center justify-content-center" style="height: 140px; border: 1px dashed #dee2e6; border-radius: 8px; background-color: #ffffff;">
                                                <div class="text-center">
                                                    <i class="fas fa-plus-circle" style="color: #dee2e6; font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                                                    <p class="mb-0 small text-muted">Detalles técnicos</p>
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
        </div>
    </div>

    <!-- Capítulo 4: Análisis y Observaciones -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="border: 1px solid #e9ecef; background-color: #ffffff;">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                    <div class="d-flex align-items-center">
                        <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #6c757d; border-radius: 50%; color: white;">
                            <span style="font-size: 1.2rem; font-weight: bold;">4</span>
                        </div>
                        <div>
                            <h2 class="mb-1" style="color: #495057; font-size: 1.5rem;">Análisis y Observaciones</h2>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">Evaluación del estado actual y recomendaciones</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 h-100" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                <div class="text-center mb-3">
                                    <div class="d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #20c997; border-radius: 50%; color: white;">
                                        <i class="fas fa-clipboard-check" style="font-size: 1.2rem;"></i>
                                    </div>
                                </div>
                                <h4 class="text-center mb-3" style="color: #495057; font-size: 1.1rem;">Estado General</h4>
                                <div class="text-center p-3 mb-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #ffffff;">
                                    <p class="mb-2 text-muted" style="font-size: 0.9rem;">{{ Str::limit($inventario->descripcion ?? 'Equipo en condiciones normales de operación.', 80) }}</p>
                                </div>
                                <div class="text-center">
                                    <span class="badge" style="background-color: #6c757d; color: white; font-size: 0.7rem;">
                                        {{ $inventario->updated_at ? $inventario->updated_at->format('d/m/Y') : 'No disponible' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="p-3 h-100" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                <div class="text-center mb-3">
                                    <div class="d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #6f42c1; border-radius: 50%; color: white;">
                                        <i class="fas fa-user-check" style="font-size: 1.2rem;"></i>
                                    </div>
                                </div>
                                <h4 class="text-center mb-3" style="color: #495057; font-size: 1.1rem;">Responsabilidad</h4>
                                <div class="text-center p-3 mb-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #ffffff;">
                                    <p class="mb-1 text-muted" style="font-size: 0.8rem;">Responsable asignado:</p>
                                    <p class="mb-0" style="color: #495057; font-size: 0.9rem;">{{ $inventario->responsable ?? 'No asignado' }}</p>
                                </div>
                                <div class="text-center">
                                    <span class="badge" style="background-color: #6c757d; color: white; font-size: 0.7rem;">
                                        Asignación vigente
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="p-3 h-100" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                <div class="text-center mb-3">
                                    <div class="d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #ffc107; border-radius: 50%; color: white;">
                                        <i class="fas fa-lightbulb" style="font-size: 1.2rem;"></i>
                                    </div>
                                </div>
                                <h4 class="text-center mb-3" style="color: #495057; font-size: 1.1rem;">Recomendaciones</h4>
                                <div class="text-center p-3 mb-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #ffffff;">
                                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Mantener condiciones ambientales adecuadas y realizar inspecciones periódicas.</p>
                                </div>
                                <div class="text-center">
                                    <span class="badge" style="background-color: #6c757d; color: white; font-size: 0.7rem;">
                                        Seguimiento requerido
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Timeline de Observaciones -->
                    <div class="mt-4">
                        <div class="p-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-history" style="color: #6c757d; font-size: 1.2rem;"></i>
                                </div>
                                <h4 class="mb-0" style="color: #495057; font-size: 1.1rem;">Historial de Observaciones</h4>
                            </div>
                            @php
                                $observacionesTexto = $inventario->observaciones ?? '*11/10/2024 LLEGA DE ECCI CALI A BODEGA DELICIAS,AMARILLA *30/12/2024 SALE DE BODEGA DELICIAS A BODEGAS AMERICAS';
                                $observacionesArray = explode('*', $observacionesTexto);
                                $observacionesArray = array_filter($observacionesArray, function($obs) {
                                    return !empty(trim($obs));
                                });
                            @endphp
                            @if(count($observacionesArray) > 0)
                                <div class="timeline position-relative">
                                    @foreach($observacionesArray as $index => $observacion)
                                        <div class="timeline-item mb-4 position-relative">
                                            <div class="d-flex align-items-start">
                                                <div class="flex-shrink-0 position-relative">
                                                    <div class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #6c757d; border-radius: 50%; color: white;">
                                                        <span style="font-size: 0.9rem;">{{ $index + 1 }}</span>
                                                    </div>
                                                    @if($index < count($observacionesArray) - 1)
                                                        <div class="position-absolute" style="top: 40px; left: 50%; transform: translateX(-50%); width: 2px; height: 30px; background-color: #dee2e6;"></div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 ms-4">
                                                    <div class="p-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #ffffff;">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <span class="badge" style="background-color: #6c757d; color: white; font-size: 0.7rem;">
                                                                Registro {{ $index + 1 }}
                                                            </span>
                                                        </div>
                                                        <p class="mb-0" style="color: #2c3e50; font-size: 0.95rem; line-height: 1.6;">{{ trim($observacion) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-sticky-note" style="color: #bdc3c7; font-size: 2.5rem;"></i>
                                    </div>
                                    <h5 class="font-weight-bold" style="color: #7f8c8d;">Sin observaciones registradas</h5>
                                    <p class="mb-0" style="color: #95a5a6;">No hay registros de observaciones para este activo</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Capítulo 5: Historial y Documentación -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0" style="background: #ffffff; border-top: 3px solid #6c757d;">
                <div class="card-header" style="background: #6c757d; border: none;">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: #6c757d; font-size: 1.5rem; font-weight: bold;">5</span>
                        </div>
                        <div>
                            <h2 class="font-weight-bold text-white mb-1" style="font-size: 1.6rem;">Historial y Documentación</h2>
                            <p class="text-white-50 mb-0" style="font-size: 0.9rem;">Registro completo de actividades y documentos asociados</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-5">
                    <!-- Pestañas con diseño minimalista -->
                    <div class="p-3 rounded mb-4" style="background: #f8f9fa; border: 1px solid #dee2e6;">
                        <ul class="nav nav-pills nav-fill" id="historialTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active font-weight-bold" id="movimientos-tab" data-bs-toggle="tab" data-bs-target="#movimientos" type="button" role="tab" style="background: #6c757d; color: white; border: none; padding: 10px 16px;">
                                    <i class="fas fa-exchange-alt me-2" style="color: #17a2b8;"></i>Movimientos
                                    <span class="badge bg-white ms-2" style="color: #6c757d; font-weight: bold;">{{ $inventario->movimientos->count() + 2 }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link font-weight-bold" id="mantenimientos-tab" data-bs-toggle="tab" data-bs-target="#mantenimientos" type="button" role="tab" style="color: #6c757d; border: 1px solid #dee2e6; background: white; padding: 10px 16px;">
                                    <i class="fas fa-tools me-2" style="color: #fd7e14;"></i>Mantenimientos
                                    <span class="badge ms-2" style="background-color: #6c757d; color: white; font-weight: bold;">{{ $inventario->mantenimientos->count() + 2 }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link font-weight-bold" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab" style="color: #6c757d; border: 1px solid #dee2e6; background: white; padding: 10px 16px;">
                                    <i class="fas fa-file-alt me-2" style="color: #6f42c1;"></i>Documentos
                                    <span class="badge ms-2" style="background-color: #6c757d; color: white; font-weight: bold;">3</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Contenido de las pestañas -->
                    <div class="tab-content" id="historialTabsContent">
                        <!-- Movimientos -->
                        <div class="tab-pane fade show active" id="movimientos" role="tabpanel">
                            <div class="bg-white rounded-lg shadow-sm" style="border: 1px solid #e9ecef;">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead style="background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);">
                                            <tr>
                                                <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-calendar-alt me-2" style="color: #ffc107;"></i>Fecha y Hora</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-exchange-alt me-2" style="color: #17a2b8;"></i>Tipo de Movimiento</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-map-marker-alt me-2" style="color: #dc3545;"></i>Origen</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-map-marker-alt me-2" style="color: #28a745;"></i>Destino</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-user me-2" style="color: #6f42c1;"></i>Responsable</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-check-circle me-2" style="color: #20c997;"></i>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Datos de ejemplo con fechas específicas -->
                                            <tr style="border-bottom: 1px solid #e9ecef; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                                                <td class="py-4" style="color: #2c3e50;"><strong style="color: #34495e;">14/07/2025</strong><br><small style="color: #7f8c8d;">12:00</small></td>
                                                <td class="py-4" style="color: #2c3e50;"><i class="fas fa-truck me-2" style="color: #3498db;"></i>Traslado</td>
                                                <td class="py-4" style="color: #2c3e50;">Bodega Delicias</td>
                                                <td class="py-4" style="color: #2c3e50;">664 EL TIEMPO</td>
                                                <td class="py-4" style="color: #2c3e50;">Andrea Viviana</td>
                                                <td class="py-4"><span class="badge px-3 py-2 shadow-sm" style="background-color: #27ae60; color: white; font-size: 0.8rem;"><i class="fas fa-check me-1"></i>Completado</span></td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e9ecef; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                                                <td class="py-4" style="color: #2c3e50;"><strong style="color: #34495e;">28/02/2025</strong><br><small style="color: #7f8c8d;">07:16</small></td>
                                                <td class="py-4" style="color: #2c3e50;"><i class="fas fa-box me-2" style="color: #e74c3c;"></i>Traslado</td>
                                                <td class="py-4" style="color: #2c3e50;">Bodegas Américas</td>
                                                <td class="py-4" style="color: #2c3e50;">Bodega Delicias</td>
                                                <td class="py-4" style="color: #2c3e50;">Usuario eliminado</td>
                                                <td class="py-4"><span class="badge px-3 py-2 shadow-sm" style="background-color: #27ae60; color: white; font-size: 0.8rem;"><i class="fas fa-check me-1"></i>Completado</span></td>
                                            </tr>
                                            @foreach($inventario->movimientos->take(3) as $movimiento)
                                            <tr style="border-bottom: 1px solid #e9ecef; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                                                <td class="py-4" style="color: #2c3e50;">{{ $movimiento->fecha_movimiento ? $movimiento->fecha_movimiento->format('d/m/Y H:i') : '-' }}</td>
                                                <td class="py-4" style="color: #2c3e50;"><i class="fas fa-arrow-right me-2" style="color: #f39c12;"></i>{{ ucfirst($movimiento->tipo_movimiento ?? 'Movimiento') }}</td>
                                                <td class="py-4" style="color: #2c3e50;">{{ $movimiento->ubicacion_origen ?? 'No especificado' }}</td>
                                                <td class="py-4" style="color: #2c3e50;">{{ $movimiento->ubicacion_destino ?? 'No especificado' }}</td>
                                                <td class="py-4" style="color: #2c3e50;">{{ $movimiento->usuario->name ?? 'Usuario eliminado' }}</td>
                                                <td class="py-4">
                                                    <span class="badge px-3 py-2 shadow-sm" style="background-color: #27ae60; color: white; font-size: 0.8rem;"><i class="fas fa-check me-1"></i>Completado</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Mantenimientos -->
                        <div class="tab-pane fade" id="mantenimientos" role="tabpanel">
                            <div class="bg-white rounded-lg shadow-sm" style="border: 1px solid #e9ecef;">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead style="background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);">
                                            <tr>
                                                <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-calendar-alt me-2" style="color: #ffc107;"></i>Fecha</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-tools me-2" style="color: #fd7e14;"></i>Tipo</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-clipboard-list me-2" style="color: #17a2b8;"></i>Descripción</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-user-cog me-2" style="color: #6f42c1;"></i>Técnico</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-check-circle me-2" style="color: #20c997;"></i>Estado</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-dollar-sign me-2" style="color: #28a745;"></i>Costo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inventario->mantenimientos->take(5) as $mantenimiento)
                                            <tr style="border-bottom: 1px solid #e9ecef; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                                                <td class="py-4" style="color: #2c3e50;">{{ $mantenimiento->fecha_mantenimiento ? $mantenimiento->fecha_mantenimiento->format('d/m/Y H:i') : '-' }}</td>
                                                <td class="py-4" style="color: #2c3e50;"><i class="fas fa-cog me-2" style="color: #3498db;"></i>{{ $mantenimiento->tipo ?? 'No especificado' }}</td>
                                                <td class="py-4" style="color: #2c3e50;">{{ $mantenimiento->descripcion ?? '-' }}</td>
                                                <td class="py-4" style="color: #2c3e50;">{{ $mantenimiento->tecnico ?? 'No asignado' }}</td>
                                                <td class="py-4">
                                                    <span class="badge px-3 py-2 shadow-sm" style="background-color: {{ $mantenimiento->estado == 'completado' ? '#27ae60' : ($mantenimiento->estado == 'pendiente' ? '#f39c12' : '#3498db') }}; color: white; font-size: 0.8rem;">
                                                        <i class="fas fa-{{ $mantenimiento->estado == 'completado' ? 'check' : ($mantenimiento->estado == 'pendiente' ? 'clock' : 'info') }} me-1"></i>{{ ucfirst($mantenimiento->estado ?? 'pendiente') }}
                                                    </span>
                                                </td>
                                                <td class="py-4" style="color: #2c3e50; font-weight: 600;">${{ number_format($mantenimiento->costo ?? 0, 2) }}</td>
                                            </tr>
                                            @endforeach
                                            <!-- Datos de ejemplo con mejor formato -->
                                            <tr style="border-bottom: 1px solid #e9ecef; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                                                <td class="py-4" style="color: #2c3e50;"><strong style="color: #34495e;">20/01/2024</strong></td>
                                                <td class="py-4" style="color: #2c3e50;"><i class="fas fa-calendar-check me-2" style="color: #27ae60;"></i>Preventivo</td>
                                                <td class="py-4" style="color: #2c3e50;">Revisión general y lubricación</td>
                                                <td class="py-4" style="color: #2c3e50;">Carlos Rodríguez</td>
                                                <td class="py-4"><span class="badge px-3 py-2 shadow-sm" style="background-color: #27ae60; color: white; font-size: 0.8rem;"><i class="fas fa-check me-1"></i>Completado</span></td>
                                                <td class="py-4" style="color: #2c3e50; font-weight: 600;"><strong>$150.00</strong></td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e9ecef; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                                                <td class="py-4" style="color: #2c3e50;"><strong style="color: #34495e;">05/01/2024</strong></td>
                                                <td class="py-4" style="color: #2c3e50;"><i class="fas fa-wrench me-2" style="color: #f39c12;"></i>Correctivo</td>
                                                <td class="py-4" style="color: #2c3e50;">Reparación de componente eléctrico</td>
                                                <td class="py-4" style="color: #2c3e50;">Ana López</td>
                                                <td class="py-4"><span class="badge px-3 py-2 shadow-sm" style="background-color: #f39c12; color: white; font-size: 0.8rem;"><i class="fas fa-clock me-1"></i>En proceso</span></td>
                                                <td class="py-4" style="color: #2c3e50; font-weight: 600;"><strong>$320.00</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Documentos -->
                        <div class="tab-pane fade" id="documentos" role="tabpanel">
                            <div class="bg-white rounded-lg shadow-sm" style="border: 1px solid #e9ecef;">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead style="background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);">
                                            <tr>
                                                <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-file-alt me-2" style="color: #17a2b8;"></i>Documento</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-tag me-2" style="color: #ffc107;"></i>Tipo</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-calendar-alt me-2" style="color: #fd7e14;"></i>Fecha</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-hdd me-2" style="color: #6c757d;"></i>Tamaño</th>
                        <th class="font-weight-bold text-white border-0 py-4" style="font-size: 0.9rem;"><i class="fas fa-cogs me-2" style="color: #6f42c1;"></i>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Datos de ejemplo con mejor formato -->
                                            <tr style="border-bottom: 1px solid #e9ecef; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                                                <td class="py-4" style="color: #2c3e50;">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-pdf fa-2x me-3" style="color: #e74c3c;"></i>
                                                        <div>
                                                            <strong style="color: #34495e;">Manual_Usuario.pdf</strong>
                                                            <br><small style="color: #7f8c8d;">Manual de operación</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-4"><span class="badge px-3 py-2 shadow-sm" style="background-color: #3498db; color: white; font-size: 0.8rem;"><i class="fas fa-book me-1"></i>Manual</span></td>
                                                <td class="py-4" style="color: #2c3e50;"><strong style="color: #34495e;">15/12/2023</strong></td>
                                                <td class="py-4" style="color: #2c3e50; font-weight: 600;">2.5 MB</td>
                                                <td class="py-4">
                                                    <button class="btn btn-sm me-1 shadow-sm" style="background-color: #34495e; color: white; border: none;"><i class="fas fa-download"></i></button>
                                                    <button class="btn btn-sm shadow-sm" style="background-color: #3498db; color: white; border: none;"><i class="fas fa-eye"></i></button>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e9ecef; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                                                <td class="py-4" style="color: #2c3e50;">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-alt fa-2x me-3" style="color: #3498db;"></i>
                                                        <div>
                                                            <strong style="color: #34495e;">Certificado_Garantia.docx</strong>
                                                            <br><small style="color: #7f8c8d;">Documento de garantía</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-4"><span class="badge px-3 py-2 shadow-sm" style="background-color: #27ae60; color: white; font-size: 0.8rem;"><i class="fas fa-shield-alt me-1"></i>Garantía</span></td>
                                                <td class="py-4" style="color: #2c3e50;"><strong style="color: #34495e;">10/01/2024</strong></td>
                                                <td class="py-4" style="color: #2c3e50; font-weight: 600;">1.2 MB</td>
                                                <td class="py-4">
                                                    <button class="btn btn-sm me-1 shadow-sm" style="background-color: #34495e; color: white; border: none;"><i class="fas fa-download"></i></button>
                                                    <button class="btn btn-sm shadow-sm" style="background-color: #3498db; color: white; border: none;"><i class="fas fa-eye"></i></button>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom: 1px solid #e9ecef; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                                                <td class="py-4" style="color: #2c3e50;">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-image fa-2x me-3" style="color: #27ae60;"></i>
                                                        <div>
                                                            <strong style="color: #34495e;">Especificaciones_Tecnicas.jpg</strong>
                                                            <br><small style="color: #7f8c8d;">Hoja de especificaciones</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-4"><span class="badge px-3 py-2 shadow-sm" style="background-color: #f39c12; color: white; font-size: 0.8rem;"><i class="fas fa-cog me-1"></i>Especificaciones</span></td>
                                                <td class="py-4" style="color: #2c3e50;"><strong style="color: #34495e;">08/01/2024</strong></td>
                                                <td class="py-4" style="color: #2c3e50; font-weight: 600;">850 KB</td>
                                                <td class="py-4">
                                                    <button class="btn btn-sm me-1 shadow-sm" style="background-color: #34495e; color: white; border: none;"><i class="fas fa-download"></i></button>
                                                    <button class="btn btn-sm shadow-sm" style="background-color: #3498db; color: white; border: none;"><i class="fas fa-eye"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Imágenes -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Imagen del Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Imagen del equipo" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-body {
        flex: 1 1 auto;
        min-height: 1px;
        padding: 1.25rem;
    }
    .text-xs {
        font-size: .8rem;
    }
    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }
    .border-left-primary { border-left: .25rem solid #4e73df!important; }
    .border-left-success { border-left: .25rem solid #1cc88a!important; }
    .border-left-info { border-left: .25rem solid #36b9cc!important; }
    .border-left-warning { border-left: .25rem solid #f6c23e!important; }
    .border-left-secondary { border-left: .25rem solid #858796!important; }
    .border-left-dark { border-left: .25rem solid #5a5c69!important; }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.075);
    }
    .thead-light th {
        background-color: #f8f9fc;
        color: #5a5c69;
        border-color: #e3e6f0;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table th, .table td {
        white-space: normal;
        word-wrap: break-word;
    }
    .text-wrap {
        white-space: normal !important;
    }
    .table {
        width: 100% !important;
    }
    
    .text-gray-800 {
        color: #5a5c69 !important;
    }
    .text-gray-600 {
        color: #858796 !important;
    }
    .text-gray-300 {
        color: #dddfeb !important;
    }

    /* Dark Theme Styles */
    [data-bs-theme="dark"] .text-gray-800 {
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .text-gray-600 {
        color: #cbd5e1 !important;
    }
    [data-bs-theme="dark"] .text-gray-300 {
        color: #cbd5e1 !important;
    }
    [data-bs-theme="dark"] .bg-light {
        background-color: #1e293b !important;
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .text-xs {
        color: inherit !important;
    }
    [data-bs-theme="dark"] .card-header {
        background-color: #334155 !important;
    }

    /* Header Styles from categorias.blade.php */
    .header-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        overflow: hidden;
        position: relative;
    }

    .header-main {
        padding: 2rem;
        position: relative;
        z-index: 2;
    }

    .header-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .header-title-section {
        flex: 1;
    }

    .header-icon {
        width: 80px;
        height: 80px;
        background: #f8f9fa;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e9ecef;
    }

    .header-icon i {
        font-size: 2.5rem;
        color: #6c757d;
    }

    .header-text {
        color: #6c757d;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .header-title {
        color: #495057;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
    }

    .header-badges {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .header-badge {
        background: #f8f9fa;
        color: #495057;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .header-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .header-stats-center {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .stat-item-center {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-item-center:hover {
        background: #e9ecef;
        transform: translateY(-2px);
    }

    .stat-icon-inline {
        margin-bottom: 0.5rem;
    }

    .stat-icon-inline i {
        font-size: 1.2rem;
    }

    .stat-item {
        background: #f8f9fa;
        padding: 1.25rem;
        border-radius: 15px;
        border: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        background: #e9ecef;
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background: #e9ecef;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon i {
        font-size: 1.5rem;
        color: #6c757d;
    }

    .stat-content {
        flex: 1;
    }

    .stat-number {
        font-size: 1.2rem;
        font-weight: 700;
        color: #495057;
        margin: 0;
        line-height: 1.2;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.85rem;
        margin: 0;
        margin-top: 0.25rem;
        font-weight: 500;
    }

    .equipment-image {
        width: 100%;
        height: 200px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }

    .equipment-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-image {
        width: 100%;
        height: 100%;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #6c757d;
    }

    .no-image i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .header-actions .btn {
        background: #f8f9fa;
        color: #495057;
        border: 1px solid #e9ecef;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .header-actions .btn:hover {
        background: #e9ecef;
        color: #495057;
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .header-actions .btn-primary {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .header-actions .btn-primary:hover {
        background: #0056b3;
        color: white;
        border-color: #0056b3;
    }

    .header-actions .btn-primary:hover {
        background: white;
        color: #475569;
    }

    /* Dark theme adaptations */
    [data-bs-theme="dark"] .header-card {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
    }

    [data-bs-theme="dark"] .header-badge,
    [data-bs-theme="dark"] .stat-item {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
    }

    [data-bs-theme="dark"] .header-actions .btn {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
    }

    [data-bs-theme="dark"] .header-actions .btn:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .header-info {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .header-title {
            font-size: 2rem;
        }

        .header-stats {
            grid-template-columns: 1fr;
        }

        .header-actions {
            justify-content: center;
        }
    }
        border-bottom-color: #475569 !important;
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .thead-light th {
        background-color: #334155 !important;
        color: #f8fafc !important;
        border-color: #475569 !important;
    }
    [data-bs-theme="dark"] .table-hover tbody tr:hover {
        background-color: rgba(71, 85, 105, 0.4) !important;
    }
    [data-bs-theme="dark"] .border-left-primary {
        border-left-color: #60a5fa !important;
    }
    [data-bs-theme="dark"] .border-left-success {
        border-left-color: #22c55e !important;
    }
    [data-bs-theme="dark"] .border-left-info {
        border-left-color: #06b6d4 !important;
    }
    [data-bs-theme="dark"] .border-left-warning {
        border-left-color: #f59e0b !important;
    }
    [data-bs-theme="dark"] .border-left-secondary {
        border-left-color: #64748b !important;
    }
    [data-bs-theme="dark"] .border-left-dark {
        border-left-color: #475569 !important;
    }
    [data-bs-theme="dark"] .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.6) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Modal de imágenes
    document.addEventListener('DOMContentLoaded', function() {
        const imageModal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        
        if (imageModal) {
            imageModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const imageUrl = button.getAttribute('data-image');
                modalImage.src = imageUrl;
            });
        }
    });
</script>
@endpush