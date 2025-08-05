<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ubicacion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class UbicacionController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Ubicacion::class);
        $ubicaciones = Ubicacion::all();
        return view('ubicaciones.index', compact('ubicaciones'));
    }

    public function create()
    {
        $this->authorize('create', Ubicacion::class);
        return view('ubicaciones.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Ubicacion::class);
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        Ubicacion::create($validatedData);

        return redirect()->route('ubicaciones.index')->with('success', 'Ubicación creada con éxito.');
    }

    public function show(Ubicacion $ubicacion)
    {
        $this->authorize('view', $ubicacion);
        return view('ubicaciones.show', compact('ubicacion'));
    }

    public function edit(Ubicacion $ubicacion)
    {
        $this->authorize('update', $ubicacion);
        return view('ubicaciones.edit', compact('ubicacion'));
    }

    public function update(Request $request, Ubicacion $ubicacion)
    {
        $this->authorize('update', $ubicacion);
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $ubicacion->update($validatedData);

        return redirect()->route('ubicaciones.index')->with('success', 'Ubicación actualizada con éxito.');
    }

    public function destroy($id)
{
    try {
        $ubicacion = Ubicacion::findOrFail($id);
        
        $this->authorize('delete', $ubicacion);

        if ($ubicacion->inventarios()->exists()) {
            return back()->with('error', 'No se puede eliminar la ubicación porque tiene elementos de inventario asociados.');
        }

        DB::beginTransaction();

        $deleted = $ubicacion->delete();

        if ($deleted) {
            DB::commit();
            return redirect()->route('ubicaciones.index')->with('success', 'Ubicación eliminada con éxito.');
        } else {
            DB::rollBack();
            return back()->with('error', 'No se pudo eliminar la ubicación. Por favor, inténtelo de nuevo.');
        }
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return back()->with('error', 'La ubicación no existe.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error al eliminar la ubicación: ' . $e->getMessage());
    }
}
}