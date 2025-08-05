<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Documento;
use App\Models\Inventario;
use App\Exports\DocumentosDuplicadosExport;
use App\Imports\DocumentosDuplicadosImport;
use ZipArchive;

class DocumentAnalysisController extends Controller
{
    public function index()
    {
        \Log::info('=== MÉTODO INDEX EJECUTÁNDOSE ===', [
            'timestamp' => now(),
            'user_id' => auth()->id(),
            'request_method' => request()->method(),
            'has_session_success' => session()->has('success'),
            'has_session_import_details' => session()->has('import_details')
        ]);

        // Análisis de documentos duplicados REALES (misma ruta física)
        $duplicates = DB::table('documentos')
            ->select('ruta', DB::raw('COUNT(DISTINCT inventario_id) as elementos_afectados'), DB::raw('GROUP_CONCAT(DISTINCT inventario_id) as inventario_ids'), DB::raw('GROUP_CONCAT(DISTINCT nombre) as nombres'))
            ->whereNotNull('ruta')
            ->where('ruta', '!=', '')
            ->groupBy('ruta')
            ->having('elementos_afectados', '>', 1)
            ->orderBy('elementos_afectados', 'desc')
            ->get();

        // Análisis de archivos faltantes
        $documentos = Documento::with('inventario')->get();
        $missing = [];
        $found = 0;

        foreach ($documentos as $documento) {
            if (!Storage::disk('public')->exists($documento->ruta)) {
                $missing[] = [
                    'id' => $documento->id,
                    'nombre' => $documento->nombre,
                    'inventario_codigo' => $documento->inventario->codigo_unico,
                    'inventario_nombre' => $documento->inventario->nombre,
                    'ruta' => $documento->ruta,
                    'fecha_subida' => $documento->created_at->format('d/m/Y H:i')
                ];
            } else {
                $found++;
            }
        }

        // 🖼️ NUEVO: Análisis de imágenes duplicadas
        $inventarios = Inventario::whereNotNull('imagen_principal')
            ->orWhereNotNull('imagen_secundaria')
            ->get();
        
        $imagesMissing = [];
        $imagesFound = 0;
        $totalImages = 0;
        $imagesDuplicateNames = [];
        $imagesDuplicateSize = [];
        
        // Arrays para detectar duplicados
        $imagesByName = [];
        $imagesBySize = [];

        foreach ($inventarios as $inventario) {
            // Verificar imagen principal
            if ($inventario->imagen_principal) {
                $totalImages++;
                $fullPath = storage_path('app/public/' . $inventario->imagen_principal);
                
                if (!Storage::disk('public')->exists($inventario->imagen_principal)) {
                    $imagesMissing[] = [
                        'tipo' => 'Principal',
                        'inventario_codigo' => $inventario->codigo_unico,
                        'inventario_nombre' => $inventario->nombre,
                        'ruta' => $inventario->imagen_principal,
                        'fecha_subida' => $inventario->created_at->format('d/m/Y H:i')
                    ];
                } else {
                    $imagesFound++;
                    
                    $fileName = basename($inventario->imagen_principal);
                    $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                    
                    // Detectar duplicados por nombre
                    if (!isset($imagesByName[$fileName])) {
                        $imagesByName[$fileName] = [];
                    }
                    $imagesByName[$fileName][] = [
                        'tipo' => 'Principal',
                        'inventario_codigo' => $inventario->codigo_unico,
                        'inventario_nombre' => $inventario->nombre,
                        'ruta' => $inventario->imagen_principal,
                        'tamaño' => $fileSize
                    ];
                    
                    // Detectar duplicados por tamaño (solo si el archivo existe y tiene tamaño > 0)
                    if ($fileSize > 0) {
                        if (!isset($imagesBySize[$fileSize])) {
                            $imagesBySize[$fileSize] = [];
                        }
                        $imagesBySize[$fileSize][] = [
                            'tipo' => 'Principal',
                            'inventario_codigo' => $inventario->codigo_unico,
                            'inventario_nombre' => $inventario->nombre,
                            'archivo' => $fileName,
                            'ruta' => $inventario->imagen_principal,
                            'tamaño' => $fileSize
                        ];
                    }
                }
            }

            // Verificar imagen secundaria
            if ($inventario->imagen_secundaria) {
                $totalImages++;
                $fullPath = storage_path('app/public/' . $inventario->imagen_secundaria);
                
                if (!Storage::disk('public')->exists($inventario->imagen_secundaria)) {
                    $imagesMissing[] = [
                        'tipo' => 'Secundaria',
                        'inventario_codigo' => $inventario->codigo_unico,
                        'inventario_nombre' => $inventario->nombre,
                        'ruta' => $inventario->imagen_secundaria,
                        'fecha_subida' => $inventario->created_at->format('d/m/Y H:i')
                    ];
                } else {
                    $imagesFound++;
                    
                    $fileName = basename($inventario->imagen_secundaria);
                    $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                    
                    // Detectar duplicados por nombre
                    if (!isset($imagesByName[$fileName])) {
                        $imagesByName[$fileName] = [];
                    }
                    $imagesByName[$fileName][] = [
                        'tipo' => 'Secundaria',
                        'inventario_codigo' => $inventario->codigo_unico,
                        'inventario_nombre' => $inventario->nombre,
                        'ruta' => $inventario->imagen_secundaria,
                        'tamaño' => $fileSize
                    ];
                    
                    // Detectar duplicados por tamaño (solo si el archivo existe y tiene tamaño > 0)
                    if ($fileSize > 0) {
                        if (!isset($imagesBySize[$fileSize])) {
                            $imagesBySize[$fileSize] = [];
                        }
                        $imagesBySize[$fileSize][] = [
                            'tipo' => 'Secundaria',
                            'inventario_codigo' => $inventario->codigo_unico,
                            'inventario_nombre' => $inventario->nombre,
                            'archivo' => $fileName,
                            'ruta' => $inventario->imagen_secundaria,
                            'tamaño' => $fileSize
                        ];
                    }
                }
            }
        }

        // Procesar duplicados por nombre
        foreach ($imagesByName as $fileName => $images) {
            if (count($images) > 1) {
                $imagesDuplicateNames[] = [
                    'archivo' => $fileName,
                    'elementos_afectados' => count($images),
                    'elementos' => $images
                ];
            }
        }

        // Procesar duplicados por tamaño (posibles mismas imágenes con nombres diferentes)
        foreach ($imagesBySize as $fileSize => $images) {
            if (count($images) > 1) {
                // Verificar que no sean el mismo archivo (misma ruta)
                $uniquePaths = array_unique(array_column($images, 'ruta'));
                if (count($uniquePaths) > 1) {
                    $imagesDuplicateSize[] = [
                        'tamaño' => $fileSize,
                        'tamaño_formateado' => $this->formatBytes($fileSize),
                        'elementos_afectados' => count($images),
                        'elementos' => $images
                    ];
                }
            }
        }

        // Procesar duplicados para la vista
        $duplicatesForView = [];
        foreach ($duplicates as $duplicate) {
            $inventarioIds = explode(',', $duplicate->inventario_ids);
            $inventarios = Inventario::whereIn('id', $inventarioIds)->get();
            $nombres = explode(',', $duplicate->nombres);
            
            $elementosInfo = [];
            foreach ($inventarios as $inventario) {
                $elementosInfo[] = [
                    'codigo' => $inventario->codigo_unico,
                    'nombre' => $inventario->nombre,
                    'id' => $inventario->id
                ];
            }

            $duplicatesForView[] = [
                'ruta' => $duplicate->ruta,
                'nombres' => array_unique($nombres), // Nombres de documentos que usan esta ruta
                'elementos_afectados' => $duplicate->elementos_afectados,
                'elementos' => $elementosInfo
            ];
        }

        $stats = [
            'total_documentos' => $documentos->count(),
            'documentos_con_archivo' => $found,
            'documentos_sin_archivo' => count($missing),
            'rutas_duplicadas' => $duplicates->count(),
            'elementos_afectados_por_duplicados' => $duplicates->sum('elementos_afectados'),
            // 🖼️ NUEVO: Estadísticas de imágenes
            'total_imagenes' => $totalImages,
            'imagenes_con_archivo' => $imagesFound,
            'imagenes_sin_archivo' => count($imagesMissing),
            'imagenes_duplicadas_nombre' => count($imagesDuplicateNames),
            'imagenes_duplicadas_tamaño' => count($imagesDuplicateSize)
        ];

        \Log::info('=== MÉTODO INDEX COMPLETADO ===', [
            'stats_keys' => array_keys($stats),
            'stats_total_documentos' => $stats['total_documentos'] ?? 'NO DEFINIDO',
            'duplicatesForView_count' => count($duplicatesForView),
            'missing_count' => count($missing),
            'about_to_return_view' => true
        ]);

        return view('admin.document-analysis', compact('duplicatesForView', 'missing', 'stats', 'imagesMissing', 'imagesDuplicateNames', 'imagesDuplicateSize'));
    }

