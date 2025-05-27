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
        $currentYear = date('Y');

        // Admin
        $adminKeyPair = $ecdsaService->generateKeyPair();
        User::create([
            'username' => 'admin123',
            'password' => bcrypt('admin123'),
            'fullname' => 'Admin Kemahasiswaan',
            'role_id' => 1,
            'institution_id' => 17,
            'public_key' => $adminKeyPair['publicKey'],
            'private_key' => $adminKeyPair['privateKey'],
            'year' => $currentYear,
            'is_active' => true,
        ]);

        // Ketua Umum
        $ketuaUmumKeyPair = $ecdsaService->generateKeyPair();
        User::create([
            'username' => 'ketuaumum',
            'password' => bcrypt('ketuaumum'),
            'fullname' => 'M. Sirajuddin Al Haqiqiy',
            'role_id' => 2,
            'institution_id' => 1,
            'public_key' => $ketuaUmumKeyPair['publicKey'],
            'private_key' => $ketuaUmumKeyPair['privateKey'],
            'year' => $currentYear,
            'is_active' => true,
        ]);

        // Sekretaris Umum
        $sekretarisUmumKeyPair = $ecdsaService->generateKeyPair();
        User::create([
            'username' => 'sekumsekum',
            'password' => bcrypt('sekumsekum'),
            'fullname' => 'Agam Jatimulya',
            'role_id' => 3,
            'institution_id' => 1,
            'public_key' => $sekretarisUmumKeyPair['publicKey'],
            'private_key' => $sekretarisUmumKeyPair['privateKey'],
            'year' => $currentYear,
            'is_active' => true,
        ]);

        // Ketua Panitia
        $ketuaPanitiaKeyPair = $ecdsaService->generateKeyPair();
        $ketuaPanitia = User::create([
            'username' => 'ketuapanitia',
            'password' => bcrypt('ketuapanitia'),
            'fullname' => 'Moh. Almukarrom. H. D.',
            'role_id' => 4,
            'institution_id' => 1,
            'public_key' => $ketuaPanitiaKeyPair['publicKey'],
            'private_key' => $ketuaPanitiaKeyPair['privateKey'],
            'year' => $currentYear,
            'is_active' => true,
        ]);

        // Sekretaris Panitia
        $sekretarisPanitiaKeyPair = $ecdsaService->generateKeyPair();
        $sekretarisPanitia = User::create([
            'username' => 'sekpansekpan',
            'password' => bcrypt('sekpansekpan'),
            'fullname' => 'Moch. Jofani Ubaidillah',
            'role_id' => 5,
            'institution_id' => 1,
            'public_key' => $sekretarisPanitiaKeyPair['publicKey'],
            'private_key' => $sekretarisPanitiaKeyPair['privateKey'],
            'year' => $currentYear,
            'is_active' => true,
        ]);

        // Create a committee and associate users
        Committee::create([
            'institution_id' => 1,
            'name' => 'Sosialisasi Program Kerja UKM Pagar Nusa',
            'chairman_id' => $ketuaPanitia->id,
            'secretary_id' => $sekretarisPanitia->id,
        ]);

        // Pembina
        $pembinaKeyPair = $ecdsaService->generateKeyPair();
        User::create([
            'username' => 'pembina123',
            'password' => bcrypt('pembina123'),
            'fullname' => 'Dr. H. Saiful Mustofa, M.Pd, MA',
            'role_id' => 6,
            'institution_id' => 1,
            'public_key' => $pembinaKeyPair['publicKey'],
            'private_key' => $pembinaKeyPair['privateKey'],
            'year' => $currentYear,
            'is_active' => true,
        ]);

        // User::factory(10)->create();
    }
}
