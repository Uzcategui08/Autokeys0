<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trabajos', function (Blueprint $table) {
            $table->json('traducciones')->nullable()->after('nombre');
        });
    }

    public function down()
    {
        Schema::table('trabajos', function (Blueprint $table) {
            $table->dropColumn('traducciones');
        });
    }
};
