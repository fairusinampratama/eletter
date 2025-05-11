<?php

namespace App\Http\Controllers\SekretarisUmum;

use App\Http\Controllers\SekretarisUmumController;
use App\Models\SuratPanitia;
use App\Models\Kepanitiaan;
use App\Models\KategoriSurat;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SuratPanitiaController extends SekretarisUmumController
{
    public $title = 'Surat Panitia';

    public function index()
    {
        $suratPanitia = SuratPanitia::with(['kepanitiaan', 'kategoriSurat'])->get();

        return view('dashboard.sekretaris-umum.surat-panitia.index', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'suratPanitia' => $suratPanitia,
        ]);
    }

    public function create()
    {
        $kepanitiaan = Kepanitiaan::all();
        $kategoriSurat = KategoriSurat::all();

        return view('dashboard.sekretaris-umum.surat-panitia.create', [
            'menuItems' => $this->menuItems,
            'title' => 'Tambah ' . $this->title,
            'kepanitiaan' => $kepanitiaan,
            'kategoriSurat' => $kategoriSurat,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nomor' => ['required', 'string', 'max:255', 'unique:surat_panitia,nomor'],
                'judul' => ['required', 'string', 'max:255'],
                'kepanitiaan_id' => ['required', 'exists:kepanitiaan,id'],
                'kategori_surat_id' => ['required', 'exists:kategori_surat,id'],
                'tanggal' => ['required', 'date'],
                'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            ]);

            $data = [
                'nomor' => $request->nomor,
                'judul' => $request->judul,
                'kepanitiaan_id' => $request->kepanitiaan_id,
                'kategori_surat_id' => $request->kategori_surat_id,
                'tanggal' => $request->tanggal,
            ];

            if ($request->hasFile('file')) {
                $data['file'] = $request->file('file')->store('surat-panitia');
            }

            SuratPanitia::create($data);

            return redirect()->route('sekretaris-umum.surat-panitia.index')
                ->with('success', 'Surat panitia berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambah surat panitia: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $surat = SuratPanitia::findOrFail($id);
        $kepanitiaan = Kepanitiaan::all();
        $kategoriSurat = KategoriSurat::all();

        return view('dashboard.sekretaris-umum.surat-panitia.edit', [
            'menuItems' => $this->menuItems,
            'title' => 'Edit ' . $this->title,
            'surat' => $surat,
            'kepanitiaan' => $kepanitiaan,
            'kategoriSurat' => $kategoriSurat,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $surat = SuratPanitia::findOrFail($id);

            $request->validate([
                'nomor' => ['required', 'string', 'max:255', 'unique:surat_panitia,nomor,' . $surat->id],
                'judul' => ['required', 'string', 'max:255'],
                'kepanitiaan_id' => ['required', 'exists:kepanitiaan,id'],
                'kategori_surat_id' => ['required', 'exists:kategori_surat,id'],
                'tanggal' => ['required', 'date'],
                'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            ]);

            $data = [
                'nomor' => $request->nomor,
                'judul' => $request->judul,
                'kepanitiaan_id' => $request->kepanitiaan_id,
                'kategori_surat_id' => $request->kategori_surat_id,
                'tanggal' => $request->tanggal,
            ];

            if ($request->hasFile('file')) {
                // Optionally, delete old file here
                $data['file'] = $request->file('file')->store('surat-panitia');
            }

            $surat->update($data);

            return redirect()->route('sekretaris-umum.surat-panitia.index')
                ->with('success', 'Surat panitia berhasil diperbarui.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui surat panitia: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $surat = SuratPanitia::findOrFail($id);
            $surat->delete();

            return redirect()->route('sekretaris-umum.surat-panitia.index')
                ->with('success', 'Surat panitia berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus surat panitia: ' . $e->getMessage());
        }
    }
}
