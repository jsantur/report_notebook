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
            $table->string('cod_po')->nullable()->after('po');
        });
        
        Schema::table('asignaciones_temp', function (Blueprint $table) {
            $table->string('cod_po')->nullable()->after('po');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignaciones', function (Blueprint $table) {
            $table->dropColumn('cod_po');
        });
        
        Schema::table('asignaciones_temp', function (Blueprint $table) {
            $table->dropColumn('cod_po');
        });
    }
};
