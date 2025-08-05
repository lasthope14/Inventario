@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h1 class="h3 mb-0">Detalles de la Categoría</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h5 class="card-title">{{ $categoria->nombre }}</h5>
                    <p class="card-text"><strong>Prefijo:</strong> {{ $categoria->prefijo }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="card-text"><strong>Creada el:</strong> {{ $categoria->created_at->format('d/m/Y H:i') }}</p>
                    <p class="card-text"><strong>Última actualización:</strong> {{ $categoria->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver a la lista
                </a>
            </div>
        </div>
    </div>
</div>
@endsection