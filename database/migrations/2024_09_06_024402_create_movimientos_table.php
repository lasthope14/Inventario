<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('movimientos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('inventario_id')->constrained()->onDelete('cascade');
        $table->string('ubicacion_origen');
        $table->string('ubicacion_destino');
        $table->foreignId('usuario_origen_id')->constrained('users');
        $table->foreignId('usuario_destino_id')->constrained('users');
        $table->text('motivo')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
