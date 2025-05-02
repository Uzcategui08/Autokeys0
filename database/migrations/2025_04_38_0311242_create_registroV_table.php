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
        Schema::create('registroV', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_h');
            $table->unsignedBigInteger('id_empleado')->constrained('empleados', 'id_empleado')->onDelete('cascade');;
            $table->string('trabajo');
            $table->string('cliente');
            $table->string('telefono');
            $table->decimal('valor_v');
            $table->string('estatus');
            $table->string('titular_c');
            $table->json('pagos')->nullable();
            $table->string('descripcion_ce')->nullable();
            $table->string('lugarventa'); 
            $table->decimal('monto_ce')->nullable();
            $table->string('cobro')->nullable();
            $table->string('porcentaje_c');
            $table->string('metodo_pce')->nullable();
            $table->string('marca');
            $table->string('modelo');
            $table->integer('año');
            $table->json('items');
            $table->json('costos')->nullable();
            $table->json('gastos')->nullable();
            $table->integer('id_abono')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registroV');
    }
};
