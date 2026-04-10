<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function dashboard()
    {
        // Dashboard logic - show overview
        return view('mahasiswa.dashboard');
    }

    public function index()
    {
        // Viewing modules logic
        return view('mahasiswa.modules.index');
    }
}
