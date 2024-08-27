<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::create(['language_name' => 'English', 'language_code' => 'en']);
        Language::create(['language_name' => 'French', 'language_code' => 'fr']);
        Language::create(['language_name' => 'Spanish', 'language_code' => 'es']);
    }
}
