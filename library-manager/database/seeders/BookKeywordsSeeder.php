<?php

namespace Database\Seeders;

use App\Models\BookKeyword;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookKeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BookKeyword::create(['book_id' => 1, 'keyword_id' => 1]);
        BookKeyword::create(['book_id' => 2, 'keyword_id' => 2]);
        BookKeyword::create(['book_id' => 2, 'keyword_id' => 3]);
        BookKeyword::create(['book_id' => 1, 'keyword_id' => 4]);
        BookKeyword::create(['book_id' => 2, 'keyword_id' => 5]);
        BookKeyword::create(['book_id' => 2, 'keyword_id' => 6]);
        BookKeyword::create(['book_id' => 1, 'keyword_id' => 7]);
        BookKeyword::create(['book_id' => 2, 'keyword_id' => 8]);
        BookKeyword::create(['book_id' => 2, 'keyword_id' => 9]);
        BookKeyword::create(['book_id' => 1, 'keyword_id' => 10]);
        BookKeyword::create(['book_id' => 2, 'keyword_id' => 11]);
        BookKeyword::create(['book_id' => 2, 'keyword_id' => 12]);

    }
}
