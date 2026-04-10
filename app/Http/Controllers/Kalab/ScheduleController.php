<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        // Practicum schedules logic
        return view('kalab.schedules.index');
    }

    public function finalize(Request $request)
    {
        // Finalize schedule logic
        return redirect()->back()->with('success', 'Schedule finalized.');
    }
}
