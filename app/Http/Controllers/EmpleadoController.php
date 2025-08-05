<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EmpleadoController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Empleado::class);
        $empleados = Empleado::all();
        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        $this->authorize('create', Empleado::class);
        return view('empleados.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Empleado::class);
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cargo' => 'nullable|string|max:255',
        ]);

        Empleado::create($request->all());

        return redirect()->route('empleados.index')->with('success', 'Empleado creado exitosamente.');
    }

    public function show(Empleado $empleado)
    {
        $this->authorize('view', $empleado);
        return view('empleados.show', compact('empleado'));
    }

    public function edit(Empleado $empleado)
    {
        $this->authorize('update', $empleado);
        return view('empleados.edit', compact('empleado'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $this->authorize('update', $empleado);
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cargo' => 'nullable|string|max:255',
        ]);

        $empleado->update($request->all());

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado exitosamente.');
    }

    public function destroy(Empleado $empleado)
    {
        $this->authorize('delete', $empleado);
        $empleado->delete();

        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado exitosamente.');
    }
}