<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        // Managing attendance logic
        return view('aslab.attendance.index');
    }

    public function mark(Request $request)
    {
        // Mark attendance logic
        return redirect()->back()->with('success', 'Attendance marked.');
    }
}
