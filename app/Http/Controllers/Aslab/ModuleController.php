<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Practicum;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    // =========================================================================
    // 1. LOGIKA PILIH PRAKTIKUM (CONTEXT SWITCHING)
    // =========================================================================
    
    public function showSelectPracticum() {
        // Mengambil praktikum yang dipegang aslab ini melalui tabel pivot
        $practicums = auth()->user()->practicums; 
        return view('aslab.select_practicum', compact('practicums'));
    }

    public function storeSelectPracticum(Request $request) {
        $request->validate(['practicum_id' => 'required|exists:practicums,id']);
        // Simpan ke session untuk memfilter semua data di menu lain
        session(['active_practicum_id' => $request->practicum_id]);
        return redirect()->route('aslab.dashboard');
    }

    // =========================================================================
    // 2. MANAJEMEN PLOTTING SESI (ANTREAN MAHASISWA)
    // =========================================================================

    public function dataMahasiswa() {
        if (!session()->has('active_practicum_id')) {
            return redirect()->route('aslab.select-practicum')->with('error', 'Silakan pilih praktikum terlebih dahulu!');
        }

        $activeId = session('active_practicum_id');
        // Mahasiswa yang mendaftar tapi belum di-plot jadwalnya (session_name masih null)
        $students = Enrollment::where('practicum_id', $activeId)
                                ->whereNull('session_name')
                                ->with('user')
                                ->get();
                            
        return view('aslab.mahasiswa_list', compact('students'));
    }

    public function assignSession(Request $request, $id) {
        $request->validate([
            'session_name' => 'required',
            'jam' => 'required',
            'ruangan' => 'required'
        ]);

        $enrollment = Enrollment::findOrFail($id);
        $enrollment->update([
            'session_name' => $request->session_name,
            'jam'          => $request->jam,
            'ruangan'      => $request->ruangan,
        ]);

        return back()->with('success', 'Berhasil menetapkan sesi untuk ' . $enrollment->user->name);
    }

    // =========================================================================
    // 3. MANAJEMEN DOSBIM & DATA SESI
    // =========================================================================

    public function dataSesi() {
        $activeId = session('active_practicum_id');
        if(!$activeId) return redirect()->route('aslab.select-practicum');

        $sessions = Enrollment::where('practicum_id', $activeId)
                                ->whereNotNull('session_name')
                                ->with(['user', 'dosbim'])
                                ->get()
                                ->groupBy('session_name');

        // Mengambil daftar Dosen Pembimbing untuk dropdown
        $listDosbim = User::whereHas('role', function($q) {
            $q->where('slug', 'dosen-pembimbing');
        })->get();

        return view('aslab.sesi_list', compact('sessions', 'listDosbim'));
    }

    public function assignDosbim(Request $request, $id) {
        $request->validate(['dosbim_id' => 'required|exists:users,id']);
        
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->update(['dosbim_id' => $request->dosbim_id]);

        return back()->with('success', 'Dosen Pembimbing berhasil ditugaskan!');
    }

    // =========================================================================
    // 4. CMS MATERI & TUGAS (DENGAN SOAL REMIDI)
    // =========================================================================

    public function index() {
        $activeId = session('active_practicum_id');
        if(!$activeId) return redirect()->route('aslab.select-practicum');

        $modules = Module::where('practicum_id', $activeId)->latest()->get();
        return view('aslab.modules.index', compact('modules'));
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required',
            'type' => 'required', // 'Materi' atau 'Tugas'
            'deadline' => 'required',
            'file_materi' => 'required|max:102400', 
            'file_remidi' => 'nullable|max:102400', // Opsional
        ]);

        $data = [
            'practicum_id' => session('active_practicum_id'),
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'deadline' => $request->deadline,
        ];

        // Upload File Utama
        if ($request->hasFile('file_materi')) {
            $file = $request->file('file_materi');
            $name = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('modules', $name, 'public'); 
            $data['file_path'] = 'modules/' . $name;
        }

        // Upload File Remidi (Jika Ada)
        if ($request->hasFile('file_remidi')) {
            $fileRem = $request->file('file_remidi');
            $nameRem = 'remidi_' . time() . '_' . Str::slug($request->title) . '.' . $fileRem->getClientOriginalExtension();
            $fileRem->storeAs('modules', $nameRem, 'public'); 
            $data['file_remidi'] = 'modules/' . $nameRem;
        }

        Module::create($data);
        return back()->with('success', 'Konten Berhasil Diterbitkan!');
    }

    public function update(Request $request, $id) {
        $module = Module::findOrFail($id);
        
        $module->title = $request->title;
        $module->description = $request->description;
        $module->type = $request->type;
        $module->deadline = $request->deadline;

        // Update File Utama jika ada upload baru
        if ($request->hasFile('file_materi')) {
            Storage::disk('public')->delete($module->file_path); 
            $path = $request->file('file_materi')->store('modules', 'public');
            $module->file_path = $path;
        }

        // Update File Remidi jika ada upload baru
        if ($request->hasFile('file_remidi')) {
            if($module->file_remidi) Storage::disk('public')->delete($module->file_remidi);
            $pathRem = $request->file('file_remidi')->store('modules', 'public');
            $module->file_remidi = $pathRem;
        }

        $module->save();
        return back()->with('success', 'Materi/Tugas berhasil diperbarui!');
    }

    public function destroy($id) {
        $module = Module::findOrFail($id);
        Storage::disk('public')->delete($module->file_path);
        if($module->file_remidi) Storage::disk('public')->delete($module->file_remidi);
        $module->delete();
        return back()->with('success', 'Konten telah dihapus!');
    }

    // =========================================================================
    // 5. PENILAIAN BERJENJANG (MANUAL INPUT)
    // =========================================================================

    public function storeNilaiAwal(Request $request, $id) {
        $request->validate(['nilai_awal' => 'required|numeric|min:0|max:100']);
        $enrollment = Enrollment::findOrFail($id);

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

        // Jika nilai remidi tetap di bawah 65, maka status menjadi GAGAL
        $status = ($request->nilai_remidi >= 65) ? 'lulus' : 'gagal';

        $enrollment->update([
            'nilai_remidi' => $request->nilai_remidi,
            'status_aslab' => $status
        ]);

        return back()->with('success', 'Nilai remidi berhasil disimpan. Status Akhir: ' . strtoupper($status));
    }
}