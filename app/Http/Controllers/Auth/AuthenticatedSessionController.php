<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View|RedirectResponse
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda tidak aktif. Silakan hubungi Admin Kemahasiswaan.');
        }

        // Check for committee roles
        if (in_array((int) $user->role_id, [4, 5])) { // 4 = Ketua Panitia, 5 = Sekretaris Panitia
            $committee = null;
            if ((int) $user->role_id === 4) {
                $committee = \App\Models\Committee::where('chairman_id', $user->id)->first();
            } else {
                $committee = \App\Models\Committee::where('secretary_id', $user->id)->first();
            }
            if (!$committee) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Anda tidak terdaftar sebagai ketua atau sekretaris panitia.');
            }
        }

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

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
