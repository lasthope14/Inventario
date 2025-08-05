<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('inventarios', function (Blueprint $table) {
        $table->dropColumn('cantidad');
    });
}

public function down()
{
    Schema::table('inventarios', function (Blueprint $table) {
        $table->integer('cantidad')->default(0);
    });
}
};
