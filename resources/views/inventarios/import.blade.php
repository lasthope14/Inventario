@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-import me-2"></i>
                        Importar Inventario
                    </h3>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Referencias -->
                        <div class="col-md-7 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Referencias para la Importación
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="accordion" id="accordionReferencias">
                                        <!-- Categorías -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategorias">
                                                    Categorías Disponibles
                                                </button>
                                            </h2>
                                            <div id="collapseCategorias" class="accordion-collapse collapse show" data-bs-parent="#accordionReferencias">
                                                <div class="accordion-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Nombre</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($categorias as $categoria)
                                                                <tr>
                                                                    <td>{{ $categoria->id }}</td>
                                                                    <td>{{ $categoria->nombre }}</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Proveedores -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProveedores">
                                                    Proveedores Disponibles
                                                </button>
                                            </h2>
                                            <div id="collapseProveedores" class="accordion-collapse collapse" data-bs-parent="#accordionReferencias">
                                                <div class="accordion-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Nombre</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($proveedores as $proveedor)
                                                                <tr>
                                                                    <td>{{ $proveedor->id }}</td>
                                                                    <td>{{ $proveedor->nombre }}</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Ubicaciones -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUbicaciones">
                                                    Ubicaciones Disponibles
                                                </button>
                                            </h2>
                                            <div id="collapseUbicaciones" class="accordion-collapse collapse" data-bs-parent="#accordionReferencias">
                                                <div class="accordion-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Nombre</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($ubicaciones as $ubicacion)
                                                                <tr>
                                                                    <td>{{ $ubicacion->id }}</td>
                                                                    <td>{{ $ubicacion->nombre }}</td>
                                                                </tr>
                                                                @endforeach
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

                        <!-- Formulario de Importación -->
                        <div class="col-md-5 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-upload me-2"></i>
                                        Cargar Archivo
                                    </h5>
                                </div>
                                <div class="card-body">
                                    
                                    <!-- Área de carga principal -->
                                    <div class="upload-container mb-4">
                                        <form action="{{ route('inventarios.import') }}" method="POST" enctype="multipart/form-data" class="dropzone" id="importForm">
                                            @csrf
                                            <div class="dz-default dz-message">
                                                <div class="upload-content text-center">
                                                    <i class="fas fa-file-archive fa-3x mb-3 text-primary"></i>
                                                    <h5>Arrastra el archivo ZIP aquí</h5>
                                                    <p class="text-muted mb-0">o haz clic para seleccionarlo</p>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Contenedor del archivo seleccionado -->
                                    <div id="filePreview" class="mb-3" style="display: none;">
                                        <div class="selected-file p-3 bg-light rounded-3 border">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-archive text-primary me-3"></i>
                                                <div class="flex-grow-1">
                                                    <h6 class="file-name mb-1"></h6>
                                                    <small class="text-muted file-size"></small>
                                                </div>
                                                <button type="button" class="btn btn-link text-danger p-0" id="removeFile">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Barra de progreso -->
                                    <div id="uploadProgress" class="mb-3" style="display: none;">
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                                                 role="progressbar" 
                                                 style="width: 0%" 
                                                 aria-valuenow="0" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small class="text-muted d-block text-center mt-2 progress-text"></small>
                                    </div>

                                    <!-- Estado de la carga -->
                                    <div id="uploadStatus" class="mb-3" style="display: none;">
                                    </div>

                                    <!-- Botones de acción -->
                                    <div class="d-grid gap-2">
                                        <button type="button" id="analyzeBtn" class="btn btn-warning" style="display: none;">
                                            <i class="fas fa-search me-2"></i>
                                            Analizar Archivo
                                        </button>
                                        <button type="button" id="submitImport" class="btn btn-primary" style="display: none;">
                                            <i class="fas fa-upload me-2"></i>
                                            Importar Datos
                                        </button>
                                        <a href="{{ route('inventarios.template.download') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-download me-2"></i>
                                            Descargar Plantilla
                                        </a>
                                    </div>

                                    <!-- Resultados del análisis -->
                                    <div id="analysisResults" class="mt-4" style="display: none;">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-chart-bar me-2"></i>
                                                    Resultados del Análisis
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div id="analysisContent"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Guía de importación -->
                                    <div class="import-guide mt-4">
                                        <div class="alert alert-info border-start border-4">
                                            <h6 class="alert-heading d-flex align-items-center">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Estructura del Archivo ZIP
                                            </h6>
                                            <hr>
                                            <div class="file-structure">
                                                <pre class="mb-0"><code>inventario_importacion.zip
