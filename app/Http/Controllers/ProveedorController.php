<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProveedorController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Proveedor::class);
        $proveedores = Proveedor::paginate(15);
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        $this->authorize('create', Proveedor::class);
        return view('proveedores.create');
    }

    public function store(Request $request)
{
    $this->authorize('create', Proveedor::class);
    $request->validate([
        'nombre' => 'required|string|max:255',
        'contacto' => 'nullable|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'email' => 'nullable|email|unique:proveedores,email',
    ]);

    Proveedor::create($request->all());

    return redirect()->route('proveedores.index')
        ->with('success', 'Proveedor creado exitosamente.');
}


    public function show($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $this->authorize('view', $proveedor);
        return view('proveedores.show', compact('proveedor'));
    }

    public function edit($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $this->authorize('update', $proveedor);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, $id)
{
    $proveedor = Proveedor::findOrFail($id);
    $this->authorize('update', $proveedor);
    $request->validate([
        'nombre' => 'required|string|max:255',
        'contacto' => 'nullable|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'email' => 'nullable|email|unique:proveedores,email,' . $proveedor->id,
    ]);

    $proveedor->update($request->all());

    return redirect()->route('proveedores.index')
        ->with('success', 'Proveedor actualizado exitosamente.');
}

    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $this->authorize('delete', $proveedor);
        Log::info('Intento de eliminación de proveedor', ['id' => $id]);

        try {
            // Verifica si hay inventarios asociados
            $inventariosAsociados = DB::table('inventarios')->where('proveedor_id', $id)->exists();
            
            if ($inventariosAsociados) {
                Log::warning('No se puede eliminar el proveedor porque tiene inventarios asociados', ['id' => $id]);
                return back()->with('error', 'No se puede eliminar el proveedor porque tiene inventarios asociados.');
            }

            $result = $proveedor->delete();
            Log::info('Resultado de la eliminación', ['result' => $result]);

            if ($result) {
                Log::info('Proveedor eliminado con éxito');
                return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado con éxito.');
            } else {
                Log::warning('La eliminación del proveedor no fue exitosa');
                return back()->with('error', 'No se pudo eliminar el proveedor. Por favor, inténtelo de nuevo.');
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar proveedor', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'No se pudo eliminar el proveedor. ' . $e->getMessage());
        }
    }
}