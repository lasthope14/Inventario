<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowNullValorInInventariosTable extends Migration
{
    public function up()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->decimal('valor', 10, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->decimal('valor', 10, 2)->nullable(false)->change();
        });
    }
}