<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Kalab\MonitoringController;
use App\Http\Controllers\Kalab\ResourceController;
use App\Http\Controllers\Kalab\ScheduleController;
use App\Http\Controllers\Aslab\GradingController;
use App\Http\Controllers\Aslab\AttendanceController;
use App\Http\Controllers\Aslab\TroubleshootingController;
use App\Http\Controllers\Mahasiswa\ModuleController as MahasiswaModule;
use App\Http\Controllers\Aslab\ModuleController as AslabModule; 
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

    // 1. Konteks Praktikum (Pilih Praktikum)
    Route::get('/select-practicum', [MonitoringController::class, 'showSelectPracticum'])->name('select-practicum');
    Route::post('/select-practicum', [MonitoringController::class, 'storeSelectPracticum'])->name('select-practicum.store');

    // 2. Master Dashboard & Analytics
    Route::get('/dashboard', [MonitoringController::class, 'index'])->name('dashboard');

    // 3. Manajemen Praktikum (Fitur Baru: Tambah RPL, Web, dll)
    Route::get('/practicums', [ResourceController::class, 'practicumIndex'])->name('practicums.index');
    Route::post('/practicums', [ResourceController::class, 'practicumStore'])->name('practicums.store');
    Route::delete('/practicums/{id}', [ResourceController::class, 'practicumDestroy'])->name('practicums.destroy');

    // 4. Kelulusan Final (Penghitungan 40% Aslab + 60% Dosbim)
    Route::get('/graduation', [MonitoringController::class, 'graduation'])->name('graduation');
    Route::post('/graduation/{id}/approve', [MonitoringController::class, 'finalApprove'])->name('graduation.approve');

    // 5. User Management
    Route::get('/users', [ResourceController::class, 'index'])->name('users');
    Route::post('/users', [ResourceController::class, 'store'])->name('users.store');
    Route::patch('/users/{id}/toggle', [ResourceController::class, 'toggleStatus'])->name('users.toggle');

    Route::get('/export-report', [MonitoringController::class, 'export'])->name('export');
});

