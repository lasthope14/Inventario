<?php

/**
 * Script para ejecutar migraciones pendientes en producción
 * 
 * Este script debe ejecutarse en el servidor de producción para
 * aplicar las migraciones que faltan, especialmente la columna
 * 'nuevo_estado' en la tabla movimientos.
 * 
 * Instrucciones:
 * 1. Subir este archivo al servidor de producción
 * 2. Ejecutar: php migrate-production.php
 * 3. O ejecutar directamente: php artisan migrate --force
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "=== EJECUTANDO MIGRACIONES EN PRODUCCIÓN ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";

// Verificar estado actual de migraciones
echo "Estado actual de migraciones:\n";
$kernel->call('migrate:status');

echo "\n=== EJECUTANDO MIGRACIONES PENDIENTES ===\n";

// Ejecutar migraciones pendientes
try {
    $kernel->call('migrate', ['--force' => true]);
    echo "\n✅ Migraciones ejecutadas exitosamente\n";
} catch (Exception $e) {
    echo "\n❌ Error al ejecutar migraciones: " . $e->getMessage() . "\n";
    exit(1);
}

// Verificar estado final
echo "\n=== ESTADO FINAL DE MIGRACIONES ===\n";
$kernel->call('migrate:status');

echo "\n=== LIMPIANDO CACHÉ ===\n";
try {
    $kernel->call('config:cache');
    $kernel->call('route:cache');
    $kernel->call('view:cache');
    echo "✅ Caché limpiado exitosamente\n";
} catch (Exception $e) {
    echo "⚠️ Advertencia al limpiar caché: " . $e->getMessage() . "\n";
}

echo "\n=== PROCESO COMPLETADO ===\n";
echo "Las migraciones han sido aplicadas.\n";
echo "El sistema debería funcionar correctamente ahora.\n";