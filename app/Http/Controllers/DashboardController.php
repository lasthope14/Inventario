<?php

namespace App\Http\Controllers;

use App\Models\InventarioUbicacion;
use App\Models\Mantenimiento;
use App\Models\Movimiento;
use App\Models\Ubicacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        $data = [
            'total_inventario' => InventarioUbicacion::sum('cantidad'),
            
            'disponibles' => InventarioUbicacion::where('estado', 'disponible')
                ->sum('cantidad'),
            
            'en_uso' => InventarioUbicacion::where('estado', 'en uso')
                ->sum('cantidad'),
            
            'en_mantenimiento' => InventarioUbicacion::where('estado', 'en mantenimiento')
                ->sum('cantidad'),
            
            'proximos_mantenimientos' => Mantenimiento::with(['inventario:id,nombre', 'solicitadoPor:id,name'])
                ->where(function($query) use ($now) {
                    $query->where('fecha_programada', '>=', $now)
                          ->orWhere('tipo', 'correctivo');
                })
                ->whereNull('fecha_realizado')
                ->orderBy('fecha_programada', 'desc')  // Cambiamos el orderByRaw por un simple orderBy descendente
                ->take(5)
                ->get(),
            
            'mantenimientos_realizados' => Mantenimiento::whereNotNull('fecha_realizado')
                ->count(),
            
            'ubicaciones' => InventarioUbicacion::selectRaw('ubicacion_id, ubicaciones.nombre, SUM(cantidad) as cantidad')
                ->join('ubicaciones', 'inventario_ubicaciones.ubicacion_id', '=', 'ubicaciones.id')
                ->groupBy('ubicacion_id', 'ubicaciones.nombre')
                ->orderBy('cantidad', 'desc')
                ->get(),
            
            'movimientos_recientes' => Movimiento::with([
                    'inventario:id,nombre',
                    'realizadoPor:id,name'
                ])
                ->latest()
                ->take(5)
                ->get(),
            
            'mantenimientos_pendientes' => Mantenimiento::where('fecha_programada', '>=', $now)
                ->whereNull('fecha_realizado')
                ->count(),
            
            'mantenimientos_vencidos' => Mantenimiento::where('fecha_programada', '<', $now)
                ->whereNull('fecha_realizado')
                ->count(),
        ];

        // Calcular el valor total del inventario
        $data['valor_total_inventario'] = InventarioUbicacion::join('inventarios', 
            'inventario_ubicaciones.inventario_id', '=', 'inventarios.id')
            ->selectRaw('SUM(inventario_ubicaciones.cantidad * inventarios.valor_unitario) as total_valor')
            ->value('total_valor') ?? 0;

        return view('dashboard', $data);
    }
}