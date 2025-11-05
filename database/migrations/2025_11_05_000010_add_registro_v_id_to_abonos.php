<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('abonos', function (Blueprint $table) {
      if (!Schema::hasColumn('abonos', 'registro_v_id')) {
        $table->unsignedBigInteger('registro_v_id')->nullable()->after('id_empleado');
      }
    });

    // Backfill registro_v_id using id_abono linkage on registroV or concepto pattern
    try {
      // 1) Link by registroV.id_abono
      DB::table('abonos')
        ->select('id_abonos')
        ->orderBy('id_abonos')
        ->chunkById(500, function ($rows) {
          foreach ($rows as $row) {
            $ventaId = DB::table('registroV')->where('id_abono', $row->id_abonos)->value('id');
            if ($ventaId) {
              DB::table('abonos')
                ->where('id_abonos', $row->id_abonos)
                ->update(['registro_v_id' => $ventaId]);
            }
          }
        }, 'id_abonos');

      // 2) Link by concepto pattern "Abono por venta #<id>"
      DB::table('abonos')
        ->select('id_abonos', 'concepto')
        ->whereNull('registro_v_id')
        ->orderBy('id_abonos')
        ->chunkById(500, function ($rows) {
          foreach ($rows as $row) {
            if (!is_null($row->concepto)) {
              if (preg_match('/^Abono por venta #(\d+)$/', trim($row->concepto), $m)) {
                $ventaId = (int)($m[1] ?? 0);
                if ($ventaId > 0) {
                  $exists = DB::table('registroV')->where('id', $ventaId)->exists();
                  if ($exists) {
                    DB::table('abonos')
                      ->where('id_abonos', $row->id_abonos)
                      ->update(['registro_v_id' => $ventaId]);
                  }
                }
              }
            }
          }
        }, 'id_abonos');
    } catch (\Throwable $e) {
      // best-effort backfill; continue
    }

    Schema::table('abonos', function (Blueprint $table) {
      // Add FK if not exists; Laravel doesn't provide hasForeignKey, so wrap in try/catch
      try {
        $table->foreign('registro_v_id')
          ->references('id')->on('registroV')
          ->onDelete('cascade');
      } catch (\Throwable $e) {
        // ignore if it already exists
      }
    });
  }

  public function down(): void
  {
    Schema::table('abonos', function (Blueprint $table) {
      try {
        $table->dropForeign(['registro_v_id']);
      } catch (\Throwable $e) {
        // ignore
      }
      if (Schema::hasColumn('abonos', 'registro_v_id')) {
        $table->dropColumn('registro_v_id');
      }
    });
  }
};
