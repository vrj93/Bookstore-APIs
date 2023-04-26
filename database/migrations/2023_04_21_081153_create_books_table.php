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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('genre_id');
            $table->longtext('description');
            $table->string('isbn', 20);
            $table->date('published');
            $table->unsignedBigInteger('publisher_id');
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('authors');
            $table->foreign('genre_id')->references('id')->on('genres');
            $table->foreign('publisher_id')->references('id')->on('publishers');
            
            $table->index('title');
            $table->index('author_id');
            $table->index('genre_id');
            $table->index('isbn');
            $table->index('publisher_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
};
