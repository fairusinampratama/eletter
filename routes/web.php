<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\VerificationController;
use Livewire\Livewire;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return redirect()->intended(
            match ((int) $user->role_id) {
                1 => route('admin-kemahasiswaan.dashboard'),
                2 => route('ketua-umum.dashboard'),
                3 => route('sekretaris-umum.dashboard'),
                4 => route('ketua-panitia.dashboard'),
                5 => route('sekretaris-panitia.dashboard'),
                6 => route('pembina.dashboard'),
                default => '/'
            }
        );
    }
    return redirect('/login');
});

// Verification Routes
Route::get('/verify/{verification_id}', [VerificationController::class, 'show'])->name('verify');
Route::post('/verify/{verification_id}', [VerificationController::class, 'verify'])->name('verify.check');

require __DIR__ . '/auth.php';
