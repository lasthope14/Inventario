@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h2 class="mb-3 mb-md-0 fs-4"><i class="fas fa-users me-2"></i> Empleados</h2>
                <a href="{{ route('empleados.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus me-2"></i> Nuevo Empleado
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($empleados->isEmpty())
                <p class="text-muted fs-5 text-center">No hay empleados registrados.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Cargo</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($empleados as $empleado)
                                <tr>
                                    <td>{{ $empleado->nombre }}</td>
                                    <td>{{ $empleado->cargo ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('empleados.edit', $empleado) }}" class="btn btn-outline-primary btn-sm me-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('empleados.destroy', $empleado) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este empleado?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }
</style>
@endpush