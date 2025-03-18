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
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id('id_presupuesto');
            $table->foreignId('id_cliente')->constrained('clientes', 'id_cliente')->onDelete('cascade');
            $table->date('f_presupuesto');
            $table->date('validez');
            $table->integer('descuento');
            $table->integer('iva');
            $table->string('estado');
            $table->json('items');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
