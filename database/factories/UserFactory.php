<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Services\ECDSAService;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate key pair for new user
        $ecdsaService = app(ECDSAService::class);
        $keyPair = $ecdsaService->generateKeyPair();

        $roleId = $this->faker->randomElement([2, 3, 6]);
        $currentYear = date('Y');

        // Check if there's already an active user for this role in current year
        $hasActiveUser = \App\Models\User::where('role_id', $roleId)
            ->where('year', $currentYear)
            ->where('is_active', true)
            ->exists();

        return [
            'username' => $this->faker->unique()->userName,
            'password' => Hash::make('password'),
            'fullname' => $this->faker->name,
            'role_id' => $roleId,
            'institution_id' => $this->faker->numberBetween(1, 16),
            'public_key' => $keyPair['publicKey'],
            'private_key' => $keyPair['privateKey'],
            'year' => $currentYear,
            'is_active' => !$hasActiveUser,
        ];
    }

    // Add specific states for committee roles
    public function asCommitteeChairman()
    {
        return $this->state([
            'role_id' => 4, // Specific role for chairmen
        ]);
    }

    public function asCommitteeSecretary()
    {
        return $this->state([
            'role_id' => 5, // Specific role for secretaries
        ]);
    }
}
