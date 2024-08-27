<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Author::create(['name' => 'George Orwell']);
        Author::create(['name' => 'J.K. Rowling']);
        Author::create(['name' => 'J.R.R. Tolkien']);
    }
}
