@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    <h2 class="mb-0 fs-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Análisis de Documentos Duplicados - PROBLEMA CRÍTICO
                    </h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" role="alert">
                        <h5 class="alert-heading">🚨 PROBLEMA IDENTIFICADO</h5>
                        <p>El sistema detectó que documentos con el mismo nombre están siendo sobrescritos, causando pérdida de archivos importantes.</p>
                        <hr>
                        <p class="mb-0">
                            <strong>Causa:</strong> Los documentos se guardaban sin incluir el código del elemento, 
                            causando que "hoja de vida.pdf" de un arnés reemplace "hoja de vida.pdf" de una eslinga.
                        </p>
                    </div>

                    <!-- Mensajes de sistema -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                                             @if(session('info'))
                             <div class="alert alert-info alert-dismissible fade show" role="alert">
                                 <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                                 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                             </div>
                         @endif

                         <!-- Mostrar detalles de importación si existen -->
                         @if(session('import_details'))
                             <div class="alert alert-success">
                                 <h6 class="alert-heading">
                                     <i class="fas fa-check-circle me-2"></i>Detalles de la Importación
                                 </h6>
                                 @if(session('import_stats'))
                                     @php $stats = session('import_stats'); @endphp
                                     <div class="row mb-3">
                                         <div class="col-md-3">
                                             <div class="text-center">
                                                 <h4 class="text-primary">{{ $stats['total'] }}</h4>
                                                 <small>Total Procesados</small>
                                             </div>
                                         </div>
                                         <div class="col-md-3">
                                             <div class="text-center">
                                                 <h4 class="text-success">{{ $stats['created'] }}</h4>
                                                 <small>Creados</small>
                                             </div>
                                         </div>
                                         <div class="col-md-3">
                                             <div class="text-center">
                                                 <h4 class="text-info">{{ $stats['updated'] }}</h4>
                                                 <small>Actualizados</small>
                                             </div>
                                         </div>
                                         <div class="col-md-3">
                                             <div class="text-center">
                                                 <h4 class="text-danger">{{ $stats['errors'] }}</h4>
                                                 <small>Errores</small>
                                             </div>
                                         </div>
                                     </div>
                                 @endif
                                 
                                 <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#importDetailsCollapse">
                                     <i class="fas fa-eye me-1"></i>Ver Detalles Completos
                                 </button>
                                 
                                 <div class="collapse mt-3" id="importDetailsCollapse">
                                     <div class="table-responsive">
                                         <table class="table table-sm">
                                             <thead>
                                                 <tr>
                                                     <th>Estado</th>
                                                     <th>Mensaje</th>
                                                 </tr>
                                             </thead>
                                             <tbody>
                                                 @foreach(session('import_details') as $detail)
                                                 <tr>
                                                     <td>
                                                         @if($detail['status'] === 'created')
                                                             <span class="badge bg-success">Creado</span>
                                                         @elseif($detail['status'] === 'updated')
                                                             <span class="badge bg-info">Actualizado</span>
                                                         @else
                                                             <span class="badge bg-danger">Error</span>
                                                         @endif
                                                     </td>
                                                     <td><small>{{ $detail['message'] }}</small></td>
                                                 </tr>
                                                 @endforeach
                                             </tbody>
                                         </table>
                                     </div>
                                 </div>
                             </div>
                         @endif

                         <!-- Barra de progreso para importación -->
                         <div id="importProgress" class="d-none">
                             <div class="alert alert-info">
                                 <div class="d-flex justify-content-between align-items-center">
                                     <div>
                                         <i class="fas fa-spinner fa-spin me-2"></i>
                                         <strong>Procesando importación...</strong>
                                         <span id="progressText">Iniciando...</span>
                                     </div>
                                     <div class="ms-3">
                                         <div class="progress" style="width: 200px;">
                                             <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                                  role="progressbar" style="width: 0%"></div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="row mb-4">
        <!-- Estadísticas de Documentos -->
        <div class="col-12 mb-3">
            <h5 class="text-primary"><i class="fas fa-file-alt me-2"></i>Estadísticas de Documentos</h5>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_documentos'] ?? 'N/A' }}</h4>
                            <p class="mb-0">Total Documentos</p>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['documentos_con_archivo'] ?? 'N/A' }}</h4>
                            <p class="mb-0">Con Archivo</p>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['documentos_sin_archivo'] ?? 'N/A' }}</h4>
                            <p class="mb-0">Sin Archivo</p>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['rutas_duplicadas'] ?? 'N/A' }}</h4>
                            <p class="mb-0">Archivos Compartidos</p>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🖼️ NUEVO: Estadísticas de Imágenes -->
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h5 class="text-info"><i class="fas fa-image me-2"></i>Estadísticas de Imágenes</h5>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_imagenes'] ?? 'N/A' }}</h4>
                            <p class="mb-0">Total Imágenes</p>
                        </div>
                        <div>
                            <i class="fas fa-image fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['imagenes_con_archivo'] ?? 'N/A' }}</h4>
                            <p class="mb-0">Con Archivo</p>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['imagenes_sin_archivo'] ?? 'N/A' }}</h4>
                            <p class="mb-0">Sin Archivo</p>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['imagenes_duplicadas_nombre'] ?? 'N/A' }}</h4>
                            <p class="mb-0">Duplicadas por Nombre</p>
                        </div>
                        <div>
                            <i class="fas fa-copy fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-orange text-white" style="background-color: #fd7e14;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['imagenes_duplicadas_tamaño'] ?? 'N/A' }}</h4>
                            <p class="mb-0">Duplicadas por Tamaño</p>
                        </div>
                        <div>
                            <i class="fas fa-equals fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Principales -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>Acciones de Corrección
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <form action="{{ route('admin.documents.clean-orphans') }}" method="POST" class="mb-2">
                                @csrf
                                <input type="hidden" name="dry_run" value="1">
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="fas fa-search me-2"></i>Simular Limpieza
                                </button>
                            </form>
                            <small class="text-muted">Muestra qué registros se eliminarían sin ejecutar</small>
                        </div>
                        <div class="col-md-4">
                            <form action="{{ route('admin.documents.clean-orphans') }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100" 
                                        onclick="return confirm('¿Está seguro de eliminar los registros huérfanos?')">
                                    <i class="fas fa-broom me-2"></i>Limpiar Huérfanos
                                </button>
                            </form>
                            <small class="text-muted">Elimina registros sin archivo físico</small>
                        </div>
                                                 <div class="col-md-3">
                             <a href="{{ route('admin.documents.generate-report') }}" class="btn btn-success w-100 mb-2">
                                 <i class="fas fa-download me-2"></i>Descargar Reporte
                             </a>
                             <small class="text-muted">Genera reporte completo del problema</small>
                         </div>
                         <div class="col-md-3">
                             <a href="{{ route('admin.documents.test-logging') }}" class="btn btn-info w-100 mb-2" target="_blank">
                                 <i class="fas fa-bug me-2"></i>Probar Logging
                             </a>
                             <small class="text-muted">Verificar que el sistema de logs funciona</small>
                         </div>
                     </div>
                     
                     <hr class="my-4">
                     
                     <div class="row">
                         <div class="col-12">
                             <h6 class="text-primary mb-3">
                                 <i class="fas fa-upload me-2"></i>Importación Masiva de Documentos
                             </h6>
                         </div>
                         <div class="col-md-3">
                             <a href="{{ route('admin.documents.generate-template') }}" class="btn btn-outline-primary w-100 mb-2">
                                 <i class="fas fa-file-excel me-2"></i>Plantilla Documentos
                             </a>
                             <small class="text-muted">Documentos duplicados/faltantes</small>
                         </div>
                         <div class="col-md-3">
                             <a href="{{ route('admin.documents.generate-images-template') }}" class="btn btn-outline-success w-100 mb-2">
                                 <i class="fas fa-image me-2"></i>Plantilla Imágenes
                             </a>
                             <small class="text-muted">Imágenes duplicadas/faltantes</small>
                         </div>
                         <div class="col-md-3">
                             <button type="button" class="btn btn-outline-warning w-100 mb-2" data-bs-toggle="modal" data-bs-target="#importModal">
                                 <i class="fas fa-upload me-2"></i>Importar Documentos
                             </button>
                             <small class="text-muted">Subir ZIP + Excel de mapeo</small>
                         </div>
                         <div class="col-md-3">
                             <button type="button" class="btn btn-outline-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#importImagesModal">
                                 <i class="fas fa-images me-2"></i>Importar Imágenes
                             </button>
                             <small class="text-muted">Subir ZIP + Excel de imágenes</small>
                         </div>
                         <div class="col-md-4">
                             <button type="button" class="btn btn-outline-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#instructionsModal">
                                 <i class="fas fa-question-circle me-2"></i>Ver Instrucciones
                             </button>
                             <small class="text-muted">Cómo usar la importación masiva</small>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentos Duplicados REALES (mismo archivo físico) -->
    @if(count($duplicatesForView) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Archivos Físicos Compartidos - CRÍTICO
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Los siguientes archivos físicos están siendo utilizados por múltiples documentos en diferentes elementos. 
                        <strong>Esto significa que varios registros apuntan al mismo archivo físico.</strong>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Archivo Físico Compartido</th>
                                    <th>Nombres de Documentos</th>
                                    <th>Elementos Afectados</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($duplicatesForView as $duplicate)
                                <tr>
                                    <td>
                                        <code>{{ basename($duplicate['ruta']) }}</code>
                                        <br><small class="text-muted">{{ dirname($duplicate['ruta']) }}/</small>
                                    </td>
                                    <td>
                                        @foreach($duplicate['nombres'] as $nombre)
                                            <span class="badge bg-warning text-dark mb-1">{{ $nombre }}</span><br>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $duplicate['elementos_afectados'] }} elementos</span>
                                    </td>
                                    <td>
                                        <div class="row">
                                            @foreach($duplicate['elementos'] as $elemento)
                                            <div class="col-md-6 mb-2">
                                                <div class="card border-left-primary">
                                                    <div class="card-body p-2">
                                                        <small>
                                                            <strong>{{ $elemento['codigo'] }}</strong><br>
                                                            {{ $elemento['nombre'] }}
                                                            <br>
                                                            <a href="{{ route('inventarios.show', $elemento['id']) }}" 
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye me-1"></i>Ver
                                                            </a>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
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

    <!-- Documentos Faltantes -->
    @if(count($missing) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-times me-2"></i>Documentos con Archivos Faltantes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Los siguientes documentos existen en la base de datos pero sus archivos físicos no se encuentran. 
                        Probablemente fueron sobrescritos por otros documentos con el mismo nombre.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre del Documento</th>
                                    <th>Elemento del Inventario</th>
                                    <th>Fecha de Subida</th>
                                    <th>Ruta Esperada</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($missing as $doc)
                                <tr>
                                    <td>{{ $doc['nombre'] }}</td>
                                    <td>
                                        <strong>{{ $doc['inventario_codigo'] }}</strong><br>
                                        <small>{{ $doc['inventario_nombre'] }}</small>
                                    </td>
                                    <td>{{ $doc['fecha_subida'] }}</td>
                                    <td>
                                        <small class="text-muted">{{ $doc['ruta'] }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">Archivo Faltante</span>
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

    <!-- 🖼️ NUEVO: Imágenes Duplicadas por Nombre -->
    @if(count($imagesDuplicateNames) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-copy me-2"></i>Imágenes Duplicadas por Nombre
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Las siguientes imágenes tienen el mismo nombre de archivo y están siendo usadas por múltiples elementos. 
                        Esto significa que los archivos se están sobrescribiendo.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre del Archivo</th>
                                    <th>Elementos Afectados</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($imagesDuplicateNames as $duplicate)
                                <tr>
                                    <td>
                                        <strong>{{ $duplicate['archivo'] }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $duplicate['elementos_afectados'] }} elementos</span>
                                    </td>
                                    <td>
                                        <div class="row">
                                            @foreach($duplicate['elementos'] as $elemento)
                                            <div class="col-md-6 mb-2">
                                                <div class="card border-left-warning">
                                                    <div class="card-body p-2">
                                                        <small>
                                                            <span class="badge bg-info">{{ $elemento['tipo'] }}</span><br>
                                                            <strong>{{ $elemento['inventario_codigo'] }}</strong><br>
                                                            {{ $elemento['inventario_nombre'] }}
                                                            <br>
                                                            <span class="text-muted">Tamaño: {{ number_format($elemento['tamaño'] / 1024, 2) }} KB</span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
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

    <!-- 🖼️ NUEVO: Imágenes Duplicadas por Tamaño -->
    @if(count($imagesDuplicateSize) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-white" style="background-color: #fd7e14;">
                    <h5 class="mb-0">
                        <i class="fas fa-equals me-2"></i>Imágenes Duplicadas por Tamaño
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Las siguientes imágenes tienen el mismo tamaño exacto, lo que sugiere que podrían ser la misma imagen 
                        guardada con nombres diferentes en elementos distintos.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tamaño del Archivo</th>
                                    <th>Elementos Afectados</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($imagesDuplicateSize as $duplicate)
                                <tr>
                                    <td>
                                        <strong>{{ $duplicate['tamaño_formateado'] }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $duplicate['elementos_afectados'] }} elementos</span>
                                    </td>
                                    <td>
                                        <div class="row">
                                            @foreach($duplicate['elementos'] as $elemento)
                                            <div class="col-md-6 mb-2">
                                                <div class="card border-left-info">
                                                    <div class="card-body p-2">
                                                        <small>
                                                            <span class="badge bg-info">{{ $elemento['tipo'] }}</span><br>
                                                            <strong>{{ $elemento['inventario_codigo'] }}</strong><br>
                                                            {{ $elemento['inventario_nombre'] }}
                                                            <br>
                                                            <code>{{ $elemento['archivo'] }}</code>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
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

    <!-- 🖼️ NUEVO: Imágenes Faltantes -->
    @if(count($imagesMissing) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-image me-2"></i>Imágenes con Archivos Faltantes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Las siguientes imágenes existen en la base de datos pero sus archivos físicos no se encuentran.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tipo de Imagen</th>
                                    <th>Elemento del Inventario</th>
                                    <th>Fecha de Subida</th>
                                    <th>Ruta Esperada</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($imagesMissing as $img)
                                <tr>
                                    <td>
                                        <span class="badge bg-info">{{ $img['tipo'] }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $img['inventario_codigo'] }}</strong><br>
                                        <small>{{ $img['inventario_nombre'] }}</small>
                                    </td>
                                    <td>{{ $img['fecha_subida'] }}</td>
                                    <td>
                                        <small class="text-muted">{{ $img['ruta'] }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">Archivo Faltante</span>
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

    <!-- Solución Implementada -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Solución Implementada
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h6 class="alert-heading">✅ PROBLEMA SOLUCIONADO PARA FUTUROS ARCHIVOS</h6>
                        <p>El sistema ahora ha sido corregido y los nuevos documentos e imágenes ya no se sobrescriben.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Cambios Implementados:</h6>
                            <ul>
                                <li><strong>Documentos:</strong> Se guardan con el código del elemento</li>
                                <li>Formato: <code>CODIGO_ELEMENTO_nombre_documento.extension</code></li>
                                <li>Ejemplo: <code>ARN001_hoja de vida.pdf</code></li>
                                <li><strong>🖼️ Imágenes:</strong> También usan códigos únicos</li>
                                <li>Formato: <code>CODIGO_ELEMENTO_principal.extension</code></li>
                                <li>Ejemplo: <code>ARN001_principal.jpg</code>, <code>ARN001_secundaria.jpg</code></li>
                                <li>Validación para evitar duplicados en el mismo elemento</li>
                                <li>Interfaz mejorada con instrucciones claras</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Acciones Requeridas:</h6>
                            <ol>
                                <li><strong>Contactar al almacenista</strong> para informar sobre el problema</li>
                                <li><strong>Re-subir documentos faltantes</strong> usando el nuevo sistema</li>
                                <li><strong>🖼️ Re-subir imágenes problemáticas</strong> con nombres únicos</li>
                                <li><strong>Verificar elementos afectados</strong> y sus archivos</li>
                                <li><strong>Limpiar registros huérfanos</strong> usando los botones de arriba</li>
                            </ol>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Los documentos ya subidos con el nuevo sistema están protegidos contra sobreescritura. 
                            Solo necesitas re-subir aquellos que aparecen como "faltantes" en la tabla de arriba.
                        </div>
                    </div>
                </div>
            </div>
        </div>
         </div>
 </div>

 <!-- Modal de Importación -->
 <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="importModalLabel">
                     <i class="fas fa-upload me-2"></i>Importación Masiva de Documentos
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <form action="{{ route('admin.documents.import') }}" method="POST" enctype="multipart/form-data">
                 @csrf
                 <div class="modal-body">
                     <div class="alert alert-info">
                         <i class="fas fa-info-circle me-2"></i>
                         <strong>Proceso de Importación:</strong><br>
                         1. Descarga la plantilla Excel con los elementos afectados<br>
                         2. Completa la columna "ARCHIVO_DOCUMENTO" con los nombres de tus archivos<br>
                         3. Crea un ZIP con todos los documentos<br>
                         4. Sube ambos archivos aquí
                     </div>
                     
                     <div class="row">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="mapping_file" class="form-label">
                                     <i class="fas fa-file-excel me-2"></i>Archivo de Mapeo (Excel/CSV)
                                 </label>
                                 <input type="file" class="form-control" id="mapping_file" name="mapping_file" 
                                        accept=".xlsx,.xls,.csv" required>
                                 <div class="form-text">Plantilla completada con el mapeo de documentos</div>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="documents_file" class="form-label">
                                     <i class="fas fa-file-archive me-2"></i>Archivo ZIP con Documentos
                                 </label>
                                 <input type="file" class="form-control" id="documents_file" name="documents_file" 
                                        accept=".zip" required>
                                 <div class="form-text">ZIP con todos los archivos de documentos</div>
                             </div>
                         </div>
                     </div>
                     
                     <div class="alert alert-warning">
                         <i class="fas fa-exclamation-triangle me-2"></i>
                         <strong>Importante:</strong> Los nombres de archivos en el Excel deben coincidir exactamente 
                         con los nombres de archivos dentro del ZIP.
                     </div>
                     
                     <div class="alert alert-info">
                         <i class="fas fa-info-circle me-2"></i>
                         <strong>Tipos de archivo aceptados:</strong><br>
                         • <strong>ZIP:</strong> Cualquier archivo .zip creado con WinRAR, 7-Zip, o el compresor nativo de Windows<br>
                         • <strong>Excel:</strong> Archivos .xlsx o .xls (plantilla descargada y completada)<br>
                         • <strong>Tamaño máximo:</strong> ZIP hasta 500MB, Excel hasta 10MB
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                     <button type="button" class="btn btn-info me-2" onclick="testFiles()">
                         <i class="fas fa-check me-2"></i>Probar Archivos
                     </button>
                     <button type="submit" class="btn btn-primary">
                         <i class="fas fa-upload me-2"></i>Importar Documentos
                     </button>
                 </div>
             </form>
         </div>
     </div>
 </div>

 <!-- Modal de Importación de Imágenes -->
 <div class="modal fade" id="importImagesModal" tabindex="-1" aria-labelledby="importImagesModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="importImagesModalLabel">
                     <i class="fas fa-images me-2"></i>Importación Masiva de Imágenes
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <form action="{{ route('admin.documents.import-images') }}" method="POST" enctype="multipart/form-data">
                 @csrf
                 <div class="modal-body">
                     <div class="alert alert-info">
                         <i class="fas fa-info-circle me-2"></i>
                         <strong>Proceso de Importación de Imágenes:</strong><br>
                         1. Descarga la plantilla Excel con los elementos que necesitan imágenes<br>
                         2. Completa la columna "ARCHIVO_IMAGEN" con los nombres de tus archivos<br>
                         3. Crea un ZIP con todas las imágenes<br>
                         4. Sube ambos archivos aquí
                     </div>
                     
                     <div class="row">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="images_mapping_file" class="form-label">
                                     <i class="fas fa-file-excel me-2"></i>Archivo de Mapeo (Excel/CSV)
                                 </label>
                                 <input type="file" class="form-control" id="images_mapping_file" name="mapping_file" 
                                        accept=".xlsx,.xls,.csv" required>
                                 <div class="form-text">Plantilla de imágenes completada</div>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="images_file" class="form-label">
                                     <i class="fas fa-file-archive me-2"></i>Archivo ZIP con Imágenes
                                 </label>
                                 <input type="file" class="form-control" id="images_file" name="images_file" 
                                        accept=".zip" required>
                                 <div class="form-text">ZIP con todas las imágenes</div>
                             </div>
                         </div>
                     </div>
                     
                     <div class="alert alert-success">
                         <i class="fas fa-check-circle me-2"></i>
                         <strong>Tipos de imagen aceptados:</strong><br>
                         • <strong>Formatos:</strong> JPG, JPEG, PNG, GIF, WEBP<br>
                         • <strong>Tamaño máximo:</strong> 5MB por imagen<br>
                         • <strong>Resolución recomendada:</strong> 1920x1080 o menor para mejor rendimiento
                     </div>
                     
                     <div class="alert alert-warning">
                         <i class="fas fa-exclamation-triangle me-2"></i>
                         <strong>Importante:</strong> Los nombres de archivos en el Excel deben coincidir exactamente 
                         con los nombres de archivos dentro del ZIP.
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                     <button type="button" class="btn btn-info me-2" onclick="testImagesFiles()">
                         <i class="fas fa-check me-2"></i>Probar Archivos
                     </button>
                     <button type="submit" class="btn btn-success">
                         <i class="fas fa-upload me-2"></i>Importar Imágenes
                     </button>
                 </div>
             </form>
         </div>
     </div>
 </div>

 <!-- Modal de Instrucciones -->
 <div class="modal fade" id="instructionsModal" tabindex="-1" aria-labelledby="instructionsModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-xl">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="instructionsModalLabel">
                     <i class="fas fa-question-circle me-2"></i>Instrucciones de Importación Masiva
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div class="row">
                     <div class="col-md-6">
                         <h6 class="text-primary">📋 Pasos a Seguir:</h6>
                         <ol>
                             <li><strong>Descargar Plantilla:</strong> Haz clic en "Descargar Plantilla" para obtener el Excel con todos los elementos afectados</li>
                             <li><strong>Identificar Elementos:</strong> Usa las columnas CODIGO_ELEMENTO y SERIAL para identificar cada elemento</li>
                             <li><strong>Completar Mapeo:</strong> En la columna "ARCHIVO_DOCUMENTO", escribe el nombre exacto del archivo para cada elemento</li>
                             <li><strong>Preparar Documentos:</strong> Reúne todos los documentos físicos en una carpeta</li>
                             <li><strong>Crear ZIP:</strong> Comprime todos los documentos en un solo archivo ZIP</li>
                             <li><strong>Importar:</strong> Sube tanto el Excel completado como el ZIP</li>
                         </ol>
                         
                         <h6 class="text-warning mt-4">⚠️ Importante:</h6>
                         <ul>
                             <li>Los nombres en el Excel deben coincidir exactamente con los archivos del ZIP</li>
                             <li>El sistema creará automáticamente nombres únicos usando el código del elemento</li>
                             <li>Los documentos duplicados ya no se sobrescribirán</li>
                         </ul>
                     </div>
                     <div class="col-md-6">
                         <h6 class="text-success">✅ Ejemplo de Mapeo:</h6>
                         <div class="table-responsive">
                             <table class="table table-sm table-bordered">
                                 <thead class="table-light">
                                     <tr>
                                         <th>CODIGO_ELEMENTO</th>
                                         <th>SERIAL</th>
                                         <th>NOMBRE_DOCUMENTO</th>
                                         <th>ARCHIVO_DOCUMENTO</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     <tr>
                                         <td>EAA-ACC-001</td>
                                         <td>ARN2024001</td>
                                         <td>HOJA DE VIDA</td>
                                         <td>arnes_hoja_vida_001.pdf</td>
                                     </tr>
                                     <tr>
                                         <td>EAA-EEY-010</td>
                                         <td>ESL2024010</td>
                                         <td>HOJA DE VIDA</td>
                                         <td>eslinga_hoja_vida_010.pdf</td>
                                     </tr>
                                     <tr>
                                         <td>HET-ET8-001</td>
                                         <td>TER2024001</td>
                                         <td>FACTURA COMPRA</td>
                                         <td>factura_termofusion_001.pdf</td>
                                     </tr>
                                 </tbody>
                             </table>
                         </div>
                         
                         <h6 class="text-info mt-3">📁 Estructura del ZIP:</h6>
                         <pre class="bg-light p-2 rounded"><code>documentos.zip
├── arnes_hoja_vida_001.pdf
├── eslinga_hoja_vida_010.pdf
├── factura_termofusion_001.pdf
└── ... (más archivos)</code></pre>
                         
                         <h6 class="text-success mt-3">🎯 Resultado Final:</h6>
                         <p>Los archivos se guardarán con nombres únicos usando el código del elemento:</p>
                         <ul class="text-success">
                             <li><code>EAA-ACC-001_HOJA DE VIDA.pdf</code> (Arnés Serial: ARN2024001)</li>
                             <li><code>EAA-EEY-010_HOJA DE VIDA.pdf</code> (Eslinga Serial: ESL2024010)</li>
                             <li><code>HET-ET8-001_FACTURA COMPRA.pdf</code> (Termofusión Serial: TER2024001)</li>
                         </ul>
                         <p class="text-info"><small><i class="fas fa-info-circle me-1"></i>El campo SERIAL te ayuda a identificar físicamente cada elemento en el almacén</small></p>
                     </div>
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
             </div>
         </div>
     </div>
 </div>

 @endsection

@push('styles')
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card-header {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
    
    .border-left-primary {
        border-left: 4px solid #007bff;
    }
    
    .border-left-warning {
        border-left: 4px solid #ffc107;
    }
    
    .border-left-info {
        border-left: 4px solid #17a2b8;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
    }
    
    .badge {
        font-size: 0.8em;
    }
</style>
@endpush

 @push('scripts')
 <script>
 document.addEventListener('DOMContentLoaded', function() {
     // Logging inicial en consola
     console.log('=== ANÁLISIS DE DOCUMENTOS CARGADO ===');
     console.log('Página: Importación masiva de documentos');
     console.log('Timestamp:', new Date().toISOString());
     
     // Log de estadísticas si existen
     @if(session('import_stats'))
         console.log('=== RESULTADOS DE IMPORTACIÓN ANTERIOR ===');
         console.log('Estadísticas:', @json(session('import_stats')));
         @if(session('import_details'))
             console.log('Detalles completos:', @json(session('import_details')));
         @endif
     @endif
     // Auto-cerrar alertas después de 5 segundos
     setTimeout(function() {
         var alerts = document.querySelectorAll('.alert-dismissible');
         alerts.forEach(function(alert) {
             var bsAlert = new bootstrap.Alert(alert);
             bsAlert.close();
         });
     }, 5000);

     // Manejar envío del formulario de importación
     const importForm = document.querySelector('#importModal form');
     const importProgress = document.getElementById('importProgress');
     const progressBar = document.getElementById('progressBar');
     const progressText = document.getElementById('progressText');

     if (importForm) {
         importForm.addEventListener('submit', function(e) {
             console.log('=== INICIANDO IMPORTACIÓN ===');
             
             const mappingFile = document.getElementById('mapping_file').files[0];
             const documentsFile = document.getElementById('documents_file').files[0];
             
             // Log detallado de archivos
             console.log('Archivo Excel:', {
                 name: mappingFile?.name,
                 size: mappingFile?.size,
                 type: mappingFile?.type,
                 lastModified: mappingFile?.lastModified ? new Date(mappingFile.lastModified) : null
             });
             
             console.log('Archivo ZIP:', {
                 name: documentsFile?.name,
                 size: documentsFile?.size,
                 type: documentsFile?.type,
                 lastModified: documentsFile?.lastModified ? new Date(documentsFile.lastModified) : null
             });
             
             // Mostrar barra de progreso
             importProgress.classList.remove('d-none');
             console.log('Barra de progreso mostrada');
             
             // Simular progreso
             let progress = 0;
             const interval = setInterval(function() {
                 progress += Math.random() * 10;
                 if (progress > 90) progress = 90;
                 
                 progressBar.style.width = progress + '%';
                 
                 if (progress < 20) {
                     progressText.textContent = 'Subiendo archivos...';
                     console.log('Progreso: Subiendo archivos... (' + Math.round(progress) + '%)');
                 } else if (progress < 40) {
                     progressText.textContent = 'Extrayendo ZIP...';
                     console.log('Progreso: Extrayendo ZIP... (' + Math.round(progress) + '%)');
                 } else if (progress < 60) {
                     progressText.textContent = 'Procesando mapeo...';
                     console.log('Progreso: Procesando mapeo... (' + Math.round(progress) + '%)');
                 } else if (progress < 80) {
                     progressText.textContent = 'Importando documentos...';
                     console.log('Progreso: Importando documentos... (' + Math.round(progress) + '%)');
                 } else {
                     progressText.textContent = 'Finalizando...';
                     console.log('Progreso: Finalizando... (' + Math.round(progress) + '%)');
                 }
             }, 200);

             // Limpiar intervalo cuando se complete
             setTimeout(function() {
                 clearInterval(interval);
                 progressBar.style.width = '100%';
                 progressText.textContent = 'Completado';
                 console.log('Progreso: Completado (100%)');
             }, 5000);
         });
     }

     // Validación de archivos
     const mappingInput = document.getElementById('mapping_file');
     const documentsInput = document.getElementById('documents_file');

     if (mappingInput) {
         mappingInput.addEventListener('change', function() {
             const file = this.files[0];
             if (file) {
                 const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                                   'application/vnd.ms-excel', 'text/csv'];
                 if (!validTypes.includes(file.type)) {
                     alert('Por favor seleccione un archivo Excel (.xlsx, .xls) o CSV válido');
                     this.value = '';
                 }
             }
         });
     }

     if (documentsInput) {
         documentsInput.addEventListener('change', function() {
             const file = this.files[0];
             if (file) {
                 // Lista de tipos MIME válidos para archivos ZIP
                 const validZipTypes = [
                     'application/zip',
                     'application/x-zip-compressed',
                     'application/x-zip',
                     'multipart/x-zip',
                     'application/octet-stream'
                 ];
                 
                 // Verificar por extensión como respaldo
                 const fileName = file.name.toLowerCase();
                 const hasZipExtension = fileName.endsWith('.zip');
                 
                 if (!validZipTypes.includes(file.type) && !hasZipExtension) {
                     alert('Por favor seleccione un archivo ZIP válido.\n\nTipo detectado: ' + file.type + '\nArchivo: ' + file.name + '\n\nAsegúrese de que el archivo tenga extensión .zip y haya sido creado con un compresor estándar.');
                     this.value = '';
                     return;
                 }
                 
                 // Verificación adicional del tamaño
                 if (file.size === 0) {
                     alert('El archivo ZIP está vacío');
                     this.value = '';
                     return;
                 }
                 
                 // Verificación de tamaño máximo (500MB)
                 const maxSize = 500 * 1024 * 1024; // 500MB en bytes
                 if (file.size > maxSize) {
                     alert('El archivo ZIP es demasiado grande. Máximo 500MB');
                     this.value = '';
                     return;
                 }
             }
         });
     }

     // Contador de elementos afectados
     const duplicatesCount = {{ count($duplicatesForView) }};
     const missingCount = {{ count($missing) }};
     
     if (duplicatesCount > 0 || missingCount > 0) {
         console.log(`Problema detectado: ${duplicatesCount} tipos de documentos duplicados, ${missingCount} archivos faltantes`);
     }
 });

   // Función para probar archivos antes de la importación
  function testFiles() {
      console.log('=== PROBANDO ARCHIVOS ===');
      
      const mappingFile = document.getElementById('mapping_file').files[0];
      const documentsFile = document.getElementById('documents_file').files[0];
      
      let messages = [];
      let allValid = true;
      
      console.log('Archivos seleccionados:', {
          excel: mappingFile ? mappingFile.name : 'ninguno',
          zip: documentsFile ? documentsFile.name : 'ninguno'
      });
      
      if (!mappingFile) {
          messages.push('❌ No se ha seleccionado archivo de mapeo Excel');
          allValid = false;
      } else {
          messages.push(`✅ Archivo Excel: ${mappingFile.name} (${(mappingFile.size / 1024 / 1024).toFixed(2)} MB)`);
          messages.push(`   Tipo MIME: ${mappingFile.type}`);
          messages.push(`   Última modificación: ${new Date(mappingFile.lastModified).toLocaleString()}`);
          
          console.log('Detalles archivo Excel:', {
              name: mappingFile.name,
              size: mappingFile.size,
              type: mappingFile.type,
              lastModified: new Date(mappingFile.lastModified)
          });
      }
      
      if (!documentsFile) {
          messages.push('❌ No se ha seleccionado archivo ZIP de documentos');
          allValid = false;
      } else {
          messages.push(`✅ Archivo ZIP: ${documentsFile.name} (${(documentsFile.size / 1024 / 1024).toFixed(2)} MB)`);
          messages.push(`   Tipo MIME: ${documentsFile.type}`);
          messages.push(`   Última modificación: ${new Date(documentsFile.lastModified).toLocaleString()}`);
          
          console.log('Detalles archivo ZIP:', {
              name: documentsFile.name,
              size: documentsFile.size,
              type: documentsFile.type,
              lastModified: new Date(documentsFile.lastModified)
          });
          
          // Verificar tipos MIME válidos para ZIP
          const validZipTypes = [
              'application/zip',
              'application/x-zip-compressed',
              'application/x-zip',
              'multipart/x-zip',
              'application/octet-stream'
          ];
          
          const hasZipExtension = documentsFile.name.toLowerCase().endsWith('.zip');
          
          if (validZipTypes.includes(documentsFile.type) || hasZipExtension) {
              messages.push('✅ Tipo de archivo ZIP válido');
              console.log('Validación ZIP: VÁLIDO');
          } else {
              messages.push('❌ Tipo de archivo ZIP no válido');
              console.log('Validación ZIP: INVÁLIDO - Tipo:', documentsFile.type);
              allValid = false;
          }
          
          if (documentsFile.size > 500 * 1024 * 1024) {
              messages.push('❌ Archivo ZIP demasiado grande (máximo 500MB)');
              console.log('Validación tamaño ZIP: DEMASIADO GRANDE');
              allValid = false;
          } else {
              messages.push('✅ Tamaño de archivo ZIP válido');
              console.log('Validación tamaño ZIP: VÁLIDO');
          }
      }
      
      // Log del resultado general
      console.log('Resultado de prueba:', {
          todosValidos: allValid,
          totalMensajes: messages.length
      });
      
      // Mostrar resultados
      const alertMessage = 'PRUEBA DE ARCHIVOS:\n\n' + messages.join('\n') + 
                          '\n\n' + (allValid ? '🎉 Todo listo para importar!' : '⚠️ Corrija los errores antes de continuar');
      
      alert(alertMessage);
      
      return allValid;
  }

  // Función para probar archivos de imágenes antes de la importación
  function testImagesFiles() {
      console.log('=== PROBANDO ARCHIVOS DE IMÁGENES ===');
      
      const mappingFile = document.getElementById('images_mapping_file').files[0];
      const imagesFile = document.getElementById('images_file').files[0];
      
      let messages = [];
      let allValid = true;
      
      console.log('Archivos de imágenes seleccionados:', {
          excel: mappingFile ? mappingFile.name : 'ninguno',
          zip: imagesFile ? imagesFile.name : 'ninguno'
      });
      
      if (!mappingFile) {
          messages.push('❌ No se ha seleccionado archivo de mapeo Excel');
          allValid = false;
      } else {
          messages.push(`✅ Archivo Excel: ${mappingFile.name} (${(mappingFile.size / 1024 / 1024).toFixed(2)} MB)`);
          messages.push(`   Tipo MIME: ${mappingFile.type}`);
          messages.push(`   Última modificación: ${new Date(mappingFile.lastModified).toLocaleString()}`);
      }
      
      if (!imagesFile) {
          messages.push('❌ No se ha seleccionado archivo ZIP de imágenes');
          allValid = false;
      } else {
          messages.push(`✅ Archivo ZIP: ${imagesFile.name} (${(imagesFile.size / 1024 / 1024).toFixed(2)} MB)`);
          messages.push(`   Tipo MIME: ${imagesFile.type}`);
          messages.push(`   Última modificación: ${new Date(imagesFile.lastModified).toLocaleString()}`);
          
          // Verificar tipos MIME válidos para ZIP
          const validZipTypes = [
              'application/zip',
              'application/x-zip-compressed',
              'application/x-zip',
              'multipart/x-zip',
              'application/octet-stream'
          ];
          
          const hasZipExtension = imagesFile.name.toLowerCase().endsWith('.zip');
          
          if (validZipTypes.includes(imagesFile.type) || hasZipExtension) {
              messages.push('✅ Tipo de archivo ZIP válido');
          } else {
              messages.push('❌ Tipo de archivo ZIP no válido');
              allValid = false;
          }
          
          if (imagesFile.size > 500 * 1024 * 1024) {
              messages.push('❌ Archivo ZIP demasiado grande (máximo 500MB)');
              allValid = false;
          } else {
              messages.push('✅ Tamaño de archivo ZIP válido');
          }
      }
      
      // Mostrar resultados
      const alertMessage = 'PRUEBA DE ARCHIVOS DE IMÁGENES:\n\n' + messages.join('\n') + 
                          '\n\n' + (allValid ? '🎉 Todo listo para importar imágenes!' : '⚠️ Corrija los errores antes de continuar');
      
      alert(alertMessage);
      
      return allValid;
  }
 </script>
 @endpush 