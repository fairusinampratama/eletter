<?php

namespace Database\Seeders;

use App\Models\Signature;
use App\Models\Letter;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SignatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users by role
        $usersByRole = [
            2 => User::where('role_id', 2)->first(), // Ketua Umum
            3 => User::where('role_id', 3)->first(), // Sekretaris Umum
            4 => User::where('role_id', 4)->first(), // Ketua Panitia
            5 => User::where('role_id', 5)->first(), // Sekretaris Panitia
            6 => User::where('role_id', 6)->first(), // Pembina
        ];

        foreach (Letter::all() as $letter) {
            $category = $letter->category;
            if ($category && $category->committee_id !== null) {
                // Committee letter: Sekretaris Panitia, Ketua Panitia, Ketua Umum, Pembina
                $roleOrder = [5, 4, 2, 6];
                foreach ($roleOrder as $i => $roleId) {
                    $user = $usersByRole[$roleId] ?? null;
                    if ($user) {
                        Signature::create([
                            'letter_id' => $letter->id,
                            'signer_id' => $user->id,
                            'order' => $i + 1,
                            'signature' => null,
                            'signed_at' => null,
                            'qr_metadata' => json_encode(['info' => 'QR for ' . $user->username]),
                        ]);
                    }
                }
            } else {
                // General letter: Sekretaris Umum, Ketua Umum, Pembina
                $roleOrder = [3, 2, 6];
                foreach ($roleOrder as $i => $roleId) {
                    $user = $usersByRole[$roleId] ?? null;
                    if ($user) {
                        Signature::create([
                            'letter_id' => $letter->id,
                            'signer_id' => $user->id,
                            'order' => $i + 1,
                            'signature' => null,
                            'signed_at' => null,
                            'qr_metadata' => json_encode(['info' => 'QR for ' . $user->username]),
                        ]);
                    }
                }
            }
        }
    }
}
