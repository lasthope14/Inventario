<?php

namespace App\Imports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Inventario;
use App\Models\Documento;
use Illuminate\Support\Facades\Storage;

class DocumentosDuplicadosImport
{
    protected $filesPath;
    protected $results = [];

    public function __construct($filesPath)
    {
        $this->filesPath = $filesPath;
        \Log::info('DocumentosDuplicadosImport constructor llamado', [
            'files_path' => $filesPath,
            'files_path_exists' => is_dir($filesPath),
            'class' => __CLASS__
        ]);
    }

    public function import($filePath)
    {
        \Log::info('=== INICIANDO IMPORTACIÓN DE DOCUMENTOS ===', [
            'excel_path' => $filePath,
            'files_path' => $this->filesPath,
            'excel_exists' => file_exists($filePath),
            'files_dir_exists' => is_dir($this->filesPath)
        ]);
        
        try {
            // Verificar que el directorio de archivos existe
            if (!is_dir($this->filesPath)) {
                throw new \Exception("Directorio de archivos no encontrado: {$this->filesPath}");
            }
            
            // Listar archivos disponibles para debug
            $availableFiles = [];
            if (is_dir($this->filesPath)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($this->filesPath, \RecursiveDirectoryIterator::SKIP_DOTS)
                );
                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $availableFiles[] = $file->getFilename();
                    }
                }
            }
            
            \Log::info('Archivos disponibles en ZIP extraído', [
                'total_files' => count($availableFiles),
                'files' => $availableFiles
            ]);
            
            // Cargar el archivo Excel
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            \Log::info('Excel cargado exitosamente', [
                'total_rows' => count($rows),
                'first_row' => $rows[0] ?? 'vacío'
            ]);
            
            // Remover encabezados
            $headers = array_shift($rows);
            
            \Log::info('Encabezados del Excel', [
                'headers' => $headers
            ]);
            
            // Procesar cada fila
            $processedRows = 0;
            $totalRows = count($rows);
            
            foreach ($rows as $rowIndex => $row) {
                // Heartbeat cada 10 filas
                if ($processedRows % 10 === 0) {
                    \Log::info("Heartbeat: Procesando fila " . ($rowIndex + 2) . " de " . ($totalRows + 1), [
                        'progreso' => round(($processedRows / $totalRows) * 100, 2) . '%',
                        'filas_procesadas' => $processedRows,
                        'filas_totales' => $totalRows,
                        'memoria_usada' => memory_get_usage(true),
                        'memoria_pico' => memory_get_peak_usage(true)
                    ]);
                }
                
                // Saltar filas vacías
                if (empty(array_filter($row))) {
                    \Log::info("Saltando fila vacía: " . ($rowIndex + 2));
                    continue;
                }
                
                \Log::info("Procesando fila " . ($rowIndex + 2), [
                    'row_data' => $row
                ]);
                
                $this->processRow($row, $rowIndex + 2); // +2 porque removimos headers y empezamos en 1
                $processedRows++;
            }
            
            \Log::info('=== IMPORTACIÓN COMPLETADA ===', [
                'processed_rows' => $processedRows,
                'total_results' => count($this->results),
                'results_summary' => array_count_values(array_column($this->results, 'status'))
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error crítico en importación', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->results[] = [
                'status' => 'error',
                'message' => 'Error leyendo archivo Excel: ' . $e->getMessage()
            ];
        }
        
        return $this->results;
    }

    private function processRow($row, $rowNumber)
    {
        try {
            // Mapear columnas (basado en el orden del template actualizado)
            $codigoElemento = trim($row[0] ?? '');
            $serial = trim($row[1] ?? '');
            $nombreElemento = trim($row[2] ?? '');
            $categoria = trim($row[3] ?? '');
            $nombreDocumento = trim($row[4] ?? '');
            $archivoActual = trim($row[5] ?? '');
            $archivoDocumento = trim($row[6] ?? '');
            $observaciones = trim($row[7] ?? '');
            
            \Log::info("Fila {$rowNumber} - Datos extraídos", [
                'codigo_elemento' => $codigoElemento,
                'serial' => $serial,
                'nombre_elemento' => $nombreElemento,
                'categoria' => $categoria,
                'nombre_documento' => $nombreDocumento,
                'archivo_actual' => $archivoActual,
                'archivo_documento' => $archivoDocumento,
                'observaciones' => $observaciones
            ]);
            
            // Solo procesar filas que tengan un nuevo archivo especificado
            if (empty($archivoDocumento)) {
                \Log::info("Fila {$rowNumber} - Saltando: No hay archivo de documento especificado");
                return;
            }
            
            // Buscar el elemento por código
            $inventario = Inventario::where('codigo_unico', $codigoElemento)->first();
            
            if (!$inventario) {
                \Log::error("Fila {$rowNumber} - Elemento no encontrado en BD", [
                    'codigo_buscado' => $codigoElemento
                ]);
                
                $this->results[] = [
                    'status' => 'error',
                    'message' => "Fila {$rowNumber}: Elemento no encontrado: {$codigoElemento}"
                ];
                return;
            }
            
            \Log::info("Fila {$rowNumber} - Elemento encontrado", [
                'inventario_id' => $inventario->id,
                'inventario_nombre' => $inventario->nombre
            ]);

            // Buscar el archivo en el directorio de archivos
            \Log::info("Fila {$rowNumber} - Buscando archivo", [
                'archivo_buscado' => $archivoDocumento,
                'directorio_busqueda' => $this->filesPath
            ]);
            
            $archivoEncontrado = $this->findFile($this->filesPath, $archivoDocumento);
            
            if (!$archivoEncontrado) {
                \Log::error("Fila {$rowNumber} - Archivo no encontrado", [
                    'archivo_buscado' => $archivoDocumento,
                    'directorio_busqueda' => $this->filesPath
                ]);
                
                $this->results[] = [
                    'status' => 'error',
                    'message' => "Fila {$rowNumber}: Archivo no encontrado: {$archivoDocumento} para elemento {$codigoElemento}"
                ];
                return;
            }
            
            \Log::info("Fila {$rowNumber} - Archivo encontrado", [
                'ruta_archivo' => $archivoEncontrado,
                'tamaño' => file_exists($archivoEncontrado) ? filesize($archivoEncontrado) : 'N/A'
            ]);

            // Generar nombre único para el archivo
            $extension = pathinfo($archivoEncontrado, PATHINFO_EXTENSION);
            $nuevoNombre = $inventario->codigo_unico . '_' . $nombreDocumento . '.' . $extension;

            // Copiar archivo a la ubicación final
            $rutaDestino = 'documentos/' . $nuevoNombre;
            
            \Log::info("Fila {$rowNumber} - Copiando archivo", [
                'origen' => $archivoEncontrado,
                'destino' => $rutaDestino,
                'nuevo_nombre' => $nuevoNombre
            ]);
            
            $contenidoArchivo = file_get_contents($archivoEncontrado);
            if ($contenidoArchivo === false) {
                throw new \Exception("No se pudo leer el archivo: {$archivoEncontrado}");
            }
            
            if (Storage::disk('public')->put($rutaDestino, $contenidoArchivo)) {
                \Log::info("Fila {$rowNumber} - Archivo copiado exitosamente");
                
                // Buscar si ya existe un documento con este nombre para este elemento
                $documentoExistente = Documento::where('inventario_id', $inventario->id)
                    ->where('nombre', $nombreDocumento)
                    ->first();

                if ($documentoExistente) {
                    \Log::info("Fila {$rowNumber} - Actualizando documento existente", [
                        'documento_id' => $documentoExistente->id,
                        'ruta_anterior' => $documentoExistente->ruta,
                        'ruta_nueva' => $rutaDestino,
                        'fecha_original' => $documentoExistente->created_at
                    ]);
                    
                    // Calcular hash del nuevo archivo
                    $rutaCompletaDestino = storage_path('app/public/' . $rutaDestino);
                    $nuevoHash = hash_file('md5', $rutaCompletaDestino);
                    
                    \Log::info("Fila {$rowNumber} - Hash calculado", [
                        'archivo' => $rutaCompletaDestino,
                        'hash' => $nuevoHash
                    ]);
                    
                    // IMPORTANTE: Conservar la fecha de creación original pero actualizar la ruta y hash
                    $fechaOriginal = $documentoExistente->created_at;
                    $documentoExistente->update([
                        'ruta' => $rutaDestino,
                        'hash' => $nuevoHash,  // ✅ ACTUALIZAR HASH
                        'updated_at' => now(),
                        'created_at' => $fechaOriginal  // Conservar fecha original
                    ]);
                    
                    $this->results[] = [
                        'status' => 'updated',
                        'message' => "Fila {$rowNumber}: Documento actualizado: {$nombreDocumento} para {$inventario->codigo_unico} (Serial: {$serial}) - Fecha original conservada: {$fechaOriginal}"
                    ];
                    
                    \Log::info("Fila {$rowNumber} - Documento actualizado exitosamente");
                } else {
                    \Log::info("Fila {$rowNumber} - Creando nuevo documento");
                    
                    // Calcular hash del nuevo archivo
                    $rutaCompletaDestino = storage_path('app/public/' . $rutaDestino);
                    $nuevoHash = hash_file('md5', $rutaCompletaDestino);
                    
                    // Crear nuevo documento
                    $nuevoDocumento = Documento::create([
                        'inventario_id' => $inventario->id,
                        'nombre' => $nombreDocumento,
                        'ruta' => $rutaDestino,
                        'hash' => $nuevoHash,  // ✅ INCLUIR HASH
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $this->results[] = [
                        'status' => 'created',
                        'message' => "Fila {$rowNumber}: Documento creado: {$nombreDocumento} para {$inventario->codigo_unico} (Serial: {$serial})"
                    ];
                    
                    \Log::info("Fila {$rowNumber} - Documento creado exitosamente", [
                        'documento_id' => $nuevoDocumento->id
                    ]);
                }
            } else {
                \Log::error("Fila {$rowNumber} - Error al guardar archivo en storage");
                
                $this->results[] = [
                    'status' => 'error',
                    'message' => "Fila {$rowNumber}: Error al copiar archivo: {$archivoDocumento} para elemento {$codigoElemento} (Serial: {$serial})"
                ];
            }

        } catch (\Exception $e) {
            \Log::error("Fila {$rowNumber} - Error crítico procesando fila", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'codigo_elemento' => $codigoElemento ?? 'N/A'
            ]);
            
            $this->results[] = [
                'status' => 'error',
                'message' => "Fila {$rowNumber}: Error procesando {$codigoElemento}: " . $e->getMessage()
            ];
        }
    }

    public function getResults()
    {
        return $this->results;
    }

    private function findFile($basePath, $fileName)
    {
        // Buscar archivo exacto
        $fullPath = $basePath . '/' . $fileName;
        if (file_exists($fullPath)) {
            return $fullPath;
        }

        // Buscar recursivamente en subdirectorios
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($basePath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === $fileName) {
                return $file->getPathname();
            }
        }

        return null;
    }
} 