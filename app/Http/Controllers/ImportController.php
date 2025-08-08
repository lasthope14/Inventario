<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Imports\InventariosImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Ubicacion;
use App\Models\ImportLog;
use App\Models\Inventario;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    protected function utf8Message($message)
    {
        return mb_convert_encoding($message, 'UTF-8', mb_list_encodings());
    }

    public function showImportForm()
{
    if (!in_array(auth()->user()->role->name, ['administrador', 'almacenista'])) {
        abort(403, $this->utf8Message('No tienes permisos para acceder a esta sección.'));
    }
    
    $categorias = Categoria::all();
    $proveedores = Proveedor::all();
    $ubicaciones = Ubicacion::all();
    
    // Modificar esta consulta
    $importLogs = ImportLog::with('user')
                    ->select('import_logs.*')
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get()
                    ->map(function ($log) {
                        $log->created_at = \Carbon\Carbon::parse($log->created_at);
                        return $log;
                    });
    
    return view('inventarios.import', compact('categorias', 'proveedores', 'ubicaciones', 'importLogs'));
}

    public function analyzeFile(Request $request)
    {
        if (!in_array(auth()->user()->role->name, ['administrador', 'almacenista'])) {
            abort(403, $this->utf8Message('No tienes permisos para realizar análisis.'));
        }

        $request->validate([
            'file' => 'required|file|mimes:zip|max:102400'
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        
        // Crear directorio temporal único
        $tempDir = 'temp_analyze_' . uniqid();
        $tempPath = storage_path('app/' . $tempDir);
        
        try {
            // Crear el directorio temporal usando PHP nativo
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            
            // Guardar el archivo ZIP temporalmente
            $zipPath = $tempPath . '/' . $fileName;
            $file->move($tempPath, $fileName);
            
            // Extraer el ZIP
            $zip = new \ZipArchive;
            if ($zip->open($zipPath) === TRUE) {
                $zip->extractTo($tempPath);
                $zip->close();
                // Eliminar el archivo ZIP después de extraer
                unlink($zipPath);
            } else {
                $this->cleanupDirectory($tempPath);
                return response()->json([
                    'success' => false,
                    'message' => $this->utf8Message('No se pudo extraer el archivo ZIP')
                ]);
            }
            
            // Buscar el archivo Excel recursivamente usando PHP nativo
            $excelFile = null;
            $allFiles = $this->getAllFilesRecursive($tempPath);
            
            // Log de depuración para ver qué archivos se encontraron
            \Log::info('Archivos encontrados en ZIP:', $allFiles);
            
            foreach ($allFiles as $file) {
                $fileName = basename($file);
                if (preg_match('/\.(xlsx|xls)$/i', $fileName)) {
                    $excelFile = $file;
                    break;
                }
            }
            
            if (!$excelFile) {
                $this->cleanupDirectory($tempPath);
                return response()->json([
                    'success' => false,
                    'message' => $this->utf8Message('No se encontró un archivo Excel en el ZIP. Archivos encontrados: ' . implode(', ', array_map('basename', $allFiles)))
                ]);
            }
            
            // Analizar el archivo Excel
            $analysis = $this->performFileAnalysis($excelFile);
            
            // Limpiar directorio temporal
            $this->cleanupDirectory($tempPath);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis
            ]);
            
        } catch (\Exception $e) {
            // Limpiar directorio temporal en caso de error
            try {
                $this->cleanupDirectory($tempPath);
            } catch (\Exception $cleanupError) {
                \Log::error('Error al limpiar directorio temporal: ' . $cleanupError->getMessage());
            }
            
            return response()->json([
                'success' => false,
                'message' => $this->utf8Message('Error al analizar el archivo: ') . $e->getMessage()
            ]);
        }
    }
    
    private function getAllFilesRecursive($dir)
    {
        $files = [];
        if (is_dir($dir)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $files[] = $file->getPathname();
                }
            }
        }
        return $files;
    }
    
    private function cleanupDirectory($dir)
    {
        if (is_dir($dir)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            
            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    rmdir($file->getPathname());
                } else {
                    unlink($file->getPathname());
                }
            }
            rmdir($dir);
        }
    }

    private function performFileAnalysis($excelFile)
    {
        $spreadsheet = IOFactory::load($excelFile);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        $headers = array_map('strtolower', array_map('trim', array_shift($rows)));
        
        $analysis = [
            'total_rows' => count($rows),
            'duplicates' => [],
            'conflicts' => [],
            'missing_references' => [],
            'warnings' => [],
            'summary' => []
        ];
        
        $numerosSerieUsados = [];
        
        foreach ($rows as $rowIndex => $row) {
            if (empty(array_filter($row))) {
                continue;
            }
            
            $data = array_combine($headers, $row);
            $rowNumber = $rowIndex + 2; // +2 porque eliminamos headers y empezamos en 1
            
            // Verificar duplicados por número de serie
            if (isset($data['numero_serie']) && !empty($data['numero_serie'])) {
                $numeroSerie = trim($data['numero_serie']);
                if (isset($numerosSerieUsados[$numeroSerie])) {
                    $analysis['duplicates'][] = [
                        'type' => 'numero_serie_duplicado',
                        'value' => $numeroSerie,
                        'rows' => [$numerosSerieUsados[$numeroSerie], $rowNumber],
                        'message' => "El número de serie '{$numeroSerie}' está duplicado en las filas {$numerosSerieUsados[$numeroSerie]} y {$rowNumber}"
                    ];
                } else {
                    $numerosSerieUsados[$numeroSerie] = $rowNumber;
                }
                
                // Verificar si ya existe en la base de datos por número de serie
                $existingInventario = Inventario::where('numero_serie', $numeroSerie)->first();
                if ($existingInventario) {
                    $analysis['conflicts'][] = [
                        'type' => 'numero_serie_existente',
                        'value' => $numeroSerie,
                        'row' => $rowNumber,
                        'existing_id' => $existingInventario->id,
                        'existing_codigo' => $existingInventario->codigo_unico,
                        'message' => "El número de serie '{$numeroSerie}' ya existe en la base de datos (Elemento: {$existingInventario->nombre}, Código: {$existingInventario->codigo_unico})"
                    ];
                }
            } else {
                // Si no tiene número de serie, es una advertencia
                $analysis['warnings'][] = [
                    'type' => 'numero_serie_vacio',
                    'row' => $rowNumber,
                    'message' => "El número de serie está vacío en la fila {$rowNumber}. Esto puede causar problemas de identificación única."
                ];
            }
            
            // Verificar referencias de categoría
            if (isset($data['categoria_id']) && !empty($data['categoria_id'])) {
                $categoria = Categoria::find($data['categoria_id']);
                if (!$categoria) {
                    $analysis['missing_references'][] = [
                        'type' => 'categoria_no_encontrada',
                        'value' => $data['categoria_id'],
                        'row' => $rowNumber,
                        'message' => "La categoría con ID {$data['categoria_id']} no existe"
                    ];
                }
            }
            
            // Verificar referencias de proveedor
            if (isset($data['proveedor_id']) && !empty($data['proveedor_id'])) {
                $proveedor = Proveedor::find($data['proveedor_id']);
                if (!$proveedor) {
                    $analysis['missing_references'][] = [
                        'type' => 'proveedor_no_encontrado',
                        'value' => $data['proveedor_id'],
                        'row' => $rowNumber,
                        'message' => "El proveedor con ID {$data['proveedor_id']} no existe"
                    ];
                }
            }
            
            // Verificar referencias de ubicación
            if (isset($data['ubicacion_id']) && !empty($data['ubicacion_id'])) {
                $ubicacion = Ubicacion::find($data['ubicacion_id']);
                if (!$ubicacion) {
                    $analysis['missing_references'][] = [
                        'type' => 'ubicacion_no_encontrada',
                        'value' => $data['ubicacion_id'],
                        'row' => $rowNumber,
                        'message' => "La ubicación con ID {$data['ubicacion_id']} no existe"
                    ];
                }
            }
            
            // Verificar campos requeridos (excluyendo numero_serie que ya se verifica arriba)
            $requiredFields = ['nombre', 'categoria_id', 'cantidad', 'estado'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    $analysis['warnings'][] = [
                        'type' => 'campo_vacio',
                        'field' => $field,
                        'row' => $rowNumber,
                        'message' => "El campo '{$field}' está vacío en la fila {$rowNumber}"
                    ];
                }
            }
        }
        
        // Generar resumen
        $analysis['summary'] = [
            'total_duplicates' => count($analysis['duplicates']),
            'total_conflicts' => count($analysis['conflicts']),
            'total_missing_references' => count($analysis['missing_references']),
            'total_warnings' => count($analysis['warnings']),
            'can_import' => count($analysis['duplicates']) === 0 && count($analysis['conflicts']) === 0 && count($analysis['missing_references']) === 0
        ];
        
        return $analysis;
    }

    public function import(Request $request)
    {
        if (!in_array(auth()->user()->role->name, ['administrador', 'almacenista'])) {
            abort(403, $this->utf8Message('No tienes permisos para realizar esta acción.'));
        }

        $request->validate([
            'file' => 'required|file|mimes:zip',
        ]);

        $zipFile = $request->file('file');
        $fileName = $zipFile->getClientOriginalName();
        
        // Crear directorio temporal único
        $tempDir = 'temp/imports/' . uniqid();
        $tempPath = storage_path('app/' . $tempDir);
        
        \Log::info('Iniciando importación', [
            'fileName' => $fileName,
            'tempDir' => $tempDir,
            'tempPath' => $tempPath
        ]);

        DB::beginTransaction();
        try {
            Storage::makeDirectory($tempDir);
            \Log::info('Directorio temporal creado');

            // Extraer ZIP
            $zip = new \ZipArchive;
            $zipResult = $zip->open($zipFile->getRealPath());
            
            \Log::info('Intentando abrir ZIP', [
                'zipResult' => $zipResult,
                'zipPath' => $zipFile->getRealPath()
            ]);

            if ($zipResult !== true) {
                throw new \Exception('No se pudo abrir el archivo ZIP. Código: ' . $zipResult);
            }

            // Listar contenido del ZIP antes de extraer
            $zipContents = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $zipContents[] = $zip->getNameIndex($i);
            }
            \Log::info('Contenido del ZIP:', $zipContents);

            $zip->extractTo($tempPath);
            $zip->close();
            \Log::info('ZIP extraído exitosamente');

            // Listar archivos en el directorio temporal
            $directFiles = array_diff(scandir($tempPath), ['.', '..']);
            \Log::info('Archivos extraídos:', $directFiles);

            // Búsqueda del archivo Excel
            $excelFile = null;

            // 1. Búsqueda directa en la raíz
            foreach ($directFiles as $file) {
                if (preg_match('/\.xlsx?$/i', $file)) {
                    $excelFile = $tempDir . '/' . $file;
                    break;
                }
            }

            \Log::info('Búsqueda directa de Excel:', [
                'tempPath' => $tempPath,
                'directFiles' => $directFiles,
                'excelFile' => $excelFile
            ]);

            // 2. Búsqueda con Storage::files si no se encontró
            if (!$excelFile) {
                $storageFiles = Storage::files($tempDir);
                foreach ($storageFiles as $file) {
                    if (preg_match('/\.xlsx?$/i', $file)) {
                        $excelFile = $file;
                        break;
                    }
                }
                
                \Log::info('Búsqueda con Storage::files:', [
                    'storageFiles' => $storageFiles,
                    'excelFile' => $excelFile
                ]);
            }

            // 3. Búsqueda recursiva si aún no se encuentra
            if (!$excelFile) {
                $allFiles = Storage::allFiles($tempDir);
                foreach ($allFiles as $file) {
                    if (preg_match('/\.xlsx?$/i', $file)) {
                        $excelFile = $file;
                        break;
                    }
                }
                
                \Log::info('Búsqueda recursiva:', [
                    'allFiles' => $allFiles,
                    'excelFile' => $excelFile
                ]);
            }

            if (!$excelFile) {
                \Log::error('No se encontró el archivo Excel:', [
                    'directFiles' => $directFiles,
                    'storageFiles' => Storage::files($tempDir),
                    'allFiles' => Storage::allFiles($tempDir)
                ]);
                throw new \Exception('No se encontró el archivo Excel en el ZIP');
            }

            // Verificar existencia física del archivo
            $fullPath = storage_path('app/' . $excelFile);
            if (!file_exists($fullPath)) {
                \Log::error('El archivo Excel no existe en la ruta:', [
                    'excelFile' => $excelFile,
                    'fullPath' => $fullPath
                ]);
                throw new \Exception('El archivo Excel no se puede acceder en: ' . $fullPath);
            }

            \Log::info('Archivo Excel encontrado:', [
                'path' => $fullPath,
                'size' => filesize($fullPath)
            ]);

            // Importar datos
            $import = new InventariosImport();
            $result = $import->import($fullPath, $tempPath);
            
            \Log::info('Importación completada', [
                'result' => $result
            ]);

            // Crear registro de la importación
            $importLog = ImportLog::create([
                'user_id' => auth()->id(),
                'file_name' => $fileName,
                'records_imported' => $result['records_imported'] ?? 0,
                'details' => $result['details'] ?? [],
                'status' => 'success',
                'imported_ids' => $result['imported_ids'] ?? [],
                'files_processed' => $result['files_processed'] ?? []
            ]);

            DB::commit();

            // Limpiar directorio temporal
            Storage::deleteDirectory($tempDir);
            \Log::info('Directorio temporal limpiado');

            return back()->with('success', $this->utf8Message('Importación completada exitosamente'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error en importación: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Intentar limpiar el directorio temporal
            try {
                Storage::deleteDirectory($tempDir);
                \Log::info('Directorio temporal limpiado después de error');
            } catch (\Exception $cleanupError) {
                \Log::error('Error al limpiar directorio temporal: ' . $cleanupError->getMessage());
            }
            
            ImportLog::create([
                'user_id' => auth()->id(),
                'file_name' => $fileName,
                'records_imported' => 0,
                'details' => [$this->utf8Message($e->getMessage())],
                'status' => 'error'
            ]);
            
            return back()->with('error', $this->utf8Message('Error en la importación: ') . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        if (!in_array(auth()->user()->role->name, ['administrador', 'almacenista'])) {
            abort(403, $this->utf8Message('No tienes permisos para descargar la plantilla.'));
        }

        $filePath = public_path('templates/inventario_template.xlsx');
        
        if (!file_exists($filePath)) {
            return back()->with('error', $this->utf8Message('La plantilla no está disponible.'));
        }
        
        return response()->download(
            $filePath, 
            'plantilla_inventario.xlsx', 
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="plantilla_inventario.xlsx"'
            ]
        );
    }

    public function destroy(ImportLog $log)
{
    if (auth()->user()->role->name !== 'administrador') {
        abort(403, $this->utf8Message('No tienes permisos para realizar esta acción.'));
    }

    if ($log->status !== 'reverted') {
        return back()->with('error', $this->utf8Message('Solo se pueden eliminar registros de importaciones que hayan sido revertidas'));
    }

    try {
        $log->delete();
        return back()->with('success', $this->utf8Message('Registro de importación eliminado correctamente'));
    } catch (\Exception $e) {
        \Log::error('Error al eliminar log de importación: ' . $e->getMessage(), [
            'exception' => $e,
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', $this->utf8Message('Error al eliminar el registro: ') . $e->getMessage());
    }
}

public function revert(ImportLog $log)
{
    if (auth()->user()->role->name !== 'administrador') {
        abort(403, $this->utf8Message('No tienes permisos para realizar esta acción.'));
    }

    if ($log->status !== 'success') {
        return back()->with('error', $this->utf8Message('Solo se pueden revertir importaciones exitosas'));
    }

    if ($log->status === 'reverted') {
        return back()->with('error', $this->utf8Message('Esta importación ya ha sido revertida'));
    }

    DB::beginTransaction();
    try {
        $importedIds = $log->imported_ids ?? [];
        
        if (empty($importedIds)) {
            throw new \Exception('No se encontraron registros para revertir');
        }

        // Obtener inventarios antes de eliminarlos
        $inventarios = Inventario::whereIn('id', $importedIds)->get();

        // Array para almacenar los hashes de documentos e imágenes procesados
        $processedHashes = [];
        $deletedRecords = 0;
        
        foreach ($inventarios as $inventario) {
            try {
                // Procesar imágenes físicas con verificación de referencias
                $mediasImagenes = DB::table('media')
                    ->where('model_type', get_class($inventario))
                    ->where('model_id', $inventario->id)
                    ->where('collection_name', 'imagenes')
                    ->get();

                foreach ($mediasImagenes as $media) {
                    $imageHash = json_decode($media->custom_properties)->hash ?? null;
                    if ($imageHash) {
                        $imageReferences = DB::table('media')
                            ->where('collection_name', 'imagenes')
                            ->where('custom_properties->hash', $imageHash)
                            ->count();

                        if ($imageReferences <= 1 && !in_array($imageHash, $processedHashes)) {
                            if ($media->file_name && Storage::disk('public')->exists('inventario_imagenes/' . $media->file_name)) {
                                Storage::disk('public')->delete('inventario_imagenes/' . $media->file_name);
                                $processedHashes[] = $imageHash;
                            }
                        }
                    }
                }

                // Procesar documentos
                $documentos = DB::table('documentos')
                    ->where('inventario_id', $inventario->id)
                    ->get();

                foreach ($documentos as $documento) {
                    if ($documento->hash) {
                        $references = DB::table('documentos')
                            ->where('hash', $documento->hash)
                            ->count();

                        if ($references <= 1 && !in_array($documento->hash, $processedHashes)) {
                            if ($documento->ruta && Storage::disk('public')->exists($documento->ruta)) {
                                Storage::disk('public')->delete($documento->ruta);
                                $processedHashes[] = $documento->hash;
                            }
                        }
                    } else {
                        if ($documento->ruta && Storage::disk('public')->exists($documento->ruta)) {
                            Storage::disk('public')->delete($documento->ruta);
                        }
                    }
                }

                // Eliminar registros en orden específico para mantener integridad referencial
                DB::table('documentos')
                    ->where('inventario_id', $inventario->id)
                    ->delete();

                DB::table('media')
                    ->where('model_type', get_class($inventario))
                    ->where('model_id', $inventario->id)
                    ->delete();

                DB::table('inventario_ubicaciones')
                    ->where('inventario_id', $inventario->id)
                    ->delete();

                // Eliminar el registro del inventario
                DB::table('inventarios')
                    ->where('id', $inventario->id)
                    ->delete();

                $deletedRecords++;

            } catch (\Exception $e) {
                \Log::error('Error al revertir inventario ID ' . $inventario->id . ': ' . $e->getMessage());
                // No hacemos continue aquí para que el error se propague y se haga rollback
                throw $e;
            }
        }

        // Actualizar el log de importación
        $log->update([
            'status' => 'reverted',
            'details' => [
                'original_details' => $log->details,
                'revert_info' => [
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                    'deleted_records' => $deletedRecords,
                    'deleted_files' => count($processedHashes),
                    'message' => 'Se eliminaron los registros y archivos asociados'
                ]
            ]
        ]);

        DB::commit();
        
        // Limpiar carpetas vacías
        $this->cleanEmptyDirectories([
            storage_path('app/public/inventario_imagenes'),
            storage_path('app/public/documentos')
        ]);

        return back()->with('success', $this->utf8Message('Importación revertida correctamente. Se eliminaron ' . $deletedRecords . ' registros.'));
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error en revert: ' . $e->getMessage(), [
            'exception' => $e,
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', $this->utf8Message('Error al revertir la importación: ') . $e->getMessage());
    }
}

/**
 * Limpia directorios vacíos
 * @param array $directories
 */
private function cleanEmptyDirectories(array $directories)
{
    foreach ($directories as $directory) {
        if (is_dir($directory)) {
            $files = array_diff(scandir($directory), ['.', '..']);
            if (empty($files)) {
                rmdir($directory);
            }
        }
    }
}
    public function __construct()
{
    // Limpiar carpetas temporales antiguas
    $this->cleanOldTempFolders();
}

private function cleanOldTempFolders()
{
    $tempPath = storage_path('app/temp/imports');
    if (file_exists($tempPath)) {
        $files = glob($tempPath . '/*');
        $now = time();
        
        foreach ($files as $file) {
            if (is_dir($file) && ($now - filemtime($file) > 3600)) { // más de 1 hora
                $this->deleteDirectory($file);
            }
        }
    }
}

private function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return;
    }
    
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
    }
    return rmdir($dir);
}
}