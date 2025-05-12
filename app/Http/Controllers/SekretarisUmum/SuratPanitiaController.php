<?php

namespace App\Http\Controllers\SekretarisUmum;

use App\Http\Controllers\SekretarisUmumController;
use App\Models\Letter;
use App\Models\LetterCategory;
use App\Models\User;

class SuratPanitiaController extends SekretarisUmumController
{
    public $title = 'Surat Panitia';

    public function index()
    {
        $userInstitutionId = auth()->user()->institution_id;

        $letters = Letter::whereHas('category', function ($query) use ($userInstitutionId) {
            $query->where('institution_id', $userInstitutionId)
                ->whereNotNull('committee_id'); // Only show letters from committees
        })->with(['category', 'creator'])->get();

        // Only show letter categories from committees in the same institution
        $categories = LetterCategory::where('institution_id', $userInstitutionId)
            ->whereNotNull('committee_id')
            ->get();

        // Get users from the same institution for signer selection
        $users = User::where('institution_id', $userInstitutionId)->get();

        return view('dashboard.sekretaris-umum.surat-panitia.index', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'letters' => $letters,
            'categories' => $categories,
            'users' => $users,
        ]);
    }
}