├── datos_inventario.xlsx
├── imagenes/
│   ├── imagen1.jpg
│   └── imagen2.jpg
└── documentos/
    └── documento.pdf</code></pre>
                                            </div>
                                            <hr>
                                            <h6 class="mb-2">Instrucciones:</h6>
                                            <ol class="mb-0 ps-3">
                                                <li>Descarga la plantilla de ejemplo</li>
                                                <li>Llena los datos según el formato</li>
                                                <li>Prepara las imágenes y documentos</li>
                                                <li>Crea un archivo ZIP con la estructura mostrada</li>
                                                <li>Los nombres de archivos en el Excel deben coincidir con los archivos en el ZIP</li>
                                                <li>Sube el ZIP y haz clic en Importar</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Historial de Importaciones -->
                        @if(isset($importLogs) && $importLogs->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-history me-2"></i>
                                    Historial de Importaciones
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Usuario</th>
                                                <th>Archivo</th>
                                                <th>Registros</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($importLogs as $log)
                                            <tr>
                                                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $log->user_name ?? $log->user->name }}</td>
                                                <td>{{ $log->file_name }}</td>
                                                <td>{{ $log->records_imported }}</td>
                                                <td>
                                                    @if($log->status === 'success')
                                                        <span class="badge bg-success">Éxito</span>
                                                    @elseif($log->status === 'reverted')
                                                        <span class="badge bg-warning">Revertido</span>
                                                    @else
                                                        <span class="badge bg-danger">Error</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-info" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#detailsModal{{ $log->id }}"
                                                                title="Ver detalles">
                                                            <i class="fas fa-info-circle"></i>
                                                        </button>

                                                        @if(auth()->user()->role->name === 'administrador')
                                                            @if($log->status === 'success')
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-warning" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#revertModal{{ $log->id }}"
                                                                        title="Revertir importación">
                                                                    <i class="fas fa-undo"></i>
                                                                </button>
                                                            @endif

                                                            @if($log->status === 'reverted')
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-danger" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#deleteModal{{ $log->id }}"
                                                                        title="Eliminar registro">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modales -->
