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
        // Log the raw request data at the very start
        \Log::info('Step 1: Raw request data', $request->all());

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
            // Log after validation
            \Log::info('Step 2: Request data after validation', $request->all());
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Step 2: Validation failed', ['errors' => $e->errors()]);
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', $e->getMessage() ?: 'Gagal menyimpan surat panitia. Silakan periksa kembali data yang diinput.');
        }

        try {
            $currentUser = auth()->user();
            $userInstitutionId = $currentUser->institution_id;

            // Log before file storage
            \Log::info('Step 3: Before file storage', $request->all());

            // Store original PDF and calculate hash
            $path = $request->file('file_path')->store('documents', 'public');
            $originalFileHash = hash_file('sha256', storage_path('app/public/' . $path));

            // Prepare letter data
            $committee = \App\Models\Committee::where('secretary_id', $currentUser->id)->first();
            if (!$committee) {
                throw new \Exception('Anda tidak terdaftar sebagai sekretaris panitia.');
            }

            $letterData = [
                'verification_id' => strtoupper(bin2hex(random_bytes(4))),
                'code' => $request->code,
                'category_id' => $request->category_id,
                'creator_id' => $currentUser->id,
                'institution_id' => $userInstitutionId,
                'committee_id' => $committee->id,
                'file_path' => $path,
                'file_hash' => $originalFileHash,
                'original_file_hash' => $originalFileHash,
                'date' => $request->date,
                'status' => 'pending'
            ];

            // Log before creating the letter
            \Log::info('Step 4: Before creating letter', ['letterData' => $letterData]);

            // Create letter record
            $letter = \App\Models\Letter::create($letterData);

            // Log before creating signatures
            \Log::info('Step 5: Before creating signatures', ['signers' => $request->signers]);

            // Sort signers by role order: Sekretaris Panitia (5), Ketua Panitia (4), Ketua Umum (2), Pembina (6)
            $roleOrder = [5, 4, 2, 6];
            $signers = collect($request->signers)->sortBy(function($signer) use ($roleOrder) {
                $roleId = \App\Models\User::find($signer['id'])->role_id;
                $idx = array_search($roleId, $roleOrder);
                return $idx === false ? 99 : $idx;
            })->values();

            // Assign order sequentially (1,2,3...)
            foreach ($signers as $idx => $signer) {
                \Log::info('Step 6: Creating signature', ['signer' => $signer]);
                $letter->signatures()->create([
                    'signer_id' => $signer['id'],
                    'order' => $idx + 1,
                    'signature' => null,
                    'signed_at' => null,
                    'qr_metadata' => [
                        'page' => (int) $signer['qr_page'],
                        'x' => (float) $signer['qr_x'],
                        'y' => (float) $signer['qr_y'],
                    ],
                ]);
            }

            // Log after all signatures created
            \Log::info('Step 7: All signatures created for letter', ['letter_id' => $letter->id]);

            return redirect()->route('sekretaris-panitia.surat-panitia.index')
                ->with('success', 'Surat panitia berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Step 8: Validation exception in processing', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Step 9: General exception', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Gagal menambah surat panitia: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function create()
    {
        $currentUser = auth()->user();
        $userInstitutionId = $currentUser->institution_id;

        // Get the committee where user is secretary or chairman using model methods
        $committee = $currentUser->committeeAsSecretary() ?? $currentUser->committeeAsChairman();
        if (!$committee) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar sebagai sekretaris atau ketua panitia.');
        }

        // Only show letter categories from this committee
        $categories = \App\Models\LetterCategory::where('institution_id', $userInstitutionId)
            ->where('committee_id', $committee->id)
            ->get();

        // Get users from the same institution for signer selection
        $users = \App\Models\User::where('institution_id', $userInstitutionId)->get();

        $sekretarisPanitia = $committee->secretary;
        $ketuaPanitia = $committee->chairman;

        return view('dashboard.sekretaris-panitia.surat-panitia.create', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'categories' => $categories,
            'users' => $users,
            'committee' => $committee,
            'sekretarisPanitia' => $sekretarisPanitia,
            'ketuaPanitia' => $ketuaPanitia,
        ]);
    }
}
