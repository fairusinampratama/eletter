<?php

namespace App\Http\Controllers\SekretarisUmum;

use App\Http\Controllers\SekretarisUmumController;
use App\Models\Kepanitiaan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KepanitiaanController extends SekretarisUmumController
{
    public $title = 'Kepanitiaan';

    public function index()
    {
        $kepanitiaan = Kepanitiaan::all();

        return view('dashboard.sekretaris-umum.kepanitiaan.index', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'kepanitiaan' => $kepanitiaan,
        ]);
    }

    public function create()
    {
        return view('dashboard.sekretaris-umum.kepanitiaan.create', [
            'menuItems' => $this->menuItems,
            'title' => 'Tambah ' . $this->title,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => ['required', 'string', 'max:255', 'unique:kepanitiaan,nama'],
            ]);

            Kepanitiaan::create([
                'nama' => $request->nama,
            ]);

            return redirect()->route('sekretaris-umum.kepanitiaan.index')
                ->with('success', 'Kepanitiaan berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambah kepanitiaan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $kepanitiaan = Kepanitiaan::findOrFail($id);

        return view('dashboard.sekretaris-umum.kepanitiaan.edit', [
            'menuItems' => $this->menuItems,
            'title' => 'Edit ' . $this->title,
            'kepanitiaan' => $kepanitiaan,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $kepanitiaan = Kepanitiaan::findOrFail($id);

            $request->validate([
                'nama' => ['required', 'string', 'max:255', 'unique:kepanitiaan,nama,' . $kepanitiaan->id],
            ]);

            $kepanitiaan->update([
                'nama' => $request->nama,
            ]);

            return redirect()->route('sekretaris-umum.kepanitiaan.index')
                ->with('success', 'Kepanitiaan berhasil diperbarui.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui kepanitiaan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $kepanitiaan = Kepanitiaan::findOrFail($id);
            $kepanitiaan->delete();

            return redirect()->route('sekretaris-umum.kepanitiaan.index')
                ->with('success', 'Kepanitiaan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus kepanitiaan: ' . $e->getMessage());
        }
    }
}
