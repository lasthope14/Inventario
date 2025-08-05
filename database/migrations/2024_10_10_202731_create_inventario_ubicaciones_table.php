<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventarioUbicacionesTable extends Migration
{
    public function up()
    {
        Schema::create('inventario_ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventario_id');
            $table->unsignedBigInteger('ubicacion_id');
            $table->integer('cantidad')->default(0);
            $table->timestamps();

            $table->foreign('inventario_id')->references('id')->on('inventarios')->onDelete('cascade');
            $table->foreign('ubicacion_id')->references('id')->on('ubicaciones')->onDelete('cascade');

            $table->unique(['inventario_id', 'ubicacion_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventario_ubicaciones');
    }
}