<?php

namespace App\Http\Controllers\KetuaPanitia;

use App\Http\Controllers\KetuaPanitiaController;
use App\Models\Committee;
use App\Models\Letter;
use App\Models\LetterCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use App\Services\PDFService;
use App\Models\User;
use App\Services\ECDSAService;

class SuratPanitiaController extends KetuaPanitiaController
{
    public $title = 'Surat Panitia';

    public function index()
    {
        $currentUser = auth()->user();
        $userInstitutionId = $currentUser->institution_id;

        // Get the committee where user is secretary
        $committee = Committee::where('chairman_id', $currentUser->id)->first();
        if (!$committee) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar sebagai ketua panitia.');
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

        return view('dashboard.ketua-panitia.surat-panitia.index', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'letters' => $letters,
            'categories' => $categories,
            'users' => $users,
        ]);
    }
}
