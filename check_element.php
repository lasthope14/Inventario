<?php

require_once 'vendor/autoload.php';

// Cargar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inventario;
use Illuminate\Support\Facades\Storage;

echo "=== VERIFICACIÓN DEL ELEMENTO EAA-LDV-005 ===\n";

$inventario = Inventario::where('codigo_unico', 'EAA-LDV-005')->first();

if (!$inventario) {
    echo "❌ Elemento no encontrado\n";
    exit;
}

echo "✅ Elemento encontrado: {$inventario->nombre}\n";
echo "ID: {$inventario->id}\n\n";

$documentos = $inventario->documentos;
echo "📄 Total documentos: " . count($documentos) . "\n\n";

foreach ($documentos as $doc) {
    $existe = Storage::disk('public')->exists($doc->ruta);
    $rutaCompleta = storage_path('app/public/' . $doc->ruta);
    
    echo "Documento: {$doc->nombre}\n";
    echo "  - Ruta BD: {$doc->ruta}\n";
    echo "  - Existe: " . ($existe ? '✅ SÍ' : '❌ NO') . "\n";
    echo "  - Ruta completa: {$rutaCompleta}\n";
    echo "  - Creado: {$doc->created_at}\n";
    echo "  - Actualizado: {$doc->updated_at}\n";
    
    if ($existe) {
        $tamaño = filesize($rutaCompleta);
        echo "  - Tamaño: " . number_format($tamaño) . " bytes\n";
    }
    echo "\n";
}

// Verificar archivos físicos en la carpeta documentos
echo "🗂️ ARCHIVOS FÍSICOS EN /storage/app/public/documentos/:\n";
$rutaDocumentos = storage_path('app/public/documentos');

if (is_dir($rutaDocumentos)) {
    $archivos = scandir($rutaDocumentos);
    $archivosEAA = array_filter($archivos, function($archivo) {
        return strpos($archivo, 'EAA-LDV-005') !== false;
    });
    
    echo "Archivos encontrados para EAA-LDV-005:\n";
    foreach ($archivosEAA as $archivo) {
        if ($archivo !== '.' && $archivo !== '..') {
            $rutaCompleta = $rutaDocumentos . '/' . $archivo;
            $tamaño = filesize($rutaCompleta);
            $fechaModificacion = date('Y-m-d H:i:s', filemtime($rutaCompleta));
            echo "  - {$archivo} ({$tamaño} bytes, modificado: {$fechaModificacion})\n";
        }
    }
} else {
    echo "❌ Directorio documentos no existe\n";
} 