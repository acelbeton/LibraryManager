<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Translation::create([
            'book_id' => 1,
            'language_id' => 2,
            'translated_title' => '1984 (French)',
            'translated_description' => 'Roman dystopique situé à Airstrip One.',
        ]);

        Translation::create([
            'book_id' => 1,
            'language_id' => 3,
            'translated_title' => '1984 (Spanish)',
            'translated_description' => 'Novela distópica situada en Airstrip One.',
        ]);

        Translation::create([
            'book_id' => 1,
            'language_id' => 4,
            'translated_title' => '1984 (Hungarian)',
            'translated_description' => 'Disztópikus regény, amely Airstrip One-ban játszódik.',
        ]);

        Translation::create([
            'book_id' => 2,
            'language_id' => 2,
            'translated_title' => 'Harry Potter à l\'école des sorciers',
            'translated_description' => 'Le début du voyage d\'un jeune sorcier.',
        ]);

        Translation::create([
            'book_id' => 2,
            'language_id' => 3,
            'translated_title' => 'Harry Potter y la piedra filosofal',
            'translated_description' => 'El comienzo del viaje de un joven mago.',
        ]);

        Translation::create([
            'book_id' => 2,
            'language_id' => 4,
            'translated_title' => 'Harry Potter és a bölcsek köve',
            'translated_description' => 'Egy fiatal varázsló utazásának kezdete.',
        ]);
    }
}
