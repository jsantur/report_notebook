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
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->time('hora');
            $table->enum('turno', ['Mañana', 'Tarde', 'Noche']);
            $table->unsignedBigInteger('supervisor_campo_id');
            $table->unsignedBigInteger('supervisor_camaras_id');
            
            // Secciones de categorías (textareas)
            $table->text('ocurrencias_relevo')->nullable();
            $table->text('distribucion_personal_camaras')->nullable();
            $table->text('distribucion_personal_campo')->nullable();
            $table->text('reporte_personal_patrullando')->nullable();
            $table->text('visualizaciones_resaltantes')->nullable();
            
            $table->timestamps();

            // Llaves foráneas a la tabla de serenazgos
            $table->foreign('supervisor_campo_id')->references('id')->on('serenazgos');
            $table->foreign('supervisor_camaras_id')->references('id')->on('serenazgos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
