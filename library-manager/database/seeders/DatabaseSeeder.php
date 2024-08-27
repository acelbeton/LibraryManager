<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PublishersSeeder::class,
            AuthorsSeeder::class,
            LanguagesSeeder::class,
            GenresSeeder::class,
            GenreTranslationsSeeder::class,
            BooksSeeder::class,
            TranslationsSeeder::class,
            KeywordsSeeder::class,
            BookKeywordsSeeder::class,
        ]);
    }
}
