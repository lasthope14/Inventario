@extends('layouts.app')

@section('content')
<div class="container-fluid" data-page="inventarios-categorias">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('inventarios.index') }}">Inventario</a></li>
            <li class="breadcrumb-item active" aria-current="page">Vista por Categorías</li>
        </ol>
    </nav>

    <!-- Header Principal -->
    <div class="categories-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="categories-title">
                    <i class="fas fa-th-large me-3"></i>
                    Vista por Categorías
                </h1>
                <p class="categories-subtitle text-muted">
                    Explora el inventario organizado por categorías con estadísticas detalladas
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="header-actions">
                    <a href="{{ route('inventarios.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-th me-1"></i>
                        Vista Principal
                    </a>
                    <a href="{{ route('inventarios.search') }}" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>
                        Buscar Elementos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Globales -->
    <div class="global-stats mb-4">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-cube"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $statsGlobales->total_elementos ?? 0 }}</div>
                        <div class="stat-label">Total Elementos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon available">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $statsGlobales->disponibles ?? 0 }}</div>
                        <div class="stat-label">Disponibles</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon in-use">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $statsGlobales->en_uso ?? 0 }}</div>
                        <div class="stat-label">En Uso</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon maintenance">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $statsGlobales->en_mantenimiento ?? 0 }}</div>
                        <div class="stat-label">Mantenimiento</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Rápidos -->
    @if(request()->hasAny(['categoria', 'estado']))
    <div class="active-filters mb-4">
        <div class="alert alert-info d-flex align-items-center">
            <i class="fas fa-filter me-2"></i>
            <span class="me-2">Filtros activos:</span>
            
            @if(request('categoria'))
                @php
                    $categoriaFiltrada = $todasCategorias->firstWhere('id', request('categoria'));
                @endphp
                <span class="badge bg-primary me-2">
                    Categoría: {{ $categoriaFiltrada->nombre ?? 'N/A' }}
                </span>
            @endif
            
            @if(request('estado'))
                <span class="badge bg-secondary me-2">
                    Estado: {{ ucfirst(str_replace(['_', ' '], [' ', ' '], request('estado'))) }}
                </span>
            @endif
            
            <a href="{{ route('inventarios.categorias') }}" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-times me-1"></i>Quitar filtros
            </a>
        </div>
    </div>
    @endif

    <!-- Lista de Categorías -->
    <div class="categories-list">
        @forelse($categorias as $categoria)
        <div class="category-item">
            <div class="category-header" data-bs-toggle="collapse" data-bs-target="#categoria-{{ $categoria->id }}" aria-expanded="false">
                <div class="category-info">
                    <div class="category-icon">
                        @if($categoria->icono)
                            <i class="{{ $categoria->icono }}"></i>
                        @else
                            <i class="fas fa-cube"></i>
                        @endif
                    </div>
                    <div class="category-details">
                        <h3 class="category-name">{{ $categoria->nombre }}</h3>
                        @if($categoria->descripcion)
                            <p class="category-description">{{ $categoria->descripcion }}</p>
                        @endif
                        <div class="category-stats">
                            <span class="stat-item">
                                <i class="fas fa-cube me-1"></i>
                                {{ $categoria->total_elementos ?? 0 }} elementos
                            </span>
                            <span class="stat-item">
                                <i class="fas fa-check-circle me-1"></i>
                                {{ $categoria->disponibles ?? 0 }} disponibles
                            </span>
                            <span class="stat-item">
                                <i class="fas fa-play-circle me-1"></i>
                                {{ $categoria->en_uso ?? 0 }} en uso
                            </span>
                            <span class="stat-item">
                                <i class="fas fa-tools me-1"></i>
                                {{ $categoria->en_mantenimiento ?? 0 }} mantenimiento
                            </span>
                        </div>
                    </div>
                </div>
                <div class="category-actions">
                    <a href="{{ route('inventarios.categoria', $categoria->id) }}" 
                       class="btn btn-primary btn-sm"
                       onclick="event.stopPropagation()">
                        <i class="fas fa-eye me-1"></i>
                        Ver Catálogo
                    </a>
                    <div class="collapse-indicator">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>
            
            <div class="collapse" id="categoria-{{ $categoria->id }}">
                <div class="category-content">
                    @if(isset($elementosPorCategoria[$categoria->id]) && $elementosPorCategoria[$categoria->id]->count() > 0)
                        <div class="products-preview">
                            <div class="row">
                                @foreach($elementosPorCategoria[$categoria->id]->take(6) as $elemento)
                                <div class="col-md-4 col-lg-2 mb-3">
                                    <div class="product-preview-card">
                                        <a href="{{ route('inventarios.show', $elemento->id) }}" class="product-preview-link">
                                            <div class="product-preview-image">
                                                @if($elemento->imagen)
                                                    <img src="{{ Storage::url($elemento->imagen) }}" alt="{{ $elemento->nombre }}" class="img-fluid">
                                                @else
                                                    <div class="product-preview-placeholder">
                                                        <i class="fas fa-cube"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="product-preview-content">
                                                <h4 class="product-preview-name">{{ Str::limit($elemento->nombre, 30) }}</h4>
                                                @if($elemento->valor_unitario)
                                                    <p class="product-preview-price">
                                                        ${{ number_format($elemento->valor_unitario, 0, ',', '.') }}
                                                    </p>
                                                @endif
                                                @php
                                                    $disponibles = $elemento->ubicaciones->where('estado', 'disponible')->sum('cantidad');
                                                @endphp
                                                <p class="product-preview-stock">
                                                    {{ $disponibles }} disponibles
                                                </p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            @if($elementosPorCategoria[$categoria->id]->count() > 6)
                            <div class="text-center mt-3">
                                <a href="{{ route('inventarios.categoria', $categoria->id) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-1"></i>
                                    Ver todos los {{ $categoria->total_elementos }} elementos
                                </a>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="empty-category text-center py-4">
                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No hay elementos en esta categoría</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state text-center py-5">
            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
            <h3 class="text-muted">No hay categorías disponibles</h3>
            <p class="text-muted">Contacta al administrador para configurar las categorías.</p>
        </div>
        @endforelse
    </div>
