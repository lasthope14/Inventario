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
            </h2>
            <div>
                <a href="{{ route('movimientos.masivo') }}" class="btn btn-success btn-sm me-2">
                    <i class="fas fa-layer-group me-2"></i> Movimientos Masivos
                </a>

            <a href="{{ route('inventarios.index') }}" class="btn btn-light btn-sm" id="volverInventarioBtn">
                <i class="fas fa-arrow-left me-2"></i> Volver a Inventario
            </a>
            </div>
        </div>
        <div class="card-body">
            @if($movimientos->isEmpty())
                <p class="text-muted fs-5">No hay movimientos registrados.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Elemento</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Empleado Origen</th>
                                <th>Empleado Destino</th>
                                <th>Realizado por</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movimientos as $movimiento)
                                <tr>
                                    <td>{{ optional($movimiento->fecha_movimiento)->format('d/m/Y H:i') ?? $movimiento->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ optional($movimiento->inventario)->nombre ?? 'N/A' }}</td>
                                    <td>{{ $movimiento->ubicacion_origen ?? 'N/A' }}</td>
                                    <td>{{ $movimiento->ubicacion_destino ?? 'N/A' }}</td>
                                    <td>{{ optional($movimiento->usuarioOrigen)->nombre ?? 'N/A' }}</td>
                                    <td>{{ optional($movimiento->usuarioDestino)->nombre ?? 'N/A' }}</td>
                                    <td>{{ optional($movimiento->realizadoPor)->name ?? 'Usuario eliminado' }}</td>
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
                    {{ $movimientos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// El sistema de navegación ahora es manejado globalmente por NavigationStateManager
</script>
@endpush
@endsection