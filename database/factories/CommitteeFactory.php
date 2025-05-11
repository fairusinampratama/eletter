<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Committee>
 */
class CommitteeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $institution = Institution::inRandomOrder()->first() ?? Institution::factory()->create();

        return [
            'institution_id' => $institution->id,
            'name' => $this->faker->unique()->company,
            'chairman_id' => User::factory()->asCommitteeChairman()->create(['institution_id' => $institution->id]),
            'secretary_id' => User::factory()->asCommitteeSecretary()->create(['institution_id' => $institution->id]),
        ];
    }
}
