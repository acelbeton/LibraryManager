<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::create([
            'title' => '1984',
            'description' => 'Dystopian novel set in Airstrip One.',
            'author_id' => 1,
            'genre_id' => 1,
            'publisher_id' => 1,
            'default_language_id' => 1,
            'cover_image' => 'cover_images/1984.jpg',
        ]);

        Book::create([
            'title' => 'Harry Potter and the Sorcerer\'s Stone',
            'description' => 'A young wizard\'s journey begins.',
            'author_id' => 2,
            'genre_id' => 3,
            'publisher_id' => 2,
            'default_language_id' => 1,
            'cover_image' => 'cover_images/hpsorcererstone.jpg',
        ]);
    }
}
