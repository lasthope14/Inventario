@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light shadow-sm">
                <div class="card-body py-3">
                    <h1 class="h4 font-weight-bold mb-0 text-gray-800 text-center">Inventario de Herramientas, Materiales y Equipos</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('inventarios.index') }}" class="text-decoration-none">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-2">Total Inventario</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col">
                                <div class="h2 mb-0 font-weight-bold text-gray-800 text-center">{{ $total_inventario }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-boxes fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('inventarios.index', ['estado' => 'disponible']) }}" class="text-decoration-none">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-2">Disponibles</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col">
                                <div class="h2 mb-0 font-weight-bold text-gray-800 text-center">{{ $disponibles }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('inventarios.index', ['estado' => 'en uso']) }}" class="text-decoration-none">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-2">En Uso</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col">
                                <div class="h2 mb-0 font-weight-bold text-gray-800 text-center">{{ $en_uso }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('inventarios.index', ['estado' => 'en mantenimiento']) }}" class="text-decoration-none">
                <div class="card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-2">En Mantenimiento</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col">
                                <div class="h2 mb-0 font-weight-bold text-gray-800 text-center">{{ $en_mantenimiento }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tools fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <a href="{{ route('mantenimientos.index', ['filtro' => 'realizados']) }}" class="text-decoration-none">
                <div class="card border-left-secondary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-2">Mantenimientos Realizados</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col">
                                <div class="h2 mb-0 font-weight-bold text-gray-800 text-center">{{ $mantenimientos_realizados }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-cogs fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-2">Valor Total Inventario</div>
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="h2 mb-0 font-weight-bold text-gray-800 text-center">${{ number_format($valor_total_inventario, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Tablas -->
    <div class="row">
        <div class="col-xl-6 col-lg-6 mb-3">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Inventario por Ubicación</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 250px;">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 mb-3">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Estado de Mantenimientos</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie" style="height: 250px;">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablas -->
    <div class="row">
        <div class="col-xl-6 mb-3">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-calendar-alt mr-2"></i>Próximos Mantenimientos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th><i class="fas fa-tools mr-1"></i>Equipo</th>
                                    <th><i class="far fa-calendar mr-1"></i>Fecha</th>
                                    <th><i class="fas fa-tag mr-1"></i>Tipo</th>
                                    <th><i class="fas fa-user mr-1"></i>Solicitado por</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proximos_mantenimientos as $mantenimiento)
                                <tr class="text-center">
                                    <td class="text-wrap"><a href="{{ route('mantenimientos.show', $mantenimiento->id) }}"><strong>{{ $mantenimiento->inventario->nombre }}</strong></a></td>
                                    <td>{{ $mantenimiento->fecha_programada->format('d/m/Y') }}</td>
                                    <td>{{ ucfirst($mantenimiento->tipo) }}</td>
                                    <td class="text-wrap">{{ $mantenimiento->solicitadoPor ? $mantenimiento->solicitadoPor->name : 'No asignado' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="col-xl-6 mb-3">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-exchange-alt mr-2"></i>Últimos Movimientos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th><i class="fas fa-box mr-1"></i>Equipo</th>
                                    <th><i class="far fa-calendar mr-1"></i>Fecha</th>
                                    <th><i class="fas fa-map-marker-alt mr-1"></i>Origen</th>
                                    <th><i class="fas fa-map-marker-alt mr-1"></i>Destino</th>
                                    <th><i class="fas fa-user mr-1"></i>Realizado por</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movimientos_recientes as $movimiento)
                                <tr class="text-center">
                                    <td class="text-wrap"><a href="{{ route('inventarios.show', $movimiento->inventario->id) }}"><strong>{{ $movimiento->inventario->nombre }}</strong></a></td>
                                    <td>{{ $movimiento->fecha_movimiento->format('d/m/Y') }}</td>
                                    <td class="text-wrap">
                                        @php
                                            $ubicacionOrigen = \App\Models\Ubicacion::find($movimiento->ubicacion_origen);
                                        @endphp
                                        {{ $ubicacionOrigen ? $ubicacionOrigen->nombre : $movimiento->ubicacion_origen }}
                                    </td>
                                    <td class="text-wrap">
                                        @php
                                            $ubicacionDestino = \App\Models\Ubicacion::find($movimiento->ubicacion_destino);
                                        @endphp
                                        {{ $ubicacionDestino ? $ubicacionDestino->nombre : $movimiento->ubicacion_destino }}
                                    </td>
                                    <td class="text-wrap">{{ $movimiento->realizadoPor ? $movimiento->realizadoPor->name : 'Usuario eliminado' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-body {
        flex: 1 1 auto;
        min-height: 1px;
        padding: 1.25rem;
    }
    .text-xs {
        font-size: .8rem;
    }
    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }
    .border-left-primary { border-left: .25rem solid #4e73df!important; }
    .border-left-success { border-left: .25rem solid #1cc88a!important; }
    .border-left-info { border-left: .25rem solid #36b9cc!important; }
    .border-left-warning { border-left: .25rem solid #f6c23e!important; }
    .border-left-secondary { border-left: .25rem solid #858796!important; }
    .border-left-dark { border-left: .25rem solid #5a5c69!important; }
    .chart-area, .chart-pie {
        height: 20rem;
        position: relative;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.075);
    }
    .thead-light th {
        background-color: #f8f9fc;
        color: #5a5c69;
        border-color: #e3e6f0;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table th, .table td {
        white-space: normal;
        word-wrap: break-word;
    }
    .text-wrap {
        white-space: normal !important;
    }
    .table {
        width: 100% !important;
    }

    /* Dark Theme Styles for Dashboard - Contraste Mejorado */
    [data-bs-theme="dark"] .text-gray-800 {
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .text-gray-300 {
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .bg-light {
        background-color: #1e293b !important;
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .text-xs {
        color: inherit !important;
    }

    [data-bs-theme="dark"] .card-header {
        background-color: #334155 !important;
        border-bottom-color: #475569 !important;
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .thead-light th {
        background-color: #334155 !important;
        color: #f8fafc !important;
        border-color: #475569 !important;
    }

    [data-bs-theme="dark"] .table-hover tbody tr:hover {
        background-color: rgba(71, 85, 105, 0.4) !important;
    }

    /* Custom border colors for dashboard cards - Contraste Mejorado */
    [data-bs-theme="dark"] .border-left-primary {
        border-left-color: #60a5fa !important;
    }

    [data-bs-theme="dark"] .border-left-success {
        border-left-color: #22c55e !important;
    }

    [data-bs-theme="dark"] .border-left-info {
        border-left-color: #06b6d4 !important;
    }

    [data-bs-theme="dark"] .border-left-warning {
        border-left-color: #f59e0b !important;
    }

    [data-bs-theme="dark"] .border-left-secondary {
        border-left-color: #64748b !important;
    }

    [data-bs-theme="dark"] .border-left-dark {
        border-left-color: #475569 !important;
    }

    /* Enhanced hover effects for dark mode */
    [data-bs-theme="dark"] .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.6) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos para las gráficas
    var ubicaciones = @json($ubicaciones->pluck('cantidad'));
    var nombresUbicaciones = @json($ubicaciones->pluck('nombre'));
    var estadoMantenimientos = [
        {{ $mantenimientos_realizados }},
        {{ $mantenimientos_pendientes }},
        {{ $mantenimientos_vencidos }}
    ];
    var etiquetasMantenimientos = ['Realizados', 'Pendientes', 'Vencidos'];

    // Function to get current theme
    function getCurrentTheme() {
        return document.documentElement.getAttribute('data-bs-theme') || 'light';
    }

    // Function to get theme-appropriate colors
    function getThemeColors() {
        const isDark = getCurrentTheme() === 'dark';
        return {
            textColor: isDark ? '#f8fafc' : '#5a5c69',
            gridColor: isDark ? '#475569' : '#e3e6f0',
            backgroundColor: isDark ? '#1e293b' : 'white'
        };
    }

    // Function to create charts with theme support
    function createCharts() {
        const colors = getThemeColors();
        
        // Destroy existing charts if they exist and have destroy method
        if (window.barChart && typeof window.barChart.destroy === 'function') {
            window.barChart.destroy();
        }
        if (window.pieChart && typeof window.pieChart.destroy === 'function') {
            window.pieChart.destroy();
        }

        // Clear canvas contexts
        const barCanvas = document.getElementById('barChart');
        const pieCanvas = document.getElementById('pieChart');
        
        if (barCanvas) {
            const barCtx = barCanvas.getContext('2d');
            barCtx.clearRect(0, 0, barCanvas.width, barCanvas.height);
        }
        
        if (pieCanvas) {
            const pieCtx = pieCanvas.getContext('2d');
            pieCtx.clearRect(0, 0, pieCanvas.width, pieCanvas.height);
        }

    // Bar Chart - Inventario por Ubicación
        if (barCanvas) {
            var ctxBar = barCanvas.getContext('2d');
            window.barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: nombresUbicaciones,
            datasets: [{
                label: 'Inventario por Ubicación',
                data: ubicaciones,
                        backgroundColor: 'rgba(96, 165, 250, 0.8)',
                        borderColor: 'rgba(96, 165, 250, 1)',
                borderWidth: 1
            }]
        },
        options: {
                    responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: colors.textColor
                            },
                            grid: {
                                color: colors.gridColor
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: colors.textColor
                            },
                            grid: {
                                color: colors.gridColor
                            }
                        }
            }
        }
    });
        }

    // Pie Chart - Estado de Mantenimientos
        if (pieCanvas) {
            var ctxPie = pieCanvas.getContext('2d');
            window.pieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: etiquetasMantenimientos,
            datasets: [{
                data: estadoMantenimientos,
                        backgroundColor: ['#34d399', '#fbbf24', '#f87171'],
                        hoverBackgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        hoverBorderColor: colors.gridColor,
                        borderWidth: 2,
                        borderColor: colors.backgroundColor
            }]
        },
        options: {
                    responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                            position: 'right',
                            labels: {
                                color: colors.textColor,
                                usePointStyle: true,
                                padding: 20
                            }
                }
            },
            cutout: '60%'
        }
            });
        }
    }

    // Initialize charts when page loads
    document.addEventListener('DOMContentLoaded', function() {
        createCharts();
        
        // Debug: Check if theme is being applied
        console.log('Dashboard loaded');
        console.log('HTML data-bs-theme:', document.documentElement.getAttribute('data-bs-theme'));
        console.log('Current theme:', getCurrentTheme());
    });

    // Listen for theme changes and update charts
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'data-bs-theme') {
                console.log('Theme changed to:', getCurrentTheme());
                // Theme changed, recreate charts
                setTimeout(createCharts, 100);
            }
        });
    });

    // Start observing HTML element for data-bs-theme changes
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-bs-theme']
    });
</script>
@endpush