<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\AslabController;
use App\Http\Controllers\Dashboard\DosenController;
use App\Http\Controllers\Dashboard\KalabController;
use App\Http\Controllers\Dashboard\MahasiswaController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {

    // 1. Halaman Landing: Carousel Pemilihan Role
    Route::get('/', [AuthController::class, 'showRoleSelection'])->name('welcome');

    // 2. Halaman Form Login: Muncul setelah pilih role (parameter {role} adalah slug)
    Route::get('/login/{role}', [AuthController::class, 'showLogin'])->name('login');

    // 3. Handle Proses Login Internal
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // 4. Google OAuth
    Route::post('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])
         ->name('auth.google.redirect');

    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])
         ->name('auth.google.callback');
});

// Logout (hanya untuk user yang sudah login)
Route::post('/logout', [AuthController::class, 'logout'])
     ->name('logout')
     ->middleware('auth');

// =============================================================================
// DASHBOARD ROUTES — Diproteksi auth + role masing-masing
// =============================================================================

/**
 * Dashboard KALAB (Kepala Laboratorium)
 * Role yang diizinkan: kalab
 */
Route::middleware(['auth', 'role:kalab'])
     ->prefix('kalab')
     ->name('kalab.')
     ->group(function () {

    Route::get('/dashboard', [KalabController::class, 'index'])->name('dashboard');
    Route::get('/users', [KalabController::class, 'manageUsers'])->name('users');
    Route::get('/laporan', [KalabController::class, 'laporan'])->name('laporan');
    // Tambahkan route KALAB lainnya di sini
});

/**
 * Dashboard MAHASISWA
 * Role yang diizinkan: mahasiswa
 */
Route::middleware(['auth', 'role:mahasiswa'])
     ->prefix('mahasiswa')
     ->name('mahasiswa.')
     ->group(function () {

    Route::get('/dashboard', [MahasiswaController::class, 'index'])->name('dashboard');
    Route::get('/jadwal', [MahasiswaController::class, 'jadwal'])->name('jadwal');
    Route::get('/nilai', [MahasiswaController::class, 'nilai'])->name('nilai');
    // Tambahkan route MAHASISWA lainnya di sini
});

/**
 * Dashboard ASISTEN LABORATORIUM
 * Role yang diizinkan: aslab
 */
Route::middleware(['auth', 'role:aslab'])
     ->prefix('aslab')
     ->name('aslab.')
     ->group(function () {

    Route::get('/dashboard', [AslabController::class, 'index'])->name('dashboard');
    Route::get('/praktikum', [AslabController::class, 'praktikum'])->name('praktikum');
    Route::get('/penilaian', [AslabController::class, 'penilaian'])->name('penilaian');
    // Tambahkan route ASLAB lainnya di sini
});

/**
 * Dashboard DOSEN PEMBIMBING
 * Role yang diizinkan: dosen-pembimbing
 */
Route::middleware(['auth', 'role:dosen-pembimbing'])
     ->prefix('dosen')
     ->name('dosen-pembimbing.')
     ->group(function () {

    Route::get('/dashboard', [DosenController::class, 'index'])->name('dashboard');
    Route::get('/bimbingan', [DosenController::class, 'bimbingan'])->name('bimbingan');
    Route::get('/nilai', [DosenController::class, 'nilai'])->name('nilai');
    // Tambahkan route DOSEN lainnya di sini
});

/**
 * Contoh route yang bisa diakses oleh MULTIPLE roles:
 * KALAB dan ASLAB sama-sama bisa melihat halaman laporan umum.
 */
Route::middleware(['auth', 'role:kalab,aslab'])
     ->prefix('lab')
     ->name('lab.')
     ->group(function () {

    Route::get('/laporan-umum', fn () => view('lab.laporan-umum'))->name('laporan-umum');
});