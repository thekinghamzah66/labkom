<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TroubleshootingController extends Controller
{
    public function index()
    {
        // Technical troubleshooting logic
        return view('aslab.troubleshooting.index');
    }
}
