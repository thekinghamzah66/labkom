<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        // High-level monitoring logic
        return view('kalab.monitoring.index');
    }
}
