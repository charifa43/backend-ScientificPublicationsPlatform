<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfessorsTable extends Migration
{
    public function up()
    {
        Schema::create('professors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->text('password');
            $table->string('phone')->nullable();
            $table->string('departement')->nullable();
            $table->string('specialty')->nullable();
            $table->enum('grade', ['DOCTORANT', 'DOCTOR'])->nullable();
            $table->enum('role', ['professor', 'director'])
                  ->default('professor');
            $table->foreignId('team_id')->nullable();
            $table->rememberToken();
             $table->timestamp('email_verified_at')->nullable();
             $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('professors');
    }
}