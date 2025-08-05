<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;  // Añadido el punto y coma

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::with('inventario')->paginate(15);
        return view('documentos.index', compact('documentos'));
    }

    public function create()
    {
        $inventarios = Inventario::all();
        return view('documentos.create', compact('inventarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'inventario_id' => 'required|exists:inventarios,id',
            'archivo' => 'required|file|max:40960',  // 40MB máximo
        ]);

        // Obtener el inventario para acceder al código único
        $inventario = \App\Models\Inventario::findOrFail($request->inventario_id);
        
        // Verificar que no existe ya un documento con el mismo nombre para este elemento
        $existeDocumento = Documento::where('inventario_id', $request->inventario_id)
            ->where('nombre', $request->nombre)
            ->first();
            
        if ($existeDocumento) {
            return redirect()->back()
                ->withErrors(['nombre' => 'Ya existe un documento con este nombre para este elemento.'])
                ->withInput();
        }

        $file = $request->file('archivo');
        $extension = $file->getClientOriginalExtension();
        
        // 🔥 SOLUCIÓN: Crear nombre único usando código del elemento
        $fileName = $inventario->codigo_unico . '_' . $request->nombre . '.' . $extension;
        
        // Verificar que el archivo físico no existe (protección adicional)
        $counter = 1;
        $originalFileName = $fileName;
        while (Storage::disk('public')->exists('documentos/' . $fileName)) {
            $fileNameWithoutExt = $inventario->codigo_unico . '_' . $request->nombre . '_' . $counter;
            $fileName = $fileNameWithoutExt . '.' . $extension;
            $counter++;
        }
        
        // Guardar el archivo con nombre único
        $path = $file->storeAs('documentos', $fileName, 'public');
        
        // Generar hash para control adicional
        $tempPath = $file->getRealPath();
        $hash = hash_file('md5', $tempPath);

        // Crear registro en base de datos
        Documento::create([
            'nombre' => $request->nombre,
            'inventario_id' => $request->inventario_id,
            'ruta' => $path,
            'hash' => $hash,
        ]);

        return redirect()->back()->with('success', 'Documento subido exitosamente con nombre único: ' . $fileName);
    }

    public function show(Documento $documento)
    {
        return view('documentos.show', compact('documento'));
    }

    public function download(Documento $documento)
{
    \Log::info('Iniciando descarga de documento', [
        'documento_id' => $documento->id,
        'nombre' => $documento->nombre,
        'ruta' => $documento->ruta,
        'hash' => $documento->hash
    ]);

    // Usar directamente la ruta de la base de datos
    $rutaPrincipal = storage_path('app/public/' . $documento->ruta);
    
    \Log::info('Verificando ruta principal', [
        'ruta' => $rutaPrincipal,
        'existe' => file_exists($rutaPrincipal)
    ]);

    if (file_exists($rutaPrincipal)) {
        \Log::info('Archivo encontrado en ruta principal', ['ruta' => $rutaPrincipal]);
        
        // Obtener la extensión del archivo
        $extension = pathinfo($rutaPrincipal, PATHINFO_EXTENSION);
        $nombreDescarga = $documento->nombre . '.' . $extension;
        
        return response()->download(
            $rutaPrincipal,
            $nombreDescarga,
            ['Content-Type' => 'application/pdf']
        );
    }

    // Si no se encuentra en la ruta principal, buscar en ubicaciones alternativas (para compatibilidad)
    $possiblePaths = [
        storage_path('app/public/documentos/' . $documento->hash . '.pdf'),
        storage_path('app/documentos/' . $documento->hash . '.pdf'),
        public_path('storage/documentos/' . $documento->hash . '.pdf'),
        storage_path('app/temp/imports/' . $documento->hash . '.pdf'),
        storage_path('app/public/media/' . $documento->hash . '.pdf'),
    ];

    $foundPath = null;
    foreach ($possiblePaths as $path) {
        \Log::info('Verificando ruta alternativa', ['path' => $path]);
        if (file_exists($path)) {
            $foundPath = $path;
            \Log::info('Archivo encontrado en ruta alternativa:', ['path' => $foundPath]);
            break;
        }
    }

    if (!$foundPath) {
        \Log::error('Archivo no encontrado en ninguna ubicación', [
            'documento_id' => $documento->id,
            'ruta_bd' => $documento->ruta,
            'hash' => $documento->hash,
            'ruta_principal' => $rutaPrincipal,
            'rutas_alternativas_intentadas' => $possiblePaths
        ]);
        abort(404, 'Archivo no encontrado');
    }

    // Obtener la extensión del archivo encontrado
    $extension = pathinfo($foundPath, PATHINFO_EXTENSION);
    $nombreDescarga = $documento->nombre . '.' . $extension;

    return response()->download(
        $foundPath,
        $nombreDescarga,
        ['Content-Type' => 'application/pdf']
    );
}

    public function destroy(Documento $documento)
    {
        Storage::disk('public')->delete($documento->ruta);
        $documento->delete();
        return redirect()->back()->with('success', 'Documento eliminado exitosamente.');
    }
}