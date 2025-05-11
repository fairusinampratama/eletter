<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            ['id' => 1, 'name' => 'Admin Kemahasiswaan'],
            ['id' => 2, 'name' => 'Ketua Umum UKM'],
            ['id' => 3, 'name' => 'Sekretaris Umum UKM'],
            ['id' => 4, 'name' => 'Ketua Panitia'],
            ['id' => 5, 'name' => 'Sekretaris Panitia'],
            ['id' => 6, 'name' => 'Pembina'],
        ]);
    }
}
