<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\Letter;
use App\Models\LetterCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Letter>
 */
class LetterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'verification_id' => fake()->unique()->uuid(),
            'code' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{6}'),
            'category_id' => LetterCategory::factory(),
            'creator_id' => User::factory(),
            'date' => fake()->date(),
            'file_path' => fake()->filePath(),
            'file_hash' => fake()->sha256(),
            'status' => fake()->randomElement(['pending', 'signed', 'rejected']),
        ];
    }
}
