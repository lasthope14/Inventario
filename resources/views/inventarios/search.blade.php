@extends('layouts.app')

@section('content')
<div class="container-fluid" data-page="inventarios-search">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('inventarios.index') }}">Inventario</a></li>
            @if($categoriaActual)
                <li class="breadcrumb-item"><a href="{{ route('inventarios.categoria', $categoriaActual->id) }}">{{ $categoriaActual->nombre }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">
                @if($search)
                    Resultados para "{{ $search }}"
                @else
                    Búsqueda de Productos
                @endif
            </li>
        </ol>
    </nav>

    <!-- Header Profesional -->
    <div class="header-card mb-4">
        <div class="header-main">
            <div class="header-title-section">
                <div class="header-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="header-text">
                    <h1 class="header-title">
                        @if($search)
                            Resultados para "{{ $search }}"
                        @elseif($categoriaActual)
                            {{ $categoriaActual->nombre }}
                        @else
                            Búsqueda Avanzada
                        @endif
                    </h1>
                    <div class="header-badges">
                        <span class="header-badge header-badge-primary">
                            <i class="fas fa-cube"></i>
                            {{ $totalResultados }} {{ $totalResultados == 1 ? 'elemento' : 'elementos' }}
                        </span>
                        @if($search)
                            <span class="header-badge header-badge-info">
                                <i class="fas fa-search"></i>
                                Búsqueda activa
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="header-stats">
                @php
                    $estadisticas = [
                        'disponible' => $inventarios->where('estado', 'disponible')->count(),
                        'en-uso' => $inventarios->where('estado', 'en uso')->count(),
                        'mantenimiento' => $inventarios->where('estado', 'en mantenimiento')->count(),
                        'baja' => $inventarios->where('estado', 'dado de baja')->count(),
                        'robado' => $inventarios->where('estado', 'robado')->count()
                    ];
                @endphp
                
                <div class="stat-item stat-disponible">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $estadisticas['disponible'] }}</div>
                        <div class="stat-label">Disponibles</div>
                    </div>
                </div>
                
                <div class="stat-item stat-en-uso">
                    <div class="stat-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $estadisticas['en-uso'] }}</div>
                        <div class="stat-label">En Uso</div>
                    </div>
                </div>
                
                <div class="stat-item stat-mantenimiento">
                    <div class="stat-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $estadisticas['mantenimiento'] }}</div>
                        <div class="stat-label">Mantenimiento</div>
                    </div>
                </div>
                
                <div class="stat-item stat-baja">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $estadisticas['baja'] }}</div>
                        <div class="stat-label">Dados de Baja</div>
                    </div>
                </div>
                
                <div class="stat-item stat-robado">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $estadisticas['robado'] }}</div>
                        <div class="stat-label">Robados</div>
                    </div>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('inventarios.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Inicio
                </a>
                @if(request()->hasAny(['categoria', 'marca', 'proveedor', 'estado', 'ubicacion', 'precio_min', 'precio_max']))
                    <a href="{{ route('inventarios.search', ['search' => $search]) }}" class="btn btn-outline-danger">
                        <i class="fas fa-times"></i>
                        Limpiar Filtros
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Sistema de Búsqueda y Filtros Avanzados -->
    <div class="advanced-search-container mb-4">
        <div class="search-header">
            <h3 class="search-title">
                <i class="fas fa-search"></i>
                Búsqueda y Filtros Avanzados
            </h3>
            <button class="toggle-filters" id="toggleFilters">
                <i class="fas fa-filter"></i>
                Mostrar Filtros
            </button>
        </div>
        
        <div class="main-search-container">
            <div class="search-input-group">
                <div class="search-input-wrapper">
                    <input type="text" 
                           class="search-input-modern" 
                           id="searchInput" 
                           placeholder="Buscar elementos por nombre, código, marca..."
                           value="{{ $search }}">
                    <i class="search-icon fas fa-search"></i>
                    <div class="search-loading" id="searchLoading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <button class="btn-clear-search-modern" id="clearSearch" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="filters-panel" id="filtersPanel" style="display: none;">
            <form method="GET" action="{{ route('inventarios.search') }}" id="filtersForm">
                <input type="hidden" name="search" value="{{ $search }}">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="order" value="{{ $order }}">
                <input type="hidden" name="per_page" value="{{ $per_page }}">
                
                <div class="filters-grid">
                    <!-- Filtro por Categoría -->
                    <div class="filter-item">
                        <label class="filter-label">
                            <i class="fas fa-th-large"></i>
                            Categoría
                        </label>
                        <select name="categoria" class="filter-select">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ $categoria == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }} ({{ $cat->inventarios_count ?? 0 }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Filtro por Marca -->
                    @if($marcas->count() > 0)
                    <div class="filter-item">
                        <label class="filter-label">
                            <i class="fas fa-tag"></i>
                            Marca
                        </label>
                        <select name="marca" class="filter-select">
                            <option value="">Todas las marcas</option>
                            @foreach($marcas as $marcaOption)
                                <option value="{{ $marcaOption }}" {{ $marca == $marcaOption ? 'selected' : '' }}>
                                    {{ $marcaOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <!-- Filtro por Estado -->
                    <div class="filter-item">
                        <label class="filter-label">
                            <i class="fas fa-info-circle"></i>
                            Estado
                        </label>
                        <select name="estado" class="filter-select">
                            <option value="">Todos los estados</option>
                            @php
                                $estados = [
                                    'disponible' => 'Disponible',
                                    'en uso' => 'En Uso',
                                    'en mantenimiento' => 'En Mantenimiento',
                                    'dado de baja' => 'Dado de Baja',
                                    'robado' => 'Robado'
                                ];
                            @endphp
                            @foreach($estados as $estadoKey => $estadoLabel)
                                <option value="{{ $estadoKey }}" {{ $estado == $estadoKey ? 'selected' : '' }}>
                                    {{ $estadoLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Filtro por Ubicación -->
                    <div class="filter-item">
                        <label class="filter-label">
                            <i class="fas fa-map-marker-alt"></i>
                            Ubicación
                        </label>
                        <select name="ubicacion" class="filter-select">
                            <option value="">Todas las ubicaciones</option>
                            @foreach($ubicaciones as $ubi)
                                <option value="{{ $ubi->id }}" {{ $ubicacion == $ubi->id ? 'selected' : '' }}>
                                    {{ $ubi->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Filtro por Proveedor -->
                    <div class="filter-item">
                        <label class="filter-label">
                            <i class="fas fa-truck"></i>
                            Proveedor
                        </label>
                        <select name="proveedor" class="filter-select">
                            <option value="">Todos los proveedores</option>
                            @foreach($proveedores as $prov)
                                <option value="{{ $prov->id }}" {{ $proveedor == $prov->id ? 'selected' : '' }}>
                                    {{ $prov->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Filtro por Rango de Precio -->
                    <div class="filter-item filter-item-wide">
                        <label class="filter-label">
                            <i class="fas fa-dollar-sign"></i>
                            Rango de Precio
                        </label>
                        <div class="price-range-inputs">
                            <input type="number" 
                                   name="precio_min" 
                                   class="filter-select" 
                                   placeholder="Precio mínimo"
                                   value="{{ $precio_min }}">
                            <span class="price-separator">-</span>
                            <input type="number" 
                                   name="precio_max" 
                                   class="filter-select" 
                                   placeholder="Precio máximo"
                                   value="{{ $precio_max }}">
                        </div>
                    </div>
                </div>
                
                <div class="filters-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Aplicar Filtros
                    </button>
                    <a href="{{ route('inventarios.search', ['search' => $search]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                        Limpiar Filtros
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Filtros Activos -->
        <div class="active-filters" id="activeFilters" style="display: none;">
            <div class="active-filters-header">
                <span class="active-filters-title">Filtros activos:</span>
            </div>
            <div class="active-filters-list" id="activeFiltersList">
                <!-- Los filtros activos se mostrarán aquí dinámicamente -->
            </div>
        </div>
    </div>

    <!-- Resultados de Búsqueda Instantánea -->
    <div id="searchResults" style="display: none;"></div>

    <!-- Resultados de Elementos -->
    <div class="search-elements-section" id="searchElements">
        <div class="search-elements-header">
            <div class="search-elements-title-section">
                <h3 class="search-elements-title">
                    <i class="fas fa-list"></i>
                    Resultados de Búsqueda
                </h3>
                <p class="search-elements-subtitle">
                    {{ $totalResultados }} {{ $totalResultados == 1 ? 'elemento encontrado' : 'elementos encontrados' }}
                    @if($inventarios->hasPages())
                        ({{ $inventarios->firstItem() }}-{{ $inventarios->lastItem() }})
                    @endif
                </p>
            </div>
            
            <div class="search-elements-controls">
                <!-- Ordenamiento -->
                <form method="GET" action="{{ route('inventarios.search') }}" class="sort-form">
                    @foreach(request()->except(['sort', 'order']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="sort" class="sort-select" onchange="this.form.submit()">
                        <option value="nombre" {{ $sort == 'nombre' ? 'selected' : '' }}>Nombre</option>
                        <option value="precio" {{ $sort == 'precio' ? 'selected' : '' }}>Precio</option>
                        <option value="fecha" {{ $sort == 'fecha' ? 'selected' : '' }}>Fecha</option>
                        <option value="categoria" {{ $sort == 'categoria' ? 'selected' : '' }}>Categoría</option>
                    </select>
                    <select name="order" class="sort-select" onchange="this.form.submit()">
                        <option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>A-Z</option>
                        <option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>Z-A</option>
                    </select>
                </form>
                
                <!-- Elementos por página -->
                <form method="GET" action="{{ route('inventarios.search') }}" class="per-page-form">
                    @foreach(request()->except(['per_page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="per_page" class="per-page-select" onchange="this.form.submit()">
                        <option value="12" {{ $per_page == 12 ? 'selected' : '' }}>12 por página</option>
                        <option value="24" {{ $per_page == 24 ? 'selected' : '' }}>24 por página</option>
                        <option value="48" {{ $per_page == 48 ? 'selected' : '' }}>48 por página</option>
                    </select>
                </form>
                
                <!-- Botones de Vista -->
                <div class="view-toggle-buttons">
                    <button class="view-toggle-btn active" data-view="grid">
                        <i class="fas fa-th"></i>
                        Cuadrícula
                    </button>
                    <button class="view-toggle-btn" data-view="table">
                        <i class="fas fa-list"></i>
                        Tabla
                    </button>
                </div>
            </div>
        </div>
        
        @if($inventarios->count() > 0)
            <!-- Vista de Cuadrícula -->
            <div class="elements-grid-view" id="elementsGridView">
                <div class="elements-grid-compact">
                    @foreach($inventarios as $inventario)
                    <div class="element-card-compact">
                        <div class="element-image-compact">
                            @if($inventario->imagen)
                                <img src="{{ Storage::url($inventario->imagen) }}" alt="{{ $inventario->nombre }}" class="lazy-load" loading="lazy">
                            @else
                                <div class="image-placeholder">
                                    <i class="fas fa-cube"></i>
                                </div>
                            @endif
                        </div>
                        <div class="element-content-compact">
                            <div class="element-header-compact">
                                <h4 class="element-name-compact">{{ $inventario->nombre }}</h4>
                                <span class="element-code-compact">{{ $inventario->codigo ?? 'N/A' }}</span>
                            </div>
                            <div class="element-details-compact">
                                @if($inventario->marca)
                                <div class="detail-row-compact">
                                    <span class="detail-label-compact">Marca:</span>
                                    <span class="detail-value-compact">{{ $inventario->marca }}</span>
                                </div>
                                @endif
                                @if($inventario->categoria)
                                <div class="detail-row-compact">
                                    <span class="detail-label-compact">Categoría:</span>
                                    <span class="detail-value-compact">{{ $inventario->categoria->nombre }}</span>
                                </div>
                                @endif
                                @if($inventario->valor_unitario)
                                <div class="detail-row-compact">
                                    <span class="detail-label-compact">Precio:</span>
                                    <span class="detail-value-compact">${{ number_format($inventario->valor_unitario, 0, ',', '.') }}</span>
                                </div>
                                @endif
                            </div>
                            
                            @if($inventario->ubicaciones && $inventario->ubicaciones->count() > 0)
                            <div class="element-locations-compact">
                                @foreach($inventario->ubicaciones->take(2) as $ubicacion)
                                <div class="location-compact">
                                    <div class="location-info-compact">
                                        <span class="location-name-compact">{{ $ubicacion->nombre }}</span>
                                        <span class="location-quantity-compact">{{ $ubicacion->cantidad }} unidad{{ $ubicacion->cantidad != 1 ? 'es' : '' }}</span>
                                    </div>
                                </div>
                                @endforeach
                                @if($inventario->ubicaciones->count() > 2)
                                <div class="more-locations-compact" onclick="expandLocations(this, {{ $inventario->id }})">
                                    <i class="fas fa-plus"></i>
                                    Ver {{ $inventario->ubicaciones->count() - 2 }} ubicación{{ $inventario->ubicaciones->count() - 2 != 1 ? 'es' : '' }} más
                                </div>
                                @endif
                            </div>
                            @endif
                            
                            <div class="status-badge-element-new status-{{ str_replace(' ', '-', $inventario->estado) }}">
                                <i class="status-icon-compact {{ getStatusIcon($inventario->estado) }}"></i>
                                <span class="status-text-compact">{{ ucfirst($inventario->estado) }}</span>
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
            
            <!-- Vista de Tabla -->
            <div class="elements-table-view" id="elementsTableView" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Elemento</th>
                                <th>Categoría</th>
                                <th>Marca</th>
                                <th>Precio</th>
                                <th>Ubicaciones</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventarios as $inventario)
                            <tr>
                                <td>
                                    <div class="table-element-info">
                                        <div class="table-element-name">{{ $inventario->nombre }}</div>
                                        <div class="table-element-code">{{ $inventario->codigo ?? 'N/A' }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($inventario->categoria)
                                        <span class="table-category-badge">{{ $inventario->categoria->nombre }}</span>
                                    @else
                                        <span class="text-muted">Sin categoría</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-brand">{{ $inventario->marca ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    @if($inventario->valor_unitario)
                                        <div class="table-price">${{ number_format($inventario->valor_unitario, 0, ',', '.') }}</div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($inventario->ubicaciones && $inventario->ubicaciones->count() > 0)
                                    <div class="table-locations">
                                        @foreach($inventario->ubicaciones->take(2) as $ubicacion)
                                        <div class="table-location-item">
                                            <div class="table-location-name">{{ $ubicacion->nombre }}</div>
                                            <div class="location-quantity-compact">{{ $ubicacion->cantidad }} unidad{{ $ubicacion->cantidad != 1 ? 'es' : '' }}</div>
                                        </div>
                                        @endforeach
                                        @if($inventario->ubicaciones->count() > 2)
                                        <div class="more-locations-table" onclick="expandLocationsTable(this, {{ $inventario->id }})">
                                            <i class="fas fa-plus"></i>
                                            {{ $inventario->ubicaciones->count() - 2 }} más
                                        </div>
                                        @endif
                                    </div>
                                    @else
                                        <span class="text-muted">Sin ubicaciones</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-status-badge status-{{ str_replace(' ', '-', $inventario->estado) }}">
                                        <i class="table-status-icon {{ getStatusIcon($inventario->estado) }}"></i>
                                        <span class="table-status-text">{{ ucfirst($inventario->estado) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="element-actions-compact">
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
            <div class="pagination-wrapper mt-4">
                {{ $inventarios->links() }}
            </div>
        @else
            <!-- Estado Vacío -->
            <div class="elements-grid-view">
                <div class="text-center py-5">
                    <i class="fas fa-search text-muted" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <h4 class="text-muted">No se encontraron elementos</h4>
                    <p class="text-muted mb-4">
                        @if($search)
                            No hay elementos que coincidan con "{{ $search }}"
                        @else
                            No hay elementos que coincidan con los filtros seleccionados
                        @endif
                    </p>
                    <div class="empty-actions">
                        <a href="{{ route('inventarios.search') }}" class="btn btn-primary me-2">
                            <i class="fas fa-times me-1"></i>
                            Limpiar filtros
                        </a>
                        <a href="{{ route('inventarios.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@php
 function getStatusIcon($estado) {
     $icons = [
         'disponible' => 'fas fa-check-circle',
         'en uso' => 'fas fa-user-circle',
         'en mantenimiento' => 'fas fa-tools',
         'dado de baja' => 'fas fa-times-circle',
         'robado' => 'fas fa-exclamation-triangle'
     ];
     return $icons[$estado] ?? 'fas fa-question-circle';
 }
 @endphp

 @push('scripts')
 <script>
 // Variables globales
 let currentView = localStorage.getItem('searchView') || 'grid';
 let searchTimeout;
 let isSearching = false;

 // Referencias a elementos del DOM
 const searchInput = document.getElementById('searchInput');
 const searchResults = document.getElementById('searchResults');
 const searchElementsGrid = document.getElementById('searchElementsGrid');
 const searchElementsTable = document.getElementById('searchElementsTable');
 const searchResultsCount = document.getElementById('searchResultsCount');
 const searchElementsCount = document.getElementById('searchElementsCount');
 const loadingIndicator = document.getElementById('loadingIndicator');
 const noResultsMessage = document.getElementById('noResultsMessage');
 const clearSearchBtn = document.getElementById('clearSearchBtn');
 const toggleFiltersBtn = document.getElementById('toggleFiltersBtn');
 const filtersPanel = document.getElementById('filtersPanel');
 const activeFiltersContainer = document.getElementById('activeFiltersContainer');
 const activeFiltersList = document.getElementById('activeFiltersList');

 // Botones de vista
 const searchGridViewBtn = document.getElementById('searchGridViewBtn');
 const searchTableViewBtn = document.getElementById('searchTableViewBtn');
 const elementsGridViewBtn = document.getElementById('elementsGridViewBtn');
 const elementsTableViewBtn = document.getElementById('elementsTableViewBtn');

 // Función de búsqueda instantánea
 function performInstantSearch() {
     const query = searchInput.value.trim();
     
     if (query.length === 0) {
         searchResults.style.display = 'none';
         return;
     }

     if (query.length < 2) {
         return;
     }

     isSearching = true;
     showLoading();
     
     // Simular búsqueda con delay
     clearTimeout(searchTimeout);
     searchTimeout = setTimeout(() => {
         const results = generateMockResults(query);
         displaySearchResults(results);
         isSearching = false;
     }, 300);
 }

 // Generar resultados simulados
 function generateMockResults(query) {
     const mockElements = [
         {
             id: 1,
             nombre: 'Laptop Dell Inspiron 15',
             codigo: 'LAP-001',
             categoria: 'Computadoras',
             marca: 'Dell',
             modelo: 'Inspiron 15 3000',
             serie: 'DL123456',
             precio: 15000,
             estado: 'disponible',
             imagen: '/images/laptop-dell.jpg',
             ubicaciones: [
                 { nombre: 'Oficina Principal - Piso 2', cantidad: 1 },
                 { nombre: 'Almacén Central', cantidad: 2 }
             ]
         },
         {
             id: 2,
             nombre: 'Monitor Samsung 24"',
             codigo: 'MON-002',
             categoria: 'Monitores',
             marca: 'Samsung',
             modelo: 'S24F350',
             serie: 'SM789012',
             precio: 8500,
             estado: 'en uso',
             imagen: '/images/monitor-samsung.jpg',
             ubicaciones: [
                 { nombre: 'Oficina Principal - Piso 1', cantidad: 3 }
             ]
         },
         {
             id: 3,
             nombre: 'Impresora HP LaserJet',
             codigo: 'IMP-003',
             categoria: 'Impresoras',
             marca: 'HP',
             modelo: 'LaserJet Pro M404n',
             serie: 'HP345678',
             precio: 12000,
             estado: 'en mantenimiento',
             imagen: '/images/impresora-hp.jpg',
             ubicaciones: [
                 { nombre: 'Taller de Mantenimiento', cantidad: 1 }
             ]
         }
     ];

     return mockElements.filter(element => 
         element.nombre.toLowerCase().includes(query.toLowerCase()) ||
         element.codigo.toLowerCase().includes(query.toLowerCase()) ||
         element.marca.toLowerCase().includes(query.toLowerCase())
     );
 }

 // Mostrar resultados de búsqueda
 function displaySearchResults(results) {
     hideLoading();
     
     if (results.length === 0) {
         showNoResults();
         return;
     }

     hideNoResults();
     searchResults.style.display = 'block';
     searchResultsCount.textContent = results.length;
     
     // Mostrar en vista actual
     if (currentView === 'grid') {
         displaySearchResultsGrid(results);
     } else {
         displaySearchResultsTable(results);
     }
 }

 // Mostrar resultados en vista de cuadrícula
 function displaySearchResultsGrid(results) {
     searchElementsGrid.style.display = 'grid';
     searchElementsTable.style.display = 'none';
     
     searchElementsGrid.innerHTML = results.map(element => `
         <div class="element-card-compact">
             <div class="element-image-compact">
                 <img src="${element.imagen}" alt="${element.nombre}" class="lazy-load" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                 <div class="image-placeholder" style="display: none;">
                     <i class="fas fa-image"></i>
                 </div>
             </div>
             <div class="element-content-compact">
                 <div class="element-header-compact">
                     <h6 class="element-name-compact">${element.nombre}</h6>
                     <small class="element-code-compact">${element.codigo}</small>
                 </div>
                 
                 <div class="status-badge-element-new status-${element.estado}">
                     <i class="${getStatusIcon(element.estado)} status-icon-compact"></i>
                     <span class="status-text-compact">${element.estado}</span>
                 </div>
                 
                 <div class="element-details-compact">
                     <div class="detail-row-compact">
                         <span class="detail-label-compact">Categoría:</span>
                         <span class="detail-value-compact">${element.categoria}</span>
                     </div>
                     <div class="detail-row-compact">
                         <span class="detail-label-compact">Marca:</span>
                         <span class="detail-value-compact">${element.marca}</span>
                     </div>
                     <div class="detail-row-compact">
                         <span class="detail-label-compact">Precio:</span>
                         <span class="detail-value-compact">$${element.precio.toLocaleString()}</span>
                     </div>
                 </div>
                 
                 <div class="element-locations-compact">
                     ${element.ubicaciones.slice(0, 2).map(ubicacion => `
                         <div class="location-compact">
                             <i class="fas fa-map-marker-alt text-muted"></i>
                             <div class="location-info-compact">
                                 <div class="location-name-compact">${ubicacion.nombre}</div>
                                 <div class="location-quantity-compact">${ubicacion.cantidad} unidad(es)</div>
                             </div>
                         </div>
                     `).join('')}
                     ${element.ubicaciones.length > 2 ? `
                         <div class="more-locations-compact" onclick="expandLocations(this, ${element.id})">
                             <i class="fas fa-plus-circle"></i>
                             <span>Ver ${element.ubicaciones.length - 2} ubicación(es) más</span>
                         </div>
                     ` : ''}
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
     `).join('');
     
     // Aplicar lazy loading
     applyLazyLoading();
 }

 // Mostrar resultados en vista de tabla
 function displaySearchResultsTable(results) {
     searchElementsGrid.style.display = 'none';
     searchElementsTable.style.display = 'block';
     
     const tableBody = searchElementsTable.querySelector('tbody');
     tableBody.innerHTML = results.map(element => `
         <tr>
             <td>
                 <div class="table-element-info">
                     <div class="table-element-name">${element.nombre}</div>
                     <small class="table-element-code">${element.codigo}</small>
                 </div>
             </td>
             <td>
                 <span class="table-category-badge">${element.categoria}</span>
             </td>
             <td>
                 <div class="table-brand-model">
                     <div class="table-brand">${element.marca}</div>
                     <small class="table-model">${element.modelo}</small>
                 </div>
             </td>
             <td>
                 <span class="table-serial">${element.serie}</span>
             </td>
             <td>$${element.precio.toLocaleString()}</td>
             <td>
                 <div class="table-locations">
                     ${element.ubicaciones.slice(0, 2).map(ubicacion => `
                         <div class="table-location-item">
                             <div class="table-location-info">
                                 <i class="fas fa-map-marker-alt text-muted"></i>
                                 <span class="table-location-name">${ubicacion.nombre}</span>
                             </div>
                             <small class="text-muted">${ubicacion.cantidad} unidad(es)</small>
                         </div>
                     `).join('')}
                     ${element.ubicaciones.length > 2 ? `
                         <div class="more-locations-table" onclick="expandLocations(this, ${element.id})">
                             <i class="fas fa-plus-circle"></i>
                             <span>Ver ${element.ubicaciones.length - 2} más</span>
                         </div>
                     ` : ''}
                 </div>
             </td>
             <td>
                 <div class="table-status-badge status-${element.estado}">
                     <i class="${getStatusIcon(element.estado)} table-status-icon"></i>
                     <span class="table-status-text">${element.estado}</span>
                 </div>
             </td>
             <td>
                 <div class="btn-group btn-group-sm">
                     <button class="btn btn-outline-primary btn-xs">
                         <i class="fas fa-eye"></i>
                     </button>
                     <button class="btn btn-outline-secondary btn-xs">
                         <i class="fas fa-edit"></i>
                     </button>
                 </div>
             </td>
         </tr>
     `).join('');
 }

 // Funciones de estado de carga
 function showLoading() {
     if (loadingIndicator) {
         loadingIndicator.style.display = 'block';
     }
 }

 function hideLoading() {
     if (loadingIndicator) {
         loadingIndicator.style.display = 'none';
     }
 }

 function showNoResults() {
     if (noResultsMessage) {
         noResultsMessage.style.display = 'block';
     }
     if (searchResults) {
         searchResults.style.display = 'none';
     }
 }

 function hideNoResults() {
     if (noResultsMessage) {
         noResultsMessage.style.display = 'none';
     }
 }

 // Aplicar vista actual
 function applyCurrentView() {
     // Actualizar botones de vista para resultados de búsqueda
     if (searchGridViewBtn && searchTableViewBtn) {
         if (currentView === 'grid') {
             searchGridViewBtn.classList.add('active');
             searchTableViewBtn.classList.remove('active');
         } else {
             searchGridViewBtn.classList.remove('active');
             searchTableViewBtn.classList.add('active');
         }
     }
     
     // Actualizar botones de vista para elementos de búsqueda
     if (elementsGridViewBtn && elementsTableViewBtn) {
         if (currentView === 'grid') {
             elementsGridViewBtn.classList.add('active');
             elementsTableViewBtn.classList.remove('active');
         } else {
             elementsGridViewBtn.classList.remove('active');
             elementsTableViewBtn.classList.add('active');
         }
     }
 }

 // Event Listeners
 document.addEventListener('DOMContentLoaded', function() {
     // Aplicar vista guardada
     applyCurrentView();
     
     // Búsqueda instantánea
     if (searchInput) {
         searchInput.addEventListener('input', performInstantSearch);
     }
     
     // Limpiar búsqueda
     if (clearSearchBtn) {
         clearSearchBtn.addEventListener('click', function() {
             searchInput.value = '';
             searchResults.style.display = 'none';
             hideNoResults();
         });
     }
     
     // Toggle filtros
     if (toggleFiltersBtn && filtersPanel) {
         toggleFiltersBtn.addEventListener('click', function() {
             const isVisible = filtersPanel.style.display !== 'none';
             filtersPanel.style.display = isVisible ? 'none' : 'block';
             
             const icon = this.querySelector('i');
             if (icon) {
                 icon.className = isVisible ? 'fas fa-filter' : 'fas fa-times';
             }
         });
     }
     
     // Botones de vista para resultados de búsqueda
     if (searchGridViewBtn) {
         searchGridViewBtn.addEventListener('click', function() {
             currentView = 'grid';
             localStorage.setItem('searchView', currentView);
             applyCurrentView();
             
             if (searchElementsGrid && searchElementsTable) {
                 searchElementsGrid.style.display = 'grid';
                 searchElementsTable.style.display = 'none';
             }
         });
     }
     
     if (searchTableViewBtn) {
         searchTableViewBtn.addEventListener('click', function() {
             currentView = 'table';
             localStorage.setItem('searchView', currentView);
             applyCurrentView();
             
             if (searchElementsGrid && searchElementsTable) {
                 searchElementsGrid.style.display = 'none';
                 searchElementsTable.style.display = 'block';
             }
         });
     }
     
     // Botones de vista para elementos de búsqueda
     if (elementsGridViewBtn) {
         elementsGridViewBtn.addEventListener('click', function() {
             currentView = 'grid';
             localStorage.setItem('searchView', currentView);
             applyCurrentView();
             
             const elementsGrid = document.getElementById('elementsGrid');
             const elementsTable = document.getElementById('elementsTable');
             if (elementsGrid && elementsTable) {
                 elementsGrid.style.display = 'grid';
                 elementsTable.style.display = 'none';
             }
         });
     }
     
     if (elementsTableViewBtn) {
         elementsTableViewBtn.addEventListener('click', function() {
             currentView = 'table';
             localStorage.setItem('searchView', currentView);
             applyCurrentView();
             
             const elementsGrid = document.getElementById('elementsGrid');
             const elementsTable = document.getElementById('elementsTable');
             if (elementsGrid && elementsTable) {
                 elementsGrid.style.display = 'none';
                 elementsTable.style.display = 'block';
             }
         });
     }
 });

 // Sistema de vista global
 function setGlobalView(view) {
     currentView = view;
     localStorage.setItem('searchView', view);
     applyCurrentView();
 }

 // Función para expandir ubicaciones
 function expandLocations(element, elementId) {
     // Implementar lógica para mostrar todas las ubicaciones
     console.log('Expandir ubicaciones para elemento:', elementId);
 }

 // Función para obtener icono de estado
 function getStatusIcon(estado) {
     const icons = {
         'disponible': 'fas fa-check-circle',
         'en uso': 'fas fa-user-circle',
         'en mantenimiento': 'fas fa-tools',
         'dado de baja': 'fas fa-times-circle',
         'robado': 'fas fa-exclamation-triangle'
     };
     return icons[estado] || 'fas fa-question-circle';
 }

 // Lazy loading para imágenes
 function applyLazyLoading() {
     const images = document.querySelectorAll('.lazy-load');
     
     const imageObserver = new IntersectionObserver((entries, observer) => {
         entries.forEach(entry => {
             if (entry.isIntersecting) {
                 const img = entry.target;
                 img.classList.add('loaded');
                 observer.unobserve(img);
             }
         });
     });
     
     images.forEach(img => imageObserver.observe(img));
 }
 </script>
 @endpush

 @push('styles')
 <style>
 /* Header Profesional */
 .header-card {
     background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
     border-radius: 12px;
     padding: 2rem;
     color: white;
     box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
     margin-bottom: 2rem;
 }

 .header-main {
     display: flex;
     justify-content: space-between;
     align-items: flex-start;
     gap: 2rem;
 }

 .header-title-section {
     display: flex;
     align-items: center;
     gap: 1rem;
     flex: 1;
 }

 .header-icon {
     background: rgba(255, 255, 255, 0.2);
     padding: 1rem;
     border-radius: 12px;
     font-size: 1.5rem;
 }

 .header-text {
     flex: 1;
 }

 .header-title {
     font-size: 2rem;
     font-weight: 700;
     margin-bottom: 0.5rem;
     color: white;
 }

 .header-badges {
     display: flex;
     gap: 0.5rem;
     flex-wrap: wrap;
 }

 .header-badge {
     display: inline-flex;
     align-items: center;
     gap: 0.25rem;
     padding: 0.25rem 0.75rem;
     border-radius: 20px;
     font-size: 0.85rem;
     font-weight: 500;
 }

 .header-badge-primary {
     background: rgba(255, 255, 255, 0.2);
     color: white;
 }

 .header-badge-info {
     background: rgba(23, 162, 184, 0.8);
     color: white;
 }

 .header-stats {
     display: flex;
     gap: 1rem;
     flex-wrap: wrap;
 }

 .stat-item {
     display: flex;
     align-items: center;
     gap: 0.75rem;
     background: rgba(255, 255, 255, 0.1);
     padding: 1rem;
     border-radius: 8px;
     min-width: 120px;
 }

 .stat-icon {
     font-size: 1.5rem;
     opacity: 0.9;
 }

 .stat-content {
     display: flex;
     flex-direction: column;
 }

 .stat-number {
     font-size: 1.5rem;
     font-weight: 700;
     line-height: 1;
 }

 .stat-label {
     font-size: 0.75rem;
     opacity: 0.9;
     text-transform: uppercase;
     letter-spacing: 0.5px;
 }

 .stat-disponible .stat-icon { color: #28a745; }
 .stat-en-uso .stat-icon { color: #17a2b8; }
 .stat-mantenimiento .stat-icon { color: #ffc107; }
 .stat-baja .stat-icon { color: #dc3545; }
 .stat-robado .stat-icon { color: #fd7e14; }

 .header-actions {
     display: flex;
     gap: 0.5rem;
     align-items: flex-start;
 }

 .header-actions .btn {
     border-color: rgba(255, 255, 255, 0.3);
     color: white;
 }

 .header-actions .btn:hover {
     background: rgba(255, 255, 255, 0.1);
     border-color: rgba(255, 255, 255, 0.5);
 }

 /* Sistema de Búsqueda y Filtros Avanzados */
 .advanced-search-container {
     background: white;
     border-radius: 12px;
     box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
     overflow: hidden;
 }

 .search-header {
     display: flex;
     justify-content: space-between;
     align-items: center;
     padding: 1.5rem;
     background: #f8f9fa;
     border-bottom: 1px solid #e9ecef;
 }

 .search-title {
     font-size: 1.25rem;
     font-weight: 600;
     color: #2c3e50;
     margin: 0;
     display: flex;
     align-items: center;
     gap: 0.5rem;
 }

 .toggle-filters {
     background: #007bff;
     color: white;
     border: none;
     padding: 0.5rem 1rem;
     border-radius: 6px;
     font-size: 0.9rem;
     cursor: pointer;
     transition: all 0.3s ease;
     display: flex;
     align-items: center;
     gap: 0.5rem;
 }

 .toggle-filters:hover {
     background: #0056b3;
 }

 .main-search-container {
     padding: 1.5rem;
 }

 .search-input-group {
     max-width: 600px;
     margin: 0 auto;
 }

 .search-input-wrapper {
     position: relative;
     display: flex;
     align-items: center;
 }

 .search-input-modern {
     width: 100%;
     padding: 1rem 3rem 1rem 3rem;
     border: 2px solid #e9ecef;
     border-radius: 50px;
     font-size: 1rem;
     transition: all 0.3s ease;
     background: #f8f9fa;
 }

 .search-input-modern:focus {
     outline: none;
     border-color: #007bff;
     background: white;
     box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
 }

 .search-icon {
     position: absolute;
     left: 1rem;
     color: #6c757d;
     font-size: 1.1rem;
 }

 .search-loading {
     position: absolute;
     right: 3rem;
     color: #007bff;
 }

 .btn-clear-search-modern {
     position: absolute;
     right: 1rem;
     background: none;
     border: none;
     color: #6c757d;
     font-size: 1.1rem;
     cursor: pointer;
     padding: 0.25rem;
     border-radius: 50%;
     transition: all 0.3s ease;
 }

 .btn-clear-search-modern:hover {
     color: #dc3545;
     background: rgba(220, 53, 69, 0.1);
 }

 .filters-panel {
     padding: 1.5rem;
     border-top: 1px solid #e9ecef;
     background: #f8f9fa;
 }

 .filters-grid {
     display: grid;
     grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
     gap: 1rem;
     margin-bottom: 1.5rem;
 }

 .filter-item {
     display: flex;
     flex-direction: column;
 }

 .filter-item-wide {
     grid-column: span 2;
 }

 .filter-label {
     font-weight: 600;
     color: #495057;
     margin-bottom: 0.5rem;
     display: flex;
     align-items: center;
     gap: 0.5rem;
     font-size: 0.9rem;
 }

 .filter-select {
     padding: 0.75rem;
     border: 1px solid #ced4da;
     border-radius: 6px;
     font-size: 0.9rem;
     background: white;
     transition: border-color 0.3s ease;
 }

 .filter-select:focus {
     outline: none;
     border-color: #007bff;
     box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
 }

 .price-range-inputs {
     display: flex;
     align-items: center;
     gap: 0.5rem;
 }

 .price-separator {
     color: #6c757d;
     font-weight: 500;
 }

 .filters-actions {
     display: flex;
     gap: 1rem;
     justify-content: center;
 }

 .active-filters {
     padding: 1rem 1.5rem;
     background: #e3f2fd;
     border-top: 1px solid #bbdefb;
 }

 .active-filters-header {
     margin-bottom: 0.5rem;
 }

 .active-filters-title {
     font-weight: 600;
     color: #1976d2;
     font-size: 0.9rem;
 }

 .active-filters-list {
     display: flex;
     flex-wrap: wrap;
     gap: 0.5rem;
 }

 .active-filter-tag {
     display: inline-flex;
     align-items: center;
     gap: 0.25rem;
     background: #1976d2;
     color: white;
     padding: 0.25rem 0.75rem;
     border-radius: 20px;
     font-size: 0.8rem;
     cursor: pointer;
 }

 .active-filter-tag:hover {
     background: #1565c0;
 }

 /* Sección de Resultados de Búsqueda */
 .search-results-section {
     background: white;
     border-radius: 12px;
     box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
     margin-bottom: 2rem;
 }

 .search-results-header {
     display: flex;
     justify-content: space-between;
     align-items: center;
     padding: 1.5rem;
     border-bottom: 1px solid #e9ecef;
 }

 .search-results-title {
     font-size: 1.25rem;
     font-weight: 600;
     color: #2c3e50;
     margin: 0;
     display: flex;
     align-items: center;
     gap: 0.5rem;
 }

 .view-toggle-buttons {
     display: flex;
     background: #f8f9fa;
     border-radius: 6px;
     padding: 0.25rem;
 }

 .view-toggle-btn {
     background: none;
     border: none;
     padding: 0.5rem 1rem;
     border-radius: 4px;
     font-size: 0.9rem;
     cursor: pointer;
     transition: all 0.3s ease;
     display: flex;
     align-items: center;
     gap: 0.5rem;
 }

 .view-toggle-btn.active {
     background: #007bff;
     color: white;
 }

 .view-toggle-btn:not(.active) {
     color: #6c757d;
 }

 .view-toggle-btn:not(.active):hover {
     background: #e9ecef;
     color: #495057;
 }

 /* Sección de Elementos de Búsqueda */
 .search-elements-section {
     background: white;
     border-radius: 12px;
     box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
 }

 .search-elements-header {
     display: flex;
     justify-content: space-between;
     align-items: flex-start;
     padding: 1.5rem;
     border-bottom: 1px solid #e9ecef;
     gap: 1rem;
 }

 .search-elements-title-section {
     flex: 1;
 }

 .search-elements-title {
     font-size: 1.25rem;
     font-weight: 600;
     color: #2c3e50;
     margin: 0 0 0.25rem 0;
     display: flex;
     align-items: center;
     gap: 0.5rem;
 }

 .search-elements-subtitle {
     color: #6c757d;
     margin: 0;
     font-size: 0.9rem;
 }

 .search-elements-controls {
     display: flex;
     align-items: center;
     gap: 1rem;
     flex-wrap: wrap;
 }

 .sort-form, .per-page-form {
     display: flex;
     align-items: center;
     gap: 0.5rem;
 }

 .sort-select, .per-page-select {
     padding: 0.5rem;
     border: 1px solid #ced4da;
     border-radius: 4px;
     font-size: 0.85rem;
     background: white;
 }

 /* Vista de Cuadrícula Compacta */
 .elements-grid-compact {
     display: grid;
     grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
     gap: 1.5rem;
     padding: 1.5rem;
 }

 .element-card-compact {
     background: white;
     border: 1px solid #e9ecef;
     border-radius: 8px;
     overflow: hidden;
     transition: all 0.3s ease;
     display: flex;
     flex-direction: column;
 }

 .element-card-compact:hover {
     box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
     border-color: #007bff;
 }

 .element-image-compact {
     height: 180px;
     background: #f8f9fa;
     display: flex;
     align-items: center;
     justify-content: center;
     overflow: hidden;
 }

 .element-image-compact img {
     width: 100%;
     height: 100%;
     object-fit: cover;
 }

 .image-placeholder {
     color: #6c757d;
     font-size: 3rem;
 }

 .element-content-compact {
     padding: 1rem;
     flex: 1;
     display: flex;
     flex-direction: column;
 }

 .element-header-compact {
     margin-bottom: 0.75rem;
 }

 .element-name-compact {
     font-size: 1.1rem;
     font-weight: 600;
     color: #2c3e50;
     margin: 0 0 0.25rem 0;
     line-height: 1.3;
 }

 .element-code-compact {
     font-size: 0.8rem;
     color: #6c757d;
     font-family: monospace;
 }

 .element-details-compact {
     margin-bottom: 0.75rem;
     flex: 1;
 }

 .detail-row-compact {
     display: flex;
     justify-content: space-between;
     margin-bottom: 0.25rem;
     font-size: 0.85rem;
 }

 .detail-label-compact {
     color: #6c757d;
     font-weight: 500;
 }

 .detail-value-compact {
     color: #495057;
     font-weight: 600;
 }

 .element-locations-compact {
     margin-bottom: 0.75rem;
 }

 .location-compact {
     display: flex;
     align-items: center;
     gap: 0.5rem;
     margin-bottom: 0.25rem;
 }

 .location-info-compact {
     display: flex;
     flex-direction: column;
 }

 .location-name-compact {
     font-size: 0.85rem;
     font-weight: 500;
     color: #495057;
 }

 .location-quantity-compact {
     font-size: 0.75rem;
     color: #6c757d;
 }

 .more-locations-compact {
     font-size: 0.8rem;
     color: #007bff;
     cursor: pointer;
     display: flex;
     align-items: center;
     gap: 0.25rem;
     margin-top: 0.25rem;
 }

 .more-locations-compact:hover {
     color: #0056b3;
 }

 .status-badge-element-new {
     display: inline-flex;
     align-items: center;
     gap: 0.25rem;
     padding: 0.25rem 0.75rem;
     border-radius: 20px;
     font-size: 0.75rem;
     font-weight: 500;
     margin-bottom: 0.75rem;
     width: fit-content;
 }

 .status-icon-compact {
     font-size: 0.8rem;
 }

 .status-text-compact {
     text-transform: capitalize;
 }

 .status-disponible {
     background: #d4edda;
     color: #155724;
 }

 .status-en-uso {
     background: #d1ecf1;
     color: #0c5460;
 }

 .status-en-mantenimiento {
     background: #fff3cd;
     color: #856404;
 }

 .status-dado-de-baja {
     background: #f8d7da;
     color: #721c24;
 }

 .status-robado {
     background: #ffeaa7;
     color: #8b4513;
 }

 .element-actions-compact {
     display: flex;
     gap: 0.5rem;
     margin-top: auto;
 }

 .btn-xs {
     padding: 0.25rem 0.5rem;
     font-size: 0.75rem;
     border-radius: 4px;
 }

 .btn-outline-primary {
     border-color: #007bff;
     color: #007bff;
 }

 .btn-outline-primary:hover {
     background: #007bff;
     color: white;
 }

 .btn-outline-secondary {
     border-color: #6c757d;
     color: #6c757d;
 }

 .btn-outline-secondary:hover {
     background: #6c757d;
     color: white;
 }

 /* Vista de Tabla */
 .table {
     margin: 0;
 }

 .table th {
     background: #f8f9fa;
     border-top: none;
     font-weight: 600;
     color: #495057;
     padding: 1rem 0.75rem;
 }

 .table td {
     padding: 1rem 0.75rem;
     vertical-align: middle;
 }

 .table-element-info {
     display: flex;
     flex-direction: column;
 }

 .table-element-name {
     font-weight: 600;
     color: #2c3e50;
     margin-bottom: 0.25rem;
 }

 .table-element-code {
     font-size: 0.8rem;
     color: #6c757d;
     font-family: monospace;
 }

 .table-category-badge {
     background: #e3f2fd;
     color: #1976d2;
     padding: 0.25rem 0.5rem;
     border-radius: 12px;
     font-size: 0.8rem;
     font-weight: 500;
 }

 .table-brand-model {
     display: flex;
     flex-direction: column;
 }

 .table-brand {
     font-weight: 500;
     color: #495057;
 }

 .table-model {
     font-size: 0.85rem;
     color: #6c757d;
 }

 .table-serial {
     font-family: monospace;
     font-size: 0.85rem;
     color: #495057;
 }

 .table-locations {
     display: flex;
     flex-direction: column;
     gap: 0.25rem;
 }

 .table-location-item {
     display: flex;
     flex-direction: column;
 }

 .table-location-info {
     display: flex;
     align-items: center;
     gap: 0.5rem;
 }

 .table-location-name {
     font-size: 0.85rem;
     font-weight: 500;
     color: #495057;
 }

 .table-status-badge {
     display: inline-flex;
     align-items: center;
     gap: 0.25rem;
     padding: 0.25rem 0.75rem;
     border-radius: 20px;
     font-size: 0.75rem;
     font-weight: 500;
 }

 .table-quantity {
     font-weight: 600;
     color: #2c3e50;
 }

 .table-status-icon {
     font-size: 0.8rem;
 }

 .table-status-text {
     text-transform: capitalize;
 }

 .more-locations-table {
     font-size: 0.8rem;
     color: #007bff;
     cursor: pointer;
     display: flex;
     align-items: center;
     gap: 0.25rem;
     margin-top: 0.25rem;
 }

 .more-locations-table:hover {
     color: #0056b3;
 }

 /* Lazy Loading */
 .lazy-load {
     opacity: 0;
     transition: opacity 0.3s ease;
 }

 .lazy-load.loaded {
     opacity: 1;
 }

 /* Formularios */
 .form-label {
     font-weight: 600;
     color: #495057;
     margin-bottom: 0.5rem;
 }

 /* Responsive */
 @media (max-width: 768px) {
     .header-main {
         flex-direction: column;
         gap: 1.5rem;
     }

     .header-stats {
         justify-content: center;
     }

     .stat-item {
         min-width: 100px;
     }

     .search-elements-header {
         flex-direction: column;
         align-items: stretch;
         gap: 1rem;
     }

     .search-elements-controls {
         justify-content: center;
     }

     .elements-grid-compact {
         grid-template-columns: 1fr;
         padding: 1rem;
     }

     .filters-grid {
         grid-template-columns: 1fr;
     }

     .filter-item-wide {
         grid-column: span 1;
     }

     .price-range-inputs {
         flex-direction: column;
     }

     .filters-actions {
         flex-direction: column;
     }
 }

 @media (max-width: 576px) {
     .header-card {
         padding: 1.5rem;
     }

     .header-title {
         font-size: 1.5rem;
     }

     .header-stats {
         grid-template-columns: repeat(2, 1fr);
     }

     .stat-item {
         min-width: auto;
     }

     .view-toggle-buttons {
         width: 100%;
     }

     .view-toggle-btn {
         flex: 1;
         justify-content: center;
     }
 }
 </style>
 @endpush
 @endsection