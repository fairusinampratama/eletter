<?php

namespace Database\Factories;

use App\Models\Letter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Signature>
 */
class SignatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'letter_id' => Letter::factory(),
            'signer_id' => User::factory(),
            'signature' => fake()->optional()->numberBetween(1, 1000),
            'public_key' => fake()->optional()->sha1(),
            'signed_at' => fake()->optional()->dateTime(),
        ];
    }
}
