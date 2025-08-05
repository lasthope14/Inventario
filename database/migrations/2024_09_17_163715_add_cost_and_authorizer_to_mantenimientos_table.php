<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->decimal('costo', 10, 2)->nullable()->after('resultado');
            $table->string('autorizado_por')->nullable()->after('costo');
        });
    }
    
    public function down()
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->dropColumn(['costo', 'autorizado_por']);
        });
    }
};
