<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table('gastos', function (Blueprint $table) {
      $table->boolean('en_vanes')->default(false)->after('pagos');
    });

    Schema::table('costos', function (Blueprint $table) {
      $table->boolean('en_vanes')->default(false)->after('pagos');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('gastos', function (Blueprint $table) {
      $table->dropColumn('en_vanes');
    });

    Schema::table('costos', function (Blueprint $table) {
      $table->dropColumn('en_vanes');
    });
  }
};
