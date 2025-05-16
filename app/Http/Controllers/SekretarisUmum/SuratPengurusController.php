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

    public function store(Request $request, PDFService $pdfService)
    {
        try {
            $request->validate([
                'code' => 'required|string|unique:letters,code',
                'file_path' => 'required|file|mimes:pdf|max:10240',
                'category_id' => 'required|exists:letter_categories,id',
                'date' => 'required|date',
                'sekretaris_umum_id' => 'nullable|exists:users,id',
                'ketua_umum_id' => 'required|exists:users,id',
                'pembina_id' => 'nullable|exists:users,id'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', $e->getMessage() ?: 'Gagal menyimpan surat pengurus. Silakan periksa kembali data yang diinput.');
        }

        try {
            // Get the institution's users
            $userInstitutionId = auth()->user()->institution_id;
            $currentUser = auth()->user();

            // Get default Sekretaris Umum and Ketua Umum for the institution
            $sekretarisUmum = User::where('institution_id', $userInstitutionId)
                ->where('role_id', 3) // Role ID for Sekretaris Umum
                ->firstOrFail();

            $ketuaUmum = User::where('institution_id', $userInstitutionId)
                ->where('role_id', 2) // Role ID for Ketua Umum
                ->firstOrFail();

            // Validate Pembina if selected
            if ($request->pembina_id) {
                $pembina = User::findOrFail($request->pembina_id);
                if ($pembina->institution_id !== $userInstitutionId || $pembina->role_id !== 6) {
                    throw ValidationException::withMessages([
                        'pembina_id' => 'Invalid Pembina selected'
                    ]);
                }
            }

            // Store original PDF and calculate hash
            $path = $request->file('file_path')->store('documents', 'public');
            $fileHash = hash_file('sha256', storage_path('app/public/' . $path));

            // Prepare letter data
            $letterData = [
                'verification_id' => strtoupper(bin2hex(random_bytes(4))),
                'code' => $request->code,
                'category_id' => $request->category_id,
                'creator_id' => $currentUser->id,
                'file_path' => $path,
                'file_hash' => $fileHash,
                'date' => $request->date,
                'status' => 'pending'
            ];

            // Create letter record
            $letter = Letter::create($letterData);

            // Create signature records in order
            $signers = [];
            $order = 1;

            // Add Sekretaris Umum if selected
            if ($request->sekretaris_umum_id) {
                $signers[] = ['id' => $request->sekretaris_umum_id, 'order' => $order++];
            }

            // Add Ketua Umum (required)
            $signers[] = ['id' => $request->ketua_umum_id, 'order' => $order++];

            // Add Pembina if selected
            if ($request->pembina_id) {
                $signers[] = ['id' => $request->pembina_id, 'order' => $order++];
            }

            foreach ($signers as $signer) {
                $letter->signatures()->create([
                    'signer_id' => $signer['id'],
                    'order' => $signer['order'],
                    'signature' => null,
                    'public_key' => null,
                    'signed_at' => null
                ]);
            }

            // Add QR code to PDF and update hash
            $signedPath = $pdfService->embedQRCode($letter);
            if ($signedPath) {
                $letter->update([
                    'file_path' => $signedPath,
                    'file_hash' => hash_file('sha256', storage_path('app/public/' . $signedPath))
                ]);
            }

            return redirect()->route('sekretaris-umum.surat-pengurus.index')
                ->with('success', 'Surat pengurus berhasil ditambahkan.');
        } catch (ValidationException $e) {
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
