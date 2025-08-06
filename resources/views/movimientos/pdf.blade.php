<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Movimientos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .filters {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .filters h3 {
            margin: 0 0 10px 0;
            font-size: 12px;
            color: #333;
        }
        .filter-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
        }
        .filter-label {
            font-weight: bold;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Movimientos de Inventario</h1>
        <p>Generado el: {{ $fecha_generacion }}</p>
        <p>Total de registros: {{ $movimientos->count() }}</p>
    </div>

    @if(!empty($filtros) && (isset($filtros['ubicacion_id']) || isset($filtros['ubicacion_origen']) || isset($filtros['ubicacion_destino']) || isset($filtros['inventario_id'])))
    <div class="filters">
        <h3>Filtros Aplicados:</h3>
        
        @if(isset($filtros['inventario_id']) && $filtros['inventario_id'])
            <div class="filter-item">
                <span class="filter-label">Inventario:</span> 
                @php
                    $inventario = \App\Models\Inventario::find($filtros['inventario_id']);
                @endphp
                {{ $inventario ? $inventario->nombre : 'ID: ' . $filtros['inventario_id'] }}
            </div>
        @endif
        
        @if(isset($filtros['ubicacion_id']) && $filtros['ubicacion_id'])
            @php
                $ubicacionFiltro = $ubicaciones->find($filtros['ubicacion_id']);
            @endphp
            <div class="filter-item">
                <span class="filter-label">Ubicación (Origen o Destino):</span> 
                {{ $ubicacionFiltro ? $ubicacionFiltro->nombre : 'ID: ' . $filtros['ubicacion_id'] }}
            </div>
        @endif
        
        @if(isset($filtros['ubicacion_origen']) && $filtros['ubicacion_origen'])
            @php
                $ubicacionOrigen = $ubicaciones->find($filtros['ubicacion_origen']);
            @endphp
            <div class="filter-item">
                <span class="filter-label">Ubicación Origen:</span> 
                {{ $ubicacionOrigen ? $ubicacionOrigen->nombre : 'ID: ' . $filtros['ubicacion_origen'] }}
            </div>
        @endif
        
        @if(isset($filtros['ubicacion_destino']) && $filtros['ubicacion_destino'])
            @php
                $ubicacionDestino = $ubicaciones->find($filtros['ubicacion_destino']);
            @endphp
            <div class="filter-item">
                <span class="filter-label">Ubicación Destino:</span> 
                {{ $ubicacionDestino ? $ubicacionDestino->nombre : 'ID: ' . $filtros['ubicacion_destino'] }}
            </div>
        @endif
    </div>
    @endif

    @if($movimientos->isEmpty())
        <div class="no-data">
            <p>No hay movimientos registrados con los filtros aplicados.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Elemento</th>
                    <th>Código</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Empleado Origen</th>
                    <th>Empleado Destino</th>
                    <th>Cantidad</th>
                    <th>Realizado por</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movimientos as $movimiento)
                    <tr>
                        <td class="text-center">{{ optional($movimiento->fecha_movimiento)->format('d/m/Y H:i') ?? $movimiento->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ optional($movimiento->inventario)->nombre ?? 'N/A' }}</td>
                        <td class="text-center">{{ optional($movimiento->inventario)->codigo_unico ?? 'N/A' }}</td>
                        <td>
                            @php
                                $ubicacionOrigen = $ubicaciones->find($movimiento->ubicacion_origen);
                            @endphp
                            {{ $ubicacionOrigen ? $ubicacionOrigen->nombre : ($movimiento->ubicacion_origen ?? 'N/A') }}
                        </td>
                        <td>
                            @php
                                $ubicacionDestino = $ubicaciones->find($movimiento->ubicacion_destino);
                            @endphp
                            {{ $ubicacionDestino ? $ubicacionDestino->nombre : ($movimiento->ubicacion_destino ?? 'N/A') }}
                        </td>
                        <td>{{ optional($movimiento->usuarioOrigen)->nombre ?? 'N/A' }}</td>
                        <td>{{ optional($movimiento->usuarioDestino)->nombre ?? 'N/A' }}</td>
                        <td class="text-center">{{ $movimiento->cantidad ?? 1 }}</td>
                        <td>{{ optional($movimiento->realizadoPor)->name ?? 'Usuario eliminado' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Sistema de Inventario - Hidroobras | Reporte generado automáticamente</p>
    </div>
</body>
</html>