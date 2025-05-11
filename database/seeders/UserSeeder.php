<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Committee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'fullname' => 'Admin Kemahasiswaan',
            'role_id' => 1,
            'institution_id' => 17,
        ]);

        User::create([
            'username' => 'ketuaumum',
            'password' => bcrypt('ketuaumum'),
            'fullname' => 'Budi Santoso',
            'role_id' => 2,
            'institution_id' => 1,
        ]);

        User::create([
            'username' => 'sekretarisumum',
            'password' => bcrypt('sekretarisumum'),
            'fullname' => 'Siti Aminah',
            'role_id' => 3, // Different role_id
            'institution_id' => 1, // Same institution
        ]);

        // Create Ketua Panitia
        $ketuaPanitia = User::create([
            'username' => 'ketuapanitia',
            'password' => bcrypt('ketuapanitia'),
            'fullname' => 'Siti Aminah',
            'role_id' => 4,
            'institution_id' => 1,
        ]);

        // Create Sekretaris Panitia
        $sekretarisPanitia = User::create([
            'username' => 'sekretarispanitia',
            'password' => bcrypt('sekretarispanitia'),
            'fullname' => 'Siti Aminah',
            'role_id' => 5,
            'institution_id' => 1,
        ]);

        // Create a committee and associate users
        Committee::create([
            'institution_id' => 1,
            'name' => 'Panitia Kegiatan',
            'chairman_id' => $ketuaPanitia->id,
            'secretary_id' => $sekretarisPanitia->id,
        ]);

        User::create([
            'username' => 'pembina',
            'password' => bcrypt('pembina'),
            'fullname' => 'Siti Aminah',
            'role_id' => 6, // Different role_id
            'institution_id' => 1, // Same institution
        ]);

        // User::factory(10)->create();
    }
}
