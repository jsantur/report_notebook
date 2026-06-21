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
        Schema::table('asignaciones', function (Blueprint $table) {
            // Optimiza la recuperación de unidades por reporte y tipo (Pilar 1: Escalabilidad)
            $table->index(['reporte_id', 'tipo']);
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            // Optimiza el conteo de flota para el dashboard y reportes (Pilar 1: Escalabilidad)
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignaciones', function (Blueprint $table) {
            $table->dropIndex(['reporte_id', 'tipo']);
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropIndex(['tipo']);
        });
    }
};
