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
        Publisher::create(['name' => 'Penguin Random House']);
        Publisher::create(['name' => 'HarperCollins']);
        Publisher::create(['name' => 'Simon & Schuster']);
    }
}