    public function cleanOrphans(Request $request)
    {
        $dryRun = $request->has('dry_run');
        
        $documentos = Documento::with('inventario')->get();
        $orphans = [];

        foreach ($documentos as $documento) {
            if (!Storage::disk('public')->exists($documento->ruta)) {
                $orphans[] = $documento;
            }
        }

        if (empty($orphans)) {
            return redirect()->back()->with('success', 'No se encontraron registros huérfanos para limpiar.');
        }

        if ($dryRun) {
            $orphanData = [];
            foreach ($orphans as $documento) {
                $orphanData[] = [
                    'id' => $documento->id,
                    'nombre' => $documento->nombre,
                    'inventario' => $documento->inventario->codigo_unico . ' - ' . $documento->inventario->nombre,
                    'fecha' => $documento->created_at->format('d/m/Y H:i')
                ];
            }
            
            return redirect()->back()->with('info', 'Se encontraron ' . count($orphans) . ' registros huérfanos que se pueden limpiar.');
        }

        // Ejecutar limpieza real
        $deleted = 0;
        $errors = [];
        
        foreach ($orphans as $documento) {
            try {
                $documento->delete();
                $deleted++;
            } catch (\Exception $e) {
                $errors[] = "Error al eliminar documento '{$documento->nombre}': " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->with('warning', "Se eliminaron {$deleted} registros, pero hubo errores: " . implode(', ', $errors));
        }

        return redirect()->back()->with('success', "Se eliminaron exitosamente {$deleted} registros huérfanos.");
    }

    public function generateReport()
    {
        $report = [];
        $report[] = "REPORTE DE DOCUMENTOS DUPLICADOS - SISTEMA INVENTARIO HIDROOBRAS";
        $report[] = "================================================================";
        $report[] = "Fecha de generación: " . now()->format('d/m/Y H:i:s');
        $report[] = "";

        // Análisis de documentos duplicados
        $duplicates = DB::table('documentos')
            ->select('nombre', DB::raw('COUNT(DISTINCT inventario_id) as elementos_afectados'))
            ->groupBy('nombre')
            ->having('elementos_afectados', '>', 1)
            ->orderBy('elementos_afectados', 'desc')
            ->get();

        // Análisis de archivos faltantes
        $documentos = Documento::with('inventario')->get();
        $missing = [];

        foreach ($documentos as $documento) {
            if (!Storage::disk('public')->exists($documento->ruta)) {
                $missing[] = $documento;
            }
        }

        $report[] = "1. RESUMEN EJECUTIVO";
        $report[] = "===================";
        $report[] = "Total documentos en sistema: " . $documentos->count();
        $report[] = "Documentos con archivos faltantes: " . count($missing);
        $report[] = "Tipos de documentos duplicados: " . $duplicates->count();
        $report[] = "Total elementos afectados: " . $duplicates->sum('elementos_afectados');
        $report[] = "";

        $report[] = "2. DOCUMENTOS DUPLICADOS DETECTADOS";
        $report[] = "===================================";
        foreach ($duplicates as $duplicate) {
            $report[] = "- {$duplicate->nombre}: {$duplicate->elementos_afectados} elementos afectados";
        }
        $report[] = "";

        if (count($missing) > 0) {
            $report[] = "3. ARCHIVOS FALTANTES";
            $report[] = "=====================";
            foreach ($missing as $doc) {
                $report[] = "- {$doc->inventario->codigo_unico}: {$doc->nombre} (ruta: {$doc->ruta})";
            }
            $report[] = "";
        }

        $report[] = "3. RESUMEN Y RECOMENDACIONES";
        $report[] = "============================";
        $report[] = "";
        $report[] = "PROBLEMA IDENTIFICADO:";
        $report[] = "- Los documentos se guardaban con nombres idénticos, causando sobreescritura";
        $report[] = "- Ejemplo: 'hoja de vida.pdf' se usaba para múltiples elementos";
        $report[] = "- Cada nuevo archivo con el mismo nombre reemplazaba al anterior";
        $report[] = "";
        $report[] = "SOLUCIÓN IMPLEMENTADA:";
        $report[] = "- Los nuevos documentos ahora incluyen el código del elemento";
        $report[] = "- Formato: CODIGO_ELEMENTO_nombre_documento.extension";
        $report[] = "- Ejemplo: ARN001_hoja de vida.pdf, ESL002_hoja de vida.pdf";
        $report[] = "- Validación para evitar duplicados en el mismo elemento";
        $report[] = "";
        $report[] = "ACCIONES REQUERIDAS:";
        $report[] = "1. 📋 Contactar al almacenista para re-subir documentos faltantes";
        $report[] = "2. 🔍 Revisar elementos afectados y verificar qué documentos faltan";
        $report[] = "3. 📁 Re-subir documentos utilizando nombres específicos";
        $report[] = "4. 🧹 Limpiar registros huérfanos desde la interfaz web";

        $reportContent = implode("\n", $report);
        $fileName = 'reporte_documentos_' . now()->format('Y-m-d_H-i-s') . '.txt';
        
        return response($reportContent)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function generateAlmacenistaReport()
    {
        // Obtener datos para el reporte del almacenista
        $duplicates = DB::table('documentos')
            ->select('nombre', DB::raw('GROUP_CONCAT(DISTINCT inventario_id) as inventario_ids'))
            ->groupBy('nombre')
            ->having(DB::raw('COUNT(DISTINCT inventario_id)'), '>', 1)
            ->get();

        $documentos = Documento::with('inventario')->get();
        $missing = [];

        foreach ($documentos as $documento) {
            if (!Storage::disk('public')->exists($documento->ruta)) {
                $missing[] = [
                    'codigo' => $documento->inventario->codigo_unico,
                    'nombre_elemento' => $documento->inventario->nombre,
                    'documento' => $documento->nombre,
                    'categoria' => $documento->inventario->categoria->nombre ?? 'N/A'
                ];
            }
        }

        // Procesar duplicados para obtener elementos afectados
        $elementosAfectados = [];
        foreach ($duplicates as $duplicate) {
            $inventarioIds = explode(',', $duplicate->inventario_ids);
            $inventarios = \App\Models\Inventario::whereIn('id', $inventarioIds)->with('categoria')->get();
            
            foreach ($inventarios as $inventario) {
                $elementosAfectados[] = [
                    'codigo' => $inventario->codigo_unico,
                    'nombre_elemento' => $inventario->nombre,
                    'documento' => $duplicate->nombre,
                    'categoria' => $inventario->categoria->nombre ?? 'N/A'
                ];
            }
        }

        $reportContent = "INSTRUCCIONES PARA RECUPERACIÓN DE DOCUMENTOS\n";
        $reportContent .= "=============================================\n";
        $reportContent .= "Sistema de Inventario Hidroobras\n";
        $reportContent .= "Fecha: " . now()->format('d/m/Y H:i:s') . "\n\n";
        
        $reportContent .= "🚨 PROBLEMA DETECTADO:\n";
        $reportContent .= "Los documentos se guardaban sin códigos únicos, causando que archivos\n";
        $reportContent .= "con el mismo nombre se sobreescribieran entre diferentes elementos.\n\n";
        
        $reportContent .= "✅ SOLUCIÓN IMPLEMENTADA:\n";
        $reportContent .= "El sistema ahora evita sobreescrituras automáticamente.\n";
        $reportContent .= "Formato nuevo: CODIGO_ELEMENTO_nombre_documento.extensión\n\n";
        
        $reportContent .= "📋 ELEMENTOS QUE NECESITAN DOCUMENTOS RE-SUBIDOS:\n";
        $reportContent .= "================================================\n\n";
        
        // Combinar y mostrar todos los elementos afectados
        $todosLosElementos = array_merge($missing, $elementosAfectados);
        
        // Agrupar por categoría para mejor organización
        $porCategoria = [];
        foreach ($todosLosElementos as $elemento) {
            $categoria = $elemento['categoria'];
            if (!isset($porCategoria[$categoria])) {
                $porCategoria[$categoria] = [];
            }
            $porCategoria[$categoria][] = $elemento;
        }
        
        foreach ($porCategoria as $categoria => $elementos) {
            $reportContent .= "📁 CATEGORÍA: {$categoria}\n";
            $reportContent .= str_repeat("-", 30) . "\n";
            
            foreach ($elementos as $elemento) {
                $reportContent .= sprintf(
                    "• %s - %s\n  Documento: %s\n\n",
                    $elemento['codigo'],
                    $elemento['nombre_elemento'],
                    $elemento['documento']
                );
            }
            $reportContent .= "\n";
        }
        
        $reportContent .= "🎯 INSTRUCCIONES PASO A PASO:\n";
        $reportContent .= "=============================\n";
        $reportContent .= "1. Para cada elemento listado arriba:\n";
        $reportContent .= "   a) Localizar el documento físico o digital original\n";
        $reportContent .= "   b) Ir al sistema web > Inventarios > Buscar el elemento\n";
        $reportContent .= "   c) Entrar a 'Detalles del Elemento'\n";
        $reportContent .= "   d) Ir a la pestaña 'Documentos'\n";
        $reportContent .= "   e) Hacer clic en 'Añadir Documento'\n";
        $reportContent .= "   f) Subir el archivo correspondiente\n\n";
        
        $reportContent .= "⚠️  IMPORTANTE:\n";
        $reportContent .= "- El sistema ahora evita sobreescrituras automáticamente\n";
        $reportContent .= "- Cada archivo se guardará con un nombre único\n";
        $reportContent .= "- NO es necesario renombrar archivos manualmente\n";
        $reportContent .= "- El sistema mostrará una vista previa del nombre final\n\n";
        
        $reportContent .= "📊 ESTADÍSTICAS:\n";
        $reportContent .= "================\n";
        $reportContent .= "Total elementos afectados: " . count($todosLosElementos) . "\n";
        $reportContent .= "Archivos físicos faltantes: " . count($missing) . "\n";
        $reportContent .= "Documentos con nombres duplicados: " . count($elementosAfectados) . "\n\n";
        
        $reportContent .= "❓ ¿NECESITAS AYUDA?\n";
        $reportContent .= "===================\n";
        $reportContent .= "Contacta al administrador del sistema para:\n";
        $reportContent .= "- Importación masiva usando Excel\n";
        $reportContent .= "- Resolución de problemas técnicos\n";
        $reportContent .= "- Capacitación adicional\n";

        // Crear archivo de texto y enviarlo como descarga
        $fileName = 'instrucciones_almacenista_' . now()->format('Y-m-d_H-i-s') . '.txt';
        
        return response($reportContent)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function showMissingDetails($nombre)
    {
        $documentos = Documento::with('inventario')
            ->where('nombre', $nombre)
            ->get();

        $details = [];
        foreach ($documentos as $documento) {
            $details[] = [
                'inventario' => $documento->inventario,
                'documento' => $documento,
                'archivo_existe' => Storage::disk('public')->exists($documento->ruta)
            ];
        }

        return response()->json($details);
    }

    public function showImportForm()
    {
        // Obtener la lista de elementos afectados para el template
        $duplicates = DB::table('documentos')
            ->select('nombre', DB::raw('GROUP_CONCAT(DISTINCT inventario_id) as inventario_ids'))
            ->groupBy('nombre')
            ->having(DB::raw('COUNT(DISTINCT inventario_id)'), '>', 1)
            ->get();

        $affectedElements = [];
        foreach ($duplicates as $duplicate) {
            $inventarioIds = explode(',', $duplicate->inventario_ids);
            $inventarios = Inventario::whereIn('id', $inventarioIds)->get();
            
            foreach ($inventarios as $inventario) {
                $affectedElements[] = [
                    'codigo' => $inventario->codigo_unico,
                    'nombre' => $inventario->nombre,
                    'documento_perdido' => $duplicate->nombre
                ];
            }
        }

        return view('admin.document-import', compact('affectedElements'));
    }

    public function generateTemplate()
    {
        try {
            $export = new \App\Exports\DocumentosDuplicadosExport();
            $spreadsheet = $export->export();
            
            // Crear writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            // Preparar archivo temporal
            $fileName = 'documentos_duplicados_template_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            $tempPath = storage_path('app/temp/' . $fileName);
            
            // Crear directorio si no existe
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }
            
            // Guardar archivo temporal
            $writer->save($tempPath);
            
            // Descargar archivo
            return response()->download($tempPath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar la plantilla: ' . $e->getMessage());
        }
    }

    public function generateImagesTemplate()
    {
        \Log::info('Generando plantilla de imágenes duplicadas/faltantes');
        
        try {
            // Obtener inventarios con imágenes
            $inventarios = Inventario::whereNotNull('imagen_principal')
                ->orWhereNotNull('imagen_secundaria')
                ->get();
            
            $problemasImagenes = [];
            
            // Arrays para detectar duplicados
            $imagesByName = [];
            $imagesBySize = [];
            
            // Primera pasada: detectar duplicados
            foreach ($inventarios as $inventario) {
                // Verificar imagen principal
                if ($inventario->imagen_principal) {
                    $fullPath = storage_path('app/public/' . $inventario->imagen_principal);
                    $fileName = basename($inventario->imagen_principal);
                    
                    if (Storage::disk('public')->exists($inventario->imagen_principal)) {
                        $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                        
                        // Detectar duplicados por nombre
                        if (!isset($imagesByName[$fileName])) {
                            $imagesByName[$fileName] = [];
                        }
                        $imagesByName[$fileName][] = [
                            'inventario' => $inventario,
                            'tipo' => 'Principal',
                            'ruta' => $inventario->imagen_principal,
                            'tamaño' => $fileSize
                        ];
                        
                        // Detectar duplicados por tamaño
                        if ($fileSize > 0) {
                            if (!isset($imagesBySize[$fileSize])) {
                                $imagesBySize[$fileSize] = [];
                            }
                            $imagesBySize[$fileSize][] = [
                                'inventario' => $inventario,
                                'tipo' => 'Principal',
                                'ruta' => $inventario->imagen_principal,
                                'archivo' => $fileName
                            ];
                        }
                    }
                }
                
                // Verificar imagen secundaria
                if ($inventario->imagen_secundaria) {
                    $fullPath = storage_path('app/public/' . $inventario->imagen_secundaria);
                    $fileName = basename($inventario->imagen_secundaria);
                    
                    if (Storage::disk('public')->exists($inventario->imagen_secundaria)) {
                        $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                        
                        // Detectar duplicados por nombre
                        if (!isset($imagesByName[$fileName])) {
                            $imagesByName[$fileName] = [];
                        }
                        $imagesByName[$fileName][] = [
                            'inventario' => $inventario,
                            'tipo' => 'Secundaria',
                            'ruta' => $inventario->imagen_secundaria,
                            'tamaño' => $fileSize
                        ];
                        
                        // Detectar duplicados por tamaño
                        if ($fileSize > 0) {
                            if (!isset($imagesBySize[$fileSize])) {
                                $imagesBySize[$fileSize] = [];
                            }
                            $imagesBySize[$fileSize][] = [
                                'inventario' => $inventario,
                                'tipo' => 'Secundaria',
                                'ruta' => $inventario->imagen_secundaria,
                                'archivo' => $fileName
                            ];
                        }
                    }
                }
            }
            
            // Segunda pasada: agregar problemas
            foreach ($inventarios as $inventario) {
                $problemas = [];
                
                // Verificar imagen principal
                if ($inventario->imagen_principal) {
                    if (!Storage::disk('public')->exists($inventario->imagen_principal)) {
                        $problemas[] = [
                            'tipo_imagen' => 'Principal',
                            'problema' => 'Archivo faltante',
                            'ruta_actual' => $inventario->imagen_principal
                        ];
                    } else {
                        // Verificar si está duplicada por nombre
                        $fileName = basename($inventario->imagen_principal);
                        if (isset($imagesByName[$fileName]) && count($imagesByName[$fileName]) > 1) {
                            $problemas[] = [
                                'tipo_imagen' => 'Principal',
                                'problema' => 'Duplicada por nombre',
                                'ruta_actual' => $inventario->imagen_principal
                            ];
                        }
                        
                        // Verificar si está duplicada por tamaño
                        $fullPath = storage_path('app/public/' . $inventario->imagen_principal);
                        $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                        if ($fileSize > 0 && isset($imagesBySize[$fileSize]) && count($imagesBySize[$fileSize]) > 1) {
                            // Verificar que no sean el mismo archivo (misma ruta)
                            $uniquePaths = array_unique(array_column($imagesBySize[$fileSize], 'ruta'));
                            if (count($uniquePaths) > 1) {
                                $problemas[] = [
                                    'tipo_imagen' => 'Principal',
                                    'problema' => 'Duplicada por tamaño',
                                    'ruta_actual' => $inventario->imagen_principal
                                ];
                            }
                        }
                    }
                }
                
                // Verificar imagen secundaria
                if ($inventario->imagen_secundaria) {
                    if (!Storage::disk('public')->exists($inventario->imagen_secundaria)) {
                        $problemas[] = [
                            'tipo_imagen' => 'Secundaria',
                            'problema' => 'Archivo faltante',
                            'ruta_actual' => $inventario->imagen_secundaria
                        ];
                    } else {
                        // Verificar si está duplicada por nombre
                        $fileName = basename($inventario->imagen_secundaria);
                        if (isset($imagesByName[$fileName]) && count($imagesByName[$fileName]) > 1) {
                            $problemas[] = [
                                'tipo_imagen' => 'Secundaria',
                                'problema' => 'Duplicada por nombre',
                                'ruta_actual' => $inventario->imagen_secundaria
                            ];
                        }
                        
                        // Verificar si está duplicada por tamaño
                        $fullPath = storage_path('app/public/' . $inventario->imagen_secundaria);
                        $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                        if ($fileSize > 0 && isset($imagesBySize[$fileSize]) && count($imagesBySize[$fileSize]) > 1) {
                            // Verificar que no sean el mismo archivo (misma ruta)
                            $uniquePaths = array_unique(array_column($imagesBySize[$fileSize], 'ruta'));
                            if (count($uniquePaths) > 1) {
                                $problemas[] = [
                                    'tipo_imagen' => 'Secundaria',
                                    'problema' => 'Duplicada por tamaño',
                                    'ruta_actual' => $inventario->imagen_secundaria
                                ];
                            }
                        }
                    }
                }
                
                // Si no tiene imágenes pero debería tenerlas (elementos importantes)
                if (!$inventario->imagen_principal && !$inventario->imagen_secundaria) {
                    $problemas[] = [
                        'tipo_imagen' => 'Principal',
                        'problema' => 'Sin imagen',
                        'ruta_actual' => ''
                    ];
                }
                
                foreach ($problemas as $problema) {
                    $problemasImagenes[] = [
                        'CODIGO_ELEMENTO' => $inventario->codigo_unico,
                        'NOMBRE_ELEMENTO' => $inventario->nombre,
                        'SERIAL' => $inventario->numero_serie ?? 'N/A',
                        'CATEGORIA' => $inventario->categoria->nombre ?? 'N/A',
                        'UBICACION' => $inventario->ubicaciones->first()->nombre ?? 'N/A',
                        'TIPO_IMAGEN' => $problema['tipo_imagen'],
                        'PROBLEMA' => $problema['problema'],
                        'RUTA_ACTUAL' => $problema['ruta_actual'],
                        'ARCHIVO_IMAGEN' => '', // Para completar por el usuario
                        'OBSERVACIONES' => ''
                    ];
                }
            }
            
            if (empty($problemasImagenes)) {
                return redirect()->route('admin.documents.analysis')
                    ->with('info', 'No se encontraron problemas de imágenes para generar plantilla.');
            }
            
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Encabezados
            $headers = [
                'CODIGO_ELEMENTO', 'NOMBRE_ELEMENTO', 'SERIAL', 'CATEGORIA', 'UBICACION',
                'TIPO_IMAGEN', 'PROBLEMA', 'RUTA_ACTUAL', 'ARCHIVO_IMAGEN', 'OBSERVACIONES'
            ];
            
            $sheet->fromArray($headers, null, 'A1');
            
            // Datos
            $row = 2;
            foreach ($problemasImagenes as $problema) {
                $sheet->fromArray(array_values($problema), null, 'A' . $row);
                $row++;
            }
            
            // Estilo para encabezados
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ];
            
            $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
            
            // Auto-ajustar columnas
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Agregar instrucciones en una hoja separada
            $instructionsSheet = $spreadsheet->createSheet();
            $instructionsSheet->setTitle('INSTRUCCIONES');
            
            $instructions = [
                ['PLANTILLA PARA IMPORTACIÓN DE IMÁGENES'],
                [''],
                ['INSTRUCCIONES:'],
                ['1. Complete la columna ARCHIVO_IMAGEN con el nombre exacto del archivo'],
                ['2. Los archivos deben estar en un ZIP junto con esta plantilla'],
                ['3. Tipos de imagen aceptados: JPG, JPEG, PNG, GIF, WEBP'],
                ['4. Tamaño máximo recomendado: 5MB por imagen'],
                [''],
                ['TIPOS DE PROBLEMAS INCLUIDOS:'],
                ['- Archivo faltante: La imagen no existe físicamente'],
                ['- Duplicada por nombre: Mismo nombre de archivo en diferentes elementos'],
                ['- Duplicada por tamaño: Mismo tamaño exacto (posiblemente la misma imagen)'],
                ['- Sin imagen: Elementos que deberían tener imagen pero no la tienen'],
                [''],
                ['TIPOS DE IMAGEN:'],
                ['- Principal: Imagen principal del elemento'],
                ['- Secundaria: Imagen adicional del elemento'],
                [''],
                ['EJEMPLO:'],
                ['CODIGO_ELEMENTO: EAA-ACC-001'],
                ['ARCHIVO_IMAGEN: arnes_principal_001.jpg'],
                ['Resultado: EAA-ACC-001_principal.jpg'],
                [''],
                ['IMPORTANTE:'],
                ['- Los nombres en ARCHIVO_IMAGEN deben coincidir exactamente con los archivos del ZIP'],
                ['- El sistema creará nombres únicos automáticamente'],
                ['- Las imágenes duplicadas ya no se sobrescribirán'],
                ['- Para duplicados: proporcione una imagen nueva o deje vacío para mantener la actual']
            ];
            
            $instructionsSheet->fromArray($instructions, null, 'A1');
            $instructionsSheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14]]);
            $instructionsSheet->getColumnDimension('A')->setWidth(80);
            
            // Activar la primera hoja
            $spreadsheet->setActiveSheetIndex(0);
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            $filename = 'plantilla_imagenes_' . date('Y-m-d_H-i-s') . '.xlsx';
            $tempPath = storage_path('app/temp/' . $filename);
            
            if (!is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }
            
            $writer->save($tempPath);
            
            \Log::info('Plantilla de imágenes generada exitosamente', [
                'filename' => $filename,
                'total_problemas' => count($problemasImagenes)
            ]);
            
            return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            \Log::error('Error generando plantilla de imágenes: ' . $e->getMessage());
            return redirect()->route('admin.documents.analysis')
                ->with('error', 'Error generando plantilla de imágenes: ' . $e->getMessage());
        }
    }

    public function importDocuments(Request $request)
    {
        // Validación personalizada más flexible para ZIP
        $request->validate([
            'documents_file' => [
                'required',
                'file',
                'max:500000', // 500MB max
                function ($attribute, $value, $fail) {
                    if (!$value instanceof \Illuminate\Http\UploadedFile) {
                        $fail('El archivo de documentos debe ser un archivo válido.');
                        return;
                    }
                    
                    // Verificar extensión
                    $extension = strtolower($value->getClientOriginalExtension());
                    if ($extension !== 'zip') {
                        $fail('El archivo de documentos debe ser un archivo ZIP.');
                        return;
                    }
                    
                    // Verificar tipos MIME válidos para ZIP
                    $validMimeTypes = [
                        'application/zip',
                        'application/x-zip-compressed',
                        'application/x-zip',
                        'multipart/x-zip',
                        'application/octet-stream'
                    ];
                    
                    $mimeType = $value->getMimeType();
                    if (!in_array($mimeType, $validMimeTypes)) {
                        $fail('El archivo de documentos debe ser un archivo ZIP válido.');
                        return;
                    }
                    
                    // Verificar que el archivo no esté vacío
                    if ($value->getSize() === 0) {
                        $fail('El archivo ZIP no puede estar vacío.');
                        return;
                    }
                }
            ],
            'mapping_file' => 'required|file|mimes:xlsx,xls|max:10000', // 10MB max
        ]);

        try {
            // Aumentar límites para procesamiento
            set_time_limit(300); // 5 minutos
            ini_set('memory_limit', '512M');
            
            // Log información del archivo para debug
            $documentsFile = $request->file('documents_file');
            $mappingFile = $request->file('mapping_file');
            
            // Verificar que los archivos temporales existen
            \Log::info('Verificando archivos temporales de PHP', [
                'zip_temp_path' => $documentsFile->getRealPath(),
                'zip_temp_exists' => file_exists($documentsFile->getRealPath()),
                'excel_temp_path' => $mappingFile->getRealPath(),
                'excel_temp_exists' => file_exists($mappingFile->getRealPath()),
                'temp_dir_php' => sys_get_temp_dir()
            ]);
            
            \Log::info('Iniciando importación de documentos', [
                'zip_name' => $documentsFile->getClientOriginalName(),
                'zip_size' => $documentsFile->getSize(),
                'zip_mime' => $documentsFile->getMimeType(),
                'excel_name' => $mappingFile->getClientOriginalName(),
                'excel_size' => $mappingFile->getSize(),
                'excel_mime' => $mappingFile->getMimeType(),
                'memory_limit' => ini_get('memory_limit'),
                'time_limit' => ini_get('max_execution_time'),
                'php_version' => PHP_VERSION
            ]);
            
            // Verificar y crear directorios base necesarios
            $storageAppDir = storage_path('app');
            if (!file_exists($storageAppDir)) {
                mkdir($storageAppDir, 0755, true);
                \Log::info('Directorio storage/app creado', ['path' => $storageAppDir]);
            }
            
            $baseTempDir = storage_path('app/temp');
            if (!file_exists($baseTempDir)) {
                mkdir($baseTempDir, 0755, true);
                \Log::info('Directorio base temp creado', ['path' => $baseTempDir]);
            }
            
            // Crear directorio temporal único
            $tempDir = 'temp/document_import_' . uniqid();
            $tempPath = storage_path('app/' . $tempDir);
            
            \Log::info('Creando directorio temporal', [
                'temp_dir' => $tempDir,
                'temp_path' => $tempPath,
                'storage_app_path' => storage_path('app/'),
                'base_temp_exists' => file_exists($baseTempDir),
                'base_temp_writable' => is_writable($baseTempDir),
                'storage_app_writable' => is_writable($storageAppDir),
                'current_user' => get_current_user(),
                'php_sapi' => php_sapi_name()
            ]);
            
            // Crear directorio usando función nativa de PHP para mayor control
            if (!file_exists($tempPath)) {
                $created = mkdir($tempPath, 0755, true);
                \Log::info('Resultado de creación de directorio', [
                    'created' => $created,
                    'exists_after' => file_exists($tempPath),
                    'is_writable' => is_writable(dirname($tempPath))
                ]);
                
                if (!$created) {
                    throw new \Exception("No se pudo crear el directorio temporal: {$tempPath}");
                }
            }
            
            // También crear usando Storage como respaldo
            Storage::makeDirectory($tempDir);

            // Procesar archivo de mapeo Excel
            \Log::info('Guardando archivo Excel', [
                'temp_dir' => $tempDir,
                'original_name' => $mappingFile->getClientOriginalName()
            ]);
            
            $mappingPath = $mappingFile->storeAs($tempDir, 'mapping.xlsx');
            
            \Log::info('Archivo Excel guardado (intento inicial)', [
                'mapping_path' => $mappingPath,
                'full_path' => storage_path('app/' . $mappingPath),
                'exists' => file_exists(storage_path('app/' . $mappingPath))
            ]);
            
            // Si el Excel no se guardó, usar método alternativo
            $excelFullPath = storage_path('app/' . $mappingPath);
            if (!file_exists($excelFullPath)) {
                \Log::warning('Excel no se guardó con storeAs, intentando método alternativo');
                
                try {
                    $sourcePath = $mappingFile->getRealPath();
                    \Log::info('Archivo fuente Excel', [
                        'source_path' => $sourcePath,
                        'source_exists' => file_exists($sourcePath),
                        'source_size' => file_exists($sourcePath) ? filesize($sourcePath) : 0
                    ]);
                    
                    $excelContent = file_get_contents($sourcePath);
                    $alternativeExcelPath = $tempPath . '/mapping.xlsx';
                    
                    \Log::info('Intentando guardar Excel manualmente', [
                        'source_path' => $mappingFile->getRealPath(),
                        'destination_path' => $alternativeExcelPath,
                        'content_size' => strlen($excelContent)
                    ]);
                    
                    $saved = file_put_contents($alternativeExcelPath, $excelContent);
                    
                    if ($saved !== false && file_exists($alternativeExcelPath)) {
                        $excelFullPath = $alternativeExcelPath;
                        \Log::info('Excel guardado exitosamente con método alternativo', [
                            'path' => $excelFullPath,
                            'size' => filesize($excelFullPath)
                        ]);
                    } else {
                        throw new \Exception('Falló el método alternativo para guardar Excel');
                    }
                    
                } catch (\Exception $e) {
                    \Log::error('Error con método alternativo para Excel', [
                        'error' => $e->getMessage(),
                        'temp_path' => $tempPath,
                        'source_exists' => file_exists($mappingFile->getRealPath()),
                        'temp_dir_writable' => is_writable($tempPath)
                    ]);
                    throw new \Exception('Error al guardar el archivo Excel: ' . $e->getMessage());
                }
            }
            
            // Procesar ZIP de documentos
            \Log::info('Guardando archivo ZIP', [
                'temp_dir' => $tempDir,
                'original_name' => $documentsFile->getClientOriginalName(),
                'temp_dir_exists' => file_exists($tempPath)
            ]);
            
            $zipPath = $documentsFile->storeAs($tempDir, 'documents.zip');
            
            \Log::info('Archivo ZIP guardado (intento)', [
                'zip_path' => $zipPath,
                'full_path' => storage_path('app/' . $zipPath),
                'exists' => file_exists(storage_path('app/' . $zipPath))
            ]);

            // Verificar que el archivo ZIP se guardó correctamente
            $zipFullPath = storage_path('app/' . $zipPath);
            if (!file_exists($zipFullPath)) {
                \Log::warning('Archivo ZIP no se guardó con storeAs, intentando método alternativo', [
                    'expected_path' => $zipFullPath,
                    'zip_path' => $zipPath,
                    'temp_dir_exists' => file_exists($tempPath)
                ]);
                
                // Método alternativo: copiar archivo manualmente
                try {
                    $sourceZipPath = $documentsFile->getRealPath();
                    \Log::info('Archivo fuente ZIP', [
                        'source_path' => $sourceZipPath,
                        'source_exists' => file_exists($sourceZipPath),
                        'source_size' => file_exists($sourceZipPath) ? filesize($sourceZipPath) : 0
                    ]);
                    
                    $zipContent = file_get_contents($sourceZipPath);
                    $alternativeZipPath = $tempPath . '/documents.zip';
                    
                    \Log::info('Intentando guardar ZIP manualmente', [
                        'source_path' => $documentsFile->getRealPath(),
                        'destination_path' => $alternativeZipPath,
                        'content_size' => strlen($zipContent)
                    ]);
                    
                    $saved = file_put_contents($alternativeZipPath, $zipContent);
                    
                    if ($saved !== false && file_exists($alternativeZipPath)) {
                        $zipFullPath = $alternativeZipPath;
                        \Log::info('ZIP guardado exitosamente con método alternativo', [
                            'path' => $zipFullPath,
                            'size' => filesize($zipFullPath)
                        ]);
                    } else {
                        throw new \Exception('Falló el método alternativo para guardar ZIP');
                    }
                    
                } catch (\Exception $e) {
                    \Log::error('Error con método alternativo para ZIP', [
                        'error' => $e->getMessage(),
                        'temp_path' => $tempPath,
                        'source_exists' => file_exists($documentsFile->getRealPath()),
                        'temp_dir_writable' => is_writable($tempPath)
                    ]);
                    throw new \Exception('Error al guardar el archivo ZIP: ' . $e->getMessage());
                }
            }
            
            \Log::info('Archivo ZIP guardado exitosamente', [
                'path' => $zipFullPath,
                'exists' => file_exists($zipFullPath),
                'size' => file_exists($zipFullPath) ? filesize($zipFullPath) : 0,
                'readable' => is_readable($zipFullPath),
                'permissions' => substr(sprintf('%o', fileperms($zipFullPath)), -4)
            ]);

            // Extraer ZIP
            \Log::info('Intentando abrir archivo ZIP', [
                'zip_path' => $zipFullPath,
                'file_exists' => file_exists($zipFullPath),
                'file_size' => filesize($zipFullPath),
                'is_readable' => is_readable($zipFullPath)
            ]);
            
            $zip = new \ZipArchive;
            $zipResult = $zip->open($zipFullPath);
            
            \Log::info('Resultado de apertura de ZIP', [
                'result_code' => $zipResult,
                'is_true' => $zipResult === true,
                'zip_object_created' => isset($zip)
            ]);
            
            if ($zipResult !== true) {
                $errorMessages = [
                    \ZipArchive::ER_OK => 'Sin error',
                    \ZipArchive::ER_MULTIDISK => 'Multi-disk zip archives not supported',
                    \ZipArchive::ER_RENAME => 'Renaming temporary file failed',
                    \ZipArchive::ER_CLOSE => 'Closing zip archive failed',
                    \ZipArchive::ER_SEEK => 'Seek error',
                    \ZipArchive::ER_READ => 'Read error',
                    \ZipArchive::ER_WRITE => 'Write error',
                    \ZipArchive::ER_CRC => 'CRC error',
                    \ZipArchive::ER_ZIPCLOSED => 'Containing zip archive was closed',
                    \ZipArchive::ER_NOENT => 'No such file',
                    \ZipArchive::ER_EXISTS => 'File already exists',
                    \ZipArchive::ER_OPEN => 'Can\'t open file',
                    \ZipArchive::ER_TMPOPEN => 'Failure to create temporary file',
                    \ZipArchive::ER_ZLIB => 'Zlib error',
                    \ZipArchive::ER_MEMORY => 'Memory allocation failure',
                    \ZipArchive::ER_CHANGED => 'Entry has been changed',
                    \ZipArchive::ER_COMPNOTSUPP => 'Compression method not supported',
                    \ZipArchive::ER_EOF => 'Premature EOF',
                    \ZipArchive::ER_INVAL => 'Invalid argument',
                    \ZipArchive::ER_NOZIP => 'Not a zip archive',
                    \ZipArchive::ER_INTERNAL => 'Internal error',
                    \ZipArchive::ER_INCONS => 'Zip archive inconsistent',
                    \ZipArchive::ER_REMOVE => 'Can\'t remove file',
                    \ZipArchive::ER_DELETED => 'Entry has been deleted'
                ];
                
                $errorMessage = $errorMessages[$zipResult] ?? 'Error desconocido';
                \Log::error('Error al abrir ZIP', [
                    'code' => $zipResult,
                    'message' => $errorMessage,
                    'file_path' => $zipFullPath
                ]);
                
                throw new \Exception("No se pudo abrir el archivo ZIP: {$errorMessage} (Código: {$zipResult})");
            }

            $extractPath = $tempPath . '/documents';
            
            // Crear directorio de extracción
            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0755, true);
            }
            
            // Extraer archivos
            $extracted = $zip->extractTo($extractPath);
            $numFiles = $zip->numFiles;
            $zip->close();
            
            if (!$extracted) {
                throw new \Exception('Error al extraer el contenido del archivo ZIP');
            }
            
            \Log::info('ZIP extraído exitosamente', [
                'extract_path' => $extractPath,
                'num_files' => $numFiles,
                'extracted_files' => is_dir($extractPath) ? scandir($extractPath) : [],
                'extract_dir_exists' => is_dir($extractPath),
                'extract_dir_readable' => is_readable($extractPath)
            ]);

            // Verificar que el archivo Excel existe (usar la ruta ya determinada)
            \Log::info('Verificando archivo Excel final', [
                'excel_path' => $excelFullPath,
                'excel_exists' => file_exists($excelFullPath),
                'excel_size' => file_exists($excelFullPath) ? filesize($excelFullPath) : 0,
                'excel_readable' => is_readable($excelFullPath)
            ]);
            
            if (!file_exists($excelFullPath)) {
                throw new \Exception('No se pudo guardar el archivo Excel después de intentar ambos métodos');
            }

            // Procesar importación usando PhpSpreadsheet
            \Log::info('Iniciando procesamiento con DocumentosDuplicadosImport', [
                'extract_path' => $extractPath,
                'excel_path' => $excelFullPath
            ]);
            
            try {
                $import = new DocumentosDuplicadosImport($extractPath);
                \Log::info('DocumentosDuplicadosImport instanciado correctamente');
                
                $results = $import->import($excelFullPath);
                \Log::info('Método import() ejecutado', [
                    'results_type' => gettype($results),
                    'results_count' => is_array($results) ? count($results) : 'no es array'
                ]);
                
                if (empty($results)) {
                    \Log::warning('El método import() retornó resultados vacíos');
                    $results = []; // Asegurar que sea un array
                }
                
                \Log::info('DocumentosDuplicadosImport completado exitosamente', [
                    'results_count' => count($results),
                    'results_preview' => array_slice($results, 0, 3) // Primeros 3 resultados para preview
                ]);
                
            } catch (\Exception $importException) {
                \Log::error('Error durante la importación de documentos', [
                    'error_message' => $importException->getMessage(),
                    'error_trace' => $importException->getTraceAsString(),
                    'extract_path' => $extractPath,
                    'excel_path' => $excelFullPath
                ]);
                
                // Re-lanzar la excepción para que sea manejada por el catch principal
                throw new \Exception('Error en la importación: ' . $importException->getMessage(), 0, $importException);
            }
            
            \Log::info('Importación completada', [
                'total_results' => count($results),
                'results_summary' => array_count_values(array_column($results, 'status'))
            ]);
            
            // Limpiar archivos temporales
            Storage::deleteDirectory($tempDir);

            $successCount = count(array_filter($results, fn($r) => in_array($r['status'], ['created', 'updated'])));
            $errorCount = count(array_filter($results, fn($r) => $r['status'] === 'error'));
            $createdCount = count(array_filter($results, fn($r) => $r['status'] === 'created'));
            $updatedCount = count(array_filter($results, fn($r) => $r['status'] === 'updated'));
            
            \Log::info('=== RESUMEN FINAL DE IMPORTACIÓN ===', [
                'total_procesados' => count($results),
                'exitosos' => $successCount,
                'errores' => $errorCount,
                'creados' => $createdCount,
                'actualizados' => $updatedCount,
                'detalles' => $results
            ]);

            $message = "Importación completada. ";
            $message .= "Documentos creados: {$createdCount}, ";
            $message .= "Documentos actualizados: {$updatedCount}, ";
            $message .= "Errores: {$errorCount}";

            // Limpiar cualquier cache que pueda estar interfiriendo
            \Log::info('Preparando redirect después de importación exitosa', [
                'message' => $message,
                'results_count' => count($results),
                'redirect_route' => 'admin.documents.analysis'
            ]);

            return redirect()->route('admin.documents.analysis')->with('success', $message)
                ->with('import_details', $results)
                ->with('import_stats', [
                    'total' => count($results),
                    'created' => $createdCount,
                    'updated' => $updatedCount,
                    'errors' => $errorCount
                ]);

        } catch (\Exception $e) {
            \Log::error('Error crítico en importación de documentos', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'temp_dir' => $tempDir ?? 'no definido',
                'temp_path' => isset($tempPath) ? $tempPath : 'no definido'
            ]);
            
            // Limpiar en caso de error
            if (isset($tempDir)) {
                try {
                    Storage::deleteDirectory($tempDir);
                    \Log::info('Directorio temporal limpiado', ['temp_dir' => $tempDir]);
                } catch (\Exception $cleanupException) {
                    \Log::error('Error al limpiar directorio temporal', [
                        'temp_dir' => $tempDir,
                        'cleanup_error' => $cleanupException->getMessage()
                    ]);
                }
            }
            
            // Limpiar también con método alternativo si existe
            if (isset($tempPath) && file_exists($tempPath)) {
                try {
                    $this->deleteDirectory($tempPath);
                    \Log::info('Directorio temporal limpiado con método alternativo', ['temp_path' => $tempPath]);
                } catch (\Exception $altCleanupException) {
                    \Log::error('Error al limpiar con método alternativo', [
                        'temp_path' => $tempPath,
                        'alt_cleanup_error' => $altCleanupException->getMessage()
                    ]);
                }
            }
            
            return redirect()->route('admin.documents.analysis')->with('error', 'Error en la importación: ' . $e->getMessage());
        }
    }

    private function readMappingFile($filePath)
    {
        $data = [];
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        
        try {
            if (in_array($extension, ['xlsx', 'xls'])) {
                // Para archivos Excel reales, necesitarías PhpSpreadsheet
                // Por ahora, retornar array vacío con mensaje de error
                throw new \Exception('Archivos Excel (.xlsx, .xls) no soportados actualmente. Use CSV.');
            } else {
                // Leer CSV
                $file = fopen($filePath, 'r');
                
                // Detectar y manejar BOM UTF-8
                $bom = fread($file, 3);
                if ($bom !== "\xEF\xBB\xBF") {
                    rewind($file);
                }
                
                // Leer encabezados
                $headers = fgetcsv($file, 0, ",");
                
                // Validar que tenemos las columnas necesarias
                if (!$headers || count($headers) < 4) {
                    throw new \Exception('El archivo CSV debe tener al menos 4 columnas: CODIGO_ELEMENTO, NOMBRE_ELEMENTO, NOMBRE_DOCUMENTO, ARCHIVO_DOCUMENTO');
                }
                
                // Leer datos
                $rowNumber = 1;
                while (($row = fgetcsv($file, 0, ",")) !== FALSE) {
                    $rowNumber++;
                    
                    // Validar que la fila tiene datos suficientes
                    if (count($row) >= 4 && !empty(trim($row[0])) && !empty(trim($row[3]))) {
                        $data[] = [
                            'codigo_elemento' => trim($row[0]),
                            'nombre_documento' => trim($row[2]),
                            'archivo_documento' => trim($row[3])
                        ];
                    } else if (!empty(trim($row[0]))) {
                        // Fila con código pero sin archivo - registrar advertencia
                        \Log::warning("Fila {$rowNumber}: Elemento {$row[0]} no tiene archivo asignado");
                    }
                }
                fclose($file);
            }
        } catch (\Exception $e) {
            \Log::error('Error leyendo archivo de mapeo: ' . $e->getMessage());
            throw $e;
        }
        
        return $data;
    }

    private function processDocumentImport($mappingData, $extractPath)
    {
        $success = 0;
        $errors = 0;
        
        foreach ($mappingData as $mapping) {
            try {
                // Buscar elemento por código
                $inventario = Inventario::where('codigo_unico', $mapping['codigo_elemento'])->first();
                if (!$inventario) {
                    $errors++;
                    continue;
                }

                // Buscar archivo físico
                $archivePath = $this->findFileInDirectory($extractPath, $mapping['archivo_documento']);
                if (!$archivePath) {
                    $errors++;
                    continue;
                }

                // Crear nombre único del archivo
                $extension = pathinfo($archivePath, PATHINFO_EXTENSION);
                $fileName = $inventario->codigo_unico . '_' . $mapping['nombre_documento'] . '.' . $extension;
                
                // Verificar si ya existe
                $counter = 1;
                while (file_exists(storage_path('app/public/documentos/' . $fileName))) {
                    $fileName = $inventario->codigo_unico . '_' . $mapping['nombre_documento'] . '_' . $counter . '.' . $extension;
                    $counter++;
                }
                
                // Copiar archivo a destino final
                $destinationPath = 'documentos/' . $fileName;
                copy($archivePath, storage_path('app/public/' . $destinationPath));

                // Crear registro en base de datos
                Documento::create([
                    'nombre' => $mapping['nombre_documento'],
                    'inventario_id' => $inventario->id,
                    'ruta' => $destinationPath,
                ]);

                $success++;

            } catch (\Exception $e) {
                $errors++;
                \Log::error('Error procesando documento: ' . $e->getMessage());
            }
        }

        return ['success' => $success, 'errors' => $errors];
    }

    private function findFileInDirectory($directory, $filename)
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        
        foreach ($files as $file) {
            if ($file->isFile() && $file->getFilename() === $filename) {
                return $file->getPathname();
            }
        }
        
        return null;
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) return;
        
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

        rmdir($dir);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Método de prueba para verificar logging
     */
    public function testLogging()
    {
        \Log::info('=== PRUEBA DE LOGGING ===', [
            'timestamp' => now(),
            'user' => auth()->user()->name ?? 'anonymous',
            'method' => __METHOD__,
            'memory_usage' => memory_get_usage(true),
            'php_version' => PHP_VERSION
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Log de prueba generado. Revisar storage/logs/laravel.log',
            'timestamp' => now()
        ]);
    }

    public function importImages(Request $request)
    {
        \Log::info('=== INICIANDO IMPORTACIÓN DE IMÁGENES ===', [
            'timestamp' => now(),
            'user_id' => auth()->id(),
            'request_method' => request()->method()
        ]);

        try {
            // Validación de archivos
            $request->validate([
                'images_file' => [
                    'required',
                    'file',
                    'max:500000', // 500MB max
                    function ($attribute, $value, $fail) {
                        if (!$value instanceof \Illuminate\Http\UploadedFile) {
                            $fail('El archivo de imágenes debe ser un archivo válido.');
                            return;
                        }
                        
                        // Verificar extensión
                        $extension = strtolower($value->getClientOriginalExtension());
                        if ($extension !== 'zip') {
                            $fail('El archivo de imágenes debe ser un archivo ZIP.');
                            return;
                        }
                        
                        // Verificar tipos MIME válidos para ZIP
                        $validMimeTypes = [
                            'application/zip',
                            'application/x-zip-compressed',
                            'application/x-zip',
                            'multipart/x-zip',
                            'application/octet-stream'
                        ];
                        
                        $mimeType = $value->getMimeType();
                        if (!in_array($mimeType, $validMimeTypes)) {
                            $fail('El archivo debe ser un ZIP válido.');
                            return;
                        }
                    }
                ],
                'excel_file' => [
                    'required',
                    'file',
                    'max:10240', // 10MB max
                    function ($attribute, $value, $fail) {
                        if (!$value instanceof \Illuminate\Http\UploadedFile) {
                            $fail('El archivo Excel debe ser un archivo válido.');
                            return;
                        }
                        
                        $extension = strtolower($value->getClientOriginalExtension());
                        if (!in_array($extension, ['xlsx', 'xls'])) {
                            $fail('El archivo debe ser un Excel válido (.xlsx o .xls).');
                            return;
                        }
                    }
                ]
            ]);

            // Crear directorio temporal único
            $tempDir = 'temp/images_import_' . uniqid();
            $tempPath = storage_path('app/' . $tempDir);
            
            if (!Storage::exists($tempDir)) {
                Storage::makeDirectory($tempDir);
            }

            \Log::info('Directorio temporal creado', ['temp_path' => $tempPath]);

            // Guardar archivo ZIP
            $zipFile = $request->file('images_file');
            $zipPath = $zipFile->storeAs($tempDir, 'images.zip');
            $zipFullPath = storage_path('app/' . $zipPath);

            // Guardar archivo Excel
            $excelFile = $request->file('excel_file');
            $excelPath = $excelFile->storeAs($tempDir, 'mapping.xlsx');
            $excelFullPath = storage_path('app/' . $excelPath);

            \Log::info('Archivos guardados', [
                'zip_path' => $zipFullPath,
                'excel_path' => $excelFullPath
            ]);

            // Extraer ZIP
            $extractPath = $tempPath . '/extracted';
            $zip = new ZipArchive;
            
            if ($zip->open($zipFullPath) === TRUE) {
                $zip->extractTo($extractPath);
                $zip->close();
                \Log::info('ZIP extraído exitosamente', ['extract_path' => $extractPath]);
            } else {
                throw new \Exception('No se pudo abrir el archivo ZIP');
            }

            // Procesar importación de imágenes
            $results = $this->processImageImport($excelFullPath, $extractPath);

            // Limpiar archivos temporales
            Storage::deleteDirectory($tempDir);

            $successCount = count(array_filter($results, fn($r) => in_array($r['status'], ['created', 'updated'])));
            $errorCount = count(array_filter($results, fn($r) => $r['status'] === 'error'));
            $createdCount = count(array_filter($results, fn($r) => $r['status'] === 'created'));
            $updatedCount = count(array_filter($results, fn($r) => $r['status'] === 'updated'));
            
            \Log::info('=== RESUMEN FINAL DE IMPORTACIÓN DE IMÁGENES ===', [
                'total_procesados' => count($results),
                'exitosos' => $successCount,
                'errores' => $errorCount,
                'creados' => $createdCount,
                'actualizados' => $updatedCount
            ]);

            $message = "Importación de imágenes completada. ";
            $message .= "Imágenes procesadas: {$successCount}, ";
            $message .= "Errores: {$errorCount}";

            return redirect()->route('admin.documents.analysis')->with('success', $message)
                ->with('import_details', $results)
                ->with('import_stats', [
                    'total' => count($results),
                    'created' => $createdCount,
                    'updated' => $updatedCount,
                    'errors' => $errorCount
                ]);

        } catch (\Exception $e) {
            \Log::error('Error crítico en importación de imágenes', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            
            // Limpiar en caso de error
            if (isset($tempDir)) {
                try {
                    Storage::deleteDirectory($tempDir);
                } catch (\Exception $cleanupException) {
                    \Log::error('Error al limpiar directorio temporal', [
                        'temp_dir' => $tempDir,
                        'error' => $cleanupException->getMessage()
                    ]);
                }
            }

            return redirect()->route('admin.documents.analysis')
                ->with('error', 'Error en la importación de imágenes: ' . $e->getMessage());
        }
    }

    private function processImageImport($excelPath, $extractPath)
    {
        $results = [];
        
        try {
            // Cargar archivo Excel
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Obtener headers
            $headers = array_map('strtolower', array_map('trim', array_shift($rows)));
            
            \Log::info('Headers encontrados en Excel', ['headers' => $headers]);
            
            foreach ($rows as $rowIndex => $row) {
                if (empty(array_filter($row))) {
                    continue;
                }
                
                $data = array_combine($headers, $row);
                
                // Verificar que tengamos los campos necesarios
                if (empty($data['codigo_elemento']) || empty($data['archivo_imagen'])) {
                    $results[] = [
                        'row' => $rowIndex + 2,
                        'codigo' => $data['codigo_elemento'] ?? 'N/A',
                        'archivo' => $data['archivo_imagen'] ?? 'N/A',
                        'status' => 'error',
                        'message' => 'Código de elemento o archivo de imagen faltante'
                    ];
                    continue;
                }
                
                // Buscar elemento por código
                $inventario = Inventario::where('codigo_unico', $data['codigo_elemento'])->first();
                if (!$inventario) {
                    $results[] = [
                        'row' => $rowIndex + 2,
                        'codigo' => $data['codigo_elemento'],
                        'archivo' => $data['archivo_imagen'],
                        'status' => 'error',
                        'message' => 'Elemento no encontrado'
                    ];
                    continue;
                }
                
                // Buscar archivo de imagen
                $imagePath = $this->findImageFile($extractPath, $data['archivo_imagen']);
                if (!$imagePath) {
                    $results[] = [
                        'row' => $rowIndex + 2,
                        'codigo' => $data['codigo_elemento'],
                        'archivo' => $data['archivo_imagen'],
                        'status' => 'error',
                        'message' => 'Archivo de imagen no encontrado en el ZIP'
                    ];
                    continue;
                }
                
                // Procesar imagen
                try {
                    $this->processImageFile($inventario, $imagePath, $data);
                    
                    $results[] = [
                        'row' => $rowIndex + 2,
                        'codigo' => $data['codigo_elemento'],
                        'archivo' => $data['archivo_imagen'],
                        'status' => 'updated',
                        'message' => 'Imagen procesada exitosamente'
                    ];
                    
                } catch (\Exception $e) {
                    $results[] = [
                        'row' => $rowIndex + 2,
                        'codigo' => $data['codigo_elemento'],
                        'archivo' => $data['archivo_imagen'],
                        'status' => 'error',
                        'message' => 'Error al procesar imagen: ' . $e->getMessage()
                    ];
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Error procesando Excel de imágenes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
        
        return $results;
    }
    
    private function findImageFile($basePath, $fileName)
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
    
    private function processImageFile($inventario, $imagePath, $data)
    {
        // Validar que sea una imagen
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('Tipo de archivo no permitido: ' . $extension);
        }
        
        // Determinar tipo de imagen (principal o secundaria)
        $tipoImagen = isset($data['tipo_imagen']) ? strtolower($data['tipo_imagen']) : 'principal';
        
        // Crear nombre único para la imagen
        $imageHash = hash_file('md5', $imagePath);
        $fileName = $inventario->codigo_unico . '_' . $tipoImagen . '_' . $imageHash . '.' . $extension;
        
        // Asegurar que existe el directorio de imágenes
        $imageDir = 'inventario_imagenes';
        if (!Storage::disk('public')->exists($imageDir)) {
            Storage::disk('public')->makeDirectory($imageDir);
        }
        
        $destinationPath = $imageDir . '/' . $fileName;
        $fullDestinationPath = storage_path('app/public/' . $destinationPath);
        
        // Copiar imagen
        if (!copy($imagePath, $fullDestinationPath)) {
            throw new \Exception('No se pudo copiar la imagen al destino');
        }
        
        // Establecer permisos
        chmod($fullDestinationPath, 0644);
        
        // Actualizar registro del inventario
        if ($tipoImagen === 'principal') {
            $inventario->imagen_principal = $destinationPath;
        } else {
            $inventario->imagen_secundaria = $destinationPath;
        }
        
        $inventario->save();
        
        \Log::info('Imagen procesada exitosamente', [
            'inventario_codigo' => $inventario->codigo_unico,
            'tipo_imagen' => $tipoImagen,
            'archivo_origen' => basename($imagePath),
            'archivo_destino' => $fileName
        ]);
    }
}