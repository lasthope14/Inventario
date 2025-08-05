<?php

namespace App\Imports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Inventario;
use App\Models\Categoria;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventariosImport
{
    protected $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    protected $allowedDocumentExtensions = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 
        'jpg', 'jpeg', 'png', 'txt', 'csv',
        'zip', 'rar', 'ppt', 'pptx'
    ];

    public function import($filePath, $filesPath)
    {
        // Asegurar que existan los directorios necesarios
        $this->ensureDirectoriesExist();
        
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        $headers = array_map('strtolower', array_map('trim', array_shift($rows)));
        
        $importedRows = 0;
        $details = [];
        $importedIds = [];
        $filesProcessed = [];
        
        foreach ($rows as $rowIndex => $row) {
            if (empty(array_filter($row))) {
                continue;
            }
            
            $data = array_combine($headers, $row);

            // Registro del valor unitario antes de procesamiento
            \Log::info('Valor unitario antes de procesamiento:', [
                'valor' => $data['valor_unitario'] ?? 'no set',
                'tipo' => isset($data['valor_unitario']) ? gettype($data['valor_unitario']) : 'undefined',
                'raw_valor' => $row[array_search('valor_unitario', $headers)] ?? 'no encontrado'
            ]);

            // Pre-procesar valor_unitario
            if (isset($data['valor_unitario'])) {
                if (is_numeric($data['valor_unitario'])) {
                    $data['valor_unitario'] = (float) $data['valor_unitario'];
                } else {
                    // Limpiar el valor de caracteres no numéricos
                    $cleanValue = preg_replace('/[^0-9.]/', '', $data['valor_unitario']);
                    $data['valor_unitario'] = (float) $cleanValue;
                }
                
                // Registro después del procesamiento
                \Log::info('Valor unitario después de procesamiento:', [
                    'valor' => $data['valor_unitario'],
                    'tipo' => gettype($data['valor_unitario'])
                ]);
            }
            
            try {
                $this->validateRow($data);
                
                $categoria = Categoria::findOrFail($data['categoria_id']);
                
                $inventario = new Inventario([
                    'categoria_id' => $data['categoria_id'],
                    'nombre' => $data['nombre'],
                    'propietario' => $data['propietario'],
                    'proveedor_id' => $data['proveedor_id'],
                    'estado' => strtolower($data['estado']),
                    'cantidadTotal' => $data['cantidad'],
                    'modelo' => $data['modelo'] ?? null,
                    'numero_serie' => $data['numero_serie'] ?? null,
                    'marca' => $data['marca'] ?? null,
                    'fecha_compra' => $this->parseDate($data['fecha_compra'] ?? null),
                    'numero_factura' => $data['numero_factura'] ?? null,
                    'valor_unitario' => $data['valor_unitario'] ?? null,
                    'fecha_baja' => $this->parseDate($data['fecha_baja'] ?? null),
                    'fecha_inspeccion' => $this->parseDate($data['fecha_inspeccion'] ?? null),
                    'observaciones' => $data['observaciones'] ?? null,
                ]);

                $inventario->codigo_unico = Inventario::generarCodigoUnico($categoria, $data['nombre']);
                $inventario->save();
                
                // Procesar imágenes
                $imagesProcesadas = $this->processImages($inventario, $data, $filesPath);
                $filesProcessed = array_merge($filesProcessed, $imagesProcesadas);

                // Procesar documentos
                $documentosProcesados = $this->processDocuments($inventario, $data, $filesPath);
                $filesProcessed = array_merge($filesProcessed, $documentosProcesados);

                $inventario->ubicaciones()->create([
                    'ubicacion_id' => $data['ubicacion_id'],
                    'cantidad' => $data['cantidad'],
                    'estado' => strtolower($data['estado'])
                ]);

                $importedIds[] = $inventario->id;
                $importedRows++;
                
                $fileDetails = [];
                if (!empty($imagesProcesadas)) {
                    $fileDetails[] = "Imágenes procesadas: " . implode(', ', $imagesProcesadas);
                }
                if (!empty($documentosProcesados)) {
                    $fileDetails[] = "Documentos procesados: " . implode(', ', $documentosProcesados);
                }

                $details[] = [
                    "row" => $rowIndex + 2,
                    "status" => "success",
                    "message" => "Importado exitosamente - {$data['nombre']} (ID: {$inventario->id})",
                    "files" => $fileDetails
                ];

            } catch (\Exception $e) {
                \Log::error('Error en importación de fila ' . ($rowIndex + 2) . ': ' . $e->getMessage());
                throw new \Exception("Error en la fila " . ($rowIndex + 2) . ": " . $e->getMessage());
            }
        }

        return [
            'records_imported' => $importedRows,
            'details' => $details,
            'imported_ids' => $importedIds,
            'files_processed' => $filesProcessed
        ];
    }
    
    protected function validateRow($data)
    {
        // Preprocesamiento adicional del valor unitario
        if (isset($data['valor_unitario'])) {
            if (!is_numeric($data['valor_unitario'])) {
                // Remover cualquier caracter no numérico excepto punto y coma
                $cleanValue = preg_replace('/[^0-9.,]/', '', $data['valor_unitario']);
                // Reemplazar coma por punto para asegurar formato decimal correcto
                $cleanValue = str_replace(',', '.', $cleanValue);
                // Convertir a float
                $data['valor_unitario'] = (float) $cleanValue;
            } else {
                $data['valor_unitario'] = (float) $data['valor_unitario'];
            }
            
            \Log::info('Valor unitario en validateRow:', [
                'original' => $data['valor_unitario'],
                'processed' => $data['valor_unitario'],
                'type' => gettype($data['valor_unitario'])
            ]);
        }

        $validator = Validator::make($data, [
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string',
            'propietario' => 'required|string',
            'proveedor_id' => 'required|exists:proveedores,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'cantidad' => 'required|integer|min:1',
            'estado' => 'required|in:disponible,en uso,en mantenimiento,dado de baja,robado',
            'modelo' => 'nullable|string',
            'numero_serie' => 'nullable|string',
            'marca' => 'nullable|string',
            'fecha_compra' => 'nullable',
            'numero_factura' => 'nullable|string',
            'valor_unitario' => 'nullable|numeric',
            'fecha_baja' => 'nullable',
            'fecha_inspeccion' => 'nullable',
            'observaciones' => 'nullable|string',
            'imagen_1' => 'nullable|string',
            'imagen_2' => 'nullable|string',
            'documento' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            \Log::error('Error de validación:', [
                'data' => $data,
                'errors' => $validator->errors()->all()
            ]);
            throw new \Exception("Error de validación: " . implode(", ", $validator->errors()->all()));
        }

        return $data;
    }

    protected function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            if (is_numeric($date)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date));
            }
            
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function ensureDirectoriesExist()
    {
        $baseStoragePath = storage_path('app/public');
        $directories = [
            $baseStoragePath . '/inventario_imagenes',
            $baseStoragePath . '/documentos'
        ];

        foreach ($directories as $directory) {
            try {
                if (!file_exists($directory)) {
                    if (mkdir($directory, 0755, true)) {
                        \Log::info("Directorio creado: $directory");
                    } else {
                        \Log::error("No se pudo crear el directorio: $directory");
                    }
                }
                
                \Log::info("Verificación de directorio", [
                    'directorio' => $directory,
                    'existe' => file_exists($directory),
                    'permisos' => substr(sprintf('%o', fileperms($directory)), -4),
                    'escribible' => is_writable($directory)
                ]);
                
            } catch (\Exception $e) {
                \Log::error("Error al crear directorio", [
                    'directorio' => $directory,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        }
    }

    protected function validateFileExtension($fileName, $allowedExtensions)
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        return in_array($extension, $allowedExtensions);
    }
    
    protected function processImages($inventario, $data, $filesPath)
    {
        $processedFiles = [];
        
        \Log::info('Verificando directorios para imágenes', [
            'public_path' => storage_path('app/public/inventario_imagenes'),
            'public_exists' => is_dir(storage_path('app/public/inventario_imagenes')),
            'public_writable' => is_writable(storage_path('app/public/inventario_imagenes')),
            'disk_exists' => Storage::disk('public')->exists('inventario_imagenes'),
            'files_path' => $filesPath,
            'files_path_exists' => is_dir($filesPath)
        ]);
        
        // Procesar imagen 1
        if (!empty($data['imagen_1'])) {
            \Log::info('Intentando procesar imagen 1', [
                'nombre_imagen' => $data['imagen_1']
            ]);

            $imagePath = $this->findFile($filesPath, $data['imagen_1'], $this->allowedImageExtensions);
            if ($imagePath) {
                try {
                    $imageHash = hash_file('md5', $imagePath);
                    \Log::info('Imagen 1 encontrada', [
                        'path' => $imagePath,
                        'hash' => $imageHash,
                        'size' => filesize($imagePath),
                        'permisos' => substr(sprintf('%o', fileperms($imagePath)), -4)
                    ]);
                    
                    $existingImage = DB::table('media')
                        ->where('collection_name', 'imagenes')
                        ->where('custom_properties->hash', $imageHash)
                        ->first();

                    if ($existingImage) {
                        \Log::info('Imagen existente encontrada, verificando archivo físico', [
                            'existing_path' => $existingImage->file_name,
                            'full_path' => storage_path('app/public/inventario_imagenes/' . $existingImage->file_name)
                        ]);

                        $physicalPath = storage_path('app/public/inventario_imagenes/' . $existingImage->file_name);
                        if (!file_exists($physicalPath)) {
                            $fileName = $imageHash . '.' . pathinfo($data['imagen_1'], PATHINFO_EXTENSION);
                            $destinationPath = 'inventario_imagenes/' . $fileName;
                            $fullDestinationPath = storage_path('app/public/' . $destinationPath);
                            
                            \Log::info('Archivo físico no encontrado, copiando nueva imagen', [
                                'origen' => $imagePath,
                                'destino' => $fullDestinationPath
                            ]);

                            if (copy($imagePath, $fullDestinationPath)) {
                                chmod($fullDestinationPath, 0644);
                                \Log::info('Imagen copiada exitosamente');
                            } else {
                                \Log::error('Error al copiar imagen', [
                                    'error' => error_get_last()
                                ]);
                                throw new \Exception("No se pudo copiar la imagen al destino");
                            }
                        }

                        $destinationPath = 'inventario_imagenes/' . $existingImage->file_name;
                        $inventario->imagen_principal = $destinationPath;
                        $inventario->save();
                    } else {
                        $fileName = $imageHash . '.' . pathinfo($data['imagen_1'], PATHINFO_EXTENSION);
                        $destinationPath = 'inventario_imagenes/' . $fileName;
                        $fullDestinationPath = storage_path('app/public/' . $destinationPath);
                        
                        \Log::info('Procesando nueva imagen', [
                            'origen' => $imagePath,
                            'destino' => $fullDestinationPath
                        ]);

                        if (copy($imagePath, $fullDestinationPath)) {
                            chmod($fullDestinationPath, 0644);
                            \Log::info('Nueva imagen copiada exitosamente');
                            
                            $inventario->imagen_principal = $destinationPath;
                            $inventario->save();

                            DB::table('media')->insert([
                                'model_type' => get_class($inventario),
                                'model_id' => $inventario->id,
                                'uuid' => (string) Str::uuid(),
                                'collection_name' => 'imagenes',
                                'name' => $inventario->codigo_unico . '_1',
                                'file_name' => $fileName,
                                'mime_type' => mime_content_type($imagePath),
                                'disk' => 'public',
                                'size' => filesize($imagePath),
                                'manipulations' => '[]',
                                'custom_properties' => json_encode(['hash' => $imageHash]),
                                'generated_conversions' => '[]',
                                'responsive_images' => '[]',
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        } else {
                            \Log::error('Error al copiar nueva imagen', [
                                'error' => error_get_last()
                            ]);
                            throw new \Exception("No se pudo copiar la nueva imagen al destino");
                        }
                    }
                    
                    $processedFiles[] = $data['imagen_1'];

                } catch (\Exception $e) {
                    \Log::error('Error procesando imagen 1', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }
        }

        // Procesar imagen 2
        if (!empty($data['imagen_2'])) {
            \Log::info('Intentando procesar imagen 2', [
                'nombre_imagen' => $data['imagen_2']
            ]);

            $imagePath = $this->findFile($filesPath, $data['imagen_2'], $this->allowedImageExtensions);
            if ($imagePath) {
                try {
                    $imageHash = hash_file('md5', $imagePath);
                    \Log::info('Imagen 2 encontrada', [
                        'path' => $imagePath,
                        'hash' => $imageHash,
                        'size' => filesize($imagePath),
                        'permisos' => substr(sprintf('%o', fileperms($imagePath)), -4)
                    ]);
                    
                    $existingImage = DB::table('media')
                        ->where('collection_name', 'imagenes')
                        ->where('custom_properties->hash', $imageHash)
                        ->first();

                    if ($existingImage) {
                        \Log::info('Imagen existente encontrada, verificando archivo físico', [
                            'existing_path' => $existingImage->file_name,
                            'full_path' => storage_path('app/public/inventario_imagenes/' . $existingImage->file_name)
                        ]);

                        $physicalPath = storage_path('app/public/inventario_imagenes/' . $existingImage->file_name);
                        if (!file_exists($physicalPath)) {
                            $fileName = $imageHash . '.' . pathinfo($data['imagen_2'], PATHINFO_EXTENSION);
                            $destinationPath = 'inventario_imagenes/' . $fileName;
                            $fullDestinationPath = storage_path('app/public/' . $destinationPath);
                            
                            \Log::info('Archivo físico no encontrado, copiando nueva imagen', [
                                'origen' => $imagePath,
                                'destino' => $fullDestinationPath
                            ]);

                            if (copy($imagePath, $fullDestinationPath)) {
                                chmod($fullDestinationPath, 0644);
                                \Log::info('Imagen copiada exitosamente');
                            } else {
                                \Log::error('Error al copiar imagen', [
                                    'error' => error_get_last()
                                ]);
                                throw new \Exception("No se pudo copiar la imagen al destino");
                            }
                        }

                        $destinationPath = 'inventario_imagenes/' . $existingImage->file_name;
                        $inventario->imagen_secundaria = $destinationPath;
                        $inventario->save();
                    } else {
                        $fileName = $imageHash . '.' . pathinfo($data['imagen_2'], PATHINFO_EXTENSION);
                        $destinationPath = 'inventario_imagenes/' . $fileName;
                        $fullDestinationPath = storage_path('app/public/' . $destinationPath);
                        
                        \Log::info('Procesando nueva imagen', [
                            'origen' => $imagePath,
                            'destino' => $fullDestinationPath
                        ]);

                        if (copy($imagePath, $fullDestinationPath)) {
                            chmod($fullDestinationPath, 0644);
                            \Log::info('Nueva imagen copiada exitosamente');
                            
                            $inventario->imagen_secundaria = $destinationPath;
                            $inventario->save();

                            DB::table('media')->insert([
                                'model_type' => get_class($inventario),
                                'model_id' => $inventario->id,
                                'uuid' => (string) Str::uuid(),
                                'collection_name' => 'imagenes',
                                'name' => $inventario->codigo_unico . '_2',
                                'file_name' => $fileName,
                                'mime_type' => mime_content_type($imagePath),
                                'disk' => 'public',
                                'size' => filesize($imagePath),
                                'manipulations' => '[]',
                                'custom_properties' => json_encode(['hash' => $imageHash]),
                                'generated_conversions' => '[]',
                                'responsive_images' => '[]',
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        } else {
                            \Log::error('Error al copiar nueva imagen', [
                                'error' => error_get_last()
                            ]);
                            throw new \Exception("No se pudo copiar la nueva imagen al destino");
                        }
                    }
                    
                    $processedFiles[] = $data['imagen_2'];

                } catch (\Exception $e) {
                    \Log::error('Error procesando imagen 2', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }
        }

        return $processedFiles;
    }

    protected function processDocuments($inventario, $data, $filesPath)
    {
        $processedFiles = [];

        if (!empty($data['documento'])) {
            $documentos = array_map('trim', explode(',', $data['documento']));
            
            foreach ($documentos as $documento) {
                if (empty($documento)) {
                    continue;
                }

                \Log::info('Procesando documento individual:', ['documento' => $documento]);

                $documentPath = $this->findFile($filesPath, $documento, $this->allowedDocumentExtensions);
                if ($documentPath) {
                    $fileHash = hash_file('md5', $documentPath);
                    
                    $existingDocument = DB::table('documentos')
                        ->where('hash', $fileHash)
                        ->first();

                    if ($existingDocument) {
                        DB::table('documentos')->insert([
                            'inventario_id' => $inventario->id,
                            'nombre' => pathinfo($documento, PATHINFO_FILENAME),
                            'ruta' => $existingDocument->ruta,
                            'hash' => $fileHash,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        $processedFiles[] = $documento;
                        continue;
                    }

                    $fileName = $this->generateUniqueFileName($documento, $fileHash);
                    $destinationPath = 'documentos/' . $fileName;
                    $fullDestinationPath = storage_path('app/public/' . $destinationPath);
                    
                    try {
                        if (!Storage::disk('public')->exists($destinationPath)) {
                            copy($documentPath, $fullDestinationPath);
                        }
                        
                        DB::table('media')->insert([
                            'model_type' => get_class($inventario),
                            'model_id' => $inventario->id,
                            'uuid' => (string) Str::uuid(),
                            'collection_name' => 'documentos',
                            'name' => $inventario->codigo_unico . '_doc',
                            'file_name' => $fileName,
                            'mime_type' => mime_content_type($documentPath),
                            'disk' => 'public',
                            'size' => filesize($documentPath),
                            'manipulations' => '[]',
                            'custom_properties' => json_encode(['hash' => $fileHash]),
                            'generated_conversions' => '[]',
                            'responsive_images' => '[]',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        DB::table('documentos')->insert([
                            'inventario_id' => $inventario->id,
                            'nombre' => pathinfo($documento, PATHINFO_FILENAME),
                            'ruta' => $destinationPath,
                            'hash' => $fileHash,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        $processedFiles[] = $documento;
                        
                    } catch (\Exception $e) {
                        \Log::error('Error procesando documento: ' . $documento, [
                            'error' => $e->getMessage()
                        ]);
                        continue;
                    }
                }
            }
        }

        return $processedFiles;
    }

    protected function generateUniqueFileName($originalName, $hash)
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return $hash . '.' . $extension;
    }

    protected function findFile($basePath, $fileName, $allowedExtensions)
    {
        $fileName = trim($fileName);
        
        \Log::info('Buscando archivo:', [
            'fileName' => $fileName,
            'basePath' => $basePath
        ]);

        $directories = ['documentos', 'imagenes', ''];
        foreach ($directories as $dir) {
            $searchPath = $dir ? $basePath . '/' . $dir : $basePath;
            
            $filePath = $searchPath . '/' . $fileName;
            if (file_exists($filePath) && $this->validateFileExtension($fileName, $allowedExtensions)) {
                \Log::info('Archivo encontrado:', ['path' => $filePath]);
                return $filePath;
            }

            $files = glob($searchPath . '/*');
            foreach ($files as $file) {
                if (strtolower(basename($file)) === strtolower($fileName)) {
                    if ($this->validateFileExtension($file, $allowedExtensions)) {
                        \Log::info('Archivo encontrado (case-insensitive):', ['path' => $file]);
                        return $file;
                    }
                }
            }
        }

        \Log::warning('Archivo no encontrado:', ['fileName' => $fileName]);
        return null;
    }
}
