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
        Schema::create('asignaciones_temp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('unidad_id');
            $table->string('tipo')->default('vehicular');
            $table->string('subtipo')->nullable();
            $table->string('placa')->nullable();
            $table->string('conductor')->nullable();
            $table->decimal('km', 10, 2)->default(0);
            $table->integer('ap')->default(0);
            $table->integer('po')->default(0);
            $table->timestamps();
            
            // Índice para búsquedas rápidas por usuario
            $table->index(['user_id', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones_temp');
    }
};
