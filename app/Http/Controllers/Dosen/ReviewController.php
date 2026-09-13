<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Practicum;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    // ==========================================
    // 1. FITUR PILIH PRAKTIKUM (PINTU MASUK)
    // ==========================================
    public function showSelectPracticum() 
    {
        // Menampilkan praktikum yang di dalamnya ada mahasiswa bimbingan dosen ini
        $practicums = Practicum::whereHas('enrollments', function($q) {
            $q->where('dosbim_id', auth()->id());
        })->get();

        return view('dosen.select_practicum', compact('practicums'));
    }

    public function storeSelectPracticum(Request $request) 
    {
        $request->validate(['practicum_id' => 'required']);
        session(['active_dosbim_practicum_id' => $request->practicum_id]);
        return redirect()->route('dosen-pembimbing.dashboard');
    }

    // ==========================================
    // 2. MONITORING MAHASISWA (REPLACEMENT DASHBOARD)
    // ==========================================
    public function mentoringIndex() 
    {
        $activeId = session('active_dosbim_practicum_id');
        if(!$activeId) return redirect()->route('dosen-pembimbing.select-practicum');

        // Ambil mahasiswa yang dibimbing dosen ini, di matkul ini, dan SUDAH LULUS aslab
        $students = Enrollment::where('practicum_id', $activeId)
                                ->where('dosbim_id', auth()->id())
                                ->where('status_aslab', 'lulus')
                                ->with('user')
                                ->get();

        return view('dosen.mentoring.index', compact('students'));
    }

    // ==========================================
    // 3. VALIDASI MATERI ASLAB (KODE LAMA)
    // ==========================================
    public function index()
    {
        $pendingModules = Module::where('is_approved', false)->latest()->get();
        return view('dosen.validation', compact('pendingModules'));
    }

    public function approve($id)
    {
        $module = Module::findOrFail($id);
        $module->update(['is_approved' => true]);
        return back()->with('success', 'Soal/Materi "' . $module->title . '" berhasil disetujui!');
    }
    
    public function reject($id)
    {
        $module = Module::findOrFail($id);
        $module->delete();
        return back()->with('success', 'Soal berhasil ditolak dan dihapus.');
    }

    // ==========================================
    // 4. KELOLA UJIAN UMUM (UTP/UAS) (KODE LAMA)
    // ==========================================
    public function examIndex()
    {
        $exams = Module::whereIn('type', ['UTP', 'UAS'])->latest()->get();
        return view('dosen.exams', compact('exams'));
    }

    public function examStore(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'deadline' => 'required',
            'file_materi' => 'required',
        ]);

        $file = $request->file('file_materi');
        $namaFile = time() . '_' . Str::slug($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('modules', $namaFile, 'public');

        Module::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'deadline' => $request->deadline,
            'file_path' => 'modules/' . $namaFile,
            'is_approved' => true,
        ]);

        return back()->with('success', 'Soal Berhasil Diterbitkan!');
    }

    public function examUpdate(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        $request->validate([
            'title' => 'required',
            'type' => 'required',
            'deadline' => 'required',
        ]);

        $module->title = $request->title;
        $module->description = $request->description;
        $module->type = $request->type;
        $module->deadline = $request->deadline;

        if ($request->hasFile('file_materi')) {
            Storage::delete('public/' . $module->file_path);
            $file = $request->file('file_materi');
            $namaFile = time() . '_' . Str::slug($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('modules', $namaFile, 'public');
            $module->file_path = 'modules/' . $namaFile;
        }

        $module->save();
        return back()->with('success', 'Soal Ujian berhasil diperbarui!');
    }

    public function examDestroy($id)
    {
        $module = Module::findOrFail($id);
        Storage::delete('public/' . $module->file_path);
        $module->delete();
        return back()->with('success', 'Soal Ujian berhasil dihapus!');
    }

    // ==========================================
    // 5. UPLOAD SOAL KHUSUS PER MAHASISWA
    // ==========================================
    public function uploadUjian(Request $request, $enrollmentId) 
    {
        $request->validate(['soal_ujian' => 'required|mimes:pdf,zip,rar|max:10000']);
        
        $enrollment = Enrollment::findOrFail($enrollmentId);
        
        if ($request->hasFile('soal_ujian')) {
            // Hapus file lama jika ada
            if($enrollment->soal_ujian_dosbim) {
                Storage::delete('public/' . $enrollment->soal_ujian_dosbim);
            }

            $path = $request->file('soal_ujian')->store('soal_ujian', 'public');
            $enrollment->update([
                'soal_ujian_dosbim' => $path
            ]);
        }

        return back()->with('success', 'Soal ujian bimbingan berhasil dikirim ke mahasiswa.');
    }

    public function bulkUploadUjian(Request $request) 
{
    $request->validate([
        'soal_ujian' => 'required|file|mimes:pdf,zip,rar|max:10240'
    ]);

    $activeId = session('active_dosbim_practicum_id');
    $dosbimId = auth()->id();

    if ($request->hasFile('soal_ujian')) {
        // 1. Simpan file soal satu kali saja
        $path = $request->file('soal_ujian')->store('soal_ujian', 'public');
        
        // 2. Update SEMUA mahasiswa bimbingan dosen ini di praktikum ini yang sudah LULUS aslab
        Enrollment::where('practicum_id', $activeId)
                    ->where('dosbim_id', $dosbimId)
                    ->where('status_aslab', 'lulus')
                    ->update([
                        'soal_ujian_dosbim' => $path
                    ]);
                    
        return back()->with('success', 'Berhasil membroadcast soal ujian ke semua mahasiswa bimbingan!');
    }

    return back()->with('error', 'Gagal mengupload file.');
}

// 1. Menampilkan daftar mahasiswa yang sudah mengumpulkan jawaban ujian dosbim
public function koreksiIndex() {
    $activeId = session('active_dosbim_practicum_id');
    if(!$activeId) return redirect()->route('dosen-pembimbing.select-practicum');

    $submissions = \App\Models\Enrollment::where('practicum_id', $activeId)
                                ->where('dosbim_id', auth()->id())
                                ->whereNotNull('jawaban_ujian_dosbim') 
                                ->with('user')
                                ->get();

    // SESUAIKAN DENGAN NAMA FILE DI FOLDER DOSEN
    return view('dosen.koreksi_ujian', compact('submissions'));
}

// 2. Menyimpan skor ujian dari Dosbim (Porsi 60%)
public function simpanNilaiUjian(Request $request, $id) {
    $request->validate([
        'nilai_ujian' => 'required|numeric|min:0|max:100',
        'status' => 'required' // Lulus, Remidi, atau Gagal
    ]);
    
    $enrollment = \App\Models\Enrollment::findOrFail($id);
    $enrollment->update([
        'nilai_ujian_dosbim' => $request->nilai_ujian,
        'status_dosbim' => strtolower($request->status) // Simpan status dosbim
    ]);

    return back()->with('success', 'Penilaian bimbingan berhasil disimpan!');
}

public function simpanRemidiDosbim(Request $request, $id) {
    $request->validate(['nilai_remidi' => 'required|numeric|min:0|max:100']);
    $enrollment = Enrollment::findOrFail($id);
    
    $status = ($request->nilai_remidi >= 65) ? 'lulus' : 'gagal';
    
    $enrollment->update([
        'nilai_remidi_dosbim' => $request->nilai_remidi,
        'status_dosbim' => $status
    ]);

    return back()->with('success', 'Nilai remidi ujian berhasil disimpan!');
}

}