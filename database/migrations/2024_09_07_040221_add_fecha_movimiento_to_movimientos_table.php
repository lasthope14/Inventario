<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaMovimientoToMovimientosTable extends Migration
{
    public function up()
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->datetime('fecha_movimiento')->after('motivo')->nullable();
        });
    }

    public function down()
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropColumn('fecha_movimiento');
        });
    }
}