</div>

<style>
/* Estilos para la vista de categorías */
.categories-header {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    padding: 2rem;
    border-radius: 8px;
}

.categories-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.categories-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
}

.global-stats {
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    transition: box-shadow 0.2s;
}

.stat-card:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    background: #6c757d;
    color: white;
    font-size: 1.5rem;
}

.stat-icon.available {
    background: #28a745;
}

.stat-icon.in-use {
    background: #ffc107;
}

.stat-icon.maintenance {
    background: #dc3545;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.active-filters {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 8px;
}

.categories-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.category-item {
    background: white;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    overflow: hidden;
    transition: box-shadow 0.3s;
}

.category-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.category-header {
    padding: 1.5rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background-color 0.2s;
}

.category-header:hover {
    background-color: #f8f9fa;
}

.category-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.category-icon {
    width: 60px;
    height: 60px;
    background: #6c757d;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: white;
    font-size: 1.5rem;
}

.category-details {
    flex: 1;
}

.category-name {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.category-description {
    color: #6c757d;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
}

.category-stats {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.stat-item {
    font-size: 0.85rem;
    color: #6c757d;
    display: flex;
    align-items: center;
}

.category-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.collapse-indicator {
    color: #6c757d;
    font-size: 1.2rem;
    transition: transform 0.3s;
}

.category-header[aria-expanded="true"] .collapse-indicator {
    transform: rotate(180deg);
}

.category-content {
    padding: 0 1.5rem 1.5rem;
    background: #f8f9fa;
}

.products-preview {
    background: white;
    padding: 1rem;
    border-radius: 8px;
}

.product-preview-card {
    background: white;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    overflow: hidden;
    transition: box-shadow 0.2s;
}

.product-preview-card:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.product-preview-link {
    display: block;
    text-decoration: none;
    color: inherit;
}

.product-preview-link:hover {
    text-decoration: none;
    color: inherit;
}

.product-preview-image {
    height: 120px;
    overflow: hidden;
    background: #f8f9fa;
}

.product-preview-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-preview-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #6c757d;
    font-size: 2rem;
}

.product-preview-content {
    padding: 0.75rem;
}

.product-preview-name {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #2c3e50;
    line-height: 1.2;
}

.product-preview-price {
    font-size: 0.9rem;
    font-weight: 600;
    color: #28a745;
    margin-bottom: 0.25rem;
}

.product-preview-stock {
    font-size: 0.8rem;
    color: #6c757d;
    margin: 0;
}

.empty-category {
    background: white;
    border-radius: 8px;
}

.empty-state {
    background: white;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    padding: 3rem;
}

/* Responsive */
@media (max-width: 768px) {
    .categories-header {
        text-align: center;
    }
    
    .header-actions {
        margin-top: 1rem;
        text-align: center;
    }
    
    .category-header {
        flex-direction: column;
        text-align: center;
    }
    
    .category-info {
        flex-direction: column;
        margin-bottom: 1rem;
    }
    
    .category-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .category-stats {
        justify-content: center;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
    }
    
    .stat-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
}
</style>
@endsection