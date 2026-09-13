<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Practicum; // Tambahkan ini
use App\Models\Enrollment; // Tambahkan ini
use Illuminate\Http\Request;
use App\Exports\GradesExport; 
use Maatwebsite\Excel\Facades\Excel; 

class MonitoringController extends Controller
{
    // ==========================================
    // 1. FITUR PILIH PRAKTIKUM (FIX ERROR)
    // ==========================================
    
    public function showSelectPracticum() 
    {
        // Ambil semua daftar praktikum untuk dipilih Kalab
        $practicums = Practicum::all();
        return view('kalab.select_practicum', compact('practicums'));
    }

    public function storeSelectPracticum(Request $request) 
    {
        $request->validate([
            'practicum_id' => 'required|exists:practicums,id'
        ]);

        // Simpan ID praktikum ke dalam session khusus Kalab
        session(['active_kalab_practicum_id' => $request->practicum_id]);
        
        return redirect()->route('kalab.dashboard');
    }

    // ==========================================
    // 2. DASHBOARD MASTER
    // ==========================================

    public function index()
    {
        // Proteksi: Jika belum pilih praktikum, arahkan ke halaman pilih
        $activeId = session('active_kalab_practicum_id');
        if (!$activeId) {
            return redirect()->route('kalab.select-practicum');
        }

        // Data untuk Stats Cards (Bisa difilter berdasarkan practicum_id jika diperlukan)
        $stats = [
            'total_mahasiswa' => User::whereHas('role', fn($q) => $q->where('slug', 'mahasiswa'))
                                     ->whereHas('enrollments', fn($q) => $q->where('practicum_id', $activeId))
                                     ->count(),
            'total_aslab'     => User::whereHas('role', fn($q) => $q->where('slug', 'aslab'))->count(),
            'user_aktif'      => User::where('is_active', true)->count(),
            'lulus_final'     => Enrollment::where('practicum_id', $activeId)
                                            ->whereNotNull('nilai_ujian_dosbim')
                                            ->get()
                                            ->filter(fn($s) => (($s->nilai_awal * 0.4) + ($s->nilai_ujian_dosbim * 0.6)) >= 65)
                                            ->count(), 
        ];

        // Data untuk Grafik Analytics Kelulusan (Contoh dummy)
        $analytics = [
            ['periode' => 'Genap 2023', 'lulus' => 45, 'gagal' => 5],
            ['periode' => 'Ganjil 2023', 'lulus' => 38, 'gagal' => 12],
            ['periode' => 'Genap 2024', 'lulus' => 52, 'gagal' => 3],
        ];

        return view('kalab.dashboard', compact('stats', 'analytics'));
    }

    // ==========================================
    // 3. KELULUSAN FINAL
    // ==========================================

    public function graduation() 
    {
        $activeId = session('active_kalab_practicum_id');
        if(!$activeId) return redirect()->route('kalab.select-practicum');

        // Ambil data enrollment yang memiliki user dan sudah dinilai Dosbim
        $students = Enrollment::where('practicum_id', $activeId)
                    ->whereHas('user') 
                    ->whereNotNull('nilai_ujian_dosbim')
                    ->with(['user', 'dosbim'])
                    ->get();

        return view('kalab.graduation', compact('students'));
    }

    // ==========================================
    // 4. EXPORT DATA
    // ==========================================

    public function export()
    {
        $activeId = session('active_kalab_practicum_id');
        $fileName = 'Laporan_Nilai_Final_' . date('Y-m-d') . '.csv';
        
        // Ambil data mahasiswa yang terdaftar di praktikum aktif
        $students = Enrollment::where('practicum_id', $activeId)
                                ->with('user')
                                ->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Nama Mahasiswa', 'NIM/Username', 'Nilai Aslab (40%)', 'Nilai Dosbim (60%)', 'Nilai Akhir', 'Status');

        $callback = function() use($students, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($students as $s) {
                $nilaiAslab = $s->nilai_awal ?? 0;
                $nilaiDosbim = $s->nilai_ujian_dosbim ?? 0;
                $nilaiAkhir = ($nilaiAslab * 0.4) + ($nilaiDosbim * 0.6);
                $status = ($nilaiAkhir >= 65) ? 'LULUS' : 'GAGAL';

                fputcsv($file, [
                    $s->user->name ?? 'N/A',
                    $s->user->username ?? 'N/A',
                    $nilaiAslab,
                    $nilaiDosbim,
                    $nilaiAkhir,
                    $status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}