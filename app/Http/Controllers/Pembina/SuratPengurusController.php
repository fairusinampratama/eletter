<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\PembinaController;
use App\Models\Letter;
use App\Models\LetterCategory;
use App\Models\User;

class SuratPengurusController extends PembinaController
{
    public $title = 'Surat Pengurus';

    public function index()
    {
        $userInstitutionId = auth()->user()->institution_id;
        $currentUserId = auth()->user()->id;

        $letters = Letter::whereHas('category', function ($query) use ($userInstitutionId) {
            $query->where('institution_id', $userInstitutionId);
        })
            ->whereHas('signatures', function ($query) use ($currentUserId) {
                $query->where('signer_id', $currentUserId)
                    ->where('order', 3); // Pembina is the third signer (order: 3)
            })
            ->with(['category', 'creator', 'signatures.signer'])
            ->get();

        // Only show letter categories from the same institution as the user
        $categories = LetterCategory::where('institution_id', $userInstitutionId)->get();

        // Get users from the same institution for signer selection
        $users = User::where('institution_id', $userInstitutionId)->get();

        return view('dashboard.pembina.surat-pengurus.index', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'letters' => $letters,
            'categories' => $categories,
            'users' => $users,
        ]);
    }
}
