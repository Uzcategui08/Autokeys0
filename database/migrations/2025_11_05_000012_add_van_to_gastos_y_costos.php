<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            if (!Schema::hasColumn('gastos', 'van')) {
                $table->string('van')->nullable()->after('en_vanes');
            }
        });

        Schema::table('costos', function (Blueprint $table) {
            if (!Schema::hasColumn('costos', 'van')) {
                $table->string('van')->nullable()->after('en_vanes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            if (Schema::hasColumn('gastos', 'van')) {
                $table->dropColumn('van');
            }
        });

        Schema::table('costos', function (Blueprint $table) {
            if (Schema::hasColumn('costos', 'van')) {
                $table->dropColumn('van');
            }
        });
    }
};
