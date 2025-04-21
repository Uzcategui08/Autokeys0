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
        Schema::create('abonos', function (Blueprint $table) {
            $table->id('id_abonos');
            $table->foreignId('id_empleado')->constrained('empleados', 'id_empleado')->onDelete('cascade');
            $table->string('concepto');
            $table->decimal('valor', 10, 2);
            $table->date('a_fecha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonos');
    }
};
