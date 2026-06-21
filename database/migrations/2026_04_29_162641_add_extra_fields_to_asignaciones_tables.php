<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asignaciones_temp', function (Blueprint $table) {
            $table->string('sector')->nullable()->after('conductor');
            $table->string('turnos')->nullable()->after('sector');
            $table->string('jurisdiccion')->nullable()->after('turnos');
        });

        Schema::table('asignaciones', function (Blueprint $table) {
            $table->string('sector')->nullable()->after('placa');
            $table->string('turnos')->nullable()->after('sector');
            $table->string('jurisdiccion')->nullable()->after('turnos');
        });
    }

    public function down(): void
    {
        Schema::table('asignaciones_temp', function (Blueprint $table) {
            $table->dropColumn(['sector', 'turnos', 'jurisdiccion']);
        });

        Schema::table('asignaciones', function (Blueprint $table) {
            $table->dropColumn(['sector', 'turnos', 'jurisdiccion']);
        });
    }
};
