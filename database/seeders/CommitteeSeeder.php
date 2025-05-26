<?php

namespace Database\Seeders;

use App\Models\Committee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommitteeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users
        $ketuaPanitia = User::where('username', 'ketuapanitia')->first();
        $sekretarisPanitia = User::where('username', 'sekretarispanitia')->first();

        // Create committees
        Committee::create([
            'institution_id' => 1, // Resimen Mahasiswa
            'name' => 'Panitia Latihan Dasar Kepemimpinan',
            'chairman_id' => $ketuaPanitia->id,
            'secretary_id' => $sekretarisPanitia->id,
        ]);

        Committee::create([
            'institution_id' => 2, // Simfoni SM
            'name' => 'Panitia Konser Amal',
            'chairman_id' => $ketuaPanitia->id,
            'secretary_id' => $sekretarisPanitia->id,
        ]);

        Committee::create([
            'institution_id' => 3, // UAPM Inovasi
            'name' => 'Panitia Lomba Inovasi',
            'chairman_id' => $ketuaPanitia->id,
            'secretary_id' => $sekretarisPanitia->id,
        ]);

        Committee::create([
            'institution_id' => 4, // Jhepret Club
            'name' => 'Panitia Turnamen Fotografi',
            'chairman_id' => $ketuaPanitia->id,
            'secretary_id' => $sekretarisPanitia->id,
        ]);

        Committee::create([
            'institution_id' => 5, // KSR PMI
            'name' => 'Panitia Donor Darah',
            'chairman_id' => $ketuaPanitia->id,
            'secretary_id' => $sekretarisPanitia->id,
        ]);
    }
}