// =============================================================================
// MAHASISWA
// =============================================================================

Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [ModuleController::class, 'dashboard'])->name('dashboard');

    // Modul & Repositori
    Route::get('/modules', [ModuleController::class, 'index'])->name('modules');
    Route::get('/modules/download/{id}', [ModuleController::class, 'download'])->name('modules.download');

    // Tugas / Submissions
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions');
    Route::get('/submissions/{id}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::post('/submissions/submit', [SubmissionController::class, 'submit'])->name('submissions.submit');

    // E-KHS / Progress
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress');

    // Fitur Scan QR (Wajib ada di sini agar terbaca mahasiswa.scan)
    Route::get('/scan', [ProgressController::class, 'showScanner'])->name('scan');
    Route::post('/attendance/store', [ProgressController::class, 'storeAttendance'])->name('attendance.store');

    Route::get('/logbook', [App\Http\Controllers\Mahasiswa\ProgressController::class, 'logbookIndex'])->name('logbook');
Route::post('/logbook', [App\Http\Controllers\Mahasiswa\ProgressController::class, 'logbookStore'])->name('logbook.store');

  // Pilih Praktikum di awal
    Route::get('/select-practicum', [ModuleController::class, 'showSelectPracticum'])->name('select-practicum');
    Route::post('/select-practicum', [ModuleController::class, 'storeSelectPracticum'])->name('select-practicum.store');

    Route::get('/dashboard', [ModuleController::class, 'dashboard'])->name('dashboard');
    Route::get('/modules', [ModuleController::class, 'index'])->name('modules');

    Route::get('/submissions/{id}', [ModuleController::class, 'show'])->name('submissions.show');

    Route::get('/modules', [ModuleController::class, 'index'])->name('modules');
    
    // Menu Tugas (Pastikan diarahkan ke ModuleController fungsi submissions)
    Route::get('/submissions', [ModuleController::class, 'submissions'])->name('submissions');

    // Detail Tugas
    Route::get('/submissions/{id}', [ModuleController::class, 'show'])->name('submissions.show');

    // Tambahkan di dalam group mahasiswa
Route::post('/submissions/submit-ujian-dosbim', [App\Http\Controllers\Mahasiswa\ModuleController::class, 'submitUjianDosbim'])->name('submissions.submit-dosbim');
});
// =============================================================================
// ASLAB
// =============================================================================
Route::middleware(['auth', 'role:aslab'])
     ->prefix('aslab')
     ->name('aslab.')
     ->group(function () {

    // Dashboard
    Route::get('/dashboard', [GradingController::class, 'dashboard'])->name('dashboard');

    // CMS Module CRUD (Menggunakan AslabModule)
    Route::get('/modules', [AslabModule::class, 'index'])->name('modules.index');
    Route::post('/modules', [AslabModule::class, 'store'])->name('modules.store');
    Route::put('/modules/{id}', [AslabModule::class, 'update'])->name('modules.update');
    Route::delete('/modules/{id}', [AslabModule::class, 'destroy'])->name('modules.destroy');

    // Penilaian
    Route::get('/grading', [GradingController::class, 'index'])->name('grading');
    Route::get('/grading/{id}', [GradingController::class, 'show'])->name('grading.show');
    Route::patch('/grading/{id}', [GradingController::class, 'update'])->name('grading.update');

    // Presensi Digital (QR Code)
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::get('/attendance/generate-qr', [AttendanceController::class, 'generateQR'])->name('attendance.qr');

    // FITUR BARU: Pemilihan Praktikum (Context Switch)
    Route::get('/select-practicum', [AslabModule::class, 'showSelectPracticum'])->name('select-practicum');
    Route::post('/select-practicum', [AslabModule::class, 'storeSelectPracticum'])->name('select-practicum.store');

    // FITUR BARU: Manajemen Sesi & Plotting Mahasiswa
    Route::get('/mahasiswa', [AslabModule::class, 'dataMahasiswa'])->name('mahasiswa');
    
    // PERBAIKAN DI SINI: Menggunakan AslabModule::class agar tidak bentrok dengan milik Mahasiswa
    Route::post('/mahasiswa/{id}/assign', [AslabModule::class, 'assignSession'])->name('mahasiswa.assign');

    Route::get('/sesi', [AslabModule::class, 'dataSesi'])->name('sesi');
    Route::post('/mahasiswa/{id}/assign-dosbim', [AslabModule::class, 'assignDosbim'])->name('mahasiswa.assign-dosbim');

    Route::post('/mahasiswa/{id}/nilai-awal', [AslabModule::class, 'storeNilaiAwal'])->name('mahasiswa.nilai-awal');
Route::post('/mahasiswa/{id}/nilai-remidi', [AslabModule::class, 'storeNilaiRemidi'])->name('mahasiswa.nilai-remidi');  
});

// =============================================
// DOSEN PEMBIMBING (CLEAN VERSION)
// =============================================
Route::middleware(['auth', 'role:dosen-pembimbing'])
     ->prefix('dosen')
     ->name('dosen-pembimbing.')
     ->group(function () {

    // 1. Pilih Praktikum
    Route::get('/select-practicum', [ReviewController::class, 'showSelectPracticum'])->name('select-practicum');
    Route::post('/select-practicum', [ReviewController::class, 'storeSelectPracticum'])->name('select-practicum.store');

    // 2. Dashboard
    Route::get('/dashboard', [ReviewController::class, 'mentoringIndex'])->name('dashboard');
    Route::post('/mentoring/bulk-upload', [ReviewController::class, 'bulkUploadUjian'])->name('mentoring.bulk-upload');
    Route::post('/mentoring/{id}/upload-soal', [ReviewController::class, 'uploadUjian'])->name('mentoring.upload-soal');

    // 3. Penilaian Ujian (Gunakan Nama Ini Secara Konsisten)
    Route::get('/penilaian-ujian', [ReviewController::class, 'koreksiIndex'])->name('penilaian-ujian.index');
    Route::post('/penilaian-ujian/{id}/simpan', [ReviewController::class, 'simpanNilaiUjian'])->name('penilaian-ujian.simpan');

    // 4. Validasi & Logbook
    Route::get('/validation', [ReviewController::class, 'index'])->name('validation');
    Route::patch('/validation/{id}/approve', [ReviewController::class, 'approve'])->name('validation.approve');
    Route::delete('/validation/{id}/reject', [ReviewController::class, 'reject'])->name('validation.reject');
    Route::get('/logbook', [App\Http\Controllers\Dosen\MonitoringController::class, 'logbook'])->name('logbook');
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
