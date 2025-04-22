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
        Schema::create('nempleados', function (Blueprint $table) {
            $table->id('id_nempleado');
            $table->foreignId('id_pnomina')->constrained('pnominas', 'id_pnomina')->onDelete('cascade');
            $table->foreignId('id_empleado')->constrained('empleados', 'id_empleado')->onDelete('cascade');
            $table->decimal('total_descuentos', 10, 2)->default(0);
            $table->decimal('total_abonos', 10, 2)->default(0);
            $table->decimal('total_prestamos', 10, 2)->default(0);
            $table->decimal('total_costos', 10, 2)->default(0);
            $table->decimal('total_pagado', 10, 2);
            $table->integer('metodo_pago');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nempleados');
    }
};
