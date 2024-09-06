<?php

namespace Database\Seeders;

use App\Models\GenreTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenreTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GenreTranslation::create(['genre_id' => '1', 'language_id' => '2', 'translated_name' => 'La fiction']);
        GenreTranslation::create(['genre_id' => '1', 'language_id' => '3', 'translated_name' => 'Ficción']);
        GenreTranslation::create(['genre_id' => '1', 'language_id' => '4', 'translated_name' => 'Szépirodalom']);
        GenreTranslation::create(['genre_id' => '2', 'language_id' => '2', 'translated_name' => 'La science fiction']);
        GenreTranslation::create(['genre_id' => '2', 'language_id' => '3', 'translated_name' => 'Ciencia Ficción']);
        GenreTranslation::create(['genre_id' => '2', 'language_id' => '4', 'translated_name' => 'Tudományos-fantasztikus']);
        GenreTranslation::create(['genre_id' => '3', 'language_id' => '2', 'translated_name' => 'Fantaisie']);
        GenreTranslation::create(['genre_id' => '3', 'language_id' => '3', 'translated_name' => 'Fantasía']);
        GenreTranslation::create(['genre_id' => '3', 'language_id' => '4', 'translated_name' => 'Fantasy']);

    }
}
