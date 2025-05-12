<?php

namespace App\Http\Controllers\KetuaUmum;

use App\Http\Controllers\KetuaUmumController;
use App\Models\Letter;
use App\Models\LetterCategory;
use App\Models\User;

class SuratPengurusController extends KetuaUmumController
{
    public $title = 'Surat Pengurus';

    public function index()
    {
        $userInstitutionId = auth()->user()->institution_id;

        $letters = Letter::whereHas('category', function ($query) use ($userInstitutionId) {
            $query->where('institution_id', $userInstitutionId)
                ->whereNull('committee_id'); // Exclude committee letters
        })->with(['category', 'creator'])->get();

        // Only show letter categories from the same institution and not from a committee
        $categories = LetterCategory::where('institution_id', $userInstitutionId)
            ->whereNull('committee_id')
            ->get();

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
