@extends('layouts.app')

@section('content')
<div class="container-fluid" data-page="inventarios-categorias">
    <!-- Header Profesional -->
    <div class="header-card">
        <div class="header-main">
            <div class="header-info">
                <div class="header-title-section">
                    <div class="header-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="header-text">
                        <h1 class="header-title">Gestión de Inventario</h1>
                        <div class="header-badges">
                            <span class="header-badge">
                                <i class="fas fa-layer-group me-1"></i>
                                {{ $categorias->count() }} {{ $categorias->count() == 1 ? 'Categoría' : 'Categorías' }}
                            </span>
                            <span class="header-badge">
                                <i class="fas fa-cube me-1"></i>
                                {{ $statsGlobales->total_elementos ?? 0 }} Elementos
                            </span>
                            <span class="header-badge">
                                <i class="fas fa-boxes me-1"></i>
                                {{ $statsGlobales->total_unidades ?? 0 }} Unidades
                            </span>
                        </div>
                        
                        <!-- Indicador de Filtros Activos -->
                        @if((request('categoria') || request('estado')) && !request('search'))
                        <div class="filters-active-indicator mt-2">
                            <div class="alert alert-success d-flex align-items-center mb-0" style="padding: 0.5rem 1rem;">
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
                                
                                <a href="{{ route('inventarios.index') }}" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-times me-1"></i>Quitar filtros
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="header-stats">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $statsGlobales->disponibles ?? 0 }}</div>
                            <div class="stat-label">Disponibles</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $statsGlobales->en_uso ?? 0 }}</div>
                            <div class="stat-label">En Uso</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $statsGlobales->en_mantenimiento ?? 0 }}</div>
                            <div class="stat-label">Mantenimiento</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $statsGlobales->dados_de_baja ?? 0 }}</div>
                            <div class="stat-label">Dados de Baja</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-user-secret"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $statsGlobales->robados ?? 0 }}</div>
                            <div class="stat-label">Robados</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('inventarios.import.form') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-file-import"></i> Importar Inventario
                </a>
                <a href="{{ route('inventarios.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Elemento
                </a>
            </div>
        </div>
    </div>

    <!-- Sistema de Búsqueda y Filtros Avanzado -->
    <div class="advanced-search-container">
        <div class="search-header">
            <div class="search-title">
                <i class="fas fa-search me-2"></i>
                <h5 class="mb-0">Búsqueda y Filtros Inteligentes</h5>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm toggle-filters" id="toggleFilters">
                <i class="fas fa-filter me-1"></i>
                <span class="filter-text">Mostrar Filtros</span>
            </button>
        </div>

        <!-- Búsqueda Principal -->
        <div class="main-search-container">
            <div class="search-input-group">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" class="form-control search-input-modern" 
                           placeholder="Buscar por nombre, código, serie, marca, modelo o categoría..." 
                           autocomplete="off">
                    <div class="search-loading" id="searchLoading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <button type="button" class="btn-clear-search-modern" id="clearSearch" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="search-suggestions" id="searchSuggestions" style="display: none;"></div>
            </div>
        </div>

        <!-- Panel de Filtros Avanzados -->
        <div class="filters-panel" id="filtersPanel" style="display: none;">
            <div class="filters-grid">
                <!-- Filtro por Categoría -->
                <div class="filter-item">
                    <label class="filter-label">
                        <i class="fas fa-layer-group me-1"></i>
                        Categoría
                    </label>
                    <select id="filterCategoria" class="form-select filter-select">
                        <option value="">Todas las categorías</option>
                        @if(isset($todasCategorias))
                            @foreach($todasCategorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Filtro por Elemento (se llena dinámicamente) -->
                <div class="filter-item">
                    <label class="filter-label">
                        <i class="fas fa-cube me-1"></i>
                        Elemento
                    </label>
                    <select id="filterElemento" class="form-select filter-select" disabled>
                        <option value="">Selecciona una categoría primero</option>
                    </select>
                </div>

                <!-- Filtro por Marca (se llena dinámicamente) -->
                <div class="filter-item">
                    <label class="filter-label">
                        <i class="fas fa-tag me-1"></i>
                        Marca
                    </label>
                    <select id="filterMarca" class="form-select filter-select" disabled>
                        <option value="">Selecciona un elemento primero</option>
                    </select>
                </div>

                <!-- Filtro por Estado -->
                <div class="filter-item">
                    <label class="filter-label">
                        <i class="fas fa-info-circle me-1"></i>
                        Estado
                    </label>
                    <select id="filterEstado" class="form-select filter-select">
                        <option value="">Todos los estados</option>
                        <option value="disponible">Disponible</option>
                        <option value="en uso">En Uso</option>
                        <option value="en mantenimiento">En Mantenimiento</option>
                        <option value="dado de baja">Dado de Baja</option>
                        <option value="robado">Robado</option>
                    </select>
                </div>

                <!-- Filtro por Ubicación -->
                <div class="filter-item">
                    <label class="filter-label">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        Ubicación
                    </label>
                    <select id="filterUbicacion" class="form-select filter-select">
                        <option value="">Todas las ubicaciones</option>
                        <!-- Se llena dinámicamente -->
                    </select>
                </div>

                <!-- Acciones de Filtros -->
                <div class="filter-actions">
                    <button type="button" class="btn btn-primary btn-sm" id="applyFilters">
                        <i class="fas fa-search me-1"></i>Aplicar Filtros
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="clearFilters">
                        <i class="fas fa-eraser me-1"></i>Limpiar Todo
                    </button>
                </div>
            </div>
        </div>

        <!-- Indicadores de Filtros Activos -->
        <div class="active-filters" id="activeFilters" style="display: none;">
            <div class="active-filters-header">
                <span class="active-filters-title">
                    <i class="fas fa-filter me-1"></i>Filtros activos:
                </span>
            </div>
            <div class="active-filters-list" id="activeFiltersList"></div>
        </div>
    </div>

    <!-- Resultados de Búsqueda -->
    <div class="search-results-section mb-4" id="instantSearchResults" style="display: none;">
        <div class="search-results-header">
            <div class="search-results-info">
                <h4 class="search-results-title">
                    <i class="fas fa-search me-2"></i>
                    Resultados de Búsqueda 
                    <span id="searchTermDisplay"></span>
                </h4>
                <span class="badge bg-primary ms-2" id="resultsCount">0 elementos</span>
            </div>
            
            <div class="search-results-controls">
                <div class="view-toggle-buttons-small">
                    <button type="button" class="view-toggle-btn-small active" data-view="grid" id="searchGridToggle">
                        <i class="fas fa-th"></i> Grid
                    </button>
                    <button type="button" class="view-toggle-btn-small" data-view="table" id="searchTableToggle">
                        <i class="fas fa-list"></i> Tabla
                    </button>
                </div>
                
                <button type="button" class="btn btn-outline-secondary btn-sm" id="clearSearchResults">
                    <i class="fas fa-broom me-1"></i>Limpiar
                </button>
            </div>
        </div>
        
        <!-- Vista Grid (usando el diseño existente) -->
        <div class="elements-grid-view" id="instantSearchGrid">
            <div class="elements-grid-compact">
                <!-- Los resultados se cargan aquí dinámicamente -->
            </div>
        </div>
        
        <!-- Vista Tabla (usando el diseño existente) -->
        <div class="search-results-table" id="instantSearchTable" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60">Imagen</th>
                            <th>Elemento</th>
                            <th>Código</th>
                            <th>Marca/Modelo</th>
                            <th>Serie</th>
                            <th>Propietario</th>
                            <th>Estados y Ubicaciones</th>
                            <th width="120" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="instantSearchTableBody">
                        <!-- Los resultados se cargan aquí dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Resultados de Búsqueda Tradicional -->
    @if(isset($inventarios) && $inventarios->count() > 0)
    <div class="search-results-section mb-4">
        <div class="search-results-header">
            <div class="search-results-info">
                <h4 class="search-results-title">
                    <i class="fas fa-search me-2"></i>
                    Resultados de Búsqueda 
                    @if(isset($searchTerm) && !empty($searchTerm))
                        para "{{ $searchTerm }}"
                    @endif
                </h4>
                <span class="badge bg-primary ms-2">{{ $inventarios->total() }} elementos</span>
            </div>
        </div>
        
        <div class="search-results-grid">
            @foreach($inventarios as $inventario)
            <div class="inventory-card">
                <div class="inventory-header">
                    <div class="inventory-icon">
                        <i class="fas fa-cube"></i>
                    </div>
                    <div class="inventory-info">
                        <h5 class="inventory-name">{{ $inventario->nombre }}</h5>
                        <p class="inventory-code">{{ $inventario->codigo_unico }}</p>
                        <span class="category-badge">{{ $inventario->categoria->nombre }}</span>
                    </div>
                </div>
                
                <div class="inventory-details">
                    <div class="detail-item">
                        <span class="detail-label">Marca:</span>
                        <span class="detail-value">{{ $inventario->marca ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Modelo:</span>
                        <span class="detail-value">{{ $inventario->modelo ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Serie:</span>
                        <span class="detail-value">{{ $inventario->numero_serie ?? 'N/A' }}</span>
                    </div>
                </div>
                
                <div class="inventory-status">
                    @foreach($inventario->ubicaciones as $ubicacion)
                        <div class="status-badge status-{{ str_replace(' ', '-', $ubicacion->estado) }}">
                            {{ $ubicacion->cantidad }} {{ ucfirst(str_replace('_', ' ', $ubicacion->estado)) }}
                        </div>
                    @endforeach
                </div>
                
                <div class="inventory-actions">
                    <a href="{{ route('inventarios.show', $inventario) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye"></i> Ver Detalles
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $inventarios->links() }}
        </div>
    </div>
    @endif

    <!-- Acordeones de Categorías con Elementos -->
    @if(!isset($inventarios) || $inventarios->count() == 0 || (isset($categorias) && $categorias->count() > 0))
    <div class="accordion" id="categoriesAccordion">
        @foreach($categorias as $categoria)
        <div class="accordion-item category-accordion-item" data-category-id="{{ $categoria->id }}">
            <!-- Header de la categoría -->
            <h2 class="accordion-header" id="heading-{{ $categoria->id }}">
                <button class="accordion-button collapsed category-accordion-button" type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#collapse-{{ $categoria->id }}" 
                        aria-expanded="false" 
                        aria-controls="collapse-{{ $categoria->id }}">
                    <div class="category-accordion-header">
                        <div class="category-info-accordion">
                            <div class="category-icon-accordion">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div class="category-details-accordion">
                                <h5 class="category-name-accordion">{{ $categoria->nombre }}</h5>
                                <div class="category-summary">
                                    <span class="summary-item">{{ $categoria->total_elementos }} elementos</span>
                                    <span class="summary-item">{{ $categoria->total_unidades }} unidades</span>
                                </div>
                                <div class="category-actions mt-2">
                                    <a href="{{ route('inventarios.categoria', $categoria->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       onclick="event.stopPropagation();"
                                       title="Ver catálogo completo de {{ $categoria->nombre }}">
                                        <i class="fas fa-external-link-alt me-1"></i>
                                        Ver Catálogo Completo
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="category-stats-compact">
                            @if($categoria->disponibles > 0)
                                <span class="stat-badge-compact stat-disponible">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $categoria->disponibles }} Disponibles
                                </span>
                            @endif
                            @if($categoria->en_uso > 0)
                                <span class="stat-badge-compact stat-en-uso">
                                    <i class="fas fa-play-circle me-1"></i>
                                    {{ $categoria->en_uso }} En Uso
                                </span>
                            @endif
                            @if($categoria->en_mantenimiento > 0)
                                <span class="stat-badge-compact stat-mantenimiento">
                                    <i class="fas fa-tools me-1"></i>
                                    {{ $categoria->en_mantenimiento }} Mantenimiento
                                </span>
                            @endif
                            @if($categoria->dados_de_baja > 0)
                                <span class="stat-badge-compact stat-baja">
                                    <i class="fas fa-ban me-1"></i>
                                    {{ $categoria->dados_de_baja }} Dados de Baja
                                </span>
                            @endif
                            @if($categoria->robados > 0)
                                <span class="stat-badge-compact stat-robado">
                                    <i class="fas fa-user-secret me-1"></i>
                                    {{ $categoria->robados }} Robados
                                </span>
                            @endif
                        </div>
                    </div>
                </button>
            </h2>
            
            <!-- Contenido de la categoría (elementos) -->
            <div id="collapse-{{ $categoria->id }}" class="accordion-collapse collapse" 
                 aria-labelledby="heading-{{ $categoria->id }}" 
                 data-bs-parent="#categoriesAccordion">
                <div class="accordion-body category-accordion-body">
                    @if(isset($elementosPorCategoria[$categoria->id]) && $elementosPorCategoria[$categoria->id]->count() > 0)
                        
                        <!-- Toggle de vista para esta categoría -->
                        <div class="category-view-toggle">
                            <div class="category-elements-count">
                                <span class="elements-count-text">{{ $elementosPorCategoria[$categoria->id]->count() }} elementos</span>
                            </div>
                            <div class="view-toggle-buttons-small">
                                <button type="button" class="view-toggle-btn-small active" data-view="grid" data-category="{{ $categoria->id }}">
                                    <i class="fas fa-th"></i> Grid
                                </button>
                                <button type="button" class="view-toggle-btn-small" data-view="table" data-category="{{ $categoria->id }}">
                                    <i class="fas fa-list"></i> Tabla
                                </button>
                            </div>
                        </div>
                        
                        <!-- Vista Grid de elementos -->
                        <div class="elements-grid-view" id="elements-grid-{{ $categoria->id }}">
                            <div class="elements-grid-compact">
                                @foreach($elementosPorCategoria[$categoria->id] as $inventario)
                                <div class="element-card-compact">
                                    <div class="element-image-compact">
                                        @if($inventario->imagen_principal)
                                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Crect width='100' height='100' fill='%23f3f4f6'/%3E%3C/svg%3E" 
                                                 data-src="{{ Storage::url($inventario->imagen_principal) }}"
                                                 alt="{{ $inventario->nombre }}" class="element-thumbnail-compact lazy-load">
                                        @else
                                            <div class="image-placeholder-compact">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="element-content-compact">
                                        <div class="element-header-compact">
                                            <h6 class="element-name-compact">{{ $inventario->nombre }}</h6>
                                            <code class="element-code-compact">{{ $inventario->codigo_unico }}</code>
                                        </div>
                                        
                                        <div class="element-details-compact">
                                            <div class="detail-row-compact">
                                                <span class="detail-label-compact">Marca/Modelo:</span>
                                                <span class="detail-value-compact">
                                                    {{ $inventario->marca }}{{ $inventario->marca && $inventario->modelo ? ' - ' : '' }}{{ $inventario->modelo ?: 'N/A' }}
                                                </span>
                                            </div>
                                            <div class="detail-row-compact">
                                                <span class="detail-label-compact">Serie:</span>
                                                <span class="detail-value-compact">{{ $inventario->numero_serie ?: 'N/A' }}</span>
                                            </div>
                                            <div class="detail-row-compact">
                                <span class="detail-label-compact">Propietario:</span>
                                <span class="detail-value-compact">{{ $inventario->propietario ?? 'HIDROOBRAS' }}</span>
                            </div>
                                        </div>
                                        
                                        <div class="element-locations-compact">
                                            @foreach($inventario->ubicaciones->take(3) as $ubicacion)
                                                <div class="location-compact">
                                                    <div class="location-info-compact">
                                                        <span class="location-name-compact">{{ $ubicacion->ubicacion ? $ubicacion->ubicacion->nombre : 'Sin Ubicación' }}</span>
                                                        <span class="location-quantity-compact">{{ $ubicacion->cantidad }} unidades</span>
                                                    </div>
                                                    <div class="status-badge-element-new status-{{ str_replace(' ', '-', strtolower($ubicacion->estado)) }}">
                                                        <i class="status-icon-compact 
                                                            @if($ubicacion->estado == 'disponible') fas fa-check-circle
                                                            @elseif($ubicacion->estado == 'en uso') fas fa-play-circle
                                                            @elseif($ubicacion->estado == 'en mantenimiento') fas fa-tools
                                                            @elseif($ubicacion->estado == 'dado de baja') fas fa-ban
                                                            @elseif($ubicacion->estado == 'robado') fas fa-user-secret
                                                            @endif"></i>
                                                        <span class="status-text-compact">{{ ucfirst(str_replace('_', ' ', $ubicacion->estado)) }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if($inventario->ubicaciones->count() > 3)
                                                <div class="more-locations-compact">
                                                    <i class="fas fa-plus-circle me-1"></i>
                                                    {{ $inventario->ubicaciones->count() - 3 }} ubicaciones más
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="element-actions-compact">
                                            <a href="{{ route('inventarios.show', $inventario) }}" class="btn btn-outline-primary btn-xs" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('update', $inventario)
                                                <a href="{{ route('inventarios.edit', $inventario) }}" class="btn btn-outline-secondary btn-xs" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $inventario)
                                                <form action="{{ route('inventarios.destroy', $inventario) }}" 
                                                      method="POST" style="display: inline-block;" 
                                                      onsubmit="return confirm('¿Eliminar este elemento?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-xs" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Vista Tabla de elementos -->
                        <div class="elements-table-view" id="elements-table-{{ $categoria->id }}" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="60">Imagen</th>
                                            <th>Elemento</th>
                                            <th>Código</th>
                                            <th>Marca/Modelo</th>
                                            <th>Serie</th>
                                            <th>Propietario</th>
                                            <th>Estados y Ubicaciones</th>
                                            <th width="120" class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($elementosPorCategoria[$categoria->id] as $inventario)
                                        <tr>
                                            <td>
                                                <div class="table-image-compact">
                                                    @if($inventario->imagen_principal)
                                                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Crect width='60' height='60' fill='%23f3f4f6'/%3E%3C/svg%3E" 
                                                             data-src="{{ Storage::url($inventario->imagen_principal) }}"
                                                             alt="{{ $inventario->nombre }}" class="table-thumbnail-compact lazy-load">
                                                    @else
                                                        <div class="table-image-placeholder-compact">
                                                            <i class="fas fa-image"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="element-name-table">{{ $inventario->nombre }}</div>
                                            </td>
                                            <td>
                                                <code class="table-code-compact">{{ $inventario->codigo_unico }}</code>
                                            </td>
                                            <td>
                                                <div class="table-brand-model">
                                                    @if($inventario->marca || $inventario->modelo)
                                                        {{ $inventario->marca }}{{ $inventario->marca && $inventario->modelo ? ' - ' : '' }}{{ $inventario->modelo }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-serial">
                                                    {{ $inventario->numero_serie ?: 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                <div class="table-owner">
                                    {{ $inventario->propietario ?? 'HIDROOBRAS' }}
                                </div>
                            </td>
                                            <td>
                                                <div class="table-locations-detailed">
                                                    @foreach($inventario->ubicaciones->take(2) as $ubicacion)
                                                        <div class="table-location-detailed">
                                                            <div class="location-info-table">
                                                                <span class="location-name-table">{{ $ubicacion->ubicacion ? $ubicacion->ubicacion->nombre : 'Sin Ubicación' }}</span>
                                                            </div>
                                                            <div class="status-badge-table status-{{ str_replace(' ', '-', strtolower($ubicacion->estado)) }}">
                                                                <span class="location-quantity-table">{{ $ubicacion->cantidad }}</span>
                                                                <i class="status-icon-table 
                                                                    @if($ubicacion->estado == 'disponible') fas fa-check-circle
                                                                    @elseif($ubicacion->estado == 'en uso') fas fa-play-circle
                                                                    @elseif($ubicacion->estado == 'en mantenimiento') fas fa-tools
                                                                    @elseif($ubicacion->estado == 'dado de baja') fas fa-ban
                                                                    @elseif($ubicacion->estado == 'robado') fas fa-user-secret
                                                                    @endif"></i>
                                                                {{ ucfirst(str_replace('_', ' ', $ubicacion->estado)) }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    @if($inventario->ubicaciones->count() > 2)
                                                        <small class="text-muted more-locations-table">
                                                            <i class="fas fa-plus-circle me-1"></i>
                                                            +{{ $inventario->ubicaciones->count() - 2 }} más
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="table-actions-compact">
                                                    <a href="{{ route('inventarios.show', $inventario) }}" class="btn btn-outline-primary btn-xs" title="Ver Detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @can('update', $inventario)
                                                        <a href="{{ route('inventarios.edit', $inventario) }}" class="btn btn-outline-secondary btn-xs" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $inventario)
                                                        <form action="{{ route('inventarios.destroy', $inventario) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Eliminar este elemento?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-xs" title="Eliminar">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                    @else
                        <div class="empty-category">
                            <i class="fas fa-inbox text-muted"></i>
                            <p class="text-muted mb-0">No hay elementos en esta categoría</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if($categorias->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-inbox"></i>
        </div>
        <h3>No hay categorías con elementos</h3>
        <p>Comienza agregando elementos al inventario para ver las categorías aquí.</p>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
/* Estilos Base */
body {
    background-color: #f8f9fa;
}

/* Header Profesional */
.header-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    overflow: hidden;
    margin-bottom: 2rem;
}

.header-main {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 2rem;
    gap: 2rem;
}

.header-info {
    flex: 1;
}

.header-title-section {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
}

.header-icon {
    width: 60px;
    height: 60px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.header-text {
    flex: 1;
}

.header-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.75rem 0;
    line-height: 1.2;
}

.header-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.header-badge {
    background: #f1f5f9;
    color: #475569;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    border: 1px solid #e2e8f0;
}

.header-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.stat-item:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 1rem;
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.75rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 500;
}

.header-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    flex-shrink: 0;
}

.header-actions .btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    min-width: 180px;
    justify-content: center;
}

.header-actions .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}

