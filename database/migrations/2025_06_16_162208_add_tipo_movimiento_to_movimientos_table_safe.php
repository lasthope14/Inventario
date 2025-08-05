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
            // Verificar si la columna no existe antes de agregarla
            if (!Schema::hasColumn('movimientos', 'tipo_movimiento')) {
                $table->enum('tipo_movimiento', ['individual', 'masivo', 'normal', 'reversion', 'ajuste'])->default('individual')->after('nuevo_estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            if (Schema::hasColumn('movimientos', 'tipo_movimiento')) {
                $table->dropColumn('tipo_movimiento');
            }
        });
    }
};
