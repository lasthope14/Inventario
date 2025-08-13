@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid px-4">
    <!-- Header Principal -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1"><i class="fas fa-exchange-alt text-primary me-2"></i>Movimientos Masivos</h1>
                    <p class="text-muted mb-0">Gestiona múltiples movimientos de inventario de forma eficiente</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#revertirMovimientosModal">
                        <i class="fas fa-undo me-2"></i>Revertir Movimientos
                    </button>
                    <a href="{{ route('movimientos.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Movimientos
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>¡Atención!</strong> Se encontraron los siguientes errores:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulario de Información General -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Movimiento</h5>
        </div>
        <div class="card-body">
            <form id="movimientoMasivoForm">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="usuario_origen_id" class="form-label fw-semibold">
                            <i class="fas fa-user text-primary me-1"></i>Empleado de Origen
                        </label>
                        <select name="usuario_origen_id" id="usuario_origen_id" class="form-select" required>
                            <option value="">Seleccione empleado origen</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id }}">
                                    {{ $empleado->nombre }} {{ $empleado->cargo ? '- ' . $empleado->cargo : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="usuario_destino_id" class="form-label fw-semibold">
                            <i class="fas fa-user-check text-success me-1"></i>Empleado de Destino
                        </label>
                        <select name="usuario_destino_id" id="usuario_destino_id" class="form-select" required>
                            <option value="">Seleccione empleado destino</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id }}">
                                    {{ $empleado->nombre }} {{ $empleado->cargo ? '- ' . $empleado->cargo : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_movimiento" class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt text-info me-1"></i>Fecha y Hora
                        </label>
                        <div class="position-relative">
                            <input type="text" class="form-control flatpickr" id="fecha_movimiento" name="fecha_movimiento" placeholder="Seleccione fecha y hora" style="padding-left: 40px;">
                            <i class="fas fa-calendar-alt position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #6c757d; pointer-events: none;"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="motivo" class="form-label fw-semibold">
                            <i class="fas fa-comment-alt text-warning me-1"></i>Motivo
                        </label>
                        <textarea name="motivo" id="motivo" class="form-control" rows="2" placeholder="Describe el motivo del movimiento..." style="resize: vertical; min-height: 45px;"></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Paneles Principales -->
    <div class="row g-4">
        <!-- Panel Izquierdo - Elementos Disponibles -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Elementos Disponibles</h5>
                            <small class="opacity-75">Selecciona los elementos que deseas mover</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success btn-sm" id="moverSeleccionados" disabled>
                                <i class="fas fa-arrow-right me-1"></i>Mover <span class="badge bg-light text-dark ms-1" id="contadorBadge">0</span>
                            </button>
                            <button type="button" class="btn btn-light btn-sm" id="refreshElementos" title="Actualizar">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <!-- Controles de Filtro -->
                    <div class="filter-section mb-3">
                        <div class="row g-2 mb-2">
                            <div class="col-md-7">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" id="filtroTexto" class="form-control" placeholder="Buscar por nombre o código...">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <select id="filtroUbicacionOrigen" class="form-select form-select-sm">
                                    <option value="">📍 Todas las ubicaciones</option>
                                    @foreach($ubicaciones as $ubicacion)
                                        <option value="{{ $ubicacion->id }}">📍 {{ $ubicacion->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Botones de Selección -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary" id="seleccionarTodos">
                                    <i class="fas fa-check-square me-1"></i>Todos
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="deseleccionarTodos">
                                    <i class="fas fa-square me-1"></i>Ninguno
                                </button>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="contadorSeleccionados">0</span> seleccionado(s)
                            </small>
                        </div>
                    </div>
                    
                    <!-- Contenedor de Elementos -->
                    <div id="elementosDisponibles" class="elementos-container">
                        <div class="empty-placeholder">
                            <i class="fas fa-spinner fa-spin"></i>
                            <h5>Cargando elementos...</h5>
                            <p class="mb-0">Por favor espera mientras se cargan los elementos disponibles</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho - Elementos a Mover -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-gradient-success text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="fas fa-arrow-right me-2"></i>Elementos a Mover</h5>
                            <small class="opacity-75">Revisa y confirma los elementos seleccionados</small>
                        </div>
                        <div class="d-flex gap-2">
                            <select id="ubicacionDestino" class="form-select form-select-sm" style="width: 180px;">
                                <option value="">🎯 Seleccione destino</option>
                                @foreach($ubicaciones as $ubicacion)
                                    <option value="{{ $ubicacion->id }}">🎯 {{ $ubicacion->nombre }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-light btn-sm" id="limpiarDestino" title="Limpiar todo">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <!-- Contenedor de Elementos Destino -->
                    <div id="elementosDestino" class="elementos-container drop-zone">
                        <div class="empty-placeholder">
                            <i class="fas fa-arrow-right"></i>
                            <h5>Panel de destino vacío</h5>
                            <p class="mb-0">Selecciona elementos del panel izquierdo para moverlos aquí</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary fs-6 px-3 py-2">
                                <i class="fas fa-boxes me-1"></i>
                                <span id="totalElementos">0</span> elementos
                            </span>
                            <button type="button" class="btn btn-outline-warning btn-sm" id="revertirUltimo" disabled title="Revertir último elemento agregado">
                                <i class="fas fa-undo me-1"></i>Revertir
                            </button>
                        </div>
                        <button type="button" class="btn btn-success btn-lg" id="procesarMovimientos" disabled>
                            <i class="fas fa-paper-plane me-2"></i>Procesar Movimientos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación con z-index alto -->
<div class="modal fade" id="confirmacionModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmación Requerida
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-question-circle fa-3x text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-2">¿Está seguro de continuar?</h6>
                        <p class="mb-0 text-muted" id="confirmacionMensaje">Esta acción no se puede deshacer.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="confirmarAccion">
                    <i class="fas fa-check me-1"></i>Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Éxito -->
<div class="modal fade" id="exitoModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>¡Operación Exitosa!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-2 text-success">¡Perfecto!</h6>
                        <p class="mb-0 text-muted" id="exitoMensaje">La operación se completó correctamente.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                    <i class="fas fa-thumbs-up me-1"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Error -->
<div class="modal fade" id="errorModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-circle me-2"></i>Error Detectado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle fa-3x text-danger"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-2 text-danger">¡Ups! Algo salió mal</h6>
                        <p class="mb-0 text-muted" id="errorMensaje">Ha ocurrido un error inesperado.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar elemento -->
<div class="modal fade" id="editarElementoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Elemento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Elemento</label>
                    <input type="text" class="form-control" id="modalElementoNombre" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Cantidad a Mover</label>
                            <input type="number" class="form-control" id="modalCantidad" min="1">
                            <small class="form-text text-muted">Máximo: <span id="modalMaxCantidad"></span></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nuevo Estado</label>
                            <select class="form-control" id="modalEstado">
                                <option value="disponible">Disponible</option>
                                <option value="en uso">En uso</option>
                                <option value="en mantenimiento">En mantenimiento</option>
                                <option value="dado de baja">Dado de baja</option>
                                <option value="robado">Robado</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarElemento">Guardar</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Variables CSS para consistencia */
    :root {
        --primary-gradient: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        --success-gradient: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        --border-radius: 0.5rem;
        --container-height: 450px;
    }

    /* Gradientes para headers */
    .bg-gradient-primary {
        background: var(--primary-gradient) !important;
    }
    
    .bg-gradient-success {
        background: var(--success-gradient) !important;
    }

    /* Contenedores principales con altura fija */
    .elementos-container {
        height: var(--container-height) !important;
        max-height: var(--container-height) !important;
        min-height: var(--container-height) !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        border: 1px solid #e9ecef;
        border-radius: var(--border-radius);
        background: #ffffff;
        padding: 0;
        position: relative;
        box-sizing: border-box;
    }
    
    /* Zona de drop mejorada */
    .drop-zone {
        border: 2px dashed #28a745 !important;
        background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%) !important;
        transition: all 0.3s ease;
    }
    
    .drop-zone:hover {
        border-color: #20c997 !important;
        background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%) !important;
        transform: scale(1.01);
    }

    /* Diseño tipo tabla mejorado */
    .elemento-row {
        display: grid;
        grid-template-columns: 45px 1fr 110px 150px;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid #f8f9fa;
        background: white;
        transition: all 0.3s ease;
        cursor: pointer;
        min-height: 70px;
        position: relative;
    }
    
    .elemento-row:hover {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-left: 4px solid #007bff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
    }
    
    .elemento-row.seleccionado {
        background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
        border-left: 4px solid #28a745;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15);
    }
    
    .elemento-row.en-destino {
        grid-template-columns: 1fr 110px 120px 90px;
        background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
        border-left: 4px solid #28a745;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.1);
    }

    /* Columnas del grid mejoradas */
    .elemento-checkbox-col {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .elemento-checkbox {
        width: 20px !important;
        height: 20px !important;
        cursor: pointer;
        margin: 0 !important;
        border: 2px solid #dee2e6;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    
    .elemento-checkbox:checked {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        transform: scale(1.1);
    }
    
    .elemento-checkbox:hover {
        border-color: #007bff;
        transform: scale(1.05);
    }

    .elemento-info-col {
        padding-right: 16px;
        min-width: 0;
    }
    
    .elemento-nombre {
        font-weight: 600;
        color: #2c3e50;
        font-size: 15px;
        line-height: 1.4;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .elemento-codigo {
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        font-size: 12px;
        color: #6c757d;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
        font-weight: 500;
        border: 1px solid #e9ecef;
    }

    .elemento-cantidad-col {
        text-align: center;
        padding: 0 12px;
    }
    
    .cantidad-disponible {
        display: block;
        font-size: 20px;
        font-weight: 700;
        color: #007bff;
        line-height: 1;
        margin-bottom: 2px;
    }
    
    .cantidad-label {
        font-size: 11px;
        color: #6c757d;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .elemento-controls-col {
        justify-content: center;
        align-items: center;
        padding: 0 12px;
    }
    
    .cantidad-controls {
        display: flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%);
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid rgba(40, 167, 69, 0.3);
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.1);
    }
    
    .cantidad-controls label {
        font-size: 12px;
        font-weight: 600;
        color: #28a745;
        margin: 0;
        white-space: nowrap;
    }
    
    .cantidad-input {
        width: 55px !important;
        height: 32px !important;
        text-align: center;
        font-weight: 600;
        border: 2px solid #28a745;
        border-radius: 6px;
        font-size: 13px;
        padding: 0 6px;
        background: white;
        transition: all 0.2s ease;
    }
    
    .cantidad-input:focus {
        border-color: #20c997;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        outline: none;
        transform: scale(1.05);
    }

    /* Columnas específicas del panel destino */
    .elemento-estado-col {
        text-align: center;
        padding: 0 12px;
    }
    
    .estado-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        display: inline-block;
    }
    
    .estado-disponible { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724; }
    .estado-en-uso { background: linear-gradient(135deg, #cce5ff 0%, #b3d7ff 100%); color: #004085; }
    .estado-en-mantenimiento { background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); color: #856404; }
    .estado-dado-de-baja { background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); color: #721c24; }
    .estado-robado { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; }
    
    .elemento-actions-col {
        display: flex;
        justify-content: center;
        gap: 6px;
        padding: 0 12px;
    }
    
    .btn-action {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .btn-edit {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }
    
    .btn-edit:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }
    
    .btn-remove {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }
    
    .btn-remove:hover {
        background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        transform: scale(1.1) rotate(-5deg);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    /* Placeholder mejorado */
    .empty-placeholder {
        text-align: center;
        padding: 80px 30px;
        color: #6c757d;
        background: linear-gradient(135deg, rgba(108, 117, 125, 0.03) 0%, rgba(108, 117, 125, 0.01) 100%);
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        margin: 20px;
        height: calc(100% - 40px);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        box-sizing: border-box;
        transition: all 0.3s ease;
    }
    
    .empty-placeholder:hover {
        border-color: #007bff;
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.05) 0%, rgba(0, 123, 255, 0.02) 100%);
    }
    
    .empty-placeholder i {
        font-size: 3rem;
        margin-bottom: 20px;
        opacity: 0.6;
        color: #007bff;
    }
    
    .empty-placeholder h5 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 10px;
    }

    /* Scrollbar personalizada mejorada */
    .elementos-container::-webkit-scrollbar {
        width: 8px;
    }
    
    .elementos-container::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 10px;
        margin: 5px;
    }
    
    .elementos-container::-webkit-scrollbar-thumb {
        background: var(--primary-gradient);
        border-radius: 10px;
        border: 1px solid #e9ecef;
    }
    
    .elementos-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    }
    
    /* Scroll para el contenedor de destino */
    #elementosDestino::-webkit-scrollbar-thumb {
        background: var(--success-gradient);
    }
    
    #elementosDestino::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #1e7e34 0%, #155724 100%);
    }

    /* Sección de filtros mejorada */
    .filter-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: var(--border-radius);
        padding: 16px;
        border: 1px solid #e9ecef;
        box-shadow: var(--shadow-sm);
    }

    /* Cards mejoradas */
    .card {
        border: none;
        box-shadow: var(--shadow-md);
        border-radius: var(--border-radius);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.2);
    }
    
    .card-header {
        border: none;
        font-weight: 600;
    }
    
    .card-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(248, 249, 250, 0.8) !important;
    }

    /* Botones mejorados */
    .btn {
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-success {
        background: var(--success-gradient);
        border: none;
    }
    
    .btn-primary {
        background: var(--primary-gradient);
        border: none;
    }

    /* Badges mejorados */
    .badge {
        border-radius: 12px;
        font-weight: 600;
        box-shadow: var(--shadow-sm);
    }

    /* Animaciones suaves */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .elemento-row {
        animation: slideInUp 0.4s ease;
    }

    /* Estilos para modales profesionales */
    .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .modal-header {
        border-bottom: none;
        border-radius: 15px 15px 0 0;
        padding: 20px 25px 15px;
    }
    
    .modal-body {
        padding: 20px 25px;
    }
    
    .modal-footer {
        border-top: none;
        border-radius: 0 0 15px 15px;
        padding: 15px 25px 20px;
    }
    
    .modal-title {
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    /* Animaciones para modales */
    .modal.fade .modal-dialog {
        transform: scale(0.8) translateY(-50px);
        transition: all 0.3s ease;
    }
    
    .modal.show .modal-dialog {
        transform: scale(1) translateY(0);
    }
    
    /* Estilos para Flatpickr */
    .flatpickr-input {
        background-image: none !important;
        padding-left: 40px !important;
    }
    
    .flatpickr-calendar {
        border-radius: 10px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
        border: none !important;
    }
    
    .flatpickr-day.selected {
        background: var(--primary-gradient) !important;
        border-color: #007bff !important;
    }
    
    .flatpickr-day:hover {
        background: #e9ecef !important;
        border-color: #007bff !important;
    }
    
    .flatpickr-current-month .flatpickr-monthDropdown-months,
    .flatpickr-current-month input.cur-year {
        background: var(--primary-gradient) !important;
        color: white !important;
        border: none !important;
    }
    
    .flatpickr-time input {
        background: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 6px !important;
    }

    /* Responsive mejorado */
    @media (max-width: 768px) {
        :root {
            --container-height: 350px;
        }
        
        .elemento-row {
            grid-template-columns: 35px 1fr 90px 120px;
            padding: 12px 16px;
            font-size: 14px;
        }
        
        .elemento-row.en-destino {
            grid-template-columns: 1fr 90px 80px 70px;
        }
        
        .cantidad-disponible {
            font-size: 18px;
        }
        
        .btn-action {
            width: 28px;
            height: 28px;
            font-size: 11px;
        }
        
        .filter-section {
            padding: 12px;
        }
        
        .modal-dialog {
            margin: 10px;
        }
        
        .modal-header,
        .modal-body,
        .modal-footer {
            padding: 15px 20px;
        }
    }

    /* Dark Theme Styles - Contraste Mejorado */
    [data-bs-theme="dark"] body {
        background-color: #0f172a;
    }

    [data-bs-theme="dark"] .h3 {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .text-muted {
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .text-primary {
        color: #60a5fa !important;
    }

    [data-bs-theme="dark"] .card {
        background: #1e293b;
        border-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .card:hover {
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.4);
    }

    [data-bs-theme="dark"] .card-header {
        background: #334155;
        border-bottom-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .bg-gradient-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
    }

    [data-bs-theme="dark"] .bg-gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }

    [data-bs-theme="dark"] .card-body {
        background: #1e293b;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .card-footer {
        background: #334155 !important;
        border-top-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .filter-section {
        background: linear-gradient(135deg, #334155 0%, #475569 100%);
        border-color: #475569;
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

    [data-bs-theme="dark"] .form-control::placeholder {
        color: #94a3b8;
    }

    [data-bs-theme="dark"] .form-select {
        background-color: #334155;
        border-color: #475569;
        color: #f8fafc;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23f8fafc' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    }

    [data-bs-theme="dark"] .form-select:focus {
        background-color: #334155;
        border-color: #3b82f6;
        color: #f8fafc;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4);
    }

    [data-bs-theme="dark"] .form-label {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .fw-semibold {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .input-group-text {
        background-color: #334155;
        border-color: #475569;
        color: #cbd5e1;
    }

    [data-bs-theme="dark"] .elementos-container {
        background: #1e293b;
        border-color: #475569;
    }

    [data-bs-theme="dark"] .drop-zone {
        border-color: #10b981 !important;
        background: linear-gradient(135deg, #134e4a 0%, #155e63 100%) !important;
    }

    [data-bs-theme="dark"] .drop-zone:hover {
        border-color: #34d399 !important;
        background: linear-gradient(135deg, #155e63 0%, #0f766e 100%) !important;
    }

    [data-bs-theme="dark"] .elemento-row {
        background: #1e293b;
        border-bottom-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .elemento-row:hover {
        background: linear-gradient(135deg, #334155 0%, #475569 100%);
        border-left-color: #3b82f6;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
    }

    [data-bs-theme="dark"] .elemento-row.seleccionado {
        background: linear-gradient(135deg, #134e4a 0%, #155e63 100%);
        border-left-color: #10b981;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    [data-bs-theme="dark"] .elemento-row.en-destino {
        background: linear-gradient(135deg, #134e4a 0%, #155e63 100%);
        border-left-color: #10b981;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
    }

    [data-bs-theme="dark"] .elemento-nombre {
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .elemento-codigo {
        background: linear-gradient(135deg, #334155 0%, #475569 100%);
        color: #cbd5e1;
        border-color: #475569;
    }

    [data-bs-theme="dark"] .cantidad-disponible {
        color: #60a5fa;
    }

    [data-bs-theme="dark"] .cantidad-label {
        color: #94a3b8;
    }

    [data-bs-theme="dark"] .cantidad-controls {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(16, 185, 129, 0.15) 100%);
        border-color: rgba(16, 185, 129, 0.5);
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }

    [data-bs-theme="dark"] .cantidad-controls label {
        color: #34d399;
    }

    [data-bs-theme="dark"] .cantidad-input {
        background: #1e293b;
        border-color: #10b981;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .cantidad-input:focus {
        background: #1e293b;
        border-color: #34d399;
        color: #f8fafc;
        box-shadow: 0 0 0 0.2rem rgba(52, 211, 153, 0.4);
    }

    [data-bs-theme="dark"] .elemento-checkbox {
        background-color: #334155;
        border-color: #475569;
    }

    [data-bs-theme="dark"] .elemento-checkbox:checked {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
    }

    [data-bs-theme="dark"] .elemento-checkbox:hover {
        border-color: #3b82f6;
    }

    [data-bs-theme="dark"] .estado-disponible {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: #d1fae5;
    }

    [data-bs-theme="dark"] .estado-en-uso {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        color: #bfdbfe;
    }

    [data-bs-theme="dark"] .estado-en-mantenimiento {
        background: linear-gradient(135deg, #92400e 0%, #b45309 100%);
        color: #fcd34d;
    }

    [data-bs-theme="dark"] .estado-dado-de-baja {
        background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);
        color: #fca5a5;
    }

    [data-bs-theme="dark"] .estado-robado {
        background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
        color: white;
    }

    [data-bs-theme="dark"] .btn-edit {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    [data-bs-theme="dark"] .btn-edit:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4);
    }

    [data-bs-theme="dark"] .btn-remove {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    }

    [data-bs-theme="dark"] .btn-remove:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        box-shadow: 0 4px 8px rgba(220, 38, 38, 0.4);
    }

    [data-bs-theme="dark"] .empty-placeholder {
        background: linear-gradient(135deg, rgba(148, 163, 184, 0.05) 0%, rgba(148, 163, 184, 0.02) 100%);
        border-color: #475569;
        color: #94a3b8;
    }

    [data-bs-theme="dark"] .empty-placeholder:hover {
        border-color: #3b82f6;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(59, 130, 246, 0.04) 100%);
    }

    [data-bs-theme="dark"] .empty-placeholder i {
        color: #3b82f6;
    }

    [data-bs-theme="dark"] .empty-placeholder h5 {
        color: #cbd5e1;
    }

    [data-bs-theme="dark"] .elementos-container::-webkit-scrollbar-track {
        background: #334155;
    }

    [data-bs-theme="dark"] .elementos-container::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border: 1px solid #475569;
    }

    [data-bs-theme="dark"] .elementos-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    }

    [data-bs-theme="dark"] #elementosDestino::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    [data-bs-theme="dark"] #elementosDestino::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }

    [data-bs-theme="dark"] .btn {
        border-color: transparent;
    }

    [data-bs-theme="dark"] .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
    }

    [data-bs-theme="dark"] .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
    }

    [data-bs-theme="dark"] .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    [data-bs-theme="dark"] .btn-success:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
    }

    [data-bs-theme="dark"] .btn-outline-primary {
        color: #60a5fa;
        border-color: #3b82f6;
    }

    [data-bs-theme="dark"] .btn-outline-primary:hover {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }

    [data-bs-theme="dark"] .btn-outline-secondary {
        color: #cbd5e1;
        border-color: #64748b;
    }

    [data-bs-theme="dark"] .btn-outline-secondary:hover {
        background-color: #64748b;
        border-color: #64748b;
        color: white;
    }

    [data-bs-theme="dark"] .btn-outline-warning {
        color: #fbbf24;
        border-color: #f59e0b;
    }

    [data-bs-theme="dark"] .btn-outline-warning:hover {
        background-color: #f59e0b;
        border-color: #f59e0b;
        color: white;
    }

    [data-bs-theme="dark"] .btn-outline-danger {
        color: #f87171;
        border-color: #ef4444;
    }

    [data-bs-theme="dark"] .btn-outline-danger:hover {
        background-color: #ef4444;
        border-color: #ef4444;
        color: white;
    }

    [data-bs-theme="dark"] .badge {
        color: white;
    }

    [data-bs-theme="dark"] .badge.bg-light {
        background-color: #334155 !important;
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .badge.bg-primary {
        background-color: #3b82f6 !important;
    }

    [data-bs-theme="dark"] .badge.bg-secondary {
        background-color: #64748b !important;
    }

    [data-bs-theme="dark"] .modal-content {
        background-color: #1e293b;
        border-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .modal-header {
        background-color: #334155;
        border-bottom-color: #475569;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .modal-header.bg-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: white !important;
    }

    [data-bs-theme="dark"] .modal-header.bg-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white !important;
    }

    [data-bs-theme="dark"] .modal-header.bg-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: white !important;
    }

    [data-bs-theme="dark"] .modal-header.bg-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
        color: white !important;
    }

    [data-bs-theme="dark"] .modal-title {
        color: inherit;
    }

    [data-bs-theme="dark"] .modal-body {
        background-color: #1e293b;
        color: #f8fafc;
    }

    [data-bs-theme="dark"] .modal-footer {
        background-color: #334155;
        border-top-color: #475569;
    }

    [data-bs-theme="dark"] .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    [data-bs-theme="dark"] .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    [data-bs-theme="dark"] .alert {
        border-color: #475569;
    }

    [data-bs-theme="dark"] .alert-danger {
        background-color: #7f1d1d;
        border-color: #991b1b;
        color: #fca5a5;
    }

    [data-bs-theme="dark"] .alert-info {
        background-color: #1e3a8a;
        border-color: #3b82f6;
        color: #bfdbfe;
    }

    [data-bs-theme="dark"] .text-danger {
        color: #f87171 !important;
    }

    [data-bs-theme="dark"] .text-success {
        color: #34d399 !important;
    }

    [data-bs-theme="dark"] .text-warning {
        color: #fbbf24 !important;
    }

    [data-bs-theme="dark"] .text-info {
        color: #60a5fa !important;
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

    [data-bs-theme="dark"] .table-responsive {
        border-color: #475569;
    }

    [data-bs-theme="dark"] .spinner-border {
        color: #3b82f6;
    }

    [data-bs-theme="dark"] .flatpickr-calendar {
        background: #1e293b !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .flatpickr-day {
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .flatpickr-day:hover {
        background: #334155 !important;
        border-color: #3b82f6 !important;
    }

    [data-bs-theme="dark"] .flatpickr-day.selected {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
        border-color: #3b82f6 !important;
    }

    [data-bs-theme="dark"] .flatpickr-current-month .flatpickr-monthDropdown-months,
    [data-bs-theme="dark"] .flatpickr-current-month input.cur-year {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
        color: white !important;
        border: none !important;
    }

    [data-bs-theme="dark"] .flatpickr-time input {
        background: #334155 !important;
        border: 1px solid #475569 !important;
        color: #f8fafc !important;
    }

    /* Alternative dark theme using body class for masivo view */
    body.dark-theme .h3 {
        color: #f8fafc !important;
    }

    body.dark-theme .text-muted {
        color: #cbd5e1 !important;
    }

    body.dark-theme .text-primary {
        color: #60a5fa !important;
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

    body.dark-theme .bg-gradient-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
    }

    body.dark-theme .bg-gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }

    body.dark-theme .card-body {
        background: #1e293b !important;
        color: #f8fafc !important;
    }

    body.dark-theme .card-footer {
        background: #334155 !important;
        border-top-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .filter-section {
        background: linear-gradient(135deg, #334155 0%, #475569 100%) !important;
        border-color: #475569 !important;
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

    body.dark-theme .form-control::placeholder {
        color: #94a3b8 !important;
    }

    body.dark-theme .form-select {
        background-color: #334155 !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23f8fafc' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
    }

    body.dark-theme .form-select:focus {
        background-color: #334155 !important;
        border-color: #3b82f6 !important;
        color: #f8fafc !important;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4) !important;
    }

    body.dark-theme .form-label {
        color: #f8fafc !important;
    }

    body.dark-theme .fw-semibold {
        color: #f8fafc !important;
    }

    body.dark-theme .input-group-text {
        background-color: #334155 !important;
        border-color: #475569 !important;
        color: #cbd5e1 !important;
    }

    body.dark-theme .elementos-container {
        background: #1e293b !important;
        border-color: #475569 !important;
    }

    body.dark-theme .drop-zone {
        border-color: #10b981 !important;
        background: linear-gradient(135deg, #134e4a 0%, #155e63 100%) !important;
    }

    body.dark-theme .drop-zone:hover {
        border-color: #34d399 !important;
        background: linear-gradient(135deg, #155e63 0%, #0f766e 100%) !important;
    }

    body.dark-theme .elemento-row {
        background: #1e293b !important;
        border-bottom-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .elemento-row:hover {
        background: linear-gradient(135deg, #334155 0%, #475569 100%) !important;
        border-left-color: #3b82f6 !important;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2) !important;
    }

    body.dark-theme .elemento-row.seleccionado {
        background: linear-gradient(135deg, #134e4a 0%, #155e63 100%) !important;
        border-left-color: #10b981 !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3) !important;
    }

    body.dark-theme .elemento-row.en-destino {
        background: linear-gradient(135deg, #134e4a 0%, #155e63 100%) !important;
        border-left-color: #10b981 !important;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25) !important;
    }

    body.dark-theme .elemento-nombre {
        color: #f8fafc !important;
    }

    body.dark-theme .elemento-codigo {
        background: linear-gradient(135deg, #334155 0%, #475569 100%) !important;
        color: #cbd5e1 !important;
        border-color: #475569 !important;
    }

    body.dark-theme .cantidad-disponible {
        color: #60a5fa !important;
    }

    body.dark-theme .cantidad-label {
        color: #94a3b8 !important;
    }

    body.dark-theme .cantidad-controls {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(16, 185, 129, 0.15) 100%) !important;
        border-color: rgba(16, 185, 129, 0.5) !important;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2) !important;
    }

    body.dark-theme .cantidad-controls label {
        color: #34d399 !important;
    }

    body.dark-theme .cantidad-input {
        background: #1e293b !important;
        border-color: #10b981 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .cantidad-input:focus {
        background: #1e293b !important;
        border-color: #34d399 !important;
        color: #f8fafc !important;
        box-shadow: 0 0 0 0.2rem rgba(52, 211, 153, 0.4) !important;
    }

    body.dark-theme .elemento-checkbox {
        background-color: #334155 !important;
        border-color: #475569 !important;
    }

    body.dark-theme .elemento-checkbox:checked {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
    }

    body.dark-theme .elemento-checkbox:hover {
        border-color: #3b82f6 !important;
    }

    body.dark-theme .estado-disponible {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        color: #d1fae5 !important;
    }

    body.dark-theme .estado-en-uso {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
        color: #bfdbfe !important;
    }

    body.dark-theme .estado-en-mantenimiento {
        background: linear-gradient(135deg, #92400e 0%, #b45309 100%) !important;
        color: #fcd34d !important;
    }

    body.dark-theme .estado-dado-de-baja {
        background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%) !important;
        color: #fca5a5 !important;
    }

    body.dark-theme .estado-robado {
        background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%) !important;
        color: white !important;
    }

    body.dark-theme .btn-edit {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
    }

    body.dark-theme .btn-edit:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4) !important;
    }

    body.dark-theme .btn-remove {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
    }

    body.dark-theme .btn-remove:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%) !important;
        box-shadow: 0 4px 8px rgba(220, 38, 38, 0.4) !important;
    }

    body.dark-theme .empty-placeholder {
        background: linear-gradient(135deg, rgba(148, 163, 184, 0.05) 0%, rgba(148, 163, 184, 0.02) 100%) !important;
        border-color: #475569 !important;
        color: #94a3b8 !important;
    }

    body.dark-theme .empty-placeholder:hover {
        border-color: #3b82f6 !important;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(59, 130, 246, 0.04) 100%) !important;
    }

    body.dark-theme .empty-placeholder i {
        color: #3b82f6 !important;
    }

    body.dark-theme .empty-placeholder h5 {
        color: #cbd5e1 !important;
    }

    body.dark-theme .modal-content {
        background-color: #1e293b !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .modal-header {
        background-color: #334155 !important;
        border-bottom-color: #475569 !important;
        color: #f8fafc !important;
    }

    body.dark-theme .modal-header.bg-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: white !important;
    }

    body.dark-theme .modal-header.bg-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white !important;
    }

    body.dark-theme .modal-header.bg-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: white !important;
    }

    body.dark-theme .modal-header.bg-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
        color: white !important;
    }

    body.dark-theme .modal-title {
        color: inherit !important;
    }

    body.dark-theme .modal-body {
        background-color: #1e293b !important;
        color: #f8fafc !important;
    }

    body.dark-theme .modal-footer {
        background-color: #334155 !important;
        border-top-color: #475569 !important;
    }

    body.dark-theme .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%) !important;
    }

    body.dark-theme .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%) !important;
    }

    body.dark-theme .alert {
        border-color: #475569 !important;
    }

    body.dark-theme .alert-danger {
        background-color: #7f1d1d !important;
        border-color: #991b1b !important;
        color: #fca5a5 !important;
    }

    body.dark-theme .alert-info {
        background-color: #1e3a8a !important;
        border-color: #3b82f6 !important;
        color: #bfdbfe !important;
    }

    body.dark-theme .text-danger {
        color: #f87171 !important;
    }

    body.dark-theme .text-success {
        color: #34d399 !important;
    }

    body.dark-theme .text-warning {
        color: #fbbf24 !important;
    }

    body.dark-theme .text-info {
        color: #60a5fa !important;
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

    body.dark-theme .spinner-border {
        color: #3b82f6 !important;
    }

    /* Estilos para checkboxes en la tabla de movimientos */
    .movimiento-checkbox {
        width: 18px !important;
        height: 18px !important;
        cursor: pointer;
        border: 2px solid #dee2e6;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    
    .movimiento-checkbox:checked {
        background-color: #007bff !important;
        border-color: #007bff !important;
        transform: scale(1.05);
    }
    
    .movimiento-checkbox:hover {
        border-color: #007bff;
        transform: scale(1.02);
    }
    
    #selectAllMovimientos {
        width: 18px !important;
        height: 18px !important;
        cursor: pointer;
        border: 2px solid #dee2e6;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    
    #selectAllMovimientos:checked {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        transform: scale(1.05);
    }
    
    #selectAllMovimientos:indeterminate {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
        transform: scale(1.05);
    }
    
    #selectAllMovimientos:hover {
        border-color: #007bff;
        transform: scale(1.02);
    }
    
    /* Estilos para dark theme */
    [data-bs-theme="dark"] .movimiento-checkbox,
    body.dark-theme .movimiento-checkbox {
        background-color: #334155 !important;
        border-color: #475569 !important;
    }
    
    [data-bs-theme="dark"] .movimiento-checkbox:checked,
    body.dark-theme .movimiento-checkbox:checked {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
    }
    
    [data-bs-theme="dark"] .movimiento-checkbox:hover,
    body.dark-theme .movimiento-checkbox:hover {
        border-color: #3b82f6 !important;
    }
    
    [data-bs-theme="dark"] #selectAllMovimientos,
    body.dark-theme #selectAllMovimientos {
        background-color: #334155 !important;
        border-color: #475569 !important;
    }
    
    [data-bs-theme="dark"] #selectAllMovimientos:checked,
    body.dark-theme #selectAllMovimientos:checked {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
    }
    
    [data-bs-theme="dark"] #selectAllMovimientos:indeterminate,
    body.dark-theme #selectAllMovimientos:indeterminate {
        background-color: #f59e0b !important;
        border-color: #f59e0b !important;
    }
    
    [data-bs-theme="dark"] #selectAllMovimientos:hover,
    body.dark-theme #selectAllMovimientos:hover {
        border-color: #3b82f6 !important;
    }
    
    /* Mejorar la apariencia de filas seleccionadas */
    .table tbody tr:has(.movimiento-checkbox:checked) {
        background-color: rgba(0, 123, 255, 0.1) !important;
    }
    
    [data-bs-theme="dark"] .table tbody tr:has(.movimiento-checkbox:checked),
    body.dark-theme .table tbody tr:has(.movimiento-checkbox:checked) {
        background-color: rgba(59, 130, 246, 0.2) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Flatpickr para fecha y hora
    try {
        flatpickr("#fecha_movimiento", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            locale: "es",
            defaultDate: new Date(),
            // minDate: "today", // Comentado para permitir fechas anteriores
            minuteIncrement: 15,
            allowInput: true,
            clickOpens: true,
            placeholder: "Seleccione fecha y hora"
        });
        console.log('Datepicker inicializado correctamente');
    } catch (error) {
        console.error('Error al inicializar datepicker:', error);
    }

    let elementosDestino = [];
    let historialMovimientos = []; // Para revertir
    let todosLosElementos = []; // Cache de todos los elementos
    let accionPendiente = null; // Para manejar confirmaciones
    
    // Cargar elementos iniciales
    // cargarElementos(); // Se llamará después de definir la función
    
    // Event listeners para filtros
    document.getElementById('filtroUbicacionOrigen').addEventListener('change', function() {
        // Cuando cambia la ubicación, necesitamos cargar nuevos elementos del servidor
        window.cargarElementos();
    });
    document.getElementById('filtroTexto').addEventListener('input', debounce(aplicarFiltros, 300));
    document.getElementById('limpiarDestino').addEventListener('click', limpiarDestino);
    document.getElementById('revertirUltimo').addEventListener('click', revertirUltimo);
    document.getElementById('moverSeleccionados').addEventListener('click', moverElementosSeleccionados);
    document.getElementById('seleccionarTodos').addEventListener('click', seleccionarTodos);
    document.getElementById('deseleccionarTodos').addEventListener('click', deseleccionarTodos);
    
    // Función debounce para el filtro de texto
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Hacer cargarElementos global
    window.cargarElementos = function cargarElementos() {
        const ubicacionId = document.getElementById('filtroUbicacionOrigen').value;
        const refreshBtn = document.getElementById('refreshElementos');
        
        console.log('Cargando elementos para ubicación:', ubicacionId);
        
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        refreshBtn.disabled = true;
        
        fetch(`/movimientos/masivo/inventario-data?ubicacion_id=${ubicacionId}`)
            .then(response => {
                console.log('Respuesta del servidor:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Verificar si la respuesta es un error del servidor
                if (data && data.error) {
                    console.error('Error del servidor:', data);
                    todosLosElementos = [];
                    mostrarError('Error del servidor: ' + (data.message || data.error));
                    return;
                }
                
                // Validar que data sea un array
                if (Array.isArray(data)) {
                    todosLosElementos = data;
                    console.log('Elementos cargados:', todosLosElementos.length);
                } else {
                    console.error('La respuesta no es un array:', data);
                    todosLosElementos = [];
                    mostrarError('Error: La respuesta del servidor no tiene el formato esperado');
                }
                aplicarFiltros();
            })
            .catch(error => {
                console.error('Error completo:', error);
                todosLosElementos = [];
                mostrarError('Error al cargar elementos: ' + error.message);
            })
            .finally(() => {
                refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
                refreshBtn.disabled = false;
            });
    }; // Cierre de window.cargarElementos
    
    // Cargar elementos iniciales después de definir la función
    window.cargarElementos();
    
    // Event listener para el botón refresh (después de definir la función)
    document.getElementById('refreshElementos').addEventListener('click', window.cargarElementos);
    
    function aplicarFiltros() {
        // Validar que todosLosElementos sea un array antes de usar filter
        if (!Array.isArray(todosLosElementos)) {
            console.error('todosLosElementos no es un array:', todosLosElementos);
            todosLosElementos = [];
            mostrarError('Error: Los datos de elementos no están en el formato correcto');
            return;
        }
        
        // Guardar elementos seleccionados antes de filtrar
        const elementosSeleccionadosIds = Array.from(document.querySelectorAll('.elemento-checkbox:checked'))
            .map(checkbox => checkbox.dataset.elementoId);
        
        const ubicacionId = document.getElementById('filtroUbicacionOrigen').value;
        const textoFiltro = document.getElementById('filtroTexto').value.toLowerCase();
        
        let elementosFiltrados = todosLosElementos.filter(elemento => {
            // Validar que elemento tenga las propiedades necesarias
            if (!elemento || typeof elemento !== 'object') {
                console.warn('Elemento inválido encontrado:', elemento);
                return false;
            }
            
            // Filtro por ubicación
            if (ubicacionId && elemento.ubicacion_id != ubicacionId) {
                return false;
            }
            
            // Filtro por texto (código, nombre, descripción)
            if (textoFiltro) {
                const textoElemento = (
                    (elemento.codigo || '') + ' ' + 
                    (elemento.nombre || '') + ' ' + 
                    (elemento.descripcion || '') + ' ' +
                    (elemento.ubicacion_nombre || '')
                ).toLowerCase();
                
                if (!textoElemento.includes(textoFiltro)) {
                    return false;
                }
            }
            
            // No mostrar elementos que ya están en destino
            return !elementosDestino.some(dest => dest.id === elemento.id);
        });
        
        mostrarElementos(elementosFiltrados, elementosSeleccionadosIds);
    }
    
    function mostrarElementos(elementos, elementosSeleccionadosIds = []) {
        const container = document.getElementById('elementosDisponibles');
        
        if (elementos.length === 0) {
            container.innerHTML = `
                <div class="empty-placeholder">
                    <i class="fas fa-box-open"></i>
                    <h5>No hay elementos disponibles</h5>
                    <p class="mb-0">Selecciona una ubicación de origen para ver los elementos disponibles</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = elementos.map(elemento => {
            const isSelected = elementosSeleccionadosIds.includes(elemento.id.toString());
            return `
                <div class="elemento-row ${isSelected ? 'seleccionado' : ''}" data-elemento-id="${elemento.id}" data-elemento='${JSON.stringify(elemento)}'>
                    <div class="elemento-checkbox-col">
                        <input class="form-check-input elemento-checkbox" type="checkbox" 
                               id="check_${elemento.id}" data-elemento-id="${elemento.id}" ${isSelected ? 'checked' : ''}>
                    </div>
                    <div class="elemento-info-col">
                        <div class="elemento-nombre">${elemento.nombre}</div>
                        <div class="elemento-codigo">${elemento.codigo}</div>
                    </div>
                    <div class="elemento-cantidad-col">
                        <span class="cantidad-disponible">${elemento.cantidad_disponible}</span>
                        <span class="cantidad-label">unidades</span>
                        <div class="mt-1">
                            <span class="estado-badge estado-${elemento.estado.replace(/\s+/g, '-').toLowerCase()}">${elemento.estado}</span>
                        </div>
                    </div>
                    <div class="elemento-controls-col" style="display: ${isSelected ? 'flex' : 'none'};">
                        <div class="cantidad-controls">
                            <label>Mover:</label>
                            <input type="number" class="cantidad-input" 
                                   min="1" max="${elemento.cantidad_disponible}" value="1" 
                                   data-elemento-id="${elemento.id}">
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
        // Agregar event listeners a los checkboxes
        container.querySelectorAll('.elemento-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const card = this.closest('.elemento-row');
                const controls = card.querySelector('.elemento-controls-col');
                
                if (this.checked) {
                    card.classList.add('seleccionado');
                    controls.style.display = 'flex';
                } else {
                    card.classList.remove('seleccionado');
                    controls.style.display = 'none';
                }
                
                actualizarBotonMover();
            });
        });
        
        // Agregar event listeners a los elementos para marcar/desmarcar al hacer clic
        container.querySelectorAll('.elemento-row').forEach(item => {
            item.addEventListener('click', function(e) {
                // No hacer nada si se hizo clic en el checkbox, input de cantidad o label
                if (e.target.classList.contains('elemento-checkbox') || 
                    e.target.classList.contains('cantidad-input') ||
                    e.target.tagName === 'LABEL') {
                    return;
                }
                
                const checkbox = this.querySelector('.elemento-checkbox');
                checkbox.checked = !checkbox.checked;
                
                const controls = this.querySelector('.elemento-controls-col');
                
                // Actualizar apariencia del elemento
                if (checkbox.checked) {
                    this.classList.add('seleccionado');
                    controls.style.display = 'flex';
                } else {
                    this.classList.remove('seleccionado');
                    controls.style.display = 'none';
                }
                
                actualizarBotonMover();
            });
        });
        
        // Event listeners para inputs de cantidad
        container.querySelectorAll('.cantidad-input').forEach(input => {
            input.addEventListener('change', function() {
                const max = parseInt(this.getAttribute('max'));
                const value = parseInt(this.value);
                
                if (value < 1) this.value = 1;
                if (value > max) this.value = max;
            });
        });
        
        // Actualizar contador y botón
        actualizarBotonMover();
    }
    
    function actualizarBotonMover() {
        const checkboxesSeleccionados = document.querySelectorAll('.elemento-checkbox:checked');
        const botonMover = document.getElementById('moverSeleccionados');
        
        botonMover.disabled = checkboxesSeleccionados.length === 0;
        
        const contadorBadge = document.getElementById('contadorBadge');
        
        if (checkboxesSeleccionados.length > 0) {
            botonMover.innerHTML = `<i class="fas fa-arrow-right me-1"></i>Mover <span class="badge bg-light text-dark ms-1" id="contadorBadge">${checkboxesSeleccionados.length}</span>`;
        } else {
            botonMover.innerHTML = `<i class="fas fa-arrow-right me-1"></i>Mover <span class="badge bg-light text-dark ms-1" id="contadorBadge">0</span>`;
        }
        
        // Actualizar contador
        actualizarContador();
    }
    
    function actualizarContador() {
        const checkboxesSeleccionados = document.querySelectorAll('.elemento-checkbox:checked');
        document.getElementById('contadorSeleccionados').textContent = checkboxesSeleccionados.length;
    }
    
    function seleccionarTodos() {
        const checkboxes = document.querySelectorAll('.elemento-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
            const item = checkbox.closest('.elemento-row');
            const controls = item.querySelector('.elemento-controls-col');
            item.classList.add('seleccionado');
            controls.style.display = 'flex';
        });
        actualizarBotonMover();
    }
    
    function deseleccionarTodos() {
        const checkboxes = document.querySelectorAll('.elemento-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
            const item = checkbox.closest('.elemento-row');
            const controls = item.querySelector('.elemento-controls-col');
            item.classList.remove('seleccionado');
            controls.style.display = 'none';
        });
        actualizarBotonMover();
    }
    
    function moverElementosSeleccionados() {
        const checkboxesSeleccionados = document.querySelectorAll('.elemento-checkbox:checked');
        
        if (checkboxesSeleccionados.length === 0) {
            mostrarError('No hay elementos seleccionados');
            return;
        }
        
        // Obtener elementos seleccionados con sus cantidades
        const elementosSeleccionados = [];
        checkboxesSeleccionados.forEach(checkbox => {
            const elementoDiv = checkbox.closest('.elemento-row');
            const elemento = JSON.parse(elementoDiv.dataset.elemento);
            const cantidadInput = elementoDiv.querySelector('.cantidad-input');
            const cantidadMover = parseInt(cantidadInput.value) || 1;
            
            // Validar cantidad
            if (cantidadMover > elemento.cantidad_disponible) {
                mostrarError(`No puedes mover ${cantidadMover} unidades de ${elemento.nombre}. Solo hay ${elemento.cantidad_disponible} disponibles.`);
                return;
            }
            
            elemento.cantidad_mover = cantidadMover;
            elementosSeleccionados.push(elemento);
        });
        
        if (elementosSeleccionados.length === 0) return;
        
        // Agregar todos los elementos seleccionados al destino
        elementosSeleccionados.forEach(elemento => {
            agregarElementoDestino(elemento, elemento.cantidad_mover);
        });
        
        // Desmarcar todos los checkboxes y ocultar controles
        checkboxesSeleccionados.forEach(checkbox => {
            checkbox.checked = false;
            const card = checkbox.closest('.elemento-row');
            const controls = card.querySelector('.elemento-controls-col');
            card.classList.remove('seleccionado');
            controls.style.display = 'none';
        });
        
        // Actualizar botón
        actualizarBotonMover();
        
        mostrarExito(`${elementosSeleccionados.length} elemento(s) agregado(s) al destino`);
    }
    
    function agregarElementoDestino(elemento, cantidad = null, nuevoEstado = null) {
        // Verificar si ya existe
        if (elementosDestino.some(e => e.id === elemento.id)) {
            mostrarError('Este elemento ya está en la lista de destino');
            return;
        }
        
        const elementoDestino = {
            ...elemento,
            cantidad_mover: cantidad || Math.min(elemento.cantidad_disponible, 1),
            nuevo_estado: nuevoEstado || elemento.estado
        };
        
        elementosDestino.push(elementoDestino);
        historialMovimientos.push({
            accion: 'agregar',
            elemento: elementoDestino,
            timestamp: Date.now()
        });
        
        actualizarVistaDestino();
        aplicarFiltros(); // Refrescar lista origen
        actualizarBotones();
    }
    
    function actualizarVistaDestino() {
        const container = document.getElementById('elementosDestino');
        
        if (elementosDestino.length === 0) {
            container.innerHTML = `
                <div class="empty-placeholder">
                    <i class="fas fa-arrow-right"></i>
                    <h5>Panel de destino vacío</h5>
                    <p class="mb-0">Selecciona elementos del panel izquierdo para moverlos aquí</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = elementosDestino.map((elemento, index) => `
            <div class="elemento-row en-destino" data-index="${index}">
                <div class="elemento-info-col">
                    <div class="elemento-nombre">${elemento.nombre}</div>
                    <div class="elemento-codigo">${elemento.codigo}</div>
                </div>
                <div class="elemento-cantidad-col">
                    <span class="cantidad-disponible">${elemento.cantidad_mover}</span>
                    <span class="cantidad-label">a mover</span>
                </div>
                <div class="elemento-estado-col">
                    <span class="estado-badge estado-${elemento.nuevo_estado.replace(/\s+/g, '-').toLowerCase()}">${elemento.nuevo_estado}</span>
                </div>
                <div class="elemento-actions-col">
                    <button type="button" class="btn-action btn-edit" onclick="editarElemento(${index})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn-action btn-remove" onclick="removerElemento(${index})" title="Quitar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    function removerElemento(index) {
        const elemento = elementosDestino[index];
        elementosDestino.splice(index, 1);
        
        historialMovimientos.push({
            accion: 'remover',
            elemento: elemento,
            timestamp: Date.now()
        });
        
        actualizarVistaDestino();
        aplicarFiltros(); // Refrescar lista origen
        actualizarBotones();
    }
    
    function revertirUltimo() {
        if (historialMovimientos.length === 0) return;
        
        const ultimaAccion = historialMovimientos.pop();
        
        if (ultimaAccion.accion === 'agregar') {
            // Remover el último elemento agregado
            const index = elementosDestino.findIndex(e => e.id === ultimaAccion.elemento.id);
            if (index !== -1) {
                elementosDestino.splice(index, 1);
            }
        } else if (ultimaAccion.accion === 'remover') {
            // Volver a agregar el elemento removido
            elementosDestino.push(ultimaAccion.elemento);
        }
        
        actualizarVistaDestino();
        aplicarFiltros();
        actualizarBotones();
        
        // Mostrar notificación
        mostrarExito('Acción revertida correctamente');
    }
    
    function limpiarDestino() {
        if (elementosDestino.length === 0) return;
        
        if (confirm('¿Estás seguro de que quieres limpiar todos los elementos del destino?')) {
            elementosDestino = [];
            historialMovimientos = [];
            actualizarVistaDestino();
            aplicarFiltros();
            actualizarBotones();
            mostrarExito('Lista de destino limpiada');
        }
    }
    
    function actualizarBotones() {
        const totalElementos = elementosDestino.length;
        document.getElementById('totalElementos').textContent = totalElementos;
        document.getElementById('procesarMovimientos').disabled = totalElementos === 0;
        document.getElementById('revertirUltimo').disabled = historialMovimientos.length === 0;
    }
    
    // Función global para editar elemento
    window.editarElemento = function(index) {
        const elemento = elementosDestino[index];
        
        // Crear modal dinámico
        const modalHtml = `
            <div class="modal fade" id="editarElementoModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-edit me-2"></i>Editar Elemento
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">🏷️ Código:</label>
                                <p class="text-muted">${elemento.codigo}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">📦 Nombre:</label>
                                <p class="text-muted">${elemento.nombre}</p>
                            </div>
                            <div class="mb-3">
                                <label for="cantidadMover" class="form-label fw-bold">
                                    <i class="fas fa-sort-numeric-up me-1"></i>Cantidad a mover:
                                </label>
                                <input type="number" class="form-control" id="cantidadMover" 
                                       value="${elemento.cantidad_mover}" 
                                       min="1" max="${elemento.cantidad_disponible}">
                                <small class="text-muted">Disponible: ${elemento.cantidad_disponible}</small>
                            </div>
                            <div class="mb-3">
                                <label for="nuevoEstado" class="form-label fw-bold">
                                    <i class="fas fa-flag me-1"></i>Nuevo estado:
                                </label>
                                <select class="form-select" id="nuevoEstado">
                                    <option value="disponible" ${elemento.nuevo_estado === 'disponible' ? 'selected' : ''}>✅ Disponible</option>
                                    <option value="en uso" ${elemento.nuevo_estado === 'en uso' ? 'selected' : ''}>🔄 En uso</option>
                                    <option value="en mantenimiento" ${elemento.nuevo_estado === 'en mantenimiento' ? 'selected' : ''}>🔧 En mantenimiento</option>
                                    <option value="dado de baja" ${elemento.nuevo_estado === 'dado de baja' ? 'selected' : ''}>❌ Dado de baja</option>
                                    <option value="robado" ${elemento.nuevo_estado === 'robado' ? 'selected' : ''}>🚨 Robado</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="guardarCambiosElemento(${index})">
                                <i class="fas fa-save me-1"></i>Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remover modal anterior si existe
        const existingModal = document.getElementById('editarElementoModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Agregar nuevo modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('editarElementoModal'));
        modal.show();
    };
    
    // Función global para guardar cambios
    window.guardarCambiosElemento = function(index) {
        const cantidad = parseInt(document.getElementById('cantidadMover').value);
        const nuevoEstado = document.getElementById('nuevoEstado').value;
        
        if (cantidad < 1 || cantidad > elementosDestino[index].cantidad_disponible) {
            mostrarError('La cantidad debe estar entre 1 y ' + elementosDestino[index].cantidad_disponible);
            return;
        }
        
        elementosDestino[index].cantidad_mover = cantidad;
        elementosDestino[index].nuevo_estado = nuevoEstado;
        
        actualizarVistaDestino();
        
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('editarElementoModal'));
        modal.hide();
        
        mostrarExito('Elemento actualizado correctamente');
    };
    
    // Función global para remover elemento
    window.removerElemento = removerElemento;
    
    // Funciones para modales profesionales
    function mostrarError(mensaje) {
        document.getElementById('errorMensaje').textContent = mensaje;
        const modal = new bootstrap.Modal(document.getElementById('errorModal'));
        modal.show();
    }
    
    function mostrarExito(mensaje) {
        document.getElementById('exitoMensaje').textContent = mensaje;
        const modal = new bootstrap.Modal(document.getElementById('exitoModal'));
        modal.show();
    }
    
    function mostrarConfirmacion(mensaje, callback) {
        document.getElementById('confirmacionMensaje').textContent = mensaje;
        const modal = new bootstrap.Modal(document.getElementById('confirmacionModal'));
        
        // Limpiar event listeners anteriores
        const btnConfirmar = document.getElementById('confirmarAccion');
        const newBtn = btnConfirmar.cloneNode(true);
        btnConfirmar.parentNode.replaceChild(newBtn, btnConfirmar);
        
        // Agregar nuevo event listener
        newBtn.addEventListener('click', function() {
            modal.hide();
            if (callback) callback();
        });
        
        modal.show();
    }
    
    // Procesar movimientos
    document.getElementById('procesarMovimientos').addEventListener('click', function() {
        const ubicacionDestino = document.getElementById('ubicacionDestino').value;
        const usuarioOrigen = document.getElementById('usuario_origen_id').value;
        const usuarioDestino = document.getElementById('usuario_destino_id').value;
        
        if (!usuarioOrigen) {
            mostrarError('Debe seleccionar un empleado de origen');
            return;
        }
        
        if (!usuarioDestino) {
            mostrarError('Debe seleccionar un empleado de destino');
            return;
        }
        
        if (!ubicacionDestino) {
            mostrarError('Debe seleccionar una ubicación de destino');
            return;
        }
        
        if (elementosDestino.length === 0) {
            mostrarError('No hay elementos para mover');
            return;
        }
        
        // Usar modal de confirmación en lugar de confirm()
        mostrarConfirmacion(
            `¿Confirma el movimiento de ${elementosDestino.length} elemento(s)? Esta acción no se puede deshacer.`,
            () => procesarMovimientosConfirmado(this)
        );
    });
    
    function procesarMovimientosConfirmado(botonProcesar) {
        const ubicacionDestino = document.getElementById('ubicacionDestino').value;
        const usuarioOrigen = document.getElementById('usuario_origen_id').value;
        const usuarioDestino = document.getElementById('usuario_destino_id').value;
        
        // Validar campos requeridos
        if (!usuarioOrigen) {
            mostrarError('Debe seleccionar un empleado de origen');
            return;
        }
        
        if (!usuarioDestino) {
            mostrarError('Debe seleccionar un empleado de destino');
            return;
        }
        
        botonProcesar.disabled = true;
        botonProcesar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
        
        // Log temporal para debug
        console.log('Datos que se envían al servidor:');
        console.log('Ubicación destino:', ubicacionDestino);
        console.log('Usuario origen:', usuarioOrigen);
        console.log('Usuario destino:', usuarioDestino);
        console.log('Elementos:', elementosDestino);
        
        const formData = new FormData();
        formData.append('ubicacion_destino_id', ubicacionDestino);
        formData.append('usuario_origen_id', usuarioOrigen);
        formData.append('usuario_destino_id', usuarioDestino);
        formData.append('elementos', JSON.stringify(elementosDestino));
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('/movimientos-masivos', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarExito(`${data.movimientos_creados} movimiento(s) procesado(s) correctamente`);
                
                // Limpiar formulario
                elementosDestino = [];
                historialMovimientos = [];
                document.getElementById('ubicacionDestino').value = '';
                actualizarVistaDestino();
                window.cargarElementos();
                actualizarBotones();
            } else {
                mostrarError(data.message || 'Error al procesar los movimientos');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error al procesar los movimientos. Por favor intente nuevamente.');
        })
        .finally(() => {
            botonProcesar.disabled = false;
            botonProcesar.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Procesar Movimientos';
        });
    }
});
</script>
@endpush

<!-- Modal para Revertir Movimientos -->
<div class="modal fade" id="revertirMovimientosModal" tabindex="-1" aria-labelledby="revertirMovimientosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="revertirMovimientosModalLabel">
                    <i class="fas fa-undo me-2"></i>Revertir Movimientos Recientes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Información:</strong> Aquí solo aparecen los movimientos que se realizaron de forma masiva desde esta vista. Puedes revertir uno por uno, seleccionar varios, o revertir todos los movimientos masivos.
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Movimientos Masivos Disponibles para Revertir</h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-warning btn-sm" id="revertirSeleccionados" onclick="revertirMovimientosSeleccionados()" style="display: none;">
                            <i class="fas fa-undo me-1"></i>Revertir Seleccionados (<span id="contadorSeleccionadosRevertir">0</span>)
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" id="revertirTodos" onclick="revertirTodosLosMovimientos()" style="display: none;">
                            <i class="fas fa-undo-alt me-1"></i>Revertir Todos
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="cargarMovimientosRevertibles()">
                            <i class="fas fa-sync-alt me-1"></i>Actualizar
                        </button>
                    </div>
                </div>
                
                <div id="movimientosRevertibles">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando movimientos...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Variables globales para movimientos revertibles
let movimientosRevertibles = [];
let movimientosSeleccionados = [];

// Funciones globales para modales
function mostrarError(mensaje) {
    document.getElementById('errorMensaje').textContent = mensaje;
    const modal = new bootstrap.Modal(document.getElementById('errorModal'));
    modal.show();
}

function mostrarExito(mensaje) {
    document.getElementById('exitoMensaje').textContent = mensaje;
    const modal = new bootstrap.Modal(document.getElementById('exitoModal'));
    modal.show();
}

function mostrarConfirmacion(mensaje, callback) {
    document.getElementById('confirmacionMensaje').textContent = mensaje;
    const modal = new bootstrap.Modal(document.getElementById('confirmacionModal'));
    
    // Limpiar event listeners anteriores
    const btnConfirmar = document.getElementById('confirmarAccion');
    const newBtn = btnConfirmar.cloneNode(true);
    btnConfirmar.parentNode.replaceChild(newBtn, btnConfirmar);
    
    // Agregar nuevo event listener
    newBtn.addEventListener('click', function() {
        modal.hide();
        if (callback) callback();
    });
    
    modal.show();
}

// Función para cargar movimientos revertibles
function cargarMovimientosRevertibles() {
    const container = document.getElementById('movimientosRevertibles');
    container.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando movimientos...</p>
        </div>
    `;
    
         fetch('/movimientos/revertibles', {
         method: 'GET',
         headers: {
             'Content-Type': 'application/json',
             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
             'Accept': 'application/json',
             'X-Requested-With': 'XMLHttpRequest'
         },
         credentials: 'same-origin'
     })
         .then(response => {
             if (!response.ok) {
                 throw new Error(`HTTP error! status: ${response.status}`);
             }
             return response.json();
         })
        .then(data => {
            // Guardar los movimientos globalmente
            movimientosRevertibles = data;
            
            if (data.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <h6 class="text-muted">No hay movimientos masivos para revertir</h6>
                        <p class="text-muted mb-0">No se han realizado movimientos masivos recientes o todos ya han sido revertidos</p>
                    </div>
                `;
                document.getElementById('revertirTodos').style.display = 'none';
                document.getElementById('revertirSeleccionados').style.display = 'none';
                return;
            }
            
            // Mostrar botones de revertir
            document.getElementById('revertirTodos').style.display = 'inline-block';
            document.getElementById('revertirSeleccionados').style.display = 'inline-block';
            
            let html = '<div class="table-responsive"><table class="table table-hover">';
            html += `
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllMovimientos" onchange="toggleSelectAllMovimientos()">
                        </th>
                        <th>Elemento</th>
                        <th>Origen → Destino</th>
                        <th>Cantidad</th>
                        <th>Fecha / Realizado por</th>
                        <th>Empleados</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
            `;
            
            data.forEach(movimiento => {
                html += `
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input movimiento-checkbox" 
                                   value="${movimiento.id}" onchange="actualizarContadorSeleccionados()">
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">${movimiento.inventario_nombre}</span>
                                <small class="text-muted">${movimiento.inventario_codigo}</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light text-dark me-2">${movimiento.ubicacion_origen}</span>
                                <i class="fas fa-arrow-right text-muted me-2"></i>
                                <span class="badge bg-primary">${movimiento.ubicacion_destino}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">${movimiento.cantidad}</span>
                            ${movimiento.nuevo_estado ? `<br><small class="text-muted">Estado: ${movimiento.nuevo_estado}</small>` : ''}
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span>${movimiento.fecha_movimiento}</span>
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    ${movimiento.realizado_por}
                                </small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <small><strong>Origen:</strong> ${movimiento.usuario_origen}</small>
                                <small><strong>Destino:</strong> ${movimiento.usuario_destino}</small>
                            </div>
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                    onclick="revertirMovimiento(${movimiento.id})"
                                    title="Revertir movimiento">
                                <i class="fas fa-undo me-1"></i>Revertir
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table></div>';
            container.innerHTML = html;
        })
                 .catch(error => {
             console.error('Error completo:', error);
             container.innerHTML = `
                 <div class="alert alert-danger">
                     <i class="fas fa-exclamation-triangle me-2"></i>
                     Error al cargar los movimientos: ${error.message}<br>
                     <small>Revisa la consola para más detalles.</small>
                 </div>
             `;
         });
}

// Función para revertir un movimiento específico
function revertirMovimiento(movimientoId) {
    mostrarConfirmacion(
        '¿Está seguro de que desea revertir este movimiento? Esta acción devolverá los elementos a su ubicación original.',
        () => {
            fetch(`/movimientos/${movimientoId}/revertir`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarExito('Movimiento revertido correctamente');
                    cargarMovimientosRevertibles();
                    if (typeof cargarElementos === 'function') {
                        cargarElementos(); // Actualizar elementos disponibles
                    }
                } else {
                    mostrarError(data.message || 'Error al revertir el movimiento');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al revertir el movimiento. Por favor intente nuevamente.');
            });
        }
    );
}

// Función para revertir todos los movimientos
function revertirTodosLosMovimientos() {
    if (movimientosRevertibles.length === 0) {
        mostrarError('No hay movimientos para revertir');
        return;
    }
    
    mostrarConfirmacion(
        `¿Está seguro de que desea revertir TODOS los ${movimientosRevertibles.length} movimientos? Esta acción devolverá todos los elementos a sus ubicaciones originales.`,
        () => {
            const btn = document.getElementById('revertirTodos');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Revirtiendo todos...';
            
            // Revertir todos los movimientos uno por uno
            let revertidos = 0;
            let errores = 0;
            let totalMovimientos = movimientosRevertibles.length;
            
            const revertirSiguiente = (index) => {
                if (index >= totalMovimientos) {
                    // Terminado
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    
                    if (errores === 0) {
                        mostrarExito(`Todos los ${revertidos} movimientos fueron revertidos correctamente`);
                    } else if (revertidos > 0) {
                        mostrarError(`Se revirtieron ${revertidos} de ${totalMovimientos} movimientos. ${errores} tuvieron errores.`);
                    } else {
                        mostrarError(`No se pudo revertir ningún movimiento. Todos tuvieron errores.`);
                    }
                    
                    cargarMovimientosRevertibles();
                    if (typeof cargarElementos === 'function') {
                        cargarElementos(); // Actualizar elementos disponibles
                    }
                    return;
                }
                
                const movimiento = movimientosRevertibles[index];
                
                fetch(`/movimientos/${movimiento.id}/revertir`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        revertidos++;
                    } else {
                        errores++;
                        console.error(`Error al revertir movimiento ${movimiento.id}:`, data.message);
                    }
                })
                .catch(error => {
                    errores++;
                    console.error(`Error al revertir movimiento ${movimiento.id}:`, error);
                })
                .finally(() => {
                    // Actualizar progreso en el botón
                    const progreso = Math.round(((index + 1) / totalMovimientos) * 100);
                    btn.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i>Revirtiendo... ${progreso}%`;
                    
                    // Continuar con el siguiente después de un pequeño delay
                    setTimeout(() => revertirSiguiente(index + 1), 100);
                });
            };
            
            // Comenzar la reversión
            revertirSiguiente(0);
        }
    );
}

// Función para seleccionar/deseleccionar todos los movimientos
function toggleSelectAllMovimientos() {
    const selectAll = document.getElementById('selectAllMovimientos');
    const checkboxes = document.querySelectorAll('.movimiento-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    actualizarContadorSeleccionados();
}

// Función para actualizar el contador de movimientos seleccionados
function actualizarContadorSeleccionados() {
    const checkboxes = document.querySelectorAll('.movimiento-checkbox:checked');
    const contador = checkboxes.length;
    
    // Actualizar contador en el botón
    document.getElementById('contadorSeleccionadosRevertir').textContent = contador;
    
    // Mostrar/ocultar botón de revertir seleccionados
    const btnRevertirSeleccionados = document.getElementById('revertirSeleccionados');
    if (contador > 0) {
        btnRevertirSeleccionados.style.display = 'inline-block';
    } else {
        btnRevertirSeleccionados.style.display = 'none';
    }
    
    // Actualizar estado del checkbox "Seleccionar todos"
    const selectAll = document.getElementById('selectAllMovimientos');
    const totalCheckboxes = document.querySelectorAll('.movimiento-checkbox').length;
    
    if (contador === 0) {
        selectAll.indeterminate = false;
        selectAll.checked = false;
    } else if (contador === totalCheckboxes) {
        selectAll.indeterminate = false;
        selectAll.checked = true;
    } else {
        selectAll.indeterminate = true;
        selectAll.checked = false;
    }
}

// Función para revertir movimientos seleccionados
function revertirMovimientosSeleccionados() {
    const checkboxes = document.querySelectorAll('.movimiento-checkbox:checked');
    const movimientosIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (movimientosIds.length === 0) {
        mostrarError('No hay movimientos seleccionados para revertir');
        return;
    }
    
    mostrarConfirmacion(
        `¿Está seguro de que desea revertir los ${movimientosIds.length} movimientos seleccionados? Esta acción devolverá todos los elementos a sus ubicaciones originales.`,
        () => {
            const btn = document.getElementById('revertirSeleccionados');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Revirtiendo seleccionados...';
            
            // Revertir movimientos seleccionados uno por uno
            let revertidos = 0;
            let errores = 0;
            let totalMovimientos = movimientosIds.length;
            
            const revertirSiguiente = (index) => {
                if (index >= totalMovimientos) {
                    // Terminado
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    
                    if (errores === 0) {
                        mostrarExito(`Todos los ${revertidos} movimientos seleccionados fueron revertidos correctamente`);
                    } else if (revertidos > 0) {
                        mostrarError(`Se revirtieron ${revertidos} de ${totalMovimientos} movimientos. ${errores} tuvieron errores.`);
                    } else {
                        mostrarError(`No se pudo revertir ningún movimiento. Todos tuvieron errores.`);
                    }
                    
                    cargarMovimientosRevertibles();
                    if (typeof cargarElementos === 'function') {
                        cargarElementos(); // Actualizar elementos disponibles
                    }
                    return;
                }
                
                const movimientoId = movimientosIds[index];
                
                fetch(`/movimientos/${movimientoId}/revertir`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        revertidos++;
                    } else {
                        errores++;
                        console.error(`Error al revertir movimiento ${movimientoId}:`, data.message);
                    }
                })
                .catch(error => {
                    errores++;
                    console.error(`Error al revertir movimiento ${movimientoId}:`, error);
                })
                .finally(() => {
                    // Actualizar progreso en el botón
                    const progreso = Math.round(((index + 1) / totalMovimientos) * 100);
                    btn.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i>Revirtiendo... ${progreso}%`;
                    
                    // Continuar con el siguiente después de un pequeño delay
                    setTimeout(() => revertirSiguiente(index + 1), 100);
                });
            };
            
            // Comenzar la reversión
            revertirSiguiente(0);
        }
    );
}

// Cargar movimientos al abrir el modal
document.getElementById('revertirMovimientosModal').addEventListener('shown.bs.modal', function () {
    cargarMovimientosRevertibles();
});
</script>
@endpush

@endsection