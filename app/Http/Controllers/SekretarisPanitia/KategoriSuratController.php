<?php

namespace App\Http\Controllers\SekretarisPanitia;

use App\Http\Controllers\SekretarisPanitiaController;
use App\Models\LetterCategory;
use App\Models\Committee;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KategoriSuratController extends SekretarisPanitiaController
{
    public $title = 'Kategori Surat';

    public function index()
    {
        $currentUser = auth()->user();
        $userInstitutionId = $currentUser->institution_id;

        // Get the committee where user is secretary
        $committee = Committee::where('secretary_id', $currentUser->id)->first();
        if (!$committee) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar sebagai sekretaris panitia.');
        }

        // Only get categories from this committee
        $categories = LetterCategory::where('institution_id', $userInstitutionId)
            ->where('committee_id', $committee->id)
            ->get();

        return view('dashboard.sekretaris-panitia.kategori-surat.index', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $currentUser = auth()->user();

            // Get the committee where user is secretary
            $committee = Committee::where('secretary_id', $currentUser->id)->first();
            if (!$committee) {
                throw new \Exception('Anda tidak terdaftar sebagai sekretaris panitia.');
            }

            LetterCategory::create([
                'name' => $request->name,
                'institution_id' => $currentUser->institution_id,
                'committee_id' => $committee->id
            ]);

            return redirect()->route('sekretaris-panitia.kategori-surat.index')
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
            $currentUser = auth()->user();

            // Get the committee where user is secretary
            $committee = Committee::where('secretary_id', $currentUser->id)->first();
            if (!$committee) {
                throw new \Exception('Anda tidak terdaftar sebagai sekretaris panitia.');
            }

            // Verify the category belongs to this committee
            if ($kategori_surat->committee_id !== $committee->id) {
                throw new \Exception('Anda tidak memiliki akses untuk mengubah kategori surat ini.');
            }

            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $kategori_surat->update([
                'name' => $request->name
            ]);

            return redirect()->route('sekretaris-panitia.kategori-surat.index')
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
            $currentUser = auth()->user();

            // Get the committee where user is secretary
            $committee = Committee::where('secretary_id', $currentUser->id)->first();
            if (!$committee) {
                throw new \Exception('Anda tidak terdaftar sebagai sekretaris panitia.');
            }

            if ($kategori_surat === 'selected') {
                $data = json_decode($request->ids, true);
                $ids = $data['ids'] ?? [];

                if (empty($ids)) {
                    return redirect()->back()
                        ->with('error', 'Tidak ada kategori surat yang dipilih untuk dihapus.');
                }

                // Verify all categories belong to this committee
                $categories = LetterCategory::whereIn('id', $ids)
                    ->where('committee_id', $committee->id)
                    ->get();

                if ($categories->count() !== count($ids)) {
                    throw new \Exception('Anda tidak memiliki akses untuk menghapus beberapa kategori surat yang dipilih.');
                }

                LetterCategory::whereIn('id', $ids)->delete();
                return redirect()->route('sekretaris-panitia.kategori-surat.index')
                    ->with('success', $categories->count() . ' kategori surat berhasil dihapus.');
            } else {
                $kategori_surat = LetterCategory::findOrFail($kategori_surat);

                // Verify the category belongs to this committee
                if ($kategori_surat->committee_id !== $committee->id) {
                    throw new \Exception('Anda tidak memiliki akses untuk menghapus kategori surat ini.');
                }

                $kategori_surat->delete();
                return redirect()->route('sekretaris-panitia.kategori-surat.index')
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
