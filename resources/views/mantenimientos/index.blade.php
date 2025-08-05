@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h2 class="mb-3 mb-md-0 fs-4">
                    <i class="fas fa-tools me-2"></i> Mantenimientos
                    @if(request('filtro') === 'realizados')
                        <span class="badge bg-success ms-2">Realizados</span>
                    @elseif(request('filtro') === 'pendientes')
                        <span class="badge bg-warning ms-2">Pendientes</span>
                    @endif
                </h2>
                <div class="d-flex flex-wrap justify-content-center justify-content-md-end">
                    @if(request()->has('inventario_id'))
                        <a href="{{ route('inventarios.show', request('inventario_id')) }}" class="btn btn-light btn-sm me-2 mb-2 mb-md-0" id="volverInventarioBtn">
                            <i class="fas fa-arrow-left me-1"></i> Volver al Inventario
                        </a>
                    @endif
                    <div class="btn-group mb-2 mb-md-0" role="group">
                        <a href="{{ route('mantenimientos.index') }}" class="btn btn-light btn-sm {{ !request('filtro') ? 'active' : '' }}">
                            <i class="fas fa-list me-1"></i> Todos
                        </a>
                        <a href="{{ route('mantenimientos.index', ['filtro' => 'realizados']) }}" class="btn btn-light btn-sm {{ request('filtro') === 'realizados' ? 'active' : '' }}">
                            <i class="fas fa-check me-1"></i> Realizados
                        </a>
                        <a href="{{ route('mantenimientos.index', ['filtro' => 'pendientes']) }}" class="btn btn-light btn-sm {{ request('filtro') === 'pendientes' ? 'active' : '' }}">
                            <i class="fas fa-clock me-1"></i> Pendientes
                        </a>
                    </div>
                    @if(auth()->user()->role->name === 'administrador' || auth()->user()->role->name === 'almacenista')
                        <a href="{{ route('mantenimientos.create') }}" class="btn btn-light btn-sm ms-md-2">
                            <i class="fas fa-plus me-1"></i> Nuevo Mantenimiento
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($mantenimientos->isEmpty())
                <p class="text-muted fs-5 text-center">No hay mantenimientos registrados.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Elemento</th>
                                <th>Tipo</th>
                                <th>Fecha Programada</th>
                                <th>Estado</th>
                                <th>Responsable</th>
                                <th>Solicitado por</th>
                                <th>Periodicidad</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mantenimientos as $mantenimiento)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $mantenimiento->inventario->nombre }}</span>
                                            <small class="text-muted">{{ $mantenimiento->inventario->codigo_unico }}</small>
                                        </div>
                                    </td>
                                    <td>{{ ucfirst($mantenimiento->tipo) }}</td>
                                    <td>{{ $mantenimiento->fecha_programada->format('d/m/Y') }}</td>
                                    <td>
                                        @if($mantenimiento->fecha_realizado)
                                            <span class="badge bg-success">Realizado</span>
                                        @else
                                            <span class="badge bg-warning">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>{{ $mantenimiento->responsable ? $mantenimiento->responsable->nombre : 'No asignado' }}</td>
                                    <td>{{ $mantenimiento->solicitadoPor ? $mantenimiento->solicitadoPor->name : 'No asignado' }}</td>
                                    <td>{{ ucfirst($mantenimiento->periodicidad ?? 'N/A') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('mantenimientos.show', $mantenimiento) }}" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(auth()->user()->role->name === 'administrador')
                                                <a href="{{ route('mantenimientos.edit', $mantenimiento) }}" class="btn btn-outline-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $mantenimientos->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media (max-width: 767.98px) {
        .table-responsive {
            font-size: 0.875rem;
        }
        .table th, .table td {
            white-space: nowrap;
        }
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
// El sistema de navegación ahora es manejado globalmente por NavigationStateManager
</script>
@endpush