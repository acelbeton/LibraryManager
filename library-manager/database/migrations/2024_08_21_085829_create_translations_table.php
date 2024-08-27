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
        Schema::create('translations', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->id();
            $table->foreignId('book_id')->constrained('books');
            $table->foreignId('language_id')->constrained('languages');
            $table->string('translated_title');
            $table->text('translated_description')->nullable();
            $table->timestamps();

            $table->index('book_id');
            $table->index('language_id');
            $table->index(['book_id', 'language_id']);

            $table->unique(['book_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
