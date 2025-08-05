<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowNullFechaCompraAndNumeroFacturaInInventariosTable extends Migration
{
    public function up()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->date('fecha_compra')->nullable()->change();
            $table->string('numero_factura')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->date('fecha_compra')->nullable(false)->change();
            $table->string('numero_factura')->nullable(false)->change();
        });
    }
}