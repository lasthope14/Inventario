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
            $table->enum('nuevo_estado', ['disponible', 'en uso', 'en mantenimiento', 'dado de baja', 'robado'])
                  ->nullable()
                  ->after('cantidad')
                  ->comment('Estado que tendrá el elemento en la ubicación de destino');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropColumn('nuevo_estado');
        });
    }
};
