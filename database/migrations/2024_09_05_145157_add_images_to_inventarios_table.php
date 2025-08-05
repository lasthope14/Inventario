<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImagesToInventariosTable extends Migration
{
    public function up()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->string('imagen_principal')->nullable();
            $table->string('imagen_secundaria')->nullable();
        });
    }

    public function down()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->dropColumn('imagen_principal');
            $table->dropColumn('imagen_secundaria');
        });
    }
}