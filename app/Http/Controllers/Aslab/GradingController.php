<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Enrollment; 
use App\Models\User;       
use App\Models\Practicum; 

class GradingController extends Controller
{
    public function dashboard()
    {
        return view('aslab.dashboard');
    }

   public function index() {
    $activeId = session('active_practicum_id');
    if(!$activeId) return redirect()->route('aslab.select-practicum');

    // Ambil semua JAWABAN mahasiswa yang sesuai dengan praktikum aktif
    $submissions = \App\Models\Submission::whereHas('module', function($q) use ($activeId) {
        $q->where('practicum_id', $activeId);
    })->with(['user', 'module'])->latest()->get();

    return view('aslab.grading.index', compact('submissions'));
}

    // ==========================================
    // LOGIKA PENILAIAN BERJENJANG (FITUR BARU)
    // ==========================================

    public function storeNilaiAwal(Request $request, $id) {
        $request->validate(['nilai_awal' => 'required|numeric|min:0|max:100']);
        
        $enrollment = Enrollment::findOrFail($id);
        
        // Logic: Jika >= 65 Lulus, jika tidak maka Remidi
        $status = ($request->nilai_awal >= 65) ? 'lulus' : 'remidi';

        $enrollment->update([
            'nilai_awal' => $request->nilai_awal,
            'status_aslab' => $status
        ]);

        return back()->with('success', 'Nilai awal berhasil disimpan. Status: ' . strtoupper($status));
    }

    public function storeNilaiRemidi(Request $request, $id) {
        $request->validate(['nilai_remidi' => 'required|numeric|min:0|max:100']);
        
        $enrollment = Enrollment::findOrFail($id);

        // Logic Tahap Akhir: Jika >= 65 Lulus, jika tidak maka Gagal Total
        $status = ($request->nilai_remidi >= 65) ? 'lulus' : 'gagal';

        $enrollment->update([
            'nilai_remidi' => $request->nilai_remidi,
            'status_aslab' => $status
        ]);

        return back()->with('success', 'Nilai remidi berhasil disimpan. Status Akhir: ' . strtoupper($status));
    }

    // ==========================================
    // LOGIKA SUBMISSION MATERI (KODE INTI KAMU)
    // ==========================================

    public function show($id)
    {
        $submission = Submission::with(['user', 'module'])->findOrFail($id);
        return view('aslab.grading.show', compact('submission'));
    }

    public function update(Request $request, $id) {
    $request->validate([
        'score' => 'required|numeric|min:0|max:100',
        'status' => 'required' // Lulus, Remidi, Gagal
    ]);

    $submission = \App\Models\Submission::findOrFail($id);
    $submission->update([
        'score' => $request->score,
        'status' => $request->status,
    ]);

    // SYNC KE TABEL ENROLLMENT
    $enrollment = \App\Models\Enrollment::where('user_id', $submission->user_id)
        ->where('practicum_id', session('active_practicum_id'))
        ->first();

    if ($enrollment) {
        $enrollment->update([
            'status_aslab' => strtolower($request->status),
            'nilai_awal'   => $request->score // Simpan skor ke enrollment juga
        ]);
    }

    return back()->with('success', 'Penilaian berhasil disimpan!');
}
}