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
        Schema::table('reportes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('turno')->constrained('users')->nullOnDelete();
        });

        // Asignar el primer usuario existente a los reportes históricos
        $firstUser = \Illuminate\Support\Facades\DB::table('users')->orderBy('id')->first();
        if ($firstUser) {
            \Illuminate\Support\Facades\DB::table('reportes')
                ->whereNull('user_id')
                ->update(['user_id' => $firstUser->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reportes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
