<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Institution::insert([
            ['id' => 1, 'name' => 'Resimen Mahasiswa'],
            ['id' => 2, 'name' => 'Simfoni SM'],
            ['id' => 3, 'name' => 'UAPM Inovasi'],
            ['id' => 4, 'name' => 'Jhepret Club'],
            ['id' => 5, 'name' => 'KSR PMI'],
            ['id' => 6, 'name' => 'Pencak Silat Pagar Nusa'],
            ['id' => 7, 'name' => 'Kopma PB'],
            ['id' => 8, 'name' => 'Pramuka'],
            ['id' => 9, 'name' => 'UNIOR'],
            ['id' => 10, 'name' => 'Mapala Tursina'],
            ['id' => 11, 'name' => 'Tae Kwon Do'],
            ['id' => 12, 'name' => 'LKP2M'],
            ['id' => 13, 'name' => 'Kommust'],
            ['id' => 14, 'name' => 'Seni Religius'],
            ['id' => 15, 'name' => 'Teater K2'],
            ['id' => 16, 'name' => 'PSM Gema Gita Buana'],
            ['id' => 17, 'name' => 'Kemahasiswaan UIN Malang'],
        ]);
    }
}
