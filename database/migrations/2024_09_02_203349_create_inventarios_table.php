<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_unico')->unique();
            $table->foreignId('categoria_id')->constrained();
            $table->string('nombre');
            $table->integer('cantidad');
            $table->string('propietario');
            $table->string('modelo');
            $table->string('numero_serie')->nullable();
            $table->string('marca');
            $table->date('fecha_compra');
            $table->string('numero_factura');
            $table->decimal('valor', 10, 2);
            $table->date('fecha_baja')->nullable();
            $table->date('fecha_inspeccion')->nullable();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventarios');
    }
};