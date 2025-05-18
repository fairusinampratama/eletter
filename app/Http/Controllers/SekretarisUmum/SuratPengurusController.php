<?php

namespace App\Http\Controllers\SekretarisUmum;

use App\Http\Controllers\SekretarisUmumController;
use App\Models\Letter;
use App\Models\LetterCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use App\Services\PDFService;
use App\Models\User;
use App\Services\ECDSAService;

class SuratPengurusController extends SekretarisUmumController
{
    public $title = 'Surat Pengurus';

    public function index()
    {
        $userInstitutionId = auth()->user()->institution_id;

        $letters = Letter::whereHas('category', function ($query) use ($userInstitutionId) {
            $query->where('institution_id', $userInstitutionId);
        })->with(['category', 'creator'])->get();

        // Only show letter categories from the same institution and NOT from a committee
        $categories = LetterCategory::nonCommittee()
            ->where('institution_id', $userInstitutionId)
            ->get();

        // Get users from the same institution for signer selection
        $users = User::where('institution_id', $userInstitutionId)->get();

        return view('dashboard.sekretaris-umum.surat-pengurus.index', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'letters' => $letters,
            'categories' => $categories,
            'users' => $users,
        ]);
    }

    public function create()
    {
        $userInstitutionId = auth()->user()->institution_id;

        // Get letter categories for this institution
        $categories = LetterCategory::nonCommittee()
            ->where('institution_id', $userInstitutionId)
            ->get();

        // Get users from the same institution for signer selection
        $users = User::where('institution_id', $userInstitutionId)->get();

        return view('dashboard.sekretaris-umum.surat-pengurus.create', [
            'menuItems' => $this->menuItems,
            'title' => 'Tambah ' . $this->title,
            'categories' => $categories,
            'users' => $users,
        ]);
    }

    public function store(Request $request, PDFService $pdfService)
    {
        try {
            $request->validate([
                'code' => 'required|string|unique:letters,code',
                'file_path' => 'required|file|mimes:pdf|max:10240',
                'category_id' => 'required|exists:letter_categories,id',
                'date' => 'required|date',
                'signers' => 'required|array|min:1',
                'signers.*.id' => 'required|exists:users,id',
                'signers.*.order' => 'required|integer',
                'signers.*.qr_page' => 'required|integer',
                'signers.*.qr_x' => 'required|numeric',
                'signers.*.qr_y' => 'required|numeric',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', $e->getMessage() ?: 'Gagal menyimpan surat pengurus. Silakan periksa kembali data yang diinput.');
        }

        try {
            $userInstitutionId = auth()->user()->institution_id;
            $currentUser = auth()->user();

            // Store original PDF and calculate hash
            $path = $request->file('file_path')->store('documents', 'public');
            $originalFileHash = hash_file('sha256', storage_path('app/public/' . $path));

            // Prepare letter data
            $letterData = [
                'verification_id' => strtoupper(bin2hex(random_bytes(4))),
                'code' => $request->code,
                'category_id' => $request->category_id,
                'creator_id' => $currentUser->id,
                'file_path' => $path,
                'file_hash' => $originalFileHash,
                'original_file_hash' => $originalFileHash,
                'date' => $request->date,
                'status' => 'pending'
            ];

            // Create letter record
            $letter = Letter::create($letterData);

            // Create signature records from signers[]
            foreach ($request->signers as $signer) {
                $letter->signatures()->create([
                    'signer_id' => $signer['id'],
                    'order' => $signer['order'],
                    'signature' => null,
                    'signed_at' => null,
                    'qr_metadata' => [
                        'page' => (int) $signer['qr_page'],
                        'x' => (float) $signer['qr_x'],
                        'y' => (float) $signer['qr_y'],
                    ],
                ]);
            }

            return redirect()->route('sekretaris-umum.surat-pengurus.index')
                ->with('success', 'Surat pengurus berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambah surat pengurus: ' . $e->getMessage())
                ->withInput();
        }
    }
}
