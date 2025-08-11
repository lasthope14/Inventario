<?php
require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Inventario;

echo "Buscando elementos que contengan HTML o caracteres especiales:\n";

$elementos = Inventario::whereNotNull('nombre')
    ->where('nombre', '!=', '')
    ->distinct()
    ->orderBy('nombre')
    ->pluck('nombre');

echo "Total de elementos únicos: " . $elementos->count() . "\n\n";

foreach ($elementos as $elemento) {
    // Buscar elementos que contengan < o >
    if (strpos($elemento, '<') !== false || strpos($elemento, '>') !== false) {
        echo "Elemento con HTML: {$elemento}\n";
    }
    
    // Buscar elementos que contengan 'div'
    if (stripos($elemento, 'div') !== false) {
        echo "Elemento con 'div': {$elemento}\n";
    }
    
    // Buscar elementos sospechosos
    if (strlen($elemento) < 5 && (stripos($elemento, 'div') !== false || strpos($elemento, '<') !== false)) {
        echo "Elemento sospechoso: '{$elemento}'\n";
    }
}

echo "\nPrimeros 20 elementos para revisión:\n";
foreach ($elementos->take(20) as $elemento) {
    echo "- {$elemento}\n";
}

echo "\nÚltimos 20 elementos para revisión:\n";
foreach ($elementos->slice(-20) as $elemento) {
    echo "- {$elemento}\n";
}
?>