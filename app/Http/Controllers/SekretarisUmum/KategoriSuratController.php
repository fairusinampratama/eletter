<?php

namespace App\Http\Controllers\SekretarisUmum;

use App\Http\Controllers\SekretarisUmumController;
use App\Models\LetterCategory;
use App\Models\Institution;
use App\Models\Committee;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KategoriSuratController extends SekretarisUmumController
{
    public $title = 'Kategori Surat';

    public function index()
    {
        $userInstitutionId = auth()->user()->institution_id;

        $categories = LetterCategory::where('institution_id', $userInstitutionId)->get();
        $institutions = Institution::all();
        $committees = Committee::all();

        return view('dashboard.sekretaris-umum.kategori-surat.index', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'categories' => $categories,
            'institutions' => $institutions,
            'committees' => $committees,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:50',
                    'regex:/^[a-zA-Z0-9\s]+$/',
                    'unique:letter_categories,name,NULL,id,institution_id,' . auth()->user()->institution_id
                ]
            ], [
                'name.min' => 'Nama kategori minimal 3 karakter',
                'name.max' => 'Nama kategori maksimal 50 karakter',
                'name.regex' => 'Nama kategori hanya boleh berisi huruf, angka, dan spasi',
                'name.unique' => 'Nama kategori sudah digunakan'
            ]);

            LetterCategory::create([
                'name' => $request->name,
                'institution_id' => auth()->user()->institution_id,
                'committee_id' => null // for surat kepengurusan
            ]);

            return redirect()->route('sekretaris-umum.kategori-surat.index')
                ->with('success', 'Kategori surat berhasil ditambahkan');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambah kategori surat: ' . $e->getMessage());
        }
    }

    public function update(Request $request, LetterCategory $kategori_surat)
    {
        try {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:50',
                    'regex:/^[a-zA-Z0-9\s]+$/',
                    'unique:letter_categories,name,' . $kategori_surat->id . ',id,institution_id,' . auth()->user()->institution_id
                ]
            ], [
                'name.min' => 'Nama kategori minimal 3 karakter',
                'name.max' => 'Nama kategori maksimal 50 karakter',
                'name.regex' => 'Nama kategori hanya boleh berisi huruf, angka, dan spasi',
                'name.unique' => 'Nama kategori sudah digunakan'
            ]);

            $kategori_surat->update([
                'name' => $request->name
            ]);

            return redirect()->route('sekretaris-umum.kategori-surat.index')
                ->with('success', 'Kategori surat berhasil diperbarui');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui kategori surat: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $kategori_surat = null)
    {
        try {
            if ($kategori_surat === 'selected') {
                $data = json_decode($request->ids, true);
                $ids = $data['ids'] ?? [];

                if (empty($ids)) {
                    return redirect()->back()
                        ->with('error', 'Tidak ada kategori surat yang dipilih untuk dihapus.');
                }

                $count = LetterCategory::whereIn('id', $ids)->count();
                if ($count !== count($ids)) {
                    return redirect()->back()
                        ->with('error', 'Beberapa kategori surat tidak ditemukan.');
                }

                LetterCategory::whereIn('id', $ids)->delete();
                return redirect()->route('sekretaris-umum.kategori-surat.index')
                    ->with('success', $count . ' kategori surat berhasil dihapus.');
            } else {
                $kategori_surat = LetterCategory::findOrFail($kategori_surat);
                $kategori_surat->delete();
                return redirect()->route('sekretaris-umum.kategori-surat.index')
                    ->with('success', 'Kategori surat berhasil dihapus.');
            }
        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Kategori surat tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus kategori surat: ' . $e->getMessage());
        }
    }
}