.header-actions .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px 0 rgba(102, 126, 234, 0.4);
}

.header-actions .btn-outline-secondary {
    background: white;
    border: 2px solid #e2e8f0;
    color: #64748b;
}

.header-actions .btn-outline-secondary:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #475569;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.1);
}

.header-actions .btn i {
    font-size: 1rem;
}

/* Sistema de Búsqueda y Filtros Avanzado */
.advanced-search-container {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    margin-bottom: 2rem;
}

.search-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: transparent;
    color: #1e293b;
}

.search-title {
    display: flex;
    align-items: center;
}

.search-title h5 {
    color: #1e293b;
    font-weight: 600;
}

.toggle-filters {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
    transition: all 0.3s ease;
}

.toggle-filters:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #475569;
}

/* Búsqueda Principal */
.main-search-container {
    padding: 2rem;
    background: #f8fafc;
}

.search-input-group {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-input-modern {
    height: 56px;
    font-size: 1.125rem;
    border-radius: 12px;
    padding-left: 56px;
    padding-right: 100px;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
    background: white;
    width: 100%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.search-input-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.1);
    outline: none;
}

.search-icon {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 1.25rem;
    z-index: 2;
}

.search-loading {
    position: absolute;
    right: 50px;
    top: 50%;
    transform: translateY(-50%);
    color: #667eea;
    z-index: 2;
}

.btn-clear-search-modern {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: #f1f5f9;
    border: none;
    color: #64748b;
    padding: 8px;
    border-radius: 8px;
    transition: all 0.3s ease;
    z-index: 2;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-clear-search-modern:hover {
    background: #e2e8f0;
    color: #dc2626;
}

.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0 0 12px 12px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
}

/* Panel de Filtros */
.filters-panel {
    padding: 2rem;
    background: white;
    border-top: 1px solid #e2e8f0;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    align-items: end;
}

.filter-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-label {
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
}

.filter-select {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: white;
    height: 44px;
    font-size: 0.875rem;
}

.filter-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.125rem rgba(102, 126, 234, 0.1);
}

.filter-select:disabled {
    background: #f8fafc;
    color: #9ca3af;
    cursor: not-allowed;
}

.filter-actions {
    display: flex;
    gap: 0.75rem;
    align-items: end;
    flex-wrap: wrap;
}

