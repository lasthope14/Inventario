@extends('layouts.app')

@section('content')
<div class="container-fluid" data-page="inventarios-categorias">
    <!-- Header Profesional -->
    <div class="header-card">
        <div class="header-main">
            <div class="header-top">
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
                </div>
                
                <div class="header-actions">
                    <div class="quick-actions-compact">
                        <a href="{{ route('inventarios.create') }}" class="quick-action-btn">
                            <div class="quick-action-icon-small">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="quick-action-text">
                                <span class="quick-action-title">Agregar</span>
                                <span class="quick-action-subtitle">Nuevo elemento</span>
                            </div>
                        </a>
                        <a href="{{ route('inventarios.import.form') }}" class="quick-action-btn">
                            <div class="quick-action-icon-small">
                                <i class="fas fa-upload"></i>
                            </div>
                            <div class="quick-action-text">
                                <span class="quick-action-title">Importar</span>
                                <span class="quick-action-subtitle">Datos masivos</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
                
                <div class="header-stats">
                    <a href="{{ route('inventarios.categoria', ['categoria' => null, 'estado' => 'disponible']) }}" class="stat-item stat-item-clickable">
                        <i class="fas fa-check-circle stat-icon stat-icon-success"></i>
                        <div class="stat-content">
                            <div class="stat-number">{{ $statsGlobales->disponibles ?? 0 }}</div>
                            <div class="stat-label">Disponibles</div>
                        </div>
                    </a>
                    <a href="{{ route('inventarios.categoria', ['categoria' => null, 'estado' => 'en uso']) }}" class="stat-item stat-item-clickable">
                        <i class="fas fa-play-circle stat-icon stat-icon-primary"></i>
                        <div class="stat-content">
                            <div class="stat-number">{{ $statsGlobales->en_uso ?? 0 }}</div>
                            <div class="stat-label">En Uso</div>
                        </div>
                    </a>
                    <a href="{{ route('inventarios.categoria', ['categoria' => null, 'estado' => 'en mantenimiento']) }}" class="stat-item stat-item-clickable">
                        <i class="fas fa-tools stat-icon stat-icon-warning"></i>
                        <div class="stat-content">
                            <div class="stat-number">{{ $statsGlobales->en_mantenimiento ?? 0 }}</div>
                            <div class="stat-label">Mantenimiento</div>
                        </div>
                    </a>
                    <a href="{{ route('inventarios.categoria', ['categoria' => null, 'estado' => 'dado de baja']) }}" class="stat-item stat-item-clickable">
                        <i class="fas fa-ban stat-icon stat-icon-danger"></i>
                        <div class="stat-content">
                            <div class="stat-number">{{ $statsGlobales->dados_de_baja ?? 0 }}</div>
                            <div class="stat-label">Dados de Baja</div>
                        </div>
                    </a>
                    <a href="{{ route('inventarios.categoria', ['categoria' => null, 'estado' => 'robado']) }}" class="stat-item stat-item-clickable">
                        <i class="fas fa-user-secret stat-icon stat-icon-dark"></i>
                        <div class="stat-content">
                            <div class="stat-number">{{ $statsGlobales->robados ?? 0 }}</div>
                            <div class="stat-label">Robados</div>
                        </div>
                    </a>
                </div>
        </div>
    </div>

    <!-- Sistema de Búsqueda y Filtros Avanzado -->
    <div class="advanced-search-container">
        <div class="search-header-and-main">
            <!-- Búsqueda Principal -->
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

    <!-- Resultados de Búsqueda del Servidor -->
    @if(isset($inventarios) && isset($searchTerm))
    <div class="search-results-section mb-4">
        <div class="search-results-header">
            <div class="search-results-info">
                <h4 class="search-results-title">
                    <i class="fas fa-search me-2"></i>
                    Resultados de Búsqueda para "{{ $searchTerm }}"
                </h4>
                <span class="badge bg-primary ms-2">{{ $inventarios->total() }} elementos</span>
            </div>
            
            <div class="search-results-controls">
                <a href="{{ route('inventarios.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Volver al inicio
                </a>
            </div>
        </div>
        
        <!-- Vista Grid de Resultados -->
        <div class="elements-grid-view">
            <div class="elements-grid-compact">
                @forelse($inventarios as $inventario)
                <div class="element-card-compact">
                    <div class="element-image-compact">
                        @if($inventario->imagen)
                            <img src="{{ asset('storage/' . $inventario->imagen) }}" alt="{{ $inventario->nombre }}" class="lazy-load">
                        @else
                            <div class="no-image-placeholder">
                                <i class="fas fa-cube"></i>
                            </div>
                        @endif
                    </div>
                    <div class="element-content-compact">
                        <div class="element-header-compact">
                            <h6 class="element-name-compact">{{ $inventario->nombre }}</h6>
                            <span class="element-code-compact">{{ $inventario->codigo_unico }}</span>
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
                                <span class="detail-label-compact">Categoría:</span>
                                <span class="detail-value-compact">{{ $inventario->categoria->nombre ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="element-locations-compact">
                            @foreach($inventario->ubicaciones->take(2) as $ubicacion)
                            <div class="location-compact">
                                <div class="location-info-compact">
                                    <span class="location-name-compact">{{ $ubicacion->ubicacion->nombre ?? 'N/A' }}</span>
                                    <span class="location-quantity-compact">{{ $ubicacion->cantidad }} unidades</span>
                                </div>
                                <div class="status-badge-element-new status-{{ str_replace(' ', '-', $ubicacion->estado) }}">
                                    <span class="status-text-compact">{{ ucfirst($ubicacion->estado) }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="element-actions-compact">
                            <a href="{{ route('inventarios.show', $inventario->id) }}" class="btn-action-compact btn-view-compact" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('inventarios.edit', $inventario->id) }}" class="btn-action-compact btn-edit-compact" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted">No se encontraron resultados</h3>
                        <p class="text-muted">Intenta con otros términos de búsqueda.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Paginación de Resultados -->
        @if($inventarios->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $inventarios->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
    @endif

    <!-- Categorías Principales -->
    @if(!isset($inventarios) || !isset($searchTerm))
    <div class="categories-section">
        <div class="section-header mb-4">
            <h2 class="section-title">
                <i class="fas fa-th-large me-2"></i>
                Categorías de Productos
            </h2>
            <p class="section-subtitle text-muted">
                Encuentra lo que necesitas navegando por nuestras categorías
            </p>
        </div>

        <div class="categories-grid">
            @forelse($categorias as $categoria)
            <div class="category-card">
                <a href="{{ route('inventarios.categoria', $categoria->id) }}" class="category-link">
                    @if($categoria->imagen)
                        <img src="{{ asset('storage/' . $categoria->imagen) }}" alt="{{ $categoria->nombre }}" class="category-image">
                    @else
                        <svg class="category-image-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V9C21 7.89543 20.1046 7 19 7H13L11 5H5C3.89543 5 3 5.89543 3 7Z" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @endif
                    <h3 class="category-name">{{ $categoria->nombre }}</h3>
                    <div class="category-total">
                        <span class="total-number">{{ $categoria->total_elementos ?? 0 }}</span>
                        <span class="total-label">elementos</span>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12">
                <div class="empty-state text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h3 class="text-muted">No hay categorías disponibles</h3>
                    <p class="text-muted">Contacta al administrador para configurar las categorías.</p>
                </div>
            </div>
            @endforelse
        </div>
        
        <!-- Paginación -->
        @if($categorias->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $categorias->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
    @endif

</div>

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
    
    // ===== FUNCIONES DE BÚSQUEDA =====
    function performInstantSearch() {
        const searchTerm = searchInput.value.trim();
        const categoryFilter = document.getElementById('filterCategoria')?.value || '';
        const elementFilter = document.getElementById('filterElemento')?.value || '';
        const brandFilter = document.getElementById('filterMarca')?.value || '';
        const statusFilter = document.getElementById('filterEstado')?.value || '';
        const locationFilter = document.getElementById('filterUbicacion')?.value || '';
        
        if (searchTerm.length === 0 && !categoryFilter && !elementFilter && !brandFilter && !statusFilter && !locationFilter) {
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
                    categoria: 'Computadoras',
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
        const tableHTML = generateTableHTML(results.elements);
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
            
            const query = this.value.trim();
            
            // Mostrar/ocultar botón de limpiar
            if (query.length > 0) {
                clearSearchBtn.style.display = 'flex';
                
                // Autocompletado
                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        fetchAutocomplete(query);
                    }, 300);
                } else {
                    hideSuggestions();
                }
            } else {
                clearSearchBtn.style.display = 'none';
                hideSuggestions();
            }
        });
        
        // Manejar navegación con teclado
        searchInput.addEventListener('keydown', function(e) {
            const suggestions = document.querySelectorAll('.suggestion-item');
            const activeSuggestion = document.querySelector('.suggestion-item.active');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (activeSuggestion) {
                    activeSuggestion.classList.remove('active');
                    const next = activeSuggestion.nextElementSibling;
                    if (next) {
                        next.classList.add('active');
                    } else {
                        suggestions[0]?.classList.add('active');
                    }
                } else {
                    suggestions[0]?.classList.add('active');
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (activeSuggestion) {
                    activeSuggestion.classList.remove('active');
                    const prev = activeSuggestion.previousElementSibling;
                    if (prev) {
                        prev.classList.add('active');
                    } else {
                        suggestions[suggestions.length - 1]?.classList.add('active');
                    }
                } else {
                    suggestions[suggestions.length - 1]?.classList.add('active');
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeSuggestion) {
                    const url = activeSuggestion.dataset.url;
                    if (url) {
                        window.location.href = url;
                    }
                }
                // Eliminado el llamado a performInstantSearch para evitar errores
            } else if (e.key === 'Escape') {
                hideSuggestions();
            }
        });
        
        // Ocultar sugerencias al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !document.getElementById('searchSuggestions').contains(e.target)) {
                hideSuggestions();
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
        
        // Guardar preferencia global
        globalViewPreference = viewType;
        localStorage.setItem('global-view-preference', viewType);
    }
    
    // Aplicar vista inicial
    applyGlobalView(globalViewPreference);
    
    // Inicializar lazy loading para imágenes existentes
    initializeLazyLoading();
});

