<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Inventario;
use App\Models\Empleado;
use App\Models\User;
use App\Models\Role;
use App\Models\Ubicacion;
use App\Notifications\MovimientoCreatedNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class MovimientoMasivoController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('create', Movimiento::class);
        
        $inventarios = Inventario::with(['categoria', 'ubicaciones.ubicacion'])
            ->whereHas('ubicaciones', function($query) {
                $query->where('cantidad', '>', 0);
            })
            ->get();
            
        $ubicaciones = Ubicacion::all();
        $empleados = Empleado::all();
        
        return view('movimientos.masivo', compact('inventarios', 'ubicaciones', 'empleados'));
    }

    public function store(Request $request)
    {
        $requestId = uniqid();
        Log::info("=== Inicio de movimiento masivo {$requestId} ===", [
            'user_id' => Auth::id(),
            'elementos_count' => count(json_decode($request->elementos ?? '[]', true))
        ]);

        $this->authorize('create', Movimiento::class);
        
        // Validar datos básicos
        $request->validate([
            'ubicacion_destino_id' => 'required|exists:ubicaciones,id',
            'elementos' => 'required|string'
        ]);

        // Decodificar elementos JSON
        try {
            $elementos = json_decode($request->elementos, true);
            if (!is_array($elementos) || empty($elementos)) {
                throw new \Exception('No se proporcionaron elementos válidos');
            }
            
            // Log de elementos recibidos para debug
            Log::info("Elementos recibidos para movimiento masivo {$requestId}", [
                'elementos' => $elementos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los elementos: ' . $e->getMessage()
            ]);
        }

        $movimientosCreados = [];
        $errores = [];

        try {
            DB::transaction(function () use ($request, $elementos, $requestId, &$movimientosCreados, &$errores) {
                foreach ($elementos as $index => $elemento) {
                    try {
                        // Validar elemento individual
                        if (!isset($elemento['id']) || !isset($elemento['ubicacion_id']) || !isset($elemento['cantidad_mover'])) {
                            $errores[] = "Elemento " . ($index + 1) . ": Datos incompletos - " . json_encode($elemento);
                            continue;
                        }

                        // Log del elemento que se está procesando
                        Log::info("Procesando elemento {$requestId}", [
                            'elemento_id' => $elemento['id'],
                            'ubicacion_id' => $elemento['ubicacion_id'],
                            'cantidad_mover' => $elemento['cantidad_mover']
                        ]);

                        // Buscar el inventario
                        $inventario = Inventario::find($elemento['id']);
                        if (!$inventario) {
                            $errores[] = "Elemento " . ($elemento['codigo'] ?? $elemento['id']) . ": No encontrado en inventario";
                            continue;
                        }

                        // Log de inventario encontrado
                        Log::info("Inventario encontrado {$requestId}", [
                            'inventario_id' => $inventario->id,
                            'inventario_nombre' => $inventario->nombre,
                            'ubicaciones_count' => $inventario->ubicaciones()->count()
                        ]);

                        // Buscar la ubicación origen en la tabla inventario_ubicaciones
                        $ubicacionOrigen = $inventario->ubicaciones()
                            ->where('ubicacion_id', $elemento['ubicacion_id'])
                            ->first();

                        // Log detallado de la búsqueda de ubicación
                        if (!$ubicacionOrigen) {
                            // Obtener todas las ubicaciones del inventario para debug
                            $todasUbicaciones = $inventario->ubicaciones()->get();
                            Log::error("Ubicación origen no encontrada {$requestId}", [
                                'inventario_id' => $inventario->id,
                                'ubicacion_buscada' => $elemento['ubicacion_id'],
                                'ubicaciones_disponibles' => $todasUbicaciones->pluck('ubicacion_id')->toArray(),
                                'ubicaciones_con_cantidad' => $todasUbicaciones->map(function($u) {
                                    return [
                                        'ubicacion_id' => $u->ubicacion_id,
                                        'cantidad' => $u->cantidad,
                                        'estado' => $u->estado
                                    ];
                                })->toArray()
                            ]);
                            
                            $errores[] = "Elemento " . ($elemento['codigo'] ?? $elemento['id']) . ": No se encontró en la ubicación de origen (ID: {$elemento['ubicacion_id']})";
                            continue;
                        }

                        $cantidadDisponible = $ubicacionOrigen->cantidad;
                        
                        Log::info("Ubicación origen encontrada {$requestId}", [
                            'inventario_id' => $inventario->id,
                            'ubicacion_id' => $elemento['ubicacion_id'],
                            'cantidad_disponible' => $cantidadDisponible,
                            'cantidad_solicitada' => $elemento['cantidad_mover']
                        ]);

                        if ($cantidadDisponible < $elemento['cantidad_mover']) {
                            $errores[] = "Elemento " . ($elemento['codigo'] ?? $elemento['id']) . ": Cantidad insuficiente (Disponible: {$cantidadDisponible}, Solicitado: {$elemento['cantidad_mover']})";
                            continue;
                        }

                        // Crear movimiento marcado como masivo
                        $movimiento = Movimiento::create([
                            'inventario_id' => $elemento['id'],
                            'ubicacion_origen' => $ubicacionOrigen->ubicacion_id ?? $elemento['ubicacion_id'],
                            'ubicacion_destino' => $request->ubicacion_destino_id,
                            'usuario_origen_id' => $request->usuario_origen_id ?? 1, // Empleado origen (desde el formulario o por defecto)
                            'usuario_destino_id' => $request->usuario_destino_id ?? 1, // Empleado destino (desde el formulario o por defecto)
                            'cantidad' => $elemento['cantidad_mover'],
                            'motivo' => 'Movimiento masivo - ' . ($elemento['codigo'] ?? 'Sin código'),
                            'nuevo_estado' => isset($elemento['nuevo_estado']) ? $elemento['nuevo_estado'] : (isset($elemento['estado']) ? $elemento['estado'] : 'disponible'),
                            'fecha_movimiento' => now(),
                            'realizado_por_id' => Auth::id(), // Usuario que realizó el movimiento masivo
                            'tipo_movimiento' => 'masivo' // Marcar como movimiento masivo
                        ]);

                        // Actualizar cantidad en ubicación origen
                        $nuevaCantidadOrigen = $cantidadDisponible - $elemento['cantidad_mover'];
                        
                        if ($nuevaCantidadOrigen > 0) {
                            $ubicacionOrigen->update(['cantidad' => $nuevaCantidadOrigen]);
                        } else {
                            // Eliminar registro si cantidad llega a 0
                            $ubicacionOrigen->delete();
                        }

                        // Agregar o actualizar en ubicación destino
                        $ubicacionDestino = $inventario->ubicaciones()
                            ->where('ubicacion_id', $request->ubicacion_destino_id)
                            ->first();

                        $estadoDestino = isset($elemento['nuevo_estado']) ? $elemento['nuevo_estado'] : (isset($elemento['estado']) ? $elemento['estado'] : 'disponible');

                        if ($ubicacionDestino) {
                            // Actualizar cantidad existente
                            $ubicacionDestino->update([
                                'cantidad' => $ubicacionDestino->cantidad + $elemento['cantidad_mover'],
                                'estado' => $estadoDestino
                            ]);
                        } else {
                            // Crear nueva relación
                            $inventario->ubicaciones()->create([
                                'ubicacion_id' => $request->ubicacion_destino_id,
                                'cantidad' => $elemento['cantidad_mover'],
                                'estado' => $estadoDestino
                            ]);
                        }

                        // Actualizar cantidad total del inventario
                        $inventario->cantidadTotal = $inventario->ubicaciones()->sum('cantidad');
                        $inventario->save();

                        $movimientosCreados[] = $movimiento;

                        Log::info("Movimiento masivo creado exitosamente", [
                            'request_id' => $requestId,
                            'movimiento_id' => $movimiento->id,
                            'inventario' => $inventario->nombre,
                            'cantidad' => $elemento['cantidad_mover'],
                            'origen' => $elemento['ubicacion_id'],
                            'destino' => $request->ubicacion_destino_id
                        ]);

                    } catch (\Exception $e) {
                        Log::error("Error en elemento individual del movimiento masivo", [
                            'request_id' => $requestId,
                            'elemento_index' => $index,
                            'elemento_id' => $elemento['id'] ?? 'N/A',
                            'elemento_data' => $elemento,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        $errores[] = "Error en elemento " . ($elemento['codigo'] ?? ($index + 1)) . ": " . $e->getMessage();
                    }
                }

                // Si hay errores críticos, cancelar toda la transacción
                if (count($errores) > 0 && count($movimientosCreados) == 0) {
                    throw new \Exception('No se pudo procesar ningún movimiento. Errores: ' . implode('; ', $errores));
                }
            });

            // Enviar notificaciones
            if (count($movimientosCreados) > 0) {
                try {
                    $rolesIds = Role::whereIn('name', ['administrador', 'almacenista'])->pluck('id');
                    $usersToNotify = User::whereIn('role_id', $rolesIds)->get();

                    foreach ($movimientosCreados as $movimiento) {
                        Notification::send($usersToNotify, new MovimientoCreatedNotification($movimiento));
                    }
                } catch (\Exception $e) {
                    Log::warning("Error al enviar notificaciones: " . $e->getMessage());
                }
            }

            $mensaje = count($movimientosCreados) . ' movimientos procesados exitosamente';
            if (count($errores) > 0) {
                $mensaje .= '. Con ' . count($errores) . ' errores: ' . implode('; ', array_slice($errores, 0, 3));
                if (count($errores) > 3) {
                    $mensaje .= ' y ' . (count($errores) - 3) . ' más...';
                }
            }

            Log::info("=== Fin de movimiento masivo {$requestId} ===", [
                'movimientos_creados' => count($movimientosCreados),
                'errores' => count($errores)
            ]);

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'movimientos_creados' => count($movimientosCreados),
                'errores' => $errores
            ]);

        } catch (\Exception $e) {
            Log::error("Error crítico en movimiento masivo {$requestId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar movimientos masivos: ' . $e->getMessage()
            ]);
        }
    }

    public function getInventarioData(Request $request)
    {
        try {
            $query = Inventario::with(['categoria', 'ubicaciones.ubicacion'])
                ->whereHas('ubicaciones', function($q) {
                    $q->where('cantidad', '>', 0);
                });
                
            // Filtrar por ubicación si se especifica
            if ($request->has('ubicacion_id') && $request->ubicacion_id) {
                $query->whereHas('ubicaciones', function($q) use ($request) {
                    $q->where('ubicacion_id', $request->ubicacion_id);
                });
            }
            
            $inventarios = $query->get();
            
            $elementos = [];
            
            foreach ($inventarios as $inventario) {
                foreach ($inventario->ubicaciones as $ubicacionInventario) {
                    // Solo incluir ubicaciones con cantidad > 0
                    if ($ubicacionInventario->cantidad > 0) {
                        // Si se filtró por ubicación, solo incluir esa ubicación
                        if ($request->ubicacion_id && $ubicacionInventario->ubicacion_id != $request->ubicacion_id) {
                            continue;
                        }
                        
                        $elementos[] = [
                            'id' => $inventario->id,
                            'codigo' => $inventario->codigo_unico,
                            'nombre' => $inventario->nombre,
                            'descripcion' => $inventario->descripcion ?? '',
                            'categoria' => $inventario->categoria->nombre ?? 'Sin categoría',
                            'ubicacion_id' => $ubicacionInventario->ubicacion_id,
                            'ubicacion_nombre' => $ubicacionInventario->ubicacion->nombre ?? 'Sin ubicación',
                            'cantidad_disponible' => $ubicacionInventario->cantidad,
                            'estado' => $ubicacionInventario->estado ?? 'disponible',
                            'valor_unitario' => $inventario->valor_unitario ?? 0
                        ];
                    }
                }
            }
            
            // Devolver directamente el array de elementos
            return response()->json($elementos);
            
        } catch (\Exception $e) {
            Log::error('Error en getInventarioData: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al cargar los datos del inventario',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revertir un movimiento específico
     */
    public function revertir(Request $request, $movimientoId)
    {
        try {
            DB::beginTransaction();
            
            $movimiento = Movimiento::findOrFail($movimientoId);
            
            // Los movimientos se pueden revertir en cualquier momento
            
            // Verificar que no esté ya revertido
            if ($movimiento->revertido) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este movimiento ya ha sido revertido'
                ]);
            }
            
            $inventario = Inventario::findOrFail($movimiento->inventario_id);
            
            // Buscar la ubicación de destino actual para restar la cantidad
            $ubicacionDestino = $inventario->ubicaciones()
                ->where('ubicacion_id', function($query) use ($movimiento) {
                    // Buscar el ID de ubicación por nombre
                    $query->select('id')
                          ->from('ubicaciones')
                          ->where('nombre', $movimiento->ubicacion_destino)
                          ->limit(1);
                })
                ->first();
            
            if (!$ubicacionDestino || $ubicacionDestino->cantidad < $movimiento->cantidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente cantidad en la ubicación de destino para revertir'
                ]);
            }
            
            // Revertir el inventario en destino
            $nuevaCantidadDestino = $ubicacionDestino->cantidad - $movimiento->cantidad;
            if ($nuevaCantidadDestino <= 0) {
                $ubicacionDestino->delete();
            } else {
                $ubicacionDestino->update(['cantidad' => $nuevaCantidadDestino]);
            }
            
            // Restaurar el inventario en origen
            $ubicacionOrigenId = \App\Models\Ubicacion::where('nombre', $movimiento->ubicacion_origen)->first()->id ?? null;
            
            if ($ubicacionOrigenId) {
                $ubicacionOrigen = $inventario->ubicaciones()
                    ->where('ubicacion_id', $ubicacionOrigenId)
                    ->first();
                
                if ($ubicacionOrigen) {
                    // Actualizar cantidad existente
                    $ubicacionOrigen->update([
                        'cantidad' => $ubicacionOrigen->cantidad + $movimiento->cantidad
                    ]);
                } else {
                    // Crear nuevo registro en origen
                    $inventario->ubicaciones()->create([
                        'ubicacion_id' => $ubicacionOrigenId,
                        'cantidad' => $movimiento->cantidad,
                        'estado' => 'disponible' // Estado por defecto al revertir
                    ]);
                }
            }
            
            // Actualizar cantidad total del inventario
            $inventario->cantidadTotal = $inventario->ubicaciones()->sum('cantidad');
            $inventario->save();
            
            // Marcar el movimiento como revertido
            $movimiento->update([
                'motivo' => ($movimiento->motivo ?? '') . ' [REVERTIDO: ' . now()->format('d/m/Y H:i') . ']',
                'revertido' => true,
                'revertido_at' => now(),
                'revertido_por' => auth()->id()
            ]);
            
            DB::commit();
            
            // Log de auditoría
            Log::info('Movimiento revertido exitosamente', [
                'movimiento_id' => $movimiento->id,
                'inventario_id' => $movimiento->inventario_id,
                'usuario_id' => auth()->id(),
                'cantidad' => $movimiento->cantidad,
                'ubicacion_origen' => $movimiento->ubicacion_origen,
                'ubicacion_destino' => $movimiento->ubicacion_destino
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Movimiento revertido correctamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al revertir movimiento: ' . $e->getMessage(), [
                'movimiento_id' => $movimientoId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al revertir el movimiento: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Obtener movimientos MASIVOS que se pueden revertir (sin restricción de tiempo)
     */
    public function movimientosRevertibles()
    {
        Log::info('=== Inicio de movimientosRevertibles (solo masivos) ===', [
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'N/A'
        ]);
        
        try {
            Log::info('Consultando movimientos masivos no revertidos...');
            
            // Solo movimientos que tienen tipo_movimiento = 'masivo'
            $movimientos = Movimiento::with(['inventario', 'usuarioOrigen', 'usuarioDestino', 'realizadoPor'])
                ->where('revertido', false)
                ->where('tipo_movimiento', 'masivo') // Solo movimientos masivos
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get();
                
            Log::info('Movimientos encontrados', ['count' => $movimientos->count()]);
            
            $movimientosFormateados = $movimientos->map(function($movimiento) {
                    return [
                        'id' => $movimiento->id,
                        'inventario_nombre' => $movimiento->inventario->nombre ?? 'N/A',
                        'inventario_codigo' => $movimiento->inventario->codigo_unico ?? 'N/A',
                        'ubicacion_origen' => $movimiento->ubicacion_origen ?? 'N/A',
                        'ubicacion_destino' => $movimiento->ubicacion_destino ?? 'N/A',
                        'cantidad' => $movimiento->cantidad ?? 0,
                        'fecha_movimiento' => $movimiento->fecha_movimiento ? 
                            \Carbon\Carbon::parse($movimiento->fecha_movimiento)->format('d/m/Y H:i') : 
                            $movimiento->created_at->format('d/m/Y H:i'),
                        'usuario_origen' => $movimiento->usuarioOrigen->nombre ?? 'N/A',
                        'usuario_destino' => $movimiento->usuarioDestino->nombre ?? 'N/A',
                        'realizado_por' => $movimiento->realizadoPor->name ?? 'N/A',
                        'motivo' => $movimiento->motivo ?? '',
                        'nuevo_estado' => $movimiento->nuevo_estado ?? 'disponible',
                        'created_at' => $movimiento->created_at->format('d/m/Y H:i')
                                         ];
                 });
            
            Log::info('Devolviendo respuesta JSON', ['movimientos_count' => $movimientosFormateados->count()]);
            
            return response()->json($movimientosFormateados);
            
        } catch (\Exception $e) {
            Log::error('Error en movimientosRevertibles: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Error al cargar movimientos',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 