/* Filtros Activos */
.active-filters {
    padding: 1.5rem 2rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

.active-filters-header {
    margin-bottom: 1rem;
}

.active-filters-title {
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
}

.active-filters-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.active-filter-tag {
    background: #667eea;
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8125rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.active-filter-tag .remove-filter {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.active-filter-tag .remove-filter:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Resultados de Búsqueda Instantánea */
.search-results-section {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    margin-bottom: 2rem;
}

.search-results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 1.25rem 1.5rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}

.search-results-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.search-results-title {
    color: #1e293b;
    font-weight: 600;
    margin: 0;
    font-size: 1.25rem;
}

.search-results-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.view-toggle-buttons-small {
    display: flex;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.search-results-grid {
    margin-bottom: 2rem;
}

.search-results-table {
    margin-bottom: 2rem;
}

/* Los estilos de resultados de búsqueda ahora usan las clases existentes del diseño */

.location-compact {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.8125rem;
}

.location-info-compact {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.location-name-compact {
    font-weight: 500;
    color: #374151;
}

.location-quantity-compact {
    font-size: 0.75rem;
    color: #6b7280;
}

.status-badge-element-new {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-icon-compact {
    font-size: 0.75rem;
}

.status-text-compact {
    white-space: nowrap;
}

.element-actions-compact {
    margin-top: auto;
    text-align: center;
}

.btn-xs {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
    border-radius: 6px;
}

/* Estilos para tabla de resultados */
.table-element-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.table-element-name {
    font-size: 0.875rem;
    color: #1e293b;
    margin: 0;
}

.table-element-code {
    font-size: 0.75rem;
    background: #f1f5f9;
    color: #64748b;
    padding: 0.125rem 0.375rem;
    border-radius: 4px;
    align-self: flex-start;
}

.table-category-badge {
    background: #e0e7ff;
    color: #3730a3;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.table-brand-model {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.table-brand {
    font-size: 0.875rem;
    color: #374151;
    font-weight: 500;
}

.table-model {
    font-size: 0.75rem;
    color: #6b7280;
}

.table-serial {
    font-size: 0.75rem;
    background: #f3f4f6;
    color: #374151;
    padding: 0.125rem 0.375rem;
    border-radius: 4px;
}

.table-locations {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.table-location-item {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 0.5rem;
    background: #f9fafb;
}

.table-location-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.table-location-name {
    font-size: 0.75rem;
    font-weight: 500;
    color: #374151;
}

.table-status-badge {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    align-self: flex-start;
}

.table-quantity {
    font-weight: 600;
}

.table-status-icon {
    font-size: 0.75rem;
}

.table-status-text {
    white-space: nowrap;
}

/* Estilo general para form-label */
.form-label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

/* Resultados de Búsqueda */
.search-results-section {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.search-results-header h4 {
    color: #1e293b;
    font-weight: 600;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.search-results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 1.5rem;
}

.inventory-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.5rem;
    transition: all 0.2s ease;
}

.inventory-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-color: #cbd5e1;
}

.inventory-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.inventory-icon {
    width: 40px;
    height: 40px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 1rem;
    flex-shrink: 0;
}

.inventory-info {
    flex: 1;
}

.inventory-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 0.25rem 0;
    line-height: 1.2;
}

.inventory-code {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0 0 0.5rem 0;
    font-family: monospace;
}

.category-badge {
    background: #e0f2fe;
    color: #0277bd;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.inventory-details {
    margin-bottom: 1rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
}

.detail-label {
    color: #64748b;
    font-weight: 500;
}

.detail-value {
    color: #1e293b;
    text-align: right;
}

.inventory-status {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.status-disponible {
    background: #d4edda;
    color: #155724;
}

.status-badge.status-en-uso {
    background: #cce7ff;
    color: #0056b3;
}

.status-badge.status-en-mantenimiento {
    background: #fff3cd;
    color: #856404;
}

.status-badge.status-dado-de-baja {
    background: #f8d7da;
    color: #721c24;
}

.status-badge.status-robado {
    background: #f5c6cb;
    color: #721c24;
}

.inventory-actions {
    text-align: center;
}

/* Grid de Categorías */
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.category-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    transition: all 0.2s ease;
}

.category-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-color: #cbd5e1;
}

.category-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.category-icon {
    width: 50px;
    height: 50px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.category-info {
    flex: 1;
}

.category-name {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
}

.category-description {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0;
    line-height: 1.4;
}

.category-stats {
    margin-bottom: 1.5rem;
}

.stat-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.stat-item-small {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}

.stat-label-small {
    font-size: 0.75rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 500;
}

.stat-number-small {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1e293b;
}

.status-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
}

.status-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    padding: 0.75rem 0.5rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
}

.status-item > i {
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.status-item > span {
    font-size: 1.125rem;
    font-weight: 700;
    line-height: 1;
}

.status-label {
    font-size: 0.65rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    opacity: 0.8;
    line-height: 1;
}

.status-disponible {
    background: #d4edda;
    color: #155724;
}

.status-en-uso {
    background: #cce7ff;
    color: #0056b3;
}

.status-mantenimiento {
    background: #fff3cd;
    color: #856404;
}

.status-baja {
    background: #f8d7da;
    color: #721c24;
}

.status-robado {
    background: #e2e3e5;
    color: #383d41;
}

.category-actions {
    text-align: center;
}

/* Estado Vacío */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #64748b;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    margin-bottom: 1rem;
    color: #1e293b;
}

/* Responsive */
@media (max-width: 768px) {
    .header-main {
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .header-actions {
        flex-direction: row;
        align-self: stretch;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
    }
    
    .search-results-grid {
        grid-template-columns: 1fr;
    }
    
    .search-input-wrapper {
        max-width: 100%;
    }
    
    .header-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filters-row {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .filter-group {
        min-width: auto;
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    
    .filter-label {
        align-self: flex-start;
    }
    
    .actions-group {
        justify-content: center;
    }
}

/* Toggle de Vista */
.view-toggle-section {
    border-top: 1px solid #e2e8f0;
    padding: 1rem 2rem;
    background: #f8fafc;
}

.view-toggle-wrapper {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.view-toggle-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
}

.view-toggle-buttons {
    display: flex;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
}

.view-toggle-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: transparent;
    border: none;
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border-right: 1px solid #e2e8f0;
}

.view-toggle-btn:last-child {
    border-right: none;
}

.view-toggle-btn:hover {
    background: #f1f5f9;
    color: #475569;
}

.view-toggle-btn.active {
    background: #3b82f6;
    color: white;
}

.view-toggle-btn.active:hover {
    background: #2563eb;
}

/* Vista de Tabla */
.categories-table-view {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.categories-table-view .table {
    margin-bottom: 0;
}

.categories-table-view .table th {
    border-bottom: 2px solid #e2e8f0;
    font-weight: 600;
    color: #374151;
    padding: 1rem 0.75rem;
}

.categories-table-view .table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}

.categories-table-view .table tbody tr:hover {
    background-color: #f8fafc;
}

.category-icon-small {
    width: 32px;
    height: 32px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 0.875rem;
}

/* Acordeones de Categorías - Diseño Profesional */
.category-accordion-item {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    margin-bottom: 0.75rem;
    overflow: hidden;
    background: white;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.category-accordion-button {
    background: white;
    border: none;
    padding: 1.25rem 1.5rem;
    width: 100%;
    text-align: left;
    font-weight: 500;
    color: #374151;
    box-shadow: none;
    transition: background-color 0.15s ease;
}

.category-accordion-button:hover {
    background: #f9fafb;
}

.category-accordion-button:not(.collapsed) {
    background: #f3f4f6;
    border-bottom: 1px solid #e5e7eb;
}

.category-accordion-button:focus {
    box-shadow: none;
    border: none;
    outline: none;
}

.category-accordion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.category-info-accordion {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.category-icon-accordion {
    width: 36px;
    height: 36px;
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 0.875rem;
}

.category-actions {
    margin-top: 0.5rem;
}

.category-actions .btn {
    font-size: 0.8125rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.category-actions .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.category-details-accordion {
    flex: 1;
}

.category-name-accordion {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin: 0 0 0.25rem 0;
}

.category-summary {
    display: flex;
    gap: 1rem;
    font-size: 0.8125rem;
    color: #6b7280;
}

.summary-item {
    color: #6b7280;
    font-weight: 500;
}

.category-stats-compact {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    align-items: center;
}

.stat-badge-compact {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    border: 1px solid;
    background: white;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
}

.stat-badge-compact.stat-disponible { 
    color: #059669; 
    border-color: #d1fae5;
    background: #ecfdf5;
}

.stat-badge-compact.stat-en-uso { 
    color: #1d4ed8; 
    border-color: #dbeafe;
    background: #eff6ff;
}

.stat-badge-compact.stat-mantenimiento { 
    color: #d97706; 
    border-color: #fed7aa;
    background: #fffbeb;
}

.stat-badge-compact.stat-baja { 
    color: #dc2626; 
    border-color: #fecaca;
    background: #fef2f2;
}

.stat-badge-compact.stat-robado { 
    color: #4b5563; 
    border-color: #e5e7eb;
    background: #f9fafb;
}

.category-accordion-body {
    padding: 1.5rem;
    background: #fafbfc;
    border-top: 1px solid #f3f4f6;
}

/* Toggle de Vista por Categoría - Profesional */
.category-view-toggle {
    margin-bottom: 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.category-elements-count {
    flex: 1;
}

.elements-count-text {
    font-size: 0.8125rem;
    color: #6b7280;
    font-weight: 500;
}

.view-toggle-buttons-small {
    display: inline-flex;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.view-toggle-btn-small {
    padding: 0.5rem 0.875rem;
    background: transparent;
    border: none;
    color: #6b7280;
    font-size: 0.8125rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    border-right: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.view-toggle-btn-small:last-child {
    border-right: none;
}

.view-toggle-btn-small:hover {
    background: #f3f4f6;
    color: #374151;
}

.view-toggle-btn-small.active {
    background: white;
    color: #111827;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* Grid de Elementos - Diseño Profesional Mejorado */
.elements-grid-compact {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 1.5rem;
}

.element-card-compact {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.element-card-compact:hover {
    border-color: #d1d5db;
    box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transform: translateY(-2px);
}

.element-image-compact {
    height: 160px;
    overflow: hidden;
    background: #f9fafb;
    position: relative;
    flex-shrink: 0;
}

.element-thumbnail-compact {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.3s ease;
}

.element-thumbnail-compact.lazy-load {
    opacity: 0.7;
    filter: blur(2px);
}

.element-thumbnail-compact.loaded {
    opacity: 1;
    filter: none;
}

.element-thumbnail-compact.error {
    opacity: 0.5;
}

.image-placeholder-compact {
    width: 100%;
    height: 100%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 2rem;
}

.element-content-compact {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.element-header-compact {
    margin-bottom: 1rem;
    border-bottom: 1px solid #f3f4f6;
    padding-bottom: 1rem;
}

.element-name-compact {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin: 0 0 0.75rem 0;
    line-height: 1.3;
}

.element-code-compact {
    background: #f3f4f6;
    color: #6b7280;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8125rem;
    font-weight: 500;
    font-family: 'Courier New', monospace;
    display: inline-block;
}

.element-details-compact {
    margin-bottom: 1.25rem;
    flex: 1;
}

.detail-row-compact {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    gap: 1rem;
}

.detail-label-compact {
    color: #6b7280;
    font-weight: 500;
    flex-shrink: 0;
    min-width: 90px;
}

.detail-value-compact {
    color: #374151;
    font-weight: 500;
    text-align: right;
    word-break: break-word;
}

.element-locations-compact {
    margin-bottom: 1.25rem;
}

.location-compact {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 0.875rem;
    background: #fafbfc;
    border: 1px solid #f3f4f6;
    border-radius: 8px;
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
}

.location-info-compact {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.location-name-compact {
    color: #374151;
    font-weight: 600;
    flex: 1;
}

.location-quantity-compact {
    color: #6b7280;
    font-size: 0.8125rem;
    font-weight: 500;
}

.status-badge-element-new {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8125rem;
    font-weight: 500;
    border: 1px solid;
}

.status-badge-element-new.status-disponible { 
    color: #059669; 
    border-color: #d1fae5;
    background: #ecfdf5;
}

.status-badge-element-new.status-en-uso { 
    color: #1d4ed8; 
    border-color: #dbeafe;
    background: #eff6ff;
}

.status-badge-element-new.status-en-mantenimiento { 
    color: #d97706; 
    border-color: #fed7aa;
    background: #fffbeb;
}

.status-badge-element-new.status-dado-de-baja { 
    color: #dc2626; 
    border-color: #fecaca;
    background: #fef2f2;
}

.status-badge-element-new.status-robado { 
    color: #4b5563; 
    border-color: #e5e7eb;
    background: #f9fafb;
}

.status-icon-compact {
    font-size: 0.875rem;
    flex-shrink: 0;
}

.status-text-compact {
    font-weight: 600;
}

.more-locations-compact {
    color: #6b7280;
}

.more-locations-compact.clickeable {
    color: #3b82f6;
    cursor: pointer;
    font-weight: 500;
    transition: color 0.2s ease;
}

.more-locations-compact.clickeable:hover {
    color: #2563eb;
    text-decoration: underline;
}

.more-locations-table.clickeable {
    color: #3b82f6 !important;
    cursor: pointer;
    font-weight: 500;
    transition: color 0.2s ease;
}

.more-locations-table.clickeable:hover {
    color: #2563eb !important;
    text-decoration: underline;
}
    font-size: 0.8125rem;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    padding: 0.5rem;
    background: #f9fafb;
    border: 1px dashed #e5e7eb;
    border-radius: 6px;
    font-weight: 500;
}

.element-actions-compact {
    display: flex;
    gap: 0.5rem;
    padding-top: 1rem;
    border-top: 1px solid #f3f4f6;
    margin-top: auto;
    justify-content: center;
}

.btn-xs {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    line-height: 1.2;
    border-radius: 4px;
    font-weight: 500;
    transition: all 0.15s ease;
}

/* Tabla Profesional Mejorada */
.elements-table-view .table {
    margin-bottom: 0;
    font-size: 0.875rem;
}

.elements-table-view .table thead th {
    background: #f9fafb;
    border-bottom: 2px solid #e5e7eb;
    color: #374151;
    font-weight: 600;
    font-size: 0.8125rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    padding: 0.875rem 0.75rem;
    border-top: none;
    white-space: nowrap;
    vertical-align: middle;
}

.elements-table-view .table tbody td {
    padding: 0.875rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
}

.elements-table-view .table tbody tr {
    transition: background-color 0.15s ease;
}

.elements-table-view .table tbody tr:hover {
    background-color: #f9fafb;
}

.table-image-compact {
    width: 56px;
    height: 56px;
    overflow: hidden;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    flex-shrink: 0;
}

.table-thumbnail-compact {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.3s ease;
}

.table-thumbnail-compact.lazy-load {
    opacity: 0.7;
    filter: blur(1px);
}

.table-thumbnail-compact.loaded {
    opacity: 1;
    filter: none;
}

.table-thumbnail-compact.error {
    opacity: 0.5;
}

.table-image-placeholder-compact {
    width: 100%;
    height: 100%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 1.25rem;
}

.element-name-table {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #111827;
    line-height: 1.3;
    margin-bottom: 0.25rem;
}

.table-code-compact {
    background: #f3f4f6;
    color: #6b7280;
    padding: 0.375rem 0.625rem;
    border-radius: 6px;
    font-size: 0.8125rem;
    font-weight: 500;
    font-family: 'Courier New', monospace;
    display: inline-block;
}

.table-brand-model {
    font-size: 0.875rem;
    line-height: 1.4;
    color: #4b5563;
    font-weight: 500;
}

.table-serial {
    font-size: 0.875rem;
    color: #4b5563;
    font-weight: 500;
}

.table-owner {
    font-size: 0.875rem;
    color: #4b5563;
    font-weight: 500;
}

.table-locations-detailed {
    min-width: 250px;
    max-width: 300px;
}

.table-location-detailed {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: #fafbfc;
    border: 1px solid #f3f4f6;
    border-radius: 6px;
    font-size: 0.8125rem;
}

.location-info-table {
    flex: 1;
    min-width: 0;
}

.location-name-table {
    color: #374151;
    font-weight: 600;
    font-size: 0.8125rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    line-height: 1.2;
}

.location-quantity-table {
    color: inherit;
    font-size: 0.6875rem;
    font-weight: 700;
    margin-right: 0.25rem;
}

.status-badge-table {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.6875rem;
    font-weight: 600;
    border: 1px solid;
    flex-shrink: 0;
    white-space: nowrap;
}

.status-badge-table.status-disponible { 
    color: #059669; 
    border-color: #d1fae5;
    background: #ecfdf5;
}

.status-badge-table.status-en-uso { 
    color: #1d4ed8; 
    border-color: #dbeafe;
    background: #eff6ff;
}

.status-badge-table.status-en-mantenimiento { 
    color: #d97706; 
    border-color: #fed7aa;
    background: #fffbeb;
}

.status-badge-table.status-dado-de-baja { 
    color: #dc2626; 
    border-color: #fecaca;
    background: #fef2f2;
}

.status-badge-table.status-robado { 
    color: #4b5563; 
    border-color: #e5e7eb;
    background: #f9fafb;
}

.status-icon-table {
    font-size: 0.6875rem;
    flex-shrink: 0;
}

.more-locations-table {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    font-weight: 500;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #6b7280;
    font-style: italic;
}

.table-actions-compact {
    display: flex;
    gap: 0.25rem;
    justify-content: center;
    align-items: center;
    flex-wrap: nowrap;
    min-width: 120px;
}

.empty-category {
    text-align: center;
    padding: 3rem 2rem;
    background: #fafbfc;
    border: 2px dashed #e5e7eb;
    border-radius: 8px;
    color: #6b7280;
}

.empty-category i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: #9ca3af;
}

.empty-category p {
    font-size: 0.875rem;
    font-weight: 500;
}

/* Dark Theme Styles for Categorias - Contraste Mejorado */
[data-bs-theme="dark"] .header-card {
    background: #1e293b;
    border-color: #475569;
    color: #f8fafc;
}

[data-bs-theme="dark"] .search-filters-card {
    background: #1e293b;
    border-color: #475569;
    color: #f8fafc;
}

[data-bs-theme="dark"] .category-accordion-item {
    background: #1e293b;
    border-color: #475569;
}

[data-bs-theme="dark"] .category-accordion-button {
    background: #1e293b;
    color: #f8fafc;
}

[data-bs-theme="dark"] .category-accordion-button:hover {
    background: #334155;
}

[data-bs-theme="dark"] .category-accordion-button:not(.collapsed) {
    background: #334155;
    border-bottom-color: #475569;
}

[data-bs-theme="dark"] .category-accordion-body {
    background: #0f172a;
    border-top-color: #475569;
}

[data-bs-theme="dark"] .category-icon-accordion {
    background: #334155;
    border-color: #475569;
    color: #cbd5e1;
}

[data-bs-theme="dark"] .view-toggle-buttons-small {
    background: #334155;
    border-color: #475569;
}

[data-bs-theme="dark"] .view-toggle-btn-small {
    color: #cbd5e1;
    border-right-color: #475569;
}

[data-bs-theme="dark"] .view-toggle-btn-small:hover {
    background: #475569;
    color: #f8fafc;
}

[data-bs-theme="dark"] .view-toggle-btn-small.active {
    background: #1e293b;
    color: #f8fafc;
}

[data-bs-theme="dark"] .element-card-compact {
    background: #1e293b;
    border-color: #475569;
}

[data-bs-theme="dark"] .element-card-compact:hover {
    border-color: #64748b;
}

[data-bs-theme="dark"] .element-image-compact {
    background: #334155;
}

[data-bs-theme="dark"] .image-placeholder-compact {
    background: #334155;
    color: #64748b;
}

[data-bs-theme="dark"] .element-header-compact {
    border-bottom-color: #475569;
}

[data-bs-theme="dark"] .element-code-compact {
    background: #334155;
    color: #cbd5e1;
}

[data-bs-theme="dark"] .location-compact {
    background: #334155;
    border-color: #475569;
}

[data-bs-theme="dark"] .element-actions-compact {
    border-top-color: #475569;
}

[data-bs-theme="dark"] .elements-table-view .table thead th {
    background: #1e293b !important;
    border-bottom-color: #475569 !important;
    color: #f8fafc !important;
}

/* Mejoras específicas para headers de tabla */
[data-bs-theme="dark"] .table thead th {
    background-color: #1e293b !important;
    border-bottom-color: #475569 !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .table-light thead th {
    background-color: #1e293b !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .table-light {
    background-color: #1e293b !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .elements-table-view .table tbody td {
    border-bottom-color: #475569;
    color: #f8fafc;
}

[data-bs-theme="dark"] .elements-table-view .table tbody tr:hover {
    background-color: #334155;
}

[data-bs-theme="dark"] .table-image-compact {
    border-color: #475569;
}

[data-bs-theme="dark"] .table-image-placeholder-compact {
    background: #334155;
    color: #64748b;
}

[data-bs-theme="dark"] .table-code-compact {
    background: #334155;
    color: #cbd5e1;
}

[data-bs-theme="dark"] .table-location-simple {
    background: #334155;
    border-color: #475569;
}

[data-bs-theme="dark"] .empty-category {
    background: #334155;
    border-color: #475569;
    color: #cbd5e1;
}

/* Elementos adicionales para mejor contraste */
[data-bs-theme="dark"] .header-icon {
    background: #334155;
    border-color: #475569;
    color: #cbd5e1;
}

[data-bs-theme="dark"] .header-title {
    color: #f8fafc;
}

[data-bs-theme="dark"] .header-badge {
    background: #334155;
    color: #f8fafc;
    border-color: #475569;
}

[data-bs-theme="dark"] .stat-item {
    background: #334155;
    border-color: #475569;
}

[data-bs-theme="dark"] .stat-item:hover {
    background: #475569;
    border-color: #64748b;
}

[data-bs-theme="dark"] .stat-icon {
    background: #1e293b;
    border-color: #475569;
    color: #cbd5e1;
}

[data-bs-theme="dark"] .stat-number {
    color: #f8fafc;
}

[data-bs-theme="dark"] .stat-label {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .search-input {
    background: #334155;
    border-color: #475569;
    color: #f8fafc;
}

[data-bs-theme="dark"] .search-input:focus {
    background: #334155;
    border-color: #64748b;
    color: #f8fafc;
}

[data-bs-theme="dark"] .search-icon {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .filter-label {
    color: #f8fafc;
}

[data-bs-theme="dark"] .category-filter-select {
    background: #334155;
    border-color: #475569;
    color: #f8fafc;
}

[data-bs-theme="dark"] .category-filter-select:focus {
    background: #334155;
    border-color: #64748b;
    color: #f8fafc;
}

[data-bs-theme="dark"] .filters-actions-section {
    border-top-color: #475569;
}

[data-bs-theme="dark"] .search-results-section {
    background: #1e293b;
    border-color: #475569;
}

[data-bs-theme="dark"] .search-results-header h4 {
    color: #f8fafc;
}

[data-bs-theme="dark"] .inventory-card {
    background: #334155;
    border-color: #475569;
}

[data-bs-theme="dark"] .inventory-card:hover {
    border-color: #64748b;
}

[data-bs-theme="dark"] .inventory-icon {
    background: #1e293b;
    border-color: #475569;
    color: #cbd5e1;
}

[data-bs-theme="dark"] .inventory-name {
    color: #f8fafc;
}

[data-bs-theme="dark"] .inventory-code {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .detail-label {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .detail-value {
    color: #f8fafc;
}

[data-bs-theme="dark"] .category-name-accordion {
    color: #f8fafc;
}

[data-bs-theme="dark"] .summary-item {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .elements-count-text {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .element-name-compact {
    color: #f8fafc;
}

[data-bs-theme="dark"] .detail-compact {
    color: #e2e8f0;
}

[data-bs-theme="dark"] .location-name-compact {
    color: #f8fafc;
}

[data-bs-theme="dark"] .element-name-table {
    color: #f8fafc;
}

[data-bs-theme="dark"] .table-brand-model {
    color: #e2e8f0;
}

[data-bs-theme="dark"] .location-name-simple {
    color: #f8fafc;
}

/* Estilos adicionales para modo oscuro - Elementos faltantes */
[data-bs-theme="dark"] .main-search-container {
    background: #1e293b !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .search-input-modern {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .search-input-modern:focus {
    background: #334155 !important;
    border-color: #3b82f6 !important;
    color: #f8fafc !important;
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.1) !important;
}

[data-bs-theme="dark"] .btn-clear-search-modern {
    background: #475569 !important;
    color: #cbd5e1 !important;
}

[data-bs-theme="dark"] .btn-clear-search-modern:hover {
    background: #64748b !important;
    color: #ef4444 !important;
}

[data-bs-theme="dark"] .search-suggestions {
    background: #334155 !important;
    border-color: #475569 !important;
}

[data-bs-theme="dark"] .filters-panel {
    background: #1e293b !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .filter-select {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .filter-select:focus {
    background: #334155 !important;
    border-color: #3b82f6 !important;
    color: #f8fafc !important;
    box-shadow: 0 0 0 0.125rem rgba(59, 130, 246, 0.1) !important;
}

[data-bs-theme="dark"] .filter-select:disabled {
    background: #475569 !important;
    color: #64748b !important;
}

[data-bs-theme="dark"] .search-loading {
    background: rgba(15, 23, 42, 0.9);
    color: #f8fafc;
}

[data-bs-theme="dark"] .search-loading .spinner-border {
    color: #3b82f6;
}

[data-bs-theme="dark"] .active-filters {
    background: #334155;
    border-color: #475569;
}

[data-bs-theme="dark"] .active-filters-title {
    color: #f8fafc;
}

[data-bs-theme="dark"] .active-filter-tag {
    background: #475569;
    border-color: #64748b;
    color: #f8fafc;
}

[data-bs-theme="dark"] .active-filter-tag .remove-filter {
    background: #64748b;
    color: #f8fafc;
}

[data-bs-theme="dark"] .active-filter-tag .remove-filter:hover {
    background: #ef4444;
    color: #f8fafc;
}

[data-bs-theme="dark"] .status-badge-element-new {
    border-color: #475569;
}

[data-bs-theme="dark"] .status-badge-element-new.status-disponible {
    background: #065f46;
    color: #d1fae5;
    border-color: #10b981;
}

[data-bs-theme="dark"] .status-badge-element-new.status-en-uso {
    background: #1e3a8a;
    color: #dbeafe;
    border-color: #3b82f6;
}

[data-bs-theme="dark"] .status-badge-element-new.status-en-mantenimiento {
    background: #92400e;
    color: #fef3c7;
    border-color: #f59e0b;
}

[data-bs-theme="dark"] .status-badge-element-new.status-dado-de-baja {
    background: #7f1d1d;
    color: #fecaca;
    border-color: #ef4444;
}

[data-bs-theme="dark"] .status-badge-element-new.status-robado {
    background: #581c87;
    color: #e9d5ff;
    border-color: #8b5cf6;
}

[data-bs-theme="dark"] .status-badge-table {
    border-color: #475569;
}

[data-bs-theme="dark"] .status-badge-table.status-disponible {
    background: #065f46;
    color: #d1fae5;
}

[data-bs-theme="dark"] .status-badge-table.status-en-uso {
    background: #1e3a8a;
    color: #dbeafe;
}

[data-bs-theme="dark"] .status-badge-table.status-en-mantenimiento {
    background: #92400e;
    color: #fef3c7;
}

[data-bs-theme="dark"] .status-badge-table.status-dado-de-baja {
    background: #7f1d1d;
    color: #fecaca;
}

[data-bs-theme="dark"] .status-badge-table.status-robado {
    background: #581c87;
    color: #e9d5ff;
}

[data-bs-theme="dark"] .search-header {
    background: transparent;
    color: #f8fafc;
}

[data-bs-theme="dark"] .search-header .search-title {
    color: #f8fafc;
}

[data-bs-theme="dark"] .search-header .search-title h5 {
    color: #f8fafc;
}

[data-bs-theme="dark"] .search-header .search-title i {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .advanced-search-container {
    background: #1e293b;
    border-color: #475569;
}

[data-bs-theme="dark"] .search-header .toggle-filters {
    background: #334155;
    border-color: #475569;
    color: #cbd5e1;
}

[data-bs-theme="dark"] .search-header .toggle-filters:hover {
    background: #475569;
    border-color: #64748b;
    color: #f8fafc;
}

[data-bs-theme="dark"] .header-actions .btn-primary {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border: none;
    color: white;
}

[data-bs-theme="dark"] .header-actions .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px 0 rgba(79, 70, 229, 0.4);
}

[data-bs-theme="dark"] .header-actions .btn-outline-secondary {
    background: #334155;
    border: 2px solid #475569;
    color: #cbd5e1;
}

[data-bs-theme="dark"] .header-actions .btn-outline-secondary:hover {
    background: #475569;
    border-color: #64748b;
    color: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.3);
}

[data-bs-theme="dark"] .header-card {
    background: #1e293b;
    border-color: #475569;
}

[data-bs-theme="dark"] .header-title {
    color: #f8fafc;
}

[data-bs-theme="dark"] .header-badge {
    background: #334155;
    color: #cbd5e1;
    border-color: #475569;
}

[data-bs-theme="dark"] .stat-item {
    background: #334155;
    border-color: #475569;
}

[data-bs-theme="dark"] .stat-item:hover {
    background: #475569;
    border-color: #64748b;
}

[data-bs-theme="dark"] .stat-number {
    color: #f8fafc;
}

[data-bs-theme="dark"] .stat-label {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .stat-icon {
    background: #475569;
    border-color: #64748b;
    color: #cbd5e1;
}

[data-bs-theme="dark"] .header-icon {
    background: #334155;
    border-color: #475569;
    color: #cbd5e1;
}

[data-bs-theme="dark"] .search-results-header {
    background: #1e293b;
    border-color: #475569;
}

[data-bs-theme="dark"] .search-results-title {
    color: #f8fafc;
}

[data-bs-theme="dark"] .search-results-controls .btn {
    background: rgba(248, 250, 252, 0.1);
    border-color: rgba(248, 250, 252, 0.2);
    color: #f8fafc;
}

[data-bs-theme="dark"] .search-results-controls .btn:hover {
    background: rgba(248, 250, 252, 0.2);
    border-color: rgba(248, 250, 252, 0.3);
}

[data-bs-theme="dark"] .view-toggle-buttons-small {
    background: #334155;
    border-color: #475569;
}

[data-bs-theme="dark"] .view-toggle-btn-small {
    color: #cbd5e1;
    border-color: #475569;
}

[data-bs-theme="dark"] .view-toggle-btn-small:hover {
    background: #475569;
    color: #f8fafc;
}

[data-bs-theme="dark"] .view-toggle-btn-small.active {
    background: #64748b;
    color: #f8fafc;
}

[data-bs-theme="dark"] .main-search-container {
    background: #334155;
}

[data-bs-theme="dark"] .main-search-container * {
    background: #334155 !important;
    color: #f8fafc !important;
    border-color: #475569 !important;
}

[data-bs-theme="dark"] .search-input-modern {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .search-input-modern:focus {
    background: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .search-icon {
    color: #cbd5e1 !important;
}

[data-bs-theme="dark"] .btn-clear-search-modern {
    background: #475569 !important;
    color: #cbd5e1 !important;
}

[data-bs-theme="dark"] .btn-clear-search-modern:hover {
    background: #64748b !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .table-thumbnail-compact {
    border-color: #475569;
}

[data-bs-theme="dark"] .element-thumbnail-compact {
    border-color: #475569;
}

[data-bs-theme="dark"] .detail-row-compact {
    border-bottom-color: #475569;
}

[data-bs-theme="dark"] .detail-label-compact {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .detail-value-compact {
    color: #f8fafc;
}

[data-bs-theme="dark"] .location-info-compact {
    color: #f8fafc;
}

[data-bs-theme="dark"] .location-quantity-compact {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .more-locations-compact {
    background: #475569;
    color: #cbd5e1;
    border-color: #64748b;
}

[data-bs-theme="dark"] .more-locations-compact:hover {
    background: #64748b;
    color: #f8fafc;
}

[data-bs-theme="dark"] .more-locations-table {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .more-locations-table:hover {
    color: #f8fafc;
}

[data-bs-theme="dark"] .table-locations-detailed {
    border-color: #475569;
}

[data-bs-theme="dark"] .table-location-detailed {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .location-info-table {
    color: #f8fafc;
}

[data-bs-theme="dark"] .location-name-table {
    color: #f8fafc;
}

[data-bs-theme="dark"] .location-quantity-table {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .table-serial {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .table-owner {
    color: #e2e8f0;
}

[data-bs-theme="dark"] .table-actions-compact .btn {
    border-color: #475569;
}

[data-bs-theme="dark"] .table-actions-compact .btn:hover {
    border-color: #64748b;
}

[data-bs-theme="dark"] .element-actions-compact .btn {
    border-color: #475569;
}

[data-bs-theme="dark"] .element-actions-compact .btn:hover {
    border-color: #64748b;
}

/* Alternative dark theme using body class for categorias - Contraste Mejorado */
body.dark-theme .header-card {
    background: #1e293b !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .search-filters-card {
    background: #1e293b !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .category-accordion-item {
    background: #1e293b !important;
    border-color: #475569 !important;
}

body.dark-theme .category-accordion-button {
    background: #1e293b !important;
    color: #f8fafc !important;
}

body.dark-theme .category-accordion-button:hover {
    background: #334155 !important;
}

body.dark-theme .category-accordion-button:not(.collapsed) {
    background: #334155 !important;
    border-bottom-color: #475569 !important;
}

body.dark-theme .category-accordion-body {
    background: #0f172a !important;
    border-top-color: #475569 !important;
}

body.dark-theme .element-card-compact {
    background: #1e293b !important;
    border-color: #475569 !important;
}

body.dark-theme .element-card-compact:hover {
    border-color: #64748b !important;
}

body.dark-theme .elements-table-view .table thead th {
    background: #334155 !important;
    border-bottom-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .elements-table-view .table tbody td {
    border-bottom-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .elements-table-view .table tbody tr:hover {
    background-color: #334155 !important;
}

/* Elementos adicionales para el sistema dual */
body.dark-theme .header-icon {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #cbd5e1 !important;
}

body.dark-theme .header-title {
    color: #f8fafc !important;
}

body.dark-theme .header-badge {
    background: #334155 !important;
    color: #f8fafc !important;
    border-color: #475569 !important;
}

body.dark-theme .stat-item {
    background: #334155 !important;
    border-color: #475569 !important;
}

body.dark-theme .stat-item:hover {
    background: #475569 !important;
    border-color: #64748b !important;
}

body.dark-theme .stat-icon {
    background: #1e293b !important;
    border-color: #475569 !important;
    color: #cbd5e1 !important;
}

body.dark-theme .stat-number {
    color: #f8fafc !important;
}

body.dark-theme .stat-label {
    color: #cbd5e1 !important;
}

body.dark-theme .search-input {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .search-input:focus {
    background: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
}

body.dark-theme .search-icon {
    color: #cbd5e1 !important;
}

body.dark-theme .main-search-container {
    background: #1e293b !important;
    color: #f8fafc !important;
}

body.dark-theme .search-input-modern {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .search-input-modern:focus {
    background: #334155 !important;
    border-color: #3b82f6 !important;
    color: #f8fafc !important;
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.1) !important;
}

body.dark-theme .btn-clear-search-modern {
    background: #475569 !important;
    color: #cbd5e1 !important;
}

body.dark-theme .btn-clear-search-modern:hover {
    background: #64748b !important;
    color: #ef4444 !important;
}

body.dark-theme .search-suggestions {
    background: #334155 !important;
    border-color: #475569 !important;
}

body.dark-theme .filters-panel {
    background: #1e293b !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .filter-select {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .filter-select:focus {
    background: #334155 !important;
    border-color: #3b82f6 !important;
    color: #f8fafc !important;
    box-shadow: 0 0 0 0.125rem rgba(59, 130, 246, 0.1) !important;
}

body.dark-theme .filter-select:disabled {
    background: #475569 !important;
    color: #64748b !important;
}

body.dark-theme .table-serial {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .table-location-detailed {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .search-loading {
    background: rgba(15, 23, 42, 0.9) !important;
    color: #f8fafc !important;
}

body.dark-theme .active-filters {
    background: #334155 !important;
    border-color: #475569 !important;
}

body.dark-theme .active-filters-title {
    color: #f8fafc !important;
}

body.dark-theme .active-filter-tag {
    background: #475569 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
}

body.dark-theme .status-badge-element-new.status-disponible {
    background: #065f46 !important;
    color: #d1fae5 !important;
    border-color: #10b981 !important;
}

body.dark-theme .status-badge-element-new.status-en-uso {
    background: #1e3a8a !important;
    color: #dbeafe !important;
    border-color: #3b82f6 !important;
}

body.dark-theme .status-badge-element-new.status-en-mantenimiento {
    background: #92400e !important;
    color: #fef3c7 !important;
    border-color: #f59e0b !important;
}

body.dark-theme .status-badge-element-new.status-dado-de-baja {
    background: #7f1d1d !important;
    color: #fecaca !important;
    border-color: #ef4444 !important;
}

body.dark-theme .status-badge-element-new.status-robado {
    background: #581c87 !important;
    color: #e9d5ff !important;
    border-color: #8b5cf6 !important;
}

body.dark-theme .status-badge-table.status-disponible {
    background: #065f46 !important;
    color: #d1fae5 !important;
}

body.dark-theme .status-badge-table.status-en-uso {
    background: #1e3a8a !important;
    color: #dbeafe !important;
}

body.dark-theme .status-badge-table.status-en-mantenimiento {
    background: #92400e !important;
    color: #fef3c7 !important;
}

body.dark-theme .status-badge-table.status-dado-de-baja {
    background: #7f1d1d !important;
    color: #fecaca !important;
}

body.dark-theme .status-badge-table.status-robado {
    background: #581c87 !important;
    color: #e9d5ff !important;
}

body.dark-theme .search-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #581c87 100%) !important;
    color: white !important;
}

body.dark-theme .search-header .search-title h5 {
    color: white !important;
}

body.dark-theme .search-header .toggle-filters {
    background: rgba(255, 255, 255, 0.2) !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
    color: white !important;
}

body.dark-theme .search-header .toggle-filters:hover {
    background: rgba(255, 255, 255, 0.3) !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
    color: white !important;
}

body.dark-theme .search-results-header {
    background: #1e293b !important;
    border-color: #475569 !important;
}

body.dark-theme .search-results-title {
    color: #f8fafc !important;
}

body.dark-theme .search-results-controls .btn {
    background: rgba(248, 250, 252, 0.1) !important;
    border-color: rgba(248, 250, 252, 0.2) !important;
    color: #f8fafc !important;
}

body.dark-theme .search-results-controls .btn:hover {
    background: rgba(248, 250, 252, 0.2) !important;
    border-color: rgba(248, 250, 252, 0.3) !important;
}

body.dark-theme .view-toggle-buttons-small {
    background: #334155 !important;
    border-color: #475569 !important;
}

body.dark-theme .view-toggle-btn-small {
    color: #cbd5e1 !important;
    border-color: #475569 !important;
}

body.dark-theme .view-toggle-btn-small:hover {
    background: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .view-toggle-btn-small.active {
    background: #64748b !important;
    color: #f8fafc !important;
}

body.dark-theme .main-search-container {
    background: #334155 !important;
}

body.dark-theme .main-search-container * {
    background: #334155 !important;
    color: #f8fafc !important;
    border-color: #475569 !important;
}

body.dark-theme .search-input-modern {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .search-input-modern:focus {
    background: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
}

body.dark-theme .search-icon {
    color: #cbd5e1 !important;
}

body.dark-theme .btn-clear-search-modern {
    background: #475569 !important;
    color: #cbd5e1 !important;
}

body.dark-theme .btn-clear-search-modern:hover {
    background: #64748b !important;
    color: #f8fafc !important;
}

body.dark-theme .element-name-compact {
    color: #f8fafc !important;
}

body.dark-theme .detail-label-compact {
    color: #cbd5e1 !important;
}

body.dark-theme .detail-value-compact {
    color: #f8fafc !important;
}

body.dark-theme .location-info-compact {
    color: #f8fafc !important;
}

body.dark-theme .location-quantity-compact {
    color: #cbd5e1 !important;
}

body.dark-theme .more-locations-compact {
    background: #475569 !important;
    color: #cbd5e1 !important;
    border-color: #64748b !important;
}

body.dark-theme .more-locations-compact:hover {
    background: #64748b !important;
    color: #f8fafc !important;
}

/* Estilos adicionales para asegurar compatibilidad completa con modo oscuro */
[data-bs-theme="dark"] .btn-outline-warning {
    color: #fbbf24;
    border-color: #fbbf24;
}

[data-bs-theme="dark"] .btn-outline-warning:hover {
    background-color: #fbbf24;
    color: #0f172a;
}

[data-bs-theme="dark"] .btn-xs {
    border-color: #475569;
}

[data-bs-theme="dark"] .btn-xs:hover {
    border-color: #64748b;
}

[data-bs-theme="dark"] .form-control::placeholder {
    color: #9ca3af;
}

[data-bs-theme="dark"] .form-select option {
    background-color: #334155 !important;
    color: #f8fafc !important;
}

/* Correcciones específicas para los selectores de filtros */
[data-bs-theme="dark"] select {
    background-color: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] select:focus {
    background-color: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4) !important;
}

[data-bs-theme="dark"] .form-select {
    background-color: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23f8fafc' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e") !important;
}

[data-bs-theme="dark"] .form-select:focus {
    background-color: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4) !important;
}

/* Inputs específicos para campos de serie y otros */
[data-bs-theme="dark"] input[type="text"] {
    background-color: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] input[type="text"]:focus {
    background-color: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4) !important;
}

/* Correcciones adicionales para todos los tipos de input */
[data-bs-theme="dark"] input {
    background-color: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] input:focus {
    background-color: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4) !important;
}

/* Específicamente para inputs dentro de tablas */
[data-bs-theme="dark"] .table input {
    background-color: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

[data-bs-theme="dark"] .table input:focus {
    background-color: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
}

/* Mejorar el contraste de las celdas de tabla */
[data-bs-theme="dark"] .table td {
    border-bottom-color: #475569 !important;
    background-color: #1e293b !important;
}

[data-bs-theme="dark"] .table tbody tr {
    background-color: #1e293b !important;
}

[data-bs-theme="dark"] .table tbody tr:hover {
    background-color: #334155 !important;
}

[data-bs-theme="dark"] .text-center {
    color: inherit;
}

[data-bs-theme="dark"] .py-4 {
    color: inherit;
}

[data-bs-theme="dark"] .me-2 {
    color: inherit;
}

[data-bs-theme="dark"] .clickeable {
    color: #60a5fa;
}

[data-bs-theme="dark"] .clickeable:hover {
    color: #3b82f6;
}

/* Estilos para los estados en los badges con mejor contraste */
[data-bs-theme="dark"] .status-icon-compact,
[data-bs-theme="dark"] .status-icon-table {
    color: inherit;
}

[data-bs-theme="dark"] .status-text-compact {
    color: inherit;
}

/* Botones de formularios en modo oscuro */
[data-bs-theme="dark"] form button {
    border-color: #475569;
}

[data-bs-theme="dark"] form button:hover {
    border-color: #64748b;
}

/* Mejoras para el panel de filtros */
[data-bs-theme="dark"] .form-label {
    color: #f8fafc;
}

[data-bs-theme="dark"] .btn-primary {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

[data-bs-theme="dark"] .btn-primary:hover {
    background-color: #2563eb;
    border-color: #2563eb;
}

[data-bs-theme="dark"] .btn-secondary {
    background-color: #64748b;
    border-color: #64748b;
}

[data-bs-theme="dark"] .btn-secondary:hover {
    background-color: #475569;
    border-color: #475569;
}

/* Asegurar que las imágenes lazy loading tengan el fondo correcto */
[data-bs-theme="dark"] .lazy-load {
    background-color: #334155;
}

/* Estilos para los elementos de la tabla responsive */
[data-bs-theme="dark"] .table-responsive {
    border-color: #475569;
}

[data-bs-theme="dark"] .table-responsive .table {
    background-color: #1e293b;
}

/* Compatibilidad con body.dark-theme adicional */
body.dark-theme .btn-outline-warning {
    color: #fbbf24 !important;
    border-color: #fbbf24 !important;
}

body.dark-theme .btn-outline-warning:hover {
    background-color: #fbbf24 !important;
    color: #0f172a !important;
}

body.dark-theme .form-control::placeholder {
    color: #9ca3af !important;
}

body.dark-theme .clickeable {
    color: #60a5fa !important;
}

body.dark-theme .clickeable:hover {
    color: #3b82f6 !important;
}

body.dark-theme .btn-primary {
    background-color: #3b82f6 !important;
    border-color: #3b82f6 !important;
}

body.dark-theme .btn-secondary {
    background-color: #64748b !important;
    border-color: #64748b !important;
}

/* Estilos finales para elementos específicos que pueden faltar */
[data-bs-theme="dark"] .view-toggle-controls {
    background: rgba(248, 250, 252, 0.1);
    border: 1px solid rgba(248, 250, 252, 0.2);
    border-radius: 6px;
}

[data-bs-theme="dark"] .view-toggle-btn-small {
    background: transparent;
    color: rgba(248, 250, 252, 0.8);
    border-color: rgba(248, 250, 252, 0.2);
}

[data-bs-theme="dark"] .view-toggle-btn-small:hover {
    background: rgba(248, 250, 252, 0.1);
    color: #f8fafc;
}

[data-bs-theme="dark"] .view-toggle-btn-small.active {
    background: rgba(248, 250, 252, 0.2);
    color: #f8fafc;
    border-color: rgba(248, 250, 252, 0.3);
}

[data-bs-theme="dark"] .category-view-toggle {
    border-color: #475569;
}

[data-bs-theme="dark"] .category-elements-count {
    color: #f8fafc;
}

[data-bs-theme="dark"] .elements-count-text {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .category-summary {
    color: #cbd5e1;
}

[data-bs-theme="dark"] .stat-badge-compact {
    border-color: #475569;
}

[data-bs-theme="dark"] .stat-badge-compact.stat-disponible {
    background: #065f46;
    color: #d1fae5;
}

[data-bs-theme="dark"] .stat-badge-compact.stat-en-uso {
    background: #1e3a8a;
    color: #dbeafe;
}

[data-bs-theme="dark"] .stat-badge-compact.stat-mantenimiento {
    background: #92400e;
    color: #fef3c7;
}

[data-bs-theme="dark"] .stat-badge-compact.stat-baja {
    background: #7f1d1d;
    color: #fecaca;
}

[data-bs-theme="dark"] .stat-badge-compact.stat-robado {
    background: #581c87;
    color: #e9d5ff;
}

[data-bs-theme="dark"] .search-results-grid {
    background: transparent;
}

[data-bs-theme="dark"] .search-results-table {
    background: transparent;
}

[data-bs-theme="dark"] .badge {
    color: #f8fafc;
}

[data-bs-theme="dark"] .badge.bg-primary {
    background-color: #3b82f6 !important;
    color: #f8fafc !important;
}

/* Compatibilidad body.dark-theme para los nuevos estilos */
body.dark-theme .view-toggle-btn-small {
    background: transparent !important;
    color: rgba(248, 250, 252, 0.8) !important;
    border-color: rgba(248, 250, 252, 0.2) !important;
}

body.dark-theme .view-toggle-btn-small:hover {
    background: rgba(248, 250, 252, 0.1) !important;
    color: #f8fafc !important;
}

body.dark-theme .view-toggle-btn-small.active {
    background: rgba(248, 250, 252, 0.2) !important;
    color: #f8fafc !important;
    border-color: rgba(248, 250, 252, 0.3) !important;
}

body.dark-theme .stat-badge-compact.stat-disponible {
    background: #065f46 !important;
    color: #d1fae5 !important;
}

body.dark-theme .stat-badge-compact.stat-en-uso {
    background: #1e3a8a !important;
    color: #dbeafe !important;
}

body.dark-theme .stat-badge-compact.stat-mantenimiento {
    background: #92400e !important;
    color: #fef3c7 !important;
}

body.dark-theme .stat-badge-compact.stat-baja {
    background: #7f1d1d !important;
    color: #fecaca !important;
}

body.dark-theme .stat-badge-compact.stat-robado {
    background: #581c87 !important;
    color: #e9d5ff !important;
}

body.dark-theme .badge.bg-primary {
    background-color: #3b82f6 !important;
    color: #f8fafc !important;
}

/* Correcciones body.dark-theme para elementos identificados en capturas */
body.dark-theme select {
    background-color: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme select:focus {
    background-color: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4) !important;
}

body.dark-theme .form-select {
    background-color: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23f8fafc' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e") !important;
}

body.dark-theme .form-select:focus {
    background-color: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4) !important;
}

body.dark-theme input[type="text"] {
    background-color: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme input[type="text"]:focus {
    background-color: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4) !important;
}

body.dark-theme .table thead th {
    background-color: #1e293b !important;
    border-bottom-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .table-light thead th {
    background-color: #1e293b !important;
    color: #f8fafc !important;
}

body.dark-theme .table-light {
    background-color: #1e293b !important;
    color: #f8fafc !important;
}

/* Correcciones adicionales body.dark-theme */
body.dark-theme input {
    background-color: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme input:focus {
    background-color: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.4) !important;
}

body.dark-theme .table input {
    background-color: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .table input:focus {
    background-color: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
}

body.dark-theme .table td {
    border-bottom-color: #475569 !important;
    background-color: #1e293b !important;
}

body.dark-theme .table tbody tr {
    background-color: #1e293b !important;
}

body.dark-theme .table tbody tr:hover {
    background-color: #334155 !important;
}

body.dark-theme .stat-label {
    color: #cbd5e1 !important;
}

body.dark-theme .search-input {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .search-input:focus {
    background: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
}

body.dark-theme .search-icon {
    color: #cbd5e1 !important;
}

body.dark-theme .filter-label {
    color: #f8fafc !important;
}

body.dark-theme .category-filter-select {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

body.dark-theme .category-filter-select:focus {
    background: #334155 !important;
    border-color: #64748b !important;
    color: #f8fafc !important;
}

body.dark-theme .filters-actions-section {
    border-top-color: #475569 !important;
}

body.dark-theme .search-results-section {
    background: #1e293b !important;
    border-color: #475569 !important;
}

body.dark-theme .search-results-header h4 {
    color: #f8fafc !important;
}

body.dark-theme .inventory-card {
    background: #334155 !important;
    border-color: #475569 !important;
}

body.dark-theme .inventory-card:hover {
    border-color: #64748b !important;
}

body.dark-theme .inventory-icon {
    background: #1e293b !important;
    border-color: #475569 !important;
    color: #cbd5e1 !important;
}

body.dark-theme .inventory-name {
    color: #f8fafc !important;
}

body.dark-theme .inventory-code {
    color: #cbd5e1 !important;
}

body.dark-theme .detail-label {
    color: #cbd5e1 !important;
}

body.dark-theme .detail-value {
    color: #f8fafc !important;
}

body.dark-theme .category-name-accordion {
    color: #f8fafc !important;
}

body.dark-theme .summary-item {
    color: #cbd5e1 !important;
}

body.dark-theme .elements-count-text {
    color: #cbd5e1 !important;
}

body.dark-theme .element-name-compact {
    color: #f8fafc !important;
}

body.dark-theme .detail-compact {
    color: #e2e8f0 !important;
}

body.dark-theme .location-name-compact {
    color: #f8fafc !important;
}

body.dark-theme .element-name-table {
    color: #f8fafc !important;
}

body.dark-theme .table-brand-model {
    color: #e2e8f0 !important;
}

body.dark-theme .location-name-simple {
    color: #f8fafc !important;
}

/* Responsive Mejorado */
@media (max-width: 1200px) {
    .elements-grid-compact {
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.25rem;
    }
    
    .table-locations-detailed {
        min-width: 240px;
        max-width: 280px;
    }
}

@media (max-width: 992px) {
    .category-accordion-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .category-stats-compact {
        align-self: stretch;
        justify-content: flex-start;
    }
    
    .elements-grid-compact {
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1rem;
    }
    
    .table-locations-detailed {
        min-width: 180px;
        max-width: 220px;
    }
    
    .element-content-compact {
        padding: 1.25rem;
    }
    
    .elements-table-view .table thead th {
        padding: 0.75rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .elements-table-view .table tbody td {
        padding: 0.75rem 0.5rem;
    }
}

@media (max-width: 768px) {
    .elements-grid-compact {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .element-card-compact {
        border-radius: 8px;
    }
    
    .element-image-compact {
        height: 140px;
    }
    
    .element-content-compact {
        padding: 1rem;
    }
    
    .element-name-compact {
        font-size: 1rem;
    }
    
    .detail-row-compact {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .detail-label-compact {
        min-width: auto;
        font-size: 0.8125rem;
    }
    
    .detail-value-compact {
        text-align: left;
        font-size: 0.875rem;
    }
    
    .category-view-toggle {
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        text-align: center;
    }
    
    .view-toggle-buttons-small {
        order: -1;
    }
    
    .table-responsive {
        font-size: 0.8125rem;
    }
    
    .elements-table-view .table thead th {
        padding: 0.75rem 0.5rem;
        font-size: 0.6875rem;
    }
    
    .elements-table-view .table tbody td {
        padding: 0.75rem 0.5rem;
    }
    
    .table-image-compact {
        width: 40px;
        height: 40px;
    }
    
    .table-locations-detailed {
        min-width: 160px;
        max-width: 200px;
    }
    
    .table-location-detailed {
        padding: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .status-badge-table {
        padding: 0.25rem 0.5rem;
        font-size: 0.6875rem;
        gap: 0.25rem;
    }
    
    .table-actions-compact {
        gap: 0.125rem;
        min-width: 100px;
        flex-wrap: wrap;
    }
    
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.6875rem;
    }
}

@media (max-width: 576px) {
    .elements-grid-compact {
        gap: 0.75rem;
    }
    
    .element-content-compact {
        padding: 0.875rem;
    }
    
    .element-header-compact {
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
    }
    
    .element-name-compact {
        font-size: 0.9375rem;
        margin-bottom: 0.5rem;
    }
    
    .element-code-compact {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .location-compact {
        padding: 0.75rem;
        margin-bottom: 0.5rem;
    }
    
    .status-badge-element-new {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
        gap: 0.375rem;
    }
    
    .more-locations-compact {
        font-size: 0.75rem;
        padding: 0.375rem;
    }
    
    /* Ocultar algunas columnas en móvil para mejor legibilidad */
    .elements-table-view .table th:nth-child(4),
    .elements-table-view .table td:nth-child(4),
    .elements-table-view .table th:nth-child(5),
    .elements-table-view .table td:nth-child(5) {
        display: none;
    }
    
    .table-locations-detailed {
        min-width: 140px;
        max-width: 160px;
    }
}

/* ===== ESTILOS DE ALTA PRIORIDAD PARA FORZAR MODO OSCURO ===== */
/* Estos estilos tienen la máxima prioridad para elementos problemáticos */

html[data-bs-theme="dark"] .main-search-container,
body.dark-theme .main-search-container {
    background: #1e293b !important;
    color: #f8fafc !important;
}

html[data-bs-theme="dark"] .search-input-modern,
body.dark-theme .search-input-modern {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

html[data-bs-theme="dark"] .table-serial,
body.dark-theme .table-serial {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

html[data-bs-theme="dark"] .table-location-detailed,
body.dark-theme .table-location-detailed {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

/* Forzar colores en elementos internos */
html[data-bs-theme="dark"] .table-location-detailed *,
body.dark-theme .table-location-detailed * {
    color: #f8fafc !important;
}

html[data-bs-theme="dark"] .table-serial *,
body.dark-theme .table-serial * {
    color: #f8fafc !important;
}

/* Elementos específicos dentro de table-location-detailed */
html[data-bs-theme="dark"] .table-location-detailed .location-info-table,
body.dark-theme .table-location-detailed .location-info-table {
    color: #f8fafc !important;
}

html[data-bs-theme="dark"] .table-location-detailed .location-name-table,
body.dark-theme .table-location-detailed .location-name-table {
    color: #f8fafc !important;
}

html[data-bs-theme="dark"] .table-location-detailed .status-badge-table,
body.dark-theme .table-location-detailed .status-badge-table {
    background: #475569 !important;
    color: #f8fafc !important;
    border-color: #64748b !important;
}

/* Asegurar que TODOS los inputs y selects cambien sin excepción */
html[data-bs-theme="dark"] input,
html[data-bs-theme="dark"] select,
html[data-bs-theme="dark"] .form-control,
html[data-bs-theme="dark"] .form-select,
body.dark-theme input,
body.dark-theme select,
body.dark-theme .form-control,
body.dark-theme .form-select {
    background-color: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

/* Forzar estilos en elementos de filtros */
html[data-bs-theme="dark"] .filter-select,
body.dark-theme .filter-select {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}

/* Forzar fondo del contenedor principal de búsqueda */
html[data-bs-theme="dark"] .main-search-container *,
body.dark-theme .main-search-container * {
    color: #f8fafc !important;
}

html[data-bs-theme="dark"] .main-search-container input,
body.dark-theme .main-search-container input {
    background: #334155 !important;
    border-color: #475569 !important;
    color: #f8fafc !important;
}
</style>
@endpush

@push('scripts')
<script>
console.log('=== CATEGORIAS.BLADE.PHP SCRIPT STARTED ===');

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== CATEGORIAS.BLADE.PHP DOMContentLoaded EVENT FIRED ===');
    
    // Solo ejecutar si estamos en la página de categorías
    const currentPage = document.querySelector('[data-page]')?.getAttribute('data-page');
    if (currentPage !== 'inventarios-categorias') {
        console.log('No estamos en inventarios-categorias, saltando JavaScript...');
        return;
    }
    
    console.log('Ejecutando JavaScript de inventarios-categorias...');
    
    // ===== SISTEMA DE BÚSQUEDA INSTANTÁNEA Y FILTROS EN CASCADA =====
    
    // Variables globales
    let searchTimeout;
    let currentFilters = {
        search: '',
        categoria_id: '',
        elemento_nombre: '',
        marca: '',
        estado: '',
        ubicacion_id: ''
    };
    let activeFilters = [];
    
    // Elementos DOM
    const searchInput = document.getElementById('searchInput');
    const searchLoading = document.getElementById('searchLoading');
    const clearSearchBtn = document.getElementById('clearSearch');
    const toggleFiltersBtn = document.getElementById('toggleFilters');
    const filtersPanel = document.getElementById('filtersPanel');
    const instantSearchResults = document.getElementById('instantSearchResults');
    const instantSearchGrid = document.getElementById('instantSearchGrid');
    const resultsCount = document.getElementById('resultsCount');
    const hideSearchResultsBtn = document.getElementById('hideSearchResults');
    const activeFiltersContainer = document.getElementById('activeFilters');
    const activeFiltersList = document.getElementById('activeFiltersList');
    
    // Filtros
    const filterCategoria = document.getElementById('filterCategoria');
    const filterElemento = document.getElementById('filterElemento');
    const filterMarca = document.getElementById('filterMarca');
    const filterEstado = document.getElementById('filterEstado');
    const filterUbicacion = document.getElementById('filterUbicacion');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const clearFiltersBtn = document.getElementById('clearFilters');
    
    // ===== NAVEGACIÓN SIMPLE CON HISTORIAL DEL NAVEGADOR =====
    
    // Función simple para marcar navegación a detalle
    function setupDetailNavigation() {
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href*="/inventarios/"]');
            if (link && link.href.match(/\/inventarios\/\d+$/)) {
                // Marcar que vamos a una vista de detalle
                sessionStorage.setItem('from_inventarios_list', 'true');
            }
        });
    }
    
    // Inicializar navegación
    setupDetailNavigation();
    
    // Reconfigurar navegación después de búsquedas (para nuevos elementos)
    function refreshDetailNavigation() {
        setupDetailNavigation();
    }
    
    // ===== BÚSQUEDA INSTANTÁNEA OPTIMIZADA =====
    
    // Variables para optimización de búsqueda
    let lastSearchQuery = '';
    let isSearching = false;
    
    // Event listener optimizado con debouncing mejorado
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const query = this.value.trim();
            
            // Mostrar/ocultar botón de limpiar
            if (clearSearchBtn) {
                clearSearchBtn.style.display = query.length > 0 ? 'flex' : 'none';
            }
            
            // Limpiar timeout anterior
            clearTimeout(searchTimeout);
            
            // Evitar búsquedas duplicadas
            if (query === lastSearchQuery) {
                return;
            }
            
            if (query.length >= 2) {
                // Mostrar loading solo si no estamos ya buscando
                if (searchLoading && !isSearching) {
                    searchLoading.style.display = 'block';
                }
                
                // Debounce search con tiempo optimizado
                searchTimeout = setTimeout(() => {
                    if (!isSearching) {
                        lastSearchQuery = query;
                        currentFilters.search = query;
                        performInstantSearch(query);
                        refreshDetailNavigation(); // Reconfigurar navegación para nuevos elementos
                    }
                }, 250); // Reducido de 300ms a 250ms para mejor responsividad
            } else if (query.length === 0) {
                lastSearchQuery = '';
                hideInstantResults();
            }
        });
        
        // Agregar event listener para Enter key
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                if (query.length >= 1) {
                    lastSearchQuery = query;
                    performInstantSearch(query);
                }
            }
        });
    }
    
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus(); // Mantener el foco en el input
            }
            this.style.display = 'none';
            lastSearchQuery = '';
            currentFilters.search = '';
            hideInstantResults();
            refreshDetailNavigation(); // Reconfigurar navegación
        });
    }
    
    // Toggle de vistas para resultados de búsqueda
    const searchGridToggle = document.getElementById('searchGridToggle');
    const searchTableToggle = document.getElementById('searchTableToggle');
    const instantSearchTable = document.getElementById('instantSearchTable');
    const clearSearchResultsBtn = document.getElementById('clearSearchResults');
    
    if (searchGridToggle) {
        searchGridToggle.addEventListener('click', function() {
            searchGridToggle.classList.add('active');
            if (searchTableToggle) searchTableToggle.classList.remove('active');
            instantSearchGrid.style.display = 'grid';
            if (instantSearchTable) instantSearchTable.style.display = 'none';
        });
    }
    
    if (searchTableToggle) {
        searchTableToggle.addEventListener('click', function() {
            searchTableToggle.classList.add('active');
            if (searchGridToggle) searchGridToggle.classList.remove('active');
            instantSearchGrid.style.display = 'none';
            if (instantSearchTable) instantSearchTable.style.display = 'block';
        });
    }
    
    if (clearSearchResultsBtn) {
        clearSearchResultsBtn.addEventListener('click', function() {
            // Limpiar búsqueda
            searchInput.value = '';
            if (clearSearchBtn) clearSearchBtn.style.display = 'none';
            currentFilters.search = '';
            
            // Ocultar resultados
            hideInstantResults();
        });
    }
    
    function performInstantSearch(query = null) {
        // Prevenir búsquedas concurrentes
        if (isSearching) {
            return;
        }
        
        isSearching = true;
        
        if (query !== null) {
            currentFilters.search = query;
        }
        
        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key]) {
                params.append(key, currentFilters[key]);
            }
        });
        
        // Mostrar loading si hay elemento de loading
        if (searchLoading) searchLoading.style.display = 'block';
        
        // Usar XMLHttpRequest para mejor compatibilidad con navegadores
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `{{ route('inventarios.search-instantaneo') }}?${params.toString()}`, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        
        // Timeout para evitar búsquedas que se cuelguen
        xhr.timeout = 10000; // 10 segundos
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                isSearching = false;
                if (searchLoading) searchLoading.style.display = 'none';
                
                if (xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        displayInstantResults(data);
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        showSearchError('Error al procesar los resultados');
                    }
                } else {
                    console.error('Error en búsqueda instantánea:', xhr.status);
                    showSearchError('Error al realizar la búsqueda');
                }
            }
        };
        
        xhr.ontimeout = function() {
            isSearching = false;
            if (searchLoading) searchLoading.style.display = 'none';
            console.error('Timeout en búsqueda instantánea');
            showSearchError('La búsqueda tardó demasiado tiempo');
        };
        
        xhr.onerror = function() {
            isSearching = false;
            if (searchLoading) searchLoading.style.display = 'none';
            console.error('Error de red en búsqueda instantánea');
            showSearchError('Error de conexión');
        };
        
        xhr.send();
    }
    
    function showSearchError(message) {
        if (instantSearchGrid) {
            const gridContainer = instantSearchGrid.querySelector('.elements-grid-compact');
            if (gridContainer) {
                gridContainer.innerHTML = `<div class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle me-2"></i>${message}</div>`;
            }
        }
        
        const instantSearchTableBody = document.getElementById('instantSearchTableBody');
        if (instantSearchTableBody) {
            instantSearchTableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle me-2"></i>${message}</td></tr>`;
        }
        
        if (instantSearchResults) {
            instantSearchResults.style.display = 'block';
        }
    }
    
    function displayInstantResults(data) {
        const searchTermDisplay = document.getElementById('searchTermDisplay');
        const instantSearchTableBody = document.getElementById('instantSearchTableBody');
        
        // Actualizar información de resultados
        resultsCount.textContent = `${data.total} elementos`;
        
        // Solo mostrar término de búsqueda si realmente hay búsqueda por texto
        if (searchTermDisplay) {
            if (currentFilters.search && currentFilters.search.trim() !== '') {
                searchTermDisplay.textContent = `para "${currentFilters.search}"`;
            } else {
                searchTermDisplay.textContent = ''; // Limpiar si no hay búsqueda por texto
            }
        }
        
        if (data.inventarios.length === 0) {
            const gridContainer = instantSearchGrid.querySelector('.elements-grid-compact');
            gridContainer.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-search me-2"></i>No se encontraron resultados</div>';
            if (instantSearchTableBody) {
                instantSearchTableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-search me-2"></i>No se encontraron resultados</td></tr>';
            }
        } else {
            // Vista Grid (usando el diseño existente)
            const gridContainer = instantSearchGrid.querySelector('.elements-grid-compact');
            gridContainer.innerHTML = data.inventarios.map(inventario => createInventoryCard(inventario)).join('');
            
            // Vista Tabla (usando el diseño existente)
            if (instantSearchTableBody) {
                instantSearchTableBody.innerHTML = data.inventarios.map(inventario => createInventoryTableRow(inventario)).join('');
            }
            
            // Inicializar lazy loading para las nuevas imágenes
            initializeLazyLoading();
        }
        
        instantSearchResults.style.display = 'block';
        
        // Ocultar categorías cuando hay resultados de búsqueda
        const categoriasSection = document.querySelector('.accordion');
        if (categoriasSection) {
            categoriasSection.style.display = 'none';
        }
        
        // Configurar navegación para los nuevos elementos
        setTimeout(() => {
            refreshDetailNavigation();
        }, 50);
    }
    
    function createInventoryCard(inventario) {
        const ubicacionesHtml = inventario.ubicaciones.slice(0, 3).map(ubicacion => {
            const estadoClass = ubicacion.estado.replace(/\s+/g, '-').toLowerCase();
            const estadoIcon = getEstadoIcon(ubicacion.estado);
            
            return `
                <div class="location-compact">
                    <div class="location-info-compact">
                        <span class="location-name-compact">${ubicacion.ubicacion_nombre}</span>
                        <span class="location-quantity-compact">${ubicacion.cantidad} unidades</span>
                    </div>
                    <div class="status-badge-element-new status-${estadoClass}">
                        <i class="status-icon-compact ${estadoIcon}"></i>
                        <span class="status-text-compact">${ubicacion.estado.charAt(0).toUpperCase() + ubicacion.estado.slice(1).replace('_', ' ')}</span>
                    </div>
                </div>
            `;
        }).join('');
        
        const moreLocations = inventario.ubicaciones.length > 3 ? 
            `<div class="more-locations-compact clickeable" onclick="expandLocations(this, ${JSON.stringify(inventario.ubicaciones.slice(3)).replace(/"/g, '&quot;')})">
                <i class="fas fa-plus-circle me-1"></i>
                ${inventario.ubicaciones.length - 3} ubicaciones más
            </div>` : '';
        
        return `
            <div class="element-card-compact">
                <div class="element-image-compact">
                    ${inventario.imagen_principal ? 
                        `<img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Crect width='100' height='100' fill='%23f3f4f6'/%3E%3C/svg%3E" 
                             data-src="${inventario.imagen_principal}" 
                             alt="${inventario.nombre}" 
                             class="element-thumbnail-compact lazy-load">` :
                        `<div class="image-placeholder-compact"><i class="fas fa-image"></i></div>`
                    }
                </div>
                
                <div class="element-content-compact">
                    <div class="element-header-compact">
                        <h6 class="element-name-compact">${inventario.nombre}</h6>
                        <code class="element-code-compact">${inventario.codigo_unico}</code>
                    </div>
                    
                    <div class="element-details-compact">
                        <div class="detail-row-compact">
                            <span class="detail-label-compact">Marca/Modelo:</span>
                            <span class="detail-value-compact">
                                ${(inventario.marca || inventario.modelo) ? 
                                    `${inventario.marca || ''}${inventario.marca && inventario.modelo ? ' - ' : ''}${inventario.modelo || ''}` :
                                    'N/A'
                                }
                            </span>
                        </div>
                        <div class="detail-row-compact">
                            <span class="detail-label-compact">Serie:</span>
                            <span class="detail-value-compact">${inventario.numero_serie || 'N/A'}</span>
                        </div>
                    </div>
                    
                    <div class="element-locations-compact">
                        ${ubicacionesHtml}
                        ${moreLocations}
                    </div>
                    
                    <div class="element-actions-compact">
                        <a href="/inventarios/${inventario.id}" class="btn btn-outline-primary btn-xs" title="Ver Detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        ${inventario.permisos && inventario.permisos.puede_editar ? 
                            `<a href="/inventarios/${inventario.id}/edit" class="btn btn-outline-warning btn-xs" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>` : ''
                        }
                        ${inventario.permisos && inventario.permisos.puede_eliminar ? 
                            `<form method="POST" action="/inventarios/${inventario.id}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este elemento?')">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>` : ''
                        }
                    </div>
                </div>
            </div>
        `;
    }
    
    function createInventoryTableRow(inventario) {
        const ubicacionesHtml = inventario.ubicaciones.slice(0, 2).map(ubicacion => {
            const estadoClass = ubicacion.estado.replace(/\s+/g, '-').toLowerCase();
            const estadoIcon = getEstadoIcon(ubicacion.estado);
            
            return `
                <div class="table-location-detailed">
                    <div class="location-info-table">
                        <span class="location-name-table">${ubicacion.ubicacion_nombre}</span>
                    </div>
                    <div class="status-badge-table status-${estadoClass}">
                        <span class="location-quantity-table">${ubicacion.cantidad}</span>
                        <i class="status-icon-table ${estadoIcon}"></i>
                        ${ubicacion.estado.charAt(0).toUpperCase() + ubicacion.estado.slice(1).replace('_', ' ')}
                    </div>
                </div>
            `;
        }).join('');
        
        const moreLocations = inventario.ubicaciones.length > 2 ? 
            `<small class="text-muted more-locations-table clickeable" onclick="expandTableLocations(this, ${JSON.stringify(inventario.ubicaciones.slice(2)).replace(/"/g, '&quot;')})">
                <i class="fas fa-plus-circle me-1"></i>
                +${inventario.ubicaciones.length - 2} más
            </small>` : '';
        
        return `
            <tr>
                <td>
                    <div class="table-image-compact">
                        ${inventario.imagen_principal ? 
                            `<img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Crect width='60' height='60' fill='%23f3f4f6'/%3E%3C/svg%3E" 
                                 data-src="${inventario.imagen_principal}" 
                                 alt="${inventario.nombre}" 
                                 class="table-thumbnail-compact lazy-load">` :
                            `<div class="table-image-placeholder-compact"><i class="fas fa-image"></i></div>`
                        }
                    </div>
                </td>
                <td>
                    <div class="element-name-table">${inventario.nombre}</div>
                </td>
                <td>
                    <code class="table-code-compact">${inventario.codigo_unico}</code>
                </td>
                <td>
                    <div class="table-brand-model">
                        ${(inventario.marca || inventario.modelo) ? 
                            `${inventario.marca || ''}${inventario.marca && inventario.modelo ? ' - ' : ''}${inventario.modelo || ''}` :
                            '<span class="text-muted">N/A</span>'
                        }
                    </div>
                </td>
                <td>
                    <div class="table-serial">
                        ${inventario.numero_serie || 'N/A'}
                    </div>
                </td>
                <td>
                    <div class="table-owner">
                        ${inventario.propietario || '<span class="text-muted">Sin asignar</span>'}
                    </div>
                </td>
                <td>
                    <div class="table-locations-detailed">
                        ${ubicacionesHtml}
                        ${moreLocations}
                    </div>
                </td>
                <td class="text-center">
                    <div class="table-actions-compact">
                        <a href="/inventarios/${inventario.id}" class="btn btn-outline-primary btn-xs" title="Ver Detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        ${inventario.permisos && inventario.permisos.puede_editar ? 
                            `<a href="/inventarios/${inventario.id}/edit" class="btn btn-outline-warning btn-xs" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>` : ''
                        }
                        ${inventario.permisos && inventario.permisos.puede_eliminar ? 
                            `<form method="POST" action="/inventarios/${inventario.id}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este elemento?')">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>` : ''
                        }
                    </div>
                </td>
            </tr>
        `;
    }
    
    function getEstadoIcon(estado) {
        const iconMap = {
            'disponible': 'fas fa-check-circle',
            'en uso': 'fas fa-play-circle', 
            'en mantenimiento': 'fas fa-tools',
            'dado de baja': 'fas fa-ban',
            'robado': 'fas fa-user-secret'
        };
        return iconMap[estado] || 'fas fa-circle';
    }
    
    function hideInstantResults() {
        instantSearchResults.style.display = 'none';
        
        // Mostrar categorías de nuevo
        const categoriasSection = document.querySelector('.accordion');
        if (categoriasSection) {
            categoriasSection.style.display = 'block';
        }
    }
    
    function filterCategoriesOnly() {
        // Ocultar resultados de búsqueda instantánea
        hideInstantResults();
        
        // Mostrar solo la categoría seleccionada
        const selectedCategoryId = currentFilters.categoria_id;
        const allCategoryCards = document.querySelectorAll('.accordion-item');
        
        allCategoryCards.forEach(card => {
            const categoryId = card.getAttribute('data-category-id');
            if (categoryId === selectedCategoryId) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    function showAllCategories() {
        // Mostrar todas las categorías
        const allCategoryCards = document.querySelectorAll('.accordion-item');
        allCategoryCards.forEach(card => {
            card.style.display = 'block';
        });
    }
    
    // ===== FILTROS EN CASCADA =====
    
    toggleFiltersBtn.addEventListener('click', function() {
        const isVisible = filtersPanel.style.display !== 'none';
        
        if (isVisible) {
            filtersPanel.style.display = 'none';
            this.querySelector('.filter-text').textContent = 'Mostrar Filtros';
            this.querySelector('i').className = 'fas fa-filter me-1';
        } else {
            filtersPanel.style.display = 'block';
            this.querySelector('.filter-text').textContent = 'Ocultar Filtros';
            this.querySelector('i').className = 'fas fa-filter-circle-xmark me-1';
            loadInitialFilterData();
        }
    });
    
    function loadInitialFilterData() {
        // Cargar ubicaciones iniciales
        fetch(`{{ route('inventarios.filtros-cascada') }}`)
            .then(response => response.json())
            .then(data => {
                updateUbicacionesSelect(data.ubicaciones || []);
            })
            .catch(error => console.error('Error cargando filtros iniciales:', error));
    }
    
    filterCategoria.addEventListener('change', function() {
        const categoriaId = this.value;
        
        if (categoriaId) {
            // Habilitar filtro de elementos
            filterElemento.disabled = false;
            
            // Cargar elementos de la categoría
            loadCascadeFilters({ categoria_id: categoriaId });
        } else {
            // Deshabilitar filtros dependientes
            filterElemento.disabled = true;
            filterMarca.disabled = true;
            
            // Limpiar opciones
            filterElemento.innerHTML = '<option value="">Selecciona una categoría primero</option>';
            filterMarca.innerHTML = '<option value="">Selecciona un elemento primero</option>';
            
            // Recargar ubicaciones generales
            loadInitialFilterData();
        }
    });
    
    filterElemento.addEventListener('change', function() {
        const elementoNombre = this.value;
        const categoriaId = filterCategoria.value;
        
        if (elementoNombre && categoriaId) {
            // Habilitar filtro de marcas
            filterMarca.disabled = false;
            
            // Siempre cargar marcas para el elemento seleccionado
            loadCascadeFilters({ 
                categoria_id: categoriaId, 
                elemento_nombre: elementoNombre 
            });
        } else if (!elementoNombre && categoriaId) {
            // Si se deselecciona elemento pero hay categoría, cargar elementos de la categoría
            filterMarca.disabled = true;
            filterMarca.innerHTML = '<option value="">Selecciona un elemento primero</option>';
        } else {
            // Deshabilitar filtro de marcas
            filterMarca.disabled = true;
            filterMarca.innerHTML = '<option value="">Selecciona un elemento primero</option>';
        }
    });
    
    function loadCascadeFilters(params) {
        const urlParams = new URLSearchParams(params);
        
        fetch(`{{ route('inventarios.filtros-cascada') }}?${urlParams.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.elementos) {
                    updateElementosSelect(data.elementos);
                }
                
                if (data.marcas) {
                    updateMarcasSelect(data.marcas);
                }
                
                if (data.ubicaciones) {
                    updateUbicacionesSelect(data.ubicaciones);
                }
            })
            .catch(error => console.error('Error cargando filtros en cascada:', error));
    }
    
    function updateElementosSelect(elementos) {
        const currentValue = filterElemento.value;
        filterElemento.innerHTML = '<option value="">Todos los elementos</option>';
        elementos.forEach(elemento => {
            const selected = elemento === currentValue ? 'selected' : '';
            filterElemento.innerHTML += `<option value="${elemento}" ${selected}>${elemento}</option>`;
        });
    }
    
    function updateMarcasSelect(marcas) {
        const currentValue = filterMarca.value;
        filterMarca.innerHTML = '<option value="">Todas las marcas</option>';
        
        let foundCurrentValue = false;
        marcas.forEach(marca => {
            if (marca === currentValue) foundCurrentValue = true;
            const selected = marca === currentValue ? 'selected' : '';
            filterMarca.innerHTML += `<option value="${marca}" ${selected}>${marca}</option>`;
        });
        
        // Si el valor actual no está en las nuevas opciones, limpiar la selección
        if (!foundCurrentValue && currentValue) {
            filterMarca.value = '';
        }
    }
    
    function updateUbicacionesSelect(ubicaciones) {
        filterUbicacion.innerHTML = '<option value="">Todas las ubicaciones</option>';
        ubicaciones.forEach(ubicacion => {
            filterUbicacion.innerHTML += `<option value="${ubicacion.id}">${ubicacion.nombre}</option>`;
        });
    }
    
    // ===== APLICAR Y LIMPIAR FILTROS =====
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            // Actualizar filtros actuales
            currentFilters.categoria_id = filterCategoria ? filterCategoria.value : '';
            currentFilters.elemento_nombre = filterElemento ? filterElemento.value : '';
            currentFilters.marca = filterMarca ? filterMarca.value : '';
            currentFilters.estado = filterEstado ? filterEstado.value : '';
            currentFilters.ubicacion_id = filterUbicacion ? filterUbicacion.value : '';
            
            // Limpiar búsqueda por texto si no hay texto en el campo de búsqueda
            if (!searchInput.value || searchInput.value.trim() === '') {
                currentFilters.search = '';
            }
            
            // Actualizar filtros activos
            updateActiveFilters();
            
            // Si solo hay filtro de categoría, filtrar las categorías mostradas
            if (currentFilters.categoria_id && !currentFilters.search && !currentFilters.elemento_nombre && !currentFilters.marca && !currentFilters.estado && !currentFilters.ubicacion_id) {
                filterCategoriesOnly();
            } else {
                // Si hay otros filtros o búsqueda, usar búsqueda instantánea
                performInstantSearch();
            }
            
            // Guardar estado después de aplicar filtros
            refreshDetailNavigation();
        });
    }
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            // Limpiar todos los filtros
            Object.keys(currentFilters).forEach(key => {
                currentFilters[key] = '';
            });
            
            // Resetear selects
            if (filterCategoria) filterCategoria.value = '';
            if (filterElemento) {
                filterElemento.value = '';
                filterElemento.disabled = true;
            }
            if (filterMarca) {
                filterMarca.value = '';
                filterMarca.disabled = true;
            }
            if (filterEstado) filterEstado.value = '';
            if (filterUbicacion) filterUbicacion.value = '';
            
            // Limpiar búsqueda
            if (searchInput) searchInput.value = '';
            if (clearSearchBtn) clearSearchBtn.style.display = 'none';
            
            // Ocultar filtros activos
            if (activeFiltersContainer) activeFiltersContainer.style.display = 'none';
            
            // Ocultar resultados de búsqueda
            hideInstantResults();
            
            // Mostrar todas las categorías
            showAllCategories();
            
            // Recargar datos iniciales
            loadInitialFilterData();
            
            // Reconfigurar navegación
            refreshDetailNavigation();
        });
    }
    
    function updateActiveFilters() {
        activeFilters = [];
        
        if (currentFilters.categoria_id) {
            const categoriaText = filterCategoria.options[filterCategoria.selectedIndex].text;
            activeFilters.push({ key: 'categoria_id', label: 'Categoría', value: categoriaText });
        }
        
        if (currentFilters.elemento_nombre) {
            activeFilters.push({ key: 'elemento_nombre', label: 'Elemento', value: currentFilters.elemento_nombre });
        }
        
        if (currentFilters.marca) {
            activeFilters.push({ key: 'marca', label: 'Marca', value: currentFilters.marca });
        }
        
        if (currentFilters.estado) {
            activeFilters.push({ key: 'estado', label: 'Estado', value: currentFilters.estado.charAt(0).toUpperCase() + currentFilters.estado.slice(1).replace('_', ' ') });
        }
        
        if (currentFilters.ubicacion_id) {
            const ubicacionText = filterUbicacion.options[filterUbicacion.selectedIndex].text;
            activeFilters.push({ key: 'ubicacion_id', label: 'Ubicación', value: ubicacionText });
        }
        
        displayActiveFilters();
    }
    
    function displayActiveFilters() {
        if (activeFilters.length === 0) {
            activeFiltersContainer.style.display = 'none';
            return;
        }
        
        activeFiltersList.innerHTML = activeFilters.map(filter => `
            <div class="active-filter-tag">
                <span>${filter.label}: ${filter.value}</span>
                <button type="button" class="remove-filter" data-filter="${filter.key}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');
        
        activeFiltersContainer.style.display = 'block';
        
        // Agregar event listeners para remover filtros individuales
        activeFiltersList.querySelectorAll('.remove-filter').forEach(btn => {
            btn.addEventListener('click', function() {
                const filterKey = this.getAttribute('data-filter');
                removeFilter(filterKey);
            });
        });
    }
    
    function removeFilter(filterKey) {
        currentFilters[filterKey] = '';
        
        // Actualizar el select correspondiente
        switch(filterKey) {
            case 'categoria_id':
                filterCategoria.value = '';
                filterCategoria.dispatchEvent(new Event('change'));
                break;
            case 'elemento_nombre':
                filterElemento.value = '';
                filterElemento.dispatchEvent(new Event('change'));
                break;
            case 'marca':
                filterMarca.value = '';
                break;
            case 'estado':
                filterEstado.value = '';
                break;
            case 'ubicacion_id':
                filterUbicacion.value = '';
                break;
        }
        
        updateActiveFilters();
        
        // Realizar nueva búsqueda con filtros actualizados
        performInstantSearch();
    }
    
    // ===== SISTEMA DE TOGGLE GLOBAL =====
    
    let globalViewPreference = localStorage.getItem('global-view-preference') || 'grid';
    
    // Toggle de Vista Global - Aplica a todas las categorías
    const categoryToggleBtns = document.querySelectorAll('.view-toggle-btn-small');
    
    function applyGlobalView(viewType) {
        // Actualizar todos los botones
        categoryToggleBtns.forEach(btn => {
            if (btn.getAttribute('data-view') === viewType) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        // Aplicar vista a todas las categorías
        document.querySelectorAll('[id^="elements-grid-"]').forEach(gridView => {
            gridView.style.display = viewType === 'grid' ? 'block' : 'none';
        });
        
        document.querySelectorAll('[id^="elements-table-"]').forEach(tableView => {
            tableView.style.display = viewType === 'table' ? 'block' : 'none';
        });
        
        // Aplicar vista a resultados de búsqueda
        if (instantSearchGrid) {
            instantSearchGrid.style.display = viewType === 'grid' ? 'block' : 'none';
        }
        if (instantSearchTable) {
            instantSearchTable.style.display = viewType === 'table' ? 'block' : 'none';
        }
        
        // Actualizar botones de resultados de búsqueda
        if (searchGridToggle && searchTableToggle) {
            if (viewType === 'grid') {
                searchGridToggle.classList.add('active');
                searchTableToggle.classList.remove('active');
            } else {
                searchGridToggle.classList.remove('active');
                searchTableToggle.classList.add('active');
            }
        }
        
        // Guardar preferencia global
        globalViewPreference = viewType;
        localStorage.setItem('global-view-preference', viewType);
    }
    
    // Agregar event listeners a todos los botones
    categoryToggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const viewType = this.getAttribute('data-view');
            applyGlobalView(viewType);
        });
    });
    
    // Aplicar vista inicial
    applyGlobalView(globalViewPreference);
    
    // Inicializar lazy loading para imágenes existentes
    initializeLazyLoading();
    
    console.log('Sistema de búsqueda instantánea y filtros en cascada inicializado');
});

// ===== FUNCIÓN GLOBAL PARA EXPANDIR UBICACIONES =====
function expandLocations(element, additionalLocations) {
    const container = element.parentElement;
    
    additionalLocations.forEach(ubicacion => {
        const estadoClass = ubicacion.estado.replace(/\s+/g, '-').toLowerCase();
        const estadoIcon = getEstadoIcon(ubicacion.estado);
        
        const locationDiv = document.createElement('div');
        locationDiv.className = 'location-compact';
        locationDiv.innerHTML = `
            <div class="location-info-compact">
                <span class="location-name-compact">${ubicacion.ubicacion_nombre}</span>
                <span class="location-quantity-compact">${ubicacion.cantidad} unidades</span>
            </div>
            <div class="status-badge-element-new status-${estadoClass}">
                <i class="status-icon-compact ${estadoIcon}"></i>
                <span class="status-text-compact">${ubicacion.estado.charAt(0).toUpperCase() + ubicacion.estado.slice(1).replace('_', ' ')}</span>
            </div>
        `;
        
        container.insertBefore(locationDiv, element);
    });
    
    // Remover el botón "más ubicaciones"
    element.remove();
}

function expandTableLocations(element, additionalLocations) {
    const container = element.parentElement;
    
    additionalLocations.forEach(ubicacion => {
        const estadoClass = ubicacion.estado.replace(/\s+/g, '-').toLowerCase();
        const estadoIcon = getEstadoIcon(ubicacion.estado);
        
        const locationDiv = document.createElement('div');
        locationDiv.className = 'table-location-detailed';
        locationDiv.innerHTML = `
            <div class="location-info-table">
                <span class="location-name-table">${ubicacion.ubicacion_nombre}</span>
            </div>
            <div class="status-badge-table status-${estadoClass}">
                <span class="location-quantity-table">${ubicacion.cantidad}</span>
                <i class="status-icon-table ${estadoIcon}"></i>
                ${ubicacion.estado.charAt(0).toUpperCase() + ubicacion.estado.slice(1).replace('_', ' ')}
            </div>
        `;
        
        container.insertBefore(locationDiv, element);
    });
    
    // Remover el botón "más ubicaciones"
    element.remove();
}

function getEstadoIcon(estado) {
    switch(estado) {
        case 'disponible': return 'fas fa-check-circle';
        case 'en uso': return 'fas fa-play-circle';
        case 'en mantenimiento': return 'fas fa-tools';
        case 'dado de baja': return 'fas fa-ban';
        case 'robado': return 'fas fa-user-secret';
        default: return 'fas fa-question-circle';
    }
}

// ===== LAZY LOADING DE IMÁGENES =====
function initializeLazyLoading() {
    // Verificar si el navegador soporta Intersection Observer
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.getAttribute('data-src');
                    
                    if (src) {
                        // Crear una nueva imagen para precargar
                        const newImg = new Image();
                        newImg.onload = function() {
                            // Una vez cargada, reemplazar el src
                            img.src = src;
                            img.classList.remove('lazy-load');
                            img.classList.add('loaded');
                        };
                        newImg.onerror = function() {
                            // Si falla la carga, mostrar placeholder
                            img.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Crect width='100' height='100' fill='%23e5e7eb'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%239ca3af'%3E❌%3C/text%3E%3C/svg%3E";
                            img.classList.remove('lazy-load');
                            img.classList.add('error');
                        };
                        newImg.src = src;
                    }
                    
                    // Dejar de observar esta imagen
                    observer.unobserve(img);
                }
            });
        }, {
            // Cargar imágenes 100px antes de que entren en el viewport
            rootMargin: '100px 0px',
            threshold: 0.01
        });

        // Observar todas las imágenes con lazy loading
        document.querySelectorAll('img.lazy-load').forEach(img => {
            imageObserver.observe(img);
        });
    } else {
        // Fallback para navegadores sin soporte
        document.querySelectorAll('img.lazy-load').forEach(img => {
            const src = img.getAttribute('data-src');
            if (src) {
                img.src = src;
                img.classList.remove('lazy-load');
            }
        });
    }
}
</script>
@endpush
 