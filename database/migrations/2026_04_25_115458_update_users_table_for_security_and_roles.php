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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('email')->nullable()->change();
            $table->enum('role', ['admin', 'user'])->default('user')->after('password');
            $table->string('security_question')->nullable()->after('role');
            $table->string('security_answer')->nullable()->after('security_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'security_question', 'security_answer']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
