@extends('layouts.app')

@section('title', 'Detalle del Equipo - ' . $inventario->nombre)

@section('content')
<div class="container-fluid py-4">
    <!-- Header Profesional -->
    <div class="header-card">
        <div class="header-main">
            <div class="row align-items-start">
                <!-- Información del elemento en la parte superior izquierda -->
                <div class="col-md-9">
                    <!-- Información del título -->
                    <div class="header-title-section mb-3">
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
                            @if($inventario->numero_serie)
                            <span class="header-badge">
                                <i class="fas fa-hashtag me-1" style="color: #6f42c1;"></i>
                                {{ $inventario->numero_serie }}
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Imagen del equipo debajo de la información -->
                    <div class="equipment-image mt-2">
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
                            <img src="{{ $imageUrl }}" alt="{{ $inventario->nombre }}" class="img-fluid rounded" style="width: 100%; height: calc(100vh - 400px); min-height: 500px; max-height: 800px; object-fit: contain; border: 2px solid #e9ecef;">
                        @else
                            <div class="no-image d-flex flex-column align-items-center justify-content-center" style="width: 100%; height: calc(100vh - 400px); min-height: 500px; max-height: 800px; border: 2px dashed #dee2e6; border-radius: 8px; background-color: #f8f9fa;">
                                <i class="fas fa-camera" style="color: #6c757d; font-size: 5rem; margin-bottom: 1.5rem;"></i>
                                <span style="color: #6c757d; font-weight: 500; font-size: 1.2rem;">Sin imagen disponible</span>
                                <small class="mt-1" style="color: #212529;">Pendiente de captura</small>
                            </div>
                        @endif
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
                                <i class="fas fa-building" style="color: #fd7e14;"></i>
                            </div>
                            <div class="stat-number">{{ $inventario->propietario }}</div>
                            <div class="stat-label">Propietario</div>
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
                            <div class="stat-number">{{ $ultimoResponsable->usuarioDestino->nombre ?? 'Sin asignar' }}</div>
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
                            <div class="p-3 h-100" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-building" style="color: #007bff; font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="mb-0" style="color: #212529; font-size: 1.1rem;">Información Corporativa</h4>
                                </div>
                                <div class="space-y-2">
                                    <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Propietario</span>
                        <span style="color: #212529; font-size: 0.9rem;">{{ $inventario->propietario ?? 'HIDROOBRAS' }}</span>
                                        </div>
                                    </div>
                                    <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Proveedor</span>
                        <span style="color: #212529; font-size: 0.9rem;">{{ $inventario->proveedor->nombre ?? 'No especificado' }}</span>
                                        </div>
                                    </div>
                                    <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Categoría</span>
                        <span style="color: #212529; font-size: 0.9rem;">{{ $inventario->categoria->nombre ?? 'No especificada' }}</span>
                                        </div>
                                    </div>
                                    <div class="p-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Fecha de Adquisición</span>
                        <span style="color: #212529; font-size: 0.9rem;">{{ $inventario->fecha_adquisicion ? $inventario->fecha_adquisicion->format('d/m/Y') : 'No especificada' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="p-3 h-100" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-cogs" style="color: #28a745; font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="mb-0" style="color: #212529; font-size: 1.1rem;">Especificaciones Técnicas</h4>
                                </div>
                                <div class="space-y-2">
                                    <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Número de Serie</span>
                                            <span class="badge bg-secondary" style="font-size: 0.7rem;">{{ $inventario->numero_serie ?? 'No especificado' }}</span>
                                        </div>
                                    </div>
                                    <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Marca</span>
                        <span style="color: #212529; font-size: 0.9rem;">{{ $inventario->marca ?? 'No especificada' }}</span>
                                        </div>
                                    </div>
                                    <div class="p-2 mb-2" style="border-bottom: 1px solid #e9ecef;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Modelo</span>
                        <span style="color: #212529; font-size: 0.9rem;">{{ $inventario->modelo ?? 'No especificado' }}</span>
                                        </div>
                                    </div>
                                    <div class="p-2">
                                        <span class="d-block" style="color: #212529; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 0.5rem;">Descripción</span>
                    <p class="mb-0" style="color: #212529; font-size: 0.9rem; line-height: 1.4;">{{ $inventario->descripcion ?? 'Sin descripción disponible' }}</p>
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
                        <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #28a745; border-radius: 50%; color: white;">
                            <i class="fas fa-dollar-sign" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Situación Financiera y Ubicación</h2>
                            <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Valoración económica y distribución geográfica</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 h-100" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-dollar-sign" style="color: #28a745; font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="mb-0" style="color: #212529; font-size: 1.1rem;">Valoración Económica</h4>
                                </div>
                                <div class="text-center p-3 mb-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #ffffff;">
                                    <h3 class="mb-1" style="color: #212529; font-size: 1.8rem;">${{ number_format(($inventario->valor_unitario ?? 0) * ($inventario->cantidad ?? 1), 2) }}</h3>
                <p class="mb-0" style="color: #212529; font-size: 0.8rem; text-transform: uppercase;">Valor Total del Activo</p>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="p-2" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #ffffff;">
                                            <span class="d-block" style="color: #212529; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 0.3rem;">Valor Unitario</span>
                    <span style="color: #212529; font-size: 0.9rem;">${{ number_format($inventario->valor_unitario ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #ffffff;">
                                            <span class="d-block" style="color: #212529; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 0.3rem;">Cantidad</span>
                    <span style="color: #212529; font-size: 0.9rem;">{{ $inventario->cantidad ?? 1 }} unidades</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="p-3 h-100" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-map-marker-alt" style="color: #dc3545; font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="mb-0" style="color: #212529; font-size: 1.1rem;">Ubicación Actual</h4>
                                </div>
                                @php
                                    // Obtener ubicación desde la relación ubicaciones o movimientos
                                    $ubicacionActual = $inventario->ubicaciones->first()->ubicacion->nombre ?? 
                                                      $inventario->movimientos->last()->ubicacion_destino ?? 
                                                      $inventario->ubicacion_actual ?? 
                                                      'Sin ubicación';
                                    $estadoActual = $ubicaciones->first()->estado ?? 'disponible';
                                    $statusConfig = match($estadoActual) {
                                        'disponible' => ['color' => '#27ae60', 'icon' => 'fa-check-circle', 'text' => 'Disponible'],
                                        'en uso' => ['color' => '#3498db', 'icon' => 'fa-cogs', 'text' => 'En Operación'],
                                        'en mantenimiento' => ['color' => '#f39c12', 'icon' => 'fa-wrench', 'text' => 'En Mantenimiento'],
                                        'fuera de servicio' => ['color' => '#e74c3c', 'icon' => 'fa-exclamation-triangle', 'text' => 'Fuera de Servicio'],
                                        default => ['color' => '#95a5a6', 'icon' => 'fa-question-circle', 'text' => 'Sin Estado']
                                    };
                                @endphp
                                <div class="text-center p-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #ffffff;">
                                    <h3 class="mb-2" style="color: #212529; font-size: 1.3rem;">{{ $ubicacionActual }}</h3>
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <i class="fas {{ $statusConfig['icon'] }} me-2" style="color: {{ $statusConfig['color'] }}; font-size: 1rem;"></i>
                                        <span class="badge" style="background-color: {{ $statusConfig['color'] }}; color: white; font-size: 0.8rem;">{{ $statusConfig['text'] }}</span>
                                    </div>
                                    <div class="p-2" style="background-color: #f8f9fa; border-radius: 4px;">
                                        <small style="color: #212529;">{{ $inventario->cantidad ?? 1 }} unidades registradas</small>
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
                        <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #ffc107; border-radius: 50%; color: white;">
                            <i class="fas fa-camera" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Documentación Visual</h2>
                            <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Registro fotográfico y evidencias</p>
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
                                        <i class="fas fa-camera" style="color: #007bff; font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="mb-0" style="color: #212529; font-size: 1.1rem;">Vista Principal del Equipo</h4>
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
                                            <h5 style="color: #212529; font-weight: 600;">Imagen no disponible</h5>
                <p class="mb-0" style="color: #212529;">Pendiente de captura fotográfica</p>
                <small style="color: #212529;">Las imágenes se almacenan en storage/inventario_imagenes</small>
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
                                        <i class="fas fa-images" style="color: #28a745; font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="mb-0" style="color: #212529; font-size: 1.1rem;">Imágenes Complementarias</h4>
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
                                                     style="height: 300px; object-fit: cover; cursor: pointer; border: 1px solid #e9ecef; border-radius: 8px;"
                                                     onclick="openImageModal('{{ $secondaryImageUrl }}', '{{ $inventario->nombre }} - Vista Secundaria')">
                                                <div class="position-absolute top-0 end-0 m-1">
                                                    <span class="badge" style="background-color: #17a2b8; color: white; font-size: 0.7rem;">Secundaria</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center" style="height: 300px; border: 1px dashed #dee2e6; border-radius: 8px; background-color: #ffffff;">
                                                <div class="text-center">
                                                    <i class="fas fa-plus-circle" style="color: #dee2e6; font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                                                    <p class="mb-0 small" style="color: #212529;">Imagen Secundaria</p>
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

    <!-- Capítulo 4: Bitácora de Observaciones -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="border: 1px solid #e9ecef; background-color: #ffffff;">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                    <div class="d-flex align-items-center">
                        <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #17a2b8; border-radius: 50%; color: white;">
                            <i class="fas fa-clipboard-list" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Bitácora de Observaciones</h2>
                            <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Registro cronológico de eventos y observaciones del equipo</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    @php
                        // Procesar observaciones para la bitácora
                        $observacionesTexto = $inventario->observaciones ?? '*11/10/2024 LLEGA DE ECCI CALI A BODEGA DELICIAS,AMARILLA *30/12/2024 SALE DE BODEGA DELICIAS A BODEGAS AMERICAS';
                        $observacionesArray = array_filter(explode('*', $observacionesTexto), function($obs) {
                            return !empty(trim($obs));
                        });
                        $observacionesArray = array_values($observacionesArray);
                    @endphp
                    
                    <!-- Acordeón de Bitácora -->
                    <div class="accordion" id="bitacoraAccordion2">
                        <div class="accordion-item" style="border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden;">
                            <h2 class="accordion-header" id="headingBitacora2">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBitacora2" aria-expanded="true" aria-controls="collapseBitacora2" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: none; padding: 1rem;">
                                    <div class="d-flex align-items-center w-100">
                                        <div class="me-3">
                                            <div class="d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background: linear-gradient(135deg, #17a2b8 0%, #17a2b8dd 100%); border-radius: 50%; color: white; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                                <i class="fas fa-clipboard-list" style="font-size: 0.8rem;"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge me-2" style="background-color: #17a2b8; color: white; font-size: 0.7rem; padding: 0.3rem 0.6rem;">
                                                        {{ count($observacionesArray) }} Observaciones
                                                    </span>
                                                    <span class="badge" style="background-color: #6c757d; color: white; font-size: 0.7rem; padding: 0.3rem 0.6rem;">
                                                        <i class="fas fa-calendar-alt me-1"></i>Registro Completo
                                                    </span>
                                                </div>
                                                <small class="text-muted me-3">{{ $inventario->updated_at ? $inventario->updated_at->format('H:i') : '' }}</small>
                                            </div>
                                            <div class="mt-1">
                                                <span style="color: #212529; font-size: 0.85rem;">Ver todas las observaciones registradas del equipo</span>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseBitacora2" class="accordion-collapse collapse show" aria-labelledby="headingBitacora2" data-bs-parent="#bitacoraAccordion2">
                                <div class="accordion-body" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 1.5rem;">
                                    @if(count($observacionesArray) > 0)
                                        <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;" class="custom-scrollbar">
                                            @foreach($observacionesArray as $index => $observacion)
                                                @php
                                                    // Extraer fecha si existe en el formato del texto
                                                    preg_match('/^(\d{1,2}\/\d{1,2}\/\d{4})\s*(.*)/', trim($observacion), $matches);
                                                    $fecha = $matches[1] ?? null;
                                                    $contenido = $matches[2] ?? trim($observacion);
                                                    
                                                    // Colores para diferentes tipos de eventos
                                                    $tipoColors = [
                                                        'LLEGA' => ['color' => '#28a745', 'icon' => 'fas fa-arrow-down'],
                                                        'SALE' => ['color' => '#dc3545', 'icon' => 'fas fa-arrow-up'],
                                                        'SE LLEVA' => ['color' => '#dc3545', 'icon' => 'fas fa-truck'],
                                                        'SE ENVIA' => ['color' => '#dc3545', 'icon' => 'fas fa-shipping-fast'],
                                                        'ENVIO' => ['color' => '#dc3545', 'icon' => 'fas fa-shipping-fast'],
                                                        'COMPRA' => ['color' => '#28a745', 'icon' => 'fas fa-shopping-cart'],
                                                        'ADQUISICION' => ['color' => '#28a745', 'icon' => 'fas fa-shopping-cart'],
                                                        'MANTENIMIENTO' => ['color' => '#ffc107', 'icon' => 'fas fa-tools'],
                                                        'REVISION' => ['color' => '#17a2b8', 'icon' => 'fas fa-search'],
                                                        'REPARACION' => ['color' => '#6f42c1', 'icon' => 'fas fa-wrench'],
                                                        'INGRESO' => ['color' => '#28a745', 'icon' => 'fas fa-sign-in-alt'],
                                                        'RETIRO' => ['color' => '#dc3545', 'icon' => 'fas fa-sign-out-alt']
                                                    ];
                                                    
                                                    $tipoDetectado = null;
                                                    foreach($tipoColors as $tipo => $config_tipo) {
                                                        if(stripos($contenido, $tipo) !== false) {
                                                            $tipoDetectado = $config_tipo;
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Icono por defecto consistente para todas las entradas
                                                    if(!$tipoDetectado) {
                                                        $tipoDetectado = [
                                                            'color' => '#17a2b8',
                                                            'icon' => 'fas fa-clipboard-check'
                                                        ];
                                                    }
                                                @endphp
                                                
                                                <div class="d-flex mb-3 position-relative" style="border-left: 2px solid #e9ecef; margin-left: 20px; padding-left: 30px; {{ $index === count($observacionesArray) - 1 ? 'border-left: 2px solid transparent;' : '' }}">
                                                    <div class="flex-shrink-0 position-relative" style="margin-left: -51px; z-index: 2;">
                                                        <div class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: linear-gradient(135deg, {{ $tipoDetectado['color'] }} 0%, {{ $tipoDetectado['color'] }}dd 100%); border-radius: 50%; color: white; border: 3px solid #ffffff; box-shadow: 0 3px 8px rgba(0,0,0,0.15);">
                                                            <i class="{{ $tipoDetectado['icon'] }}" style="font-size: 0.9rem;"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="p-3" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 8px; border: 1px solid #e9ecef; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="badge me-2" style="background-color: {{ $tipoDetectado['color'] }}; color: white; font-size: 0.7rem; padding: 0.3rem 0.6rem;">
                                                                        Entrada #{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                                                    </span>
                                                                    @if($fecha)
                                                                        <span class="badge" style="background-color: #6c757d; color: white; font-size: 0.7rem; padding: 0.3rem 0.6rem;">
                                                                            <i class="fas fa-calendar-alt me-1"></i>{{ $fecha }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <small class="text-muted">{{ $inventario->updated_at ? $inventario->updated_at->format('H:i') : '' }}</small>
                                                            </div>
                                                            <p class="mb-0" style="color: #212529; font-size: 0.95rem; line-height: 1.6; font-weight: 500;">{{ $contenido }}</p>
                                                            @if(strlen($contenido) > 80)
                                                                <div class="mt-2 pt-2" style="border-top: 1px solid #e9ecef;">
                                                                    <small class="text-muted">Registro detallado • {{ strlen($contenido) }} caracteres</small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <div class="mb-3">
                                                <i class="fas fa-book-open" style="color: #bdc3c7; font-size: 3rem;"></i>
                                            </div>
                                            <h5 class="font-weight-bold mb-2" style="color: #7f8c8d;">Bitácora Vacía</h5>
                                            <p class="mb-0" style="color: #95a5a6;">No se han registrado observaciones para este equipo</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                        
                        @if(count($observacionesArray) > 0)
                            <!-- Footer informativo -->
                            <div class="d-flex justify-content-center align-items-center pt-3" style="border-top: 1px solid #e9ecef;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clipboard-list me-2" style="color: #6c757d;"></i>
                                    <small class="text-muted">{{ count($observacionesArray) }} {{ count($observacionesArray) === 1 ? 'observación registrada' : 'observaciones registradas' }} en la bitácora</small>
                                </div>
                            </div>
                        @endif
                    </div>
                    

                </div>
            </div>
        </div>
    </div>

    <!-- Capítulo 4: Bitácora de Observaciones -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="border: 1px solid #e9ecef; background-color: #ffffff;">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                    <div class="d-flex align-items-center">
                        <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #17a2b8; border-radius: 50%; color: white;">
                            <i class="fas fa-clipboard-list" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Bitácora de Observaciones</h2>
                            <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Registro cronológico de eventos y observaciones del equipo</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    @php
                        // Procesar observaciones para la bitácora
                        $observacionesTexto = $inventario->observaciones ?? '*11/10/2024 LLEGA DE ECCI CALI A BODEGA DELICIAS,AMARILLA *30/12/2024 SALE DE BODEGA DELICIAS A BODEGAS AMERICAS';
                        $observacionesArray = array_filter(explode('*', $observacionesTexto), function($obs) {
                            return !empty(trim($obs));
                        });
                        $observacionesArray = array_values($observacionesArray);
                    @endphp
                    
                    <!-- Acordeón de Bitácora -->
                    <div class="accordion" id="bitacoraAccordion">
                        <div class="accordion-item" style="border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden;">
                            <h2 class="accordion-header" id="headingBitacora">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBitacora" aria-expanded="true" aria-controls="collapseBitacora" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: none; padding: 1rem;">
                                    <div class="d-flex align-items-center w-100">
                                        <div class="me-3">
                                            <div class="d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background: linear-gradient(135deg, #17a2b8 0%, #17a2b8dd 100%); border-radius: 50%; color: white; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                                <i class="fas fa-clipboard-list" style="font-size: 0.8rem;"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge me-2" style="background-color: #17a2b8; color: white; font-size: 0.7rem; padding: 0.3rem 0.6rem;">
                                                        {{ count($observacionesArray) }} Observaciones
                                                    </span>
                                                    <span class="badge" style="background-color: #6c757d; color: white; font-size: 0.7rem; padding: 0.3rem 0.6rem;">
                                                        <i class="fas fa-calendar-alt me-1"></i>Registro Completo
                                                    </span>
                                                </div>
                                                <small class="text-muted me-3">{{ $inventario->updated_at ? $inventario->updated_at->format('H:i') : '' }}</small>
                                            </div>
                                            <div class="mt-1">
                                                <span style="color: #212529; font-size: 0.85rem;">Ver todas las observaciones registradas del equipo</span>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseBitacora" class="accordion-collapse collapse show" aria-labelledby="headingBitacora" data-bs-parent="#bitacoraAccordion">
                                <div class="accordion-body" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 1.5rem;">
                                    @if(count($observacionesArray) > 0)
                                        <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;" class="custom-scrollbar">
                                            @foreach($observacionesArray as $index => $observacion)
                                                @php
                                                    // Extraer fecha si existe en el formato del texto
                                                    preg_match('/^(\d{1,2}\/\d{1,2}\/\d{4})\s*(.*)/', trim($observacion), $matches);
                                                    $fecha = $matches[1] ?? null;
                                                    $contenido = $matches[2] ?? trim($observacion);
                                                    
                                                    // Colores para diferentes tipos de eventos
                                                    $tipoColors = [
                                                        'LLEGA' => ['color' => '#28a745', 'icon' => 'fas fa-arrow-down'],
                                                        'SALE' => ['color' => '#dc3545', 'icon' => 'fas fa-arrow-up'],
                                                        'SE LLEVA' => ['color' => '#dc3545', 'icon' => 'fas fa-truck'],
                                                        'SE ENVIA' => ['color' => '#dc3545', 'icon' => 'fas fa-shipping-fast'],
                                                        'ENVIO' => ['color' => '#dc3545', 'icon' => 'fas fa-shipping-fast'],
                                                        'COMPRA' => ['color' => '#28a745', 'icon' => 'fas fa-shopping-cart'],
                                                        'ADQUISICION' => ['color' => '#28a745', 'icon' => 'fas fa-shopping-cart'],
                                                        'MANTENIMIENTO' => ['color' => '#ffc107', 'icon' => 'fas fa-tools'],
                                                        'REVISION' => ['color' => '#17a2b8', 'icon' => 'fas fa-search'],
                                                        'REPARACION' => ['color' => '#6f42c1', 'icon' => 'fas fa-wrench'],
                                                        'INGRESO' => ['color' => '#28a745', 'icon' => 'fas fa-sign-in-alt'],
                                                        'RETIRO' => ['color' => '#dc3545', 'icon' => 'fas fa-sign-out-alt']
                                                    ];
                                                    
                                                    $tipoDetectado = null;
                                                    foreach($tipoColors as $tipo => $config_tipo) {
                                                        if(stripos($contenido, $tipo) !== false) {
                                                            $tipoDetectado = $config_tipo;
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Icono por defecto consistente para todas las entradas
                                                    if(!$tipoDetectado) {
                                                        $tipoDetectado = [
                                                            'color' => '#17a2b8',
                                                            'icon' => 'fas fa-clipboard-check'
                                                        ];
                                                    }
                                                @endphp
                                                
                                                <div class="d-flex mb-3 position-relative" style="border-left: 2px solid #e9ecef; margin-left: 20px; padding-left: 30px; {{ $index === count($observacionesArray) - 1 ? 'border-left: 2px solid transparent;' : '' }}">
                                                    <div class="flex-shrink-0 position-relative" style="margin-left: -51px; z-index: 2;">
                                                        <div class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: linear-gradient(135deg, {{ $tipoDetectado['color'] }} 0%, {{ $tipoDetectado['color'] }}dd 100%); border-radius: 50%; color: white; border: 3px solid #ffffff; box-shadow: 0 3px 8px rgba(0,0,0,0.15);">
                                                            <i class="{{ $tipoDetectado['icon'] }}" style="font-size: 0.9rem;"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="p-3" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 8px; border: 1px solid #e9ecef; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="badge me-2" style="background-color: {{ $tipoDetectado['color'] }}; color: white; font-size: 0.7rem; padding: 0.3rem 0.6rem;">
                                                                        Entrada #{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                                                    </span>
                                                                    @if($fecha)
                                                                        <span class="badge" style="background-color: #6c757d; color: white; font-size: 0.7rem; padding: 0.3rem 0.6rem;">
                                                                            <i class="fas fa-calendar-alt me-1"></i>{{ $fecha }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <small class="text-muted">{{ $inventario->updated_at ? $inventario->updated_at->format('H:i') : '' }}</small>
                                                            </div>
                                                            <p class="mb-0" style="color: #212529; font-size: 0.95rem; line-height: 1.6; font-weight: 500;">{{ $contenido }}</p>
                                                            @if(strlen($contenido) > 80)
                                                                <div class="mt-2 pt-2" style="border-top: 1px solid #e9ecef;">
                                                                    <small class="text-muted">Registro detallado • {{ strlen($contenido) }} caracteres</small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <div class="mb-3">
                                                <i class="fas fa-book-open" style="color: #bdc3c7; font-size: 3rem;"></i>
                                            </div>
                                            <h5 class="font-weight-bold mb-2" style="color: #7f8c8d;">Bitácora Vacía</h5>
                                            <p class="mb-0" style="color: #95a5a6;">No se han registrado observaciones para este equipo</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                        
                        @if(count($observacionesArray) > 0)
                            <!-- Footer informativo -->
                            <div class="d-flex justify-content-center align-items-center pt-3" style="border-top: 1px solid #e9ecef;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clipboard-list me-2" style="color: #6c757d;"></i>
                                    <small class="text-muted">{{ count($observacionesArray) }} {{ count($observacionesArray) === 1 ? 'observación registrada' : 'observaciones registradas' }} en la bitácora</small>
                                </div>
                            </div>
                        @endif
                    </div>
                    

                </div>
            </div>
        </div>
    </div>


</div>

<!-- Modal para Imágenes -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Imagen del Equipo</h5>
                <div class="d-flex align-items-center">
                    <!-- Controles de imagen -->
                    <div class="btn-group me-3" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="rotateImage(-90)" title="Rotar izquierda">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="rotateImage(90)" title="Rotar derecha">
                            <i class="fas fa-redo"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="zoomImage(-0.2)" title="Zoom out">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="zoomImage(0.2)" title="Zoom in">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetImage()" title="Restablecer">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body text-center" style="overflow: hidden; position: relative; height: 70vh;">
                <div id="imageContainer" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; overflow: auto;">
                    <img id="modalImage" src="" alt="Imagen del equipo" style="max-width: 100%; max-height: 100%; transition: transform 0.3s ease; cursor: grab;">
                </div>
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

    /* Estilos unificados para todas las secciones/capítulos */
    .row.mb-4 {
        margin-bottom: 1.5rem !important;
    }

    .row.mb-4 .col-12 {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }

    .row.mb-4 .card {
        border: 1px solid #e9ecef !important;
        background-color: #ffffff !important;
        border-radius: 12px !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
        margin-bottom: 0 !important;
        width: 100% !important;
    }

    .row.mb-4 .card-header {
        background-color: #f8f9fa !important;
        border-bottom: 1px solid #e9ecef !important;
        padding: 1.25rem !important;
        border-radius: 12px 12px 0 0 !important;
    }

    /* SOBRESCRIBIR ESTILOS GLOBALES CON MÁXIMA ESPECIFICIDAD */
    body .container .row.mb-4 .col-12 .card .card-body,
    body .container .row.mb-4 .col-12 .card .card-body.p-4,
    body .container .row.mb-4 .col-12 .card .card-body.p-3 {
        background-color: #ffffff !important;
        padding: 1.5rem !important;
        border-radius: 0 0 12px 12px !important;
        margin: 0 !important;
        width: 100% !important;
    }

    .row.mb-4 .card h2 {
        color: #212529 !important;
        font-size: 1.5rem !important;
        margin-bottom: 0.25rem !important;
        font-weight: 600 !important;
    }

    .row.mb-4 .card p {
        color: #6c757d !important;
        font-size: 0.9rem !important;
        margin-bottom: 0 !important;
    }

    /* Estilos unificados para modo oscuro */
    [data-bs-theme="dark"] .row.mb-4 .card {
        border: 1px solid #475569 !important;
        background-color: #1e293b !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3) !important;
    }

    [data-bs-theme="dark"] .row.mb-4 .card-header {
        background-color: #334155 !important;
        border-bottom: 1px solid #475569 !important;
    }

    [data-bs-theme="dark"] body .container .row.mb-4 .col-12 .card .card-body,
    [data-bs-theme="dark"] body .container .row.mb-4 .col-12 .card .card-body.p-4,
    [data-bs-theme="dark"] body .container .row.mb-4 .col-12 .card .card-body.p-3 {
        background-color: #1e293b !important;
        padding: 1.5rem !important;
        margin: 0 !important;
        width: 100% !important;
    }

    [data-bs-theme="dark"] .row.mb-4 .card h2 {
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .row.mb-4 .card p {
        color: #cbd5e1 !important;
    }

    /* Estilos específicos para contenedores internos */
    .row.mb-4 .card .row.g-3 {
        margin: 0 !important;
    }

    .row.mb-4 .card .row.g-3 > .col-md-6 {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }

    /* Asegurar que todos los acordeones tengan el mismo estilo */
    .row.mb-4 .accordion {
        width: 100% !important;
    }

    .row.mb-4 .accordion-item {
        border: 1px solid #e9ecef !important;
        border-radius: 8px !important;
        margin-bottom: 0 !important;
    }

    [data-bs-theme="dark"] .row.mb-4 .accordion-item {
        border: 1px solid #475569 !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Variables globales para el control de imagen
    let currentRotation = 0;
    let currentScale = 1;
    let isDragging = false;
    let startX, startY, translateX = 0, translateY = 0;

    // Función para abrir el modal de imagen
    window.openImageModal = function(src, title = 'Imagen del Equipo') {
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalTitle');
        
        modalImage.src = src;
        modalImage.alt = title;
        modalTitle.textContent = title;
        
        // Resetear transformaciones
        resetImage();
        
        var modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();
    }

    // Función para rotar imagen
    window.rotateImage = function(degrees) {
        currentRotation += degrees;
        updateImageTransform();
    }

    // Función para hacer zoom
    window.zoomImage = function(factor) {
        currentScale += factor;
        if (currentScale < 0.1) currentScale = 0.1;
        if (currentScale > 5) currentScale = 5;
        updateImageTransform();
    }

    // Función para resetear imagen
    window.resetImage = function() {
        currentRotation = 0;
        currentScale = 1;
        translateX = 0;
        translateY = 0;
        updateImageTransform();
    }

    // Actualizar transformación de imagen
    function updateImageTransform() {
        const modalImage = document.getElementById('modalImage');
        modalImage.style.transform = `translate(${translateX}px, ${translateY}px) rotate(${currentRotation}deg) scale(${currentScale})`;
    }

    // Funcionalidad de arrastrar imagen
    document.addEventListener('DOMContentLoaded', function() {
        const modalImage = document.getElementById('modalImage');
        const imageContainer = document.getElementById('imageContainer');
        
        // Eventos de mouse para arrastrar
        modalImage.addEventListener('mousedown', function(e) {
            if (currentScale > 1) {
                isDragging = true;
                startX = e.clientX - translateX;
                startY = e.clientY - translateY;
                modalImage.style.cursor = 'grabbing';
                e.preventDefault();
            }
        });

        document.addEventListener('mousemove', function(e) {
            if (isDragging && currentScale > 1) {
                translateX = e.clientX - startX;
                translateY = e.clientY - startY;
                updateImageTransform();
            }
        });

        document.addEventListener('mouseup', function() {
            if (isDragging) {
                isDragging = false;
                modalImage.style.cursor = currentScale > 1 ? 'grab' : 'default';
            }
        });

        // Zoom con rueda del mouse
        imageContainer.addEventListener('wheel', function(e) {
            e.preventDefault();
            const zoomFactor = e.deltaY > 0 ? -0.1 : 0.1;
            zoomImage(zoomFactor);
        });

        // Actualizar cursor según el zoom
        modalImage.addEventListener('load', function() {
            modalImage.style.cursor = currentScale > 1 ? 'grab' : 'default';
        });

        // Cerrar modal al hacer clic fuera de la imagen
        document.getElementById('imageModal').addEventListener('click', function(event) {
            if (event.target === this) {
                const modalInstance = bootstrap.Modal.getInstance(this);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        });

        // Atajos de teclado
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('imageModal');
            if (modal.classList.contains('show')) {
                switch(e.key) {
                    case 'ArrowLeft':
                        rotateImage(-90);
                        break;
                    case 'ArrowRight':
                        rotateImage(90);
                        break;
                    case '+':
                    case '=':
                        zoomImage(0.2);
                        break;
                    case '-':
                        zoomImage(-0.2);
                        break;
                    case 'r':
                    case 'R':
                        resetImage();
                        break;
                }
            }
        });
    });
</script>
@endpush