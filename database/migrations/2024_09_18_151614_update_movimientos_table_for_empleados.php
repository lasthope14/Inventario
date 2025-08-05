<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMovimientosTableForEmpleados extends Migration
{
    public function up()
    {
        Schema::table('movimientos', function (Blueprint $table) {
            // Eliminar las restricciones de clave foránea existentes
            $table->dropForeign(['usuario_origen_id']);
            $table->dropForeign(['usuario_destino_id']);

            // Modificar las columnas para que hagan referencia a la tabla 'empleados'
            $table->foreign('usuario_origen_id')->references('id')->on('empleados')->onDelete('cascade');
            $table->foreign('usuario_destino_id')->references('id')->on('empleados')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('movimientos', function (Blueprint $table) {
            // Revertir los cambios si es necesario
            $table->dropForeign(['usuario_origen_id']);
            $table->dropForeign(['usuario_destino_id']);

            $table->foreign('usuario_origen_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('usuario_destino_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}