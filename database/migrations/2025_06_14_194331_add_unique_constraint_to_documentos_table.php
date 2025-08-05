<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            // Agregar constraint único para evitar documentos duplicados por elemento
            $table->unique(['inventario_id', 'nombre'], 'unique_documento_por_elemento');
            
            // Agregar índice para búsquedas más rápidas
            $table->index(['inventario_id', 'nombre'], 'idx_inventario_nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            // Eliminar constraints e índices
            $table->dropUnique('unique_documento_por_elemento');
            $table->dropIndex('idx_inventario_nombre');
        });
    }
};
