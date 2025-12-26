<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id(); // ID auto-incrémenté
            $table->string('name'); // Nom de l'équipe (ex: "IA et Big Data")
            $table->text('description')->nullable(); // Description optionnelle
            $table->foreignId('team_leader_id')->nullable();
            $table->timestamps(); // created_at et updated_at automatiques
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
    }
}
