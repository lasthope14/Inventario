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
        Schema::table('movimientos', function (Blueprint $table) {
            $table->boolean('revertido')->default(false)->after('nuevo_estado');
            $table->timestamp('revertido_at')->nullable()->after('revertido');
            $table->unsignedBigInteger('revertido_por')->nullable()->after('revertido_at');
            $table->unsignedBigInteger('movimiento_original_id')->nullable()->after('revertido_por');
            $table->enum('tipo_movimiento', ['normal', 'reversion', 'ajuste'])->default('normal')->after('movimiento_original_id');
            
            // Índices para mejorar rendimiento
            $table->index(['revertido', 'created_at']);
            $table->index('movimiento_original_id');
            
            // Claves foráneas
            $table->foreign('revertido_por')->references('id')->on('users')->onDelete('set null');
            $table->foreign('movimiento_original_id')->references('id')->on('movimientos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropForeign(['revertido_por']);
            $table->dropForeign(['movimiento_original_id']);
            $table->dropIndex(['revertido', 'created_at']);
            $table->dropIndex(['movimiento_original_id']);
            $table->dropColumn(['revertido', 'revertido_at', 'revertido_por', 'movimiento_original_id', 'tipo_movimiento']);
        });
    }
};
