<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        // Reviewing student final reports logic
        return view('dosen.reviews.index');
    }

    public function submit(Request $request)
    {
        // Submit review logic
        return redirect()->back()->with('success', 'Review submitted.');
    }
}
