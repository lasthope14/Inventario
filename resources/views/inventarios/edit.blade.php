@extends('layouts.app')

@section('title', 'Editar Equipo - ' . $inventario->nombre)

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
                                    <i class="fas fa-edit" style="font-size: 1.2rem;"></i>
                                </div>
                                <div>
                                    <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Editar: {{ $inventario->nombre }}</h2>
                                    <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Código: {{ $inventario->codigo_unico ?? $inventario->codigo }}</p>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('inventarios.show', $inventario->id) }}" id="verShowBtn" class="btn" style="background-color: #6c757d; color: white; border: none; border-radius: 6px; padding: 8px 16px; font-size: 0.875rem; font-weight: 500;">
                                    <i class="fas fa-eye me-1"></i>Ver
                                </a>
                                <button type="button" id="volverEditBtn" class="btn" style="background-color: #495057; color: white; border: none; border-radius: 6px; padding: 8px 16px; font-size: 0.875rem; font-weight: 500;">
                                    <i class="fas fa-arrow-left me-1"></i>Volver
                                </button>
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
                            
                            /* Form controls para tema oscuro */
                            [data-bs-theme="dark"] .form-control {
                                background-color: #374151;
                                border-color: #6b7280;
                                color: #f9fafb;
                            }
                            
                            [data-bs-theme="dark"] .form-control:focus {
                                background-color: #374151;
                                border-color: #3b82f6;
                                color: #f9fafb;
                                box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
                            }
                            
                            [data-bs-theme="dark"] .form-select {
                                background-color: #374151;
                                border-color: #6b7280;
                                color: #f9fafb;
                            }
                            
                            [data-bs-theme="dark"] .form-select:focus {
                                background-color: #374151;
                                border-color: #3b82f6;
                                color: #f9fafb;
                                box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
                            }
                        </style>
                        
                        <form action="{{ route('inventarios.update', $inventario->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Contenedor 1: Información Básica -->
                            <div class="row mb-4 mx-0">
                                <div class="col-12 px-0">
                                    <div class="card">
                                        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                                            <style>
                                                [data-bs-theme="dark"] .card-header {
                                                    background-color: #374151 !important;
                                                    border-bottom: 1px solid #4b5563 !important;
                                                }
                                                [data-bs-theme="dark"] .card-header h2,
                                                [data-bs-theme="dark"] .card-header p {
                                                    color: #f8fafc !important;
                                                }
                                                [data-bs-theme="dark"] .card {
                                                    background-color: #1f2937 !important;
                                                    border-color: #374151 !important;
                                                }
                                                [data-bs-theme="dark"] .ubicacion-item {
                                                    background-color: #374151 !important;
                                                    border-color: #4b5563 !important;
                                                }
                                                [data-bs-theme="dark"] .form-label {
                                                    color: #f8fafc !important;
                                                }
                                                [data-bs-theme="dark"] .form-select,
                                                [data-bs-theme="dark"] .form-control {
                                                    background-color: #1f2937 !important;
                                                    border-color: #4b5563 !important;
                                                    color: #f8fafc !important;
                                                }
                                                [data-bs-theme="dark"] .form-select option {
                                                    background-color: #1f2937 !important;
                                                    color: #f8fafc !important;
                                                }
                                            </style>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #007bff; border-radius: 50%; color: white;">
                                                    <i class="fas fa-id-card" style="font-size: 1.2rem;"></i>
                                                </div>
                                                <div>
                                                    <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Información Básica</h2>
                                                    <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Datos principales del equipo</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="nombre" class="form-label">Nombre del Equipo</label>
                                                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $inventario->nombre) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="codigo" class="form-label">Código</label>
                                                    <input type="text" class="form-control" id="codigo" name="codigo" value="{{ old('codigo', $inventario->codigo) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="categoria_id" class="form-label">Categoría</label>
                                                    <select class="form-select" id="categoria_id" name="categoria_id">
                                                        <option value="">Seleccionar categoría</option>
                                                        @foreach($categorias as $categoria)
                                                            <option value="{{ $categoria->id }}" {{ old('categoria_id', $inventario->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                                                {{ $categoria->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="propietario" class="form-label">Propietario</label>
                                                    <input type="text" class="form-control" id="propietario" name="propietario" value="{{ old('propietario', $inventario->propietario ?? 'HIDROOBRAS') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="tipo_propiedad" class="form-label">Tipo de Propiedad</label>
                                                    <select class="form-select" id="tipo_propiedad" name="tipo_propiedad" required>
                                                        <option value="propio" {{ old('tipo_propiedad', $inventario->tipo_propiedad) == 'propio' ? 'selected' : '' }}>Propio</option>
                                                        <option value="alquiler" {{ old('tipo_propiedad', $inventario->tipo_propiedad) == 'alquiler' ? 'selected' : '' }}>Alquiler</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label for="descripcion" class="form-label">Descripción</label>
                                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $inventario->descripcion) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contenedor 2: Detalles Técnicos -->
                            <div class="row mb-4 mx-0">
                                <div class="col-12 px-0">
                                    <div class="card">
                                        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #28a745; border-radius: 50%; color: white;">
                                                    <i class="fas fa-cogs" style="font-size: 1.2rem;"></i>
                                                </div>
                                                <div>
                                                    <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Detalles Técnicos</h2>
                                                    <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Especificaciones y características técnicas</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="marca" class="form-label">Marca</label>
                                                    <input type="text" class="form-control" id="marca" name="marca" value="{{ old('marca', $inventario->marca) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="modelo" class="form-label">Modelo</label>
                                                    <input type="text" class="form-control" id="modelo" name="modelo" value="{{ old('modelo', $inventario->modelo) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="numero_serie" class="form-label">Número de Serie</label>
                                                    <input type="text" class="form-control" id="numero_serie" name="numero_serie" value="{{ old('numero_serie', $inventario->numero_serie) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="proveedor_id" class="form-label">Proveedor</label>
                                                    <select class="form-select" id="proveedor_id" name="proveedor_id">
                                                        <option value="">Seleccionar proveedor</option>
                                                        @foreach($proveedores as $proveedor)
                                                            <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $inventario->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                                                                {{ $proveedor->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contenedor 3: Información Financiera -->
                            <div class="row mb-4 mx-0">
                                <div class="col-12 px-0">
                                    <div class="card">
                                        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #28a745; border-radius: 50%; color: white;">
                                                    <i class="fas fa-chart-line" style="font-size: 1.2rem;"></i>
                                                </div>
                                                <div>
                                                    <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Información Financiera</h2>
                                                    <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Detalles económicos y de adquisición</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="valor_unitario" class="form-label">Valor Unitario</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" id="valor_unitario" name="valor_unitario" value="{{ old('valor_unitario', $inventario->valor_unitario) }}" step="0.01" min="0">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="estado_financiero" class="form-label">Estado Financiero</label>
                                                    <select class="form-select" id="estado_financiero" name="estado_financiero">
                                                        <option value="">Seleccionar estado</option>
                                                        <option value="activo" {{ old('estado_financiero', $inventario->estado_financiero) == 'activo' ? 'selected' : '' }}>Activo</option>
                                                        <option value="depreciado" {{ old('estado_financiero', $inventario->estado_financiero) == 'depreciado' ? 'selected' : '' }}>Depreciado</option>
                                                        <option value="dado_de_baja" {{ old('estado_financiero', $inventario->estado_financiero) == 'dado_de_baja' ? 'selected' : '' }}>Dado de Baja</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="fecha_compra" class="form-label">Fecha de Compra</label>
                                                    <input type="date" class="form-control" id="fecha_compra" name="fecha_compra" value="{{ old('fecha_compra', $inventario->fecha_compra ? $inventario->fecha_compra->format('Y-m-d') : '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="numero_factura" class="form-label">Número de Factura</label>
                                                    <input type="text" class="form-control" id="numero_factura" name="numero_factura" value="{{ old('numero_factura', $inventario->numero_factura) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="depreciacion" class="form-label">Depreciación</label>
                                                    <input type="text" class="form-control" id="depreciacion" name="depreciacion" value="{{ old('depreciacion', $inventario->depreciacion) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="fecha_inspeccion" class="form-label">Fecha de Inspección</label>
                                                    <input type="date" class="form-control" id="fecha_inspeccion" name="fecha_inspeccion" value="{{ old('fecha_inspeccion', $inventario->fecha_inspeccion ? $inventario->fecha_inspeccion->format('Y-m-d') : '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contenedor 4: Cantidades y Estados por Ubicación -->
                            <div class="row mb-4 mx-0">
                                <div class="col-12 px-0">
                                    <div class="card">
                                        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #17a2b8; border-radius: 50%; color: white;">
                                                    <i class="fas fa-map-marker-alt" style="font-size: 1.2rem;"></i>
                                                </div>
                                                <div>
                                                    <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Ubicaciones y Cantidades</h2>
                                                    <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Distribución del equipo por ubicaciones</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <div id="ubicaciones-container">
                                                @foreach($inventario->ubicaciones as $index => $ubicacion)
                                                    <div class="ubicacion-item mb-3 p-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <label class="form-label">Ubicación</label>
                                                                <select class="form-select" name="ubicaciones_existentes[{{ $ubicacion->id }}][ubicacion_id]" data-ubicacion-id="{{ $ubicacion->pivot->ubicacion_id }}">
                                                    <option value="">Seleccionar ubicación</option>
                                                    @foreach($ubicaciones as $ub)
                                                        <option value="{{ $ub->id }}" {{ ($ubicacion->pivot->ubicacion_id == $ub->id) ? 'selected' : '' }}>
                                                            {{ $ub->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">Cantidad</label>
                                                                <input type="number" class="form-control cantidad-input" name="ubicaciones_existentes[{{ $ubicacion->id }}][cantidad]" value="{{ $ubicacion->pivot->cantidad ?? 1 }}" min="0">
                                                <input type="hidden" name="ubicacion_existente[{{ $ubicacion->id }}]" value="{{ $ubicacion->id }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">Estado</label>
                                                                <select class="form-select" name="ubicaciones_existentes[{{ $ubicacion->id }}][estado]">
                                                    <option value="disponible" {{ ($ubicacion->pivot->estado == 'disponible') ? 'selected' : '' }}>Disponible</option>
                                                    <option value="en uso" {{ ($ubicacion->pivot->estado == 'en uso') ? 'selected' : '' }}>En Uso</option>
                                                    <option value="en mantenimiento" {{ ($ubicacion->pivot->estado == 'en mantenimiento') ? 'selected' : '' }}>En Mantenimiento</option>
                                                    <option value="dado de baja" {{ ($ubicacion->pivot->estado == 'dado de baja') ? 'selected' : '' }}>Dado de Baja</option>
                                                    <option value="robado" {{ ($ubicacion->pivot->estado == 'robado') ? 'selected' : '' }}>Robado</option>
                                                </select>
                                                            </div>
                                                            <div class="col-md-2 d-flex align-items-end">
                                                                <button type="button" class="btn btn-outline-danger remove-ubicacion" style="width: 100%; height: calc(1.5em + 1.2rem + 2px);">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-outline-primary" id="add-ubicacion">
                                                <i class="fas fa-plus me-2"></i>Agregar Ubicación
                                            </button>
                                            <div class="mt-3">
                                                <strong>Cantidad Total: <span id="cantidad-total">0</span></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contenedor 5: Documentos y QR -->
                            <div class="row mb-4 mx-0">
                                <div class="col-12 px-0">
                                    <div class="card">
                                        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #6f42c1; border-radius: 50%; color: white;">
                                                    <i class="fas fa-qrcode" style="font-size: 1.2rem;"></i>
                                                </div>
                                                <div>
                                                    <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Documentos y QR</h2>
                                                    <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Códigos QR y enlaces de documentación</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="qr_code" class="form-label">Código QR (Imagen)</label>
                                                    <input type="file" class="form-control" id="qr_code" name="qr_code" accept="image/*">
                                                    @if($inventario->qr_code)
                                                        <div class="mt-2">
                                                            <small class="text-muted">QR actual: {{ basename($inventario->qr_code) }}</small>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="enlace_documentacion" class="form-label">Enlace de Documentación</label>
                                                    <input type="url" class="form-control" id="enlace_documentacion" name="enlace_documentacion" value="{{ old('enlace_documentacion', $inventario->enlace_documentacion) }}" placeholder="https://...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contenedor 6: Observaciones -->
                            <div class="row mb-4 mx-0">
                                <div class="col-12 px-0">
                                    <div class="card">
                                        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #fd7e14; border-radius: 50%; color: white;">
                                                    <i class="fas fa-sticky-note" style="font-size: 1.2rem;"></i>
                                                </div>
                                                <div>
                                                    <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Observaciones</h2>
                                                    <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Notas y comentarios adicionales</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label for="observaciones" class="form-label">Observaciones</label>
                                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="4" placeholder="Ingrese observaciones adicionales...">{{ old('observaciones', $inventario->observaciones) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contenedor 7: Imágenes Adicionales -->
                            <div class="row mb-4 mx-0">
                                <div class="col-12 px-0">
                                    <div class="card">
                                        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #ffc107; border-radius: 50%; color: #212529;">
                                                    <i class="fas fa-images" style="font-size: 1.2rem;"></i>
                                                </div>
                                                <div>
                                                    <h2 class="mb-1" style="color: #212529; font-size: 1.5rem;">Imágenes</h2>
                                                    <p class="mb-0" style="color: #212529; font-size: 0.9rem;">Fotografías del equipo</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <!-- Imágenes Existentes -->
                                            @php
                                                $imagenes = $inventario->getMedia('imagenes');
                                                $totalImagenes = $imagenes->count();
                                                $imagenPrincipal = null;
                                                $imagenPrincipalFileName = null;
                                                
                                                // Determinar imagen principal
                                                if($inventario->imagen_principal && file_exists(storage_path('app/public/' . $inventario->imagen_principal))) {
                                                    $imagenPrincipal = asset('storage/' . $inventario->imagen_principal);
                                                    $imagenPrincipalFileName = basename($inventario->imagen_principal);
                                                }
                                                elseif($inventario->getFirstMediaUrl('imagenes') && $inventario->getMedia('imagenes')->count() > 0) {
                                                    $imagenPrincipal = $inventario->getFirstMediaUrl('imagenes');
                                                    $imagenPrincipalFileName = $inventario->getMedia('imagenes')->first()->file_name;
                                                }
                                                elseif($inventario->imagen && file_exists(storage_path('app/public/inventario_imagenes/' . $inventario->imagen))) {
                                                    $imagenPrincipal = asset('storage/inventario_imagenes/' . $inventario->imagen);
                                                    $imagenPrincipalFileName = $inventario->imagen;
                                                }
                                                
                                                // Filtrar imágenes adicionales (excluir la imagen principal)
                                                $imagenesAdicionales = $imagenes->filter(function($imagen) use ($imagenPrincipalFileName) {
                                                    return $imagen->file_name !== $imagenPrincipalFileName;
                                                });
                                            @endphp
                                            
                                            @php
                                                $imagenSecundaria = $imagenesAdicionales->first();
                                            @endphp
                                            
                                            @if($imagenPrincipal || $imagenSecundaria)
                                            <div class="mb-4">
                                                <h5 class="mb-3" style="color: #495057; font-weight: 600;">Imágenes Actuales del Equipo</h5>
                                                <div class="row g-4">
                                                    @if($imagenPrincipal)
                                                    <div class="col-md-6">
                                                        <div class="text-center">
                                                            <div class="position-relative d-inline-block w-100">
                                                                <img src="{{ $imagenPrincipal }}" alt="Imagen Principal" class="img-fluid" style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px; border: 3px solid #007bff; box-shadow: 0 4px 12px rgba(0,123,255,0.3);">
                                                                <span class="badge bg-primary position-absolute top-0 start-0 m-2" style="font-size: 0.75rem; font-weight: bold;">PRINCIPAL</span>
                                                            </div>
                                                            <p class="mt-2 mb-0 text-primary fw-bold" style="font-size: 0.9rem;">Imagen Principal</p>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @if($imagenSecundaria)
                                                    <div class="col-md-6">
                                                        <div class="text-center">
                                                            <div class="position-relative d-inline-block w-100">
                                                                <img src="{{ asset('storage/inventario_imagenes/' . $imagenSecundaria->file_name) }}" alt="Imagen Secundaria" class="img-fluid" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; border: 2px solid #28a745; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" onmouseover="this.style.transform='scale(1.03)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.2)'" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">
                                                                <span class="badge bg-success position-absolute top-0 end-0 m-2" style="font-size: 0.7rem;">SECUNDARIA</span>
                                                            </div>
                                                            <p class="mt-2 mb-0 text-success fw-bold" style="font-size: 0.9rem;">Imagen Secundaria</p>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                            
                                            <!-- Gestión de Imágenes -->
                                            <div class="border-top pt-4">
                                                <h5 class="mb-3" style="color: #495057; font-weight: 600;">Gestionar Imágenes</h5>
                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <div class="card border-primary" style="border-width: 2px;">
                                                            <div class="card-header bg-primary text-white">
                                                                <h6 class="mb-0"><i class="fas fa-image me-2"></i>Cambiar Imagen Principal</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <input type="file" class="form-control" id="imagen_principal" name="imagen_principal" accept="image/*" onchange="previewImage(this, 'preview-principal')">
                                                                <div id="preview-principal" class="mt-3"></div>
                                                                <small class="text-muted d-block mt-2"><i class="fas fa-info-circle me-1"></i>Selecciona una nueva imagen para reemplazar la actual</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card border-success" style="border-width: 2px;">
                                                            <div class="card-header bg-success text-white">
                                                                <h6 class="mb-0"><i class="fas fa-image me-2"></i>Cambiar Imagen Secundaria</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <input type="file" class="form-control" id="imagen_secundaria" name="imagen_secundaria" accept="image/*" onchange="previewImage(this, 'preview-secundaria')">
                                                                <div id="preview-secundaria" class="mt-3"></div>
                                                                <small class="text-muted d-block mt-2"><i class="fas fa-info-circle me-1"></i>Selecciona una nueva imagen secundaria para reemplazar la actual</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botones de Acción -->
                            <div class="row mb-4 mx-0">
                                <div class="col-12 px-0">
                                    <div class="d-flex justify-content-center gap-3">
                                        <button type="submit" class="btn" style="background-color: #28a745; color: white; border: none; border-radius: 6px; min-width: 140px; padding: 12px 20px; font-weight: 600; font-size: 0.9rem; box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);">
                                            <i class="fas fa-save me-2"></i>Guardar Cambios
                                        </button>
                                        <a href="{{ route('inventarios.show', $inventario->id) }}" class="btn" style="background-color: #6c757d; color: white; border: none; border-radius: 6px; min-width: 140px; padding: 12px 20px; font-weight: 600; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para calcular cantidad total
function calcularCantidadTotal() {
    let total = 0;
    document.querySelectorAll('.cantidad-input').forEach(input => {
        const ubicacionItem = input.closest('.ubicacion-item');
        // Solo contar si la ubicación no está oculta (marcada para eliminación)
        if (ubicacionItem && ubicacionItem.style.display !== 'none') {
            total += parseInt(input.value) || 0;
        }
    });
    document.getElementById('cantidad-total').textContent = total;
}

// Función para agregar nueva ubicación
function agregarUbicacion() {
    const container = document.getElementById('ubicaciones-container');
    const index = container.children.length;
    
    const ubicacionHtml = `
        <div class="ubicacion-item mb-3 p-3" style="border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Ubicación</label>
                    <select class="form-select" name="ubicaciones[${index}][ubicacion_id]">
                        <option value="">Seleccionar ubicación</option>
                        @foreach($ubicaciones as $ub)
                            <option value="{{ $ub->id }}">{{ $ub->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" class="form-control cantidad-input" name="ubicaciones[${index}][cantidad]" value="1" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="ubicaciones[${index}][estado]">
                        <option value="disponible">Disponible</option>
                        <option value="en uso">En Uso</option>
                        <option value="en mantenimiento">En Mantenimiento</option>
                        <option value="dado de baja">Dado de Baja</option>
                        <option value="robado">Robado</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger remove-ubicacion" style="width: 100%; height: calc(1.5em + 1.2rem + 2px);">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', ubicacionHtml);
    actualizarEventos();
    calcularCantidadTotal();
}

// Función para actualizar eventos
function actualizarEventos() {
    // Eventos para remover ubicación
    document.querySelectorAll('.remove-ubicacion').forEach(btn => {
        btn.onclick = function() {
            const ubicacionItem = this.closest('.ubicacion-item');
            const cantidadInput = ubicacionItem.querySelector('input[name^="ubicaciones_existentes["], input[name^="ubicaciones["]');
            
            if (cantidadInput) {
                // Si es una ubicación existente, marcar cantidad como 0 para eliminarla
                cantidadInput.value = 0;
                ubicacionItem.style.display = 'none';
            } else {
                // Si es una ubicación nueva, simplemente remover del DOM
                ubicacionItem.remove();
            }
            calcularCantidadTotal();
        };
    });
    
    // Eventos para calcular total
    document.querySelectorAll('.cantidad-input').forEach(input => {
        input.oninput = calcularCantidadTotal;
    });
}

// Función para preview de imagen principal
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';
            img.style.borderRadius = '8px';
            img.style.border = '1px solid #e9ecef';
            preview.appendChild(img);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Función para preview de imágenes múltiples
function previewImages(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';
    
    if (input.files) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                img.style.borderRadius = '8px';
                img.style.border = '1px solid #e9ecef';
                img.style.margin = '5px';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }
}

// Inicializar eventos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Evento para agregar ubicación
    document.getElementById('add-ubicacion').onclick = agregarUbicacion;
    
    // Actualizar eventos existentes
    actualizarEventos();
    
    // Calcular cantidad total inicial
    calcularCantidadTotal();
    
    // Manejar botón Volver
    const volverEditBtn = document.getElementById('volverEditBtn');
    if (volverEditBtn) {
        volverEditBtn.addEventListener('click', function(e) {
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
            } else {
                // Usar historial del navegador por defecto
                history.back();
            }
        });
     }
     
     // Manejar enlace Ver para limpiar sessionStorage de categoria
     const verShowBtn = document.getElementById('verShowBtn');
     if (verShowBtn) {
         verShowBtn.addEventListener('click', function() {
             // Limpiar sessionStorage de categoria para evitar navegación incorrecta
             sessionStorage.removeItem('from_categoria_view');
             sessionStorage.removeItem('categoria_return_url');
         });
     }
});
</script>
@endsection