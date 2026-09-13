<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Submission; // Wajib panggil model ini
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    public function index()
    {
        $modules = Module::latest()->get();
        return view('mahasiswa.submissions.index', compact('modules'));
    }

    public function show($id)
    {
        $module = Module::findOrFail($id);
        return view('mahasiswa.submissions.show', compact('module'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'file_tugas' => 'required|max:10240', // 10MB
        ]);

        if ($request->hasFile('file_tugas')) {
            $file = $request->file('file_tugas');
            $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $namaFile = time() . '_' . $safeName . '.' . $file->getClientOriginalExtension();

            // 1. Simpan file fisik
            $file->storeAs('modules', $namaFile, 'public');

            // 2. SIMPAN DATA KE DATABASE (BAGIAN INI YANG PENTING)
            Submission::create([
                'user_id'   => auth()->id(),       // ID Mahasiswa yang login
                'module_id' => $request->module_id, // ID Modul yang dikerjakan
                'file_path' => 'modules/' . $namaFile,
                'status'    => 'Pending',          // Status awal
            ]);

            return redirect()->route('mahasiswa.dashboard')->with('success', 'Tugas berhasil diunggah dan tercatat di sistem!');
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }
}