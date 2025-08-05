<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoToInventarioUbicacionesTable extends Migration
{
    public function up()
    {
        Schema::table('inventario_ubicaciones', function (Blueprint $table) {
            $table->enum('estado', ['disponible', 'en uso', 'en mantenimiento', 'dado de baja', 'robado'])->default('disponible');
        });
    }

    public function down()
    {
        Schema::table('inventario_ubicaciones', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
}