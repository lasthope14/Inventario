@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0 fs-4">Registrar Nuevo Mantenimiento</h2>
            <a href="{{ $inventario ? route('inventarios.show', $inventario->id) : route('inventarios.index') }}" class="btn btn-light btn-sm">Volver</a>
        </div>
        <div class="card-body">
            <form action="{{ route('mantenimientos.store') }}" method="POST" id="mantenimientoForm">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="mb-3">Información del Elemento</h5>
                        <div class="mb-3">
                            <label for="inventario_id" class="form-label">Elemento</label>
                            <select name="inventario_id" id="inventario_id" class="form-select" required {{ $inventario ? 'disabled' : '' }}>
                                <option value="">Seleccione el elemento</option>
                                @foreach($inventarios as $inv)
                                    <option value="{{ $inv->id }}" {{ $inventario && $inventario->id == $inv->id ? 'selected' : '' }}>
                                        {{ $inv->nombre }} ({{ $inv->codigo_unico }})
                                    </option>
                                @endforeach
                            </select>
                            @if($inventario)
                                <input type="hidden" name="inventario_id" value="{{ $inventario->id }}">
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo de Mantenimiento</label>
                            <select name="tipo" id="tipo" class="form-select" required>
                                <option value="">Seleccione el tipo</option>
                                <option value="preventivo">Preventivo</option>
                                <option value="correctivo">Correctivo</option>
                            </select>
                        </div>
                        <div class="mb-3" id="periodicidad-group" style="display: none;">
                            <label for="periodicidad" class="form-label">Periodicidad</label>
                            <select name="periodicidad" id="periodicidad" class="form-select">
                                <option value="">Seleccione la periodicidad</option>
                                <option value="diario">Diario</option>
                                <option value="semanal">Semanal</option>
                                <option value="quincenal">Quincenal</option>
                                <option value="mensual">Mensual</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="semestral">Semestral</option>
                                <option value="anual">Anual</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Fechas y Responsables</h5>
                        <div class="mb-3">
                            <label for="fecha_programada" class="form-label">Fecha Programada</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control flatpickr" 
                                       id="fecha_programada" 
                                       name="fecha_programada" 
                                       placeholder="Seleccione fecha"
                                       required
                                       readonly>
                                <span class="input-group-text pointer" onclick="document.getElementById('fecha_programada').click()">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_realizado" class="form-label">Fecha Realizado</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control flatpickr" 
                                       id="fecha_realizado" 
                                       name="fecha_realizado" 
                                       placeholder="Seleccione fecha"
                                       readonly>
                                <span class="input-group-text pointer" onclick="document.getElementById('fecha_realizado').click()">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="responsable_id" class="form-label">Responsable del Mantenimiento</label>
                            <select name="responsable_id" id="responsable_id" class="form-select">
                                <option value="">Seleccione el responsable (opcional)</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="mb-3">Detalles del Mantenimiento</h5>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="resultado" class="form-label">Resultado</label>
                            <textarea name="resultado" id="resultado" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Información Adicional</h5>
                        <div class="mb-3">
                            <label for="costo" class="form-label">Costo del Mantenimiento</label>
                            <input type="number" step="0.01" name="costo" id="costo" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="autorizado_por" class="form-label">Autorizado Por</label>
                            <input type="text" name="autorizado_por" id="autorizado_por" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Solicitado Por</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ $inventario ? route('inventarios.show', $inventario->id) : route('inventarios.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary" id="submitButton">Guardar Mantenimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .form-label {
        font-weight: bold;
    }
    .form-control, .form-select {
        width: 100%;
    }
    .mb-3 {
        margin-bottom: 1rem !important;
    }
    h5 {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
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
    document.addEventListener('DOMContentLoaded', function() {
        // Configuración del selector de tipo y periodicidad
        var tipoSelect = document.getElementById('tipo');
        var periodicidadGroup = document.getElementById('periodicidad-group');
        var periodicidadSelect = document.getElementById('periodicidad');
        
        function togglePeriodicidad() {
            if (tipoSelect.value === 'preventivo') {
                periodicidadGroup.style.display = 'block';
                periodicidadSelect.required = true;
            } else {
                periodicidadGroup.style.display = 'none';
                periodicidadSelect.required = false;
                periodicidadSelect.value = '';
            }
        }
        
        tipoSelect.addEventListener('change', togglePeriodicidad);
        togglePeriodicidad();

        // Configuración de Flatpickr
        flatpickr.localize(flatpickr.l10ns.es);
        
        const configFecha = {
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

        // Inicializar Flatpickr en los campos de fecha
        flatpickr("#fecha_programada", configFecha);
        flatpickr("#fecha_realizado", configFecha);

        // Protección contra doble envío
        const form = document.getElementById('mantenimientoForm');
        const submitButton = document.getElementById('submitButton');

        form.addEventListener('submit', function(e) {
            if (form.getAttribute('data-submitting')) {
                e.preventDefault();
                return false;
            }

            submitButton.disabled = true;
            form.setAttribute('data-submitting', 'true');
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
        });
    });
</script>
@endpush