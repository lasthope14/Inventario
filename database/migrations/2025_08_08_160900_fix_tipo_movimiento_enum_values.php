<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar si la columna existe y actualizar sus valores enum
        if (Schema::hasColumn('movimientos', 'tipo_movimiento')) {
            // Usar ALTER TABLE directo para modificar el ENUM
            DB::statement("ALTER TABLE movimientos MODIFY COLUMN tipo_movimiento ENUM('individual', 'masivo', 'normal', 'reversion', 'ajuste') DEFAULT 'individual'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a la definición anterior
        if (Schema::hasColumn('movimientos', 'tipo_movimiento')) {
            DB::statement("ALTER TABLE movimientos MODIFY COLUMN tipo_movimiento ENUM('normal', 'reversion', 'ajuste') DEFAULT 'normal'");
        }
    }
};