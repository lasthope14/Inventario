@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ isset($movimiento) ? 'Editar' : 'Registrar Nuevo' }} Movimiento</h1>
    
    <form action="{{ isset($movimiento) ? route('movimientos.update', $movimiento) : route('movimientos.store') }}" method="POST" id="movimientoForm">
        @csrf
        @if(isset($movimiento))
            @method('PUT')
        @endif
        <input type="hidden" name="inventario_id" value="{{ $inventario->id }}">
        <div class="form-group mb-3">
            <label for="elemento">Elemento</label>
            <input type="text" id="elemento" class="form-control" value="{{ $inventario->nombre }} ({{ $inventario->codigo_unico }})" readonly>
        </div>
        <div class="form-group mb-3">
            <label for="ubicacion_origen">Ubicación de Origen</label>
            <select name="ubicacion_origen" id="ubicacion_origen" class="form-control" required>
                <option value="">Seleccione la ubicación de origen</option>
                @foreach($ubicaciones as $ubicacion)
                    <option value="{{ $ubicacion->id }}" 
                            {{ (isset($movimiento) && $movimiento->ubicacion_origen == $ubicacion->id) || (!isset($movimiento) && $inventario->ubicacion_id == $ubicacion->id) ? 'selected' : '' }}
                            data-cantidad="{{ $inventario->ubicaciones->where('ubicacion_id', $ubicacion->id)->first()->cantidad ?? 0 }}">
                        {{ $ubicacion->nombre }} (Disponible: {{ $inventario->ubicaciones->where('ubicacion_id', $ubicacion->id)->first()->cantidad ?? 0 }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-3">
            <label for="ubicacion_destino">Ubicación de Destino</label>
            <select name="ubicacion_destino" id="ubicacion_destino" class="form-control" required>
                <option value="">Seleccione la ubicación de destino</option>
                @foreach($ubicaciones as $ubicacion)
                    <option value="{{ $ubicacion->id }}" {{ isset($movimiento) && $movimiento->ubicacion_destino == $ubicacion->id ? 'selected' : '' }}>
                        {{ $ubicacion->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-3">
            <label for="cantidad">Cantidad a Mover</label>
            <input type="number" name="cantidad" id="cantidad" class="form-control" required min="1" max="{{ $inventario->cantidadTotal }}">
            <small class="form-text text-muted">Cantidad máxima disponible: <span id="max-cantidad">{{ $inventario->cantidadTotal }}</span></small>
        </div>
        <div class="form-group mb-3">
            <label for="nuevo_estado">Estado del Elemento</label>
            <select name="nuevo_estado" id="nuevo_estado" class="form-control" required>
                <option value="">Seleccione el nuevo estado</option>
                <option value="disponible">Disponible</option>
                <option value="en uso">En uso</option>
                <option value="en mantenimiento">En mantenimiento</option>
                <option value="dado de baja">Dado de baja</option>
                <option value="robado">Robado</option>
            </select>
            <small class="form-text text-muted">El estado se aplicará al elemento en la ubicación de destino</small>
        </div>
        <div class="form-group mb-3">
            <label for="usuario_origen_id">Empleado de Origen</label>
            <select name="usuario_origen_id" id="usuario_origen_id" class="form-control" required>
                <option value="">Seleccione el empleado de origen</option>
                @foreach($empleados as $empleado)
                    <option value="{{ $empleado->id }}" {{ isset($movimiento) && $movimiento->usuario_origen_id == $empleado->id ? 'selected' : '' }}>
                        {{ $empleado->nombre }} {{ $empleado->cargo ? '- ' . $empleado->cargo : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-3">
            <label for="usuario_destino_id">Empleado de Destino</label>
            <select name="usuario_destino_id" id="usuario_destino_id" class="form-control" required>
                <option value="">Seleccione el empleado de destino</option>
                @foreach($empleados as $empleado)
                    <option value="{{ $empleado->id }}" {{ isset($movimiento) && $movimiento->usuario_destino_id == $empleado->id ? 'selected' : '' }}>
                        {{ $empleado->nombre }} {{ $empleado->cargo ? '- ' . $empleado->cargo : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-3">
            <label for="fecha_movimiento">Fecha y Hora de Movimiento</label>
            <div class="input-group">
                <input type="text" 
                       class="form-control flatpickr" 
                       id="fecha_movimiento" 
                       name="fecha_movimiento" 
                       placeholder="Seleccione fecha y hora"
                       value="{{ isset($movimiento) ? $movimiento->fecha_movimiento->format('d/m/Y H:i') : '' }}"
                       readonly>
                <span class="input-group-text pointer" onclick="document.getElementById('fecha_movimiento').click()">
                    <i class="fas fa-calendar-alt"></i>
                </span>
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="motivo">Motivo</label>
            <textarea name="motivo" id="motivo" class="form-control" rows="3">{{ isset($movimiento) ? $movimiento->motivo : '' }}</textarea>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary" id="submitButton">
                {{ isset($movimiento) ? 'Actualizar' : 'Registrar' }} Movimiento
            </button>
            @if(isset($movimiento))
                <a href="{{ route('movimientos.show', $movimiento->id) }}" class="btn btn-secondary">Cancelar</a>
            @else
                <a href="{{ route('inventarios.show', $inventario->id) }}" class="btn btn-secondary">Cancelar</a>
            @endif
        </div>
    </form>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
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
    .spinner-border {
        width: 1rem;
        height: 1rem;
        border-width: 0.2em;
        margin-right: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Código para la cantidad máxima
    const ubicacionOrigenSelect = document.getElementById('ubicacion_origen');
    const cantidadInput = document.getElementById('cantidad');
    const maxCantidadSpan = document.getElementById('max-cantidad');

    ubicacionOrigenSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const maxCantidad = selectedOption.dataset.cantidad;
        cantidadInput.max = maxCantidad;
        maxCantidadSpan.textContent = maxCantidad;
    });

    // Configuración de Flatpickr
    flatpickr.localize(flatpickr.l10ns.es);
    
    const config = {
        dateFormat: "d/m/Y H:i",
        enableTime: true,
        time_24hr: true,
        minuteIncrement: 1,
        defaultHour: new Date().getHours(),
        defaultMinute: new Date().getMinutes(),
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
            console.log('=== FLATPICKR FECHA SELECCIONADA ===');
            console.log('Fechas seleccionadas:', selectedDates);
            console.log('String de fecha:', dateStr);
            console.log('Fecha como objeto:', selectedDates[0]);
            if (selectedDates[0]) {
                console.log('Año:', selectedDates[0].getFullYear());
                console.log('Mes:', selectedDates[0].getMonth() + 1);
                console.log('Día:', selectedDates[0].getDate());
                console.log('Hora:', selectedDates[0].getHours());
                console.log('Minutos:', selectedDates[0].getMinutes());
                console.log('Timestamp:', selectedDates[0].getTime());
            }
            console.log('=== FIN FLATPICKR ===');
            instance.input.value = dateStr;
        }
    };

    document.querySelectorAll('.flatpickr').forEach(function(elem) {
        flatpickr(elem, config);
    });

    // Protección contra doble envío
    const form = document.getElementById('movimientoForm');
    const submitButton = document.getElementById('submitButton');

    form.addEventListener('submit', function(e) {
        if (form.getAttribute('data-submitting')) {
            e.preventDefault();
            return false;
        }

        // Log detallado antes del envío
        const fechaInput = document.getElementById('fecha_movimiento');
        const formData = new FormData(form);
        
        console.log('=== DATOS DEL FORMULARIO ANTES DEL ENVÍO ===');
        console.log('Fecha seleccionada:', fechaInput.value);
        console.log('Tipo de dato fecha:', typeof fechaInput.value);
        console.log('Longitud fecha:', fechaInput.value.length);
        console.log('Todos los datos del formulario:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        console.log('=== FIN DATOS DEL FORMULARIO ===');

        submitButton.disabled = true;
        form.setAttribute('data-submitting', 'true');
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
    });
});
</script>
@endpush

@endsection