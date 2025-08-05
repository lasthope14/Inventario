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
                    <option value="{{ $ubicacion->id }}" {{ (isset($movimiento) && $movimiento->ubicacion_origen == $ubicacion->id) || (!isset($movimiento) && $inventario->ubicacion_id == $ubicacion->id) ? 'selected' : '' }}>
                        {{ $ubicacion->nombre }}
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
            <label for="nuevo_estado">Estado del Elemento</label>
            <select name="nuevo_estado" id="nuevo_estado" class="form-control" required>
                <option value="">Seleccione el nuevo estado</option>
                <option value="disponible" {{ isset($movimiento) && $movimiento->nuevo_estado == 'disponible' ? 'selected' : '' }}>Disponible</option>
                <option value="en uso" {{ isset($movimiento) && $movimiento->nuevo_estado == 'en uso' ? 'selected' : '' }}>En uso</option>
                <option value="en mantenimiento" {{ isset($movimiento) && $movimiento->nuevo_estado == 'en mantenimiento' ? 'selected' : '' }}>En mantenimiento</option>
                <option value="dado de baja" {{ isset($movimiento) && $movimiento->nuevo_estado == 'dado de baja' ? 'selected' : '' }}>Dado de baja</option>
                <option value="robado" {{ isset($movimiento) && $movimiento->nuevo_estado == 'robado' ? 'selected' : '' }}>Robado</option>
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
    flatpickr.localize(flatpickr.l10ns.es);
    
    const config = {
        dateFormat: "d/m/Y H:i",
        enableTime: true,
        time_24hr: true,
        allowInput: false,
        disableMobile: true,
        defaultHour: new Date().getHours(),
        defaultMinute: new Date().getMinutes(),
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
            },
            months: {
                shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                longhand: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
            }
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

        submitButton.disabled = true;
        form.setAttribute('data-submitting', 'true');
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
    });
});
</script>
@endpush

@endsection