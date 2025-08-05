@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0"><i class="fas fa-edit me-3"></i>Editar Elemento de Inventario</h1>
            <a href="{{ route('inventarios.show', $inventario) }}" class="btn btn-light btn-sm">
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

            <form action="{{ route('inventarios.update', $inventario) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Información Básica
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="categoria_id" class="form-label">Categoría</label>
                                    <select name="categoria_id" id="categoria_id" class="form-select" required>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id }}" {{ old('categoria_id', $inventario->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                                {{ $categoria->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control" required value="{{ old('nombre', $inventario->nombre) }}">
                                </div>
                                <div class="mb-3">
                                    <label for="propietario" class="form-label">Propietario</label>
                                    <input type="text" name="propietario" id="propietario" class="form-control" required value="{{ old('propietario', $inventario->propietario) }}">
                                </div>
                                <!-- Se eliminó el selector de estado general -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0"><i class="fas fa-cog me-2"></i>Detalles Técnicos</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="modelo" class="form-label">Modelo</label>
                                    <input type="text" name="modelo" id="modelo" class="form-control" value="{{ old('modelo', $inventario->modelo) }}">
                                    <small class="text-muted">Este campo es opcional</small>
                                </div>
                                <div class="mb-3">
                                    <label for="numero_serie" class="form-label">Número de Serie</label>
                                    <input type="text" name="numero_serie" id="numero_serie" class="form-control" value="{{ old('numero_serie', $inventario->numero_serie) }}">
                                    <small class="text-muted">Este campo es opcional</small>
                                </div>
                                <div class="mb-3">
                                    <label for="marca" class="form-label">Marca</label>
                                    <input type="text" name="marca" id="marca" class="form-control" value="{{ old('marca', $inventario->marca) }}">
                                    <small class="text-muted">Este campo es opcional</small>
                                </div>
                                <div class="mb-3">
                                    <label for="proveedor_id" class="form-label">Proveedor</label>
                                    <select name="proveedor_id" id="proveedor_id" class="form-select" required>
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
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0"><i class="fas fa-dollar-sign me-2"></i>Información Financiera</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="fecha_compra" class="form-label">Fecha de Compra</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control flatpickr" 
                                               id="fecha_compra" 
                                               name="fecha_compra" 
                                               value="{{ $inventario->fecha_compra_formatted }}"
                                               placeholder="Seleccione una fecha"
                                               readonly>
                                        <span class="input-group-text pointer" onclick="document.getElementById('fecha_compra').click()">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted">Este campo es opcional</small>
                                </div>
                                <div class="mb-3">
                                    <label for="numero_factura" class="form-label">Número de Factura</label>
                                    <input type="text" name="numero_factura" id="numero_factura" class="form-control" value="{{ $inventario->numero_factura }}">
                                    <small class="text-muted">Este campo es opcional</small>
                                </div>
                                <div class="mb-3">
                                    <label for="valor_unitario" class="form-label">Valor Unitario</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="valor_unitario" id="valor_unitario" class="form-control" min="0" step="0.01" required value="{{ old('valor_unitario', $inventario->valor_unitario) }}">
                                    </div>
                                    <small class="text-muted">Este campo es opcional</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i>Fechas Importantes</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="fecha_baja" class="form-label">Fecha de Baja</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control flatpickr" 
                                               id="fecha_baja" 
                                               name="fecha_baja" 
                                               value="{{ $inventario->fecha_baja_formatted }}"
                                               placeholder="Seleccione una fecha"
                                               readonly>
                                        <span class="input-group-text pointer" onclick="document.getElementById('fecha_baja').click()">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted">Este campo es opcional</small>
                                </div>
                                <div class="mb-3">
                                    <label for="fecha_inspeccion" class="form-label">Fecha de Inspección</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control flatpickr" 
                                               id="fecha_inspeccion" 
                                               name="fecha_inspeccion" 
                                               value="{{ $inventario->fecha_inspeccion_formatted }}"
                                               placeholder="Seleccione una fecha"
                                               readonly>
                                        <span class="input-group-text pointer" onclick="document.getElementById('fecha_inspeccion').click()">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted">Este campo es opcional</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Cantidades y Estados por Ubicación
                            <small class="ms-2 text-muted">Gestione la cantidad y estado de los elementos en cada ubicación</small>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th style="width: 30%">Ubicación</th>
                                        <th style="width: 30%">Cantidad</th>
                                        <th style="width: 40%">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventario->ubicaciones as $ubicacionInventario)
                                        <tr>
                                            <td class="align-middle">
                                                <strong>{{ $ubicacionInventario->ubicacion->nombre }}</strong>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           name="cantidades[{{ $ubicacionInventario->ubicacion_id }}]" 
                                                           class="form-control text-center"
                                                           value="{{ $ubicacionInventario->cantidad }}" 
                                                           min="0"
                                                           required>
                                                    <span class="input-group-text">unidades</span>
                                                </div>
                                            </td>
                                            <td>
                                                <select name="estados[{{ $ubicacionInventario->ubicacion_id }}]" 
                                                        class="form-select estado-ubicacion" 
                                                        required
                                                        data-ubicacion="{{ $ubicacionInventario->ubicacion->nombre }}">
                                                    @foreach(['disponible', 'en uso', 'en mantenimiento', 'dado de baja', 'robado'] as $estado)
                                                        <option value="{{ $estado }}" 
                                                                {{ $ubicacionInventario->estado == $estado ? 'selected' : '' }}>
                                                            {{ ucfirst($estado) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <!-- Fila para agregar nueva ubicación -->
                                    <tr class="table-light">
                                        <td>
                                            <select name="nueva_ubicacion_id" class="form-select">
                                                <option value="">Agregar nueva ubicación...</option>
                                                @foreach($ubicaciones as $ubicacion)
                                                    @if(!$inventario->ubicaciones->contains('ubicacion_id', $ubicacion->id))
                                                        <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" 
                                                       name="nueva_ubicacion_cantidad" 
                                                       class="form-control text-center" 
                                                       placeholder="Cantidad" 
                                                       min="1">
                                                <span class="input-group-text">unidades</span>
                                            </div>
                                        </td>
                                        <td>
                                            <select name="nueva_ubicacion_estado" class="form-select">
                                                <option value="">Seleccionar estado...</option>
                                                @foreach(['disponible', 'en uso', 'en mantenimiento', 'dado de baja', 'robado'] as $estado)
                                                    <option value="{{ $estado }}">{{ ucfirst($estado) }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td class="text-end"><strong>Total:</strong></td>
                                        <td colspan="2">
                                            <strong id="cantidadTotal">{{ $inventario->cantidadTotal }}</strong> unidades
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="fas fa-image me-2"></i>Imágenes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="imagen_principal" class="form-label">Imagen Principal</label>
                                <input type="file" class="form-control" id="imagen_principal" name="imagen_principal" onchange="previewImage(this, 'preview_imagen_principal')">
                                <img id="preview_imagen_principal" src="{{ $inventario->imagen_principal ? asset('storage/' . $inventario->imagen_principal) : '' }}" 
                                     alt="Vista previa imagen principal" class="img-thumbnail mt-2" style="max-height: 200px; max-width: 100%;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="imagen_secundaria" class="form-label">Imagen Secundaria</label>
                                <input type="file" class="form-control" id="imagen_secundaria" name="imagen_secundaria" onchange="previewImage(this, 'preview_imagen_secundaria')">
                                <img id="preview_imagen_secundaria" src="{{ $inventario->imagen_secundaria ? asset('storage/' . $inventario->imagen_secundaria) : '' }}" 
                                     alt="Vista previa imagen secundaria" class="img-thumbnail mt-2" style="max-height: 200px; max-width: 100%;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0"><i class="fas fa-comment-alt me-2"></i>Observaciones</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="3">{{ $inventario->observaciones }}</textarea>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('inventarios.show', $inventario) }}" class="btn btn-secondary me-md-2">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Actualizar Elemento
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
    .form-control, .form-select {
        border-radius: 10px;
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
    
    /* Nuevos estilos para la tabla de ubicaciones */
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.02);
    }
    
    .estado-ubicacion {
        transition: all 0.3s ease;
    }
    
    .estado-ubicacion option[value="disponible"] {
        background-color: #e8f5e9;
    }
    
    .estado-ubicacion option[value="en uso"] {
        background-color: #e3f2fd;
    }
    
    .estado-ubicacion option[value="en mantenimiento"] {
        background-color: #fff3e0;
    }
    
    .estado-ubicacion option[value="dado de baja"] {
        background-color: #ffebee;
    }
    
    .estado-ubicacion option[value="robado"] {
        background-color: #fce4ec;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script>
// Función para previsualización de imágenes
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
            document.getElementById(previewId).style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Configuración de Flatpickr
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

    // Manejo de cantidades y total
    function actualizarTotal() {
        let total = 0;
        document.querySelectorAll('input[name^="cantidades["]').forEach(function(input) {
            total += parseInt(input.value) || 0;
        });
        const nuevaCantidad = document.querySelector('input[name="nueva_ubicacion_cantidad"]');
        if (nuevaCantidad && nuevaCantidad.value) {
            total += parseInt(nuevaCantidad.value);
        }
        document.getElementById('cantidadTotal').textContent = total;
    }

    // Escuchar cambios en las cantidades
    document.querySelectorAll('input[type="number"]').forEach(function(input) {
        input.addEventListener('change', actualizarTotal);
        input.addEventListener('keyup', actualizarTotal);
    });

    // Validación de nueva ubicación
    const nuevaUbicacionSelect = document.querySelector('select[name="nueva_ubicacion_id"]');
    const nuevaCantidadInput = document.querySelector('input[name="nueva_ubicacion_cantidad"]');
    const nuevoEstadoSelect = document.querySelector('select[name="nueva_ubicacion_estado"]');

    function validarNuevaUbicacion() {
        if (nuevaUbicacionSelect.value) {
            nuevaCantidadInput.required = true;
            nuevoEstadoSelect.required = true;
        } else {
            nuevaCantidadInput.required = false;
            nuevoEstadoSelect.required = false;
        }
    }

    nuevaUbicacionSelect.addEventListener('change', validarNuevaUbicacion);
    
    // Validación del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        if (nuevaUbicacionSelect.value) {
            if (!nuevaCantidadInput.value || !nuevoEstadoSelect.value) {
                e.preventDefault();
                alert('Por favor complete todos los campos de la nueva ubicación');
            }
        }
    });
});
</script>
@endpush