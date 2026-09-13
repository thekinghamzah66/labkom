<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Submission;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProgressController extends Controller
{
    /**
     * Halaman Utama Progres / E-KHS
     * Menampilkan daftar nilai dan feedback
     */
   public function index()
{
    // 1. Ambil ID Praktikum yang sedang dipilih dari session
    $activeId = session('active_mhs_practicum_id');

    // 2. Jika belum pilih praktikum, arahkan ke halaman pemilihan
    if (!$activeId) {
        return redirect()->route('mahasiswa.select-practicum');
    }

    // 3. Ambil daftar Modul & Tugas HANYA untuk praktikum yang sedang aktif
    // Kita sertakan relasi userSubmission untuk mengambil nilai mahasiswa tersebut
    $progress = \App\Models\Module::where('practicum_id', $activeId)
        ->with(['userSubmission' => function($query) {
            $query->where('user_id', auth()->id());
        }])
        ->latest()
        ->get();

    return view('mahasiswa.progress.index', compact('progress'));
}

    /**
     * Menampilkan halaman scanner kamera
     */
    public function showScanner()
    {
        return view('mahasiswa.attendance.scan');
    }

    /**
     * Memproses data hasil scan QR
     */
   public function storeAttendance(Request $request)
{
    try {
        // 1. Ambil data dari QR Code & Session
        $qrCode = $request->qr_code; // Format: LAB-1-2026-06-28
        $activeId = session('active_mhs_practicum_id');

        if (!$activeId) {
            return response()->json(['success' => false, 'message' => 'Pilih praktikum terlebih dahulu di Dashboard!']);
        }

        // 2. Pecah token berdasarkan tanda "-"
        $data = explode('-', $qrCode);

        // 3. Validasi format token (Harus ada 5 bagian: LAB, ID, Y, M, D)
        if (count($data) < 5 || $data[0] !== 'LAB') {
            return response()->json(['success' => false, 'message' => 'Format QR Code tidak dikenali atau salah!']);
        }

        $practicumIdFromQR = $data[1];
        $dateFromQR = $data[2] . '-' . $data[3] . '-' . $data[4]; // Menghasilkan: 2026-06-28

        // 4. CEK: Apakah praktikum di QR sama dengan praktikum yang sedang diikuti?
        if ($practicumIdFromQR != $activeId) {
            return response()->json(['success' => false, 'message' => 'Ini bukan QR Code untuk praktikum yang Anda ikuti sekarang!']);
        }

        // 5. CEK: Apakah tanggal di QR adalah tanggal hari ini?
        if ($dateFromQR !== now()->toDateString()) {
            return response()->json(['success' => false, 'message' => 'QR Code ini sudah kadaluarsa (bukan untuk hari ini)!']);
        }

        // 6. CEK: Apakah mahasiswa sudah absen hari ini di praktikum ini?
        $alreadyAttended = \App\Models\Attendance::where('user_id', auth()->id())
            ->where('practicum_id', $activeId)
            ->whereDate('date', now())
            ->exists();

        if ($alreadyAttended) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan presensi hari ini!']);
        }

        /**
         * 7. SIMPAN PRESENSI
         * Catatan: Saya menghapus kolom 'time' karena biasanya default Laravel hanya pakai 'date'
         * Jika tabelmu punya kolom 'time', silakan tambahkan lagi di bawah.
         */
        \App\Models\Attendance::create([
            'user_id'      => auth()->id(),
            'practicum_id' => $activeId,
            'date'         => now()->toDateString(),
            'status'       => 'Hadir',
             'session_token' => $qrCode
        ]);

        return response()->json(['success' => true, 'message' => 'Presensi berhasil dicatat!']);

    } catch (\Exception $e) {
        // Jika ada error database (misal kolom tidak ada), pesan aslinya akan muncul di modal
        return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
    }
}
    public function logbookIndex()
{
    // Ambil riwayat logbook milik mahasiswa yang sedang login
    $logs = \App\Models\Logbook::where('user_id', auth()->id())->latest()->get();
    return view('mahasiswa.logbook', compact('logs'));
}

public function logbookStore(Request $request)
{
    $request->validate([
        'judul' => 'required|string|max:255',
        'aktivitas' => 'required|string',
    ]);

    \App\Models\Logbook::create([
        'user_id' => auth()->id(),
        'judul' => $request->judul,
        'aktivitas' => $request->aktivitas,
    ]);

    return back()->with('success', 'Laporan bimbingan berhasil dikirim ke Dosen Pembimbing!');
}

}