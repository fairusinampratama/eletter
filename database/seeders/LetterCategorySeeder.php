<?php

namespace Database\Seeders;

use App\Models\LetterCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LetterCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General categories (institutional, not committee)
        LetterCategory::insert([
            ['name' => 'Surat Undangan', 'institution_id' => 1, 'committee_id' => null],
            ['name' => 'Surat Permohonan', 'institution_id' => 1, 'committee_id' => null],
            ['name' => 'Surat Pengantar', 'institution_id' => 1, 'committee_id' => null],
            ['name' => 'Surat Keterangan', 'institution_id' => 1, 'committee_id' => null],
            ['name' => 'Surat Tugas', 'institution_id' => 1, 'committee_id' => null],
            ['name' => 'Surat Keputusan', 'institution_id' => 1, 'committee_id' => null],
            ['name' => 'Surat Pemberitahuan', 'institution_id' => 1, 'committee_id' => null],
            ['name' => 'Surat Rekomendasi', 'institution_id' => 1, 'committee_id' => null],
            ['name' => 'Surat Pernyataan', 'institution_id' => 1, 'committee_id' => null],
            ['name' => 'Surat Kuasa', 'institution_id' => 1, 'committee_id' => null],
        ]);

        // Committee categories (assign to a sample committee, e.g., id=1)
        LetterCategory::insert([
            ['name' => 'Surat Penetapan Panitia', 'institution_id' => 1, 'committee_id' => 1],
            ['name' => 'Surat Pembentukan Panitia', 'institution_id' => 1, 'committee_id' => 1],
            ['name' => 'Surat Pertanggungjawaban Panitia', 'institution_id' => 1, 'committee_id' => 1],
            ['name' => 'Surat Laporan Kegiatan', 'institution_id' => 1, 'committee_id' => 1],
            ['name' => 'Surat Evaluasi Kegiatan', 'institution_id' => 1, 'committee_id' => 1],
        ]);
    }
}
