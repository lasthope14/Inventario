@extends('layouts.app')
@section('content')
<div class="container-fluid px-4">
        <!-- Header Principal Profesional -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="header-card">
                <div class="header-main">
                    <div class="header-info">
                        <div class="header-title-section">
                            <div class="header-icon">
                                <i class="fas fa-cube"></i>
                            </div>
                            <div class="header-text">
                                <h1 class="header-title">{{ $inventario->nombre }}</h1>
                                <div class="header-badges">
                                    <span class="header-badge">
                                        <i class="fas fa-barcode me-1"></i>{{ $inventario->codigo_unico }}
                                    </span>
                                    <span class="header-badge">
                                        <i class="fas fa-tag me-1"></i>{{ $inventario->categoria->nombre }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stats profesionales -->
                        <div class="header-stats">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number">{{ $inventario->cantidad_total }}</div>
                                    <div class="stat-label">Total</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number">${{ number_format($inventario->valor_unitario, 0) }}</div>
                                    <div class="stat-label">Unitario</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number">{{ $inventario->ubicaciones->count() }}</div>
                                    <div class="stat-label">Ubicaciones</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number">{{ $inventario->movimientos->count() }}</div>
                                    <div class="stat-label">Movimientos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="header-actions">
                    @if(auth()->user()->role->name === 'administrador')
                        <a href="{{ route('inventarios.edit', $inventario) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Editar
                        </a>
                    @endif
                    <a href="{{ route('inventarios.index') }}" 
                           class="btn btn-outline-secondary"
                           id="volverBtn">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Detallada en Grid Moderno -->
    <div class="row g-4 mb-4">
        <!-- Información Básica -->
        <div class="col-xl-4 col-lg-6">
            <div class="info-card h-100">
                <div class="info-card-header">
                    <i class="fas fa-info-circle text-primary"></i>
                    <h5>Información Básica</h5>
                        </div>
                <div class="info-card-body">
                    <div class="info-item">
                        <span class="info-label">Propietario</span>
                        <span class="info-value">{{ $inventario->propietario }}</span>
                        </div>
                    <div class="info-item">
                        <span class="info-label">Proveedor</span>
                        <span class="info-value">{{ $inventario->proveedor->nombre }}</span>
                    </div>
                    @if($inventario->descripcion)
                    <div class="info-item">
                        <span class="info-label">Descripción</span>
                        <span class="info-value">{{ $inventario->descripcion }}</span>
                </div>
                    @endif
                        </div>
                        </div>
                    </div>

        <!-- Detalles Técnicos -->
        <div class="col-xl-4 col-lg-6">
            <div class="info-card h-100">
                <div class="info-card-header">
                    <i class="fas fa-cogs text-info"></i>
                    <h5>Detalles Técnicos</h5>
                </div>
                <div class="info-card-body">
                    @if($inventario->marca)
                    <div class="info-item">
                        <span class="info-label">Marca</span>
                        <span class="info-value">{{ $inventario->marca }}</span>
            </div>
                    @endif
                    @if($inventario->modelo)
                    <div class="info-item">
                        <span class="info-label">Modelo</span>
                        <span class="info-value">{{ $inventario->modelo }}</span>
                        </div>
                    @endif
                    @if($inventario->numero_serie)
                    <div class="info-item">
                        <span class="info-label">Número de Serie</span>
                        <span class="info-value">{{ $inventario->numero_serie }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información Financiera -->
        <div class="col-xl-4 col-lg-12">
            <div class="info-card h-100">
                <div class="info-card-header">
                    <i class="fas fa-dollar-sign text-success"></i>
                    <h5>Información Financiera</h5>
                        </div>
                <div class="info-card-body">
                    <div class="info-item">
                        <span class="info-label">Valor Total</span>
                        <span class="info-value fw-bold text-success">
                            ${{ number_format($inventario->valor_unitario * $inventario->cantidad_total, 2) }}
                        </span>
                    </div>
                    @if($inventario->fecha_compra)
                    <div class="info-item">
                        <span class="info-label">Fecha de Compra</span>
                        <span class="info-value">{{ $inventario->fecha_compra->format('d/m/Y') }}</span>
                </div>
                    @endif
                    @if($inventario->numero_factura)
                    <div class="info-item">
                        <span class="info-label">Factura</span>
                        <span class="info-value">{{ $inventario->numero_factura }}</span>
                        </div>
                    @endif
                        </div>
                    </div>
                </div>
            </div>

    <!-- Sección de Ubicaciones Moderna -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <i class="fas fa-map-marker-alt text-warning"></i>
                    <h5>Distribución por Ubicaciones</h5>
                </div>
                <div class="info-card-body">
                    @php
                        $ubicacionesConCantidad = $inventario->ubicaciones->filter(function($ubicacion) {
                            return $ubicacion->cantidad > 0;
                        });
                        $totalUbicaciones = $ubicacionesConCantidad->count();
                    @endphp
                    
                    @if($totalUbicaciones > 0)
                        <div class="locations-grid" data-count="{{ $totalUbicaciones }}">
                            @foreach($ubicacionesConCantidad as $ubicacion)
                                                                <div class="location-item">
                                    <div class="location-card">
                                        <div class="location-icon">
                                            <i class="fas fa-building"></i>
                    </div>
                                        <h6 class="location-name">{{ $ubicacion->ubicacion->nombre }}</h6>
                                        <div class="location-details">
                                            <span class="quantity-badge">{{ $ubicacion->cantidad }} unidades</span>
                                            <span class="status-badge status-{{ str_replace(' ', '-', $ubicacion->estado) }}">
                                                    {{ ucfirst($ubicacion->estado) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                        @endforeach
                    </div>
                    @else
                        <div class="empty-locations">
                            <i class="fas fa-map-marker-alt"></i>
                            <p>No hay ubicaciones con cantidad disponible</p>
                                </div>
                            @endif
                </div>
                    </div>
                </div>
            </div>

    <!-- Galería de Imágenes Moderna -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="info-card">
                <div class="info-card-header">
                    <i class="fas fa-images text-purple"></i>
                    <h5>Galería de Imágenes</h5>
                </div>
                <div class="info-card-body">
                    <div class="image-gallery">
                        <!-- Imagen Principal -->
                        <div class="image-container primary">
                            <div class="image-frame">
                                    @if($inventario->imagen_principal)
                                        <img src="{{ asset('storage/' . $inventario->imagen_principal) }}" 
                                             alt="Imagen principal" 
                                         class="gallery-image"
                                             onclick="openImageModal('{{ asset('storage/' . $inventario->imagen_principal) }}')">
                                    <div class="image-overlay">
                                        <i class="fas fa-search-plus"></i>
                                        <span>Ver imagen completa</span>
                                    </div>
                                    @else
                                    <div class="image-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Imagen principal<br><small>No disponible</small></p>
                                        </div>
                                    @endif
                                <div class="image-label">Principal</div>
                                </div>
                            </div>
                        
                        <!-- Imagen Secundaria -->
                        <div class="image-container secondary">
                            <div class="image-frame">
                                    @if($inventario->imagen_secundaria)
                                        <img src="{{ asset('storage/' . $inventario->imagen_secundaria) }}" 
                                             alt="Imagen secundaria" 
                                         class="gallery-image"
                                             onclick="openImageModal('{{ asset('storage/' . $inventario->imagen_secundaria) }}')">
                                    <div class="image-overlay">
                                        <i class="fas fa-search-plus"></i>
                                        <span>Ver imagen completa</span>
                                    </div>
                                    @else
                                    <div class="image-placeholder">
                                        <i class="fas fa-image"></i>
                                        <p>Imagen secundaria<br><small>No disponible</small></p>
                                        </div>
                                    @endif
                                <div class="image-label">Secundaria</div>
                            </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <!-- Observaciones Modernas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="info-card">
                                <div class="info-card-header clickable-header" id="observacionesHeader">
                    <i class="fas fa-sticky-note text-orange"></i>
                    <h5>Observaciones</h5>
                    <div class="toggle-arrow ms-auto" id="toggleObservaciones">
                            <i class="fas fa-chevron-down"></i>
                </div>
                </div>
                <div class="info-card-body" id="observacionesContent" style="display: none;">
                    @if($inventario->observaciones)
                        <div class="observations-container">
                            @foreach(explode("\n", $inventario->observaciones) as $index => $observacion)
                                @if(trim($observacion) !== '')
                                    <div class="observation-item">
                                        <div class="observation-marker">{{ $index + 1 }}</div>
                                        <div class="observation-content">
                                            <p>{{ trim($observacion) }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="empty-observations">
                            <i class="fas fa-sticky-note"></i>
                            <p>No hay observaciones registradas para este elemento</p>
                        </div>
                    @endif
                </div>
            </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Historial y Documentación</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-fill flex-column flex-sm-row" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active w-100" id="movimientos-tab" data-bs-toggle="tab" data-bs-target="#movimientos" type="button" role="tab" aria-controls="movimientos" aria-selected="true">Movimientos</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100" id="mantenimientos-tab" data-bs-toggle="tab" data-bs-target="#mantenimientos" type="button" role="tab" aria-controls="mantenimientos" aria-selected="false">Mantenimientos</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab" aria-controls="documentos" aria-selected="false">Documentos</button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="myTabContent">
                        <div class="tab-pane fade show active" id="movimientos" role="tabpanel" aria-labelledby="movimientos-tab">
                            @if(auth()->user()->role->name === 'administrador' || auth()->user()->role->name === 'almacenista')
                                <a href="{{ route('movimientos.create', ['inventario_id' => $inventario->id]) }}" class="btn btn-primary mb-3">
                                    <i class="fas fa-plus me-2"></i>Registrar Movimiento
                                </a>
                            @endif
                            @if($inventario->movimientos->isEmpty())
                                <div class="alert alert-info" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>No hay movimientos registrados para este elemento.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">Fecha</th>
                                                <th class="text-center">Origen</th>
                                                <th class="text-center">Destino</th>
                                                <th class="text-center">Realizado por</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inventario->movimientos->sortByDesc('fecha_movimiento')->take(5) as $movimiento)
                                                <tr>
                                                    <td class="text-center">{{ $movimiento->fecha_movimiento ? $movimiento->fecha_movimiento->format('d/m/Y H:i') : $movimiento->created_at->format('d/m/Y H:i') }}</td>
                                                    <td class="text-center">
                                                        @php
                                                            $ubicacionOrigen = \App\Models\Ubicacion::find($movimiento->ubicacion_origen);
                                                        @endphp
                                                        {{ $ubicacionOrigen ? $ubicacionOrigen->nombre : $movimiento->ubicacion_origen }}
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $ubicacionDestino = \App\Models\Ubicacion::find($movimiento->ubicacion_destino);
                                                        @endphp
                                                        {{ $ubicacionDestino ? $ubicacionDestino->nombre : $movimiento->ubicacion_destino }}
                                                    </td>
                                                    <td class="text-center">{{ $movimiento->realizadoPor ? $movimiento->realizadoPor->name : 'N/A' }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ route('movimientos.show', $movimiento) }}" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <a href="{{ route('movimientos.index', ['inventario_id' => $inventario->id]) }}" class="btn btn-link">Ver todos los movimientos</a>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="mantenimientos" role="tabpanel" aria-labelledby="mantenimientos-tab">
                            @if(auth()->user()->role->name === 'administrador' || auth()->user()->role->name === 'almacenista')
                                <a href="{{ route('mantenimientos.create', ['inventario_id' => $inventario->id]) }}" class="btn btn-primary mb-3">
                                    <i class="fas fa-plus me-2"></i>Programar Mantenimiento
                                </a>
                            @endif
                            @if($inventario->mantenimientos->isEmpty())
                                <div class="alert alert-info" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>No hay mantenimientos programados para este elemento.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">Tipo</th>
                                                <th class="text-center">Descripción</th>
                                                <th class="text-center">Fecha Programada</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Pospuesto</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inventario->mantenimientos->sortBy('fecha_programada')->take(5) as $mantenimiento)
                                                <tr>
                                                    <td class="text-center">{{ ucfirst($mantenimiento->tipo) }}</td>
                                                    <td class="text-center">{{ Str::limit($mantenimiento->descripcion, 50) }}</td>
                                                    <td class="text-center">{{ $mantenimiento->fecha_programada->format('d/m/Y') }}</td>
                                                    <td class="text-center">
                                                        @if($mantenimiento->fecha_realizado)
                                                            <span class="badge bg-success">Realizado</span>
                                                        @elseif($mantenimiento->tipo === 'correctivo')
                                                            <span class="badge bg-danger">Urgente</span
                                                            @else
                                                            <span class="badge bg-warning">Pendiente</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($mantenimiento->veces_pospuesto > 0)
                                                            <span class="badge bg-secondary">{{ $mantenimiento->veces_pospuesto }} {{ $mantenimiento->veces_pospuesto == 1 ? 'vez' : 'veces' }}</span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('mantenimientos.show', $mantenimiento) }}" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if(!$mantenimiento->fecha_realizado && (auth()->user()->role->name === 'administrador'))
                                                            <button onclick="marcarRealizado({{ $mantenimiento->id }})" class="btn btn-sm btn-outline-success">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button onclick="posponerMantenimiento({{ $mantenimiento->id }})" class="btn btn-sm btn-outline-warning">
                                                                <i class="fas fa-clock"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <a href="{{ route('mantenimientos.index', ['inventario_id' => $inventario->id]) }}" class="btn btn-link">Ver todos los mantenimientos</a>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="documentos" role="tabpanel" aria-labelledby="documentos-tab">
                            @if(auth()->user()->role->name === 'administrador' || auth()->user()->role->name === 'almacenista')
                                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#uploadDocumentoModal">
                                    <i class="fas fa-plus me-2"></i>Añadir Documento
                                </button>
                            @endif
                            @if($inventario->documentos->isEmpty())
                                <div class="alert alert-info" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>No hay documentos asociados a este elemento.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">Nombre del Documento</th>
                                                <th class="text-center">Fecha de Subida</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inventario->documentos as $documento)
                                                <tr>
                                                    <td class="text-center">{{ $documento->nombre }}</td>
                                                    <td class="text-center">{{ $documento->created_at->format('d/m/Y H:i') }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ route('documentos.download', $documento) }}?v={{ $documento->updated_at->timestamp }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        @if(auth()->user()->role->name === 'administrador')
                                                            <form action="{{ route('documentos.destroy', $documento) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este documento?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver imágenes en grande -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body p-0">
                <img src="" class="img-fluid" id="modalImage" alt="Imagen ampliada">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para subir documentos -->
<div class="modal fade" id="uploadDocumentoModal" tabindex="-1" aria-labelledby="uploadDocumentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentoModalLabel">Subir Nuevo Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="inventario_id" value="{{ $inventario->id }}">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Documento</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="archivo" class="form-label">Archivo</label>
                        <input type="file" class="form-control" id="archivo" name="archivo" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Subir Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<style>
    /* Estilos Base */
    body {
        background-color: #f8f9fa;
    }
    
    /* Header Profesional */
    .header-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }
    
    .header-main {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 2rem;
        gap: 2rem;
    }
    
    .header-info {
        flex: 1;
    }
    
    .header-title-section {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .header-icon {
        width: 60px;
        height: 60px;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    
    .header-text {
        flex: 1;
    }
    
    .header-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 0.75rem 0;
        line-height: 1.2;
    }
    
    .header-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .header-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        border: 1px solid #e2e8f0;
    }
    
    .header-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1.5rem;
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .stat-item:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 1rem;
        flex-shrink: 0;
    }
    
    .stat-content {
        flex: 1;
    }
    
    .stat-number {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 500;
    }
    
    .header-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        flex-shrink: 0;
    }
    
    @media (max-width: 768px) {
        .header-main {
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .header-actions {
            flex-direction: row;
            align-self: stretch;
        }
        
        .header-stats {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .stat-item {
            padding: 0.75rem;
        }
        
        .header-title {
            font-size: 1.5rem;
        }
    }
    
    /* Cards de Información */
    .info-card {
        background: white;
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .info-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .info-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .info-card-header i {
        font-size: 1.25rem;
    }
    
    .info-card-header h5 {
        margin: 0;
        font-weight: 600;
        color: #2d3748;
    }
    
    .info-card-body {
        padding: 1.5rem;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .info-label {
        font-weight: 500;
        color: #718096;
        font-size: 0.875rem;
        min-width: 100px;
        text-align: left;
    }
    
    .info-value {
        font-weight: 600;
        color: #2d3748;
        text-align: right;
        word-break: break-word;
        max-width: 60%;
    }
    
    /* Location Cards Mejoradas - Layout Vertical */
    .location-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 0.75rem;
        transition: all 0.2s ease;
        height: 100%;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        min-height: 140px;
    }
    
    .location-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.12);
        border-color: #cbd5e1;
    }
    
    .location-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }
    
    .location-name {
        font-weight: 600;
        color: #1e293b;
        margin: 0;
        font-size: 1rem;
        line-height: 1.2;
    }
    
    .location-details {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
    }
    
    .quantity-badge, .status-badge {
        width: 100%;
        text-align: center;
        padding: 0.375rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid transparent;
    }
    
    .quantity-badge {
        background: #f1f5f9;
        color: #475569;
        border-color: #e2e8f0;
        text-transform: none;
        font-weight: 500;
    }
    
    .status-badge {
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    
    .status-disponible {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }
    
    .status-en-uso {
        background: #dbeafe;
        color: #1e40af;
        border-color: #93c5fd;
    }
    
    .status-en-mantenimiento {
        background: #fef3c7;
        color: #a16207;
        border-color: #fcd34d;
    }
    
    .status-dado-de-baja {
        background: #fee2e2;
        color: #dc2626;
        border-color: #fca5a5;
    }
    
    .status-robado {
        background: #fee2e2;
        color: #991b1b;
        border-color: #f87171;
    }
    
    /* Grid centrado para ubicaciones */
    .locations-grid {
        display: grid;
        gap: 1.5rem;
        justify-content: center;
        align-items: start;
    }
    
    /* Responsive grid que se centra según la cantidad - Más compacto */
    .locations-grid[data-count="1"] {
        grid-template-columns: 180px;
        justify-content: center;
    }
    
    .locations-grid[data-count="2"] {
        grid-template-columns: repeat(2, 180px);
        justify-content: center;
    }
    
    .locations-grid[data-count="3"] {
        grid-template-columns: repeat(3, 180px);
        justify-content: center;
    }
    
    .locations-grid[data-count="4"] {
        grid-template-columns: repeat(4, 180px);
        justify-content: center;
    }
    
    .locations-grid[data-count="5"] {
        grid-template-columns: repeat(5, 180px);
        justify-content: center;
    }
    
    .locations-grid[data-count="6"] {
        grid-template-columns: repeat(6, 180px);
        justify-content: center;
    }
    
    /* Para más de 6 ubicaciones, usar grid automático */
    .locations-grid:not([data-count="1"]):not([data-count="2"]):not([data-count="3"]):not([data-count="4"]):not([data-count="5"]):not([data-count="6"]) {
        grid-template-columns: repeat(auto-fit, 180px);
        max-width: 1080px;
        margin: 0 auto;
        justify-content: center;
    }
    
    .location-item {
        width: 100%;
    }
    
    .empty-locations {
        text-align: center;
        padding: 3rem 1rem;
        color: #64748b;
    }
    
    .empty-locations i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    .empty-locations p {
        font-size: 1.125rem;
        margin: 0;
    }
    
        /* Responsive para móviles */
    @media (max-width: 768px) {
        .locations-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 1rem;
            max-width: 400px;
        }
        
        .location-card {
            padding: 1rem;
            min-height: 120px;
        }
        
        .location-icon {
            width: 32px;
            height: 32px;
            font-size: 0.875rem;
        }
        
        .location-name {
            font-size: 0.875rem;
        }
        
        .quantity-badge, .status-badge {
            font-size: 0.6875rem;
            padding: 0.25rem 0.375rem;
        }
    }
    
    @media (max-width: 480px) {
        .locations-grid {
            grid-template-columns: 1fr !important;
            max-width: 200px;
        }
    }
     
     /* Colores para iconos */
     .text-purple {
         color: #8b5cf6 !important;
     }
     
     .text-orange {
         color: #f59e0b !important;
     }
     
     /* Galería de Imágenes Moderna */
     .image-gallery {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 2rem;
         max-width: 800px;
         margin: 0 auto;
     }
     
     .image-container {
         position: relative;
     }
     
     .image-frame {
         position: relative;
         background: white;
         border: 1px solid #e2e8f0;
         border-radius: 12px;
         overflow: hidden;
         box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
         transition: all 0.3s ease;
         aspect-ratio: 4/3;
     }
     
     .image-frame:hover {
         transform: translateY(-2px);
         box-shadow: 0 8px 25px -3px rgba(0, 0, 0, 0.12);
         border-color: #cbd5e1;
     }
     
     .gallery-image {
         width: 100%;
         height: 100%;
         object-fit: cover;
         cursor: zoom-in;
         transition: transform 0.3s ease;
     }
     
     .image-frame:hover .gallery-image {
         transform: scale(1.05);
     }
     
     .image-overlay {
         position: absolute;
         top: 0;
         left: 0;
         right: 0;
         bottom: 0;
         background: rgba(0, 0, 0, 0.7);
         display: flex;
         flex-direction: column;
         align-items: center;
         justify-content: center;
         opacity: 0;
         transition: opacity 0.3s ease;
         color: white;
         font-size: 0.875rem;
         font-weight: 500;
         gap: 0.5rem;
     }
     
     .image-frame:hover .image-overlay {
         opacity: 1;
     }
     
     .image-overlay i {
         font-size: 2rem;
         margin-bottom: 0.5rem;
     }
     
     .image-placeholder {
         width: 100%;
         height: 100%;
         display: flex;
         flex-direction: column;
         align-items: center;
         justify-content: center;
         color: #94a3b8;
         background: #f8fafc;
         text-align: center;
         padding: 2rem;
     }
     
     .image-placeholder i {
         font-size: 3rem;
         margin-bottom: 1rem;
         opacity: 0.5;
     }
     
     .image-placeholder p {
         margin: 0;
         font-weight: 500;
         line-height: 1.4;
     }
     
     .image-placeholder small {
         color: #64748b;
         font-weight: 400;
     }
     
     .image-label {
         position: absolute;
         top: 0.75rem;
         left: 0.75rem;
         background: rgba(255, 255, 255, 0.95);
         color: #475569;
         padding: 0.375rem 0.75rem;
         border-radius: 6px;
         font-size: 0.75rem;
         font-weight: 600;
         text-transform: uppercase;
         letter-spacing: 0.025em;
         backdrop-filter: blur(4px);
         border: 1px solid rgba(0, 0, 0, 0.1);
     }
     
     /* Responsive para galería */
     @media (max-width: 768px) {
         .image-gallery {
             grid-template-columns: 1fr;
             gap: 1.5rem;
             max-width: 400px;
         }
         
                   .image-frame {
              aspect-ratio: 3/2;
          }
      }
      
      /* Observaciones Modernas */
      .observations-container {
          display: flex;
          flex-direction: column;
          gap: 1rem;
      }
      
      .observation-item {
          display: flex;
          align-items: flex-start;
          gap: 1rem;
          padding: 1rem;
          background: #f8fafc;
          border: 1px solid #e2e8f0;
          border-radius: 8px;
          transition: all 0.2s ease;
      }
      
      .observation-item:hover {
          background: #f1f5f9;
          border-color: #cbd5e1;
      }
      
      .observation-marker {
          width: 28px;
          height: 28px;
          background: #3b82f6;
          color: white;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 0.75rem;
          font-weight: 600;
          flex-shrink: 0;
          margin-top: 0.125rem;
      }
      
      .observation-content {
          flex: 1;
          min-width: 0;
      }
      
      .observation-content p {
          margin: 0;
          color: #374151;
          line-height: 1.6;
          font-size: 0.875rem;
      }
      
      .empty-observations {
          text-align: center;
          padding: 3rem 1rem;
          color: #64748b;
      }
      
      .empty-observations i {
          font-size: 3rem;
          margin-bottom: 1rem;
          opacity: 0.5;
      }
      
             .empty-observations p {
           font-size: 1rem;
           margin: 0;
       }
       
       /* Header clickeable para observaciones */
       .clickable-header {
           cursor: pointer;
           transition: background-color 0.2s ease;
       }
       
       .clickable-header:hover {
           background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
       }
       
       /* Flecha toggle sin recuadro */
       .toggle-arrow {
           width: 24px;
           height: 24px;
           display: flex;
           align-items: center;
           justify-content: center;
           cursor: pointer;
           color: #64748b;
           transition: all 0.2s ease;
           border-radius: 4px;
       }
       
       .toggle-arrow:hover {
           color: #475569;
           background: rgba(0, 0, 0, 0.05);
       }
       
       .toggle-arrow i {
           font-size: 0.875rem;
           transition: transform 0.2s ease;
    }
    
    .card-header {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    
    .form-control, .form-select {
        border-radius: 10px;
    }
    
    /* Header y Botones */
    .header-fixed {
        position: sticky;
        top: 65px;
        left: 0;
        right: 0;
        z-index: 1040;
        margin-bottom: 1.5rem;
        transition: box-shadow 0.3s ease;
    }

    .header-fixed.scrolled {
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .header-content {
        background-color: #0d6efd;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .header-content h1 {
        font-size: 1.5rem;
        margin: 0;
        color: white;
    }

    .header-content .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }

    .header-content .btn-light {
        background-color: #fff;
        border-color: #fff;
        color: #0d6efd;
    }

    .header-content .btn-light:hover {
        background-color: #f8f9fa;
        border-color: #f8f9fa;
    }

    /* Observaciones */
    .observaciones-container {
        max-height: 300px;
        overflow-y: auto;
        padding-right: 10px;
    }
    
    .observacion-item {
        margin-bottom: 10px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
        border-left: 3px solid #0d6efd;
    }
    
    .observacion-item:last-child {
        margin-bottom: 0;
    }

    #toggleObservaciones:focus {
        box-shadow: none;
    }

    /* Badges y Estados */
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.7em;
        border-radius: 0.5rem;
    }

    /* Tabs */
    .nav-tabs {
        border-bottom: none;
        display: flex;
        gap: 0.5rem;
    }

    .nav-tabs .nav-link {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        color: #495057;
        background-color: #f8f9fa;
        transition: all 0.2s ease-in-out;
    }

    .nav-tabs .nav-link.active {
        background-color: #0d6efd;
        color: #ffffff;
        border-color: #0d6efd;
    }

    /* Tablas */
    .table-responsive {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    /* Modal de Imágenes */
    #imageModal .modal-dialog {
        max-width: 90%;
        margin: 1.75rem auto;
    }

    #imageModal .modal-content {
        background-color: transparent;
        border: none;
    }

    #imageModal .modal-body {
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
    }

    #imageModal img {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
    }

    /* Responsive Design */
    @media (max-width: 767px) {
        .header-fixed {
            top: 56px;
        }

        .header-content {
            padding: 0.75rem;
        }

        .header-content h1 {
            font-size: 1.1rem;
        }

        .header-content .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        .header-content .btn i {
            margin-right: 0 !important;
            font-size: 1rem;
        }

        .header-content .btn span {
            display: none;
        }

        .nav-tabs {
            flex-direction: column;
        }

        .nav-tabs .nav-link {
            width: 100%;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .table td, .table th {
            padding: 0.5rem;
            font-size: 0.875rem;
        }

        .d-flex.gap-2 {
            gap: 0.375rem !important;
        }
    }

    @media (min-width: 768px) {
        .nav-tabs {
            flex-direction: row;
        }

        .nav-tabs .nav-item {
            flex: 1;
        }

        .header-content .btn span {
            display: inline;
        }
    }

    /* Utilidades */
    .cursor-pointer {
        cursor: pointer;
    }

    /* Dark Theme Styles - Contraste Mejorado */
    [data-bs-theme="dark"] body {
        background-color: #0f172a;
    }

    [data-bs-theme="dark"] .header-card {
        background: #1e293b;
        border-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .header-icon {
        background: #334155;
        border-color: #475569;
        color: #cbd5e1;
    }

    [data-bs-theme="dark"] .header-title {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .header-badge {
        background: #334155;
        color: #f8fafc;
        border-color: #475569;
    }

    [data-bs-theme="dark"] .stat-item {
        background: #334155;
        border-color: #475569;
    }

    [data-bs-theme="dark"] .stat-item:hover {
        background: #475569;
        border-color: #64748b;
    }

    [data-bs-theme="dark"] .stat-icon {
        background: #1e293b;
        border-color: #475569;
        color: #cbd5e1;
    }

    [data-bs-theme="dark"] .stat-number {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .stat-label {
        color: #cbd5e1;
    }

    [data-bs-theme="dark"] .info-card {
        background: #1e293b;
        border-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .info-card:hover {
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
    }

    [data-bs-theme="dark"] .info-card-header {
        background: linear-gradient(135deg, #334155 0%, #475569 100%);
        border-bottom-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .info-card-header h5 {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .info-card-body {
        background: #1e293b;
    }

    [data-bs-theme="dark"] .info-item {
        border-bottom-color: #475569;
    }

    [data-bs-theme="dark"] .info-label {
        color: #cbd5e1;
    }

    [data-bs-theme="dark"] .info-value {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .location-card {
        background: #1e293b;
        border-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .location-card:hover {
        border-color: #64748b;
        box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.4);
    }

    [data-bs-theme="dark"] .location-name {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .quantity-badge {
        background: #334155;
        color: #f8fafc;
        border-color: #475569;
    }

    [data-bs-theme="dark"] .empty-locations {
        color: #cbd5e1;
    }

    [data-bs-theme="dark"] .empty-locations i {
        color: #64748b;
    }

    [data-bs-theme="dark"] .image-frame {
        background: #1e293b;
        border-color: #475569;
    }

    [data-bs-theme="dark"] .image-placeholder {
        background: #334155;
        color: #64748b;
    }

    [data-bs-theme="dark"] .image-placeholder small {
        color: #475569;
    }

    [data-bs-theme="dark"] .observation-item {
        background: #334155;
        border-color: #475569;
    }

    [data-bs-theme="dark"] .observation-item:hover {
        background: #475569;
        border-color: #64748b;
    }

    [data-bs-theme="dark"] .observation-content p {
        color: #e2e8f0;
    }

    [data-bs-theme="dark"] .empty-observations {
        color: #cbd5e1;
    }

    [data-bs-theme="dark"] .empty-observations i {
        color: #64748b;
    }

    [data-bs-theme="dark"] .clickable-header:hover {
        background: linear-gradient(135deg, #475569 0%, #334155 100%);
    }

    [data-bs-theme="dark"] .toggle-arrow {
        color: #cbd5e1;
    }

    [data-bs-theme="dark"] .toggle-arrow:hover {
        color: #f8fafc;
        background: rgba(255, 255, 255, 0.1);
    }

    [data-bs-theme="dark"] .card {
        background: #1e293b;
        border-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .card-header {
        background: #334155;
        border-bottom-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .card-title {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .nav-tabs .nav-link {
        background-color: #334155;
        color: #cbd5e1;
        border-color: #475569;
    }

    [data-bs-theme="dark"] .nav-tabs .nav-link.active {
        background-color: #3b82f6;
        color: #ffffff;
        border-color: #3b82f6;
    }

    [data-bs-theme="dark"] .nav-tabs .nav-link:hover {
        background-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .table {
        --bs-table-bg: #1e293b;
        --bs-table-color: #f8fafc;
        --bs-table-border-color: #475569;
        background-color: #1e293b;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .table th {
        background-color: #334155;
        color: #f8fafc;
        border-bottom-color: #475569;
    }

    [data-bs-theme="dark"] .table td {
        border-bottom-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .table tbody tr:hover {
        background-color: #334155;
    }

    [data-bs-theme="dark"] .table-light {
        --bs-table-bg: #334155;
        --bs-table-color: #f8fafc;
    }

    [data-bs-theme="dark"] .alert-info {
        background-color: #1e3a8a;
        border-color: #3b82f6;
        color: #e0f2fe;
    }

    [data-bs-theme="dark"] .modal-content {
        background-color: #1e293b;
        border-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .modal-header {
        border-bottom-color: #475569;
        background-color: #334155;
    }

    [data-bs-theme="dark"] .modal-title {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .modal-footer {
        border-top-color: #475569;
        background-color: #334155;
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

    [data-bs-theme="dark"] .form-label {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    [data-bs-theme="dark"] .badge.bg-success {
        background-color: #22c55e !important;
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .badge.bg-warning {
        background-color: #f59e0b !important;
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .badge.bg-danger {
        background-color: #ef4444 !important;
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .badge.bg-secondary {
        background-color: #64748b !important;
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .text-success {
        color: #22c55e !important;
    }

    /* Alternative dark theme using body class for show view */
    body.dark-theme .header-card {
        background: #1e293b !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .header-icon {
        background: #334155 !important;
        border-color: #475569 !important;
        color: #cbd5e1 !important;
    }

    body.dark-theme .header-title {
        color: #f8fafc !important;
    }

    body.dark-theme .header-badge {
        background: #334155 !important;
        color: #f8fafc !important;
        border-color: #475569 !important;
    }

    body.dark-theme .stat-item {
        background: #334155 !important;
        border-color: #475569 !important;
    }

    body.dark-theme .stat-item:hover {
        background: #475569 !important;
        border-color: #64748b !important;
    }

    body.dark-theme .stat-icon {
        background: #1e293b !important;
        border-color: #475569 !important;
        color: #cbd5e1 !important;
    }

    body.dark-theme .stat-number {
        color: #f8fafc !important;
    }

    body.dark-theme .stat-label {
        color: #cbd5e1 !important;
    }

    body.dark-theme .info-card {
        background: #1e293b !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .info-card-header {
        background: linear-gradient(135deg, #334155 0%, #475569 100%) !important;
        border-bottom-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .info-card-header h5 {
        color: #f8fafc !important;
    }

    body.dark-theme .info-card-body {
        background: #1e293b !important;
    }

    body.dark-theme .info-item {
        border-bottom-color: #475569 !important;
    }

    body.dark-theme .info-label {
        color: #cbd5e1 !important;
    }

    body.dark-theme .info-value {
        color: #f8fafc !important;
    }

    body.dark-theme .location-card {
        background: #1e293b !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .location-card:hover {
        border-color: #64748b !important;
    }

    body.dark-theme .location-name {
        color: #f8fafc !important;
    }

    body.dark-theme .quantity-badge {
        background: #334155 !important;
        color: #f8fafc !important;
        border-color: #475569 !important;
    }

    body.dark-theme .empty-locations {
        color: #cbd5e1 !important;
    }

    body.dark-theme .empty-locations i {
        color: #64748b !important;
    }

    body.dark-theme .image-frame {
        background: #1e293b !important;
        border-color: #475569 !important;
    }

    body.dark-theme .image-placeholder {
        background: #334155 !important;
        color: #64748b !important;
    }

    body.dark-theme .observation-item {
        background: #334155 !important;
        border-color: #475569 !important;
    }

    body.dark-theme .observation-item:hover {
        background: #475569 !important;
        border-color: #64748b !important;
    }

    body.dark-theme .observation-content p {
        color: #e2e8f0 !important;
    }

    body.dark-theme .empty-observations {
        color: #cbd5e1 !important;
    }

    body.dark-theme .empty-observations i {
        color: #64748b !important;
    }

    body.dark-theme .card {
        background: #1e293b !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .card-header {
        background: #334155 !important;
        border-bottom-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .card-title {
        color: #f8fafc !important;
    }

    body.dark-theme .nav-tabs .nav-link {
        background-color: #334155 !important;
        color: #cbd5e1 !important;
        border-color: #475569 !important;
    }

    body.dark-theme .nav-tabs .nav-link.active {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
        border-color: #3b82f6 !important;
    }

    body.dark-theme .nav-tabs .nav-link:hover {
        background-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .table {
        --bs-table-bg: #1e293b;
        --bs-table-color: #f8fafc;
        --bs-table-border-color: #475569;
        background-color: #1e293b !important;
        color: #f8fafc !important;
    }

    body.dark-theme .table th {
        background-color: #334155 !important;
        color: #f8fafc !important;
        border-bottom-color: #475569 !important;
    }

    body.dark-theme .table td {
        border-bottom-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .table tbody tr:hover {
        background-color: #334155 !important;
    }

    body.dark-theme .table-light {
        --bs-table-bg: #334155;
        --bs-table-color: #f8fafc;
    }

    body.dark-theme .alert-info {
        background-color: #1e3a8a !important;
        border-color: #3b82f6 !important;
        color: #e0f2fe !important;
    }

    body.dark-theme .modal-content {
        background-color: #1e293b !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .modal-header {
        border-bottom-color: #475569 !important;
        background-color: #334155 !important;
    }

    body.dark-theme .modal-title {
        color: #f8fafc !important;
    }

    body.dark-theme .modal-footer {
        border-top-color: #475569 !important;
        background-color: #334155 !important;
    }

    body.dark-theme .form-control {
        background-color: #334155 !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .form-control:focus {
        background-color: #334155 !important;
        border-color: #3b82f6 !important;
        color: #f8fafc !important;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4) !important;
    }

    body.dark-theme .form-label {
        color: #f8fafc !important;
    }

    body.dark-theme .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%) !important;
    }

    body.dark-theme .badge.bg-success {
        background-color: #22c55e !important;
        color: #ffffff !important;
    }

    body.dark-theme .badge.bg-warning {
        background-color: #f59e0b !important;
        color: #ffffff !important;
    }

    body.dark-theme .badge.bg-danger {
        background-color: #ef4444 !important;
        color: #ffffff !important;
    }

    body.dark-theme .badge.bg-secondary {
        background-color: #64748b !important;
        color: #ffffff !important;
    }

    body.dark-theme .text-success {
        color: #22c55e !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funciones de imágenes
    window.openImageModal = function(src) {
        const modalImage = document.getElementById('modalImage');
        if (modalImage) {
            modalImage.src = src;
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
        }
    }

    // Funciones de mantenimiento
    window.marcarRealizado = function(mantenimientoId) {
        if (confirm('¿Estás seguro de que quieres marcar este mantenimiento como realizado?')) {
            fetch(`/mantenimientos/${mantenimientoId}/marcar-realizado`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Hubo un error al marcar el mantenimiento como realizado.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al procesar la solicitud.');
            });
        }
    }

    window.posponerMantenimiento = function(mantenimientoId) {
        if (confirm('¿Estás seguro de que quieres posponer este mantenimiento?')) {
            fetch(`/mantenimientos/${mantenimientoId}/posponer`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Hubo un error al posponer el mantenimiento.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al procesar la solicitud.');
            });
        }
    }

    // Manejador de observaciones
    const toggleBtn = document.getElementById('toggleObservaciones');
    const observacionesContent = document.getElementById('observacionesContent');
    const observacionesHeader = document.getElementById('observacionesHeader');
    
    function toggleObservaciones() {
            if (observacionesContent.style.display === 'none') {
                observacionesContent.style.display = 'block';
                toggleBtn.innerHTML = '<i class="fas fa-chevron-up"></i>';
            } else {
                observacionesContent.style.display = 'none';
                toggleBtn.innerHTML = '<i class="fas fa-chevron-down"></i>';
            }
    }
    
    if (toggleBtn && observacionesContent) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleObservaciones();
        });
    }
    
    if (observacionesHeader && observacionesContent) {
        observacionesHeader.addEventListener('click', function() {
            toggleObservaciones();
        });
    }

    // Manejador de scroll
    const header = document.querySelector('.header-fixed');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 0) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // Sistema de navegación mejorado para conservar estado
    const volverBtn = document.getElementById('volverBtn');
    if (volverBtn) {
        volverBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Intentar obtener la URL de retorno guardada
            let returnUrl = sessionStorage.getItem('inventarios_return_url');
            
            console.log('URL de retorno encontrada:', returnUrl);
            
            // Si no hay URL guardada, usar la URL por defecto
            if (!returnUrl) {
                returnUrl = '{{ route("inventarios.index") }}';
                console.log('Usando URL por defecto:', returnUrl);
            }
            
            // Limpiar el estado guardado
            sessionStorage.removeItem('inventarios_return_url');
            
            // Navegar a la URL
            console.log('Navegando a:', returnUrl);
            window.location.href = returnUrl;
        });
    } else {
        console.log('Botón volver no encontrado');
    }
});
</script>
@endpush