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
            'book_id' => 2,
            'language_id' => 2,
            'translated_title' => 'Harry Potter à l\'école des sorciers',
            'translated_description' => 'Le début du voyage d\'un jeune sorcier.',
        ]);
    }
}