// ===== FUNCIONES DE AUTOCOMPLETADO =====
function fetchAutocomplete(query) {
    const suggestionsContainer = document.getElementById('searchSuggestions');
    
    // Mostrar loading
    suggestionsContainer.innerHTML = '<div class="suggestion-loading"><i class="fas fa-spinner fa-spin"></i> Buscando...</div>';
    suggestionsContainer.style.display = 'block';
    
    fetch(`{{ route('inventarios.autocomplete') }}?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            displaySuggestions(data);
        })
        .catch(error => {
            console.error('Error en autocompletado:', error);
            hideSuggestions();
        });
}

function displaySuggestions(suggestions) {
    const suggestionsContainer = document.getElementById('searchSuggestions');
    
    if (suggestions.length === 0) {
        suggestionsContainer.innerHTML = '<div class="suggestion-empty">No se encontraron sugerencias</div>';
        return;
    }
    
    let html = '';
    suggestions.forEach(suggestion => {
        const iconClass = getTypeIcon(suggestion.type);
        html += `
            <div class="suggestion-item" data-url="${suggestion.url}">
                <div class="suggestion-icon">
                    <i class="${iconClass}"></i>
                </div>
                <div class="suggestion-content">
                    <div class="suggestion-text">${suggestion.text}</div>
                    <div class="suggestion-subtitle">${suggestion.subtitle}</div>
                </div>
                <div class="suggestion-type">
                    <span class="type-badge type-${suggestion.type}">${getTypeLabel(suggestion.type)}</span>
                </div>
            </div>
        `;
    });
    
    suggestionsContainer.innerHTML = html;
    suggestionsContainer.style.display = 'block';
    
    // Agregar event listeners a las sugerencias
    document.querySelectorAll('.suggestion-item').forEach(item => {
        item.addEventListener('click', function() {
            const url = this.dataset.url;
            if (url) {
                window.location.href = url;
            }
        });
        
        item.addEventListener('mouseenter', function() {
            document.querySelectorAll('.suggestion-item').forEach(s => s.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

function hideSuggestions() {
    const suggestionsContainer = document.getElementById('searchSuggestions');
    suggestionsContainer.style.display = 'none';
    suggestionsContainer.innerHTML = '';
}

function getTypeIcon(type) {
    switch(type) {
        case 'inventario':
            return 'fas fa-cube';
        case 'categoria':
            return 'fas fa-folder';
        case 'codigo':
            return 'fas fa-barcode';
        case 'ubicacion':
            return 'fas fa-map-marker-alt';
        case 'estado':
            return 'fas fa-info-circle';
        default:
            return 'fas fa-search';
    }
}

function getTypeLabel(type) {
    switch(type) {
        case 'inventario':
            return 'Elemento';
        case 'categoria':
            return 'Categoría';
        case 'codigo':
            return 'Código';
        case 'ubicacion':
            return 'Ubicación';
        case 'estado':
            return 'Estado';
        default:
            return 'Resultado';
    }
}

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
    flex-direction: column;
    padding: 1.5rem;
    gap: 1rem;
}

.header-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
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
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 0;
    padding: 0;
    justify-items: stretch;
    width: 100%;
}

@media (min-width: 1400px) {
    .header-stats {
        grid-template-columns: repeat(5, 1fr);
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }
}

@media (max-width: 1399px) and (min-width: 1200px) {
    .header-stats {
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
    }
}

@media (max-width: 1199px) and (min-width: 900px) {
    .header-stats {
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
}

@media (max-width: 899px) and (min-width: 600px) {
    .header-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.875rem;
    }
}

@media (max-width: 599px) {
    .header-stats {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 1rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}



.stat-item-clickable {
    text-decoration: none;
    color: inherit;
    cursor: pointer;
}

.stat-item-clickable:hover {
    text-decoration: none;
    color: inherit;
    background: #f8fafc;
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.stat-item-clickable:active {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.stat-item-clickable:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.stat-icon {
    font-size: 1.5rem;
    flex-shrink: 0;
}



.stat-icon-success {
    color: #22c55e !important;
}

.stat-icon-primary {
    color: #3b82f6 !important;
}

.stat-icon-warning {
    color: #f59e0b !important;
}

.stat-icon-danger {
    color: #ef4444 !important;
}

.stat-icon-dark {
    color: #4b5563 !important;
}



.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.8rem;
    color: #1e293b;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-weight: 600;
    margin: 0;
    line-height: 1.2;
}

/* Responsive adjustments for mobile */
@media (max-width: 768px) {
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .stat-label {
        font-size: 0.7rem;
        letter-spacing: 0.05em;
    }
    
    .stat-item {
        padding: 1rem;
        gap: 0.75rem;
    }
}

@media (max-width: 480px) {
    .stat-icon {
        width: 36px;
        height: 36px;
        font-size: 0.9rem;
    }
    
    .stat-number {
        font-size: 1.25rem;
    }
    
    .stat-label {
        font-size: 0.65rem;
    }
    
    .stat-item {
        padding: 0.875rem;
        gap: 0.625rem;
    }
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
    overflow: visible;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    margin-bottom: 2rem;
    position: relative;
    z-index: 10;
}

.search-header-and-main {
    padding: 2rem;
    background: #f8fafc;
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

.search-input-group {
    position: relative;
    max-width: 100%;
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

/* Estilos del Autocompletado */
.suggestion-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: all 0.2s ease;
    gap: 12px;
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-item:hover,
.suggestion-item.active {
    background: #f8fafc;
    border-left: 3px solid #3b82f6;
}

.suggestion-icon {
    width: 32px;
    height: 32px;
    background: #f1f5f9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    flex-shrink: 0;
}

.suggestion-item.active .suggestion-icon {
    background: #dbeafe;
    color: #3b82f6;
}

.suggestion-content {
    flex: 1;
    min-width: 0;
}

.suggestion-text {
    font-weight: 500;
    color: #1e293b;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.suggestion-subtitle {
    font-size: 0.875rem;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.suggestion-type {
    flex-shrink: 0;
}

.type-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.type-inventario {
    background: #dbeafe;
    color: #1d4ed8;
}

.type-categoria {
    background: #dcfce7;
    color: #166534;
}

.type-codigo {
    background: #fef3c7;
    color: #92400e;
}

.type-ubicacion {
    background: #fce7f3;
    color: #be185d;
}

.type-estado {
    background: #e0e7ff;
    color: #3730a3;
}

.suggestion-loading {
    padding: 16px;
    text-align: center;
    color: #64748b;
    font-size: 0.875rem;
}

.suggestion-empty {
    padding: 16px;
    text-align: center;
    color: #64748b;
    font-size: 0.875rem;
    font-style: italic;
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

.view-toggle-btn-small {
    background: white;
    border: none;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.view-toggle-btn-small:hover {
    background: #f9fafb;
    color: #374151;
}

.view-toggle-btn-small.active {
    background: #667eea;
    color: white;
}

.search-results-grid {
    margin-bottom: 2rem;
}

.search-results-table {
    margin-bottom: 2rem;
}

/* Elementos Grid Compacto */
.elements-grid-compact {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
}

.element-card-compact {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.element-card-compact:hover {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: translateY(-2px);
}

.element-image-compact {
    height: 160px;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

.element-image-compact img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.3s ease;
}

.element-image-compact img.lazy-load {
    opacity: 0;
}

.element-image-compact img.loaded {
    opacity: 1;
}

.element-image-compact .image-placeholder {
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
}

.element-header-compact {
    margin-bottom: 1rem;
}

.element-name-compact {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin: 0 0 0.5rem 0;
    line-height: 1.3;
}

.element-code-compact {
    background: #f3f4f6;
    color: #6b7280;
    padding: 0.375rem 0.625rem;
    border-radius: 6px;
    font-size: 0.8125rem;
    font-weight: 500;
    font-family: 'Courier New', monospace;
    display: inline-block;
}

.element-details-compact {
    margin-bottom: 1rem;
}

.detail-row-compact {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.detail-label-compact {
    color: #6b7280;
    font-weight: 500;
}

.detail-value-compact {
    color: #374151;
    font-weight: 600;
    text-align: right;
    flex: 1;
    margin-left: 1rem;
}

.element-locations-compact {
    margin-bottom: 1rem;
}

.location-compact {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.8125rem;
    margin-bottom: 0.5rem;
}

.location-info-compact {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
    flex: 1;
}

.location-name-compact {
    color: #374151;
    font-weight: 600;
    font-size: 0.8125rem;
}

.location-quantity-compact {
    color: #6b7280;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge-element-new {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.6875rem;
    font-weight: 600;
    border: 1px solid;
    flex-shrink: 0;
}

.status-icon-compact {
    font-size: 0.6875rem;
    flex-shrink: 0;
}

.status-text-compact {
    white-space: nowrap;
}

.status-disponible {
    color: #059669;
    border-color: #d1fae5;
    background: #ecfdf5;
}

.status-en-uso {
    color: #1d4ed8;
    border-color: #dbeafe;
    background: #eff6ff;
}

.status-en-mantenimiento {
    color: #d97706;
    border-color: #fed7aa;
    background: #fffbeb;
}

.status-dado-de-baja {
    color: #dc2626;
    border-color: #fecaca;
    background: #fef2f2;
}

.status-robado {
    color: #4b5563;
    border-color: #e5e7eb;
    background: #f9fafb;
}

.more-locations-compact {
    color: #6b7280;
    font-size: 0.75rem;
    text-align: center;
    font-style: italic;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.more-locations-compact:hover {
    background: #f3f4f6;
    color: #374151;
}

.element-actions-compact {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding-top: 1rem;
    border-top: 1px solid #f3f4f6;
    margin-top: 1rem;
}

.btn-xs {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    line-height: 1;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-xs:hover {
    transform: translateY(-1px);
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
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
    width: 100%;
}

.category-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    overflow: hidden;
    width: 100%;
    box-sizing: border-box;
    height: 400px;
}

.category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
    border-color: #3b82f6;
}

.category-link {
    display: flex;
    flex-direction: column;
    height: 100%;
    text-decoration: none;
    color: inherit;
    width: 100%;
    padding: 0.5rem 0;
    gap: 0.5rem;
}

.category-link:hover {
    text-decoration: none;
    color: inherit;
}

.category-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    transition: transform 0.3s ease;
    display: block;
}



.category-image-svg {
    width: 100%;
    height: 200px;
    opacity: 0.6;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 8px;
    padding: 60px;
    box-sizing: border-box;
    display: block;
    object-fit: cover;
}

.category-card:hover .category-image {
    transform: scale(1.02);
}

.category-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
    line-height: 1.4;
    text-align: center;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.category-total {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    text-align: center;
}

.total-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: #3b82f6;
    line-height: 1;
}

.total-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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

.inventory-header {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.inventory-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.inventory-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
}

.inventory-stats {
    display: flex;
    gap: 1rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    text-align: center;
    transition: box-shadow 0.2s;
}

.stat-card:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
}

.search-section {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 15px;
}

.search-form .input-group {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 50px;
    overflow: hidden;
}

.search-form .form-control {
    border: none;
    padding: 1rem 1.5rem;
    font-size: 1.1rem;
}

.search-form .input-group-text {
    background: white;
    border: none;
    padding: 1rem 1.5rem;
}

.search-form .btn {
    padding: 1rem 2rem;
    border: none;
    font-weight: 600;
}

.section-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #2c3e50;
}

.section-subtitle {
    font-size: 1.1rem;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.category-card {
    background: white;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    overflow: hidden;
    transition: all 0.3s ease;
}

.category-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: #dee2e6;
}

.category-link {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    text-decoration: none;
    color: inherit;
}

/* Removed conflicting .category-image rule that was limiting size to 60px */

.category-icon {
    font-size: 1.8rem;
    color: #6c757d;
}

.category-content {
    flex: 1;
}

.category-name {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.category-description {
    color: #6c757d;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
}



.stat-item {
    font-size: 0.85rem;
    color: #6c757d;
}

.category-arrow {
    flex-shrink: 0;
    color: #6c757d;
    font-size: 1.2rem;
}

.quick-actions-section {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 15px;
}

.quick-action-card {
    display: block;
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.quick-action-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    color: inherit;
    text-decoration: none;
    border-color: #dee2e6;
}

.quick-action-icon {
    width: 50px;
    height: 50px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.quick-action-icon i {
    font-size: 1.5rem;
    color: #6c757d;
}

/* Colores específicos para cada acción */
.quick-action-card:nth-child(1) .quick-action-icon i {
    color: #28a745;
}

.quick-action-card:nth-child(2) .quick-action-icon i {
    color: #007bff;
}

.quick-action-card:nth-child(3) .quick-action-icon i {
    color: #ffc107;
}

.quick-action-card:nth-child(4) .quick-action-icon i {
    color: #dc3545;
}

.quick-action-content h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.quick-action-content p {
    color: #6c757d;
    margin: 0;
    font-size: 0.9rem;
}

/* Estilos para botones compactos en header */
.quick-actions-compact {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.quick-action-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    text-decoration: none;
    color: inherit;
    transform: translateY(-1px);
}

.quick-action-icon-small {
    width: 40px;
    height: 40px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.quick-action-icon-small i {
    font-size: 1.125rem;
    color: #64748b;
}

/* Colores específicos para iconos de acción */
.quick-action-btn:first-child .quick-action-icon-small {
    background: rgba(34, 197, 94, 0.1);
    border-color: rgba(34, 197, 94, 0.3);
}

.quick-action-btn:first-child .quick-action-icon-small i {
    color: #22c55e;
}

.quick-action-btn:last-child .quick-action-icon-small {
    background: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
}

.quick-action-btn:last-child .quick-action-icon-small i {
    color: #3b82f6;
}

.quick-action-text {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.quick-action-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.2;
}

.quick-action-subtitle {
    font-size: 0.75rem;
    color: #64748b;
    line-height: 1.2;
}

/* Responsive para botones compactos */
@media (max-width: 768px) {
    .quick-actions-compact {
        flex-direction: column;
        gap: 0.5rem;
        width: 100%;
    }
    
    .quick-action-btn {
        width: 100%;
        justify-content: center;
        padding: 0.625rem 0.75rem;
    }
    
    .quick-action-text {
        text-align: center;
    }
}

@media (max-width: 480px) {
    .quick-action-btn {
        padding: 0.5rem;
        gap: 0.5rem;
    }
    
    .quick-action-icon-small {
        width: 32px;
        height: 32px;
    }
    
    .quick-action-icon-small i {
        font-size: 0.875rem;
    }
    
    .quick-action-title {
        font-size: 0.8125rem;
    }
    
    .quick-action-subtitle {
        font-size: 0.6875rem;
    }
}

.empty-state {
    background: white;
    border-radius: 15px;
    padding: 3rem;
}

/* Responsive */
@media (max-width: 1200px) {
    .categories-grid {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }
}

@media (max-width: 768px) {
    .inventory-header {
        text-align: center;
    }
    
    .inventory-stats {
        justify-content: center;
        margin-top: 1rem;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .category-link {
        padding: 1rem;
        gap: 0.75rem;
    }
    
    .category-image-wrapper {
        width: 50px;
        height: 50px;
    }
    
    .category-name {
        font-size: 1rem;
    }
    
    .total-number {
        font-size: 1.25rem;
    }
}

@media (max-width: 480px) {
    .category-link {
        padding: 0.75rem;
        gap: 0.5rem;
    }
    
    .category-image-wrapper {
        width: 40px;
        height: 40px;
    }
}
</style>
@endsection