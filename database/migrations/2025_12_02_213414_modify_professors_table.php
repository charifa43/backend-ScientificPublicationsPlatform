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
        Schema::table('professors', function (Blueprint $table) {
            // Ajouter les champs manquants si nécessaire
            if (!Schema::hasColumn('professors', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('professors', 'remember_token')) {
                $table->rememberToken()->nullable()->after('password');
            }
            
            if (!Schema::hasColumn('professors', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('remember_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professors', function (Blueprint $table) {
            $table->dropColumn(['password', 'remember_token', 'email_verified_at']);
        });
    }
};