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
        Schema::table('teams', function (Blueprint $table) {
            // Ajouter le champ 'field' (filière) - DÉJÀ PRÉSENT
            $table->enum('field', ['Maths', 'Informatique', 'IA', 'Physique', 'Génie Civil'])
                  ->default('Informatique')
                  ->after('name');
            
            $table->string('domain')->nullable()->after('field');
            $table->date('creation_date')->nullable()->after('domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['field', 'domain', 'creation_date']);
        });
    }
};