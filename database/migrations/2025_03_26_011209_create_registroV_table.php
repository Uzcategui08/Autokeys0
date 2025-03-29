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
            $table->string('tecnico');
            $table->string('trabajo');
            $table->string('cliente');
            $table->string('telefono');
            $table->decimal('valor_v');
            $table->string('estatus');
            $table->string('metodo_p');
            $table->string('titular_c');
            $table->string('cobro');
            $table->string('descripcion_ce');
            $table->decimal('monto_ce');
            $table->string('metodo_pce');
            $table->string('porcentaje_c');
            $table->string('marca');
            $table->string('modelo');
            $table->integer('año');
            $table->json('items');
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
