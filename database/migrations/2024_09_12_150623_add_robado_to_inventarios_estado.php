<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->enum('estado', ['disponible', 'en uso', 'en mantenimiento', 'dado de baja', 'robado'])
                  ->default('disponible')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->enum('estado', ['disponible', 'en uso', 'en mantenimiento', 'dado de baja'])
                  ->default('disponible')
                  ->change();
        });
    }
};