<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Practicum;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    // 1. Halaman Pilih Praktikum (Sama seperti Aslab)
    public function showSelectPracticum() {
        // Mahasiswa bisa memilih praktikum apa saja yang tersedia
        $practicums = Practicum::all(); 
        return view('mahasiswa.select_practicum', compact('practicums'));
    }

    public function storeSelectPracticum(Request $request) {
    $request->validate([
        'practicum_id' => 'required|exists:practicums,id'
    ]);

    $userId = auth()->id();
    $practicumId = $request->practicum_id;

    // KUNCI PERBAIKAN: 
    // Kita cek dulu, apakah mahasiswa ini sudah terdaftar di praktikum ini?
    // Jika belum, maka kita buatkan data baru di tabel enrollments.
    \App\Models\Enrollment::firstOrCreate(
        [
            'user_id' => $userId,
            'practicum_id' => $practicumId
        ],
        [
            'status_aslab' => 'pending' // Status awal saat baru daftar
        ]
    );

    // Simpan ke session agar dashboard Mahasiswa menampilkan praktikum yang benar
    session(['active_mhs_practicum_id' => $practicumId]);

    return redirect()->route('mahasiswa.dashboard')->with('success', 'Berhasil masuk ke praktikum.');
}

    // 2. Dashboard Mahasiswa (Info Sesi, Dosbim, & Status Lulus)
    public function dashboard() {
        $activeId = session('active_mhs_practicum_id');
        if(!$activeId) return redirect()->route('mahasiswa.select-practicum');

        // Ambil data pendaftaran mhs ini di praktikum tersebut
        $enrollment = Enrollment::where('user_id', auth()->id())
                                ->where('practicum_id', $activeId)
                                ->with(['dosbim', 'practicum'])
                                ->first();

        return view('mahasiswa.dashboard', compact('enrollment'));
    }

    // 3. Daftar Soal/Materi dari Aslab
  // Halaman MODUL (Menampilkan yang tipenya Materi)
public function index() { 
    $activeId = session('active_mhs_practicum_id');
    
    if(!$activeId) return redirect()->route('mahasiswa.select-practicum');

    $modules = Module::where('practicum_id', $activeId) // Filter Praktikum
                     ->where('type', 'Materi')          // Filter hanya Tipe Materi
                     ->with('userSubmission')
                     ->latest()
                     ->get();
    
    return view('mahasiswa.modules.index', compact('modules'));
}

public function submissions() { 
    $activeId = session('active_mhs_practicum_id');
    
    if(!$activeId) {
        return redirect()->route('mahasiswa.select-practicum');
    }

    // Ubah nama variabel dari $tasks menjadi $modules agar sesuai dengan file Blade
    $modules = Module::where('practicum_id', $activeId) // Filter Praktikum A / B
                   ->where('type', 'Tugas')            // Filter hanya Tipe Tugas
                   ->with('userSubmission')
                   ->latest()
                   ->get();
    
    return view('mahasiswa.submissions.index', compact('modules'));
}

public function show($id)
{
    // 1. Ambil Modul dan Submission (untuk cek status Lulus/Remidi)
    $module = \App\Models\Module::with('userSubmission')->findOrFail($id);

    // 2. Ambil Enrollment (untuk ambil data Dosbim & Soal Ujian dari Dosen)
    $enrollment = \App\Models\Enrollment::where('user_id', auth()->id())
        ->where('practicum_id', $module->practicum_id) // Pastikan ID Praktikum sinkron
        ->with('dosbim')
        ->first();

    return view('mahasiswa.submissions.show', [
        'module' => $module,
        'enrollment' => $enrollment
    ]);
}

public function submitUjianDosbim(Request $request)
{
    $request->validate([
        'enrollment_id' => 'required',
        'file_jawaban' => 'required|file|mimes:pdf,zip,rar|max:10240', // Maks 10MB
    ]);

    $enrollment = \App\Models\Enrollment::findOrFail($request->enrollment_id);

    // Keamanan: Pastikan yang upload adalah pemilik enrollment ini
    if ($enrollment->user_id !== auth()->id()) {
        return back()->with('error', 'Akses tidak sah.');
    }

    if ($request->hasFile('file_jawaban')) {
        // Simpan file jawaban ke folder public/jawaban_ujian
        $path = $request->file('file_jawaban')->store('jawaban_ujian', 'public');
        
        // Update kolom jawaban_ujian_dosbim di tabel enrollments
        $enrollment->update([
            'jawaban_ujian_dosbim' => $path
        ]);

        return back()->with('success', 'Jawaban ujian bimbingan berhasil dikirim ke Dosen Pembimbing!');
    }

    return back()->with('error', 'Gagal mengirim file.');
}


}