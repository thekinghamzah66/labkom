<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index()
    {
        // Submitting code/reports logic
        return view('mahasiswa.submissions.index');
    }

    public function submit(Request $request)
    {
        // Submit submission logic
        return redirect()->back()->with('success', 'Submission submitted.');
    }
}
