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
        Schema::create('pnominas', function (Blueprint $table) {
            $table->id('id_pnomina');
            $table->foreignId('id_tnomina')->constrained('tnominas', 'id_tnomina')->onDelete('cascade');
            $table->date('inicio');
            $table->date('fin');
            $table->boolean('activo')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pnominas');
    }
};
