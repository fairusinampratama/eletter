<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\Committee;
use App\Models\LetterCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class LetterCategoryFactory extends Factory
{
    protected $model = LetterCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'institution_id' => $this->faker->numberBetween(1, 16),
            'committee_id' => Committee::factory(),
        ];
    }
}
