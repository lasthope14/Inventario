@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Información básica de la ubicación -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0 fs-4"><i class="fas fa-map-marker-alt me-2"></i>Detalles de la Ubicación</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h5 class="card-title">{{ $ubicacion->nombre }}</h5>
                    <p class="card-text"><strong>Descripción:</strong> {{ $ubicacion->descripcion ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="card-text"><strong>Creada el:</strong> {{ $ubicacion->created_at->format('d/m/Y H:i') }}</p>
                    <p class="card-text"><strong>Última actualización:</strong> {{ $ubicacion->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('ubicaciones.edit', $ubicacion) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-1"></i>Editar
                </a>
                <a href="{{ route('ubicaciones.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Volver a la lista
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas de inventarios -->
    <div class="row mb-4">
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $estadisticas['total_inventarios'] }}</h3>
                    <p class="mb-0 small">Tipos de Inventario</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $estadisticas['total_unidades'] }}</h3>
                    <p class="mb-0 small">Total Unidades</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $estadisticas['disponibles'] }}</h3>
                    <p class="mb-0 small">Disponibles</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $estadisticas['en_uso'] }}</h3>
                    <p class="mb-0 small">En Uso</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $estadisticas['en_mantenimiento'] }}</h3>
                    <p class="mb-0 small">En Mantenimiento</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $estadisticas['dados_de_baja'] + $estadisticas['robados'] }}</h3>
                    <p class="mb-0 small">Fuera de Servicio</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de inventarios en esta ubicación -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h3 class="mb-0 fs-5"><i class="fas fa-boxes me-2"></i>Inventarios en esta Ubicación</h3>
        </div>
        <div class="card-body">
            @if($inventarios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Marca/Modelo</th>
                                <th>Número de Serie</th>
                                <th>Cantidad</th>
                                <th>Estado</th>
                                <th>Última Actualización</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventarios as $inventario)
                                <tr>
                                    <td>
                                        <code class="text-primary">{{ $inventario->codigo_unico }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $inventario->nombre }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $inventario->categoria_nombre }}</span>
                                    </td>
                                    <td>
                                        {{ $inventario->marca ?? 'N/A' }}
                                        @if($inventario->modelo)
                                            <br><small class="text-muted">{{ $inventario->modelo }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $inventario->numero_serie ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary fs-6">{{ $inventario->cantidad }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $estadoClases = [
                                                'disponible' => 'bg-success',
                                                'en uso' => 'bg-warning',
                                                'en mantenimiento' => 'bg-secondary',
                                                'dado de baja' => 'bg-danger',
                                                'robado' => 'bg-dark'
                                            ];
                                            $clase = $estadoClases[$inventario->estado] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $clase }}">{{ ucfirst($inventario->estado) }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($inventario->ultima_actualizacion)->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <a href="{{ route('inventarios.show', $inventario->id) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay inventarios en esta ubicación</h5>
                    <p class="text-muted">Los inventarios aparecerán aquí cuando sean asignados a esta ubicación.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection