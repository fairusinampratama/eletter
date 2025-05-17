<?php

namespace App\Http\Controllers\SekretarisPanitia;

use App\Http\Controllers\SekretarisPanitiaController;
use App\Models\Letter;
use App\Models\LetterCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use App\Services\PDFService;
use App\Models\User;
use App\Services\ECDSAService;
use App\Models\Committee;

class SuratPanitiaController extends SekretarisPanitiaController
{
    public $title = 'Surat Panitia';

    public function index()
    {
        $currentUser = auth()->user();
        $userInstitutionId = $currentUser->institution_id;

        // Get the committee where user is secretary
        $committee = Committee::where('secretary_id', $currentUser->id)->first();
        if (!$committee) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar sebagai sekretaris panitia.');
        }

        // Get letters through category relationship for this committee
        $letters = Letter::whereHas('category', function ($query) use ($committee) {
            $query->where('institution_id', $committee->institution_id)
                ->where('committee_id', $committee->id);
        })->with(['category', 'creator'])->get();

        // Only show letter categories from this committee
        $categories = LetterCategory::where('institution_id', $userInstitutionId)
            ->where('committee_id', $committee->id)
            ->get();

        // Get users from the same institution for signer selection
        $users = User::where('institution_id', $userInstitutionId)->get();

        return view('dashboard.sekretaris-panitia.surat-panitia.index', [
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
                'sekretaris_panitia_id' => 'nullable|exists:users,id',
                'ketua_panitia_id' => 'required|exists:users,id',
                'ketua_umum_id' => 'required|exists:users,id',
                'pembina_id' => 'nullable|exists:users,id'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', $e->getMessage() ?: 'Gagal menyimpan surat panitia. Silakan periksa kembali data yang diinput.');
        }

        try {
            // Get the current user and their institution
            $currentUser = auth()->user();
            $userInstitutionId = $currentUser->institution_id;

            // Get the committee where user is secretary
            $committee = Committee::where('secretary_id', $currentUser->id)->first();
            if (!$committee) {
                throw new \Exception('Anda tidak terdaftar sebagai sekretaris panitia.');
            }

            // Verify the category belongs to this committee
            $category = LetterCategory::where('id', $request->category_id)
                ->where('committee_id', $committee->id)
                ->firstOrFail();

            // Get Ketua Panitia for this committee
            $ketuaPanitia = User::where('id', $committee->chairman_id)->firstOrFail();

            // Get Ketua Umum for the institution
            $ketuaUmum = User::where('institution_id', $userInstitutionId)
                ->where('role_id', 2) // Role ID for Ketua Umum
                ->firstOrFail();

            // Validate Pembina if selected
            if ($request->pembina_id) {
                $pembina = User::findOrFail($request->pembina_id);
                if (!$pembina->isMentor() || $pembina->institution_id !== $userInstitutionId) {
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
                'institution_id' => $userInstitutionId,
                'committee_id' => $committee->id,
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

            // Add Sekretaris Panitia if selected
            if ($request->sekretaris_panitia_id) {
                $signers[] = [
                    'id' => $request->sekretaris_panitia_id,
                    'order' => $order++,
                    'role' => 'Sekretaris Panitia'
                ];
            }

            // Add Ketua Panitia (required)
            $signers[] = [
                'id' => $request->ketua_panitia_id,
                'order' => $order++,
                'role' => 'Ketua Panitia'
            ];

            // Add Ketua Umum (required)
            $signers[] = [
                'id' => $request->ketua_umum_id,
                'order' => $order++,
                'role' => 'Ketua Umum'
            ];

            // Add Pembina if selected
            if ($request->pembina_id) {
                $signers[] = [
                    'id' => $request->pembina_id,
                    'order' => $order++,
                    'role' => 'Pembina'
                ];
            }

            foreach ($signers as $signer) {
                $letter->signatures()->create([
                    'signer_id' => $signer['id'],
                    'order' => $signer['order'],
                    'role' => $signer['role'],
                    'signature' => null,
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

            return redirect()->route('sekretaris-panitia.surat-panitia.index')
                ->with('success', 'Surat panitia berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambah surat panitia: ' . $e->getMessage())
                ->withInput();
        }
    }
}
