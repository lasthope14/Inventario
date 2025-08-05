<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRealizadoPorIdToMovimientosTable extends Migration
{
    public function up()
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->unsignedBigInteger('realizado_por_id')->nullable()->after('motivo');
            $table->foreign('realizado_por_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropForeign(['realizado_por_id']);
            $table->dropColumn('realizado_por_id');
        });
    }
}