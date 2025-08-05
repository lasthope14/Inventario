<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoriaController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Categoria::class);
        $categorias = Categoria::paginate(15);
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        $this->authorize('create', Categoria::class);
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Categoria::class);
        $request->validate([
            'nombre' => 'required|string|max:255',
            'prefijo' => 'required|string|max:3|unique:categorias',
        ]);

        Categoria::create($request->all());

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function show(Categoria $categoria)
    {
        $this->authorize('view', $categoria);
        return view('categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria)
    {
        $this->authorize('update', $categoria);
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $this->authorize('update', $categoria);
        $request->validate([
            'nombre' => 'required|string|max:255',
            'prefijo' => 'required|string|max:3|unique:categorias,prefijo,' . $categoria->id,
        ]);

        $categoria->update($request->all());

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Categoria $categoria)
    {
        $this->authorize('delete', $categoria);
    if ($categoria->inventarios()->exists()) {
        return back()->with('error', 'No se puede eliminar la categoría porque tiene elementos de inventario asociados.');
    }

    $categoria->delete();
    return redirect()->route('categorias.index')->with('success', 'Categoría eliminada con éxito.');
    }
}