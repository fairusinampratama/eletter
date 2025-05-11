<?php

namespace Database\Seeders;

use App\Models\LetterCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LetterCategorySeeder extends Seeder
{
    public function run(): void
    {
        LetterCategory::factory()->count(10)->create();
    }
}
