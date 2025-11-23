<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            ['name' => 'Work', 'color' => '#bc0101', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'School', 'color' => '#1134e4', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shopping', 'color' => '#ecc789', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Personal', 'color' => '#6b0edd', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

