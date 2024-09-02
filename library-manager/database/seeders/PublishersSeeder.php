<?php

namespace Database\Seeders;

use App\Models\Publisher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublishersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Publisher::create([
            'name' => 'Penguin Random House',
            'address' => '1745 Broadway, New York, NY 10019, USA'
        ]);

        Publisher::create([
            'name' => 'HarperCollins',
            'address' => '195 Broadway, New York, NY 10007, USA'
        ]);

        Publisher::create([
            'name' => 'Simon & Schuster',
            'address' => '1230 Avenue of the Americas, New York, NY 10020, USA'
        ]);
    }
}
