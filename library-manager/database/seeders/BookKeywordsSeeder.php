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
    }
}
