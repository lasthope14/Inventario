<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateResponsableIdForeignKeyInMantenimientosTable extends Migration
{
    public function up()
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
            $table->foreign('responsable_id')
                  ->references('id')
                  ->on('proveedores')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
            $table->foreign('responsable_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
}