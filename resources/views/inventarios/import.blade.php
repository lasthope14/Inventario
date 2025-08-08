@extends('layouts.app')

@section('title', 'Importar Inventario')

@section('content')
<div class="container-fluid py-3">
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
                                    <i class="fas fa-file-import" style="font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Importar Inventario</h2>
                                    <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Carga masiva de equipos desde archivo ZIP</p>
                                </div>
                            </div>
                            <div>
                                <span class="badge px-3 py-2 d-flex align-items-center" style="background-color: #e3f2fd; color: #1565c0; font-size: 0.9rem; gap: 0.5rem; border: 1px solid #1976d2; font-weight: 600;">
                                    <i class="fas fa-upload" style="font-size: 1rem; color: #1976d2;"></i>
                                    Importación
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
                            
                            /* Dropzone para tema oscuro */
                            [data-bs-theme="dark"] .dropzone {
                                background-color: #374151 !important;
                                border-color: #6b7280 !important;
                                color: #f9fafb !important;
                            }
                            
                            [data-bs-theme="dark"] .dropzone .dz-message {
                                color: #d1d5db !important;
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
                            
                            /* Alertas para tema oscuro */
                            [data-bs-theme="dark"] .alert-info {
                                background-color: rgba(59, 130, 246, 0.1);
                                border-color: #3b82f6;
                                color: #93c5fd;
                            }
                            
                            [data-bs-theme="dark"] .alert-success {
                                background-color: rgba(34, 197, 94, 0.1);
                                border-color: #22c55e;
                                color: #86efac;
                            }
                            
                            [data-bs-theme="dark"] .alert-danger {
                                background-color: rgba(239, 68, 68, 0.1);
                                border-color: #ef4444;
                                color: #fca5a5;
                            }
                            
                            /* Tablas para tema oscuro */
                            [data-bs-theme="dark"] .table {
                                color: #f8fafc;
                            }
                            
                            [data-bs-theme="dark"] .table th {
                                border-color: #475569;
                                background-color: #334155;
                            }
                            
                            [data-bs-theme="dark"] .table td {
                                border-color: #475569;
                            }
                            
                            [data-bs-theme="dark"] .table-hover tbody tr:hover {
                                background-color: rgba(255, 255, 255, 0.05);
                            }
                            
                            /* Accordion para tema oscuro */
                            [data-bs-theme="dark"] .accordion-item {
                                background-color: #334155;
                                border-color: #475569;
                            }
                            
                            [data-bs-theme="dark"] .accordion-button {
                                background-color: #334155;
                                color: #f8fafc;
                                border-color: #475569;
                            }
                            
                            [data-bs-theme="dark"] .accordion-button:not(.collapsed) {
                                background-color: #1e293b;
                                color: #f8fafc;
                            }
                            
                            [data-bs-theme="dark"] .accordion-body {
                                background-color: #1e293b;
                                color: #f8fafc;
                            }
                            
                            /* Dropzone styles */
                            .dropzone {
                                border: 2px dashed #007bff;
                                border-radius: 12px;
                                background-color: #f8f9fa;
                                padding: 40px 20px;
                                text-align: center;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                min-height: 200px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            }
                            
                            .dropzone:hover {
                                border-color: #0056b3;
                                background-color: #e3f2fd;
                            }
                            
                            .dropzone.dz-drag-hover {
                                border-color: #28a745;
                                background-color: #e8f5e8;
                            }
                            
                            .upload-content h5 {
                                color: #007bff;
                                font-weight: 600;
                                margin-bottom: 8px;
                            }
                            
                            .upload-content p {
                                color: #6c757d;
                                margin-bottom: 0;
                            }
                            
                            .selected-file {
                                border: 1px solid #e9ecef;
                                border-radius: 8px;
                                background-color: #f8f9fa;
                            }
                            
                            .progress {
                                border-radius: 6px;
                                overflow: hidden;
                            }
                            
                            .file-structure {
                                background-color: #f8f9fa;
                                border: 1px solid #e9ecef;
                                border-radius: 6px;
                                padding: 12px;
                                font-family: 'Courier New', monospace;
                                font-size: 0.85rem;
                            }
                            
                            [data-bs-theme="dark"] .file-structure {
                                background-color: #374151;
                                border-color: #6b7280;
                                color: #f9fafb;
                            }
                        </style>
                        
                        <!-- Alertas -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mb-3">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Errores encontrados:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        
                        <!-- Sección de Instrucciones -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="accordion" id="instructionsAccordion">
                                    <div class="accordion-item border-0 shadow-sm">
                                        <h2 class="accordion-header" id="instructionsHeading">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#instructionsCollapse" aria-expanded="false" aria-controls="instructionsCollapse" style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border: none; border-bottom: 1px solid #dee2e6;">
                                                <div class="d-flex align-items-center w-100">
                                                    <div class="me-3">
                                                        <i class="fas fa-info-circle text-primary" style="font-size: 1.2rem;"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0 text-dark fw-bold">Instrucciones de Importación</h6>
                                                        <small class="text-muted">Sigue estos pasos para una importación exitosa - Haz clic para expandir</small>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="instructionsCollapse" class="accordion-collapse collapse" aria-labelledby="instructionsHeading" data-bs-parent="#instructionsAccordion">
                                            <div class="accordion-body p-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-primary mb-3">
                                                    <i class="fas fa-list-ol me-2"></i>Pasos a Seguir:
                                                </h6>
                                                <div class="step-by-step-guide">
                                                    <div class="step-item mb-3 p-3 border rounded" style="background-color: #f8f9fa; border-left: 4px solid #007bff !important;">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge bg-primary me-2">1</span>
                                                            <strong class="text-primary">Descargar Plantilla Excel</strong>
                                                        </div>
                                                        <p class="mb-0 text-muted">Haz clic en "Descargar Plantilla" para obtener el archivo Excel con el formato correcto</p>
                                                    </div>
                                                    
                                                    <div class="step-item mb-3 p-3 border rounded" style="background-color: #f8f9fa; border-left: 4px solid #28a745 !important;">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge bg-success me-2">2</span>
                                                            <strong class="text-success">Completar Datos en Excel</strong>
                                                        </div>
                                                        <p class="mb-2 text-muted">Llena el Excel con la información de tus equipos usando los IDs de las referencias rápidas</p>
                                                        <small class="text-info"><i class="fas fa-info-circle me-1"></i>Los nombres de archivos deben coincidir exactamente con los nombres en el Excel</small>
                                                    </div>
                                                    
                                                    <div class="step-item mb-3 p-3 border rounded" style="background-color: #fff3cd; border-left: 4px solid #ffc107 !important;">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge bg-warning text-dark me-2">3</span>
                                                            <strong class="text-warning">Crear Estructura de Carpetas</strong>
                                                        </div>
                                                        <p class="mb-2 text-muted">Organiza tus archivos con esta estructura exacta:</p>
                                                        <div class="bg-dark text-light p-3 rounded font-monospace" style="font-size: 0.85rem;">
                                                            📁 Mi_Importacion.zip<br>
                                                            ├── 📄 plantilla_inventario.xlsx <span class="text-warning">(en la raíz)</span><br>
                                                            ├── 📁 imagenes <span class="text-info">(minúsculas)</span><br>
                                                            │   ├── 📷 equipo001.jpg<br>
                                                            │   ├── 📷 equipo002.png<br>
                                                            │   └── 📷 ...<br>
                                                            └── 📁 documentos <span class="text-info">(minúsculas)</span><br>
                                                                ├── 📄 manual_equipo001.pdf<br>
                                                                ├── 📄 ficha_equipo002.pdf<br>
                                                                └── 📄 ...
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="step-item mb-3 p-3 border rounded" style="background-color: #f8f9fa; border-left: 4px solid #6f42c1 !important;">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge bg-secondary me-2">4</span>
                                                            <strong class="text-secondary">Comprimir en ZIP</strong>
                                                        </div>
                                                        <p class="mb-0 text-muted">Selecciona el Excel y las dos carpetas, luego comprime todo en un archivo ZIP</p>
                                                    </div>
                                                    
                                                    <div class="step-item mb-3 p-3 border rounded" style="background-color: #f8f9fa; border-left: 4px solid #17a2b8 !important;">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge bg-info me-2">5</span>
                                                            <strong class="text-info">Subir y Procesar</strong>
                                                        </div>
                                                        <p class="mb-0 text-muted">Arrastra el ZIP a la zona de carga, analiza (opcional) e importa los datos</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-warning mb-3">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>Importante:
                                                </h6>
                                                <div class="alert alert-warning border-0" style="background-color: #fff3cd;">
                                                    <h6 class="alert-heading mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Estructura Obligatoria del ZIP:</h6>
                                                    <ul class="mb-2">
                                                        <li><strong>Excel en la raíz:</strong> El archivo Excel debe estar en el nivel principal del ZIP</li>
                                                        <li><strong>Carpeta "imagenes":</strong> Exactamente con este nombre en minúsculas</li>
                                                        <li><strong>Carpeta "documentos":</strong> Exactamente con este nombre en minúsculas</li>
                                                        <li><strong>Nombres coincidentes:</strong> Los archivos dentro deben tener los mismos nombres que aparecen en el Excel</li>
                                                    </ul>
                                                    <hr class="my-2">
                                                    <h6 class="mb-2">Requisitos Técnicos:</h6>
                                                    <ul class="mb-0">
                                                        <li><strong>Formato ZIP:</strong> Solo archivos .zip son aceptados</li>
                                                        <li><strong>Tamaño máximo:</strong> 100MB por archivo</li>
                                                        <li><strong>IDs válidos:</strong> Usa solo los IDs mostrados en las referencias rápidas</li>
                                                    </ul>
                                                </div>
                                                
                                                <h6 class="text-success mb-3">
                                                    <i class="fas fa-lightbulb me-2"></i>Consejos:
                                                </h6>
                                                <div class="alert alert-success border-0" style="background-color: #d1e7dd;">
                                                    <h6 class="alert-heading mb-2"><i class="fas fa-lightbulb me-2"></i>Consejos para Evitar Errores:</h6>
                                                    <ul class="mb-2">
                                                        <li><strong>Verifica nombres:</strong> Los archivos en las carpetas deben tener exactamente los mismos nombres que en el Excel</li>
                                                        <li><strong>Usa "Analizar":</strong> Siempre analiza antes de importar para detectar errores</li>
                                                        <li><strong>Carpetas en minúsculas:</strong> "imagenes" y "documentos" deben estar en minúsculas</li>
                                                        <li><strong>Excel en raíz:</strong> No pongas el Excel dentro de ninguna carpeta</li>
                                                    </ul>
                                                    <div class="bg-light p-2 rounded">
                                                        <small class="text-muted"><strong>Ejemplo:</strong> Si en el Excel tienes "equipo001.jpg", debe existir el archivo "equipo001.jpg" en la carpeta "imagenes"</small>
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

                        <div class="row">
                            <!-- Sección de Carga de Archivo -->
                            <div class="col-12">
                                <!-- Área de carga principal -->
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <i class="fas fa-cloud-upload-alt" style="font-size: 1.5rem; color: #007bff;"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1" style="color: #212529; font-weight: 600;">Cargar Archivo ZIP</h5>
                                            <p class="mb-0" style="color: #6c757d; font-size: 0.9rem;">Selecciona el archivo ZIP que contiene los datos del inventario</p>
                                        </div>
                                    </div>
                                    
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
                                    <button type="button" id="analyzeBtn" class="btn btn-warning btn-sm" style="display: none; font-size: 0.9rem; padding: 0.5rem 1rem;">
                                        <i class="fas fa-search me-2"></i>
                                        Analizar Archivo
                                    </button>
                                    <button type="button" id="submitImport" class="btn btn-primary btn-sm" style="display: none; font-size: 0.9rem; padding: 0.5rem 1rem;">
                                        <i class="fas fa-upload me-2"></i>
                                        Importar Datos
                                    </button>
                                    <a href="{{ route('inventarios.template.download') }}" class="btn btn-sm" style="font-size: 0.9rem; padding: 0.5rem 1rem; background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057;">
                                        <i class="fas fa-download me-2" style="color: #6c757d;"></i>
                                        Descargar Plantilla
                                    </a>
                                </div>

                                <!-- Referencias Rápidas -->
                                <div class="mt-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header" style="background: #f8f9fa; border: none; border-bottom: 1px solid #dee2e6;">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas fa-bookmark text-primary" style="font-size: 1.1rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-dark fw-bold">Referencias para Importación</h6>
                                                    <small class="text-muted">IDs y nombres válidos para usar en tu archivo Excel</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="row g-0">
                                                <!-- Categorías -->
                                                <div class="col-md-4">
                                                    <div class="p-3 border-end h-100">
                                                        <div class="d-flex align-items-center mb-3">
                                                             <div class="me-2">
                                                                 <i class="fas fa-tags text-primary" style="font-size: 1.1rem;"></i>
                                                             </div>
                                                             <div>
                                                                 <h6 class="mb-0 fw-bold text-dark">Categorías</h6>
                                                                 <small class="text-muted">Campo: categoria_id</small>
                                                             </div>
                                                         </div>
                                                        <div class="reference-list" style="max-height: 250px; overflow-y: auto;">
                                                            @if(isset($categorias) && $categorias->count() > 0)
                                                                @foreach($categorias as $categoria)
                                                                    <div class="d-flex align-items-center justify-content-between py-2 px-3 mb-2 rounded" style="background-color: #f8f9fa; border-left: 3px solid #007bff;">
                                                                        <span class="fw-bold text-dark" style="font-size: 1rem; min-width: 35px;">{{ $categoria->id }}</span>
                                                                         <span class="text-dark flex-grow-1 ms-2" style="font-size: 0.95rem;">{{ $categoria->nombre }}</span>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="text-center py-3">
                                                                    <i class="fas fa-exclamation-circle text-muted mb-2"></i>
                                                                    <p class="text-muted mb-0" style="font-size: 0.9rem;">No hay categorías disponibles</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Proveedores -->
                                                <div class="col-md-4">
                                                    <div class="p-3 border-end h-100">
                                                        <div class="d-flex align-items-center mb-3">
                                                             <div class="me-2">
                                                                 <i class="fas fa-truck text-success" style="font-size: 1.1rem;"></i>
                                                             </div>
                                                             <div>
                                                                 <h6 class="mb-0 fw-bold text-dark">Proveedores</h6>
                                                                 <small class="text-muted">Campo: proveedor_id</small>
                                                             </div>
                                                         </div>
                                                        <div class="reference-list" style="max-height: 250px; overflow-y: auto;">
                                                            @if(isset($proveedores) && $proveedores->count() > 0)
                                                                @foreach($proveedores as $proveedor)
                                                                    <div class="d-flex align-items-center justify-content-between py-2 px-3 mb-2 rounded" style="background-color: #f8f9fa; border-left: 3px solid #28a745;">
                                                                        <span class="fw-bold text-dark" style="font-size: 1rem; min-width: 35px;">{{ $proveedor->id }}</span>
                                                                         <span class="text-dark flex-grow-1 ms-2" style="font-size: 0.95rem;">{{ $proveedor->nombre }}</span>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="text-center py-3">
                                                                    <i class="fas fa-exclamation-circle text-muted mb-2"></i>
                                                                    <p class="text-muted mb-0" style="font-size: 0.9rem;">No hay proveedores disponibles</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Ubicaciones -->
                                                <div class="col-md-4">
                                                    <div class="p-3 h-100">
                                                        <div class="d-flex align-items-center mb-3">
                                                             <div class="me-2">
                                                                 <i class="fas fa-map-marker-alt text-warning" style="font-size: 1.1rem;"></i>
                                                             </div>
                                                             <div>
                                                                 <h6 class="mb-0 fw-bold text-dark">Ubicaciones</h6>
                                                                 <small class="text-muted">Campo: ubicacion_id</small>
                                                             </div>
                                                         </div>
                                                        <div class="reference-list" style="max-height: 250px; overflow-y: auto;">
                                                            @if(isset($ubicaciones) && $ubicaciones->count() > 0)
                                                                @foreach($ubicaciones as $ubicacion)
                                                                    <div class="d-flex align-items-center justify-content-between py-2 px-3 mb-2 rounded" style="background-color: #f8f9fa; border-left: 3px solid #ffc107;">
                                                                        <span class="fw-bold text-dark" style="font-size: 1rem; min-width: 35px;">{{ $ubicacion->id }}</span>
                                                                         <span class="text-dark flex-grow-1 ms-2" style="font-size: 0.95rem;">{{ $ubicacion->nombre }}</span>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="text-center py-3">
                                                                    <i class="fas fa-exclamation-circle text-muted mb-2"></i>
                                                                    <p class="text-muted mb-0" style="font-size: 0.9rem;">No hay ubicaciones disponibles</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light border-0">
                                            <div class="row text-center">
                                                <div class="col-12">
                                                    <small class="text-muted">
                                                        <i class="fas fa-lightbulb me-1"></i>
                                                        <strong>Tip:</strong> Usa los números de ID en tu archivo Excel para referenciar estos elementos correctamente
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                            </div>
                        </div>
                        
                        <!-- Historial de Importaciones -->
                        @if(isset($importLogs) && $importLogs->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-history" style="font-size: 1.2rem; color: #6c757d;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0" style="color: #212529; font-weight: 600;">Historial de Importaciones</h6>
                                    </div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm">
                                                <thead class="table-light">
                                                    <tr style="font-size: 0.85rem;">
                                                        <th class="py-2">Fecha</th>
                                                        <th class="py-2">Usuario</th>
                                                        <th class="py-2">Archivo</th>
                                                        <th class="py-2">Registros</th>
                                                        <th class="py-2">Estado</th>
                                                        <th class="py-2">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($importLogs as $log)
                                                    <tr style="font-size: 0.85rem;">
                                                        <td class="py-2">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                                        <td class="py-2">{{ $log->user_name ?? $log->user->name }}</td>
                                                        <td class="py-2">{{ Str::limit($log->file_name, 20) }}</td>
                                                        <td class="py-2">{{ $log->records_imported }}</td>
                                                        <td class="py-2">
                                                            @if($log->status === 'success')
                                                                <span class="badge bg-success" style="font-size: 0.75rem;">Éxito</span>
                                                            @elseif($log->status === 'reverted')
                                                                <span class="badge bg-warning" style="font-size: 0.75rem;">Revertido</span>
                                                            @else
                                                                <span class="badge bg-danger" style="font-size: 0.75rem;">Error</span>
                                                            @endif
                                                        </td>
                                                        <td class="py-2">
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <button type="button" 
                                                                        class="btn btn-outline-info btn-sm" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#logModal{{ $log->id }}"
                                                                        style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                @if($log->status === 'success')
                                                                    <form action="{{ route('importlogs.revert', $log->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" 
                                                                                class="btn btn-outline-warning btn-sm" 
                                                                                onclick="return confirm('¿Estás seguro de que quieres revertir esta importación?')"
                                                                                style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                                            <i class="fas fa-undo"></i>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                                @if($log->status === 'error')
                                                                    <form action="{{ route('importlogs.destroy', $log->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                                class="btn btn-outline-danger btn-sm" 
                                                                                onclick="return confirm('¿Estás seguro de que quieres eliminar este registro de error?')"
                                                                                style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
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
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales para detalles de logs -->
@if(isset($importLogs))
    @foreach($importLogs as $log)
    <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de Importación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Archivo:</strong> {{ $log->file_name }}<br>
                            <strong>Usuario:</strong> {{ $log->user_name ?? $log->user->name }}<br>
                            <strong>Fecha:</strong> {{ $log->created_at->format('d/m/Y H:i:s') }}<br>
                            <strong>Registros:</strong> {{ $log->records_imported }}<br>
                            <strong>Estado:</strong> 
                            @if($log->status === 'success')
                                <span class="badge bg-success">Éxito</span>
                            @elseif($log->status === 'reverted')
                                <span class="badge bg-warning">Revertido</span>
                            @else
                                <span class="badge bg-danger">Error</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($log->error_message)
                                <strong>Error:</strong><br>
                                <div class="alert alert-danger">
                                    {{ $log->error_message }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($log->details)
                        <hr>
                        <strong>Detalles:</strong>
                        <pre class="bg-light p-3 rounded">{{ is_array($log->details) ? json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $log->details }}</pre>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endif

<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

<script>
// Configuración de Dropzone
Dropzone.autoDiscover = false;

const myDropzone = new Dropzone("#importForm", {
    url: "{{ route('inventarios.import') }}",
    maxFiles: 1,
    acceptedFiles: ".zip",
    addRemoveLinks: false,
    autoProcessQueue: false,
    uploadMultiple: false,
    parallelUploads: 1,
    maxFilesize: 100, // 100MB
    dictDefaultMessage: '',
    
    init: function() {
        const dropzone = this;
        
        // Botón de envío
        document.getElementById('submitImport').addEventListener('click', function() {
            if (dropzone.getQueuedFiles().length > 0) {
                dropzone.processQueue();
            }
        });
        
        // Botón de análisis
        document.getElementById('analyzeBtn').addEventListener('click', function() {
            if (dropzone.getQueuedFiles().length > 0) {
                analyzeFile(dropzone.getQueuedFiles()[0]);
            }
        });
        
        // Botón de remover archivo
        document.getElementById('removeFile').addEventListener('click', function() {
            dropzone.removeAllFiles();
            hideFilePreview();
            hideButtons();
        });
    },
    
    addedfile: function(file) {
        showFilePreview(file);
        showButtons();
    },
    
    removedfile: function(file) {
        hideFilePreview();
        hideButtons();
    },
    
    uploadprogress: function(file, progress, bytesSent) {
        showProgress(progress);
    },
    
    success: function(file, response) {
        hideProgress();
        if (response.success) {
            showStatus('success', response.message);
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showStatus('error', response.message || 'Error en la importación');
        }
    },
    
    error: function(file, errorMessage) {
        hideProgress();
        showStatus('error', typeof errorMessage === 'string' ? errorMessage : 'Error al subir el archivo');
    }
});

function showFilePreview(file) {
    document.querySelector('.file-name').textContent = file.name;
    document.querySelector('.file-size').textContent = formatFileSize(file.size);
    document.getElementById('filePreview').style.display = 'block';
}

function hideFilePreview() {
    document.getElementById('filePreview').style.display = 'none';
}

function showButtons() {
    document.getElementById('analyzeBtn').style.display = 'block';
    document.getElementById('submitImport').style.display = 'block';
}

function hideButtons() {
    document.getElementById('analyzeBtn').style.display = 'none';
    document.getElementById('submitImport').style.display = 'none';
}

function showProgress(progress) {
    const progressBar = document.querySelector('.progress-bar');
    const progressText = document.querySelector('.progress-text');
    
    progressBar.style.width = progress + '%';
    progressBar.setAttribute('aria-valuenow', progress);
    progressText.textContent = `Subiendo... ${Math.round(progress)}%`;
    
    document.getElementById('uploadProgress').style.display = 'block';
}

function hideProgress() {
    document.getElementById('uploadProgress').style.display = 'none';
}

function showStatus(type, message) {
    const statusDiv = document.getElementById('uploadStatus');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    statusDiv.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show">
            <i class="fas ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    statusDiv.style.display = 'block';
}

function analyzeFile(file) {
    // Implementar análisis de archivo
    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    
    fetch('{{ route("inventarios.analyze") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAnalysisResults(data.analysis);
        } else {
            showStatus('error', data.message || 'Error al analizar el archivo');
        }
    })
    .catch(error => {
        showStatus('error', 'Error al analizar el archivo');
    });
}

function showAnalysisResults(analysis) {
    const content = document.getElementById('analysisContent');
    content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Registros encontrados:</h6>
                <p class="text-primary fs-4">${analysis.total_records || 0}</p>
            </div>
            <div class="col-md-6">
                <h6>Imágenes encontradas:</h6>
                <p class="text-success fs-4">${analysis.images_found || 0}</p>
            </div>
        </div>
        ${analysis.errors && analysis.errors.length > 0 ? `
            <div class="alert alert-warning">
                <h6>Advertencias:</h6>
                <ul class="mb-0">
                    ${analysis.errors.map(error => `<li>${error}</li>`).join('')}
                </ul>
            </div>
        ` : ''}
    `;
    document.getElementById('analysisResults').style.display = 'block';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endsection