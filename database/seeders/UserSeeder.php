<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Committee;
use App\Services\ECDSAService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ecdsaService = app(ECDSAService::class);

        // Admin
        $adminKeyPair = $ecdsaService->generateKeyPair();
        User::create([
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'fullname' => 'Admin Kemahasiswaan',
            'role_id' => 1,
            'institution_id' => 17,
            'public_key' => $adminKeyPair['publicKey'],
            'private_key' => $adminKeyPair['privateKey'],
        ]);

        // Ketua Umum
        $ketuaUmumKeyPair = $ecdsaService->generateKeyPair();
        User::create([
            'username' => 'ketuaumum',
            'password' => bcrypt('ketuaumum'),
            'fullname' => 'Budi Santoso',
            'role_id' => 2,
            'institution_id' => 1,
            'public_key' => $ketuaUmumKeyPair['publicKey'],
            'private_key' => $ketuaUmumKeyPair['privateKey'],
        ]);

        // Sekretaris Umum
        $sekretarisUmumKeyPair = $ecdsaService->generateKeyPair();
        User::create([
            'username' => 'sekretarisumum',
            'password' => bcrypt('sekretarisumum'),
            'fullname' => 'Siti Aminah',
            'role_id' => 3,
            'institution_id' => 1,
            'public_key' => $sekretarisUmumKeyPair['publicKey'],
            'private_key' => $sekretarisUmumKeyPair['privateKey'],
        ]);

        // Ketua Panitia
        $ketuaPanitiaKeyPair = $ecdsaService->generateKeyPair();
        $ketuaPanitia = User::create([
            'username' => 'ketuapanitia',
            'password' => bcrypt('ketuapanitia'),
            'fullname' => 'Siti Aminah',
            'role_id' => 4,
            'institution_id' => 1,
            'public_key' => $ketuaPanitiaKeyPair['publicKey'],
            'private_key' => $ketuaPanitiaKeyPair['privateKey'],
        ]);

        // Sekretaris Panitia
        $sekretarisPanitiaKeyPair = $ecdsaService->generateKeyPair();
        $sekretarisPanitia = User::create([
            'username' => 'sekretarispanitia',
            'password' => bcrypt('sekretarispanitia'),
            'fullname' => 'Siti Aminah',
            'role_id' => 5,
            'institution_id' => 1,
            'public_key' => $sekretarisPanitiaKeyPair['publicKey'],
            'private_key' => $sekretarisPanitiaKeyPair['privateKey'],
        ]);

        // Create a committee and associate users
        Committee::create([
            'institution_id' => 1,
            'name' => 'Panitia Kegiatan',
            'chairman_id' => $ketuaPanitia->id,
            'secretary_id' => $sekretarisPanitia->id,
        ]);

        // Pembina
        $pembinaKeyPair = $ecdsaService->generateKeyPair();
        User::create([
            'username' => 'pembina',
            'password' => bcrypt('pembina'),
            'fullname' => 'Siti Aminah',
            'role_id' => 6,
            'institution_id' => 1,
            'public_key' => $pembinaKeyPair['publicKey'],
            'private_key' => $pembinaKeyPair['privateKey'],
        ]);

        // User::factory(10)->create();
    }
}
