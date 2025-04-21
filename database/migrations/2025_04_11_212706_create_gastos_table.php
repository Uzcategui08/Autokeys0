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
        Schema::create('gastos', function (Blueprint $table) {
            $table->id('id_gastos');
            $table->date('f_gastos');
            $table->integer('id_tecnico');
            $table->text('descripcion');
            $table->string('subcategoria');
            $table->decimal('valor', 10, 2);
            $table->enum('estatus', ['pagado', 'pendiente', 'parcialmente_pagado']);
            $table->json('pagos')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
