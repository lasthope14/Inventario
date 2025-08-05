@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
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
</div>
@endsection