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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id('id_empleado');
            $table->foreignId('id_tnomina')->constrained('tnominas', 'id_tnomina')->onDelete('cascade');
            $table->string('nombre');
            $table->integer('cedula');
            $table->string('cargo');
            $table->decimal('salario_base', 10,2);
            $table->integer('metodo_pago');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
