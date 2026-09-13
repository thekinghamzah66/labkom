<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Practicum;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResourceController extends Controller
{
    // =========================================================================
    // 1. MANAJEMEN USER (ASLAB & MAHASISWA)
    // =========================================================================

    public function index(Request $request)
    {
        // Ambil data role untuk dropdown di modal tambah user
        $roles = Role::whereIn('slug', ['aslab', 'mahasiswa'])->get();

        // Ambil data user dengan filter role aslab & mahasiswa
        $query = User::with('role')->whereHas('role', function($q) {
            $q->whereIn('slug', ['aslab', 'mahasiswa']);
        });

        // Fitur Pencarian
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(10);

        return view('kalab.users.index', compact('users', 'roles'));
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'Status user berhasil diperbarui!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        return back()->with('success', 'User baru berhasil ditambahkan!');
    }

    // =========================================================================
    // 2. MANAJEMEN PRAKTIKUM (DENGAN PENUGASAN ASLAB OTOMATIS)
    // =========================================================================

    public function practicumIndex() 
    {
        // Ambil semua praktikum beserta siapa aslab yang ditugaskan (relasi users)
        $practicums = Practicum::with('users')->latest()->get();
        
        // Ambil daftar semua user yang rolenya 'aslab' untuk ditampilkan di checkbox Form
        $allAslabs = User::whereHas('role', function($q) {
            $q->where('slug', 'aslab');
        })->get();

        return view('kalab.practicums.index', compact('practicums', 'allAslabs'));
    }

    public function practicumStore(Request $request) 
    {
        $request->validate([
            'name' => 'required|unique:practicums,name',
            'aslab_ids' => 'required|array' // Kalab WAJIB centang minimal 1 aslab
        ]);

        // 1. Buat data praktikum baru
        $practicum = Practicum::create(['name' => $request->name]);

        // 2. HUBUNGKAN KE ASLAB (Proses penugasan otomatis ke tabel pivot)
        // Inilah yang membuat praktikum langsung muncul di menu Aslab
        $practicum->users()->attach($request->aslab_ids);

        return back()->with('success', 'Praktikum baru berhasil dibuat dan Aslab telah ditugaskan!');
    }

   public function practicumDestroy($id) 
{
    $practicum = Practicum::findOrFail($id);
    
    // 1. Hapus pendaftaran mahasiswa di praktikum ini
    $practicum->enrollments()->delete();

    // 2. Hapus modul/tugas di praktikum ini (Opsional, jika ingin bersih total)
    $practicum->modules()->delete();

    // 3. Hapus data absensi di praktikum ini
    $practicum->attendances()->delete();

    // 4. Putuskan hubungan dengan Aslab di tabel pivot
    $practicum->users()->detach();
    
    // 5. Terakhir, baru hapus praktikumnya
    $practicum->delete();
    
    return back()->with('success', 'Praktikum dan seluruh data terkait (Mahasiswa, Tugas, Absen) berhasil dihapus!');
}
    // =========================================================================
    // 3. KONTEKS PILIH PRAKTIKUM (FILTRASI DATA KALAB)
    // =========================================================================

    public function showSelectPracticum() 
    {
        $practicums = Practicum::all();
        return view('kalab.select_practicum', compact('practicums'));
    }

    public function storeSelectPracticum(Request $request) 
    {
        $request->validate(['practicum_id' => 'required|exists:practicums,id']);
        
        // Simpan ke session khusus Kalab agar Master Dashboard & Kelulusan terfilter
        session(['active_kalab_practicum_id' => $request->practicum_id]);
        
        return redirect()->route('kalab.dashboard');
    }

    // =========================================================================
    // 4. KELULUSAN FINAL (REKAP NILAI 40% ASLAB + 60% DOSBIM)
    // =========================================================================

    public function graduation() 
    {
        $activeId = session('active_kalab_practicum_id');
        
        // Jika Kalab belum pilih praktikum apa yang mau dipantau, arahkan ke halaman pilih
        if(!$activeId) return redirect()->route('kalab.select-practicum');

        // Ambil mahasiswa yang sudah diberikan nilai ujian oleh Dosbim (tidak null)
        $students = Enrollment::where('practicum_id', $activeId)
                                ->whereNotNull('nilai_ujian_dosbim')
                                ->with(['user', 'dosbim'])
                                ->get();

        return view('kalab.graduation', compact('students'));
    }
}