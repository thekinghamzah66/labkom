<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        // Monitoring overall student academic progress logic
        return view('dosen.monitoring.index');
    }
}
