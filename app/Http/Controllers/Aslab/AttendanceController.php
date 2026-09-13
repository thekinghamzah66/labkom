<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Practicum;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        // 1. Ambil ID Praktikum dari Session
        $activeId = session('active_practicum_id');

        // 2. Jika aslab belum pilih praktikum, lempar balik ke halaman pemilihan
        if (!$activeId) {
            return redirect()->route('aslab.select-practicum')
                             ->with('error', 'Silakan pilih praktikum yang sedang dikelola terlebih dahulu.');
        }

        // 3. Ambil Nama Praktikum (untuk ditampilkan di view jika butuh)
        $currentPracticum = Practicum::find($activeId);

        // 4. Buat token unik: Gabungan KODE-ID_PRAKTIKUM-TANGGAL
        // Contoh: LAB-1-2024-05-01
        $todayToken = "LAB-" . $activeId . "-" . Carbon::now()->format('Y-m-d');
        
        // 5. Ambil daftar mahasiswa yang sudah absen HANYA untuk praktikum ini dan hari ini
        $attendees = Attendance::with('user')
            ->where('practicum_id', $activeId) // FILTER BERDASARKAN PRAKTIKUM
            ->whereDate('date', Carbon::today()) // Pastikan pakai whereDate agar akurat
            ->latest()
            ->get();

        return view('aslab.attendance.index', compact('todayToken', 'attendees', 'currentPracticum'));
    }

    /**
     * Opsional: Fungsi untuk generate QR Code (jika dipisah)
     */
    public function generateQR()
    {
        $activeId = session('active_practicum_id');
        if (!$activeId) return redirect()->route('aslab.select-practicum');

        // Token ini yang akan di-scan oleh mahasiswa
        $token = "LAB-" . $activeId . "-" . Carbon::now()->format('Y-m-d');
        
        return view('aslab.attendance.qr_generator', compact('token'));
    }
}