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
        Schema::create('reporte_unidades', function (Blueprint $table) {
            $table->id();
            $table->string('unidad');
            $table->string('placa');
            $table->decimal('km', 10, 2)->default(0);
            $table->integer('ap')->default(0); // Auxilio Público en minutos
            $table->integer('po')->default(0); // Personal Operativo
            $table->date('fecha');
            $table->string('turno');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_unidades');
    }
};
