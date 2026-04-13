<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Kalab\MonitoringController;
use App\Http\Controllers\Kalab\ResourceController;
use App\Http\Controllers\Kalab\ScheduleController;
use App\Http\Controllers\Aslab\GradingController;
use App\Http\Controllers\Aslab\AttendanceController;
use App\Http\Controllers\Aslab\TroubleshootingController;
use App\Http\Controllers\Mahasiswa\ModuleController;
use App\Http\Controllers\Mahasiswa\SubmissionController;
use App\Http\Controllers\Mahasiswa\ProgressController;
use App\Http\Controllers\Dosen\ReviewController;
use App\Http\Controllers\Dosen\MonitoringController as DosenMonitoringController;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showRoleSelection'])->name('welcome');
    Route::get('/login/{role}', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])
     ->name('logout')
     ->middleware('auth');

// =============================================================================
// KALAB
// =============================================================================
Route::middleware(['auth', 'role:kalab'])
     ->prefix('kalab')
     ->name('kalab.')
     ->group(function () {

    // ✅ Dashboard langsung return view, tidak perlu controller
    Route::get('/dashboard', fn() => view('kalab.dashboard'))->name('dashboard');

    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources');
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules');
    Route::post('/schedules/finalize', [ScheduleController::class, 'finalize'])->name('schedules.finalize');
});

// =============================================================================
// MAHASISWA
// =============================================================================
Route::middleware(['auth', 'role:mahasiswa'])
     ->prefix('mahasiswa')
     ->name('mahasiswa.')
     ->group(function () {

    // ✅ Dashboard langsung return view
    Route::get('/dashboard', fn() => view('mahasiswa.dashboard'))->name('dashboard');

    Route::get('/modules', [ModuleController::class, 'index'])->name('modules');
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions');
    Route::post('/submissions/submit', [SubmissionController::class, 'submit'])->name('submissions.submit');
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress');
});

// =============================================================================
// ASLAB
// =============================================================================
Route::middleware(['auth', 'role:aslab'])
     ->prefix('aslab')
     ->name('aslab.')
     ->group(function () {

    // ✅ Dashboard langsung return view
    Route::get('/dashboard', fn() => view('aslab.dashboard'))->name('dashboard');

    Route::get('/grading', [GradingController::class, 'index'])->name('grading');
    Route::post('/grading/submit', [GradingController::class, 'submitGrade'])->name('grading.submit');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/mark', [AttendanceController::class, 'markAttendance'])->name('attendance.mark');
    Route::get('/troubleshooting', [TroubleshootingController::class, 'troubleshooting'])->name('troubleshooting');
});

// =============================================================================
// DOSEN PEMBIMBING
// =============================================================================
Route::middleware(['auth', 'role:dosen-pembimbing'])
     ->prefix('dosen')
     ->name('dosen.')
     ->group(function () {

    // ✅ Dashboard langsung return view
    Route::get('/dashboard', fn() => view('dosen.dashboard'))->name('dashboard');

    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::post('/reviews/submit', [ReviewController::class, 'submitReview'])->name('reviews.submit');
    Route::get('/monitoring', [DosenMonitoringController::class, 'index'])->name('monitoring');
});

// =============================================================================
// MULTI ROLE
// =============================================================================
Route::middleware(['auth', 'role:kalab,aslab'])
     ->prefix('lab')
     ->name('lab.')
     ->group(function () {
    Route::get('/laporan-umum', fn() => view('lab.laporan-umum'))->name('laporan-umum');
});
