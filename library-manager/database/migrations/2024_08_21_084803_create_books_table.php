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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('author_id')->constrained('authors');
            $table->foreignId('genre_id')->constrained('genres');
            $table->foreignId('publisher_id')->constrained('publishers');
            $table->foreignId('default_language_id')->constrained('languages');
            $table->string('cover_image')->nullable();
            $table->timestamps();

            $table->index('author_id');
            $table->index('genre_id');
            $table->index('publisher_id');
            $table->index('default_language_id');
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
