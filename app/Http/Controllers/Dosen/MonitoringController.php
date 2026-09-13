<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        // 1. Ambil data mahasiswa dengan nilai di bawah 75 (Remidi)
        $remidiStudents = Submission::with(['user', 'module'])
                            ->where('score', '<', 75)
                            ->get();

        // Stats untuk dashboard
        $stats = [
            'total_remidi' => $remidiStudents->count(),
            'pending_validation' => \App\Models\Module::where('is_approved', false)->count(),
        ];

        return view('dosen.dashboard', compact('remidiStudents', 'stats'));
    }

public function logbook()
{
    $logbooks = \App\Models\Logbook::with('user')->latest()->get();
    return view('dosen.logbook', compact('logbooks'));
}

public function logbookComment(Request $request, $id)
{
    $log = \App\Models\Logbook::findOrFail($id);
    $log->update(['feedback' => $request->feedback]);

    return back()->with('success', 'Feedback bimbingan berhasil dikirim!');
}
}