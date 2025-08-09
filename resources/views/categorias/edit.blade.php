@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h1 class="h3 mb-0">Editar Categoría</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('categorias.update', $categoria) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $categoria->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="prefijo" class="form-label">Prefijo</label>
                        <input type="text" class="form-control @error('prefijo') is-invalid @enderror" id="prefijo" name="prefijo" value="{{ old('prefijo', $categoria->prefijo) }}" required maxlength="3">
                        @error('prefijo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="imagen" class="form-label">Imagen de la Categoría</label>
                        @if($categoria->imagen)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $categoria->imagen) }}" alt="Imagen actual" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                <p class="text-muted small mt-1">Imagen actual</p>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('imagen') is-invalid @enderror" id="imagen" name="imagen" accept="image/*">
                        <small class="form-text text-muted">Formatos permitidos: JPEG, PNG, JPG, GIF, SVG. Tamaño máximo: 2MB. Dejar vacío para mantener la imagen actual.</small>
                        @error('imagen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-1"></i> Actualizar Categoría
                    </button>
                    <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection