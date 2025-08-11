<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\Proveedor;
use App\Models\Ubicacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Versión mejorada de los métodos API para filtros en cascada
 * 
 * Mejoras implementadas:
 * 1. Uso de caché para mejorar rendimiento
 * 2. Queries más eficientes
 * 3. Validación de parámetros
 * 4. Manejo consistente de errores
 * 5. Reducción de complejidad
 */
class InventarioControllerMejorado extends Controller
{
    /**
     * Obtener marcas por elemento (mejorado)
     */
    public function getMarcasPorElementoMejorado(Request $request)
    {
        $request->validate([
            'elemento' => 'required|string',
            'categoria_id' => 'required|integer|exists:categorias,id'
        ]);
        
        $elemento = $request->get('elemento');
        $categoriaId = $request->get('categoria_id');
        
        // Usar caché para mejorar rendimiento
        $cacheKey = "marcas_elemento_{$categoriaId}_{$elemento}";
        
        $marcas = Cache::remember($cacheKey, 300, function() use ($categoriaId, $elemento) {
            return Inventario::where('categoria_id', $categoriaId)
                ->where('nombre', $elemento)
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('marca')
                          ->where('marca', '!=', '')
                          ->where('marca', '!=', 'N/A');
                    })->orWhere(function($q) {
                        $q->whereNotNull('modelo')
                          ->where('modelo', '!=', '')
                          ->where('modelo', '!=', 'N/A');
                    });
                })
                ->select('marca', 'modelo')
                ->distinct()
                ->get()
                ->flatMap(function($item) {
                    $opciones = [];
                    if (!empty($item->marca) && $item->marca !== 'N/A') {
                        $opciones[] = $item->marca;
                    }
                    if (!empty($item->modelo) && $item->modelo !== 'N/A' && $item->modelo !== $item->marca) {
                        $opciones[] = $item->modelo;
                    }
                    return $opciones;
                })
                ->unique()
                ->sort()
                ->values();
        });
        
        return response()->json($marcas);
    }
    
    /**
     * Obtener proveedores por elemento y marca (mejorado)
     */
    public function getProveedoresPorElementoMarcaMejorado(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|integer|exists:categorias,id'
        ]);
        
        $elemento = $request->get('elemento');
        $marca = $request->get('marca');
        $categoriaId = $request->get('categoria_id');
        
        $cacheKey = "proveedores_{$categoriaId}_{$elemento}_{$marca}";
        
        $proveedores = Cache::remember($cacheKey, 300, function() use ($categoriaId, $elemento, $marca) {
            $query = DB::table('inventarios')
                ->join('proveedores', 'inventarios.proveedor_id', '=', 'proveedores.id')
                ->where('inventarios.categoria_id', $categoriaId)
                ->whereNotNull('inventarios.proveedor_id');
            
            if ($elemento) {
                $query->where('inventarios.nombre', $elemento);
            }
            
            if ($marca) {
                $query->where(function($q) use ($marca) {
                    $q->where('inventarios.marca', $marca)
                      ->orWhere('inventarios.modelo', $marca);
                });
            }
            
            return $query->select('proveedores.id', 'proveedores.nombre')
                ->distinct()
                ->orderBy('proveedores.nombre')
                ->get()
                ->map(function($proveedor) {
                    return [
                        'id' => $proveedor->id,
                        'nombre' => $proveedor->nombre
                    ];
                });
        });
        
        return response()->json($proveedores);
    }
    
    /**
     * Obtener ubicaciones por elemento (mejorado)
     */
    public function getUbicacionesPorElementoMejorado(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|integer|exists:categorias,id'
        ]);
        
        $elemento = $request->get('elemento');
        $categoriaId = $request->get('categoria_id');
        
        $cacheKey = "ubicaciones_{$categoriaId}_{$elemento}";
        
        $ubicaciones = Cache::remember($cacheKey, 300, function() use ($categoriaId, $elemento) {
            $query = DB::table('inventario_ubicaciones')
                ->join('inventarios', 'inventario_ubicaciones.inventario_id', '=', 'inventarios.id')
                ->join('ubicaciones', 'inventario_ubicaciones.ubicacion_id', '=', 'ubicaciones.id')
                ->where('inventarios.categoria_id', $categoriaId);
            
            if ($elemento) {
                $query->where('inventarios.nombre', $elemento);
            }
            
            return $query->select('ubicaciones.id', 'ubicaciones.nombre')
                ->distinct()
                ->orderBy('ubicaciones.nombre')
                ->get()
                ->map(function($ubicacion) {
                    return [
                        'id' => $ubicacion->id,
                        'nombre' => $ubicacion->nombre
                    ];
                });
        });
        
        return response()->json($ubicaciones);
    }
    
    /**
     * Obtener estados por elemento (mejorado)
     */
    public function getEstadosPorElementoMejorado(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|integer|exists:categorias,id'
        ]);
        
        $elemento = $request->get('elemento');
        $categoriaId = $request->get('categoria_id');
        
        $cacheKey = "estados_{$categoriaId}_{$elemento}";
        
        $estados = Cache::remember($cacheKey, 300, function() use ($categoriaId, $elemento) {
            $query = DB::table('inventario_ubicaciones')
                ->join('inventarios', 'inventario_ubicaciones.inventario_id', '=', 'inventarios.id')
                ->where('inventarios.categoria_id', $categoriaId)
                ->whereNotNull('inventario_ubicaciones.estado');
            
            if ($elemento) {
                $query->where('inventarios.nombre', $elemento);
            }
            
            $estadosUnicos = $query->select('inventario_ubicaciones.estado')
                ->distinct()
                ->orderBy('inventario_ubicaciones.estado')
                ->pluck('inventario_ubicaciones.estado');
            
            $estadoLabels = [
                'disponible' => 'Disponible',
                'en uso' => 'En Uso',
                'en mantenimiento' => 'En Mantenimiento',
                'dado de baja' => 'Dado de Baja',
                'robado' => 'Robado'
            ];
            
            return $estadosUnicos->map(function($estado) use ($estadoLabels) {
                return [
                    'value' => $estado,
                    'label' => $estadoLabels[$estado] ?? ucfirst($estado)
                ];
            });
        });
        
        return response()->json($estados);
    }
    
    /**
     * Método unificado para obtener todos los filtros de una vez (NUEVO)
     * Esto reduce el número de peticiones HTTP y mejora la experiencia del usuario
     */
    public function getFiltrosUnificados(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|integer|exists:categorias,id'
        ]);
        
        $elemento = $request->get('elemento');
        $marca = $request->get('marca');
        $categoriaId = $request->get('categoria_id');
        
        $cacheKey = "filtros_unificados_{$categoriaId}_{$elemento}_{$marca}";
        
        $filtros = Cache::remember($cacheKey, 300, function() use ($categoriaId, $elemento, $marca) {
            $resultado = [];
            
            // Obtener marcas si hay elemento seleccionado
            if ($elemento) {
                $resultado['marcas'] = Inventario::where('categoria_id', $categoriaId)
                    ->where('nombre', $elemento)
                    ->where(function($query) {
                        $query->where(function($q) {
                            $q->whereNotNull('marca')
                              ->where('marca', '!=', '')
                              ->where('marca', '!=', 'N/A');
                        })->orWhere(function($q) {
                            $q->whereNotNull('modelo')
                              ->where('modelo', '!=', '')
                              ->where('modelo', '!=', 'N/A');
                        });
                    })
                    ->select('marca', 'modelo')
                    ->distinct()
                    ->get()
                    ->flatMap(function($item) {
                        $opciones = [];
                        if (!empty($item->marca) && $item->marca !== 'N/A') {
                            $opciones[] = $item->marca;
                        }
                        if (!empty($item->modelo) && $item->modelo !== 'N/A' && $item->modelo !== $item->marca) {
                            $opciones[] = $item->modelo;
                        }
                        return $opciones;
                    })
                    ->unique()
                    ->sort()
                    ->values();
            } else {
                $resultado['marcas'] = [];
            }
            
            // Obtener proveedores
            $queryProveedores = DB::table('inventarios')
                ->join('proveedores', 'inventarios.proveedor_id', '=', 'proveedores.id')
                ->where('inventarios.categoria_id', $categoriaId)
                ->whereNotNull('inventarios.proveedor_id');
            
            if ($elemento) {
                $queryProveedores->where('inventarios.nombre', $elemento);
            }
            
            if ($marca) {
                $queryProveedores->where(function($q) use ($marca) {
                    $q->where('inventarios.marca', $marca)
                      ->orWhere('inventarios.modelo', $marca);
                });
            }
            
            $resultado['proveedores'] = $queryProveedores
                ->select('proveedores.id', 'proveedores.nombre')
                ->distinct()
                ->orderBy('proveedores.nombre')
                ->get()
                ->map(function($proveedor) {
                    return [
                        'id' => $proveedor->id,
                        'nombre' => $proveedor->nombre
                    ];
                });
            
            // Obtener ubicaciones
            $queryUbicaciones = DB::table('inventario_ubicaciones')
                ->join('inventarios', 'inventario_ubicaciones.inventario_id', '=', 'inventarios.id')
                ->join('ubicaciones', 'inventario_ubicaciones.ubicacion_id', '=', 'ubicaciones.id')
                ->where('inventarios.categoria_id', $categoriaId);
            
            if ($elemento) {
                $queryUbicaciones->where('inventarios.nombre', $elemento);
            }
            
            $resultado['ubicaciones'] = $queryUbicaciones
                ->select('ubicaciones.id', 'ubicaciones.nombre')
                ->distinct()
                ->orderBy('ubicaciones.nombre')
                ->get()
                ->map(function($ubicacion) {
                    return [
                        'id' => $ubicacion->id,
                        'nombre' => $ubicacion->nombre
                    ];
                });
            
            // Obtener estados
            $queryEstados = DB::table('inventario_ubicaciones')
                ->join('inventarios', 'inventario_ubicaciones.inventario_id', '=', 'inventarios.id')
                ->where('inventarios.categoria_id', $categoriaId)
                ->whereNotNull('inventario_ubicaciones.estado');
            
            if ($elemento) {
                $queryEstados->where('inventarios.nombre', $elemento);
            }
            
            $estadosUnicos = $queryEstados
                ->select('inventario_ubicaciones.estado')
                ->distinct()
                ->orderBy('inventario_ubicaciones.estado')
                ->pluck('inventario_ubicaciones.estado');
            
            $estadoLabels = [
                'disponible' => 'Disponible',
                'en uso' => 'En Uso',
                'en mantenimiento' => 'En Mantenimiento',
                'dado de baja' => 'Dado de Baja',
                'robado' => 'Robado'
            ];
            
            $resultado['estados'] = $estadosUnicos->map(function($estado) use ($estadoLabels) {
                return [
                    'value' => $estado,
                    'label' => $estadoLabels[$estado] ?? ucfirst($estado)
                ];
            });
            
            return $resultado;
        });
        
        return response()->json($filtros);
    }
}