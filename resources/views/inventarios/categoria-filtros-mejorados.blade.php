{{-- Versión mejorada de los filtros en cascada --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando filtros mejorados...');
    
    // Referencias a elementos del DOM
    const filterElemento = document.getElementById('filterElemento');
    const filterMarca = document.getElementById('filterMarca');
    const filterProveedor = document.getElementById('filterProveedor');
    const filterUbicacion = document.getElementById('filterUbicacion');
    const filterEstado = document.getElementById('filterEstado');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const filtersForm = document.getElementById('filtersForm');
    
    const categoriaId = '{{ $categoria->id }}';
    
    // Estado de los filtros
    let isUpdating = false;
    
    // Función para deshabilitar/habilitar filtros durante actualizaciones
    function setFiltersLoading(loading) {
        isUpdating = loading;
        const filters = [filterMarca, filterProveedor, filterUbicacion, filterEstado];
        filters.forEach(filter => {
            if (filter) {
                filter.disabled = loading;
                if (loading) {
                    filter.style.opacity = '0.6';
                } else {
                    filter.style.opacity = '1';
                }
            }
        });
    }
    
    // Función para hacer peticiones API con manejo de errores
    async function fetchAPI(url, defaultValue = []) {
        try {
            console.log(`📡 Fetching: ${url}`);
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            const data = await response.json();
            console.log(`✅ Data received:`, data);
            return data;
        } catch (error) {
            console.error(`❌ Error fetching ${url}:`, error);
            return defaultValue;
        }
    }
    
    // Función para actualizar marcas
    async function actualizarMarcas() {
        if (!filterElemento || !filterMarca) return;
        
        const elemento = filterElemento.value;
        const valorActual = filterMarca.value;
        
        if (!elemento) {
            filterMarca.innerHTML = '<option value="">Selecciona un elemento primero</option>';
            filterMarca.disabled = true;
            return;
        }
        
        filterMarca.innerHTML = '<option value="">Cargando marcas...</option>';
        
        const url = `/api/marcas-por-elemento?elemento=${encodeURIComponent(elemento)}&categoria_id=${categoriaId}`;
        const marcas = await fetchAPI(url);
        
        filterMarca.innerHTML = '<option value="">Todas las marcas</option>';
        marcas.forEach(marca => {
            const selected = valorActual === marca ? 'selected' : '';
            filterMarca.innerHTML += `<option value="${marca}" ${selected}>${marca}</option>`;
        });
        
        filterMarca.disabled = false;
    }
    
    // Función para actualizar proveedores
    async function actualizarProveedores() {
        if (!filterProveedor) return;
        
        const elemento = filterElemento?.value || '';
        const marca = filterMarca?.value || '';
        const valorActual = filterProveedor.value;
        
        filterProveedor.innerHTML = '<option value="">Cargando proveedores...</option>';
        
        const params = new URLSearchParams({
            categoria_id: categoriaId,
            elemento: elemento,
            marca: marca
        });
        
        const url = `/api/proveedores-por-elemento-marca?${params.toString()}`;
        const proveedores = await fetchAPI(url);
        
        filterProveedor.innerHTML = '<option value="">Todos los proveedores</option>';
        proveedores.forEach(proveedor => {
            const selected = valorActual == proveedor.id ? 'selected' : '';
            filterProveedor.innerHTML += `<option value="${proveedor.id}" ${selected}>${proveedor.nombre}</option>`;
        });
    }
    
    // Función para actualizar ubicaciones
    async function actualizarUbicaciones() {
        if (!filterUbicacion) return;
        
        const elemento = filterElemento?.value || '';
        const valorActual = filterUbicacion.value;
        
        filterUbicacion.innerHTML = '<option value="">Cargando ubicaciones...</option>';
        
        const params = new URLSearchParams({
            categoria_id: categoriaId,
            elemento: elemento
        });
        
        const url = `/api/ubicaciones-por-elemento?${params.toString()}`;
        const ubicaciones = await fetchAPI(url);
        
        filterUbicacion.innerHTML = '<option value="">Todas las ubicaciones</option>';
        ubicaciones.forEach(ubicacion => {
            const selected = valorActual == ubicacion.id ? 'selected' : '';
            filterUbicacion.innerHTML += `<option value="${ubicacion.id}" ${selected}>${ubicacion.nombre}</option>`;
        });
    }
    
    // Función para actualizar estados
    async function actualizarEstados() {
        if (!filterEstado) return;
        
        const elemento = filterElemento?.value || '';
        const valorActual = filterEstado.value;
        
        filterEstado.innerHTML = '<option value="">Cargando estados...</option>';
        
        const params = new URLSearchParams({
            categoria_id: categoriaId,
            elemento: elemento
        });
        
        const url = `/api/estados-por-elemento?${params.toString()}`;
        const estados = await fetchAPI(url);
        
        filterEstado.innerHTML = '<option value="">Todos los estados</option>';
        estados.forEach(estado => {
            const selected = valorActual === estado.value ? 'selected' : '';
            filterEstado.innerHTML += `<option value="${estado.value}" ${selected}>${estado.label}</option>`;
        });
    }
    
    // Función principal para actualizar todos los filtros dependientes
    async function actualizarFiltrosDependientes() {
        if (isUpdating) {
            console.log('⏳ Ya hay una actualización en progreso, saltando...');
            return;
        }
        
        setFiltersLoading(true);
        
        try {
            // Actualizar marcas primero
            await actualizarMarcas();
            
            // Luego actualizar el resto en paralelo
            await Promise.all([
                actualizarProveedores(),
                actualizarUbicaciones(),
                actualizarEstados()
            ]);
            
            console.log('✅ Todos los filtros actualizados');
        } catch (error) {
            console.error('❌ Error actualizando filtros:', error);
        } finally {
            setFiltersLoading(false);
        }
    }
    
    // Event listeners
    if (filterElemento) {
        filterElemento.addEventListener('change', function() {
            console.log('🔄 Elemento cambiado a:', this.value);
            actualizarFiltrosDependientes();
        });
    }
    
    if (filterMarca) {
        filterMarca.addEventListener('change', function() {
            console.log('🔄 Marca cambiada a:', this.value);
            if (!isUpdating) {
                setFiltersLoading(true);
                Promise.all([
                    actualizarProveedores(),
                    actualizarUbicaciones(),
                    actualizarEstados()
                ]).finally(() => setFiltersLoading(false));
            }
        });
    }
    
    // Limpiar filtros
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            window.location.href = '{{ route("inventarios.categoria", $categoria->id) }}';
        });
    }
    
    // Submit del formulario
    if (filtersForm) {
        filtersForm.addEventListener('submit', function(e) {
            console.log('📤 Enviando formulario...');
            
            // Sincronizar campo de búsqueda
            const visible = document.getElementById('searchInput');
            const hidden = document.getElementById('hiddenSearchInput');
            if (visible && hidden) {
                hidden.value = visible.value.trim();
            }
        });
    }
    
    // Restaurar filtros al cargar la página
    function restaurarFiltros() {
        const elementoSeleccionado = '{{ request("elemento") }}';
        
        if (elementoSeleccionado && filterElemento) {
            filterElemento.value = elementoSeleccionado;
            // Disparar el evento change para cargar los filtros dependientes
            filterElemento.dispatchEvent(new Event('change'));
        }
    }
    
    // Inicializar
    console.log('🎯 Restaurando filtros iniciales...');
    restaurarFiltros();
    
    console.log('✅ Filtros mejorados inicializados correctamente');
});
</script>