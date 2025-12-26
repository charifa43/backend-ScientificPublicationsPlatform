<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicationsTable extends Migration
{
    public function up()
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('publication_year');
            $table->enum('type', ['research', 'conference', 'chapter', 'thesis', 'other']);
            $table->string('doi')->nullable();
            $table->string('publication_url')->nullable();
            $table->text('abstract')->nullable();
            $table->text('external_authors')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('publications');
    }
}