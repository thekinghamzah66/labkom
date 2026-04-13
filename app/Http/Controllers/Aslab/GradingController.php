<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GradingController extends Controller
{
    public function index()
    {
        // Grading submissions logic
        return view('aslab.grading.index');
    }

    public function submit(Request $request)
    {
        // Submit grade logic
        return redirect()->back()->with('success', 'Grade submitted.');
    }
}
