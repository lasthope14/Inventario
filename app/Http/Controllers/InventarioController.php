<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class InventarioController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        // Si hay parámetros de filtro, redirigir al método search
        if ($request->hasAny(['search', 'categoria', 'marca', 'proveedor', 'estado', 'ubicacion', 'precio_min', 'precio_max'])) {
            return $this->search($request);
        }

        // Vista principal del inventario - similar a homepage de Homecenter
        // Obtener estadísticas globales
        $statsGlobales = DB::table('inventario_ubicaciones')
            ->join('inventarios', 'inventario_ubicaciones.inventario_id', '=', 'inventarios.id')
            ->select([
                DB::raw('COUNT(DISTINCT inventarios.id) as total_elementos'),
                DB::raw('SUM(inventario_ubicaciones.cantidad) as total_unidades'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "disponible" THEN inventario_ubicaciones.cantidad ELSE 0 END) as disponibles'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "en uso" THEN inventario_ubicaciones.cantidad ELSE 0 END) as en_uso'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "en mantenimiento" THEN inventario_ubicaciones.cantidad ELSE 0 END) as en_mantenimiento'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "dado de baja" THEN inventario_ubicaciones.cantidad ELSE 0 END) as dados_de_baja'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "robado" THEN inventario_ubicaciones.cantidad ELSE 0 END) as robados')
            ])
            ->first();

        // Obtener categorías con estadísticas y paginación
        $categorias = Categoria::withCount([
            'inventarios as total_elementos',
            'inventarios as disponibles' => function ($query) {
                $query->whereHas('ubicaciones', function ($q) {
                    $q->where('estado', 'disponible');
                });
            }
        ])->orderBy('nombre')->paginate(12); // 12 categorías por página

        return view('inventarios.index', compact('categorias', 'statsGlobales'));
    }

    public function search(Request $request)
    {
        // Método de búsqueda similar a Homecenter
        $search = $request->get('search');
        $categoria = $request->get('categoria');
        $marca = $request->get('marca');
        $proveedor = $request->get('proveedor');
        $estado = $request->get('estado');
        $ubicacion = $request->get('ubicacion');
        $precio_min = $request->get('precio_min');
        $precio_max = $request->get('precio_max');
        $sort = $request->get('sort', 'nombre');
        $order = $request->get('order', 'asc');
        $per_page = $request->get('per_page', 12);

        // Construir query base
        $query = Inventario::with(['categoria', 'proveedor', 'ubicaciones'])
            ->select('inventarios.*')
            ->distinct();

        // Aplicar filtros
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion', 'LIKE', "%{$search}%")
                  ->orWhere('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('marca', 'LIKE', "%{$search}%")
                  ->orWhere('modelo', 'LIKE', "%{$search}%")
                  ->orWhereHas('categoria', function ($q) use ($search) {
                      $q->where('nombre', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($categoria) {
            $query->where('categoria_id', $categoria);
        }

        if ($marca) {
            $query->where('marca', 'LIKE', "%{$marca}%");
        }

        if ($proveedor) {
            $query->where('proveedor_id', $proveedor);
        }

        if ($estado) {
            $query->whereHas('ubicaciones', function ($q) use ($estado) {
                $q->where('estado', $estado);
            });
        }

        if ($ubicacion) {
            $query->whereHas('ubicaciones', function ($q) use ($ubicacion) {
                $q->where('ubicacion_id', $ubicacion);
            });
        }

        if ($precio_min) {
            $query->where('valor_unitario', '>=', $precio_min);
        }

        if ($precio_max) {
            $query->where('valor_unitario', '<=', $precio_max);
        }

        // Aplicar ordenamiento
        switch ($sort) {
            case 'precio':
                $query->orderBy('valor_unitario', $order);
                break;
            case 'fecha':
                $query->orderBy('created_at', $order);
                break;
            case 'categoria':
                $query->join('categorias', 'inventarios.categoria_id', '=', 'categorias.id')
                      ->orderBy('categorias.nombre', $order);
                break;
            default:
                $query->orderBy('nombre', $order);
        }

        // Obtener resultados paginados
        $inventarios = $query->paginate($per_page)->withQueryString();

        // Obtener datos para filtros
        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get();
        $ubicaciones = Ubicacion::orderBy('nombre')->get();
        $marcas = Inventario::select('marca')
            ->whereNotNull('marca')
            ->where('marca', '!=', '')
            ->distinct()
            ->orderBy('marca')
            ->pluck('marca');

        // Estadísticas de búsqueda
        $totalResultados = $inventarios->total();
        $categoriaActual = $categoria ? Categoria::find($categoria) : null;

        return view('inventarios.search', compact(
            'inventarios',
            'categorias',
            'proveedores',
            'ubicaciones',
            'marcas',
            'search',
            'categoria',
            'categoriaActual',
            'marca',
            'proveedor',
            'estado',
            'ubicacion',
            'precio_min',
            'precio_max',
            'sort',
            'order',
            'per_page',
            'totalResultados'
        ));
    }

    public function showCategorias(Request $request)
    {
        // Solo redirigir a búsqueda si hay búsqueda por texto, no por filtros
        if ($request->filled('search')) {
            return $this->searchInventarios($request);
        }
        
        // Obtener estadísticas globales eficientemente
        $statsGlobales = DB::table('inventario_ubicaciones')
            ->join('inventarios', 'inventario_ubicaciones.inventario_id', '=', 'inventarios.id')
            ->select([
                DB::raw('COUNT(DISTINCT inventarios.id) as total_elementos'),
                DB::raw('SUM(inventario_ubicaciones.cantidad) as total_unidades'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "disponible" THEN inventario_ubicaciones.cantidad ELSE 0 END) as disponibles'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "en uso" THEN inventario_ubicaciones.cantidad ELSE 0 END) as en_uso'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "en mantenimiento" THEN inventario_ubicaciones.cantidad ELSE 0 END) as en_mantenimiento'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "dado de baja" THEN inventario_ubicaciones.cantidad ELSE 0 END) as dados_de_baja'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "robado" THEN inventario_ubicaciones.cantidad ELSE 0 END) as robados')
            ])
            ->first();

        // Construir query base para categorías
        $categoriasQuery = DB::table('categorias')
            ->leftJoin('inventarios', 'categorias.id', '=', 'inventarios.categoria_id')
            ->leftJoin('inventario_ubicaciones', 'inventarios.id', '=', 'inventario_ubicaciones.inventario_id');

        // Aplicar filtro de categoría si existe
        if ($request->filled('categoria')) {
            $categoriasQuery->where('categorias.id', $request->categoria);
        }

        // Aplicar filtro de estado si existe
        if ($request->filled('estado')) {
            $categoriasQuery->where('inventario_ubicaciones.estado', $request->estado)
                           ->havingRaw('SUM(CASE WHEN inventario_ubicaciones.estado = ? THEN inventario_ubicaciones.cantidad ELSE 0 END) > 0', [$request->estado]);
        }

        // Obtener categorías con sus estadísticas
        $categorias = $categoriasQuery
            ->select([
                'categorias.id',
                'categorias.nombre',
                DB::raw('COUNT(DISTINCT inventarios.id) as total_elementos'),
                DB::raw('COALESCE(SUM(inventario_ubicaciones.cantidad), 0) as total_unidades'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "disponible" THEN inventario_ubicaciones.cantidad ELSE 0 END) as disponibles'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "en uso" THEN inventario_ubicaciones.cantidad ELSE 0 END) as en_uso'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "en mantenimiento" THEN inventario_ubicaciones.cantidad ELSE 0 END) as en_mantenimiento'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "dado de baja" THEN inventario_ubicaciones.cantidad ELSE 0 END) as dados_de_baja'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "robado" THEN inventario_ubicaciones.cantidad ELSE 0 END) as robados')
            ])
            ->groupBy('categorias.id', 'categorias.nombre')
            ->having('total_elementos', '>', 0)
            ->orderBy('categorias.nombre')
            ->get();

        // Obtener todas las categorías para el filtro (sin filtros aplicados)
        $todasCategorias = DB::table('categorias')
            ->leftJoin('inventarios', 'categorias.id', '=', 'inventarios.categoria_id')
            ->select('categorias.id', 'categorias.nombre')
            ->groupBy('categorias.id', 'categorias.nombre')
            ->havingRaw('COUNT(inventarios.id) > 0')
            ->orderBy('categorias.nombre')
            ->get();

        // Obtener elementos por categoría para la vista integrada
        $elementosPorCategoria = collect();
        foreach ($categorias as $categoria) {
            $elementos = Inventario::with(['ubicaciones.ubicacion', 'categoria', 'proveedor'])
                ->where('categoria_id', $categoria->id)
                ->orderBy('nombre')
                ->get();
            
            $elementosPorCategoria->put($categoria->id, $elementos);
        }

        return view('inventarios.categorias', compact('categorias', 'statsGlobales', 'todasCategorias', 'elementosPorCategoria'));
    }

    /**
     * Mostrar vista de catálogo para una categoría específica (estilo Homecenter)
     */
    public function showCategoria(Request $request, $categoriaId)
    {
        // Obtener la categoría
        $categoria = Categoria::findOrFail($categoriaId);
        
        // Query base para inventarios de esta categoría
        $query = Inventario::with(['ubicaciones.ubicacion', 'categoria', 'proveedor'])
            ->where('categoria_id', $categoriaId);
        
        // Aplicar filtros de búsqueda si existen
        if ($request->filled('search')) {
            $searchTerm = mb_strtolower(trim($request->search));
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(numero_serie) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(codigo_unico) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(marca) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(modelo) LIKE ?', ['%' . $searchTerm . '%']);
            });
        }
        
        // Aplicar otros filtros
        if ($request->filled('marca')) {
            $query->where('marca', $request->marca);
        }
        
        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }
        
        if ($request->filled('estado')) {
            $query->whereHas('ubicaciones', function($q) use ($request) {
                $q->where('estado', $request->estado);
            });
        }
        
        if ($request->filled('ubicacion')) {
            $query->whereHas('ubicaciones', function($q) use ($request) {
                $q->where('ubicacion_id', $request->ubicacion);
            });
        }
        
        if ($request->filled('elemento')) {
            $query->where('nombre', $request->elemento);
        }
        
        // Filtros de rango de valores
        if ($request->filled('valor_min')) {
            $query->where('valor_unitario', '>=', $request->valor_min);
        }
        
        if ($request->filled('valor_max')) {
            $query->where('valor_unitario', '<=', $request->valor_max);
        }
        
        // Ordenación
        $sortBy = $request->get('sort', 'nombre');
        $sortOrder = $request->get('order', 'asc');
        
        switch ($sortBy) {
            case 'precio':
                $query->orderBy('valor_unitario', $sortOrder);
                break;
            case 'fecha':
                $query->orderBy('fecha_compra', $sortOrder);
                break;
            case 'marca':
                $query->orderBy('marca', $sortOrder);
                break;
            default:
                $query->orderBy('nombre', $sortOrder);
        }
        
        // Paginación
        $perPage = $request->get('per_page', 24); // 24 elementos por página (6x4 grid)
        $inventarios = $query->paginate($perPage)->withQueryString();
        
        // Obtener datos para filtros
        $marcas = Inventario::where('categoria_id', $categoriaId)
            ->whereNotNull('marca')
            ->where('marca', '!=', '')
            ->distinct()
            ->pluck('marca')
            ->sort()
            ->values();
            
        $proveedores = Proveedor::whereHas('inventarios', function($q) use ($categoriaId) {
            $q->where('categoria_id', $categoriaId);
        })->orderBy('nombre')->get();
        
        $ubicaciones = Ubicacion::whereHas('inventarios', function($q) use ($categoriaId) {
            $q->where('categoria_id', $categoriaId);
        })->orderBy('nombre')->get();
        
        // Obtener elementos únicos de la categoría
        $elementos = Inventario::where('categoria_id', $categoriaId)
            ->whereNotNull('nombre')
            ->where('nombre', '!=', '')
            ->distinct()
            ->pluck('nombre')
            ->sort()
            ->values();
        
        // Estadísticas de la categoría
        $stats = DB::table('inventario_ubicaciones')
            ->join('inventarios', 'inventario_ubicaciones.inventario_id', '=', 'inventarios.id')
            ->where('inventarios.categoria_id', $categoriaId)
            ->select([
                DB::raw('COUNT(DISTINCT inventarios.id) as total_elementos'),
                DB::raw('SUM(inventario_ubicaciones.cantidad) as total_unidades'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "disponible" THEN inventario_ubicaciones.cantidad ELSE 0 END) as disponibles'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "en uso" THEN inventario_ubicaciones.cantidad ELSE 0 END) as en_uso'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "en mantenimiento" THEN inventario_ubicaciones.cantidad ELSE 0 END) as en_mantenimiento'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "dado de baja" THEN inventario_ubicaciones.cantidad ELSE 0 END) as dados_de_baja'),
                DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "robado" THEN inventario_ubicaciones.cantidad ELSE 0 END) as robados')
            ])
            ->first();
        
        return view('inventarios.categoria', compact(
            'categoria', 'inventarios', 'marcas', 'proveedores', 'ubicaciones', 'elementos', 'stats'
        ));
    }

    public function searchInventarios(Request $request)
    {
        $searchTerm = mb_strtolower(trim($request->search));
        
        $query = Inventario::with(['ubicaciones.ubicacion', 'categoria', 'proveedor']);
        
        // Aplicar búsqueda por texto si existe
        if (!empty($searchTerm)) {
            $query->where(function($q) use ($searchTerm) {
                // Primero intentar búsqueda exacta por número de serie
                $q->where(function($exactQ) use ($searchTerm) {
                    $exactQ->whereRaw('LOWER(numero_serie) = ?', [strtolower($searchTerm)])
                           ->orWhereRaw('LOWER(codigo_unico) = ?', [strtolower($searchTerm)]);
                });
                
                // Si no hay resultados exactos, buscar coincidencias parciales
                $q->orWhere(function($partialQ) use ($searchTerm) {
                        $partialQ->whereRaw('LOWER(nombre) LIKE ?', ['%' . $searchTerm . '%'])
                                ->orWhereRaw('LOWER(numero_serie) LIKE ?', ['%' . $searchTerm . '%'])
                            ->orWhereRaw('LOWER(codigo_unico) LIKE ?', ['%' . $searchTerm . '%'])
                            ->orWhereRaw('LOWER(marca) LIKE ?', ['%' . $searchTerm . '%'])
                            ->orWhereRaw('LOWER(modelo) LIKE ?', ['%' . $searchTerm . '%']);
                });
            });
        }

        // Filtros avanzados
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        if ($request->filled('marca')) {
            $query->where('marca', $request->marca);
                    }

        if ($request->filled('ubicacion_id')) {
            $query->whereHas('ubicaciones', function($q) use ($request) {
                $q->where('ubicacion_id', $request->ubicacion_id);
            });
        }

        if ($request->filled('estado')) {
            $query->whereHas('ubicaciones', function($q) use ($request) {
                $q->where('estado', $request->estado);
            });
        }

        // Filtros de rango de valores
        if ($request->filled('valor_min')) {
            $query->where('valor_unitario', '>=', $request->valor_min);
        }

        if ($request->filled('valor_max')) {
            $query->where('valor_unitario', '<=', $request->valor_max);
        }

        // Filtros de fecha
        if ($request->filled('fecha_compra_desde')) {
            $query->where('fecha_compra', '>=', $request->fecha_compra_desde);
        }

        if ($request->filled('fecha_compra_hasta')) {
            $query->where('fecha_compra', '<=', $request->fecha_compra_hasta);
                            }

        // Ordenación inteligente
        if (!empty($searchTerm)) {
            $query->addSelect([
                '*',
                DB::raw("
                    CASE 
                        WHEN LOWER(numero_serie) = '" . strtolower($searchTerm) . "' THEN 100
                        WHEN LOWER(codigo_unico) = '" . strtolower($searchTerm) . "' THEN 90
                        WHEN LOWER(nombre) = '" . strtolower($searchTerm) . "' THEN 80
                        WHEN LOWER(numero_serie) LIKE '" . strtolower($searchTerm) . "%' THEN 70
                        WHEN LOWER(codigo_unico) LIKE '" . strtolower($searchTerm) . "%' THEN 60
                        WHEN LOWER(nombre) LIKE '" . strtolower($searchTerm) . "%' THEN 50
                        WHEN LOWER(marca) LIKE '" . strtolower($searchTerm) . "%' THEN 45
                        WHEN LOWER(numero_serie) LIKE '%" . strtolower($searchTerm) . "%' THEN 40
                        WHEN LOWER(codigo_unico) LIKE '%" . strtolower($searchTerm) . "%' THEN 30
                        WHEN LOWER(nombre) LIKE '%" . strtolower($searchTerm) . "%' THEN 20
                        WHEN LOWER(marca) LIKE '%" . strtolower($searchTerm) . "%' THEN 15
                        ELSE 0 
                    END as search_relevance
                ")
            ]);
            $query->orderByDesc('search_relevance')->orderBy('nombre');
        } else {
            // Ordenación por defecto cuando no hay búsqueda por texto
            $orderBy = $request->get('order_by', 'nombre');
            $orderDirection = $request->get('order_direction', 'asc');
            
            $validOrderFields = ['nombre', 'codigo_unico', 'marca', 'valor_unitario', 'fecha_compra', 'created_at'];
            if (in_array($orderBy, $validOrderFields)) {
                $query->orderBy($orderBy, $orderDirection);
            } else {
                $query->orderBy('nombre', 'asc');
            }
        }

        $inventarios = $query->paginate(20);

        // Obtener opciones para filtros (dinámicos basados en los resultados actuales)
        $filtros = $this->getFiltrosDisponibles($request);
        
        // Si hay filtros o búsqueda, mostrar también las categorías para contexto
        $categorias = collect();
        $statsGlobales = null;
        
        if ($request->filled(['search', 'categoria', 'estado', 'order_by'])) {
            // Obtener estadísticas globales
            $statsGlobales = DB::table('inventario_ubicaciones')
                ->join('inventarios', 'inventario_ubicaciones.inventario_id', '=', 'inventarios.id')
                ->select([
                    DB::raw('COUNT(DISTINCT inventarios.id) as total_elementos'),
                    DB::raw('SUM(inventario_ubicaciones.cantidad) as total_unidades'),
                    DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "disponible" THEN inventario_ubicaciones.cantidad ELSE 0 END) as disponibles'),
                    DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "en uso" THEN inventario_ubicaciones.cantidad ELSE 0 END) as en_uso'),
                    DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "en mantenimiento" THEN inventario_ubicaciones.cantidad ELSE 0 END) as en_mantenimiento'),
                    DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "dado de baja" THEN inventario_ubicaciones.cantidad ELSE 0 END) as dados_de_baja'),
                    DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "robado" THEN inventario_ubicaciones.cantidad ELSE 0 END) as robados')
                ])
                ->first();

            // Obtener categorías para los filtros
            $categorias = DB::table('categorias')
                ->leftJoin('inventarios', 'categorias.id', '=', 'inventarios.categoria_id')
                ->leftJoin('inventario_ubicaciones', 'inventarios.id', '=', 'inventario_ubicaciones.inventario_id')
                ->select([
                    'categorias.id',
                    'categorias.nombre',
                    DB::raw('COUNT(DISTINCT inventarios.id) as total_elementos'),
                    DB::raw('COALESCE(SUM(inventario_ubicaciones.cantidad), 0) as total_unidades'),
                    DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "disponible" THEN inventario_ubicaciones.cantidad ELSE 0 END) as disponibles'),
                    DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "en uso" THEN inventario_ubicaciones.cantidad ELSE 0 END) as en_uso'),
                    DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "en mantenimiento" THEN inventario_ubicaciones.cantidad ELSE 0 END) as en_mantenimiento'),
                    DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "dado de baja" THEN inventario_ubicaciones.cantidad ELSE 0 END) as dados_de_baja'),
                    DB::raw('SUM(CASE WHEN inventario_ubicaciones.estado = "robado" THEN inventario_ubicaciones.cantidad ELSE 0 END) as robados')
                ])
                ->groupBy('categorias.id', 'categorias.nombre')
                ->having('total_elementos', '>', 0)
                ->orderBy('categorias.nombre')
                ->get();
        }

        return view('inventarios.categorias', compact('inventarios', 'searchTerm', 'filtros', 'categorias', 'statsGlobales'));
    }

    /**
     * API endpoint para autocompletado de búsqueda
     */
    public function autocomplete(Request $request)
    {
        $query = trim($request->get('q', ''));
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $suggestions = [];
        $queryLower = strtolower($query);
        
        // Buscar en inventarios por nombre, código único, número de serie, marca y modelo
        $inventarios = Inventario::with(['ubicaciones.ubicacion'])
            ->where(function($q) use ($query, $queryLower) {
                $q->where('nombre', 'LIKE', "%{$query}%")
                  ->orWhere('codigo_unico', 'LIKE', "%{$query}%")
                  ->orWhere('numero_serie', 'LIKE', "%{$query}%")
                  ->orWhere('marca', 'LIKE', "%{$query}%")
                  ->orWhere('modelo', 'LIKE', "%{$query}%");
            })
            ->orderByRaw("CASE 
                WHEN LOWER(nombre) LIKE ? THEN 1
                WHEN LOWER(codigo_unico) LIKE ? THEN 2
                WHEN LOWER(numero_serie) LIKE ? THEN 3
                ELSE 4
            END", [
                "%{$queryLower}%", 
                "%{$queryLower}%", 
                "%{$queryLower}%"
            ])
            ->limit(8)
            ->get(['id', 'nombre', 'codigo_unico', 'numero_serie', 'marca', 'modelo']);
            
        foreach ($inventarios as $inventario) {
            // Crear subtítulo más informativo
            $subtitle_parts = [];
            
            // Manejar múltiples ubicaciones y estados
            $ubicaciones = $inventario->ubicaciones;
            if ($ubicaciones->count() > 0) {
                $primeraUbicacion = $ubicaciones->first();
                if ($primeraUbicacion && $primeraUbicacion->ubicacion) {
                    // Agregar ubicación
                    if ($ubicaciones->count() > 1) {
                        $adicionales = $ubicaciones->count() - 1;
                        $subtitle_parts[] = 'Ubicación: ' . $primeraUbicacion->ubicacion->nombre . ' y ' . $adicionales . ' más';
                    } else {
                        $subtitle_parts[] = 'Ubicación: ' . $primeraUbicacion->ubicacion->nombre;
                    }
                    
                    // Agregar estado
                    if ($primeraUbicacion->estado) {
                        $subtitle_parts[] = 'Estado: ' . ucfirst($primeraUbicacion->estado);
                    }
                }
            }
            
            if ($inventario->numero_serie) {
                $subtitle_parts[] = 'Serie: ' . $inventario->numero_serie;
            }
            if ($inventario->marca) {
                $subtitle_parts[] = $inventario->marca;
            }
            
            $subtitle = implode(' • ', $subtitle_parts);
            

            
            $suggestions[] = [
                'type' => 'inventario',
                'id' => $inventario->id,
                'text' => $inventario->nombre,
                'subtitle' => $subtitle ?: 'Elemento de inventario',
                'url' => route('inventarios.show', $inventario->id)
            ];
        }
        
        // Buscar en categorías
        $categorias = Categoria::where('nombre', 'LIKE', "%{$query}%")
            ->limit(3)
            ->get(['id', 'nombre']);
            
        foreach ($categorias as $categoria) {
            $suggestions[] = [
                'type' => 'categoria',
                'id' => $categoria->id,
                'text' => $categoria->nombre,
                'subtitle' => 'Categoría',
                'url' => route('inventarios.categoria', $categoria->id)
            ];
        }
        
        // Buscar en ubicaciones
        $ubicaciones = Ubicacion::where('nombre', 'LIKE', "%{$query}%")
            ->limit(3)
            ->get(['id', 'nombre']);
            
        foreach ($ubicaciones as $ubicacion) {
            $suggestions[] = [
                'type' => 'ubicacion',
                'id' => $ubicacion->id,
                'text' => $ubicacion->nombre,
                'subtitle' => 'Ubicación',
                'url' => route('inventarios.index', ['ubicacion' => $ubicacion->id])
            ];
        }
        
        // Buscar por estados si coincide
        $estados = ['disponible', 'en uso', 'en mantenimiento', 'dado de baja', 'robado'];
        $estadosCoincidentes = array_filter($estados, function($estado) use ($queryLower) {
            return strpos(strtolower($estado), $queryLower) !== false;
        });
        
        foreach ($estadosCoincidentes as $estado) {
            $suggestions[] = [
                'type' => 'estado',
                'id' => $estado,
                'text' => ucfirst($estado),
                'subtitle' => 'Estado',
                'url' => route('inventarios.index', ['estado' => $estado])
            ];
        }
        
        // Limitar el total de sugerencias
        $suggestions = array_slice($suggestions, 0, 12);
        
        return response()->json($suggestions);
    }

    /**
     * Obtiene los filtros disponibles de forma dinámica
     */
    public function getFiltrosDisponibles(Request $request)
    {
        $baseQuery = Inventario::with(['categoria', 'proveedor', 'ubicaciones.ubicacion']);
        
        // Aplicar búsqueda por texto si existe para filtrar opciones
        $searchTerm = mb_strtolower(trim($request->search));
        if (!empty($searchTerm)) {
            $baseQuery->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(numero_serie) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(codigo_unico) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(marca) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(modelo) LIKE ?', ['%' . $searchTerm . '%']);
            });
            }
        
            return [
            'categorias' => Categoria::whereIn('id', 
                (clone $baseQuery)->distinct()->pluck('categoria_id')
            )->orderBy('nombre')->get(),
            
            'proveedores' => Proveedor::whereIn('id', 
                (clone $baseQuery)->distinct()->pluck('proveedor_id')
            )->orderBy('nombre')->get(),
            
            'marcas' => (clone $baseQuery)->whereNotNull('marca')
                ->distinct()
                ->orderBy('marca')
                ->pluck('marca'),
            
            'ubicaciones' => Ubicacion::whereIn('id', 
                DB::table('inventario_ubicaciones')
                    ->whereIn('inventario_id', (clone $baseQuery)->pluck('id'))
                    ->distinct()
                    ->pluck('ubicacion_id')
            )->orderBy('nombre')->get(),
            
            'estados' => DB::table('inventario_ubicaciones')
                ->whereIn('inventario_id', (clone $baseQuery)->pluck('id'))
                ->distinct()
                ->pluck('estado')
                ->filter()
                ->sort(),
            
            'rango_valores' => [
                'min' => (clone $baseQuery)->whereNotNull('valor_unitario')->min('valor_unitario') ?? 0,
                'max' => (clone $baseQuery)->whereNotNull('valor_unitario')->max('valor_unitario') ?? 0
            ],
            
            'rango_fechas' => [
                'min' => (clone $baseQuery)->whereNotNull('fecha_compra')->min('fecha_compra'),
                'max' => (clone $baseQuery)->whereNotNull('fecha_compra')->max('fecha_compra')
            ]
        ];
    }

    /**
     * API endpoint para obtener filtros dinámicos vía AJAX
     */
    public function getFiltrosAjax(Request $request)
    {
        $filtros = $this->getFiltrosDisponibles($request);
        return response()->json($filtros);
    }

    /**
     * Búsqueda instantánea vía AJAX
     */
    public function searchInstantaneo(Request $request)
    {
        $query = Inventario::with(['categoria', 'ubicaciones.ubicacion']);
        
        // Búsqueda por texto
        if ($request->filled('search')) {
            $searchTerm = mb_strtolower(trim($request->search));
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(numero_serie) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(codigo_unico) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(marca) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(modelo) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereHas('categoria', function($q) use ($searchTerm) {
                      $q->whereRaw('LOWER(nombre) LIKE ?', ['%' . $searchTerm . '%']);
                  });
            });
        }

        // Filtros en cascada
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('elemento_nombre')) {
            $query->where('nombre', $request->elemento_nombre);
        }

        if ($request->filled('marca')) {
            $query->where('marca', $request->marca);
        }
        
        if ($request->filled('estado')) {
            $query->whereHas('ubicaciones', function($q) use ($request) {
                $q->where('estado', $request->estado);
            });
        }

        if ($request->filled('ubicacion_id')) {
            $query->whereHas('ubicaciones', function($q) use ($request) {
                $q->where('ubicacion_id', $request->ubicacion_id);
            });
        }

        $inventarios = $query->limit(50)->get();

        // Obtener permisos del usuario actual
        $user = auth()->user();

        return response()->json([
            'inventarios' => $inventarios->map(function($inventario) use ($user) {
                return [
                    'id' => $inventario->id,
                    'nombre' => $inventario->nombre,
                    'codigo_unico' => $inventario->codigo_unico,
                    'marca' => $inventario->marca,
                    'modelo' => $inventario->modelo,
                    'numero_serie' => $inventario->numero_serie,
                    'propietario' => $inventario->propietario,
                    'categoria' => $inventario->categoria->nombre,
                    'imagen_principal' => $inventario->imagen_principal ? \Storage::url($inventario->imagen_principal) : null,
                    'ubicaciones' => $inventario->ubicaciones->map(function($ubicacion) {
                        return [
                            'ubicacion_nombre' => $ubicacion->ubicacion ? $ubicacion->ubicacion->nombre : 'Sin Ubicación',
                            'cantidad' => $ubicacion->cantidad,
                            'estado' => $ubicacion->estado
                        ];
                    }),
                    // Incluir permisos para este inventario específico
                    'permisos' => [
                        'puede_editar' => $user->can('update', $inventario),
                        'puede_eliminar' => $user->can('delete', $inventario),
                        'puede_ver' => $user->can('view', $inventario)
                    ]
                ];
            }),
            'total' => $inventarios->count(),
            // Incluir permisos generales del usuario
            'permisos_usuario' => [
                'puede_crear' => $user->can('create', Inventario::class),
                'es_administrador' => $user->role->name === 'administrador'
            ]
        ]);
    }

    /**
     * Obtener opciones en cascada para filtros
     */
    public function getFiltrosCascada(Request $request)
    {
        $response = [];

        // Si se selecciona categoría, obtener elementos de esa categoría
        if ($request->filled('categoria_id')) {
            $elementos = Inventario::where('categoria_id', $request->categoria_id)
                ->select('nombre')
                ->distinct()
                ->orderBy('nombre')
                ->pluck('nombre')
                ->toArray();
            $response['elementos'] = $elementos;

            // Si también se selecciona elemento, obtener marcas de ese elemento en esa categoría
            if ($request->filled('elemento_nombre')) {
                $marcas = Inventario::where('categoria_id', $request->categoria_id)
                    ->where('nombre', $request->elemento_nombre)
                    ->whereNotNull('marca')
                    ->select('marca')
                    ->distinct()
                    ->orderBy('marca')
                    ->pluck('marca')
                    ->toArray();
                $response['marcas'] = $marcas;

                // Obtener ubicaciones de este elemento específico
                $ubicaciones = DB::table('inventario_ubicaciones')
                    ->join('inventarios', 'inventario_ubicaciones.inventario_id', '=', 'inventarios.id')
                    ->join('ubicaciones', 'inventario_ubicaciones.ubicacion_id', '=', 'ubicaciones.id')
                    ->where('inventarios.categoria_id', $request->categoria_id)
                    ->where('inventarios.nombre', $request->elemento_nombre)
                    ->select('ubicaciones.id', 'ubicaciones.nombre')
                    ->distinct()
                    ->orderBy('ubicaciones.nombre')
                    ->get();
                $response['ubicaciones'] = $ubicaciones;
            }
        }

        // Obtener todos los estados disponibles
        $estados = ['disponible', 'en uso', 'en mantenimiento', 'dado de baja', 'robado'];
        $response['estados'] = $estados;

        // Si no hay categoría seleccionada, obtener todas las ubicaciones
        if (!$request->filled('categoria_id')) {
            $ubicaciones = DB::table('ubicaciones')
                ->join('inventario_ubicaciones', 'ubicaciones.id', '=', 'inventario_ubicaciones.ubicacion_id')
                ->select('ubicaciones.id', 'ubicaciones.nombre')
                ->distinct()
                ->orderBy('ubicaciones.nombre')
                ->get();
            $response['ubicaciones'] = $ubicaciones;
        }

        return response()->json($response);
    }

    public function getElementosPorCategoria($categoriaId)
    {
        $elementos = Inventario::where('categoria_id', $categoriaId)
            ->select('nombre')
            ->distinct()
            ->orderBy('nombre')
            ->get()
            ->map(function ($elemento) {
                return [
                    'nombre' => $elemento->nombre
                ];
            });

        return response()->json($elementos);
    }
    
    public function show(Inventario $inventario)
    {
        $inventario->load(['categoria', 'proveedor', 'ubicacion', 'mantenimientos', 'documentos', 'media']);
        
        // Cargar las ubicaciones con sus cantidades
        $ubicaciones = $inventario->ubicaciones()->with('ubicacion')->get();
        
        // Calcular la cantidad total
        $cantidadTotal = $ubicaciones->sum('cantidad');
        
        // Obtener el último responsable del equipo desde los movimientos
        $ultimoResponsable = $inventario->movimientos()
            ->with(['usuarioDestino'])
            ->whereNotNull('usuario_destino_id')
            ->orderBy('fecha_movimiento', 'desc')
            ->first();
        
        // Cargar todos los movimientos con las relaciones necesarias (sin paginación para la vista)
        $movimientos = $inventario->movimientos()
            ->with(['realizadoPor', 'usuarioOrigen', 'usuarioDestino'])
            ->orderBy('fecha_movimiento', 'desc')
            ->get();
        
        // Obtener las imágenes usando Spatie Media Library
        $imagenes = $inventario->getMedia('imagenes');
        
        // Obtener los documentos usando Spatie Media Library
        $documentos = $inventario->getMedia('documentos');
        
        return view('inventarios.show', compact('inventario', 'movimientos', 'ubicaciones', 'cantidadTotal', 'imagenes', 'documentos', 'ultimoResponsable'));
    }

    public function testContainers(Inventario $inventario)
    {
        $inventario->load(['categoria', 'proveedor', 'ubicacion', 'mantenimientos', 'documentos', 'media']);
        
        // Cargar las ubicaciones con sus cantidades
        $ubicaciones = $inventario->ubicaciones()->with('ubicacion')->get();
        
        // Calcular la cantidad total
        $cantidadTotal = $ubicaciones->sum('cantidad');
        
        // Obtener el último responsable del equipo desde los movimientos
        $ultimoResponsable = $inventario->movimientos()
            ->with(['usuarioDestino'])
            ->whereNotNull('usuario_destino_id')
            ->orderBy('fecha_movimiento', 'desc')
            ->first();
        
        // Cargar los movimientos paginados con las relaciones necesarias
        $movimientos = $inventario->movimientos()
            ->with(['realizadoPor'])
            ->orderBy('fecha_movimiento', 'desc')
            ->paginate(10);
        
        // Obtener las imágenes usando Spatie Media Library
        $imagenes = $inventario->getMedia('imagenes');
        
        // Obtener los documentos usando Spatie Media Library
        $documentos = $inventario->getMedia('documentos');
        
        return view('inventarios.test-containers', compact('inventario', 'movimientos', 'ubicaciones', 'cantidadTotal', 'imagenes', 'documentos', 'ultimoResponsable'));
    }

    public function create()
    {
        $this->authorize('create', Inventario::class);
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        $ubicaciones = Ubicacion::all();
        return view('inventarios.create', compact('categorias', 'proveedores', 'ubicaciones'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Inventario::class);
        
        // Obtener los datos del request
        $data = $request->all();
        
        // Convertir las fechas del formato d/m/Y a Y-m-d
        $fechas = ['fecha_compra', 'fecha_baja', 'fecha_inspeccion'];
        foreach ($fechas as $fecha) {
            if (!empty($data[$fecha])) {
                try {
                    $data[$fecha] = Carbon::createFromFormat('d/m/Y', $data[$fecha])->format('Y-m-d');
                } catch (\Exception $e) {
                    $data[$fecha] = null;
                }
            } else {
                $data[$fecha] = null;
            }
        }

        $validatedData = Validator::make($data, [
            'categoria_id' => 'required|exists:categorias,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'nombre' => 'required|string|max:255',
            'cantidad' => 'required|integer|min:1',
            'propietario' => 'required|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:255',
            'fecha_compra' => 'nullable|date',
            'numero_factura' => 'nullable|string|max:255',
            'valor_unitario' => 'required|numeric|min:0',
            'fecha_baja' => 'nullable|date',
            'fecha_inspeccion' => 'nullable|date',
            'observaciones' => 'nullable|string',
            'imagen_principal' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'imagen_secundaria' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'estado' => 'required|in:disponible,en uso,en mantenimiento,dado de baja,robado',
        ])->validate();

        // 🔥 SOLUCIÓN: Generar código único una sola vez para usar en imágenes y elemento
        $categoria = Categoria::findOrFail($request->categoria_id);
        $codigoUnico = Inventario::generarCodigoUnico($categoria, $validatedData['nombre']);
        $validatedData['codigo_unico'] = $codigoUnico;

        if ($request->hasFile('imagen_principal')) {
            $file = $request->file('imagen_principal');
            $extension = $file->getClientOriginalExtension();
            $fileName = $codigoUnico . '_principal.' . $extension;
            
            // Verificar que el archivo no existe
            $counter = 1;
            while (Storage::disk('public')->exists('inventario_imagenes/' . $fileName)) {
                $fileName = $codigoUnico . '_principal_' . $counter . '.' . $extension;
                $counter++;
            }
            
            $validatedData['imagen_principal'] = $file->storeAs('inventario_imagenes', $fileName, 'public');
        }

        if ($request->hasFile('imagen_secundaria')) {
            $file = $request->file('imagen_secundaria');
            $extension = $file->getClientOriginalExtension();
            $fileName = $codigoUnico . '_secundaria.' . $extension;
            
            // Verificar que el archivo no existe
            $counter = 1;
            while (Storage::disk('public')->exists('inventario_imagenes/' . $fileName)) {
                $fileName = $codigoUnico . '_secundaria_' . $counter . '.' . $extension;
                $counter++;
            }
            
            $validatedData['imagen_secundaria'] = $file->storeAs('inventario_imagenes', $fileName, 'public');
        }

        $cantidadInicial = $validatedData['cantidad'];
        $estadoInicial = $validatedData['estado'];
        unset($validatedData['cantidad'], $validatedData['estado']);

        $inventarioData = array_filter($validatedData, function ($value) {
            return $value !== null && $value !== '';
        });

        $inventario = null;

        DB::transaction(function () use ($inventarioData, $cantidadInicial, $estadoInicial, &$inventario) {
            $inventario = Inventario::create($inventarioData);

            $inventario->ubicaciones()->create([
                'ubicacion_id' => $inventarioData['ubicacion_id'],
                'cantidad' => $cantidadInicial,
                'estado' => $estadoInicial
            ]);
        });

        if ($inventario) {
            return redirect()->route('inventarios.show', $inventario)->with('success', 'Elemento de inventario creado con éxito.');
        } else {
            return back()->with('error', 'Hubo un problema al crear el elemento de inventario.');
        }
    }

    public function edit(Inventario $inventario)
    {
        $this->authorize('update', $inventario);
        $inventario->load('ubicaciones');
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        $ubicaciones = Ubicacion::all();
        return view('inventarios.edit', compact('inventario', 'categorias', 'proveedores', 'ubicaciones'));
    }

    public function update(Request $request, Inventario $inventario)
{
    $this->authorize('update', $inventario);
    
    // Obtener los datos del request
    $data = $request->all();
    
    // Guardar la categoría actual antes de la actualización
    $categoriaAnterior = $inventario->categoria_id;
    
    // Convertir las fechas del formato d/m/Y a Y-m-d
    $fechas = ['fecha_compra', 'fecha_baja', 'fecha_inspeccion'];
    foreach ($fechas as $fecha) {
        if (!empty($data[$fecha])) {
            try {
                $data[$fecha] = Carbon::createFromFormat('d/m/Y', $data[$fecha])->format('Y-m-d');
            } catch (\Exception $e) {
                $data[$fecha] = null;
            }
        } else {
            $data[$fecha] = null;
        }
    }

    $validator = Validator::make($data, [
        'categoria_id' => 'nullable|exists:categorias,id',
        'proveedor_id' => 'nullable|exists:proveedores,id',
        'nombre' => 'nullable|string|max:255',
        'propietario' => 'nullable|string|max:255',
        'modelo' => 'nullable|string|max:255',
        'numero_serie' => 'nullable|string|max:255',
        'marca' => 'nullable|string|max:255',
        'fecha_compra' => 'nullable|date',
        'numero_factura' => 'nullable|string|max:255',
        'valor_unitario' => 'nullable|numeric|min:0',
        'fecha_baja' => 'nullable|date',
        'fecha_inspeccion' => 'nullable|date',
        'observaciones' => 'nullable|string',
        'imagen_principal' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'imagen_secundaria' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'cantidades' => 'array',
        'cantidades.*' => 'integer|min:0',
        'estados' => 'array',
        'estados.*' => 'nullable|in:disponible,en uso,en mantenimiento,dado de baja,robado',
        'nueva_ubicacion_id' => 'nullable|exists:ubicaciones,id',
        'nueva_ubicacion_cantidad' => 'nullable|integer|min:0',
        'nueva_ubicacion_estado' => 'nullable|in:disponible,en uso,en mantenimiento,dado de baja,robado',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $validatedData = $validator->validated();

    try {
        DB::transaction(function () use ($inventario, $validatedData, $request, $categoriaAnterior) {
                    // 🔥 SOLUCIÓN: Manejar imágenes con nombres únicos
            if ($request->hasFile('imagen_principal')) {
                if ($inventario->imagen_principal) {
                    Storage::disk('public')->delete($inventario->imagen_principal);
                }
                            
                        $file = $request->file('imagen_principal');
                        $extension = $file->getClientOriginalExtension();
                        $fileName = $inventario->codigo_unico . '_principal.' . $extension;
                        
                        // Verificar que el archivo no existe
                        $counter = 1;
                        while (Storage::disk('public')->exists('inventario_imagenes/' . $fileName)) {
                            $fileName = $inventario->codigo_unico . '_principal_' . $counter . '.' . $extension;
                            $counter++;
                        }
                        
                        $validatedData['imagen_principal'] = $file->storeAs('inventario_imagenes', $fileName, 'public');
            }

            if ($request->hasFile('imagen_secundaria')) {
                if ($inventario->imagen_secundaria) {
                    Storage::disk('public')->delete($inventario->imagen_secundaria);
                }
                            
                        $file = $request->file('imagen_secundaria');
                        $extension = $file->getClientOriginalExtension();
                        $fileName = $inventario->codigo_unico . '_secundaria.' . $extension;
                        
                        // Verificar que el archivo no existe
                        $counter = 1;
                        while (Storage::disk('public')->exists('inventario_imagenes/' . $fileName)) {
                            $fileName = $inventario->codigo_unico . '_secundaria_' . $counter . '.' . $extension;
                            $counter++;
                        }
                        
                        $validatedData['imagen_secundaria'] = $file->storeAs('inventario_imagenes', $fileName, 'public');
            }

            // Actualizar el inventario
            $inventario->update($validatedData);

            // Verificar si la categoría ha cambiado
            if ($categoriaAnterior != $inventario->categoria_id) {
                $codigoAnterior = $inventario->codigo_unico;
                $nuevoCodigoUnico = $inventario->actualizarCodigoUnico();
                
                Log::info('Cambio de código único por cambio de categoría', [
                    'inventario_id' => $inventario->id,
                    'codigo_anterior' => $codigoAnterior,
                    'codigo_nuevo' => $nuevoCodigoUnico,
                    'categoria_anterior' => $categoriaAnterior,
                    'categoria_nueva' => $inventario->categoria_id,
                    'usuario' => auth()->user()->name,
                    'fecha' => now()
                ]);
            }

            // Actualizar cantidades y estados existentes
            $cantidadTotal = 0;
            if (isset($request->cantidades)) {
                foreach ($request->cantidades as $relacionId => $cantidad) {
                    $cantidad = max(0, intval($cantidad));
                    $estado = $request->estados[$relacionId] ?? 'disponible';
                    $ubicacionId = $request->cantidades_ubicacion[$relacionId] ?? null;
                    
                    // Verificar que tenemos el ID de ubicación
                    if (!$ubicacionId) {
                        continue;
                    }
                    
                    if ($cantidad > 0) {
                        // Actualizar registro existente por su ID específico
                        $ubicacionExistente = $inventario->ubicaciones()->find($relacionId);
                        if ($ubicacionExistente) {
                            $ubicacionExistente->update([
                                'ubicacion_id' => $ubicacionId,
                                'cantidad' => $cantidad,
                                'estado' => $estado
                            ]);
                        }
                        
                        Log::info('Actualización de ubicación', [
                            'inventario_id' => $inventario->id,
                            'ubicacion_id' => $ubicacionId,
                            'cantidad' => $cantidad,
                            'estado' => $estado,
                            'relacion_id' => $relacionId
                        ]);
                        
                        $cantidadTotal += $cantidad;
                    } else {
                        // Eliminar registro específico si cantidad es 0
                        $inventario->ubicaciones()->where('id', $relacionId)->delete();
                        
                        Log::info('Eliminación de ubicación específica por cantidad 0', [
                            'inventario_id' => $inventario->id,
                            'relacion_id' => $relacionId,
                            'ubicacion_id' => $ubicacionId
                        ]);
                    }
                }
            }

            // Procesar ubicaciones dinámicas agregadas desde JavaScript
            if ($request->has('ubicaciones') && is_array($request->ubicaciones)) {
                foreach ($request->ubicaciones as $ubicacionData) {
                    if (isset($ubicacionData['ubicacion_id']) && isset($ubicacionData['cantidad']) && $ubicacionData['cantidad'] > 0) {
                        // Verificar si ya existe esta combinación de ubicación y estado
                        $ubicacionExistente = $inventario->ubicaciones()
                            ->where('ubicacion_id', $ubicacionData['ubicacion_id'])
                            ->where('estado', $ubicacionData['estado'] ?? 'disponible')
                            ->first();
                        
                        if ($ubicacionExistente) {
                            // Si existe la misma ubicación con el mismo estado, reemplazar la cantidad
                            $ubicacionExistente->update([
                                'cantidad' => intval($ubicacionData['cantidad'])
                            ]);
                            
                            Log::info('Ubicación existente actualizada', [
                                'inventario_id' => $inventario->id,
                                'ubicacion_id' => $ubicacionData['ubicacion_id'],
                                'cantidad_nueva' => intval($ubicacionData['cantidad']),
                                'estado' => $ubicacionData['estado'] ?? 'disponible'
                            ]);
                        } else {
                            // Si no existe esta combinación, crear nueva
                            $inventario->ubicaciones()->create([
                                'ubicacion_id' => $ubicacionData['ubicacion_id'],
                                'cantidad' => intval($ubicacionData['cantidad']),
                                'estado' => $ubicacionData['estado'] ?? 'disponible'
                            ]);
                            
                            Log::info('Nueva ubicación creada desde JavaScript', [
                                'inventario_id' => $inventario->id,
                                'ubicacion_id' => $ubicacionData['ubicacion_id'],
                                'cantidad' => intval($ubicacionData['cantidad']),
                                'estado' => $ubicacionData['estado'] ?? 'disponible'
                            ]);
                        }
                        
                        $cantidadTotal += intval($ubicacionData['cantidad']);
                    }
                }
            }

            // Agregar nueva ubicación si se proporciona (mantener compatibilidad)
            if ($request->nueva_ubicacion_id && $request->nueva_ubicacion_cantidad > 0) {
                $nuevaUbicacion = $inventario->ubicaciones()->create([
                    'ubicacion_id' => $request->nueva_ubicacion_id,
                    'cantidad' => $request->nueva_ubicacion_cantidad,
                    'estado' => $request->nueva_ubicacion_estado ?? 'disponible'
                ]);
                
                Log::info('Nueva ubicación agregada', [
                    'inventario_id' => $inventario->id,
                    'ubicacion_id' => $request->nueva_ubicacion_id,
                    'cantidad' => $request->nueva_ubicacion_cantidad,
                    'estado' => $request->nueva_ubicacion_estado ?? 'disponible'
                ]);
                
                $cantidadTotal += $request->nueva_ubicacion_cantidad;
            }

            // Actualizar cantidad total
            $inventario->cantidadTotal = $cantidadTotal;
            $inventario->save();
            
            Log::info('Actualización de inventario completada', [
                'inventario_id' => $inventario->id,
                'cantidad_total' => $cantidadTotal
            ]);
        });

        return redirect()
            ->route('inventarios.show', $inventario)
            ->with('success', 'Elemento de inventario actualizado con éxito.');
            // No agregar fragmento para evitar scroll automático

    } catch (\Exception $e) {
        Log::error('Error al actualizar inventario', [
            'inventario_id' => $inventario->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()
            ->withErrors(['error' => 'Error al actualizar el inventario: ' . $e->getMessage()])
            ->withInput();
    }
}

    public function destroy(Inventario $inventario)
    {
        $this->authorize('delete', $inventario);

        try {
            DB::beginTransaction();

            // Eliminar imágenes asociadas
            if ($inventario->imagen_principal) {
                Storage::disk('public')->delete($inventario->imagen_principal);
            }
            if ($inventario->imagen_secundaria) {
                Storage::disk('public')->delete($inventario->imagen_secundaria);
            }

            // Eliminar documentos asociados primero
            $documentos = $inventario->documentos;
            foreach ($documentos as $documento) {
                // Eliminar archivo físico si existe
                if ($documento->ruta && Storage::disk('public')->exists($documento->ruta)) {
                    Storage::disk('public')->delete($documento->ruta);
                }
                // Eliminar registro de la base de datos
                $documento->delete();
            }

            // Eliminar archivos de media library asociados
            $inventario->clearMediaCollection('documentos');
            $inventario->clearMediaCollection('imagenes');

            // Eliminar elementos asociados
            $inventario->elementos()->delete();

            // Registrar en el log antes de eliminar
            Log::info('Elemento de inventario eliminado', [
                'usuario' => auth()->user()->name,
                'id_usuario' => auth()->id(),
                'id_inventario' => $inventario->id,
                'codigo_unico' => $inventario->codigo_unico,
                'nombre' => $inventario->nombre,
                'documentos_eliminados' => $documentos->count(),
                'fecha_eliminacion' => now()->toDateTimeString()
            ]);

            // Eliminar el inventario
            $inventario->delete();

            DB::commit();

            return redirect()->route('inventarios.index')->with('success', 'Elemento de inventario eliminado con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar inventario', [
                'id' => $inventario->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('inventarios.index')->with('error', 'No se pudo eliminar el elemento de inventario. Por favor, inténtelo de nuevo.');
        }
    }
}


   

