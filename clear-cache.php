<?php
// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir la base path
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';

// Cargar el framework
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    // Limpiar configuración
    $artisan = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    echo "Limpiando caché...<br>";
    
    // Ejecutar comandos
    $artisan->call('config:clear');
    echo "Config cleared<br>";
    
    $artisan->call('cache:clear');
    echo "Cache cleared<br>";
    
    $artisan->call('route:clear');
    echo "Route cache cleared<br>";
    
    $artisan->call('view:clear');
    echo "View cache cleared<br>";
    
    $artisan->call('optimize:clear');
    echo "Optimizations cleared<br>";

    // Limpiar caché de aplicación
    if (function_exists('opcache_reset')) {
        opcache_reset();
        echo "OPCache cleared<br>";
    }

    // Limpiar caché de archivos
    $cacheFolders = [
        'storage/framework/cache',
        'storage/framework/views',
        'storage/framework/sessions',
        'bootstrap/cache'
    ];

    foreach ($cacheFolders as $folder) {
        $path = __DIR__ . '/' . $folder;
        if (is_dir($path)) {
            $files = glob($path.'/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            echo "$folder cleaned<br>";
        }
    }

    echo "<br>¡Todo limpio! 🎉";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}