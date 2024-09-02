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
        Author::create([
            'name' => 'George Orwell',
            'bio' => 'George Orwell was an English novelist, essayist, journalist, and critic, best known for his dystopian novels "1984" and "Animal Farm".'
        ]);

        Author::create([
            'name' => 'J.K. Rowling',
            'bio' => 'J.K. Rowling is a British author, best known for writing the "Harry Potter" fantasy series, one of the best-selling book series in history.'
        ]);

        Author::create([
            'name' => 'J.R.R. Tolkien',
            'bio' => 'J.R.R. Tolkien was an English writer, poet, and professor, best known as the author of the classic high fantasy works "The Hobbit" and "The Lord of the Rings".'
        ]);
    }
}
