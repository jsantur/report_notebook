<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('serenazgos', function (Blueprint $table) {
            if (DB::connection()->getDriverName() !== 'sqlite') {
                $table->fullText(['nombres', 'apellido_paterno', 'apellido_materno', 'dni'], 'idx_serenazgos_fulltext');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serenazgos', function (Blueprint $table) {
            if (DB::connection()->getDriverName() !== 'sqlite') {
                $table->dropFullText('idx_serenazgos_fulltext');
            }
        });
    }
};
