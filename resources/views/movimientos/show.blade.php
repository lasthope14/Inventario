@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="overflow: visible !important;">
    <div class="card shadow-sm mb-4" style="z-index: 1; position: relative;">
        <div class="card-header bg-primary text-white" style="z-index: 1; position: relative;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h2 class="mb-3 mb-md-0 fs-4">Hoja de Ruta del Movimiento</h2>
                <div class="d-flex flex-wrap justify-content-center button-group">
                    @if(auth()->user()->role->name === 'administrador')
                        <a href="{{ route('movimientos.edit', $movimiento) }}" class="btn btn-light me-2">
                            <i class="fas fa-edit me-1"></i>
                            <span>Editar</span>
                        </a>
                        <form action="{{ route('movimientos.destroy', $movimiento) }}" method="POST" class="d-inline me-2" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-light" id="deleteButton" onclick="return confirmDelete(event)">
                                <i class="fas fa-trash me-1"></i>
                                <span>Eliminar</span>
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('inventarios.show', $movimiento->inventario) }}" class="btn btn-light" id="volverBtn">
                        <i class="fas fa-arrow-left me-1"></i>
                        <span>Volver</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="border-bottom pb-2 mb-3">Información del Elemento</h5>
                    <dl class="row">
                        <dt class="col-sm-4">Nombre:</dt>
                        <dd class="col-sm-8">{{ $movimiento->inventario->nombre }}</dd>
                        
                        <dt class="col-sm-4">Código:</dt>
                        <dd class="col-sm-8">{{ $movimiento->inventario->codigo_unico }}</dd>
                        
                        <dt class="col-sm-4">Categoría:</dt>
                        <dd class="col-sm-8">{{ $movimiento->inventario->categoria->nombre }}</dd>
                        
                        <dt class="col-sm-4">Cantidad Total:</dt>
                        <dd class="col-sm-8">{{ $movimiento->inventario->cantidadTotal }}</dd>
                        
                        <dt class="col-sm-4">Cantidad Movida:</dt>
                        <dd class="col-sm-8">{{ $movimiento->cantidad }}</dd>
                    </dl>
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-image me-2"></i> Imagen Principal</h6>
                            <div class="image-container" style="height: 150px; overflow: hidden;">
                                @if($movimiento->inventario->imagen_principal)
                                    <img src="{{ asset('storage/' . $movimiento->inventario->imagen_principal) }}" 
                                         class="img-fluid rounded cursor-pointer" 
                                         alt="Imagen principal" 
                                         onclick="openImageModal(this.src)" 
                                         style="object-fit: cover; width: 100%; height: 100%;">
                                @else
                                    <div class="d-flex justify-content-center align-items-center h-100 bg-light rounded">
                                        <span class="text-muted">Sin imagen principal</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-images me-2"></i> Imagen Secundaria</h6>
                            <div class="image-container" style="height: 150px; overflow: hidden;">
                                @if($movimiento->inventario->imagen_secundaria)
                                    <img src="{{ asset('storage/' . $movimiento->inventario->imagen_secundaria) }}" 
                                         class="img-fluid rounded cursor-pointer" 
                                         alt="Imagen secundaria" 
                                         onclick="openImageModal(this.src)" 
                                         style="object-fit: cover; width: 100%; height: 100%;">
                                @else
                                    <div class="d-flex justify-content-center align-items-center h-100 bg-light rounded">
                                        <span class="text-muted">Sin imagen secundaria</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <h5 class="border-bottom pb-2 mb-3">Detalles del Movimiento</h5>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="timeline-container">
                                <ul class="list-group list-group-flush timeline-list">
                                    <li class="list-group-item timeline-item" data-step="origen">
                                        <div class="timeline-marker origen-marker"></div>
                                        <div class="timeline-content-wrapper">
                                    <h6 class="mb-1">Origen</h6>
                                            <p class="mb-0"><strong>Ubicación:</strong> 
                                                @php
                                                    $ubicacionOrigen = \App\Models\Ubicacion::find($movimiento->ubicacion_origen);
                                                @endphp
                                                {{ $ubicacionOrigen ? $ubicacionOrigen->nombre : ($movimiento->ubicacion_origen ?? 'N/A') }}
                                            </p>
                                    <p class="mb-0"><strong>Empleado:</strong> {{ $movimiento->usuarioOrigen->nombre }} {{ $movimiento->usuarioOrigen->cargo ? '- ' . $movimiento->usuarioOrigen->cargo : '' }}</p>
                                        </div>
                                </li>
                                    <li class="list-group-item timeline-item" data-step="movimiento">
                                        <div class="timeline-marker movimiento-marker"></div>
                                        <div class="timeline-content-wrapper">
                                    <h6 class="mb-1">Movimiento</h6>
                                    <p class="mb-0"><strong>Fecha:</strong> {{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}</p>
                                    <p class="mb-0"><strong>Motivo:</strong> {{ $movimiento->motivo ?? 'No especificado' }}</p>
                                    <p class="mb-0"><strong>Realizado por:</strong> {{ $movimiento->realizadoPor->name ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Cantidad movida:</strong> {{ $movimiento->cantidad }}</p>
                                        </div>
                                </li>
                                    <li class="list-group-item timeline-item" data-step="destino">
                                        <div class="timeline-marker destino-marker"></div>
                                        <div class="timeline-content-wrapper">
                                    <h6 class="mb-1">Destino</h6>
                                            <p class="mb-0"><strong>Ubicación:</strong> 
                                                @php
                                                    $ubicacionDestino = \App\Models\Ubicacion::find($movimiento->ubicacion_destino);
                                                @endphp
                                                {{ $ubicacionDestino ? $ubicacionDestino->nombre : ($movimiento->ubicacion_destino ?? 'N/A') }}
                                            </p>
                                    <p class="mb-0"><strong>Empleado:</strong> {{ $movimiento->usuarioDestino->nombre }} {{ $movimiento->usuarioDestino->cargo ? '- ' . $movimiento->usuarioDestino->cargo : '' }}</p>
                                        </div>
                                </li>
                            </ul>
                            </div>
                        </div>
                        <div class="col-md-7 d-flex align-items-center justify-content-center">
                            <lottie-player 
                                src="https://lottie.host/ab409af5-cfce-40c1-9f51-f54ce30b967e/ove6TdscIe.json"
                                background="transparent"
                                speed="1"
                                style="width: 300px; height: 300px;"
                                loop
                                autoplay>
                            </lottie-player>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-4">Resumen de Movimientos</h5>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exchange-alt fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Movimientos</h6>
                            <p class="mb-0 fs-4">{{ $estadisticas['total_movimientos'] }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-alt fa-2x text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Último Movimiento</h6>
                            <p class="mb-0">{{ $estadisticas['ultimo_movimiento'] }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-map-marker-alt fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Ubicación Actual</h6>
                            <p class="mb-0">{{ $estadisticas['ubicacion_actual'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Movimientos por Mes</h5>
                </div>
                <div class="card-body">
                    <canvas id="movimientosChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Ubicaciones más Frecuentes</h5>
                </div>
                <div class="card-body">
                    <canvas id="ubicacionesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal para ver imágenes en grande -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0">
                <img src="" class="img-fluid w-100" id="modalImage" alt="Imagen ampliada">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<style>
    .cursor-pointer {
        cursor: pointer;
    }
    .img-thumbnail {
        transition: transform 0.2s;
    }
    .img-thumbnail:hover {
        transform: scale(1.05);
    }
    .image-container img {
        transition: transform 0.3s ease;
    }
    .image-container img:hover {
        transform: scale(1.05);
    }
    
    .button-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-light {
        background-color: #fff;
        border-color: #dee2e6;
        color: #495057;
        font-weight: 600;
    }

    .btn-light:hover {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #495057;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Estilos para el modal de imagen */
    .modal-dialog-centered {
        display: flex;
        align-items: center;
        min-height: calc(100% - 1rem);
    }

    .modal-content {
        width: 100%;
    }

    /* Estilos para Lottie Player */
    lottie-player {
        max-width: 100%;
        height: auto;
        margin: 0 auto;
        z-index: 1 !important;
        position: relative !important;
    }

    /* Estilos para el spinner y botón deshabilitado */
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.2em;
        margin-right: 0.5rem;
    }

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.65;
    }

    .submitting {
        pointer-events: none;
    }

    #deleteButton:disabled {
        background-color: #e9ecef;
        border-color: #e9ecef;
        color: #6c757d;
    }

    @media (max-width: 768px) {
        lottie-player {
            width: 200px !important;
            height: 200px !important;
        }
    }

    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline::before {
        content: '';
        background: #000;
        width: 2px;
        height: 100%;
        position: absolute;
        left: 18px;
        top: 0;
    }

    .timeline-item {
        margin-bottom: 30px;
        position: relative;
    }

    .timeline-icon {
        background: #fff;
        border: 2px solid #000;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        position: absolute;
        left: 0;
        top: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .timeline-content {
        margin-left: 60px;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
    }

    /* Línea de Tiempo Vertical Roja */
    .timeline-container {
        position: relative;
    }

    .timeline-list {
        position: relative;
        padding-left: 0;
    }

    .timeline-list::before {
        content: '';
        position: absolute;
        right: 20px;
        top: 30px;
        bottom: 30px;
        width: 3px;
        background: #dc3545;
        border-radius: 2px;
        z-index: 1;
    }

    .timeline-item {
        position: relative;
        border: none !important;
        background: transparent !important;
        padding: 1.5rem 3.5rem 1.5rem 0;
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 3px solid #dc3545;
        background: #fff;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .timeline-marker::after {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #dc3545;
    }

    .origen-marker::after {
        background: #28a745;
    }

    .movimiento-marker::after {
        background: #ffc107;
    }

    .destino-marker::after {
        background: #dc3545;
    }

    .timeline-content-wrapper {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .timeline-content-wrapper:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: translateX(5px);
    }

    .timeline-content-wrapper h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 0.75rem;
        font-size: 1rem;
    }

    .timeline-content-wrapper p {
        color: #6c757d;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .timeline-content-wrapper p:last-child {
        margin-bottom: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .timeline-list::before {
            right: 15px;
        }

        .timeline-marker {
            right: 3px;
            width: 20px;
            height: 20px;
        }

        .timeline-marker::after {
            width: 6px;
            height: 6px;
        }

        .timeline-item {
            padding-right: 2.5rem;
            padding-left: 0;
        }
    }

    /* Iconos para los marcadores */
    .timeline-marker {
        font-size: 10px !important;
        color: #fff !important;
        font-weight: bold !important;
    }

    .origen-marker {
        background: #28a745 !important;
        border-color: #28a745 !important;
    }

    .origen-marker::after {
        content: '\f3c5' !important;
        font-family: 'Font Awesome 5 Free' !important;
        font-weight: 900 !important;
        width: auto !important;
        height: auto !important;
        border-radius: 0 !important;
        background: transparent !important;
    }

    .movimiento-marker {
        background: #ffc107 !important;
        border-color: #ffc107 !important;
        color: #000 !important;
    }

    .movimiento-marker::after {
        content: '\f0d1' !important;
        font-family: 'Font Awesome 5 Free' !important;
        font-weight: 900 !important;
        width: auto !important;
        height: auto !important;
        border-radius: 0 !important;
        background: transparent !important;
    }

    .destino-marker {
        background: #dc3545 !important;
        border-color: #dc3545 !important;
    }

    .destino-marker::after {
        content: '\f024' !important;
        font-family: 'Font Awesome 5 Free' !important;
        font-weight: 900 !important;
        width: auto !important;
        height: auto !important;
        border-radius: 0 !important;
        background: transparent !important;
    }
    
    /* Navegación limpia sin conflictos */
    
    /* Estilos para modo oscuro */
    [data-bs-theme="dark"] {
        .btn-light {
            background-color: #334155;
            border-color: #475569;
            color: #f8fafc;
        }

        .btn-light:hover {
            background-color: #475569;
            border-color: #64748b;
            color: #f8fafc;
        }

        .timeline-content-wrapper {
            background: #1e293b;
            border-color: #475569;
            color: #f8fafc;
        }

        .timeline-content-wrapper h6 {
            color: #f8fafc;
        }

        .timeline-content-wrapper p {
            color: #e2e8f0;
        }

        .timeline-marker {
            background: #1e293b;
            border-color: #dc3545;
        }

        .card {
            background-color: #1e293b;
            border-color: #475569;
        }

        .card-header {
            background-color: #334155 !important;
            border-color: #475569;
        }

        .list-group-item {
            background-color: #1e293b;
            color: #f8fafc;
            border-color: #475569;
        }

        .modal-content {
            background-color: #1e293b;
            color: #f8fafc;
        }

        .modal-header {
            border-bottom: 1px solid #475569;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        .timeline::before {
            background: #f8fafc;
        }

        .timeline-icon {
            background: #1e293b;
            border-color: #f8fafc;
        }

        .timeline-content {
            background: #334155;
        }

        #deleteButton:disabled {
            background-color: #adb5bd;
            border-color: #adb5bd;
            color: #000;
        }
    }

    /* Fallback para navegadores que no soporten data-bs-theme */
    @media (prefers-color-scheme: dark) {
        body {
            background-color: #0f172a;
            color: #f8fafc;
        }

        .btn-light {
            background-color: #334155;
            border-color: #475569;
            color: #f8fafc;
        }

        .btn-light:hover {
            background-color: #475569;
            border-color: #64748b;
            color: #f8fafc;
        }

        .card {
            background-color: #1e293b;
        }
        .card-header {
            background-color: #334155 !important;
        }
        .list-group-item {
            background-color: #1e293b;
            color: #f8fafc;
        }
        .modal-content {
            background-color: #1e293b;
            color: #f8fafc;
        }
        .modal-header {
            border-bottom: 1px solid #475569;
        }
        .modal-header .btn-close {
            filter: invert(1);
        }
        .timeline::before {
            background: #f8fafc;
        }
        .timeline-icon {
            background: #1e293b;
            border-color: #f8fafc;
        }
        .timeline-content {
            background: #334155;
        }

        #deleteButton:disabled {
            background-color: #475569;
            border-color: #475569;
            color: #cbd5e1;
        }
    }

    /* Línea de Tiempo Vertical Roja */
    .timeline-container {
        position: relative;
    }

    .timeline-list {
        position: relative;
        padding-left: 0;
    }

    .timeline-list::before {
        content: '';
        position: absolute;
        right: 20px;
        top: 30px;
        bottom: 30px;
        width: 3px;
        background: #dc3545;
        border-radius: 2px;
        z-index: 1;
    }

    .timeline-item {
        position: relative;
        border: none !important;
        background: transparent !important;
        padding: 1.5rem 3.5rem 1.5rem 0;
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 3px solid #dc3545;
        background: #fff;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .timeline-marker::after {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #dc3545;
    }

    .origen-marker::after {
        background: #28a745;
    }

    .movimiento-marker::after {
        background: #ffc107;
    }

    .destino-marker::after {
        background: #dc3545;
    }

    .timeline-content-wrapper {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .timeline-content-wrapper:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: translateX(5px);
    }

    .timeline-content-wrapper h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 0.75rem;
        font-size: 1rem;
    }

    .timeline-content-wrapper p {
        color: #6c757d;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .timeline-content-wrapper p:last-child {
        margin-bottom: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .timeline-list::before {
            right: 15px;
        }

        .timeline-marker {
            right: 3px;
            width: 20px;
            height: 20px;
        }

        .timeline-marker::after {
            width: 6px;
            height: 6px;
        }

        .timeline-item {
            padding-right: 2.5rem;
            padding-left: 0;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/@lottiefiles/lottie-player@2.0.8/dist/lottie-player.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para confirmar y manejar la eliminación
    window.confirmDelete = function(event) {
        event.preventDefault();
        
        const form = document.getElementById('deleteForm');
        const deleteButton = document.getElementById('deleteButton');
        
        // Si ya se está procesando la eliminación, evitar múltiples envíos
        if (form.getAttribute('data-submitting')) {
            return false;
        }

        // Confirmar la eliminación
        if (confirm('¿Estás seguro de que quieres eliminar este movimiento?')) {
            // Deshabilitar el botón y marcar el formulario como en proceso
            deleteButton.disabled = true;
            form.setAttribute('data-submitting', 'true');
            form.classList.add('submitting');
            
            // Cambiar el contenido del botón para mostrar el spinner
            deleteButton.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="d-none d-md-inline ms-2">Eliminando...</span>
            `;
            
            // Enviar el formulario
            form.submit();
        }
        
        return false;
    };

    // Función para abrir el modal de imagen
    window.openImageModal = function(src) {
        document.getElementById('modalImage').src = src;
        var modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();
    }

    // Cerrar el modal al hacer clic fuera de la imagen
    document.getElementById('imageModal').addEventListener('click', function(event) {
        if (event.target === this) {
            bootstrap.Modal.getInstance(this).hide();
        }
    });

    // Gráfico de movimientos por mes
    var ctxMovimientos = document.getElementById('movimientosChart').getContext('2d');
    var movimientosChart = new Chart(ctxMovimientos, {
        type: 'bar',
        data: {
            labels: {!! json_encode($estadisticas['meses']) !!},
            datasets: [{
                label: 'Movimientos',
                data: {!! json_encode($estadisticas['movimientos_por_mes']) !!},
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Gráfico de ubicaciones más frecuentes
    var ctxUbicaciones = document.getElementById('ubicacionesChart').getContext('2d');
    var ubicacionesChart = new Chart(ctxUbicaciones, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($estadisticas['ubicaciones']) !!},
            datasets: [{
                data: {!! json_encode($estadisticas['frecuencia_ubicaciones']) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });
    
    // El sistema de navegación ahora es manejado globalmente por NavigationStateManager
});
</script>
@endpush