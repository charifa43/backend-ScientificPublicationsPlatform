<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       // 1. FK: professors.team_id → teams.id
        Schema::table('professors', function (Blueprint $table) {
            $table->foreign('team_id')
                  ->references('id')
                  ->on('teams')
                  ->onDelete('set null');
        });
        
        // 2. FK: teams.team_leader_id → professors.id
        // Doit être APRÈS que professors existe
        Schema::table('teams', function (Blueprint $table) {
            $table->foreign('team_leader_id')
                  ->references('id')
                  ->on('professors')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         // L'ordre inverse pour le rollback
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['team_leader_id']);
        });
        
        Schema::table('professors', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
        });
    }
};
