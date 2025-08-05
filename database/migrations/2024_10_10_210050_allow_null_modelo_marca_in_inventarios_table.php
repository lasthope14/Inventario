<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowNullModeloMarcaInInventariosTable extends Migration
{
    public function up()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->string('modelo')->nullable()->change();
            $table->string('marca')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->string('modelo')->nullable(false)->change();
            $table->string('marca')->nullable(false)->change();
        });
    }
}