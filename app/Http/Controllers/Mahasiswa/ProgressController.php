<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index()
    {
        // Tracking progress/grades logic
        return view('mahasiswa.progress.index');
    }
}
