<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SignatureController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::middleware([RoleMiddleware::class . ':1'])->group(function () {
        Route::prefix('admin-kemahasiswaan')->name('admin-kemahasiswaan.')->group(function () {
            Route::get('/', function () {
                return redirect()->route('admin-kemahasiswaan.ukm.index');
            })->name('dashboard');

            Route::resource('ukm', \App\Http\Controllers\AdminKemahasiswaan\UkmController::class);
            Route::resource('pengguna', \App\Http\Controllers\AdminKemahasiswaan\PenggunaController::class);
        });
    });

    Route::middleware([RoleMiddleware::class . ':2'])->group(function () {
        Route::prefix('ketua-umum')->name('ketua-umum.')->group(function () {
            Route::get('/', function () {
                return redirect()->route('ketua-umum.surat-pengurus.index');
            })->name('dashboard');

            Route::resource('surat-pengurus', \App\Http\Controllers\KetuaUmum\SuratPengurusController::class);
            Route::resource('surat-panitia', \App\Http\Controllers\KetuaUmum\SuratPanitiaController::class);
        });
    });

    Route::middleware([RoleMiddleware::class . ':3'])->group(function () {
        Route::prefix('sekretaris-umum')->name('sekretaris-umum.')->group(function () {
            Route::get('/', function () {
                return redirect()->route('sekretaris-umum.surat-pengurus.index');
            })->name('dashboard');

            Route::resource('surat-pengurus', \App\Http\Controllers\SekretarisUmum\SuratPengurusController::class);
            Route::resource('kategori-surat', \App\Http\Controllers\SekretarisUmum\KategoriSuratController::class);
            Route::resource('kepanitiaan', \App\Http\Controllers\SekretarisUmum\KepanitiaanController::class);
            Route::resource('surat-panitia', \App\Http\Controllers\SekretarisUmum\SuratPanitiaController::class);
            Route::post('kepanitiaan/store-user', [\App\Http\Controllers\SekretarisUmum\KepanitiaanController::class, 'storeUser'])->name('kepanitiaan.store-user');
        });
    });

    Route::middleware([RoleMiddleware::class . ':4'])->group(function () {
        Route::prefix('ketua-panitia')->name('ketua-panitia.')->group(function () {
            Route::get('/', function () {
                return redirect()->route('ketua-panitia.surat-panitia.index');
            })->name('dashboard');

            Route::resource('surat-panitia', \App\Http\Controllers\KetuaPanitia\SuratPanitiaController::class);
        });
    });

    Route::middleware([RoleMiddleware::class . ':5'])->group(function () {
        Route::prefix('sekretaris-panitia')->name('sekretaris-panitia.')->group(function () {
            Route::get('/', function () {
                return redirect()->route('sekretaris-panitia.kategori-surat.index');
            })->name('dashboard');

            Route::resource('kategori-surat', \App\Http\Controllers\SekretarisPanitia\KategoriSuratController::class);
            Route::resource('surat-panitia', \App\Http\Controllers\SekretarisPanitia\SuratPanitiaController::class);
        });
    });

    Route::middleware([RoleMiddleware::class . ':6'])->group(function () {
        Route::prefix('pembina')->name('pembina.')->group(function () {
            Route::get('/', function () {
                return redirect()->route('pembina.surat-pengurus.index');
            })->name('dashboard');

            Route::resource('surat-pengurus', \App\Http\Controllers\Pembina\SuratPengurusController::class);
            Route::resource('surat-panitia', \App\Http\Controllers\Pembina\SuratPanitiaController::class);
        });
    });

    // Add signature routes outside role middleware since it's handled in the controller
    Route::post('signatures/sign', [SignatureController::class, 'sign'])->name('signatures.sign');

    // Logout route
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

