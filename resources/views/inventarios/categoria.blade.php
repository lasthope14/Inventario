@extends('layouts.app')

@section('content')
<div class="container-fluid" data-page="inventarios-categoria">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('inventarios.index') }}">Inventario</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $categoria->nombre }}</li>
        </ol>
    </nav>

    <!-- Header Profesional -->
    <div class="header-card mb-4">
        <div class="header-main">
            <div class="header-title-section">
                <div class="header-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="header-text">
                    <h1 class="header-title">{{ $categoria->nombre }}</h1>
                    <div class="header-badges">
                        <span class="header-badge header-badge-primary">
                            <i class="fas fa-cube"></i>
                            {{ $stats->total_elementos ?? 0 }} Elementos
                        </span>
                        <span class="header-badge header-badge-secondary">
                            <i class="fas fa-boxes"></i>
                            {{ $stats->total_unidades ?? 0 }} Unidades
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="header-stats">
                <div class="stat-item">
                    <div class="stat-icon stat-icon-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats->disponibles ?? 0 }}</div>
                        <div class="stat-label">Disponibles</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon stat-icon-primary">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats->en_uso ?? 0 }}</div>
                        <div class="stat-label">En Uso</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon stat-icon-warning">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats->en_mantenimiento ?? 0 }}</div>
                        <div class="stat-label">Mantenimiento</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon stat-icon-danger">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats->dados_de_baja ?? 0 }}</div>
                        <div class="stat-label">Dados de Baja</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon stat-icon-dark">
                        <i class="fas fa-user-secret"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats->robados ?? 0 }}</div>
                        <div class="stat-label">Robados</div>
                    </div>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('inventarios.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Agregar Elemento
                </a>
            </div>
        </div>
    </div>

    <!-- Sistema de Búsqueda y Filtros Avanzados -->
    <div class="advanced-search-container mb-4">
        <div class="search-header">
            <h3 class="search-title">
                <i class="fas fa-search"></i>
                Búsqueda y Filtros
            </h3>
            <button type="button" class="toggle-filters" id="toggleFilters">
                <i class="fas fa-sliders-h"></i>
                <span class="filter-text">Mostrar Filtros</span>
            </button>
        </div>
        
        <div class="main-search-container">
            <div class="search-input-group">
                <div class="search-input-wrapper">
                    <input type="text" 
                           class="search-input-modern" 
                           id="searchInput" 
                           placeholder="Buscar elementos por nombre, código, marca, modelo, serie..." 
                           autocomplete="off">
                    <div class="search-icon">
                        <i class="fas fa-search"></i>
                    </div>
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
        
        <div class="filters-panel" id="filtersPanel" style="display: none;">
            <div class="filters-grid">
                <div class="filter-item">
                    <label class="filter-label">
                        <i class="fas fa-tag"></i>
                        Elemento
                    </label>
                    <select class="filter-select" id="filterElemento">
                        <option value="">Todos los elementos</option>
                        @foreach($elementos ?? [] as $elemento)
                            <option value="{{ $elemento }}">{{ $elemento }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-item">
                    <label class="filter-label">
                        <i class="fas fa-industry"></i>
                        Marca
                    </label>
                    <select class="filter-select" id="filterMarca">
                        <option value="">Todas las marcas</option>
                        @foreach($marcas ?? [] as $marca)
                            <option value="{{ $marca }}">{{ $marca }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-item">
                    <label class="filter-label">
                        <i class="fas fa-info-circle"></i>
                        Estado
                    </label>
                    <select class="filter-select" id="filterEstado">
                        <option value="">Todos los estados</option>
                        <option value="disponible">Disponible</option>
                        <option value="en_uso">En Uso</option>
                        <option value="en_mantenimiento">En Mantenimiento</option>
                        <option value="dado_de_baja">Dado de Baja</option>
                        <option value="robado">Robado</option>
                    </select>
                </div>
                
                <div class="filter-item">
                    <label class="filter-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Ubicación
                    </label>
                    <select class="filter-select" id="filterUbicacion">
                        <option value="">Todas las ubicaciones</option>
                        @foreach($ubicaciones ?? [] as $ubicacion)
                            <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-actions">
                    <button type="button" class="btn btn-primary" id="applyFilters">
                        <i class="fas fa-filter"></i>
                        Aplicar Filtros
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="clearFilters">
                        <i class="fas fa-times"></i>
                        Limpiar Todo
                    </button>
                </div>
            </div>
        </div>
        
        <div class="active-filters" id="activeFilters" style="display: none;">
            <div class="active-filters-header">
                <span class="active-filters-title">Filtros Activos:</span>
            </div>
            <div class="active-filters-list" id="activeFiltersList"></div>
        </div>
    </div>

    <!-- Resultados de Búsqueda Instantánea -->
    <div class="search-results-section" id="instantSearchResults" style="display: none;">
        <div class="search-results-header">
            <div class="search-results-info">
                <h4 class="search-results-title">
                    <i class="fas fa-search"></i>
                    Resultados de búsqueda <span id="searchTermDisplay"></span>
                </h4>
            </div>
            <div class="search-results-controls">
                <div class="view-toggle-buttons-small">
                    <button type="button" class="view-toggle-btn-small active" id="searchGridToggle" data-view="grid">
                        <i class="fas fa-th"></i>
                        Tarjetas
                    </button>
                    <button type="button" class="view-toggle-btn-small" id="searchTableToggle" data-view="table">
                        <i class="fas fa-list"></i>
                        Tabla
                    </button>
                </div>
            </div>
        </div>
        
        <div class="search-results-grid" id="instantSearchGrid"></div>
        
        <div class="search-results-table" id="instantSearchTable" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Elemento</th>
                            <th>Código</th>
                            <th>Marca/Modelo</th>
                            <th>Serie</th>
                            <th>Propietario</th>
                            <th>Ubicaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="instantSearchTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Vista de Elementos de la Categoría -->
    <div class="category-elements-section">
        <div class="category-elements-header">
            <div class="category-elements-info">
                <h4 class="category-elements-title">
                    <i class="fas fa-layer-group"></i>
                    Elementos de {{ $categoria->nombre }}
                </h4>
                <p class="category-elements-subtitle">
                    Mostrando {{ $inventarios->firstItem() ?? 0 }} - {{ $inventarios->lastItem() ?? 0 }} 
                    de {{ $inventarios->total() }} elementos
                </p>
            </div>
            <div class="category-elements-controls">
                <div class="view-toggle-buttons">
                    <button type="button" class="view-toggle-btn active" data-view="grid">
                        <i class="fas fa-th"></i>
                        Tarjetas
                    </button>
                    <button type="button" class="view-toggle-btn" data-view="table">
                        <i class="fas fa-list"></i>
                        Tabla
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Vista Grid -->
        <div class="elements-grid-view" id="elements-grid-category">
            <div class="elements-grid-compact">
                @foreach($inventarios as $inventario)
                    <div class="element-card-compact">
                        <div class="element-image-compact">
                            @if($inventario->imagen)
                                <img src="{{ asset('storage/' . $inventario->imagen) }}" 
                                     alt="{{ $inventario->nombre }}" 
                                     class="lazy-load" 
                                     data-src="{{ asset('storage/' . $inventario->imagen) }}">
                            @else
                                <div class="image-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </div>
                        <div class="element-content-compact">
                            <div class="element-header-compact">
                                <h6 class="element-name-compact">{{ $inventario->nombre }}</h6>
                                <span class="element-code-compact">{{ $inventario->codigo }}</span>
                            </div>
                            <div class="element-details-compact">
                                <div class="detail-row-compact">
                                    <span class="detail-label-compact">Marca/Modelo:</span>
                                    <span class="detail-value-compact">{{ $inventario->marca }} {{ $inventario->modelo }}</span>
                                </div>
                                <div class="detail-row-compact">
                                    <span class="detail-label-compact">Serie:</span>
                                    <span class="detail-value-compact">{{ $inventario->numero_serie }}</span>
                                </div>
                                <div class="detail-row-compact">
                                    <span class="detail-label-compact">Propietario:</span>
                                    <span class="detail-value-compact">{{ $inventario->propietario ?? 'No asignado' }}</span>
                                </div>
                            </div>
                            <div class="element-locations-compact">
                                @php
                                    $ubicaciones = $inventario->ubicaciones ?? collect();
                                    $ubicacionesLimitadas = $ubicaciones->take(2);
                                    $ubicacionesAdicionales = $ubicaciones->skip(2);
                                @endphp
                                
                                @foreach($ubicacionesLimitadas as $ubicacion)
                                    <div class="location-compact">
                                        <div class="location-info-compact">
                                            <span class="location-name-compact">{{ $ubicacion->nombre ?? 'Ubicación no definida' }}</span>
                                            <span class="location-quantity-compact">{{ $ubicacion->pivot ? ($ubicacion->pivot->cantidad ?? 1) : 1 }} unidades</span>
                                        </div>
                                        @php
                                            $estado = $ubicacion->pivot ? ($ubicacion->pivot->estado ?? 'disponible') : 'disponible';
                                        @endphp
                                        <div class="status-badge-element-new status-{{ str_replace(' ', '-', strtolower($estado)) }}">
                                            <i class="status-icon-compact {{ $estado == 'disponible' ? 'fas fa-check-circle' : ($estado == 'en_uso' ? 'fas fa-play-circle' : ($estado == 'en_mantenimiento' ? 'fas fa-tools' : ($estado == 'dado_de_baja' ? 'fas fa-ban' : 'fas fa-user-secret'))) }}"></i>
                                            <span class="status-text-compact">{{ ucfirst(str_replace('_', ' ', $estado)) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($ubicacionesAdicionales->count() > 0)
                                    <div class="more-locations-compact" onclick="expandLocations(this, {{ $ubicacionesAdicionales->toJson() }})">
                                        +{{ $ubicacionesAdicionales->count() }} ubicaciones más
                                    </div>
                                @endif
                            </div>
                            <div class="element-actions-compact">
                                <a href="{{ route('inventarios.show', $inventario->id) }}" class="btn btn-outline-primary btn-xs">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="{{ route('inventarios.edit', $inventario->id) }}" class="btn btn-outline-secondary btn-xs">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Vista Tabla -->
        <div class="elements-table-view" id="elements-table-category" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Elemento</th>
                            <th>Código</th>
                            <th>Marca/Modelo</th>
                            <th>Serie</th>
                            <th>Propietario</th>
                            <th>Ubicaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventarios as $inventario)
                            <tr>
                                <td>
                                    @if($inventario->imagen)
                                        <img src="{{ asset('storage/' . $inventario->imagen) }}" 
                                             alt="{{ $inventario->nombre }}" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <div style="width: 50px; height: 50px; background: #f3f4f6; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-element-info">
                                        <h6 class="table-element-name">{{ $inventario->nombre }}</h6>
                                        <span class="table-element-code">{{ $inventario->codigo }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="table-serial">{{ $inventario->codigo }}</span>
                                </td>
                                <td>
                                    <div class="table-brand-model">
                                        <span class="table-brand">{{ $inventario->marca }}</span>
                                        <span class="table-model">{{ $inventario->modelo }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="table-serial">{{ $inventario->numero_serie }}</span>
                                </td>
                                <td>{{ $inventario->propietario ?? 'No asignado' }}</td>
                                <td>
                                    <div class="table-locations">
                                        @php
                                            $ubicaciones = $inventario->ubicaciones ?? collect();
                                            $ubicacionesLimitadas = $ubicaciones->take(2);
                                            $ubicacionesAdicionales = $ubicaciones->skip(2);
                                        @endphp
                                        
                                        @foreach($ubicacionesLimitadas as $ubicacion)
                                            <div class="table-location-item">
                                                <div class="table-location-info">
                                                    <span class="table-location-name">{{ $ubicacion->nombre ?? 'Ubicación no definida' }}</span>
                                                </div>
                                                @php
                                                    $estadoTable = $ubicacion->pivot ? ($ubicacion->pivot->estado ?? 'disponible') : 'disponible';
                                                    $cantidadTable = $ubicacion->pivot ? ($ubicacion->pivot->cantidad ?? 1) : 1;
                                                @endphp
                                                <div class="table-status-badge status-{{ str_replace(' ', '-', strtolower($estadoTable)) }}">
                                                    <span class="table-quantity">{{ $cantidadTable }}</span>
                                                    <i class="table-status-icon {{ $estadoTable == 'disponible' ? 'fas fa-check-circle' : ($estadoTable == 'en_uso' ? 'fas fa-play-circle' : ($estadoTable == 'en_mantenimiento' ? 'fas fa-tools' : ($estadoTable == 'dado_de_baja' ? 'fas fa-ban' : 'fas fa-user-secret'))) }}"></i>
                                                    <span class="table-status-text">{{ ucfirst(str_replace('_', ' ', $estadoTable)) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        @if($ubicacionesAdicionales->count() > 0)
                                            <div class="more-locations-table" onclick="expandTableLocations(this, {{ $ubicacionesAdicionales->toJson() }})">
                                                <i class="fas fa-plus-circle"></i> Ver {{ $ubicacionesAdicionales->count() }} ubicaciones más
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('inventarios.show', $inventario->id) }}" class="btn btn-outline-primary btn-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('inventarios.edit', $inventario->id) }}" class="btn btn-outline-secondary btn-xs">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Paginación -->
        @if($inventarios->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $inventarios->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== VARIABLES GLOBALES =====
    let searchTimeout;
    let globalViewPreference = localStorage.getItem('global-view-preference') || 'grid';
    
    // ===== ELEMENTOS DEL DOM =====
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const toggleFiltersBtn = document.getElementById('toggleFilters');
    const filtersPanel = document.getElementById('filtersPanel');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const activeFiltersContainer = document.getElementById('activeFilters');
    const searchResultsSection = document.getElementById('instantSearchResults');
    const instantSearchGrid = document.getElementById('instantSearchGrid');
    const instantSearchTable = document.getElementById('instantSearchTable');
    const searchGridToggle = document.getElementById('searchGridToggle');
    const searchTableToggle = document.getElementById('searchTableToggle');
    
    // Botones de vista para elementos de categoría
    const categoryToggleBtns = document.querySelectorAll('.view-toggle-btn');
    const elementsGridView = document.getElementById('elements-grid-category');
    const elementsTableView = document.getElementById('elements-table-category');
    
    // ===== FUNCIONES DE BÚSQUEDA =====
    function performInstantSearch() {
        const searchTerm = searchInput.value.trim();
        const elementFilter = document.getElementById('filterElemento').value;
        const brandFilter = document.getElementById('filterMarca').value;
        const statusFilter = document.getElementById('filterEstado').value;
        const locationFilter = document.getElementById('filterUbicacion').value;
        
        if (searchTerm.length === 0 && !elementFilter && !brandFilter && !statusFilter && !locationFilter) {
            hideSearchResults();
            return;
        }
        
        // Mostrar loading
        showSearchLoading();
        
        // Simular búsqueda (aquí iría la llamada AJAX real)
        setTimeout(() => {
            const mockResults = generateMockResults(searchTerm);
            displaySearchResults(mockResults);
        }, 300);
    }
    
    function generateMockResults(searchTerm) {
        // Esta función simula resultados de búsqueda
        // En la implementación real, esto sería una llamada AJAX al servidor
        return {
            elements: [
                {
                    id: 1,
                    codigo: 'LAP001',
                    nombre: 'Laptop Dell Inspiron',
                    categoria: '{{ $categoria->nombre }}',
                    marca: 'Dell',
                    modelo: 'Inspiron 15 3000',
                    serie: 'DL123456',
                    propietario: 'Juan Pérez',
                    imagen: '/images/laptop-dell.jpg',
                    ubicaciones: [
                        { ubicacion_nombre: 'Oficina Principal', cantidad: 1, estado: 'en uso' },
                        { ubicacion_nombre: 'Almacén', cantidad: 2, estado: 'disponible' }
                    ]
                }
            ],
            total: 1
        };
    }
    
    function displaySearchResults(results) {
        if (results.total === 0) {
            showNoResults();
            return;
        }
        
        // Mostrar sección de resultados
        searchResultsSection.style.display = 'block';
        
        // Actualizar título
        document.getElementById('searchTermDisplay').textContent = 
            `(${results.total} elementos)`;
        
        // Generar HTML para grid
        const gridHTML = generateGridHTML(results.elements);
        instantSearchGrid.innerHTML = gridHTML;
        
        // Generar HTML para tabla
        document.getElementById('instantSearchTableBody').innerHTML = generateTableBodyHTML(results.elements);
        
        // Aplicar vista actual
        applyCurrentView();
        
        // Inicializar lazy loading para nuevas imágenes
        initializeLazyLoading();
    }
    
    function generateGridHTML(elements) {
        return `
            <div class="elements-grid-compact">
                ${elements.map(element => `
                    <div class="element-card-compact">
                        <div class="element-image-compact">
                            <img src="${element.imagen}" alt="${element.nombre}" class="lazy-load" data-src="${element.imagen}">
                        </div>
                        <div class="element-content-compact">
                            <div class="element-header-compact">
                                <h6 class="element-name-compact">${element.nombre}</h6>
                                <span class="element-code-compact">${element.codigo}</span>
                            </div>
                            <div class="element-details-compact">
                                <div class="detail-row-compact">
                                    <span class="detail-label-compact">Marca/Modelo:</span>
                                    <span class="detail-value-compact">${element.marca} ${element.modelo}</span>
                                </div>
                                <div class="detail-row-compact">
                                    <span class="detail-label-compact">Serie:</span>
                                    <span class="detail-value-compact">${element.serie}</span>
                                </div>
                                <div class="detail-row-compact">
                                    <span class="detail-label-compact">Propietario:</span>
                                    <span class="detail-value-compact">${element.propietario}</span>
                                </div>
                            </div>
                            <div class="element-locations-compact">
                                ${element.ubicaciones.slice(0, 2).map(ubicacion => `
                                    <div class="location-compact">
                                        <div class="location-info-compact">
                                            <span class="location-name-compact">${ubicacion.ubicacion_nombre}</span>
                                            <span class="location-quantity-compact">${ubicacion.cantidad} unidades</span>
                                        </div>
                                        <div class="status-badge-element-new status-${ubicacion.estado.replace(/\s+/g, '-')}">
                                            <i class="status-icon-compact ${getEstadoIcon(ubicacion.estado)}"></i>
                                            <span class="status-text-compact">${ubicacion.estado.charAt(0).toUpperCase() + ubicacion.estado.slice(1).replace('_', ' ')}</span>
                                        </div>
                                    </div>
                                `).join('')}
                                ${element.ubicaciones.length > 2 ? `
                                    <div class="more-locations-compact" onclick="expandLocations(this, ${JSON.stringify(element.ubicaciones.slice(2)).replace(/"/g, '&quot;')})">
                                        +${element.ubicaciones.length - 2} ubicaciones más
                                    </div>
                                ` : ''}
                            </div>
                            <div class="element-actions-compact">
                                <a href="/inventarios/${element.id}" class="btn btn-outline-primary btn-xs">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="/inventarios/${element.id}/edit" class="btn btn-outline-secondary btn-xs">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }
    
    function generateTableBodyHTML(elements) {
        return elements.map(element => `
            <tr>
                <td>
                    <img src="${element.imagen}" alt="${element.nombre}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                </td>
                <td>
                    <div class="table-element-info">
                        <h6 class="table-element-name">${element.nombre}</h6>
                        <span class="table-element-code">${element.codigo}</span>
                    </div>
                </td>
                <td>
                    <span class="table-serial">${element.codigo}</span>
                </td>
                <td>
                    <div class="table-brand-model">
                        <span class="table-brand">${element.marca}</span>
                        <span class="table-model">${element.modelo}</span>
                    </div>
                </td>
                <td>
                    <span class="table-serial">${element.serie}</span>
                </td>
                <td>${element.propietario}</td>
                <td>
                    <div class="table-locations">
                        ${element.ubicaciones.slice(0, 2).map(ubicacion => `
                            <div class="table-location-item">
                                <div class="table-location-info">
                                    <span class="table-location-name">${ubicacion.ubicacion_nombre}</span>
                                </div>
                                <div class="table-status-badge status-${ubicacion.estado.replace(/\s+/g, '-')}">
                                    <span class="table-quantity">${ubicacion.cantidad}</span>
                                    <i class="table-status-icon ${getEstadoIcon(ubicacion.estado)}"></i>
                                    <span class="table-status-text">${ubicacion.estado.charAt(0).toUpperCase() + ubicacion.estado.slice(1).replace('_', ' ')}</span>
                                </div>
                            </div>
                        `).join('')}
                        ${element.ubicaciones.length > 2 ? `
                            <div class="more-locations-table" onclick="expandTableLocations(this, ${JSON.stringify(element.ubicaciones.slice(2)).replace(/"/g, '&quot;')})">
                                <i class="fas fa-plus-circle"></i> Ver ${element.ubicaciones.length - 2} ubicaciones más
                            </div>
                        ` : ''}
                    </div>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="/inventarios/${element.id}" class="btn btn-outline-primary btn-xs">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/inventarios/${element.id}/edit" class="btn btn-outline-secondary btn-xs">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `).join('');
    }
    
    function showSearchLoading() {
        searchResultsSection.style.display = 'block';
        document.getElementById('searchTermDisplay').textContent = 'Buscando...';
        instantSearchGrid.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Cargando resultados...</div>';
        document.getElementById('instantSearchTableBody').innerHTML = '<tr><td colspan="8" class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Cargando resultados...</td></tr>';
    }
    
    function showNoResults() {
        searchResultsSection.style.display = 'block';
        document.getElementById('searchTermDisplay').textContent = 'Sin resultados';
        const noResultsHTML = '<div class="text-center p-4"><i class="fas fa-search"></i><br>No se encontraron elementos que coincidan con tu búsqueda.</div>';
        instantSearchGrid.innerHTML = noResultsHTML;
        document.getElementById('instantSearchTableBody').innerHTML = '<tr><td colspan="8" class="text-center p-4"><i class="fas fa-search"></i><br>No se encontraron elementos que coincidan con tu búsqueda.</td></tr>';
    }
    
    function hideSearchResults() {
        searchResultsSection.style.display = 'none';
    }
    
    function applyCurrentView() {
        const currentView = globalViewPreference;
        if (currentView === 'grid') {
            instantSearchGrid.style.display = 'block';
            instantSearchTable.style.display = 'none';
            if (searchGridToggle) searchGridToggle.classList.add('active');
            if (searchTableToggle) searchTableToggle.classList.remove('active');
        } else {
            instantSearchGrid.style.display = 'none';
            instantSearchTable.style.display = 'block';
            if (searchGridToggle) searchGridToggle.classList.remove('active');
            if (searchTableToggle) searchTableToggle.classList.add('active');
        }
    }
    
    // ===== EVENT LISTENERS =====
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performInstantSearch, 300);
            
            // Mostrar/ocultar botón de limpiar
            if (this.value.length > 0) {
                clearSearchBtn.style.display = 'flex';
            } else {
                clearSearchBtn.style.display = 'none';
            }
        });
    }
    
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            this.style.display = 'none';
            hideSearchResults();
        });
    }
    
    if (toggleFiltersBtn) {
        toggleFiltersBtn.addEventListener('click', function() {
            const isVisible = filtersPanel.style.display !== 'none';
            filtersPanel.style.display = isVisible ? 'none' : 'block';
            this.classList.toggle('active', !isVisible);
            
            const filterText = this.querySelector('.filter-text');
            if (filterText) {
                filterText.textContent = isVisible ? 'Mostrar Filtros' : 'Ocultar Filtros';
            }
        });
    }
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', performInstantSearch);
    }
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            document.querySelectorAll('.filter-select').forEach(select => {
                select.value = '';
            });
            hideSearchResults();
        });
    }
    
    // Event listeners para botones de vista de resultados de búsqueda
    if (searchGridToggle) {
        searchGridToggle.addEventListener('click', function() {
            applyGlobalView('grid');
        });
    }
    
    if (searchTableToggle) {
        searchTableToggle.addEventListener('click', function() {
            applyGlobalView('table');
        });
    }
    
    // Event listeners para botones de vista de elementos de categoría
    categoryToggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const viewType = this.getAttribute('data-view');
            applyCategoryView(viewType);
        });
    });
    
    // ===== SISTEMA DE VISTA GLOBAL =====
    function applyGlobalView(viewType) {
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
        
        // Aplicar también a la vista de categoría
        applyCategoryView(viewType);
        
        // Guardar preferencia global
        globalViewPreference = viewType;
        localStorage.setItem('global-view-preference', viewType);
    }
    
    function applyCategoryView(viewType) {
        // Actualizar botones de vista de categoría
        categoryToggleBtns.forEach(btn => {
            if (btn.getAttribute('data-view') === viewType) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        // Aplicar vista a elementos de categoría
        if (elementsGridView && elementsTableView) {
            if (viewType === 'grid') {
                elementsGridView.style.display = 'block';
                elementsTableView.style.display = 'none';
            } else {
                elementsGridView.style.display = 'none';
                elementsTableView.style.display = 'block';
            }
        }
    }
    
    // Aplicar vista inicial
    applyCategoryView(globalViewPreference);
    
    // Inicializar lazy loading para imágenes existentes
    initializeLazyLoading();
    
    console.log('Sistema de búsqueda instantánea y filtros para categoría inicializado');
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
        locationDiv.className = 'table-location-item';
        locationDiv.innerHTML = `
            <div class="table-location-info">
                <span class="table-location-name">${ubicacion.ubicacion_nombre}</span>
            </div>
            <div class="table-status-badge status-${estadoClass}">
                <span class="table-quantity">${ubicacion.cantidad}</span>
                <i class="table-status-icon ${estadoIcon}"></i>
                <span class="table-status-text">${ubicacion.estado.charAt(0).toUpperCase() + ubicacion.estado.slice(1).replace('_', ' ')}</span>
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

@push('scripts')
<script>
// Variables globales
let currentView = localStorage.getItem('categoryView') || 'grid';
let searchTimeout;
let isSearching = false;
let currentFilters = {
    search: '',
    elemento: '',
    marca: '',
    estado: '',
    ubicacion: ''
};

// Referencias a elementos del DOM
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');
const categoryElements = document.getElementById('categoryElements');
const filtersPanel = document.getElementById('filtersPanel');
const activeFiltersContainer = document.getElementById('activeFilters');
const toggleFiltersBtn = document.getElementById('toggleFilters');
const clearSearchBtn = document.getElementById('clearSearch');
const searchLoading = document.getElementById('searchLoading');
const viewToggleButtons = document.querySelectorAll('.view-toggle-btn');
const viewToggleButtonsSmall = document.querySelectorAll('.view-toggle-btn-small');

// Función de búsqueda instantánea
function performInstantSearch(query) {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    searchTimeout = setTimeout(() => {
        if (query.trim() === '') {
            hideSearchResults();
            return;
        }
        
        showSearchLoading();
        
        // Simular búsqueda con delay
        setTimeout(() => {
            const results = generateMockResults(query);
            displaySearchResults(results, query);
            hideSearchLoading();
        }, 300);
    }, 300);
}

// Generar resultados mock
function generateMockResults(query) {
    const mockResults = [
        {
            id: 1,
            nombre: 'Laptop Dell Inspiron 15',
            codigo: 'LAP-001',
            categoria: 'Computadoras',
            marca: 'Dell',
            modelo: 'Inspiron 15 3000',
            estado: 'disponible',
            ubicaciones: [
                { nombre: 'Oficina Principal - Piso 2', cantidad: 1 },
                { nombre: 'Almacén Central', cantidad: 2 }
            ],
            imagen: '/images/laptop-dell.jpg'
        },
        {
            id: 2,
            nombre: 'Monitor Samsung 24"',
            codigo: 'MON-002',
            categoria: 'Monitores',
            marca: 'Samsung',
            modelo: 'S24F350',
            estado: 'en-uso',
            ubicaciones: [
                { nombre: 'Sala de Conferencias A', cantidad: 2 }
            ],
            imagen: '/images/monitor-samsung.jpg'
        },
        {
            id: 3,
            nombre: 'Impresora HP LaserJet',
            codigo: 'IMP-003',
            categoria: 'Impresoras',
            marca: 'HP',
            modelo: 'LaserJet Pro M404n',
            estado: 'en-mantenimiento',
            ubicaciones: [
                { nombre: 'Centro de Servicios', cantidad: 1 }
            ],
            imagen: '/images/impresora-hp.jpg'
        }
    ];
    
    return mockResults.filter(item => 
        item.nombre.toLowerCase().includes(query.toLowerCase()) ||
        item.codigo.toLowerCase().includes(query.toLowerCase()) ||
        item.marca.toLowerCase().includes(query.toLowerCase())
    );
}

// Mostrar resultados de búsqueda
function displaySearchResults(results, query) {
    if (results.length === 0) {
        showNoResults(query);
        return;
    }
    
    const resultsHtml = currentView === 'grid' ? 
        generateGridResults(results) : 
        generateTableResults(results);
    
    searchResults.innerHTML = `
        <div class="search-results-section">
            <div class="search-results-header">
                <h3 class="search-results-title">
                    <i class="fas fa-search"></i>
                    Resultados para "${query}" (${results.length})
                </h3>
                <div class="view-toggle-buttons-small">
                    <button class="view-toggle-btn-small ${currentView === 'grid' ? 'active' : ''}" data-view="grid">
                        <i class="fas fa-th"></i> Cuadrícula
                    </button>
                    <button class="view-toggle-btn-small ${currentView === 'table' ? 'active' : ''}" data-view="table">
                        <i class="fas fa-list"></i> Tabla
                    </button>
                </div>
            </div>
            ${resultsHtml}
        </div>
    `;
    
    searchResults.style.display = 'block';
    categoryElements.style.display = 'none';
    
    // Agregar event listeners a los botones de vista
    const newViewButtons = searchResults.querySelectorAll('.view-toggle-btn-small');
    newViewButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const view = e.currentTarget.dataset.view;
            setView(view);
            displaySearchResults(results, query); // Refrescar con nueva vista
        });
    });
    
    // Inicializar lazy loading
    initializeLazyLoading();
}

// Generar vista de cuadrícula
function generateGridResults(results) {
    return `
        <div class="elements-grid-view">
            <div class="elements-grid-compact">
                ${results.map(item => `
                    <div class="element-card-compact">
                        <div class="element-image-compact">
                            ${item.imagen ? 
                                `<img src="${item.imagen}" alt="${item.nombre}" class="lazy-load" loading="lazy">` :
                                `<div class="image-placeholder"><i class="fas fa-image"></i></div>`
                            }
                        </div>
                        <div class="element-content-compact">
                            <div class="element-header-compact">
                                <h4 class="element-name-compact">${item.nombre}</h4>
                                <span class="element-code-compact">${item.codigo}</span>
                            </div>
                            <div class="element-details-compact">
                                <div class="detail-row-compact">
                                    <span class="detail-label-compact">Marca:</span>
                                    <span class="detail-value-compact">${item.marca}</span>
                                </div>
                                <div class="detail-row-compact">
                                    <span class="detail-label-compact">Modelo:</span>
                                    <span class="detail-value-compact">${item.modelo}</span>
                                </div>
                            </div>
                            <div class="element-locations-compact">
                                ${item.ubicaciones.slice(0, 2).map(ubicacion => `
                                    <div class="location-compact">
                                        <div class="location-info-compact">
                                            <span class="location-name-compact">${ubicacion.nombre}</span>
                                            <span class="location-quantity-compact">${ubicacion.cantidad} unidad${ubicacion.cantidad !== 1 ? 'es' : ''}</span>
                                        </div>
                                    </div>
                                `).join('')}
                                ${item.ubicaciones.length > 2 ? `
                                    <div class="more-locations-compact" onclick="expandLocations(this, ${item.id})">
                                        <i class="fas fa-plus"></i>
                                        Ver ${item.ubicaciones.length - 2} ubicación${item.ubicaciones.length - 2 !== 1 ? 'es' : ''} más
                                    </div>
                                ` : ''}
                            </div>
                            <div class="status-badge-element-new status-${item.estado}">
                                <i class="status-icon-compact ${getStatusIcon(item.estado)}"></i>
                                <span class="status-text-compact">${getStatusText(item.estado)}</span>
                            </div>
                            <div class="element-actions-compact">
                                <button class="btn btn-outline-primary btn-xs">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button class="btn btn-outline-secondary btn-xs">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

// Generar vista de tabla
function generateTableResults(results) {
    return `
        <div class="elements-table-view">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Elemento</th>
                            <th>Marca/Modelo</th>
                            <th>Ubicaciones</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${results.map(item => `
                            <tr>
                                <td>
                                    <div class="table-element-info">
                                        <div class="table-element-name">${item.nombre}</div>
                                        <div class="table-element-code">${item.codigo}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-brand-model">
                                        <div class="table-brand">${item.marca}</div>
                                        <div class="table-model">${item.modelo}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-locations">
                                        ${item.ubicaciones.slice(0, 2).map(ubicacion => `
                                            <div class="table-location-item">
                                                <div class="table-location-name">${ubicacion.nombre}</div>
                                                <div class="location-quantity-compact">${ubicacion.cantidad} unidad${ubicacion.cantidad !== 1 ? 'es' : ''}</div>
                                            </div>
                                        `).join('')}
                                        ${item.ubicaciones.length > 2 ? `
                                            <div class="more-locations-table" onclick="expandLocationsTable(this, ${item.id})">
                                                <i class="fas fa-plus"></i>
                                                ${item.ubicaciones.length - 2} más
                                            </div>
                                        ` : ''}
                                    </div>
                                </td>
                                <td>
                                    <div class="table-status-badge status-${item.estado}">
                                        <i class="table-status-icon ${getStatusIcon(item.estado)}"></i>
                                        <span class="table-status-text">${getStatusText(item.estado)}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="element-actions-compact">
                                        <button class="btn btn-outline-primary btn-xs">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary btn-xs">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

// Mostrar estado de carga
function showSearchLoading() {
    if (searchLoading) {
        searchLoading.style.display = 'block';
    }
}

// Ocultar estado de carga
function hideSearchLoading() {
    if (searchLoading) {
        searchLoading.style.display = 'none';
    }
}

// Mostrar mensaje de sin resultados
function showNoResults(query) {
    searchResults.innerHTML = `
        <div class="search-results-section">
            <div class="search-results-header">
                <h3 class="search-results-title">
                    <i class="fas fa-search"></i>
                    Sin resultados para "${query}"
                </h3>
            </div>
            <div class="elements-grid-view">
                <div class="text-center py-5">
                    <i class="fas fa-search text-muted" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <h4 class="text-muted">No se encontraron elementos</h4>
                    <p class="text-muted">Intenta con otros términos de búsqueda</p>
                </div>
            </div>
        </div>
    `;
    
    searchResults.style.display = 'block';
    categoryElements.style.display = 'none';
}

// Ocultar resultados de búsqueda
function hideSearchResults() {
    if (searchResults) {
        searchResults.style.display = 'none';
    }
    if (categoryElements) {
        categoryElements.style.display = 'block';
    }
}

// Aplicar vista actual
function applyCurrentView() {
    // Aplicar a elementos de categoría
    const gridView = document.getElementById('elementsGridView');
    const tableView = document.getElementById('elementsTableView');
    
    if (gridView && tableView) {
        if (currentView === 'grid') {
            gridView.style.display = 'block';
            tableView.style.display = 'none';
        } else {
            gridView.style.display = 'none';
            tableView.style.display = 'block';
        }
    }
    
    // Actualizar botones
    viewToggleButtons.forEach(btn => {
        btn.classList.toggle('active', btn.dataset.view === currentView);
    });
}

// Event Listeners
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value;
        currentFilters.search = query;
        
        if (clearSearchBtn) {
            clearSearchBtn.style.display = query ? 'flex' : 'none';
        }
        
        performInstantSearch(query);
    });
}

if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', () => {
        searchInput.value = '';
        currentFilters.search = '';
        clearSearchBtn.style.display = 'none';
        hideSearchResults();
    });
}

if (toggleFiltersBtn) {
    toggleFiltersBtn.addEventListener('click', () => {
        const isVisible = filtersPanel.style.display !== 'none';
        filtersPanel.style.display = isVisible ? 'none' : 'block';
        toggleFiltersBtn.innerHTML = isVisible ? 
            '<i class="fas fa-filter"></i> Mostrar Filtros' : 
            '<i class="fas fa-filter"></i> Ocultar Filtros';
    });
}

// Event listeners para botones de vista
viewToggleButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        const view = e.currentTarget.dataset.view;
        setView(view);
    });
});

// Sistema de vista global
function setView(view) {
    currentView = view;
    localStorage.setItem('categoryView', view);
    applyCurrentView();
    
    // Si hay resultados de búsqueda activos, refrescarlos
    if (searchResults.style.display === 'block' && searchInput.value.trim()) {
        const results = generateMockResults(searchInput.value);
        displaySearchResults(results, searchInput.value);
    }
}

// Función para expandir ubicaciones (vista compacta)
function expandLocations(element, elementId) {
    // Aquí iría la lógica para expandir ubicaciones
    console.log('Expandir ubicaciones para elemento:', elementId);
}

// Función para expandir ubicaciones (vista tabla)
function expandLocationsTable(element, elementId) {
    // Aquí iría la lógica para expandir ubicaciones en tabla
    console.log('Expandir ubicaciones tabla para elemento:', elementId);
}

// Función para obtener icono de estado
function getStatusIcon(estado) {
    const icons = {
        'disponible': 'fas fa-check-circle',
        'en-uso': 'fas fa-user-circle',
        'en-mantenimiento': 'fas fa-tools',
        'dado-de-baja': 'fas fa-times-circle',
        'robado': 'fas fa-exclamation-triangle'
    };
    return icons[estado] || 'fas fa-question-circle';
}

// Función para obtener texto de estado
function getStatusText(estado) {
    const texts = {
        'disponible': 'Disponible',
        'en-uso': 'En Uso',
        'en-mantenimiento': 'Mantenimiento',
        'dado-de-baja': 'Dado de Baja',
        'robado': 'Robado'
    };
    return texts[estado] || 'Desconocido';
}

// Función para lazy loading de imágenes
function initializeLazyLoading() {
    const lazyImages = document.querySelectorAll('.lazy-load');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.onload = () => img.classList.add('loaded');
                img.onerror = () => {
                    img.classList.add('error');
                    img.parentElement.innerHTML = '<div class="image-placeholder"><i class="fas fa-image"></i></div>';
                };
                observer.unobserve(img);
            }
        });
    });
    
    lazyImages.forEach(img => imageObserver.observe(img));
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    applyCurrentView();
    initializeLazyLoading();
    
    // Ocultar panel de filtros inicialmente
    if (filtersPanel) {
        filtersPanel.style.display = 'none';
    }
});
</script>
@endpush

@push('styles')
<style>
/* Incluir todos los estilos de categorias-old.blade.php */
/* Estilos Base */
body {
    background-color: #f8fafc;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Header Profesional */
.header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    margin-bottom: 2rem;
}

.header-main {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 2rem;
}

.header-title-section {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex: 1;
    min-width: 300px;
}

.header-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.header-text {
    flex: 1;
}

.header-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    line-height: 1.2;
}

.header-badges {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.header-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.header-badge-primary {
    background: rgba(59, 130, 246, 0.3);
}

.header-badge-secondary {
    background: rgba(107, 114, 128, 0.3);
}

.header-stats {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
    align-items: center;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: rgba(255, 255, 255, 0.1);
    padding: 1rem 1.5rem;
    border-radius: 12px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    min-width: 120px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.stat-icon-success {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
}

.stat-icon-primary {
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
}

.stat-icon-warning {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
}

.stat-icon-danger {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

.stat-icon-dark {
    background: rgba(107, 114, 128, 0.2);
    color: #6b7280;
}

.stat-content {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.stat-number {
    font-size: 1.875rem;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    opacity: 0.9;
    font-weight: 500;
}

.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    font-size: 0.875rem;
}

.btn-primary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.btn-primary:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-1px);
    color: white;
}

.btn-outline-secondary {
    background: transparent;
    color: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-outline-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

/* Sistema de Búsqueda y Filtros Avanzados */
.advanced-search-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.search-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.search-title {
    color: #1e293b;
    font-weight: 600;
    margin: 0;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.toggle-filters {
    background: #667eea;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.toggle-filters:hover {
    background: #5a67d8;
    transform: translateY(-1px);
}

.toggle-filters.active {
    background: #4c51bf;
}

.main-search-container {
    padding: 2rem;
    background: white;
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
    width: 100%;
    padding: 1rem 3.5rem 1rem 3rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.search-input-modern:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 0.125rem rgba(102, 126, 234, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.search-icon {
    position: absolute;
    left: 1rem;
    color: #9ca3af;
    font-size: 1.125rem;
    pointer-events: none;
}

.search-loading {
    position: absolute;
    right: 3.5rem;
    color: #667eea;
    font-size: 1rem;
}

.btn-clear-search-modern {
    position: absolute;
    right: 0.75rem;
    background: #f3f4f6;
    border: none;
    border-radius: 6px;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s ease;
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
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.active-filter-tag .remove-filter {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    border-radius: 50%;
    width: 1.25rem;
    height: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    font-size: 0.625rem;
    transition: all 0.2s ease;
}

.active-filter-tag .remove-filter:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Resultados de Búsqueda */
.search-results-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    margin-bottom: 2rem;
}

.search-results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.search-results-title {
    color: #1e293b;
    font-weight: 600;
    margin: 0;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.view-toggle-buttons-small {
    display: flex;
    background: #f1f5f9;
    border-radius: 8px;
    padding: 0.25rem;
    gap: 0.25rem;
}

.view-toggle-btn-small {
    background: transparent;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.view-toggle-btn-small.active {
    background: white;
    color: #667eea;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.view-toggle-btn-small:hover:not(.active) {
    background: rgba(255, 255, 255, 0.5);
    color: #475569;
}

/* Vista de Elementos de Categoría */
.category-elements-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.category-elements-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.category-elements-title {
    color: #1e293b;
    font-weight: 600;
    margin: 0;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.category-elements-subtitle {
    color: #64748b;
    font-size: 0.875rem;
    margin: 0.5rem 0 0 0;
}

.view-toggle-buttons {
    display: flex;
    background: #f1f5f9;
    border-radius: 8px;
    padding: 0.25rem;
    gap: 0.25rem;
}

.view-toggle-btn {
    background: transparent;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.view-toggle-btn.active {
    background: white;
    color: #667eea;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.view-toggle-btn:hover:not(.active) {
    background: rgba(255, 255, 255, 0.5);
    color: #475569;
}

/* Grid de Elementos */
.elements-grid-view {
    padding: 2rem;
}

.elements-grid-compact {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
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
    height: 180px;
    overflow: hidden;
    background: #f9fafb;
    position: relative;
    flex-shrink: 0;
}

.element-image-compact img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.2s ease;
}

.element-card-compact:hover .element-image-compact img {
    transform: scale(1.05);
}

.image-placeholder {
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
    cursor: pointer;
    transition: all 0.2s ease;
}

.more-locations-compact:hover {
    background: #f3f4f6;
    color: #374151;
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

.btn-outline-primary {
    background: transparent;
    color: #667eea;
    border: 1px solid #667eea;
}

.btn-outline-primary:hover {
    background: #667eea;
    color: white;
}

.btn-outline-secondary {
    background: transparent;
    color: #6b7280;
    border: 1px solid #6b7280;
}

.btn-outline-secondary:hover {
    background: #6b7280;
    color: white;
}

/* Vista Tabla */
.elements-table-view {
    padding: 2rem;
}

.table {
    margin-bottom: 0;
}

.table th {
    background: #f8fafc;
    color: #374151;
    font-weight: 600;
    font-size: 0.875rem;
    border-bottom: 2px solid #e2e8f0;
    padding: 1rem 0.75rem;
}

.table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}

.table-hover tbody tr:hover {
    background-color: #f8fafc;
}

.table-element-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.table-element-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.table-element-code {
    font-size: 0.75rem;
    color: #6b7280;
    font-family: 'Courier New', monospace;
}

.table-brand-model {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.table-brand {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.table-model {
    font-size: 0.75rem;
    color: #6b7280;
}

.table-serial {
    font-size: 0.875rem;
    color: #374151;
    font-family: 'Courier New', monospace;
}

.table-locations {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.table-location-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    padding: 0.5rem;
    background: #f8fafc;
    border-radius: 6px;
    font-size: 0.75rem;
}

.table-location-name {
    font-weight: 600;
    color: #374151;
}

.table-status-badge {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.6875rem;
    font-weight: 500;
    border: 1px solid;
}

.table-status-badge.status-disponible {
    color: #059669;
    border-color: #d1fae5;
    background: #ecfdf5;
}

.table-status-badge.status-en-uso {
    color: #1d4ed8;
    border-color: #dbeafe;
    background: #eff6ff;
}

.table-status-badge.status-en-mantenimiento {
    color: #d97706;
    border-color: #fed7aa;
    background: #fffbeb;
}

.table-status-badge.status-dado-de-baja {
    color: #dc2626;
    border-color: #fecaca;
    background: #fef2f2;
}

.table-status-badge.status-robado {
    color: #4b5563;
    border-color: #e5e7eb;
    background: #f9fafb;
}

.table-quantity {
    font-weight: 600;
}

.table-status-icon {
    font-size: 0.75rem;
}

.table-status-text {
    font-weight: 600;
}

.more-locations-table {
    color: #6b7280;
    font-size: 0.75rem;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    padding: 0.375rem;
    background: #f1f5f9;
    border: 1px dashed #d1d5db;
    border-radius: 4px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.more-locations-table:hover {
    background: #e2e8f0;
    color: #374151;
}

/* Lazy Loading */
.lazy-load {
    opacity: 0;
    transition: opacity 0.3s;
}

.lazy-load.loaded {
    opacity: 1;
}

.lazy-load.error {
    opacity: 1;
}

/* Responsive */
@media (max-width: 768px) {
    .header-main {
        flex-direction: column;
        align-items: stretch;
        gap: 1.5rem;
    }
    
    .header-title-section {
        min-width: auto;
    }
    
    .header-title {
        font-size: 2rem;
    }
    
    .header-stats {
        justify-content: center;
        gap: 1rem;
    }
    
    .stat-item {
        min-width: 100px;
        padding: 0.75rem 1rem;
    }
    
    .elements-grid-compact {
        grid-template-columns: 1fr;
    }
    
    .search-header,
    .category-elements-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .header-card {
        padding: 1.5rem;
    }
    
    .header-title {
        font-size: 1.75rem;
    }
    
    .header-badges {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .main-search-container {
        padding: 1.5rem;
    }
    
    .filters-panel {
        padding: 1.5rem;
    }
    
    .elements-grid-view,
    .elements-table-view {
        padding: 1.5rem;
    }
}
</style>
@endpush