@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0 fs-4"><i class="fas fa-truck me-2"></i>Detalles del Proveedor</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h5 class="card-title">{{ $proveedor->nombre }}</h5>
                    <p class="card-text"><strong>Contacto:</strong> {{ $proveedor->contacto }}</p>
                    <p class="card-text"><strong>Teléfono:</strong> {{ $proveedor->telefono }}</p>
                    <p class="card-text"><strong>Email:</strong> {{ $proveedor->email }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="card-text"><strong>Creado el:</strong> {{ $proveedor->created_at ? $proveedor->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                    <p class="card-text"><strong>Última actualización:</strong> {{ $proveedor->updated_at ? $proveedor->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('proveedores.edit', $proveedor->id) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i>Editar
                </a>
                <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Volver a la lista
                </a>
            </div>
        </div>
    </div>
</div>
@endsection