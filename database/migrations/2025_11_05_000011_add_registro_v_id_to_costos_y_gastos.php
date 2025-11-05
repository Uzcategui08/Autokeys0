<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('costos', function (Blueprint $table) {
      if (!Schema::hasColumn('costos', 'registro_v_id')) {
        $table->unsignedBigInteger('registro_v_id')->nullable()->after('id_tecnico');
      }
    });

    Schema::table('gastos', function (Blueprint $table) {
      if (!Schema::hasColumn('gastos', 'registro_v_id')) {
        $table->unsignedBigInteger('registro_v_id')->nullable()->after('id_tecnico');
      }
    });

    // Backfill desde arrays en registroV.costos y registroV.gastos
    try {
      DB::table('registroV')->orderBy('id')->chunkById(500, function ($ventas) {
        foreach ($ventas as $venta) {
          // costos
          $costos = $venta->costos;
          if (is_string($costos)) {
            $decoded = json_decode($costos, true);
            if (json_last_error() === JSON_ERROR_NONE) {
              $costos = $decoded;
            }
          }
          if (is_array($costos)) {
            foreach ($costos as $cid) {
              DB::table('costos')->where('id_costos', $cid)->update(['registro_v_id' => $venta->id]);
            }
          }

          // gastos
          $gastos = $venta->gastos;
          if (is_string($gastos)) {
            $decoded = json_decode($gastos, true);
            if (json_last_error() === JSON_ERROR_NONE) {
              $gastos = $decoded;
            }
          }
          if (is_array($gastos)) {
            foreach ($gastos as $gid) {
              DB::table('gastos')->where('id_gastos', $gid)->update(['registro_v_id' => $venta->id]);
            }
          }
        }
      }, 'id');
    } catch (\Throwable $e) {
      // best effort; continuar
    }

    Schema::table('costos', function (Blueprint $table) {
      try {
        $table->foreign('registro_v_id')
          ->references('id')->on('registroV')
          ->onDelete('cascade');
      } catch (\Throwable $e) {
        // ignorar si ya existe
      }
    });

    Schema::table('gastos', function (Blueprint $table) {
      try {
        $table->foreign('registro_v_id')
          ->references('id')->on('registroV')
          ->onDelete('cascade');
      } catch (\Throwable $e) {
        // ignorar si ya existe
      }
    });
  }

  public function down(): void
  {
    Schema::table('costos', function (Blueprint $table) {
      try {
        $table->dropForeign(['registro_v_id']);
      } catch (\Throwable $e) {
      }
      if (Schema::hasColumn('costos', 'registro_v_id')) {
        $table->dropColumn('registro_v_id');
      }
    });

    Schema::table('gastos', function (Blueprint $table) {
      try {
        $table->dropForeign(['registro_v_id']);
      } catch (\Throwable $e) {
      }
      if (Schema::hasColumn('gastos', 'registro_v_id')) {
        $table->dropColumn('registro_v_id');
      }
    });
  }
};
