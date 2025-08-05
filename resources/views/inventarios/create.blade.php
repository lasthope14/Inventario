@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0"><i class="fas fa-plus me-2"></i>Crear Nuevo Elemento de Inventario</h1>
            <a href="{{ route('inventarios.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('inventarios.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-4"><i class="fas fa-info-circle me-2"></i>Información Básica</h5>
                                <div class="mb-3">
                                    <label for="categoria_id" class="form-label"><i class="fas fa-tags me-2"></i>Categoría</label>
                                    <select name="categoria_id" id="categoria_id" class="form-select" required>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="nombre" class="form-label"><i class="fas fa-font me-2"></i>Nombre</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="cantidad" class="form-label"><i class="fas fa-sort-numeric-up me-2"></i>Cantidad</label>
                                    <input type="number" name="cantidad" id="cantidad" class="form-control" required min="1" value="1">
                                </div>
                                <div class="mb-3">
                                    <label for="propietario" class="form-label"><i class="fas fa-user me-2"></i>Propietario</label>
                                    <input type="text" name="propietario" id="propietario" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-4"><i class="fas fa-cog me-2"></i>Detalles Técnicos</h5>
                                <div class="mb-3">
                                    <label for="modelo" class="form-label"><i class="fas fa-cog me-2"></i>Modelo</label>
                                    <input type="text" name="modelo" id="modelo" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="numero_serie" class="form-label"><i class="fas fa-barcode me-2"></i>Número de Serie</label>
                                    <input type="text" name="numero_serie" id="numero_serie" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="marca" class="form-label"><i class="fas fa-trademark me-2"></i>Marca</label>
                                    <input type="text" name="marca" id="marca" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="proveedor_id" class="form-label"><i class="fas fa-building me-2"></i>Proveedor</label>
                                    <select name="proveedor_id" id="proveedor_id" class="form-select" required>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-4"><i class="fas fa-dollar-sign me-2"></i>Información Financiera</h5>
                                <div class="mb-3">
                                    <label for="fecha_compra" class="form-label"><i class="fas fa-calendar-alt me-2"></i>Fecha de Compra</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control flatpickr" 
                                               id="fecha_compra" 
                                               name="fecha_compra" 
                                               placeholder="Seleccione una fecha"
                                               readonly>
                                        <span class="input-group-text pointer" onclick="document.getElementById('fecha_compra').click()">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="numero_factura" class="form-label"><i class="fas fa-file-invoice me-2"></i>Número de Factura</label>
                                    <input type="text" name="numero_factura" id="numero_factura" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="valor_unitario" class="form-label">Valor Unitario</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="valor_unitario" id="valor_unitario" class="form-control" min="0" step="0.01" required value="{{ old('valor_unitario') }}">
                                    </div>
                                    <small class="text-muted"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-4"><i class="fas fa-map-marker-alt me-2"></i>Ubicación y Estado</h5>
                                <div class="mb-3">
                                    <label for="ubicacion_id" class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Ubicación</label>
                                    <select name="ubicacion_id" id="ubicacion_id" class="form-select" required>
                                        @foreach($ubicaciones as $ubicacion)
                                            <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="estado" class="form-label"><i class="fas fa-info-circle me-2"></i>Estado</label>
                                    <select name="estado" id="estado" class="form-select" required>
                                        <option value="disponible">Disponible</option>
                                        <option value="en uso">En uso</option>
                                        <option value="en mantenimiento">En mantenimiento</option>
                                        <option value="dado de baja">Dado de baja</option>
                                        <option value="robado">Robado</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="fecha_baja" class="form-label"><i class="fas fa-calendar-times me-2"></i>Fecha de Baja</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control flatpickr" 
                                               id="fecha_baja" 
                                               name="fecha_baja" 
                                               placeholder="Seleccione una fecha"
                                               readonly>
                                        <span class="input-group-text pointer" onclick="document.getElementById('fecha_baja').click()">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="fecha_inspeccion" class="form-label"><i class="fas fa-clipboard-check me-2"></i>Fecha de Inspección</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control flatpickr" 
                                               id="fecha_inspeccion" 
                                               name="fecha_inspeccion" 
                                               placeholder="Seleccione una fecha"
                                               readonly>
                                        <span class="input-group-text pointer" onclick="document.getElementById('fecha_inspeccion').click()">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4"><i class="fas fa-comment-alt me-2"></i>Observaciones</h5>
                                <textarea name="observaciones" id="observaciones" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4"><i class="fas fa-image me-2"></i>Imagen Principal</h5>
                                <input type="file" class="form-control mb-3" id="imagen_principal" name="imagen_principal" onchange="previewImage(this, 'preview_imagen_principal')">
                                <img id="preview_imagen_principal" src="" alt="Vista previa imagen principal" class="img-thumbnail mt-2" style="max-height: 200px; max-width: 100%; display: none;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4"><i class="fas fa-images me-2"></i>Imagen Secundaria</h5>
                                <input type="file" class="form-control mb-3" id="imagen_secundaria" name="imagen_secundaria" onchange="previewImage(this, 'preview_imagen_secundaria')">
                                <img id="preview_imagen_secundaria" src="" alt="Vista previa imagen secundaria" class="img-thumbnail mt-2" style="max-height: 200px; max-width: 100%; display: none;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('inventarios.index') }}" class="btn btn-secondary me-md-2">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Crear Elemento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .form-label {
        font-weight: bold;
    }
    .card {
        border-radius: 15px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-header {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    .btn {
        border-radius: 20px;
    }
    .card-title {
        color: #007bff;
    }
    .mb-3 {
        margin-bottom: 1rem !important;
    }
    .form-select, .form-control {
        height: calc(2.5rem + 2px);
    }
    .card-title i, .form-label i {
        margin-right: 0.5rem;
        width: 20px;
        text-align: center;
    }
    .pointer {
        cursor: pointer;
    }
    .input-group .input-group-text {
        background-color: #fff;
        border-left: none;
    }
    .flatpickr-input {
        background-color: #fff !important;
    }
    .flatpickr-input:focus {
        box-shadow: none !important;
        border-color: #dee2e6 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.style.display = 'block';
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    flatpickr.localize(flatpickr.l10ns.es);
    
    const config = {
        dateFormat: "d/m/Y",
        allowInput: false,
        disableMobile: true,
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],         
           },
           months: {
               shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
               longhand: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
           },
       },
       onChange: function(selectedDates, dateStr, instance) {
           instance.input.value = dateStr;
       }
   };

   document.querySelectorAll('.flatpickr').forEach(function(elem) {
       flatpickr(elem, config);
   });
});
</script>
@endpush