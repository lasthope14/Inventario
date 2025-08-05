<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Inventario;
use App\Models\Empleado;
use App\Models\User;
use App\Models\Role;
use App\Models\Ubicacion;
use App\Notifications\MovimientoCreatedNotification;
use App\Notifications\MovimientoUpdatedNotification;
use App\Notifications\MovimientoDeletedNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class MovimientoController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Movimiento::class);
        $query = Movimiento::query();
    
        if ($request->has('inventario_id')) {
            $query->where('inventario_id', $request->inventario_id);
        }
    
        $movimientos = $query->with(['inventario', 'usuario', 'usuarioOrigen', 'usuarioDestino', 'realizadoPor'])
                             ->orderBy('fecha_movimiento', 'desc')
                             ->paginate(15);
    
        return view('movimientos.index', compact('movimientos'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Movimiento::class);
        $inventario = Inventario::findOrFail($request->inventario_id);
        $ubicaciones = Ubicacion::all();
        $empleados = Empleado::all();
    
        return view('movimientos.create', compact('inventario', 'ubicaciones', 'empleados'));
    }
    
    public function store(Request $request)
    {
        $requestId = uniqid();
        Log::info("=== Inicio de petición store movimiento {$requestId} ===", [
            'session_id' => session()->getId(),
            'user_id' => Auth::id(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'request_data' => $request->all()
        ]);

        $this->authorize('create', Movimiento::class);
        
        $validatedData = $request->validate([
            'inventario_id' => 'required|exists:inventarios,id',
            'ubicacion_origen' => 'required|exists:ubicaciones,id',
            'ubicacion_destino' => 'required|exists:ubicaciones,id',
            'usuario_origen_id' => 'required|exists:empleados,id',
            'usuario_destino_id' => 'required|exists:empleados,id',
            'fecha_movimiento' => 'required',
            'motivo' => 'nullable|string',
            'cantidad' => 'required|integer|min:1',
            'nuevo_estado' => 'required|in:disponible,en uso,en mantenimiento,dado de baja,robado'
        ]);

        $inventario = Inventario::findOrFail($request->inventario_id);
        $inventarioUbicacionOrigen = $inventario->ubicaciones()
            ->where('ubicacion_id', $request->ubicacion_origen)
            ->firstOrFail();
        
        if ($inventarioUbicacionOrigen->cantidad < $request->cantidad) {
            Log::warning('Cantidad insuficiente en ubicación origen', [
                'request_id' => $requestId,
                'ubicacion' => $request->ubicacion_origen,
                'cantidad_solicitada' => $request->cantidad,
                'cantidad_disponible' => $inventarioUbicacionOrigen->cantidad
            ]);
            return back()->withErrors(['cantidad' => 'No hay suficiente cantidad en la ubicación de origen.']);
        }

        $estadoActual = $inventarioUbicacionOrigen->estado;

        if (!empty($validatedData['fecha_movimiento'])) {
            try {
                $validatedData['fecha_movimiento'] = Carbon::createFromFormat('d/m/Y H:i', $validatedData['fecha_movimiento'])
                    ->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                Log::error('Error al convertir fecha: ' . $e->getMessage(), [
                    'request_id' => $requestId
                ]);
                return back()->withErrors(['fecha_movimiento' => 'El formato de fecha y hora no es válido']);
            }
        }

        try {
            DB::transaction(function () use ($request, $inventario, $inventarioUbicacionOrigen, $validatedData, $estadoActual, $requestId) {
                Log::info('Iniciando transacción de movimiento', [
                    'request_id' => $requestId
                ]);

                $movimiento = new Movimiento($validatedData);
                $movimiento->realizado_por_id = Auth::id();
                $movimiento->save();

                Log::info('Movimiento creado', [
                    'request_id' => $requestId,
                    'movimiento_id' => $movimiento->id
                ]);

                $inventarioUbicacionOrigen->decrement('cantidad', $request->cantidad);
                $inventarioUbicacionDestino = $inventario->ubicaciones()
                    ->where('ubicacion_id', $request->ubicacion_destino)
                    ->first();

                if ($inventarioUbicacionDestino) {
                    $inventarioUbicacionDestino->increment('cantidad', $request->cantidad);
                    // Actualizar el estado con el nuevo estado seleccionado
                    $inventarioUbicacionDestino->update(['estado' => $request->nuevo_estado]);
                } else {
                    $inventarioUbicacionDestino = $inventario->ubicaciones()->create([
                        'ubicacion_id' => $request->ubicacion_destino,
                        'cantidad' => $request->cantidad,
                        'estado' => $request->nuevo_estado
                    ]);
                }

                $inventario->ubicaciones()->where('cantidad', 0)->delete();
                $inventario->cantidadTotal = $inventario->ubicaciones()->sum('cantidad');
                $inventario->save();

                Log::info('Iniciando proceso de notificaciones', [
                    'request_id' => $requestId
                ]);

                $rolesIds = Role::whereIn('name', ['administrador', 'almacenista'])->pluck('id');
                $usersToNotify = User::whereIn('role_id', $rolesIds)->get();

                Log::info('Usuarios a notificar', [
                    'request_id' => $requestId,
                    'cantidad_usuarios' => $usersToNotify->count(),
                    'usuarios' => $usersToNotify->pluck('name', 'email')->toArray()
                ]);

                Notification::send($usersToNotify, new MovimientoCreatedNotification($movimiento));

                Log::info('Notificaciones enviadas exitosamente', [
                    'request_id' => $requestId
                ]);
            });

            Log::info("=== Fin de petición store movimiento {$requestId} ===");

            return redirect()->route('inventarios.show', $inventario->id)
                           ->with('success', 'Movimiento registrado con éxito.');

        } catch (\Exception $e) {
            Log::error("Error en petición store movimiento {$requestId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Error al crear el movimiento. Por favor, intente nuevamente.'])
                ->withInput();
        }
    }

    public function show(Movimiento $movimiento)
    {
        $this->authorize('view', $movimiento);
        $inventario = $movimiento->inventario;
        
        $movimientosPorMes = $inventario->movimientos()
            ->selectRaw('MONTH(fecha_movimiento) as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->all();

        $meses = [];
        $movimientos = [];
        for ($i = 1; $i <= 12; $i++) {
            $meses[] = ucfirst(Carbon::create()->month($i)->locale('es')->monthName);
            $movimientos[] = $movimientosPorMes[$i] ?? 0;
        }

        $ubicacionesFrecuentes = $inventario->movimientos()
            ->selectRaw('ubicacion_destino, COUNT(*) as total')
            ->groupBy('ubicacion_destino')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $ubicaciones = $ubicacionesFrecuentes->map(function ($movimiento) {
            // Como ubicacion_destino es texto, lo usamos directamente
            return $movimiento->ubicacion_destino ?? 'Desconocido';
        })->all();

        $frecuenciaUbicaciones = $ubicacionesFrecuentes->pluck('total')->all();

        $totalMovimientos = $inventario->movimientos()->count();
        $ultimoMovimiento = $inventario->movimientos()->latest('fecha_movimiento')->first();
        // Obtener la ubicación actual del inventario (primera ubicación con cantidad > 0)
        $ubicacionActual = $inventario->ubicaciones()->where('cantidad', '>', 0)->first();

        $estadisticas = [
            'meses' => $meses,
            'movimientos_por_mes' => $movimientos,
            'ubicaciones' => $ubicaciones,
            'frecuencia_ubicaciones' => $frecuenciaUbicaciones,
            'total_movimientos' => $totalMovimientos,
            'ultimo_movimiento' => $ultimoMovimiento ? $ultimoMovimiento->fecha_movimiento->format('d/m/Y H:i') : 'N/A',
            'ubicacion_actual' => $ubicacionActual ? $ubicacionActual->ubicacion->nombre : 'N/A',
            'realizado_por' => $movimiento->usuario->name ?? 'N/A'
        ];

        return view('movimientos.show', compact('movimiento', 'estadisticas'));
    }

    public function edit(Movimiento $movimiento)
    {
        $this->authorize('update', $movimiento);
        $inventario = $movimiento->inventario;
        $ubicaciones = Ubicacion::all();
        $empleados = Empleado::all();
        return view('movimientos.edit', compact('movimiento', 'inventario', 'ubicaciones', 'empleados'));
    }

    public function update(Request $request, Movimiento $movimiento)
{
    $requestId = uniqid();
    Log::info("=== Inicio de petición update movimiento {$requestId} ===", [
        'movimiento_id' => $movimiento->id,
        'user_id' => Auth::id()
    ]);

    $this->authorize('update', $movimiento);
    
    $validatedData = $request->validate([
        'inventario_id' => 'required|exists:inventarios,id',
        'ubicacion_origen' => 'required|exists:ubicaciones,id',
        'ubicacion_destino' => 'required|exists:ubicaciones,id',
        'usuario_origen_id' => 'required|exists:empleados,id',
        'usuario_destino_id' => 'required|exists:empleados,id',
        'fecha_movimiento' => 'required',
        'motivo' => 'nullable|string',
        'nuevo_estado' => 'required|in:disponible,en uso,en mantenimiento,dado de baja,robado'
    ]);

    if (!empty($validatedData['fecha_movimiento'])) {
        try {
            $validatedData['fecha_movimiento'] = Carbon::createFromFormat('d/m/Y H:i', $validatedData['fecha_movimiento'])
                ->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            Log::error("Error al convertir fecha en update {$requestId}", [
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['fecha_movimiento' => 'El formato de fecha y hora no es válido']);
        }
    }

    try {
        DB::transaction(function () use ($movimiento, $validatedData, $requestId) {
            $inventario = $movimiento->inventario;
            $datosOriginales = $movimiento->getAttributes();
            
            // Si cambió la ubicación destino
            if ($datosOriginales['ubicacion_destino'] != $validatedData['ubicacion_destino']) {
                Log::info('Detectado cambio de ubicación destino', [
                    'request_id' => $requestId,
                    'ubicacion_anterior' => $datosOriginales['ubicacion_destino'],
                    'ubicacion_nueva' => $validatedData['ubicacion_destino']
                ]);

                // 1. Quitar cantidad de la ubicación destino anterior
                $ubicacionAnterior = $inventario->ubicaciones()
                    ->where('ubicacion_id', $datosOriginales['ubicacion_destino'])
                    ->first();

                if ($ubicacionAnterior) {
                    $ubicacionAnterior->decrement('cantidad', $movimiento->cantidad);
                    $estadoActual = $ubicacionAnterior->estado;
                    
                    if ($ubicacionAnterior->fresh()->cantidad <= 0) {
                        $ubicacionAnterior->delete();
                    }
                }

                // 2. Agregar cantidad a la nueva ubicación destino
                $nuevaUbicacion = $inventario->ubicaciones()
                    ->where('ubicacion_id', $validatedData['ubicacion_destino'])
                    ->first();

                if ($nuevaUbicacion) {
                    $nuevaUbicacion->increment('cantidad', $movimiento->cantidad);
                } else {
                    $inventario->ubicaciones()->create([
                        'ubicacion_id' => $validatedData['ubicacion_destino'],
                        'cantidad' => $movimiento->cantidad,
                        'estado' => $estadoActual ?? 'disponible'
                    ]);
                }

                // 3. Actualizar cantidad total
                $inventario->cantidadTotal = $inventario->ubicaciones()->sum('cantidad');
                $inventario->save();
            }

            // Actualizar el movimiento
            $movimiento->fill($validatedData);
            $movimiento->save();

            // Detectar cambios para notificación
            $cambios = [];
            foreach ($validatedData as $campo => $valor) {
                if (isset($datosOriginales[$campo]) && $datosOriginales[$campo] != $movimiento->$campo) {
                    $cambios[$campo] = [
                        'anterior' => $datosOriginales[$campo],
                        'nuevo' => $movimiento->$campo
                    ];
                }
            }

            if (!empty($cambios)) {
                $rolesIds = Role::whereIn('name', ['administrador', 'almacenista'])->pluck('id');
                $usersToNotify = User::whereIn('role_id', $rolesIds)->get();
                Notification::send($usersToNotify, new MovimientoUpdatedNotification($movimiento, $cambios));
            }
        });

        Log::info("=== Fin de petición update movimiento {$requestId} ===");

        return redirect()
            ->route('movimientos.show', $movimiento)
            ->with('success', 'Movimiento actualizado con éxito.');

    } catch (\Exception $e) {
        Log::error("Error en actualización de movimiento {$requestId}", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return back()
            ->withErrors(['error' => 'Error al actualizar el movimiento: ' . $e->getMessage()])
            ->withInput();
    }
}
    public function destroy(Movimiento $movimiento)
{
    $requestId = uniqid();
    Log::info("=== Inicio de petición destroy movimiento {$requestId} ===", [
        'movimiento_id' => $movimiento->id,
        'user_id' => Auth::id()
    ]);

    $this->authorize('delete', $movimiento);
    
    $detallesMovimiento = [
        'inventario_nombre' => $movimiento->inventario->nombre,
        'cantidad' => $movimiento->cantidad,
        'ubicacion_origen' => $movimiento->ubicacionOrigen->nombre,
        'ubicacion_destino' => $movimiento->ubicacionDestino->nombre,
        'fecha_movimiento' => $movimiento->fecha_movimiento->format('d/m/Y H:i'),
        'realizado_por' => $movimiento->realizadoPor->name,
        'usuario_origen' => $movimiento->usuarioOrigen->nombre,
        'usuario_destino' => $movimiento->usuarioDestino->nombre
    ];

    try {
        DB::transaction(function () use ($movimiento, $detallesMovimiento, $requestId) {
            Log::info('Iniciando transacción de eliminación', [
                'request_id' => $requestId
            ]);

            $inventario = $movimiento->inventario;
            
            // Obtener ubicación destino y su estado actual
            $ubicacionDestino = $inventario->ubicaciones()
                ->where('ubicacion_id', $movimiento->ubicacion_destino)
                ->first();
            
            // Obtener el estado de la ubicación destino o un estado por defecto
            $estadoActual = $ubicacionDestino ? $ubicacionDestino->estado : 'disponible';
            
            // Crear o actualizar ubicación origen con el estado correcto
            $ubicacionOrigen = $inventario->ubicaciones()
                ->firstOrCreate(
                    ['ubicacion_id' => $movimiento->ubicacion_origen],
                    [
                        'cantidad' => 0,
                        'estado' => $estadoActual // Aseguramos que el estado no sea nulo
                    ]
                );
                
            if ($ubicacionDestino) {
                if ($ubicacionDestino->cantidad >= $movimiento->cantidad) {
                    $ubicacionDestino->decrement('cantidad', $movimiento->cantidad);
                    $ubicacionOrigen->increment('cantidad', $movimiento->cantidad);
                    
                    if ($ubicacionDestino->fresh()->cantidad == 0) {
                        $ubicacionDestino->delete();
                    }
                }
            }

            $inventario->cantidadTotal = $inventario->ubicaciones()->sum('cantidad');
            $inventario->save();

            $movimiento->delete();

            // Notificaciones
            $rolesIds = Role::whereIn('name', ['administrador', 'almacenista'])->pluck('id');
            $usersToNotify = User::whereIn('role_id', $rolesIds)->get();
            Notification::send($usersToNotify, new MovimientoDeletedNotification($detallesMovimiento));
        });

        return redirect()
            ->route('inventarios.show', $movimiento->inventario_id)
            ->with('success', 'Movimiento eliminado con éxito.');

    } catch (\Exception $e) {
        Log::error("Error en eliminación de movimiento {$requestId}", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()->withErrors(['error' => 'Error al eliminar el movimiento.']);
    }
}

    private function notifyUsers($notification)
    {
        try {
            Log::info('Iniciando notificación a usuarios');
            
            $rolesIds = Role::whereIn('name', ['administrador', 'almacenista'])->pluck('id');
            $users = User::whereIn('role_id', $rolesIds)->get();

            Log::info('Usuarios encontrados para notificar', [
                'cantidad_usuarios' => $users->count(),
                'usuarios' => $users->pluck('name', 'email')->toArray()
            ]);

            Notification::send($users, $notification);

            Log::info('Notificaciones enviadas con éxito');
        } catch (\Exception $e) {
            Log::error('Error en notifyUsers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}