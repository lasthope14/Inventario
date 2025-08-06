<?php

/**
 * Script de emergencia para agregar la columna 'nuevo_estado' 
 * a la tabla movimientos en producción
 * 
 * Este script verifica si la columna existe y la crea si no existe.
 * Es una solución de emergencia si las migraciones normales fallan.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "=== VERIFICACIÓN Y CORRECCIÓN DE COLUMNA 'nuevo_estado' ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Verificar si la columna existe
    $columnExists = Schema::hasColumn('movimientos', 'nuevo_estado');
    
    if ($columnExists) {
        echo "✅ La columna 'nuevo_estado' ya existe en la tabla movimientos.\n";
        echo "No se requiere ninguna acción.\n";
    } else {
        echo "❌ La columna 'nuevo_estado' NO existe en la tabla movimientos.\n";
        echo "Procediendo a crearla...\n\n";
        
        // Crear la columna
        Schema::table('movimientos', function (Blueprint $table) {
            $table->enum('nuevo_estado', ['disponible', 'en uso', 'en mantenimiento', 'dado de baja', 'robado'])
                  ->nullable()
                  ->after('cantidad')
                  ->comment('Estado que tendrá el elemento en la ubicación de destino');
        });
        
        echo "✅ Columna 'nuevo_estado' creada exitosamente.\n";
    }
    
    // Verificar otras columnas relacionadas
    echo "\n=== VERIFICANDO OTRAS COLUMNAS RELACIONADAS ===\n";
    
    $columnsToCheck = [
        'revertido' => 'boolean',
        'revertido_at' => 'timestamp',
        'revertido_por' => 'bigint',
        'movimiento_original_id' => 'bigint',
        'tipo_movimiento' => 'enum'
    ];
    
    foreach ($columnsToCheck as $column => $type) {
        $exists = Schema::hasColumn('movimientos', $column);
        $status = $exists ? '✅' : '❌';
        echo "{$status} Columna '{$column}': " . ($exists ? 'EXISTE' : 'NO EXISTE') . "\n";
    }
    
    // Mostrar estructura actual de la tabla
    echo "\n=== ESTRUCTURA ACTUAL DE LA TABLA MOVIMIENTOS ===\n";
    $columns = DB::select('DESCRIBE movimientos');
    
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})" . 
             ($column->Null === 'YES' ? ' NULL' : ' NOT NULL') . 
             ($column->Default ? " DEFAULT {$column->Default}" : '') . "\n";
    }
    
    echo "\n=== PROCESO COMPLETADO EXITOSAMENTE ===\n";
    echo "La tabla movimientos está lista para funcionar correctamente.\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    
    echo "\n=== SOLUCIÓN ALTERNATIVA ===\n";
    echo "Si este script falla, ejecuta manualmente en la base de datos:\n\n";
    echo "ALTER TABLE movimientos ADD COLUMN nuevo_estado ENUM('disponible', 'en uso', 'en mantenimiento', 'dado de baja', 'robado') NULL AFTER cantidad COMMENT 'Estado que tendrá el elemento en la ubicación de destino';\n";
    
    exit(1);
}