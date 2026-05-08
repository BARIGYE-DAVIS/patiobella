<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Lpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Department check
        if (!$user->department || $user->department->name !== 'DIRECTORS') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }
        
        $pendingCount = Lpo::where('status', 'pending_director')->count();
        $approvedCount = Lpo::where('status', 'director_approved')->count();
        $rejectedCount = Lpo::where('status', 'director_rejected')->count();
        
        $recentLpos = Lpo::with(['vendor'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('director.dashboard', compact('pendingCount', 'approvedCount', 'rejectedCount', 'recentLpos'));
    }
}