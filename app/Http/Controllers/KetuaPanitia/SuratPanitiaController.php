<?php

namespace App\Http\Controllers\KetuaPanitia;

use App\Http\Controllers\KetuaPanitiaController;
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
    public $title = 'Surat Pengurus';

    public function index()
    {
        $userInstitutionId = auth()->user()->institution_id;

        $letters = Letter::whereHas('category', function ($query) use ($userInstitutionId) {
            $query->where('institution_id', $userInstitutionId);
        })->with(['category', 'creator'])->get();

        // Only show letter categories from the same institution as the user
        $categories = LetterCategory::where('institution_id', $userInstitutionId)->get();

        // Get users from the same institution for signer selection
        $users = User::where('institution_id', $userInstitutionId)->get();

        return view('dashboard.ketua-umum.surat-pengurus.index', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'letters' => $letters,
            'categories' => $categories,
            'users' => $users,
        ]);
    }
}
