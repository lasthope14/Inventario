<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('inventarios', function (Blueprint $table) {
        $table->renameColumn('ubicacion', 'ubicacion_id');
        $table->foreign('ubicacion_id')->references('id')->on('ubicaciones');
    });
}

public function down()
{
    Schema::table('inventarios', function (Blueprint $table) {
        $table->renameColumn('ubicacion_id', 'ubicacion');
        $table->dropForeign(['ubicacion_id']);
    });
}
};
