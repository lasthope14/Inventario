@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0 fs-4">
                @if(request()->has('inventario_id') && $movimientos->first())
                    <i class="fas fa-exchange-alt me-2"></i> Movimientos de {{ $movimientos->first()->inventario->nombre }}
                @else
                    <i class="fas fa-exchange-alt me-2"></i> Todos los Movimientos
                @endif
                
                @if(request()->has('ubicacion_id') && request('ubicacion_id'))
                    @php
                        $ubicacionFiltro = $ubicaciones->find(request('ubicacion_id'));
                    @endphp
                    @if($ubicacionFiltro)
                        <small class="d-block text-light mt-1">
                            <i class="fas fa-filter me-1"></i> Filtrado por ubicación: {{ $ubicacionFiltro->nombre }}
                        </small>
                    @endif
                @endif
                
                @if((request()->has('ubicacion_origen') && request('ubicacion_origen')) || (request()->has('ubicacion_destino') && request('ubicacion_destino')))
                    <small class="d-block text-light mt-1">
                        <i class="fas fa-filter me-1"></i> Filtros específicos aplicados
                    </small>
                @endif
            </h2>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('movimientos.masivo') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-layer-group me-1"></i> <span class="d-none d-md-inline">Movimientos Masivos</span><span class="d-md-none">Masivos</span>
                </a>
                
                <a href="{{ route('movimientos.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> <span class="d-none d-md-inline">Exportar PDF</span><span class="d-md-none">PDF</span>
                </a>

                <a href="{{ route('inventarios.index') }}" class="btn btn-light btn-sm" id="volverInventarioBtn">
                    <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-md-inline">Volver a Inventario</span><span class="d-md-none">Volver</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-12">
                    <form method="GET" action="{{ route('movimientos.index') }}" class="row g-3">
                        <!-- Mantener filtro de inventario si existe -->
                        @if(request()->has('inventario_id'))
                            <input type="hidden" name="inventario_id" value="{{ request('inventario_id') }}">
                        @endif
                        
                        <div class="col-12 col-md-4">
                            <label for="ubicacion_id" class="form-label">Filtrar por Ubicación <small class="text-muted">(Origen o Destino)</small></label>
                            <select name="ubicacion_id" id="ubicacion_id" class="form-select">
                                <option value="">Todas las ubicaciones</option>
                                @foreach($ubicaciones as $ubicacion)
                                    <option value="{{ $ubicacion->id }}" 
                                        {{ request('ubicacion_id') == $ubicacion->id ? 'selected' : '' }}>
                                        {{ $ubicacion->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-12 col-md-4 col-lg-3">
                            <label for="ubicacion_origen" class="form-label">Ubicación Origen</label>
                            <select name="ubicacion_origen" id="ubicacion_origen" class="form-select">
                                <option value="">Todas</option>
                                @foreach($ubicaciones as $ubicacion)
                                    <option value="{{ $ubicacion->id }}" 
                                        {{ request('ubicacion_origen') == $ubicacion->id ? 'selected' : '' }}>
                                        {{ $ubicacion->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-12 col-md-4 col-lg-3">
                            <label for="ubicacion_destino" class="form-label">Ubicación Destino</label>
                            <select name="ubicacion_destino" id="ubicacion_destino" class="form-select">
                                <option value="">Todas</option>
                                @foreach($ubicaciones as $ubicacion)
                                    <option value="{{ $ubicacion->id }}" 
                                        {{ request('ubicacion_destino') == $ubicacion->id ? 'selected' : '' }}>
                                        {{ $ubicacion->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-12 col-md-2 d-flex align-items-end">
                            <div class="d-flex flex-column flex-md-row gap-2 w-100">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="fas fa-filter me-1"></i> <span class="d-none d-sm-inline">Filtrar</span>
                                </button>
                                <a href="{{ route('movimientos.index') }}{{ request()->has('inventario_id') ? '?inventario_id=' . request('inventario_id') : '' }}" 
                                   class="btn btn-outline-secondary flex-fill">
                                    <i class="fas fa-times me-1"></i> <span class="d-none d-sm-inline">Limpiar</span>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            @if($movimientos->isEmpty())
                <p class="text-muted fs-5">No hay movimientos registrados con los filtros aplicados.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Elemento</th>
                                <th class="d-none d-md-table-cell">Origen</th>
                                <th class="d-none d-md-table-cell">Destino</th>
                                <th class="d-none d-lg-table-cell">Empleado Origen</th>
                                <th class="d-none d-lg-table-cell">Empleado Destino</th>
                                <th class="d-none d-sm-table-cell">Realizado por</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movimientos as $movimiento)
                                <tr>
                                    <td>
                                    <div>{{ optional($movimiento->fecha_movimiento)->format('d/m/Y H:i') ?? $movimiento->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    <div>{{ optional($movimiento->inventario)->nombre ?? 'N/A' }}</div>
                                    <div class="d-md-none small text-muted mt-1">
                                        @php
                                            $ubicacionOrigen = \App\Models\Ubicacion::find($movimiento->ubicacion_origen);
                                            $ubicacionDestino = \App\Models\Ubicacion::find($movimiento->ubicacion_destino);
                                        @endphp
                                        <strong>De:</strong> {{ $ubicacionOrigen ? $ubicacionOrigen->nombre : ($movimiento->ubicacion_origen ?? 'N/A') }}<br>
                                        <strong>A:</strong> {{ $ubicacionDestino ? $ubicacionDestino->nombre : ($movimiento->ubicacion_destino ?? 'N/A') }}
                                    </div>
                                    <div class="d-sm-none small text-muted mt-1">
                                        <strong>Por:</strong> {{ optional($movimiento->realizadoPor)->name ?? 'Usuario eliminado' }}
                                    </div>
                                </td>
                                    <td class="d-none d-md-table-cell">
                                        @php
                                            $ubicacionOrigen = \App\Models\Ubicacion::find($movimiento->ubicacion_origen);
                                        @endphp
                                        {{ $ubicacionOrigen ? $ubicacionOrigen->nombre : ($movimiento->ubicacion_origen ?? 'N/A') }}
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        @php
                                            $ubicacionDestino = \App\Models\Ubicacion::find($movimiento->ubicacion_destino);
                                        @endphp
                                        {{ $ubicacionDestino ? $ubicacionDestino->nombre : ($movimiento->ubicacion_destino ?? 'N/A') }}
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ optional($movimiento->usuarioOrigen)->nombre ?? 'N/A' }}</td>
                                    <td class="d-none d-lg-table-cell">{{ optional($movimiento->usuarioDestino)->nombre ?? 'N/A' }}</td>
                                    <td class="d-none d-sm-table-cell">{{ optional($movimiento->realizadoPor)->name ?? 'Usuario eliminado' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('movimientos.show', $movimiento) }}" class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-eye me-1"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $movimientos->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// El sistema de navegación ahora es manejado globalmente por NavigationStateManager

// Mejorar la experiencia de filtros
document.addEventListener('DOMContentLoaded', function() {
    const filtros = ['ubicacion_id', 'ubicacion_origen', 'ubicacion_destino'];
    
    filtros.forEach(function(filtroId) {
        const elemento = document.getElementById(filtroId);
        if (elemento) {
            elemento.addEventListener('change', function() {
                // Auto-enviar formulario cuando cambie un filtro principal
                if (filtroId === 'ubicacion_id') {
                    // Limpiar filtros específicos cuando se use el filtro general
                    document.getElementById('ubicacion_origen').value = '';
                    document.getElementById('ubicacion_destino').value = '';
                    this.form.submit();
                }
            });
        }
    });
    
    // Limpiar filtro general cuando se usen filtros específicos
    ['ubicacion_origen', 'ubicacion_destino'].forEach(function(filtroId) {
        const elemento = document.getElementById(filtroId);
        if (elemento) {
            elemento.addEventListener('change', function() {
                if (this.value) {
                    document.getElementById('ubicacion_id').value = '';
                }
            });
        }
    });
});
</script>
@endpush
@endsection