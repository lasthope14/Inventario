/**
 * Sistema de conservación de estado de navegación
 * Mantiene los filtros y posición de scroll al navegar entre vistas
 */

class NavigationStateManager {
    constructor() {
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.setupReturnUrlHandlers();
            this.setupScrollPosition();
            this.setupViewHandlers();
        });
    }

    /**
     * Configura los manejadores para guardar URLs de retorno
     */
    setupReturnUrlHandlers() {
        // Guardar URL cuando se navega desde inventarios
        if (window.location.pathname === '/inventarios' || window.location.pathname.startsWith('/inventarios')) {
            this.saveCurrentUrlOnNavigation();
        }

        // Guardar URL cuando se navega desde movimientos
        if (window.location.pathname === '/movimientos' || window.location.pathname.startsWith('/movimientos')) {
            this.saveCurrentUrlOnNavigation('movimientos');
        }

        // Guardar URL cuando se navega desde mantenimientos
        if (window.location.pathname === '/mantenimientos' || window.location.pathname.startsWith('/mantenimientos')) {
            this.saveCurrentUrlOnNavigation('mantenimientos');
        }
    }

    /**
     * Guarda la URL actual cuando se hace clic en enlaces de navegación
     */
    saveCurrentUrlOnNavigation(prefix = 'inventarios') {
        const currentUrl = window.location.href;
        
        // Enlaces de "Ver" elementos
        document.querySelectorAll('.ver-item, a[href*="/show"]').forEach(link => {
            link.addEventListener('click', () => {
                sessionStorage.setItem(`${prefix}_return_url`, currentUrl);
            });
        });

        // Enlaces a movimientos y mantenimientos desde inventarios
        if (prefix === 'inventarios') {
            document.querySelectorAll('a[href*="movimientos"], a[href*="mantenimientos"]').forEach(link => {
                link.addEventListener('click', () => {
                    sessionStorage.setItem('inventarios_return_url', currentUrl);
                });
            });
        }
    }

    /**
     * Configura la restauración de posición de scroll
     */
    setupScrollPosition() {
        // Restaurar posición de scroll
        const scrollPosition = sessionStorage.getItem('scroll_position');
        if (scrollPosition) {
            setTimeout(() => {
                window.scrollTo(0, parseInt(scrollPosition));
                sessionStorage.removeItem('scroll_position');
            }, 100);
        }

        // Guardar posición antes de navegar
        window.addEventListener('beforeunload', () => {
            sessionStorage.setItem('scroll_position', window.scrollY);
        });
    }

    /**
     * Configura manejadores específicos para cada vista
     */
    setupViewHandlers() {
        this.setupInventariosHandlers();
        this.setupMovimientosHandlers();
        this.setupMantenimientosHandlers();
    }

    /**
     * Manejadores específicos para inventarios
     */
    setupInventariosHandlers() {
        const volverBtn = document.getElementById('volverBtn');
        if (volverBtn && window.location.pathname.includes('/inventarios/')) {
            volverBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const returnUrl = this.getReturnUrl('inventarios');
                this.navigateToUrl(returnUrl);
            });
        }
    }

    /**
     * Manejadores específicos para movimientos
     */
    setupMovimientosHandlers() {
        const volverBtn = document.getElementById('volverBtn');
        const volverInventarioBtn = document.getElementById('volverInventarioBtn');
        
        if (volverBtn && window.location.pathname.includes('/movimientos/')) {
            volverBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const returnUrl = this.getReturnUrl('inventarios');
                this.navigateToUrl(returnUrl);
            });
        }

        if (volverInventarioBtn) {
            volverInventarioBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const returnUrl = this.getReturnUrl('inventarios');
                this.navigateToUrl(returnUrl);
            });
        }
    }

    /**
     * Manejadores específicos para mantenimientos
     */
    setupMantenimientosHandlers() {
        const volverInventarioBtn = document.getElementById('volverInventarioBtn');
        
        if (volverInventarioBtn) {
            volverInventarioBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const returnUrl = this.getReturnUrl('inventarios');
                this.navigateToUrl(returnUrl);
            });
        }
    }

    /**
     * Obtiene la URL de retorno desde múltiples fuentes
     */
    getReturnUrl(prefix = 'inventarios') {
        let returnUrl = null;

        // 1. Desde parámetros URL
        const urlParams = new URLSearchParams(window.location.search);
        returnUrl = urlParams.get('return_url');

        // 2. Desde sessionStorage
        if (!returnUrl) {
            returnUrl = sessionStorage.getItem(`${prefix}_return_url`);
        }

        // 3. Desde referrer
        if (!returnUrl && document.referrer) {
            const referrerUrl = new URL(document.referrer);
            if (referrerUrl.pathname.includes(`/${prefix}`)) {
                returnUrl = document.referrer;
            }
        }

        // 4. URL por defecto
        if (!returnUrl) {
            switch (prefix) {
                case 'inventarios':
                    returnUrl = '/inventarios';
                    break;
                case 'movimientos':
                    returnUrl = '/movimientos';
                    break;
                case 'mantenimientos':
                    returnUrl = '/mantenimientos';
                    break;
                default:
                    returnUrl = '/inventarios';
            }
        }

        return returnUrl;
    }

    /**
     * Navega a la URL especificada limpiando el estado
     */
    navigateToUrl(url) {
        // Limpiar sessionStorage relacionado
        ['inventarios_return_url', 'movimientos_return_url', 'mantenimientos_return_url'].forEach(key => {
            sessionStorage.removeItem(key);
        });

        // Navegar
        window.location.href = url;
    }

    /**
     * Método público para guardar URL manualmente
     */
    static saveReturnUrl(prefix = 'inventarios') {
        const currentUrl = window.location.href;
        sessionStorage.setItem(`${prefix}_return_url`, currentUrl);
    }

    /**
     * Método público para obtener URL de retorno
     */
    static getReturnUrl(prefix = 'inventarios') {
        return sessionStorage.getItem(`${prefix}_return_url`);
    }
}

// Inicializar el sistema
const navigationManager = new NavigationStateManager();

// Exportar para uso global
window.NavigationStateManager = NavigationStateManager; 