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
use Barryvdh\DomPDF\Facade\Pdf as PDF;

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
        
        // Filtro por ubicación origen
        if ($request->has('ubicacion_origen') && $request->ubicacion_origen) {
            $query->where('ubicacion_origen', $request->ubicacion_origen);
        }
        
        // Filtro por ubicación destino
        if ($request->has('ubicacion_destino') && $request->ubicacion_destino) {
            $query->where('ubicacion_destino', $request->ubicacion_destino);
        }
        
        // Filtro por cualquier ubicación (origen o destino)
        if ($request->has('ubicacion_id') && $request->ubicacion_id) {
            $query->where(function($q) use ($request) {
                $q->where('ubicacion_origen', $request->ubicacion_id)
                  ->orWhere('ubicacion_destino', $request->ubicacion_id);
            });
        }
    
        $movimientos = $query->with(['inventario', 'usuario', 'usuarioOrigen', 'usuarioDestino', 'realizadoPor'])
                             ->orderBy('fecha_movimiento', 'desc')
                             ->paginate(15);
        
        // Obtener todas las ubicaciones para los filtros
        $ubicaciones = Ubicacion::orderBy('nombre')->get();
    
        return view('movimientos.index', compact('movimientos', 'ubicaciones'));
    }

    public function exportPdf(Request $request)
    {
        $this->authorize('viewAny', Movimiento::class);
        $query = Movimiento::query();
    
        if ($request->has('inventario_id')) {
            $query->where('inventario_id', $request->inventario_id);
        }
        
        // Filtro por ubicación origen
        if ($request->has('ubicacion_origen') && $request->ubicacion_origen) {
            $query->where('ubicacion_origen', $request->ubicacion_origen);
        }
        
        // Filtro por ubicación destino
        if ($request->has('ubicacion_destino') && $request->ubicacion_destino) {
            $query->where('ubicacion_destino', $request->ubicacion_destino);
        }
        
        // Filtro por cualquier ubicación (origen o destino)
        if ($request->has('ubicacion_id') && $request->ubicacion_id) {
            $query->where(function($q) use ($request) {
                $q->where('ubicacion_origen', $request->ubicacion_id)
                  ->orWhere('ubicacion_destino', $request->ubicacion_id);
            });
        }
    
        $movimientos = $query->with(['inventario', 'usuario', 'usuarioOrigen', 'usuarioDestino', 'realizadoPor'])
                             ->orderBy('fecha_movimiento', 'desc')
                             ->get();
        
        // Obtener todas las ubicaciones para mostrar nombres
        $ubicaciones = Ubicacion::orderBy('nombre')->get();
        
        // Preparar datos para el PDF
        $data = [
            'movimientos' => $movimientos,
            'ubicaciones' => $ubicaciones,
            'filtros' => $request->all(),
            'fecha_generacion' => now()->format('d/m/Y H:i:s')
        ];
        
        $pdf = PDF::loadView('movimientos.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'movimientos_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Movimiento::class);
        $inventario = Inventario::findOrFail($request->inventario_id);
        
        // Obtener ubicaciones del inventario usando consulta directa
        $inventarioUbicaciones = DB::table('inventario_ubicaciones')
            ->join('ubicaciones', 'inventario_ubicaciones.ubicacion_id', '=', 'ubicaciones.id')
            ->where('inventario_ubicaciones.inventario_id', $inventario->id)
            ->select('ubicaciones.id as ubicacion_id', 'ubicaciones.nombre as ubicacion_nombre', 
                    'inventario_ubicaciones.cantidad', 'inventario_ubicaciones.estado')
            ->get();
        
        $inventario->ubicacionesData = $inventarioUbicaciones;
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

        Log::info("Verificando autorización para crear movimiento", [
            'request_id' => $requestId,
            'user_id' => Auth::id()
        ]);

        try {
            $this->authorize('create', Movimiento::class);
            Log::info("Autorización exitosa", ['request_id' => $requestId]);
        } catch (\Exception $e) {
            Log::error("Error de autorización", [
                'request_id' => $requestId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
        
        Log::info("Iniciando validación de datos", [
            'request_id' => $requestId,
            'fecha_recibida' => $request->input('fecha_movimiento')
        ]);

        try {
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
            Log::info("Validación de datos exitosa", [
                'request_id' => $requestId,
                'validated_data' => $validatedData
            ]);
        } catch (\Exception $e) {
            Log::error("Error en validación de datos", [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'validation_errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : null
            ]);
            throw $e;
        }

        $inventario = Inventario::findOrFail($request->inventario_id);
        $inventarioUbicacionOrigen = DB::table('inventario_ubicaciones')
            ->where('inventario_id', $inventario->id)
            ->where('ubicacion_id', $request->ubicacion_origen)
            ->first();
            
        if (!$inventarioUbicacionOrigen) {
            return back()->withErrors(['ubicacion_origen' => 'No se encontró el elemento en la ubicación de origen.']);
        }
        
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

        Log::info("Procesando fecha de movimiento", [
            'request_id' => $requestId,
            'fecha_original' => $validatedData['fecha_movimiento'],
            'tipo_fecha' => gettype($validatedData['fecha_movimiento'])
        ]);

        if (!empty($validatedData['fecha_movimiento'])) {
            try {
                $fechaOriginal = $validatedData['fecha_movimiento'];
                Log::info("Intentando convertir fecha", [
                    'request_id' => $requestId,
                    'fecha_input' => $fechaOriginal,
                    'formato_esperado' => 'd/m/Y H:i'
                ]);

                $fechaCarbon = Carbon::createFromFormat('d/m/Y H:i', $fechaOriginal);
                Log::info("Fecha convertida a Carbon", [
                    'request_id' => $requestId,
                    'fecha_carbon' => $fechaCarbon->toDateTimeString(),
                    'fecha_carbon_iso' => $fechaCarbon->toISOString()
                ]);

                $validatedData['fecha_movimiento'] = $fechaCarbon->format('Y-m-d H:i:s');
                Log::info("Fecha formateada para base de datos", [
                    'request_id' => $requestId,
                    'fecha_final' => $validatedData['fecha_movimiento']
                ]);

            } catch (\Exception $e) {
                Log::error('Error al convertir fecha', [
                    'request_id' => $requestId,
                    'fecha_input' => $validatedData['fecha_movimiento'],
                    'error_message' => $e->getMessage(),
                    'error_trace' => $e->getTraceAsString()
                ]);
                return back()->withErrors(['fecha_movimiento' => 'El formato de fecha y hora no es válido: ' . $e->getMessage()]);
            }
        }

        Log::info("Iniciando transacción de base de datos", [
            'request_id' => $requestId,
            'datos_finales' => $validatedData
        ]);

        try {
            DB::transaction(function () use ($request, $inventario, $inventarioUbicacionOrigen, $validatedData, $estadoActual, $requestId) {
                Log::info('Iniciando transacción de movimiento', [
                    'request_id' => $requestId
                ]);

                Log::info('Creando objeto Movimiento', [
                    'request_id' => $requestId,
                    'datos_movimiento' => $validatedData
                ]);

                $movimiento = new Movimiento($validatedData);
                $movimiento->realizado_por_id = Auth::id();
                
                Log::info('Guardando movimiento en base de datos', [
                    'request_id' => $requestId,
                    'movimiento_data' => $movimiento->toArray()
                ]);
                
                $movimiento->save();

                Log::info('Movimiento creado', [
                    'request_id' => $requestId,
                    'movimiento_id' => $movimiento->id
                ]);

                // Decrementar cantidad en ubicación origen
                DB::table('inventario_ubicaciones')
                    ->where('inventario_id', $inventario->id)
                    ->where('ubicacion_id', $request->ubicacion_origen)
                    ->decrement('cantidad', $request->cantidad);
                
                // Buscar ubicación destino
                $inventarioUbicacionDestino = DB::table('inventario_ubicaciones')
                    ->where('inventario_id', $inventario->id)
                    ->where('ubicacion_id', $request->ubicacion_destino)
                    ->first();

                if ($inventarioUbicacionDestino) {
                    // Incrementar cantidad en ubicación destino existente
                    DB::table('inventario_ubicaciones')
                        ->where('inventario_id', $inventario->id)
                        ->where('ubicacion_id', $request->ubicacion_destino)
                        ->increment('cantidad', $request->cantidad);
                    // Actualizar el estado
                    DB::table('inventario_ubicaciones')
                        ->where('inventario_id', $inventario->id)
                        ->where('ubicacion_id', $request->ubicacion_destino)
                        ->update(['estado' => $request->nuevo_estado]);
                } else {
                    // Crear nueva ubicación destino
                    DB::table('inventario_ubicaciones')->insert([
                        'inventario_id' => $inventario->id,
                        'ubicacion_id' => $request->ubicacion_destino,
                        'cantidad' => $request->cantidad,
                        'estado' => $request->nuevo_estado,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                // Eliminar ubicaciones con cantidad 0
                DB::table('inventario_ubicaciones')
                    ->where('inventario_id', $inventario->id)
                    ->where('cantidad', 0)
                    ->delete();
                    
                // Actualizar cantidad total
                $cantidadTotal = DB::table('inventario_ubicaciones')
                    ->where('inventario_id', $inventario->id)
                    ->sum('cantidad');
                $inventario->cantidadTotal = $cantidadTotal;
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
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'validated_data' => $validatedData ?? null
            ]);

            return back()
                ->withErrors(['error' => 'Error al crear el movimiento: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Movimiento $movimiento)
    {
        $this->authorize('view', $movimiento);
        $inventario = $movimiento->inventario;
        
        // Obtener ubicaciones del inventario usando consulta directa
        $inventarioUbicaciones = DB::table('inventario_ubicaciones')
            ->join('ubicaciones', 'inventario_ubicaciones.ubicacion_id', '=', 'ubicaciones.id')
            ->where('inventario_ubicaciones.inventario_id', $inventario->id)
            ->select('ubicaciones.id as ubicacion_id', 'ubicaciones.nombre as ubicacion_nombre', 
                    'inventario_ubicaciones.cantidad', 'inventario_ubicaciones.estado')
            ->get();
        
        $inventario->ubicacionesData = $inventarioUbicaciones;
        $inventario->cantidadTotalCalculada = $inventarioUbicaciones->sum('cantidad');
        
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
        $ubicacionActualData = DB::table('inventario_ubicaciones')
            ->join('ubicaciones', 'inventario_ubicaciones.ubicacion_id', '=', 'ubicaciones.id')
            ->where('inventario_ubicaciones.inventario_id', $inventario->id)
            ->where('inventario_ubicaciones.cantidad', '>', 0)
            ->select('ubicaciones.nombre')
            ->first();

        $estadisticas = [
            'meses' => $meses,
            'movimientos_por_mes' => $movimientos,
            'ubicaciones' => $ubicaciones,
            'frecuencia_ubicaciones' => $frecuenciaUbicaciones,
            'total_movimientos' => $totalMovimientos,
            'ultimo_movimiento' => $ultimoMovimiento ? $ultimoMovimiento->fecha_movimiento->format('d/m/Y H:i') : 'N/A',
            'ubicacion_actual' => $ubicacionActualData ? $ubicacionActualData->nombre : 'N/A',
            'realizado_por' => $movimiento->usuario->name ?? 'N/A'
        ];

        return view('movimientos.show', compact('movimiento', 'estadisticas'));
    }

    public function edit(Movimiento $movimiento)
    {
        $this->authorize('update', $movimiento);
        $inventario = $movimiento->inventario;
        
        // Obtener ubicaciones del inventario usando consulta directa
        $inventarioUbicaciones = DB::table('inventario_ubicaciones')
            ->join('ubicaciones', 'inventario_ubicaciones.ubicacion_id', '=', 'ubicaciones.id')
            ->where('inventario_ubicaciones.inventario_id', $inventario->id)
            ->select('ubicaciones.id as ubicacion_id', 'ubicaciones.nombre as ubicacion_nombre', 
                    'inventario_ubicaciones.cantidad', 'inventario_ubicaciones.estado')
            ->get();
        
        $inventario->ubicacionesData = $inventarioUbicaciones;
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
                $ubicacionAnterior = DB::table('inventario_ubicaciones')
                    ->where('inventario_id', $inventario->id)
                    ->where('ubicacion_id', $datosOriginales['ubicacion_destino'])
                    ->first();

                if ($ubicacionAnterior) {
                    $estadoActual = $ubicacionAnterior->estado;
                    
                    // Decrementar cantidad
                    DB::table('inventario_ubicaciones')
                        ->where('inventario_id', $inventario->id)
                        ->where('ubicacion_id', $datosOriginales['ubicacion_destino'])
                        ->decrement('cantidad', $movimiento->cantidad);
                    
                    // Verificar si la cantidad llegó a 0 y eliminar si es necesario
                    $cantidadActual = DB::table('inventario_ubicaciones')
                        ->where('inventario_id', $inventario->id)
                        ->where('ubicacion_id', $datosOriginales['ubicacion_destino'])
                        ->value('cantidad');
                        
                    if ($cantidadActual <= 0) {
                        DB::table('inventario_ubicaciones')
                            ->where('inventario_id', $inventario->id)
                            ->where('ubicacion_id', $datosOriginales['ubicacion_destino'])
                            ->delete();
                    }
                }

                // 2. Agregar cantidad a la nueva ubicación destino
                $nuevaUbicacion = DB::table('inventario_ubicaciones')
                    ->where('inventario_id', $inventario->id)
                    ->where('ubicacion_id', $validatedData['ubicacion_destino'])
                    ->first();

                if ($nuevaUbicacion) {
                    DB::table('inventario_ubicaciones')
                        ->where('inventario_id', $inventario->id)
                        ->where('ubicacion_id', $validatedData['ubicacion_destino'])
                        ->increment('cantidad', $movimiento->cantidad);
                } else {
                    DB::table('inventario_ubicaciones')->insert([
                        'inventario_id' => $inventario->id,
                        'ubicacion_id' => $validatedData['ubicacion_destino'],
                        'cantidad' => $movimiento->cantidad,
                        'estado' => $estadoActual ?? 'disponible',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                // 3. Actualizar cantidad total
                $cantidadTotal = DB::table('inventario_ubicaciones')
                    ->where('inventario_id', $inventario->id)
                    ->sum('cantidad');
                $inventario->cantidadTotal = $cantidadTotal;
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
            $ubicacionDestino = DB::table('inventario_ubicaciones')
                ->where('inventario_id', $inventario->id)
                ->where('ubicacion_id', $movimiento->ubicacion_destino)
                ->first();
            
            // Obtener el estado de la ubicación destino o un estado por defecto
            $estadoActual = $ubicacionDestino ? $ubicacionDestino->estado : 'disponible';
            
            // Buscar ubicación origen
            $ubicacionOrigen = DB::table('inventario_ubicaciones')
                ->where('inventario_id', $inventario->id)
                ->where('ubicacion_id', $movimiento->ubicacion_origen)
                ->first();
                
            if ($ubicacionDestino) {
                if ($ubicacionDestino->cantidad >= $movimiento->cantidad) {
                    // Decrementar cantidad en ubicación destino
                    DB::table('inventario_ubicaciones')
                        ->where('inventario_id', $inventario->id)
                        ->where('ubicacion_id', $movimiento->ubicacion_destino)
                        ->decrement('cantidad', $movimiento->cantidad);
                    
                    // Incrementar o crear ubicación origen
                    if ($ubicacionOrigen) {
                        DB::table('inventario_ubicaciones')
                            ->where('inventario_id', $inventario->id)
                            ->where('ubicacion_id', $movimiento->ubicacion_origen)
                            ->increment('cantidad', $movimiento->cantidad);
                    } else {
                        DB::table('inventario_ubicaciones')->insert([
                            'inventario_id' => $inventario->id,
                            'ubicacion_id' => $movimiento->ubicacion_origen,
                            'cantidad' => $movimiento->cantidad,
                            'estado' => $estadoActual,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                    
                    // Verificar si la ubicación destino quedó en 0 y eliminarla
                    $cantidadDestino = DB::table('inventario_ubicaciones')
                        ->where('inventario_id', $inventario->id)
                        ->where('ubicacion_id', $movimiento->ubicacion_destino)
                        ->value('cantidad');
                        
                    if ($cantidadDestino == 0) {
                        DB::table('inventario_ubicaciones')
                            ->where('inventario_id', $inventario->id)
                            ->where('ubicacion_id', $movimiento->ubicacion_destino)
                            ->delete();
                    }
                }
            }

            // Actualizar cantidad total
            $cantidadTotal = DB::table('inventario_ubicaciones')
                ->where('inventario_id', $inventario->id)
                ->sum('cantidad');
            $inventario->cantidadTotal = $cantidadTotal;
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