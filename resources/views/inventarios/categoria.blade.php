@extends('layouts.app')

@section('content')
<div class="container-fluid" data-page="inventarios-categoria">
    

    <!-- Header Profesional -->
    <div class="header-card">
        <div class="header-main">
            <div class="header-top">
                <div class="header-info">
                    <div class="header-title-section">
                        <div class="header-icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="header-title">{{ $categoria->nombre }}</h1>
                            <div class="header-badges">
                                <span class="header-badge">
                                    <i class="fas fa-cube me-1"></i>
                                    {{ $stats->total_elementos ?? 0 }} Elementos
                                </span>
                                <span class="header-badge">
                                    <i class="fas fa-boxes me-1"></i>
                                    {{ $stats->total_unidades ?? 0 }} Unidades
                                </span>
                            </div>
                            

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
                        <a href="{{ route('inventarios.index') }}" class="quick-action-btn">
                            <div class="quick-action-icon-small">
                                <i class="fas fa-arrow-left"></i>
                            </div>
                            <div class="quick-action-text">
                                <span class="quick-action-title">Volver</span>
                                <span class="quick-action-subtitle">Vista principal</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
                
            <div class="header-stats">
                <a href="{{ route('inventarios.categoria', ['categoria' => $categoria->id, 'estado' => 'disponible']) }}" class="stat-item stat-item-clickable">
                    <i class="fas fa-check-circle stat-icon stat-icon-success"></i>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats->disponibles ?? 0 }}</div>
                        <div class="stat-label">Disponibles</div>
                    </div>
                </a>
                <a href="{{ route('inventarios.categoria', ['categoria' => $categoria->id, 'estado' => 'en uso']) }}" class="stat-item stat-item-clickable">
                    <i class="fas fa-play-circle stat-icon stat-icon-primary"></i>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats->en_uso ?? 0 }}</div>
                        <div class="stat-label">En Uso</div>
                    </div>
                </a>
                <a href="{{ route('inventarios.categoria', ['categoria' => $categoria->id, 'estado' => 'en mantenimiento']) }}" class="stat-item stat-item-clickable">
                    <i class="fas fa-tools stat-icon stat-icon-warning"></i>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats->en_mantenimiento ?? 0 }}</div>
                        <div class="stat-label">Mantenimiento</div>
                    </div>
                </a>
                <a href="{{ route('inventarios.categoria', ['categoria' => $categoria->id, 'estado' => 'dado de baja']) }}" class="stat-item stat-item-clickable">
                    <i class="fas fa-ban stat-icon stat-icon-danger"></i>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats->dados_de_baja ?? 0 }}</div>
                        <div class="stat-label">Dados de Baja</div>
                    </div>
                </a>
                <a href="{{ route('inventarios.categoria', ['categoria' => $categoria->id, 'estado' => 'robado']) }}" class="stat-item stat-item-clickable">
                    <i class="fas fa-user-secret stat-icon stat-icon-dark"></i>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats->robados ?? 0 }}</div>
                        <div class="stat-label">Robados</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Sistema de Búsqueda y Filtros Integrado -->
    <div class="advanced-search-container">

        
        <!-- Contenedor Principal de Búsqueda y Filtros -->
        <div class="search-and-filters-wrapper">
            <!-- Búsqueda Principal -->
            <div class="search-input-section mb-3">
                <div class="search-input-container">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchInput" class="form-control search-input-modern" 
                               placeholder="Buscar por nombre, código, serie, marca, modelo..." 
                               autocomplete="off">

                        <button type="button" class="btn-clear-search-modern" id="clearSearch" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="search-suggestions" id="searchSuggestions" style="display: none;"></div>
                </div>
            </div>
            
            <!-- Panel de Filtros con Acordeón -->
            <div class="filters-panel-accordion" id="filtersPanel">
                <div class="filters-header mb-3">
                    <button class="btn btn-link p-0 text-decoration-none w-100 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="true" aria-controls="filtersCollapse">
                        <h6 class="filters-subtitle mb-0 d-flex align-items-center">
                            <span><i class="fas fa-filter me-2"></i>Filtros Avanzados</span>
                        </h6>
                    </button>
                </div>
                
                <div class="collapse show" id="filtersCollapse">
                
                <form id="filtersForm" method="GET" action="{{ route('inventarios.categoria', $categoria->id) }}">
                    <input type="hidden" name="categoria" value="{{ $categoria->id }}">
                    <input type="hidden" id="hiddenSearchInput" name="search" value="">
                    <div class="filters-grid">
                        <!-- Filtro por Elemento -->
                        <div class="filter-item">
                            <label class="filter-label">
                                <i class="fas fa-cube me-1"></i>
                                Elemento
                            </label>

                            <select name="elemento" class="filter-select form-select" id="filterElemento">
                                <option value="">Todos los elementos</option>
                                @foreach($elementos ?? [] as $elemento)
                                    <option value="{{ $elemento }}" {{ request('elemento') == $elemento ? 'selected' : '' }}>{{ $elemento }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Filtro por Marca -->
                        <div class="filter-item">
                            <label class="filter-label">
                                <i class="fas fa-tag me-1"></i>
                                Marca
                            </label>
                            <select name="marca" class="filter-select form-select" id="filterMarca" disabled>
                                <option value="">Selecciona un elemento primero</option>
                            </select>
                        </div>
                        
                        <!-- Filtro por Ubicación -->
                        <div class="filter-item">
                            <label class="filter-label">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Ubicación
                            </label>
                            <select name="ubicacion" class="filter-select form-select" id="filterUbicacion">
                                <option value="">Todas las ubicaciones</option>
                                @foreach($ubicaciones ?? [] as $ubicacion)
                                    <option value="{{ $ubicacion->id }}" {{ request('ubicacion') == $ubicacion->id ? 'selected' : '' }}>{{ $ubicacion->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Filtro por Estado -->
                        <div class="filter-item">
                            <label class="filter-label">
                                <i class="fas fa-info-circle me-1"></i>
                                Estado
                            </label>
                            <select name="estado" class="filter-select form-select" id="filterEstado">
                                <option value="">Todos los estados</option>
                                @foreach($estadosDisponibles ?? [] as $estado)
                                    @php
                                        $estadoLabel = [
                                            'disponible' => 'Disponible',
                                            'en uso' => 'En Uso',
                                            'en mantenimiento' => 'En Mantenimiento',
                                            'dado de baja' => 'Dado de Baja',
                                            'robado' => 'Robado'
                                        ][$estado] ?? ucfirst($estado);
                                    @endphp
                                    <option value="{{ $estado }}" {{ request('estado') == $estado ? 'selected' : '' }}>{{ $estadoLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Filtro por Proveedor -->
                        <div class="filter-item">
                            <label class="filter-label">
                                <i class="fas fa-truck me-1"></i>
                                Proveedor
                            </label>
                            <select name="proveedor" class="filter-select form-select" id="filterProveedor">
                                <option value="">Todos los proveedores</option>
                                @foreach($proveedores ?? [] as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ request('proveedor') == $proveedor->id ? 'selected' : '' }}>{{ $proveedor->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        


                    </div>
                    
                    <div class="filter-actions mt-3">
                        <button type="submit" class="btn btn-primary btn-sm me-2" id="applyFilters">
                            <i class="fas fa-search me-1"></i>
                            Buscar
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="clearFilters">
                            <i class="fas fa-broom me-1"></i>
                            Limpiar
                        </button>
                    </div>
                </form>
                </div> <!-- Cierre del collapse -->
            </div>
            
            <!-- Filtros Activos -->
            <div class="active-filters mt-3" id="activeFilters" style="display: none;">
                <div class="active-filters-header">
                    <span class="active-filters-title">Filtros activos:</span>
                </div>
                <div class="active-filters-list" id="activeFiltersList">
                    <!-- Los filtros activos se mostrarán aquí dinámicamente -->
                </div>
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
                                <span class="element-code-compact">{{ $inventario->codigo_unico ?? $inventario->codigo }}</span>
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
                                <div class="detail-row-compact">
                                    <span class="detail-label-compact">Precio:</span>
                                    <span class="detail-value-compact">${{ number_format($inventario->valor_unitario, 2) }}</span>
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
                            <th>Precio</th>
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
                                    <span class="table-code">{{ $inventario->codigo_unico ?? $inventario->codigo }}</span>
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
                                <td>${{ number_format($inventario->valor_unitario, 2) }}</td>
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
    const searchSuggestions = document.getElementById('searchSuggestions');
    
    // Botones de vista para elementos de categoría
    const categoryToggleBtns = document.querySelectorAll('.view-toggle-btn');
    const elementsGridView = document.getElementById('elements-grid-category');
    const elementsTableView = document.getElementById('elements-table-category');
    
    // ===== FUNCIONES DE AUTOCOMPLETADO =====
    function fetchAutocomplete(query) {
        if (query.length < 2) {
            hideSuggestions();
            return;
        }
        
        fetch(`{{ route('inventarios.categoria.autocomplete', $categoria->id) }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displaySuggestions(data);
            })
            .catch(error => {
                console.error('Error fetching autocomplete:', error);
                hideSuggestions();
            });
    }
    
    function displaySuggestions(suggestions) {
        if (!suggestions || suggestions.length === 0) {
            hideSuggestions();
            return;
        }
        
        const suggestionsHTML = suggestions.map(suggestion => `
            <div class="suggestion-item" data-url="${suggestion.url}">
                <div class="suggestion-icon">
                    <i class="${getSuggestionIcon(suggestion.type)}"></i>
                </div>
                <div class="suggestion-content">
                    <div class="suggestion-text">${suggestion.text}</div>
                    <div class="suggestion-subtitle">${suggestion.subtitle}</div>
                </div>
                <div class="suggestion-type">${getSuggestionTypeText(suggestion.type)}</div>
            </div>
        `).join('');
        
        searchSuggestions.innerHTML = suggestionsHTML;
        searchSuggestions.style.display = 'block';
        
        // Agregar event listeners a las sugerencias
        document.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                if (url) {
                    window.location.href = url;
                }
            });
        });
    }
    
    function hideSuggestions() {
        searchSuggestions.style.display = 'none';
        searchSuggestions.innerHTML = '';
    }
    
    function getSuggestionIcon(type) {
        const icons = {
            'inventario': 'fas fa-cube',
            'marca': 'fas fa-tag',
            'estado': 'fas fa-info-circle'
        };
        return icons[type] || 'fas fa-search';
    }
    
    function getSuggestionTypeText(type) {
        const types = {
            'inventario': 'Elemento',
            'marca': 'Marca',
            'estado': 'Estado'
        };
        return types[type] || 'Resultado';
    }
    
    // ===== FUNCIONES DE UTILIDAD =====
    function getEstadoIcon(estado) {
        const icons = {
            'disponible': 'fas fa-check-circle',
            'en_uso': 'fas fa-play-circle',
            'en uso': 'fas fa-play-circle',
            'en_mantenimiento': 'fas fa-tools',
            'en mantenimiento': 'fas fa-tools',
            'dado_de_baja': 'fas fa-ban',
            'dado de baja': 'fas fa-ban',
            'robado': 'fas fa-user-secret'
        };
        return icons[estado] || 'fas fa-question-circle';
    }
    
    // ===== EVENT LISTENERS =====
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => fetchAutocomplete(query), 300);
            } else {
                hideSuggestions();
            }
            
            // Mostrar/ocultar botón de limpiar
            if (this.value.length > 0) {
                clearSearchBtn.style.display = 'flex';
            } else {
                clearSearchBtn.style.display = 'none';
                hideSuggestions();
            }
        });
        
        searchInput.addEventListener('focus', function() {
            const query = this.value.trim();
            if (query.length >= 2) {
                fetchAutocomplete(query);
            }
        });
        
        searchInput.addEventListener('blur', function() {
            // Delay hiding suggestions to allow clicking on them
            setTimeout(() => hideSuggestions(), 150);
        });
    }
    
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            this.style.display = 'none';
            hideSuggestions();
        });
    }
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
            hideSuggestions();
        }
    });
    

    
    // Event listeners para botones de vista de elementos de categoría
    categoryToggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const viewType = this.getAttribute('data-view');
            applyCategoryView(viewType);
            // Guardar preferencia
            localStorage.setItem('global-view-preference', viewType);
        });
    });
    

    
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

@push('styles')
<style>
/* Incluir todos los estilos de categorias-old.blade.php */
/* Estilos Base */
body {
    background-color: #f8fafc;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Estilos para el acordeón de filtros */
.filters-panel-accordion .btn-link {
    color: inherit !important;
    text-decoration: none !important;
}

.filters-panel-accordion .btn-link:hover {
    color: var(--bs-primary) !important;
}

.transition-icon {
    transition: transform 0.3s ease;
}

.transition-icon.rotated {
    transform: rotate(180deg);
}

.filters-panel-accordion .filters-subtitle {
    font-weight: 600;
    color: #495057;
    margin: 0;
}

.filters-panel-accordion .collapse {
    transition: all 0.3s ease;
}

/* Mejorar el espaciado cuando el acordeón está colapsado */
.filters-panel-accordion .collapsed + .collapse {
    margin-top: 0;
}

/* Reducir el espacio entre filtros y elementos */
.category-elements-section {
    margin-top: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    padding: 1.5rem;
}

/* Color para el icono de elementos */
.category-elements-title i {
    color: #3b82f6;
}

/* Color para el icono del header */
.header-icon i {
    color: #3b82f6;
}

/* Header Profesional */
.header-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    overflow: hidden;
    margin-bottom: 1rem;
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
    margin-bottom: 1rem;
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
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: visible;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    margin-bottom: 2rem;
    position: relative;
    z-index: 10;
}

.search-header-and-main {
    padding: 1rem;
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

.search-input-container {
    position: relative;
    width: 100%;
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
    font-size: 1rem;
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

/* Panel de Filtros Siempre Visible */
.filters-panel-always-visible {
    padding: 1.5rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 1rem;
}

.filters-header {
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 0.75rem;
}

.filters-subtitle {
    color: #374151;
    font-weight: 600;
    margin: 0;
    font-size: 1rem;
    display: flex;
    align-items: center;
}

.search-and-filters-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    padding: 1.5rem;
}

.search-input-section {
    margin-bottom: 1.5rem;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.25rem;
    align-items: end;
}

.filter-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-label {
    font-weight: 600;
    color: #000;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    margin-bottom: 0.25rem;
}

.filter-label i {
    color: #007bff;
    margin-right: 0.5rem;
}

.filter-select {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: white;
    height: 44px;
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}



.filter-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.125rem rgba(102, 126, 234, 0.1);
    outline: none;
}

.filter-select:disabled {
    background: #f8fafc;
    color: #9ca3af;
    cursor: not-allowed;
}

.filter-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
    justify-content: center;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.filter-actions .btn {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    color: #1e293b;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    min-width: 120px;
    max-width: 120px;
    width: 120px;
    justify-content: center;
    flex: 0 0 120px;
    font-size: 0.875rem;
}

.filter-actions .btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    color: #1e293b;
    text-decoration: none;
}

.filter-actions .btn i {
    font-size: 1rem;
}

.filter-actions .btn-primary i {
    color: #3b82f6;
}

.filter-actions .btn-secondary i {
    color: #64748b;
}

/* Filtros Activos */
.active-filters {
    padding: 1rem 1.5rem;
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
    padding: 1rem 1.5rem;
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
    padding: 1rem;
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
    padding: 1rem;
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
    
    .search-and-filters-wrapper {
        padding: 1rem;
    }
    
    .filters-panel-always-visible {
        padding: 1rem;
    }
    
    .filter-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-actions .btn {
        width: 100%;
        justify-content: center;
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
    
    .search-and-filters-wrapper {
        padding: 0.75rem;
    }
    
    .filters-panel-always-visible {
        padding: 0.75rem;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .filter-item {
        margin-bottom: 0.5rem;
    }
    
    .filter-actions {
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .search-title h5 {
        font-size: 1.1rem;
    }
    
    .filters-subtitle {
        font-size: 0.9rem;
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables para el sistema de autocompletado y filtros
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const filtersForm = document.getElementById('filtersForm');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const hiddenSearchInput = document.getElementById('hiddenSearchInput');
    
    let searchTimeout;
    
    // ===== FUNCIONES DE AUTOCOMPLETADO =====
    function fetchAutocomplete(query) {
        if (query.length < 2) {
            hideSuggestions();
            return;
        }
        
        fetch(`{{ route('inventarios.categoria.autocomplete', $categoria->id) }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displaySuggestions(data);
            })
            .catch(error => {
                console.error('Error fetching autocomplete:', error);
                hideSuggestions();
            });
    }
    
    function displaySuggestions(suggestions) {
        const searchSuggestions = document.getElementById('searchSuggestions');
        
        if (!suggestions || suggestions.length === 0) {
            hideSuggestions();
            return;
        }
        
        const suggestionsHTML = suggestions.map(suggestion => `
            <div class="suggestion-item" data-url="${suggestion.url}">
                <div class="suggestion-icon">
                    <i class="${getSuggestionIcon(suggestion.type)}"></i>
                </div>
                <div class="suggestion-content">
                    <div class="suggestion-text">${suggestion.text}</div>
                    <div class="suggestion-subtitle">${suggestion.subtitle}</div>
                </div>
                <div class="suggestion-type">${getSuggestionTypeText(suggestion.type)}</div>
            </div>
        `).join('');
        
        searchSuggestions.innerHTML = suggestionsHTML;
        searchSuggestions.style.display = 'block';
        
        // Agregar event listeners a las sugerencias
        document.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                if (url) {
                    window.location.href = url;
                }
            });
        });
    }
    
    function hideSuggestions() {
        const searchSuggestions = document.getElementById('searchSuggestions');
        if (searchSuggestions) {
            searchSuggestions.style.display = 'none';
            searchSuggestions.innerHTML = '';
        }
    }
    
    function getSuggestionIcon(type) {
        const icons = {
            'inventario': 'fas fa-cube',
            'marca': 'fas fa-tag',
            'estado': 'fas fa-info-circle'
        };
        return icons[type] || 'fas fa-search';
    }
    
    function getSuggestionTypeText(type) {
        const types = {
            'inventario': 'Elemento',
            'marca': 'Marca',
            'estado': 'Estado'
        };
        return types[type] || 'Resultado';
    }
    
    // ===== EVENT LISTENERS PARA AUTOCOMPLETADO =====
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            // Mostrar/ocultar botón de limpiar
            if (clearSearchBtn) {
                clearSearchBtn.style.display = query.length > 0 ? 'flex' : 'none';
            }
            
            // Sincronizar con campo oculto del formulario
            if (hiddenSearchInput) {
                hiddenSearchInput.value = query;
            }
            
            // Autocompletado solo si hay al menos 2 caracteres
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => fetchAutocomplete(query), 300);
            } else {
                hideSuggestions();
            }
        });
        
        searchInput.addEventListener('focus', function() {
            const query = this.value.trim();
            if (query.length >= 2) {
                fetchAutocomplete(query);
            }
        });
        
        searchInput.addEventListener('blur', function() {
            // Delay hiding suggestions to allow clicking on them
            setTimeout(() => hideSuggestions(), 150);
        });
    }
    
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
            }
            if (hiddenSearchInput) {
                hiddenSearchInput.value = '';
            }
            this.style.display = 'none';
            hideSuggestions();
        });
    }
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        const searchSuggestions = document.getElementById('searchSuggestions');
        if (searchInput && searchSuggestions && 
            !searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
            hideSuggestions();
        }
    });
    
    // ===== FILTROS EN CASCADA =====
    const filterElemento = document.getElementById('filterElemento');
    const filterMarca = document.getElementById('filterMarca');
    const filterProveedor = document.getElementById('filterProveedor');
    const filterUbicacion = document.getElementById('filterUbicacion');
    const filterEstado = document.getElementById('filterEstado');
    
    // Función para actualizar proveedores
    // Variable para controlar actualizaciones concurrentes
    let isUpdating = false;
    
    // Función unificada para actualizar todos los filtros (API Unificada - Opción 2)
    function actualizarFiltrosUnificados() {
        if (isUpdating) {
            return;
        }
        
        isUpdating = true;
        
        const elementoSeleccionado = filterElemento ? filterElemento.value : '';
        const marcaSeleccionada = filterMarca ? filterMarca.value : '';
        
        // Guardar valores actuales
        const valoresActuales = {
            proveedor: filterProveedor ? filterProveedor.value : '',
            ubicacion: filterUbicacion ? filterUbicacion.value : '',
            estado: filterEstado ? filterEstado.value : ''
        };
        
        const params = new URLSearchParams({
            categoria_id: '{{ $categoria->id }}'
        });
        
        if (elementoSeleccionado) {
            params.append('elemento', elementoSeleccionado);
        }
        
        if (marcaSeleccionada) {
            params.append('marca', marcaSeleccionada);
        }
        
        const url = `/api/filtros-unificados?${params.toString()}`;
        
        // Mostrar estado de carga
        if (filterProveedor) filterProveedor.innerHTML = '<option value="">Cargando...</option>';
        if (filterUbicacion) filterUbicacion.innerHTML = '<option value="">Cargando...</option>';
        if (filterEstado) filterEstado.innerHTML = '<option value="">Cargando...</option>';
        
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                
                // Actualizar proveedores
                if (filterProveedor && data.proveedores) {
                    filterProveedor.innerHTML = '<option value="">Todos los proveedores</option>';
                    data.proveedores.forEach(proveedor => {
                        const selected = valoresActuales.proveedor == proveedor.id ? 'selected' : '';
                        filterProveedor.innerHTML += `<option value="${proveedor.id}" ${selected}>${proveedor.nombre}</option>`;
                    });
                }
                
                // Actualizar ubicaciones
                if (filterUbicacion && data.ubicaciones) {
                    filterUbicacion.innerHTML = '<option value="">Todas las ubicaciones</option>';
                    data.ubicaciones.forEach(ubicacion => {
                        const selected = valoresActuales.ubicacion == ubicacion.id ? 'selected' : '';
                        filterUbicacion.innerHTML += `<option value="${ubicacion.id}" ${selected}>${ubicacion.nombre}</option>`;
                    });
                }
                
                // Actualizar estados
                if (filterEstado && data.estados) {
                    filterEstado.innerHTML = '<option value="">Todos los estados</option>';
                    data.estados.forEach(estado => {
                        const selected = valoresActuales.estado == estado.value ? 'selected' : '';
                        filterEstado.innerHTML += `<option value="${estado.value}" ${selected}>${estado.label}</option>`;
                    });
                }
                

            })
            .catch(error => {
                
                // Mostrar error en los filtros
                if (filterProveedor) filterProveedor.innerHTML = '<option value="">Error al cargar proveedores</option>';
                if (filterUbicacion) filterUbicacion.innerHTML = '<option value="">Error al cargar ubicaciones</option>';
                if (filterEstado) filterEstado.innerHTML = '<option value="">Error al cargar estados</option>';
            })
            .finally(() => {
                isUpdating = false;
            });
    }
    
    // Funciones individuales para compatibilidad (llaman a la función unificada)
    function actualizarProveedores() {
        actualizarFiltrosUnificados();
    }
    
    function actualizarUbicaciones() {
        actualizarFiltrosUnificados();
    }
    
    function actualizarEstados() {
        actualizarFiltrosUnificados();
    }
    
    if (filterElemento && filterMarca) {
        
        // Botón de test para marcas

        
        filterElemento.addEventListener('change', function() {
            const elementoSeleccionado = this.value;

            
            if (elementoSeleccionado) {
                filterMarca.disabled = false;
                filterMarca.innerHTML = '<option value="">Cargando marcas...</option>';
                
                const url = `/api/marcas-por-elemento?elemento=${encodeURIComponent(elementoSeleccionado)}&categoria_id={{ $categoria->id }}`;

                
                fetch(url)
                    .then(response => {

                        return response.json();
                    })
                    .then(data => {

                        filterMarca.innerHTML = '<option value="">Todas las marcas</option>';
                        data.forEach(marca => {
                            filterMarca.innerHTML += `<option value="${marca}">${marca}</option>`;
                        });
                        // Actualizar todos los filtros después de cargar marcas

                        actualizarProveedores();
                        actualizarUbicaciones();
                        actualizarEstados();
                    })
                    .catch(error => {
                        filterMarca.innerHTML = '<option value="">Error al cargar marcas</option>';
                    });
            } else {
                filterMarca.disabled = true;
                filterMarca.innerHTML = '<option value="">Selecciona un elemento primero</option>';
                // Actualizar todos los filtros cuando no hay elemento seleccionado
                actualizarProveedores();
                actualizarUbicaciones();
                actualizarEstados();
            }
        });
        
        // Event listener para cuando cambia la marca
        filterMarca.addEventListener('change', function() {
            actualizarProveedores();
            actualizarUbicaciones();
            actualizarEstados();
        });
    }
    
    // ===== LIMPIAR FILTROS =====
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            // Redirigir a la URL base de la categoría sin filtros
            window.location.href = '{{ route("inventarios.categoria", $categoria->id) }}';
        });
    }
    
    // ===== SUBMIT HANDLER DEL FORMULARIO =====
    if (filtersForm) {
        
        filtersForm.addEventListener('submit', function(e) {
            
            // Sincronizar el campo oculto con el valor visible antes de enviar
            const visible = document.getElementById('searchInput');
            const hidden = document.getElementById('hiddenSearchInput');
            if (visible && hidden) {
                hidden.value = visible.value.trim();
            }
        });
    }
    
    // ===== VISTA TOGGLE PARA ELEMENTOS DE CATEGORÍA =====
    const viewToggleButtons = document.querySelectorAll('.view-toggle-btn');
    const elementsGridView = document.getElementById('elements-grid-category');
    const elementsTableView = document.getElementById('elements-table-category');
    
    viewToggleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const viewType = this.getAttribute('data-view');
            
            // Actualizar botones activos
            viewToggleButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Cambiar vista
            if (elementsGridView && elementsTableView) {
                if (viewType === 'grid') {
                    elementsGridView.style.display = 'block';
                    elementsTableView.style.display = 'none';
                } else {
                    elementsGridView.style.display = 'none';
                    elementsTableView.style.display = 'block';
                }
            }
        });
    });

    // Restaurar valores de filtros después de cargar la página
    function restaurarFiltros() {
        const elementoSeleccionado = '{{ request("elemento") }}';
        const marcaSeleccionada = '{{ request("marca") }}';
        const proveedorSeleccionado = '{{ request("proveedor") }}';
        
        if (elementoSeleccionado) {

            const filterElemento = document.getElementById('filterElemento');
            if (filterElemento) {

                filterElemento.value = elementoSeleccionado;
                
                // Cargar marcas para el elemento seleccionado
                const categoriaId = '{{ $categoria->id }}';
                const marcasUrl = `/api/marcas-por-elemento?elemento=${encodeURIComponent(elementoSeleccionado)}&categoria_id=${categoriaId}`;

                fetch(marcasUrl)
                    .then(response => response.json())
                     .then(data => {

                         const filterMarca = document.getElementById('filterMarca');
                         if (filterMarca) {

                             filterMarca.innerHTML = '<option value="">Todas las marcas</option>';
                             data.forEach(marca => {
                                 const option = document.createElement('option');
                                 option.value = marca;
                                 option.textContent = marca;
                                 if (marca === marcaSeleccionada) {

                                     option.selected = true;
                                 }
                                 filterMarca.appendChild(option);
                             });
                             filterMarca.disabled = false;
                             
                             // Actualizar proveedores después de restaurar marca
                             if (marcaSeleccionada) {
                                 actualizarProveedores();
                             }
                         }
                    })
                    .catch(error => {
                        console.error('Error al cargar marcas:', error);
                    });
            }
        }
    }

    // Llamar a la función de restauración después de que todo esté cargado
    restaurarFiltros();
    
    // El acordeón ya no necesita JavaScript para iconos
});
</script>
@endpush