@if(isset($importLogs) && $importLogs->count() > 0)
    @foreach($importLogs as $log)
        <!-- Modal Detalles -->
        <div class="modal fade" id="detailsModal{{ $log->id }}" tabindex="-1" aria-labelledby="detailsModalLabel{{ $log->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header {{ $log->status === 'success' ? 'bg-success' : ($log->status === 'reverted' ? 'bg-warning' : 'bg-danger') }} text-white">
                        <h5 class="modal-title">
                            <i class="fas {{ $log->status === 'success' ? 'fa-check-circle' : ($log->status === 'reverted' ? 'fa-undo' : 'fa-exclamation-circle') }} me-2"></i>
                            Detalles de la Importación
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Información General</h6>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <i class="fas fa-calendar me-2"></i>
                                        <strong>Fecha:</strong> {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </li>
                                    <li class="list-group-item">
                                        <i class="fas fa-user me-2"></i>
                                        <strong>Usuario:</strong> {{ $log->user_name ?? $log->user->name }}
                                    </li>
                                    <li class="list-group-item">
                                        <i class="fas fa-file me-2"></i>
                                        <strong>Archivo:</strong> {{ $log->file_name }}
                                    </li>
                                    <li class="list-group-item">
                                        <i class="fas fa-list-ol me-2"></i>
                                        <strong>Registros:</strong> {{ $log->records_imported }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Estado de la Importación</h6>
                                <div class="alert {{ $log->status === 'success' ? 'alert-success' : ($log->status === 'reverted' ? 'alert-warning' : 'alert-danger') }}">
                                    <i class="fas {{ $log->status === 'success' ? 'fa-check-circle' : ($log->status === 'reverted' ? 'fa-undo' : 'fa-exclamation-circle') }} me-2"></i>
                                    @if($log->status === 'success')
                                        Importación Exitosa
                                    @elseif($log->status === 'reverted')
                                        Importación Revertida
                                    @else
                                        Error en la Importación
                                    @endif
                                </div>

                                @if($log->status !== 'success')
                                    <div class="error-details mt-3">
                                        <h6 class="text-danger mb-2">Detalles del Error:</h6>
                                        <div class="alert alert-light border">
                                            @if(is_array($log->details))
                                                @foreach($log->details as $detail)
                                                    @if(is_string($detail))
                                                        <p class="mb-2"><i class="fas fa-exclamation-triangle text-danger me-2"></i>{{ $detail }}</p>
                                                    @elseif(is_array($detail))
                                                        @if(isset($detail['message']))
                                                            <p class="mb-2"><i class="fas fa-exclamation-triangle text-danger me-2"></i>{{ $detail['message'] }}</p>
                                                        @endif
                                                        @if(isset($detail['row']))
                                                            <small class="text-muted">Fila afectada: {{ $detail['row'] }}</small>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @elseif(is_string($log->details))
                                                <p class="mb-0"><i class="fas fa-exclamation-triangle text-danger me-2"></i>{{ $log->details }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($log->status === 'success' && !empty($log->details))
                            <div class="success-details mt-4">
                                <h6 class="text-muted mb-3">Detalles de la Importación</h6>
                                <div class="alert alert-light border">
                                    @foreach($log->details as $detail)
                                        <div class="import-detail-item mb-2">
                                            @if(isset($detail['message']))
                                                <p class="mb-1"><i class="fas fa-check text-success me-2"></i>{{ $detail['message'] }}</p>
                                            @endif
                                            @if(isset($detail['files']) && !empty($detail['files']))
                                                <div class="ms-4">
                                                    @foreach($detail['files'] as $file)
                                                        <small class="text-muted d-block"><i class="fas fa-file me-2"></i>{{ $file }}</small>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->role->name === 'administrador')
            @if($log->status === 'success')
                <!-- Modal Revertir -->
                <div class="modal fade" id="revertModal{{ $log->id }}" tabindex="-1" aria-labelledby="revertModalLabel{{ $log->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">
                                    <i class="fas fa-undo me-2"></i>
                                    Confirmar Reversión
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>¡Advertencia!</strong> Esta acción eliminará todos los registros importados en esta operación.
                                </div>
                                <p class="mb-0">Se eliminarán {{ $log->records_imported }} registros importados el {{ $log->created_at->format('d/m/Y H:i') }}.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <form action="{{ route('importlogs.revert', $log->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-undo me-2"></i>
                                        Revertir Importación
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($log->status === 'reverted')
                <!-- Modal Eliminar -->
                <div class="modal fade" id="deleteModal{{ $log->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $log->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-trash me-2"></i>
                                    Confirmar Eliminación
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>¿Estás seguro de que deseas eliminar este registro de importación?</p>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Este registro puede ser eliminado ya que la importación fue revertida previamente.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <form action="{{ route('importlogs.destroy', $log->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-2"></i>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endforeach
@endif
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<style>
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: #0d6efd;
    }
    .table-responsive {
        max-height: 300px;
        overflow-y: auto;
    }
    .alert-info {
        background-color: #f8f9fa;
        border-left: 4px solid #0dcaf0;
    }
    .badge {
        font-size: 0.875rem;
    }
    .tooltip {
        max-width: 300px;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    .modal-header .btn-close {
        color: white;
    }
    .list-group-item i {
        width: 20px;
    }
    .alert-light {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }
    .modal-header .btn-close:focus {
        box-shadow: none;
    }
    .btn-group .btn:hover {
        transform: translateY(-1px);
        transition: transform 0.2s;
    }
    .modal-content {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .list-unstyled li {
        padding: 0.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    .list-unstyled li:last-child {
        border-bottom: none;
    }
    .list-group-item {
        display: flex;
        align-items: center;
    }
    .list-group-item i {
        flex-shrink: 0;
    }
    .modal-body .alert {
        margin-bottom: 0;
    }
    .btn-warning {
        color: #000;
    }
    .bg-warning {
        color: #000;
    }
    .modal-header.bg-warning {
        color: #000;
    }
    .table td {
        vertical-align: middle;
    }
    
    /* Estilos específicos para modales */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1055;
    }
    
    .modal-dialog {
        margin: 1.75rem auto;
        max-width: 90%;
    }
    
    .modal-backdrop {
        z-index: 1050;
    }

    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 500px;
        }
        
        .modal-dialog-lg {
            max-width: 800px;
        }
    }

    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
        }
        .btn-group {
            display: flex;
            gap: 0.25rem;
        }
        .btn-group .btn {
            margin: 0;
        }
    }

    /* Dropzone Styles */
    .dropzone {
        border: 2px dashed #0d6efd;
        border-radius: 8px;
        background: #f8f9fa;
        min-height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .dropzone:hover {
        border-color: #0b5ed7;
        background: #f1f3f5;
    }

    .dropzone .dz-message {
        margin: 0;
        padding: 1rem;
    }

    .upload-content i {
        transition: transform 0.3s ease;
    }

    .dropzone:hover .upload-content i {
        transform: translateY(-5px);
    }

    /* File Preview Styles */
    .selected-file {
        transition: all 0.3s ease;
    }

    .selected-file:hover {
        background-color: #e9ecef !important;
    }

    #removeFile {
        transition: all 0.2s ease;
    }

    #removeFile:hover {
        transform: scale(1.1);
    }

    /* Progress Bar Styles */
    .progress {
        border-radius: 20px;
        background-color: #e9ecef;
        overflow: hidden;
    }

    .progress-bar {
        transition: width 0.3s ease;
    }

    /* Import Guide Styles */
    .import-guide pre {
        background-color: rgba(0,0,0,.03);
        padding: 1rem;
        border-radius: 4px;
        font-size: 0.875rem;
    }

    .import-guide code {
        color: #212529;
    }

    .import-guide ol {
        font-size: 0.9rem;
    }

    .import-guide li {
        margin-bottom: 0.5rem;
    }

    /* Upload Status Styles */
    #uploadStatus.success {
        background-color: #d1e7dd;
        color: #0f5132;
        padding: 1rem;
        border-radius: 6px;
        border: 1px solid #badbcc;
    }

    #uploadStatus.error {
        background-color: #f8d7da;
        color: #842029;
        padding: 1rem;
        border-radius: 6px;
        border: 1px solid #f5c2c7;
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .selected-file, #uploadProgress, #uploadStatus {
        animation: fadeIn 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    Dropzone.autoDiscover = false;

    document.addEventListener('DOMContentLoaded', function() {
        // Inicialización de modales
        const modals = document.querySelectorAll('.modal');
        modals.forEach(function(modal) {
            new bootstrap.Modal(modal);
        });

        // Inicialización de Dropzone
        const dropzoneElement = document.getElementById('importForm');
        if (dropzoneElement && !dropzoneElement.dropzone) {
            const myDropzone = new Dropzone("#importForm", {
                url: "{{ route('inventarios.import') }}",
                maxFiles: 1,
                acceptedFiles: ".zip",
                autoProcessQueue: false,
                addRemoveLinks: false,
                createImageThumbnails: false,
                previewsContainer: false,
                dictDefaultMessage: "",
                dictFileTooBig: "El archivo es demasiado grande",
                dictInvalidFileType: "Solo se permiten archivos ZIP",
                dictResponseError: "Error al subir el archivo",
                dictMaxFilesExceeded: "No puedes subir más archivos",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            // Elementos del DOM
            const submitButton = document.getElementById('submitImport');
            const analyzeButton = document.getElementById('analyzeBtn');
            const filePreview = document.getElementById('filePreview');
            const uploadProgress = document.getElementById('uploadProgress');
            const progressBar = uploadProgress.querySelector('.progress-bar');
            const progressText = uploadProgress.querySelector('.progress-text');
            const uploadStatus = document.getElementById('uploadStatus');
            const removeFileBtn = document.getElementById('removeFile');
            const analysisResults = document.getElementById('analysisResults');
            const analysisContent = document.getElementById('analysisContent');

            // Evento: Archivo añadido
            myDropzone.on("addedfile", function(file) {
                filePreview.style.display = "block";
                filePreview.querySelector(".file-name").textContent = file.name;
                filePreview.querySelector(".file-size").textContent = 
                    (file.size / (1024 * 1024)).toFixed(2) + " MB";
                analyzeButton.style.display = "block";
                submitButton.style.display = "block";
                uploadStatus.style.display = "none";
                analysisResults.style.display = "none";
            });

            // Evento: Remover archivo
            removeFileBtn.addEventListener('click', function() {
                myDropzone.removeAllFiles(true);
                resetUploadState();
            });

            // Evento: Analizar archivo
            analyzeButton.addEventListener('click', function() {
                analyzeFile();
            });

            // Evento: Iniciar importación
            submitButton.addEventListener('click', function() {
                startUpload();
            });

            // Evento: Progreso de carga
            myDropzone.on("uploadprogress", function(file, progress) {
                updateProgress(progress);
            });

            // Evento: Éxito en la carga
            myDropzone.on("success", function(file, response) {
                handleSuccess();
            });

            // Evento: Error en la carga
            myDropzone.on("error", function(file, errorMessage) {
                handleError(errorMessage);
            });

            // Funciones auxiliares
            function startUpload() {
                uploadProgress.style.display = "block";
                progressText.textContent = "Preparando importación...";
                submitButton.disabled = true;
                myDropzone.processQueue();
            }

            function updateProgress(progress) {
                progressBar.style.width = progress + "%";
                progressBar.setAttribute('aria-valuenow', progress);
                progressText.textContent = `Importando... ${Math.round(progress)}%`;
            }

            function handleSuccess() {
                progressBar.classList.remove("progress-bar-animated");
                progressText.textContent = "¡Importación completada!";
                uploadStatus.className = "mb-3 success";
                uploadStatus.innerHTML = '<i class="fas fa-check-circle me-2"></i>Archivos importados correctamente';
                uploadStatus.style.display = "block";
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }

            function handleError(errorMessage) {
                progressBar.classList.remove("progress-bar-animated");
                progressBar.classList.add("bg-danger");
                progressText.textContent = "Error en la importación";
                uploadStatus.className = "mb-3 error";
                uploadStatus.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i>${
                    typeof errorMessage === 'string' ? errorMessage : 'Error al procesar el archivo'
                }`;
                uploadStatus.style.display = "block";
                submitButton.disabled = false;
            }

            function analyzeFile() {
                const files = myDropzone.getAcceptedFiles();
                if (files.length === 0) {
                    alert('Por favor selecciona un archivo primero');
                    return;
                }

                const formData = new FormData();
                formData.append('file', files[0]);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                analyzeButton.disabled = true;
                analyzeButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Analizando...';

                fetch('{{ route("inventarios.analyze") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    analyzeButton.disabled = false;
                    analyzeButton.innerHTML = '<i class="fas fa-search me-2"></i>Analizar Archivo';

                    if (data.success) {
                        displayAnalysisResults(data.analysis);
                    } else {
                        alert('Error al analizar el archivo: ' + data.message);
                    }
                })
                .catch(error => {
                    analyzeButton.disabled = false;
                    analyzeButton.innerHTML = '<i class="fas fa-search me-2"></i>Analizar Archivo';
                    alert('Error al analizar el archivo: ' + error.message);
                });
            }

            function displayAnalysisResults(analysis) {
                let html = '';

                // Resumen general
                html += `<div class="row mb-4">`;
                html += `<div class="col-md-6">`;
                html += `<h6 class="text-primary mb-3"><i class="fas fa-chart-pie me-2"></i>Resumen General</h6>`;
                html += `<ul class="list-group">`;
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">`;
                html += `Total de filas: <span class="badge bg-primary rounded-pill">${analysis.total_rows}</span>`;
                html += `</li>`;
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">`;
                html += `Duplicados: <span class="badge bg-warning rounded-pill">${analysis.summary.total_duplicates}</span>`;
                html += `</li>`;
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">`;
                html += `Conflictos: <span class="badge bg-danger rounded-pill">${analysis.summary.total_conflicts}</span>`;
                html += `</li>`;
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">`;
                html += `Referencias faltantes: <span class="badge bg-secondary rounded-pill">${analysis.summary.total_missing_references}</span>`;
                html += `</li>`;
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">`;
                html += `Advertencias: <span class="badge bg-info rounded-pill">${analysis.summary.total_warnings}</span>`;
                html += `</li>`;
                html += `</ul>`;
                html += `</div>`;

                // Estado de importación
                html += `<div class="col-md-6">`;
                html += `<h6 class="text-primary mb-3"><i class="fas fa-shield-alt me-2"></i>Estado de Importación</h6>`;
                if (analysis.summary.can_import) {
                    html += `<div class="alert alert-success">`;
                    html += `<i class="fas fa-check-circle me-2"></i>`;
                    html += `<strong>✅ Archivo listo para importar</strong><br>`;
                    html += `No se encontraron errores críticos que impidan la importación.`;
                    html += `</div>`;
                } else {
                    html += `<div class="alert alert-danger">`;
                    html += `<i class="fas fa-exclamation-triangle me-2"></i>`;
                    html += `<strong>❌ Archivo no puede ser importado</strong><br>`;
                    html += `Se encontraron errores que deben corregirse antes de la importación.`;
                    html += `</div>`;
                }
                html += `</div>`;
                html += `</div>`;

                // Detalles de problemas
                if (analysis.duplicates.length > 0 || analysis.conflicts.length > 0 || analysis.missing_references.length > 0) {
                    html += `<div class="accordion" id="analysisAccordion">`;

                    // Duplicados
                    if (analysis.duplicates.length > 0) {
                        html += createAccordionSection('duplicates', 'Duplicados', 'danger', analysis.duplicates);
                    }

                    // Conflictos
                    if (analysis.conflicts.length > 0) {
                        html += createAccordionSection('conflicts', 'Conflictos con BD', 'warning', analysis.conflicts);
                    }

                    // Referencias faltantes
                    if (analysis.missing_references.length > 0) {
                        html += createAccordionSection('missing', 'Referencias Faltantes', 'secondary', analysis.missing_references);
                    }

                    html += `</div>`;
                }

                // Advertencias
                if (analysis.warnings.length > 0) {
                    html += `<div class="mt-3">`;
                    html += `<h6 class="text-info mb-2"><i class="fas fa-info-circle me-2"></i>Advertencias (${analysis.warnings.length})</h6>`;
                    html += `<div class="alert alert-info">`;
                    html += `<small>Se encontraron campos vacíos que podrían afectar la calidad de los datos.</small>`;
                    html += `</div>`;
                    html += `</div>`;
                }

                analysisContent.innerHTML = html;
                analysisResults.style.display = 'block';
            }

            function createAccordionSection(id, title, type, items) {
                let html = `<div class="accordion-item">`;
                html += `<h2 class="accordion-header">`;
                html += `<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${id}">`;
                html += `<i class="fas fa-exclamation-triangle me-2 text-${type}"></i>${title} (${items.length})`;
                html += `</button>`;
                html += `</h2>`;
                html += `<div id="collapse${id}" class="accordion-collapse collapse" data-bs-parent="#analysisAccordion">`;
                html += `<div class="accordion-body">`;
                html += `<div class="list-group">`;

                items.forEach(item => {
                    html += `<div class="list-group-item">`;
                    html += `<div class="d-flex w-100 justify-content-between">`;
                    html += `<h6 class="mb-1 text-${type}">${item.message}</h6>`;
                    if (item.row) {
                        html += `<small class="text-muted">Fila ${item.row}</small>`;
                    }
                    html += `</div>`;
                    if (item.value) {
                        html += `<p class="mb-1"><strong>Valor:</strong> ${item.value}</p>`;
                    }
                    if (item.existing_codigo) {
                        html += `<small class="text-muted">Código existente: ${item.existing_codigo}</small>`;
                    }
                    html += `</div>`;
                });

                html += `</div>`;
                html += `</div>`;
                html += `</div>`;
                html += `</div>`;

                return html;
            }

            function resetUploadState() {
                filePreview.style.display = "none";
                analyzeButton.style.display = "none";
                submitButton.style.display = "none";
                uploadProgress.style.display = "none";
                uploadStatus.style.display = "none";
                analysisResults.style.display = "none";
                progressBar.style.width = "0%";
                progressBar.classList.remove("bg-danger");
                progressBar.classList.add("progress-bar-animated", "bg-primary");
                progressBar.setAttribute('aria-valuenow', 0);
                uploadStatus.className = "";
                uploadStatus.innerHTML = "";
                submitButton.disabled = false;
                analyzeButton.disabled = false;
            }
        }

        // Inicializar tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Manejar detalles
        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', function() {
                const details = JSON.parse(this.dataset.details);
                const files = JSON.parse(this.dataset.files);
                updateDetailsView(details, files);
            });
        });

        function updateDetailsView(details, files) {
            let detailsHtml = '<h6 class="mb-3">Detalles de registros:</h6><ul class="list-group">';
            
            details.forEach(detail => {
                const statusClass = detail.status === 'success' ? 'text-success' : 'text-danger';
                const iconClass = detail.status === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                
                detailsHtml += `
                    <li class="list-group-item">
                        <div class="${statusClass}">
                            <i class="fas ${iconClass} me-2"></i>
                            ${detail.message}
                        </div>`;

                if (detail.files?.length > 0) {
                    detailsHtml += '<div class="mt-2 ms-4 text-muted small">';
                    detail.files.forEach(file => {
                        detailsHtml += `<div><i class="fas fa-file me-2"></i>${file}</div>`;
                    });
                    detailsHtml += '</div>';
                }
                
                detailsHtml += '</li>';
            });
            detailsHtml += '</ul>';

            document.querySelector('.details-content').innerHTML = detailsHtml;
        }
    });
</script>
@endpush
@endsection