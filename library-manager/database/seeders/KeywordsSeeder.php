<?php

namespace Database\Seeders;

use App\Models\Keyword;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Keyword::create(['keyword' => 'Dystopian', 'language_id' => 1]);
        Keyword::create(['keyword' => 'Magic', 'language_id' => 1]);
        Keyword::create(['keyword' => 'Adventure', 'language_id' => 1]);

        Keyword::create(['keyword' => 'Dystopique', 'language_id' => 2]);
        Keyword::create(['keyword' => 'Magie', 'language_id' => 2]);
        Keyword::create(['keyword' => 'Aventure', 'language_id' => 2]);

        Keyword::create(['keyword' => 'Distópico', 'language_id' => 3]);
        Keyword::create(['keyword' => 'Magia', 'language_id' => 3]);
        Keyword::create(['keyword' => 'Aventura', 'language_id' => 3]);

        Keyword::create(['keyword' => 'Disztópikus', 'language_id' => 4]);
        Keyword::create(['keyword' => 'Mágia', 'language_id' => 4]);
        Keyword::create(['keyword' => 'Kaland', 'language_id' => 4]);
    }
}
