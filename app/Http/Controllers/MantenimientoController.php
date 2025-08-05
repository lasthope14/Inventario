<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use App\Models\Inventario;
use App\Models\User;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProximoMantenimientoNotification;
use App\Notifications\NuevoMantenimientoNotification;
use Illuminate\Support\Facades\Log;
use App\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MantenimientoController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Mantenimiento::class);
        $query = Mantenimiento::with(['inventario', 'responsable', 'solicitadoPor']);

        if ($request->has('inventario_id')) {
            $query->where('inventario_id', $request->inventario_id);
        }

        if ($request->has('filtro')) {
            switch ($request->filtro) {
                case 'realizados':
                    $query->whereNotNull('fecha_realizado');
                    break;
                case 'pendientes':
                    $query->whereNull('fecha_realizado');
                    break;
            }
        }

        $mantenimientos = $query->orderBy('fecha_programada', 'desc')->paginate(15);
        return view('mantenimientos.index', compact('mantenimientos'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Mantenimiento::class);
        $inventario = null;
        if ($request->has('inventario_id')) {
            $inventario = Inventario::findOrFail($request->inventario_id);
        }
        $inventarios = Inventario::all();
        $proveedores = Proveedor::all();
        return view('mantenimientos.create', compact('inventario', 'inventarios', 'proveedores'));
    }

    public function store(Request $request)
    {
        $requestId = uniqid();
        Log::info("=== Inicio de petición store mantenimiento {$requestId} ===", [
            'session_id' => session()->getId(),
            'user_id' => Auth::id(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'request_data' => $request->all()
        ]);

        $this->authorize('create', Mantenimiento::class);
        
        $validatedData = $request->validate([
            'inventario_id' => 'required|exists:inventarios,id',
            'tipo' => 'required|in:preventivo,correctivo',
            'fecha_programada' => 'required',
            'fecha_realizado' => 'nullable',
            'descripcion' => 'required|string',
            'resultado' => 'nullable|string',
            'responsable_id' => 'nullable|exists:proveedores,id',
            'periodicidad' => 'required_if:tipo,preventivo|nullable|in:diario,semanal,quincenal,mensual,trimestral,semestral,anual',
            'costo' => 'nullable|numeric',
            'autorizado_por' => 'nullable|string',
        ]);

        try {
            if (!empty($validatedData['fecha_programada'])) {
                $validatedData['fecha_programada'] = Carbon::createFromFormat('d/m/Y', $validatedData['fecha_programada'])
                    ->format('Y-m-d');
                Log::info('Fecha programada convertida', [
                    'request_id' => $requestId,
                    'fecha' => $validatedData['fecha_programada']
                ]);
            }

            if (!empty($validatedData['fecha_realizado'])) {
                $validatedData['fecha_realizado'] = Carbon::createFromFormat('d/m/Y', $validatedData['fecha_realizado'])
                    ->format('Y-m-d');
            }

            $fechaProgramada = Carbon::parse($validatedData['fecha_programada']);

            if ($validatedData['tipo'] === 'preventivo') {
                $fechaProgramada = $this->calcularProximaFecha($fechaProgramada, $validatedData['periodicidad']);
                $validatedData['fecha_programada'] = $fechaProgramada->format('Y-m-d');
                Log::info('Fecha programada calculada para preventivo', [
                    'request_id' => $requestId,
                    'fecha' => $validatedData['fecha_programada']
                ]);
            } else {
                $validatedData['periodicidad'] = null;
            }

            $validatedData['user_id'] = Auth::id();
            $validatedData['responsable_id'] = $request->responsable_id ?: null;

            Log::info('Preparando creación de mantenimiento', [
                'request_id' => $requestId,
                'data' => $validatedData
            ]);
            
            $mantenimiento = Mantenimiento::create($validatedData);
            
            Log::info('Mantenimiento creado exitosamente', [
                'request_id' => $requestId,
                'mantenimiento_id' => $mantenimiento->id,
                'tipo' => $mantenimiento->tipo,
                'fecha_programada' => $mantenimiento->fecha_programada
            ]);

            $rolesIds = Role::whereIn('name', ['administrador', 'almacenista'])->pluck('id');
            $usersToNotify = User::whereIn('role_id', $rolesIds)->get();
            
            Log::info('Usuarios a notificar', [
                'request_id' => $requestId,
                'cantidad_usuarios' => $usersToNotify->count(),
                'usuarios' => $usersToNotify->pluck('id')->toArray()
            ]);

            Notification::send($usersToNotify, new NuevoMantenimientoNotification($mantenimiento));
            
            Log::info("=== Fin de petición store mantenimiento {$requestId} ===");

            return redirect()->route('inventarios.show', $mantenimiento->inventario_id)
                           ->with('success', 'Mantenimiento creado con éxito.');

        } catch (\Exception $e) {
            Log::error("Error en petición store mantenimiento {$requestId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $validatedData ?? null
            ]);
            
            return back()
                ->withErrors(['error' => 'Error al crear el mantenimiento. Por favor, verifique los datos e intente nuevamente.'])
                ->withInput();
        }
    }

    public function show(Mantenimiento $mantenimiento)
    {
        $this->authorize('view', $mantenimiento);
        $mantenimientosAnteriores = Mantenimiento::where('inventario_id', $mantenimiento->inventario_id)
                                                 ->where('id', '!=', $mantenimiento->id)
                                                 ->orderBy('fecha_programada', 'desc')
                                                 ->take(5)
                                                 ->get();

        $totalMantenimientos = Mantenimiento::where('inventario_id', $mantenimiento->inventario_id)->count();

        $ultimoMantenimiento = Mantenimiento::where('inventario_id', $mantenimiento->inventario_id)
                                            ->where('fecha_realizado', '!=', null)
                                            ->orderBy('fecha_realizado', 'desc')
                                            ->first();

        $proximoMantenimiento = null;
        if ($mantenimiento->tipo === 'preventivo' && $mantenimiento->periodicidad) {
            $fechaBase = $mantenimiento->fecha_realizado ?? $mantenimiento->fecha_programada;
            $proximoMantenimiento = $this->calcularProximaFecha($fechaBase, $mantenimiento->periodicidad);
        }

        $mantenimientosRealizados = Mantenimiento::where('inventario_id', $mantenimiento->inventario_id)
                                                 ->whereNotNull('fecha_realizado')
                                                 ->count();
        $mantenimientosPendientes = $totalMantenimientos - $mantenimientosRealizados;

        return view('mantenimientos.show', compact(
            'mantenimiento',
            'mantenimientosAnteriores',
            'totalMantenimientos',
            'ultimoMantenimiento',
            'proximoMantenimiento',
            'mantenimientosRealizados',
            'mantenimientosPendientes'
        ));
    }

    public function edit(Mantenimiento $mantenimiento)
    {
        $this->authorize('update', $mantenimiento);
        $inventarios = Inventario::all();
        $proveedores = Proveedor::all();
        return view('mantenimientos.edit', compact('mantenimiento', 'inventarios', 'proveedores'));
    }

    public function update(Request $request, Mantenimiento $mantenimiento)
    {
        $this->authorize('update', $mantenimiento);
        
        $validatedData = $request->validate([
            'inventario_id' => 'required|exists:inventarios,id',
            'tipo' => 'required|in:preventivo,correctivo',
            'fecha_programada' => 'required',
            'fecha_realizado' => 'nullable',
            'descripcion' => 'required|string',
            'resultado' => 'nullable|string',
            'responsable_id' => 'nullable|exists:proveedores,id',
            'periodicidad' => 'required_if:tipo,preventivo|nullable|in:diario,semanal,quincenal,mensual,trimestral,semestral,anual',
            'costo' => 'nullable|numeric',
            'autorizado_por' => 'nullable|string',
        ]);

        try {
            if (!empty($validatedData['fecha_programada'])) {
                $validatedData['fecha_programada'] = Carbon::createFromFormat('d/m/Y', $validatedData['fecha_programada'])
                    ->format('Y-m-d');
            }

            if (!empty($validatedData['fecha_realizado'])) {
                $validatedData['fecha_realizado'] = Carbon::createFromFormat('d/m/Y', $validatedData['fecha_realizado'])
                    ->format('Y-m-d');
            }

            if ($validatedData['tipo'] === 'correctivo') {
                $validatedData['periodicidad'] = null;
            } elseif ($mantenimiento->periodicidad != $request->periodicidad) {
                $fechaProgramada = Carbon::parse($validatedData['fecha_programada']);
                $validatedData['fecha_programada'] = $this->calcularProximaFecha($fechaProgramada, $request->periodicidad)
                    ->format('Y-m-d');
            }

            $validatedData['responsable_id'] = $request->responsable_id ?: null;

            $mantenimiento->update($validatedData);

            if ($mantenimiento->tipo === 'preventivo' && $mantenimiento->fecha_realizado) {
                $this->programarSiguienteMantenimiento($mantenimiento);
            }

            return redirect()->route('inventarios.show', $mantenimiento->inventario_id)
                           ->with('success', 'Mantenimiento actualizado con éxito.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar mantenimiento: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Error al actualizar el mantenimiento. Por favor, verifique los datos e intente nuevamente.'])
                ->withInput();
        }
    }

    public function destroy(Mantenimiento $mantenimiento)
    {
        $this->authorize('delete', $mantenimiento);
        $mantenimiento->delete();
        return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento eliminado con éxito.');
    }

    public function marcarRealizado(Mantenimiento $mantenimiento)
    {
        Log::info('Iniciando marcarRealizado', ['mantenimiento_id' => $mantenimiento->id]);

        try {
            $this->authorize('update', $mantenimiento);
            Log::info('Autorización exitosa');

            $mantenimiento->fecha_realizado = now();
            $mantenimiento->save();
            Log::info('Mantenimiento actualizado', ['fecha_realizado' => $mantenimiento->fecha_realizado]);

            if ($mantenimiento->tipo == 'preventivo' && $mantenimiento->periodicidad) {
                Log::info('Iniciando programación del siguiente mantenimiento');
                $this->programarSiguienteMantenimiento($mantenimiento);
                Log::info('Siguiente mantenimiento programado');
            }

            Log::info('marcarRealizado completado con éxito');
            return response()->json(['success' => true, 'message' => 'Mantenimiento marcado como realizado.']);
        } catch (\Exception $e) {
            Log::error('Error en marcarRealizado', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error al marcar el mantenimiento como realizado.'], 500);
        }
    }

    public function posponerMantenimiento(Mantenimiento $mantenimiento)
    {
        Log::info('Iniciando posponerMantenimiento', ['mantenimiento_id' => $mantenimiento->id]);

        try {
            $this->authorize('update', $mantenimiento);
            Log::info('Autorización exitosa');

            if ($mantenimiento->tipo !== 'preventivo') {
                Log::warning('Intento de posponer un mantenimiento no preventivo');
                return response()->json(['success' => false, 'message' => 'Solo se pueden posponer mantenimientos preventivos.'], 400);
            }

            $nuevaFecha = $this->calcularProximaFecha($mantenimiento->fecha_programada, $mantenimiento->periodicidad);
            $mantenimiento->update([
                'fecha_programada' => $nuevaFecha,
                'veces_pospuesto' => $mantenimiento->veces_pospuesto + 1
            ]);

            Log::info('Mantenimiento pospuesto', [
                'nueva_fecha' => $nuevaFecha,
                'veces_pospuesto' => $mantenimiento->veces_pospuesto
            ]);

            return response()->json(['success' => true, 'message' => 'Mantenimiento pospuesto con éxito.']);
        } catch (\Exception $e) {
            Log::error('Error en posponerMantenimiento', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error al posponer el mantenimiento.'], 500);
        }
    }

    private function programarSiguienteMantenimiento(Mantenimiento $mantenimiento)
    {
        $siguienteFecha = $this->calcularProximaFecha($mantenimiento->fecha_programada, $mantenimiento->periodicidad);

        Mantenimiento::create([
            'inventario_id' => $mantenimiento->inventario_id,
            'tipo' => 'preventivo',
            'fecha_programada' => $siguienteFecha,
            'descripcion' => $mantenimiento->descripcion,
            'responsable_id' => $mantenimiento->responsable_id,
            'periodicidad' => $mantenimiento->periodicidad,
            'user_id' => Auth::id(),
        ]);
    }

    private function calcularProximaFecha(Carbon $fechaBase, string $periodicidad): Carbon
    {
        $siguienteFecha = $fechaBase->copy();

        switch ($periodicidad) {
            case 'diario':
                $siguienteFecha->addDay();
                break;
            case 'semanal':
                $siguienteFecha->addWeek();
                break;
            case 'quincenal':
                $siguienteFecha->addWeeks(2);
                break;
            case 'mensual':
                $siguienteFecha->addMonthNoOverflow();
                break;
            case 'trimestral':
                $siguienteFecha->addMonthsNoOverflow(3);
                break;
            case 'semestral':
                $siguienteFecha->addMonthsNoOverflow(6);
                break;
            case 'anual':
                $siguienteFecha->addYear();
                break;
        }

        while (!checkdate($siguienteFecha->month, $siguienteFecha->day, $siguienteFecha->year)) {
            $siguienteFecha->subDay();
        }

        return $siguienteFecha;
    }
}