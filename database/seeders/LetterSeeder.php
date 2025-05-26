<?php

namespace Database\Seeders;

use App\Models\Letter;
use App\Models\User;
use App\Models\LetterCategory;
use App\Models\Committee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LetterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some existing users and categories
        $ketuaUmum = User::where('username', 'ketuaumum')->first();
        $sekretarisUmum = User::where('username', 'sekretarisumum')->first();
        $ketuaPanitia = User::where('username', 'ketuapanitia')->first();
        $sekretarisPanitia = User::where('username', 'sekretarispanitia')->first();

        // Get categories
        $undanganCategory = LetterCategory::where('name', 'Surat Undangan')->firstOrFail();
        $permohonanCategory = LetterCategory::where('name', 'Surat Permohonan')->firstOrFail();
        $keteranganCategory = LetterCategory::where('name', 'Surat Keterangan')->firstOrFail();
        $tugasCategory = LetterCategory::where('name', 'Surat Tugas')->firstOrFail();
        $penetapanCategory = LetterCategory::where('name', 'Surat Penetapan Panitia')->firstOrFail();
        $pembentukanCategory = LetterCategory::where('name', 'Surat Pembentukan Panitia')->firstOrFail();
        $pertanggungjawabanCategory = LetterCategory::where('name', 'Surat Pertanggungjawaban Panitia')->firstOrFail();
        $laporanCategory = LetterCategory::where('name', 'Surat Laporan Kegiatan')->firstOrFail();

        // Get committees
        $ldkCommittee = Committee::where('name', 'Panitia Latihan Dasar Kepemimpinan')->firstOrFail();
        $konserCommittee = Committee::where('name', 'Panitia Konser Amal')->firstOrFail();
        $inovasiCommittee = Committee::where('name', 'Panitia Lomba Inovasi')->firstOrFail();
        $fotografiCommittee = Committee::where('name', 'Panitia Turnamen Fotografi')->firstOrFail();
        $donorDarahCommittee = Committee::where('name', 'Panitia Donor Darah')->firstOrFail();

        // Create sample letters (using only fields from the model)
        Letter::create([
            'verification_id' => 'VERIF001',
            'code' => '001/UND/REMEN/2024',
            'category_id' => $undanganCategory->id,
            'creator_id' => $ketuaUmum->id,
            'file_path' => 'letters/undangan_rapat_koordinasi.pdf',
            'file_hash' => hash('sha256', 'dummycontent1'),
            'original_file_hash' => null,
            'date' => now(),
            'status' => 'pending',
        ]);

        Letter::create([
            'verification_id' => 'VERIF002',
            'code' => '002/PMH/REMEN/2024',
            'category_id' => $permohonanCategory->id,
            'creator_id' => $sekretarisUmum->id,
            'file_path' => 'letters/permohonan_dana_kegiatan.pdf',
            'file_hash' => hash('sha256', 'dummycontent2'),
            'original_file_hash' => null,
            'date' => now(),
            'status' => 'pending',
        ]);

        Letter::create([
            'verification_id' => 'VERIF003',
            'code' => '003/KET/REMEN/2024',
            'category_id' => $keteranganCategory->id,
            'creator_id' => $ketuaPanitia->id,
            'file_path' => 'letters/keterangan_aktif_organisasi.pdf',
            'file_hash' => hash('sha256', 'dummycontent3'),
            'original_file_hash' => null,
            'date' => now(),
            'status' => 'pending',
        ]);

        Letter::create([
            'verification_id' => 'VERIF004',
            'code' => '001/PTP/REMEN/2024',
            'category_id' => $penetapanCategory->id,
            'creator_id' => $ketuaUmum->id,
            'file_path' => 'letters/penetapan_panitia_ldk.pdf',
            'file_hash' => hash('sha256', 'dummycontent4'),
            'original_file_hash' => null,
            'date' => now(),
            'status' => 'pending',
        ]);

        Letter::create([
            'verification_id' => 'VERIF005',
            'code' => '001/PBK/SIMFONI/2024',
            'category_id' => $pembentukanCategory->id,
            'creator_id' => $ketuaPanitia->id,
            'file_path' => 'letters/pembentukan_panitia_konser.pdf',
            'file_hash' => hash('sha256', 'dummycontent5'),
            'original_file_hash' => null,
            'date' => now(),
            'status' => 'pending',
        ]);

        Letter::create([
            'verification_id' => 'VERIF006',
            'code' => '001/PMH/UAPM/2024',
            'category_id' => $permohonanCategory->id,
            'creator_id' => $ketuaPanitia->id,
            'file_path' => 'letters/permohonan_dana_inovasi.pdf',
            'file_hash' => hash('sha256', 'dummycontent6'),
            'original_file_hash' => null,
            'date' => now(),
            'status' => 'pending',
        ]);

        Letter::create([
            'verification_id' => 'VERIF007',
            'code' => '001/TGS/JHEPRET/2024',
            'category_id' => $tugasCategory->id,
            'creator_id' => $ketuaPanitia->id,
            'file_path' => 'letters/tugas_panitia_fotografi.pdf',
            'file_hash' => hash('sha256', 'dummycontent7'),
            'original_file_hash' => null,
            'date' => now(),
            'status' => 'pending',
        ]);

        Letter::create([
            'verification_id' => 'VERIF008',
            'code' => '001/LAP/KSR/2024',
            'category_id' => $laporanCategory->id,
            'creator_id' => $sekretarisPanitia->id,
            'file_path' => 'letters/laporan_donor_darah.pdf',
            'file_hash' => hash('sha256', 'dummycontent8'),
            'original_file_hash' => null,
            'date' => now(),
            'status' => 'pending',
        ]);

        Letter::create([
            'verification_id' => 'VERIF009',
            'code' => '002/PTJ/REMEN/2024',
            'category_id' => $pertanggungjawabanCategory->id,
            'creator_id' => $sekretarisPanitia->id,
            'file_path' => 'letters/pertanggungjawaban_ldk.pdf',
            'file_hash' => hash('sha256', 'dummycontent9'),
            'original_file_hash' => null,
            'date' => now(),
            'status' => 'pending',
        ]);
    }
}
