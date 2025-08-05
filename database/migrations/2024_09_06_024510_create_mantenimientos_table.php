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
    Schema::create('mantenimientos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('inventario_id')->constrained()->onDelete('cascade');
        $table->enum('tipo', ['preventivo', 'correctivo']);
        $table->date('fecha_programada');
        $table->date('fecha_realizado')->nullable();
        $table->text('descripcion');
        $table->text('resultado')->nullable();
        $table->foreignId('responsable_id')->constrained('users');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
