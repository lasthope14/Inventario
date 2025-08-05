<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevertedToImportLogsTable extends Migration
{
    public function up()
    {
        Schema::table('import_logs', function (Blueprint $table) {
            $table->boolean('reverted')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('import_logs', function (Blueprint $table) {
            $table->dropColumn('reverted');
        });
    }
}