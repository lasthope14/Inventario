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
        // Si hay parámetros de filtro, redirigir al método searchInventarios
        if ($request->hasAny(['search', 'categoria', 'marca', 'estado', 'ubicacion'])) {
            return $this->searchInventarios($request);
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

        // Aplicar filtro de ubicación si existe
        if ($request->filled('ubicacion')) {
            $categoriasQuery->where('inventario_ubicaciones.ubicacion_id', $request->ubicacion);
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
            $elementos = Inventario::with(['ubicaciones', 'categoria', 'proveedor'])
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
    public function showCategoria(Request $request, $categoriaId = null)
    {
        // Si no se proporciona categoría, mostrar todos los inventarios
        if ($categoriaId === null) {
            $categoria = (object) ['id' => null, 'nombre' => 'Todos los Inventarios'];
            $query = Inventario::with(['ubicaciones', 'categoria', 'proveedor']);
        } else {
            // Obtener la categoría específica
            $categoria = Categoria::findOrFail($categoriaId);
            $query = Inventario::with(['ubicaciones', 'categoria', 'proveedor'])
                ->where('categoria_id', $categoriaId);
        }
        
        // Log de filtros aplicados
        \Log::info('Filtros aplicados en showCategoria:', [
            'categoria_id' => $categoriaId,
            'search' => $request->get('search'),
            'elemento' => $request->get('elemento'),
            'marca' => $request->get('marca'),
            'proveedor' => $request->get('proveedor'),
            'estado' => $request->get('estado'),
            'ubicacion' => $request->get('ubicacion'),
            'tipo_propiedad' => $request->get('tipo_propiedad')
        ]);
        
        \Log::info('Request completo:', $request->all());
        
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
        
        if ($request->filled('proveedor')) {
            $query->where('proveedor_id', $request->proveedor);
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
            $query->where('nombre', 'LIKE', '%' . $request->elemento . '%');
        }
        
        if ($request->filled('tipo_propiedad')) {
            $query->where('tipo_propiedad', $request->tipo_propiedad);
        }
        
        // Ordenación simple por nombre
        $query->orderBy('nombre', 'asc');
        
        // Paginación fija
        $inventarios = $query->paginate(24)->withQueryString();
        
        // Obtener datos para filtros
        if ($categoriaId === null) {
            // Para todos los inventarios
            $marcas = Inventario::whereNotNull('marca')
                ->where('marca', '!=', '')
                ->distinct()
                ->pluck('marca')
                ->sort()
                ->values();
            
            $proveedores = Proveedor::whereHas('inventarios')->orderBy('nombre')->get();
            $ubicaciones = Ubicacion::whereHas('inventarios')->orderBy('nombre')->get();
            
            $elementos = Inventario::whereNotNull('nombre')
                ->where('nombre', '!=', '')
                ->distinct()
                ->orderBy('nombre')
                ->pluck('nombre');
            
            // Obtener estados disponibles de todos los inventarios
            $estadosDisponibles = DB::table('inventario_ubicaciones')
                ->distinct()
                ->pluck('estado')
                ->filter()
                ->sort()
                ->values();
            
            // Estadísticas globales
            $stats = DB::table('inventario_ubicaciones')
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
        } else {
            // Para una categoría específica
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
                ->orderBy('nombre')
                ->pluck('nombre');
            
            // Obtener estados disponibles específicos para esta categoría
            $estadosDisponibles = DB::table('inventario_ubicaciones')
                ->join('inventarios', 'inventario_ubicaciones.inventario_id', '=', 'inventarios.id')
                ->where('inventarios.categoria_id', $categoriaId)
                ->distinct()
                ->pluck('inventario_ubicaciones.estado')
                ->filter()
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
        }
        
        return view('inventarios.categoria', compact(
            'categoria', 'inventarios', 'marcas', 'proveedores', 'ubicaciones', 'elementos', 'stats', 'estadosDisponibles'
        ));
    }

    public function searchInventarios(Request $request)
    {
        $searchTerm = mb_strtolower(trim($request->search));
        
        $query = Inventario::with(['ubicaciones', 'categoria', 'proveedor']);
        
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

        // Determinar qué vista retornar basado en el contexto
        // Si viene de la vista principal (index) y es una búsqueda simple, mantener en index
        if ($request->filled('search') && !$request->filled(['categoria', 'estado', 'marca'])) {
            // Búsqueda desde index - retornar resultados en formato JSON para AJAX
            if ($request->ajax()) {
                return response()->json([
                    'inventarios' => $inventarios,
                    'total' => $inventarios->total()
                ]);
            }
            // Si no es AJAX, retornar vista index con resultados
            $todasCategorias = Categoria::all();
            return view('inventarios.index', compact('inventarios', 'searchTerm', 'filtros', 'categorias', 'statsGlobales', 'todasCategorias'));
        }
        
        // Para filtros avanzados o navegación por categorías, usar vista categorias
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
        $inventarios = Inventario::with(['ubicaciones'])
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
            ->limit(20)
            ->get(['id', 'nombre', 'codigo_unico', 'numero_serie', 'marca', 'modelo']);
            
        foreach ($inventarios as $inventario) {
            // Crear subtítulo más informativo
            $subtitle_parts = [];
            
            // Manejar múltiples ubicaciones y estados
            $ubicaciones = $inventario->ubicaciones;
            if ($ubicaciones->count() > 0) {
                $primeraUbicacion = $ubicaciones->first();
                if ($primeraUbicacion && $primeraUbicacion->nombre) {
                    // Agregar ubicación
                    if ($ubicaciones->count() > 1) {
                        $adicionales = $ubicaciones->count() - 1;
                        $subtitle_parts[] = 'Ubicación: ' . $primeraUbicacion->nombre . ' y ' . $adicionales . ' más';
                    } else {
                        $subtitle_parts[] = 'Ubicación: ' . $primeraUbicacion->nombre;
                    }
                    
                    // Agregar estado
                    if ($primeraUbicacion->pivot && $primeraUbicacion->pivot->estado) {
                        $subtitle_parts[] = 'Estado: ' . ucfirst($primeraUbicacion->pivot->estado);
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
                'url' => route('inventarios.categoria', ['categoria' => null, 'ubicacion' => $ubicacion->id])
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
                'url' => route('inventarios.categoria', ['categoria' => null, 'estado' => $estado])
            ];
        }
        
        // Limitar el total de sugerencias
        $suggestions = array_slice($suggestions, 0, 25);
        
        return response()->json($suggestions);
    }

    /**
     * API endpoint para autocompletado de búsqueda específico de categoría
     */
    public function autocompleteCategoria(Request $request, $categoriaId)
    {
        $query = trim($request->get('q', ''));
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $suggestions = [];
        $queryLower = strtolower($query);
        
        // Buscar solo en inventarios de esta categoría específica
        $inventarios = Inventario::with(['ubicaciones', 'categoria'])
            ->where('categoria_id', $categoriaId)
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
            ->limit(20)
            ->get(['id', 'nombre', 'codigo_unico', 'numero_serie', 'marca', 'modelo']);
            
        foreach ($inventarios as $inventario) {
            // Crear subtítulo más informativo
            $subtitle_parts = [];
            
            // Manejar múltiples ubicaciones y estados
            $ubicaciones = $inventario->ubicaciones;
            if ($ubicaciones->count() > 0) {
                $primeraUbicacion = $ubicaciones->first();
                if ($primeraUbicacion && $primeraUbicacion->nombre) {
                    // Agregar ubicación
                    if ($ubicaciones->count() > 1) {
                        $adicionales = $ubicaciones->count() - 1;
                        $subtitle_parts[] = 'Ubicación: ' . $primeraUbicacion->nombre . ' y ' . $adicionales . ' más';
                    } else {
                        $subtitle_parts[] = 'Ubicación: ' . $primeraUbicacion->nombre;
                    }
                    
                    // Agregar estado
                    if ($primeraUbicacion->pivot && $primeraUbicacion->pivot->estado) {
                        $subtitle_parts[] = 'Estado: ' . ucfirst($primeraUbicacion->pivot->estado);
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
        
        // Buscar por marcas específicas de esta categoría
        $marcas = Inventario::where('categoria_id', $categoriaId)
            ->where('marca', 'LIKE', "%{$query}%")
            ->whereNotNull('marca')
            ->where('marca', '!=', '')
            ->distinct()
            ->pluck('marca')
            ->take(5);
            
        foreach ($marcas as $marca) {
            $suggestions[] = [
                'type' => 'marca',
                'id' => $marca,
                'text' => $marca,
                'subtitle' => 'Marca en esta categoría',
                'url' => route('inventarios.categoria', ['categoria' => $categoriaId, 'marca' => $marca])
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
                'subtitle' => 'Estado en esta categoría',
                'url' => route('inventarios.categoria', ['categoria' => $categoriaId, 'estado' => $estado])
            ];
        }
        
        // Limitar el total de sugerencias
        $suggestions = array_slice($suggestions, 0, 25);
        
        return response()->json($suggestions);
    }

    /**
     * Obtiene los filtros disponibles de forma dinámica
     */
    public function getFiltrosDisponibles(Request $request)
    {
        $baseQuery = Inventario::with(['categoria', 'proveedor', 'ubicaciones']);
        
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
        $ubicaciones = $inventario->ubicaciones()->get();
        
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
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'enlace_documentacion' => 'nullable|url|max:255',
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

        // Manejar archivo QR
        if ($request->hasFile('qr_code')) {
            $file = $request->file('qr_code');
            $extension = $file->getClientOriginalExtension();
            $fileName = $codigoUnico . '_qr.' . $extension;
            
            // Verificar que el archivo no existe
            $counter = 1;
            while (Storage::disk('public')->exists('documentos/' . $fileName)) {
                $fileName = $codigoUnico . '_qr_' . $counter . '.' . $extension;
                $counter++;
            }
            
            $validatedData['qr_code'] = $file->storeAs('documentos', $fileName, 'public');
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
        'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'enlace_documentacion' => 'nullable|url|max:255',
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

            // Manejar archivo QR
            if ($request->hasFile('qr_code')) {
                if ($inventario->qr_code) {
                    Storage::disk('public')->delete($inventario->qr_code);
                }
                            
                        $file = $request->file('qr_code');
                        $extension = $file->getClientOriginalExtension();
                        $fileName = $inventario->codigo_unico . '_qr.' . $extension;
                        
                        // Verificar que el archivo no existe
                        $counter = 1;
                        while (Storage::disk('public')->exists('documentos/' . $fileName)) {
                            $fileName = $inventario->codigo_unico . '_qr_' . $counter . '.' . $extension;
                            $counter++;
                        }
                        
                        $validatedData['qr_code'] = $file->storeAs('documentos', $fileName, 'public');
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
            if (isset($request->ubicaciones_existentes)) {
                foreach ($request->ubicaciones_existentes as $relacionId => $ubicacionData) {
                    $cantidad = max(0, intval($ubicacionData['cantidad'] ?? 0));
                    $estado = $ubicacionData['estado'] ?? 'disponible';
                    $ubicacionId = $ubicacionData['ubicacion_id'] ?? null;
                    
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


    
    /**
     * API endpoint para obtener marcas por elemento
     */
    public function getMarcasPorElemento(Request $request)
    {
        try {
            $elemento = $request->get('elemento');
            $categoriaId = $request->get('categoria_id');
            
            if (!$elemento || !$categoriaId) {
                return response()->json([]);
            }
            
            // Obtener marcas y modelos únicos (incluyendo elementos que tengan al menos marca O modelo)
            $inventarios = Inventario::where('categoria_id', $categoriaId)
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
                ->get();
            

            
            $marcasModelos = collect();
            
            foreach ($inventarios as $inventario) {
                // Si tiene marca, agregarla
                if (!empty($inventario->marca) && $inventario->marca !== 'N/A') {
                    $marcasModelos->push($inventario->marca);
                }
                // Si tiene modelo, agregarlo
                if (!empty($inventario->modelo) && $inventario->modelo !== 'N/A') {
                    $marcasModelos->push($inventario->modelo);
                }
            }
            
            // Obtener valores únicos y ordenados
            $marcasUnicas = $marcasModelos->unique()->filter()->sort()->values();
            

                
            return response()->json($marcasUnicas);
            
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * API endpoint para obtener proveedores por elemento y marca
     */
    public function getProveedoresPorElementoMarca(Request $request)
    {
        try {
            $elemento = $request->get('elemento');
            $marca = $request->get('marca');
            $tipoPropiedad = $request->get('tipo_propiedad');
            $categoriaId = $request->get('categoria_id');
            
            if (!$categoriaId) {
                return response()->json([]);
            }
            
            $query = Inventario::where('categoria_id', $categoriaId)
                ->whereNotNull('proveedor_id');
            
            if ($elemento) {
                $query->where('nombre', $elemento);
            }
            
            if ($marca) {
                $query->where(function($q) use ($marca) {
                    $q->where('marca', $marca)
                      ->orWhere('modelo', $marca);
                });
            }
            
            $proveedores = $query->with('proveedor')
                ->distinct()
                ->get()
                ->pluck('proveedor')
                ->filter()
                ->unique('id')
                ->sortBy('nombre')
                ->values()
                ->map(function($proveedor) {
                    return [
                        'id' => $proveedor->id,
                        'nombre' => $proveedor->nombre
                    ];
                });
                
            return response()->json($proveedores);
            
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * API endpoint para obtener ubicaciones por elemento
     */
    public function getUbicacionesPorElemento(Request $request)
    {
        try {
            $elemento = $request->get('elemento');
            $categoriaId = $request->get('categoria_id');
            
            if (!$categoriaId) {
                return response()->json([]);
            }
            
            $query = Inventario::where('categoria_id', $categoriaId);
            
            if ($elemento) {
                $query->where('nombre', $elemento);
            }
            
            $ubicaciones = $query->with('ubicaciones')
                ->get()
                ->pluck('ubicaciones')
                ->flatten()
                ->unique('id')
                ->sortBy('nombre')
                ->values()
                ->map(function($ubicacion) {
                    return [
                        'id' => $ubicacion->id,
                        'nombre' => $ubicacion->nombre
                    ];
                });
                
            return response()->json($ubicaciones);
            
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * API endpoint para obtener estados por elemento
     */
    public function getEstadosPorElemento(Request $request)
    {
        try {
            $elemento = $request->get('elemento');
            $categoriaId = $request->get('categoria_id');
            
            if (!$categoriaId) {
                return response()->json([]);
            }
            
            $query = Inventario::where('categoria_id', $categoriaId);
            
            if ($elemento) {
                $query->where('nombre', $elemento);
            }
            
            // Obtener estados únicos de la tabla pivot
            $estados = $query->with('ubicaciones')
                ->get()
                ->pluck('ubicaciones')
                ->flatten()
                ->pluck('pivot.estado')
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->map(function($estado) {
                    $estadoLabels = [
                        'disponible' => 'Disponible',
                        'en uso' => 'En Uso',
                        'en mantenimiento' => 'En Mantenimiento',
                        'dado de baja' => 'Dado de Baja',
                        'robado' => 'Robado'
                    ];
                    
                    return [
                        'value' => $estado,
                        'label' => $estadoLabels[$estado] ?? ucfirst($estado)
                    ];
                });
                
            return response()->json($estados);
            
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * API unificada para obtener todos los filtros de una vez
     * Reduce de 4 peticiones HTTP a 1 sola
     */
    public function getFiltrosUnificados(Request $request)
    {
        try {
            $elemento = $request->get('elemento');
            $marca = $request->get('marca');
            $categoriaId = $request->get('categoria_id');
            $tipoPropiedad = $request->get('tipo_propiedad');
            $proveedor = $request->get('proveedor');
            $ubicacion = $request->get('ubicacion');
            $estado = $request->get('estado');
            
            if (!$categoriaId) {
                return response()->json([
                    'marcas' => [],
                    'proveedores' => [],
                    'ubicaciones' => [],
                    'estados' => [],
                    'tipos_propiedad' => []
                ]);
            }
            
            $resultado = [];
            
            // 1. Obtener marcas si hay elemento seleccionado
            if ($elemento) {
                $queryMarcas = Inventario::where('categoria_id', $categoriaId)
                    ->where('nombre', $elemento)
                    ->whereNotNull('marca')
                    ->where('marca', '!=', '')
                    ->where('marca', '!=', 'N/A');
                
                // Aplicar filtros adicionales
                if ($tipoPropiedad) {
                    $queryMarcas->where('tipo_propiedad', $tipoPropiedad);
                }
                
                if ($proveedor) {
                    $queryMarcas->where('proveedor_id', $proveedor);
                }
                
                if ($ubicacion) {
                    $queryMarcas->whereHas('ubicaciones', function($q) use ($ubicacion) {
                        $q->where('ubicacion_id', $ubicacion);
                    });
                }
                
                if ($estado) {
                    $queryMarcas->whereHas('ubicaciones', function($q) use ($estado) {
                        $q->where('estado', $estado);
                    });
                }
                
                $inventarios = $queryMarcas->select('marca', 'modelo')
                    ->distinct()
                    ->get();
                
                $marcas = collect();
                foreach ($inventarios as $inventario) {
                    if (!empty($inventario->marca) && $inventario->marca !== 'N/A') {
                        $marcas->push($inventario->marca);
                    }
                }
                
                $resultado['marcas'] = $marcas->unique()->sort()->values();
            } else {
                $resultado['marcas'] = [];
            }
            
            // 2. Obtener proveedores
            $queryProveedores = Inventario::where('categoria_id', $categoriaId)
                ->whereNotNull('proveedor_id');
            
            if ($elemento) {
                $queryProveedores->where('nombre', $elemento);
            }
            
            if ($marca) {
                $queryProveedores->where('marca', $marca);
            }
            
            if ($tipoPropiedad) {
                $queryProveedores->where('tipo_propiedad', $tipoPropiedad);
            }
            
            if ($ubicacion) {
                $queryProveedores->whereHas('ubicaciones', function($q) use ($ubicacion) {
                    $q->where('ubicacion_id', $ubicacion);
                });
            }
            
            if ($estado) {
                $queryProveedores->whereHas('ubicaciones', function($q) use ($estado) {
                    $q->where('estado', $estado);
                });
            }
            
            if ($marca) {
                $queryProveedores->where(function($q) use ($marca) {
                    $q->where('marca', $marca)
                      ->orWhere('modelo', $marca);
                });
            }
            
            $resultado['proveedores'] = $queryProveedores->with('proveedor')
                ->distinct()
                ->get()
                ->pluck('proveedor')
                ->filter()
                ->unique('id')
                ->sortBy('nombre')
                ->values()
                ->map(function($proveedor) {
                    return [
                        'id' => $proveedor->id,
                        'nombre' => $proveedor->nombre
                    ];
                });
            
            // 3. Obtener ubicaciones
            // IMPORTANTE: No aplicar el filtro de ubicación actual para evitar que desaparezca de la lista
            $queryUbicaciones = Inventario::where('categoria_id', $categoriaId);
            
            if ($elemento) {
                $queryUbicaciones->where('nombre', $elemento);
            }
            
            if ($marca) {
                $queryUbicaciones->where('marca', $marca);
            }
            
            if ($tipoPropiedad) {
                $queryUbicaciones->where('tipo_propiedad', $tipoPropiedad);
            }
            
            if ($proveedor) {
                $queryUbicaciones->where('proveedor_id', $proveedor);
            }
            
            if ($estado) {
                $queryUbicaciones->whereHas('ubicaciones', function($q) use ($estado) {
                    $q->where('estado', $estado);
                });
            }
            
            // Nota: NO aplicamos el filtro de ubicación aquí para preservar la selección actual
            // if ($ubicacion) { ... } - REMOVIDO INTENCIONALMENTE
            
            // Nota: El filtro de marca ya se aplicó arriba, no duplicar
            
            $ubicacionesDisponibles = $queryUbicaciones->with('ubicaciones')
                ->get()
                ->pluck('ubicaciones')
                ->flatten()
                ->unique('id')
                ->sortBy('nombre')
                ->values()
                ->map(function($ubicacion) {
                    return [
                        'id' => $ubicacion->id,
                        'nombre' => $ubicacion->nombre
                    ];
                });
            
            // Si hay una ubicación seleccionada, asegurar que esté en la lista
            if ($ubicacion) {
                $ubicacionSeleccionada = \App\Models\Ubicacion::find($ubicacion);
                if ($ubicacionSeleccionada) {
                    $ubicacionExiste = $ubicacionesDisponibles->contains('id', $ubicacion);
                    if (!$ubicacionExiste) {
                        // Agregar la ubicación seleccionada al inicio de la lista
                        $ubicacionesDisponibles->prepend([
                            'id' => $ubicacionSeleccionada->id,
                            'nombre' => $ubicacionSeleccionada->nombre
                        ]);
                    }
                }
            }
            
            $resultado['ubicaciones'] = $ubicacionesDisponibles;
            
            // 4. Obtener estados
            $queryEstados = Inventario::where('categoria_id', $categoriaId);
            
            if ($elemento) {
                $queryEstados->where('nombre', $elemento);
            }
            
            if ($marca) {
                $queryEstados->where('marca', $marca);
            }
            
            if ($tipoPropiedad) {
                $queryEstados->where('tipo_propiedad', $tipoPropiedad);
            }
            
            if ($proveedor) {
                $queryEstados->where('proveedor_id', $proveedor);
            }
            
            if ($ubicacion) {
                $queryEstados->whereHas('ubicaciones', function($q) use ($ubicacion) {
                    $q->where('ubicacion_id', $ubicacion);
                });
            }
            
            if ($marca) {
                $queryEstados->where(function($q) use ($marca) {
                    $q->where('marca', $marca)
                      ->orWhere('modelo', $marca);
                });
            }
            
            $estadosUnicos = $queryEstados->with('ubicaciones')
                ->get()
                ->pluck('ubicaciones')
                ->flatten()
                ->pluck('pivot.estado')
                ->filter()
                ->unique()
                ->sort()
                ->values();
            
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
            
            // 5. Obtener tipos de propiedad disponibles
            $queryTiposPropiedad = Inventario::where('categoria_id', $categoriaId);
            
            if ($elemento) {
                $queryTiposPropiedad->where('nombre', $elemento);
            }
            
            if ($marca) {
                $queryTiposPropiedad->where('marca', $marca);
            }
            
            if ($proveedor) {
                $queryTiposPropiedad->where('proveedor_id', $proveedor);
            }
            
            if ($ubicacion) {
                $queryTiposPropiedad->whereHas('ubicaciones', function($q) use ($ubicacion) {
                    $q->where('ubicacion_id', $ubicacion);
                });
            }
            
            if ($estado) {
                $queryTiposPropiedad->whereHas('ubicaciones', function($q) use ($estado) {
                    $q->where('estado', $estado);
                });
            }
            
            $tiposDisponibles = $queryTiposPropiedad->select('tipo_propiedad')
                ->distinct()
                ->whereNotNull('tipo_propiedad')
                ->pluck('tipo_propiedad')
                ->sort()
                ->values();
            
            $resultado['tipos_propiedad'] = $tiposDisponibles->map(function($tipo) {
                return [
                    'value' => $tipo,
                    'label' => $tipo == 'alquiler' ? 'ALQUILER' : 'PROPIO'
                ];
            });
            
            return response()->json($resultado);
            
        } catch (\Exception $e) {
            \Log::error('Error en getFiltrosUnificados: ' . $e->getMessage());
            return response()->json([
                'marcas' => [],
                'proveedores' => [],
                'ubicaciones' => [],
                'estados' => [],
                'tipos_propiedad' => []
            ], 500);
        }
    }
}


   

