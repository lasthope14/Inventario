<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyInventarioUbicacionesUniqueConstraint extends Migration
{
    public function up()
    {
        Schema::table('inventario_ubicaciones', function (Blueprint $table) {
            // Primero eliminar las claves foráneas
            $table->dropForeign(['inventario_id']);
            $table->dropForeign(['ubicacion_id']);
            
            // Eliminar la restricción única existente
            $table->dropUnique(['inventario_id', 'ubicacion_id']);
            
            // Crear nueva restricción única que incluye inventario_id, ubicacion_id y estado
            $table->unique(['inventario_id', 'ubicacion_id', 'estado'], 'inventario_ubicaciones_inventario_ubicacion_estado_unique');
            
            // Restaurar las claves foráneas
            $table->foreign('inventario_id')->references('id')->on('inventarios')->onDelete('cascade');
            $table->foreign('ubicacion_id')->references('id')->on('ubicaciones')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('inventario_ubicaciones', function (Blueprint $table) {
            // Eliminar las claves foráneas
            $table->dropForeign(['inventario_id']);
            $table->dropForeign(['ubicacion_id']);
            
            // Eliminar la nueva restricción única
            $table->dropUnique('inventario_ubicaciones_inventario_ubicacion_estado_unique');
            
            // Restaurar la restricción única original
            $table->unique(['inventario_id', 'ubicacion_id']);
            
            // Restaurar las claves foráneas
            $table->foreign('inventario_id')->references('id')->on('inventarios')->onDelete('cascade');
            $table->foreign('ubicacion_id')->references('id')->on('ubicaciones')->onDelete('cascade');
        });
    }
}