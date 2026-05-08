<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Lpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class LpoController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

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

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'DIRECTORS') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $tab = $request->get('tab', 'pending');

        $query = Lpo::with(['vendor', 'requisition']);

        if ($tab == 'pending') {
            $query->where('status', 'pending_director');
        } elseif ($tab == 'approved') {
            $query->where('status', 'director_approved');
        } elseif ($tab == 'rejected') {
            $query->where('status', 'director_rejected');
        }

        $lpos = $query->orderBy('created_at', 'desc')->paginate(20);

        $pendingCount = Lpo::where('status', 'pending_director')->count();
        $approvedCount = Lpo::where('status', 'director_approved')->count();
        $rejectedCount = Lpo::where('status', 'director_rejected')->count();

        return view('director.lpos.index', compact('lpos', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'DIRECTORS') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $lpo = Lpo::with(['vendor', 'requisition', 'items.inventoryItem', 'createdBy'])
            ->findOrFail($id);

        return view('director.lpos.show', compact('lpo'));
    }

// Approve LPO with director notes
public function approve(Request $request, $id)
{
    $request->validate([
        'director_notes' => 'nullable|string|max:1000',
    ]);

    DB::beginTransaction();
    
    try {
        $lpo = Lpo::with(['vendor', 'items.inventoryItem'])->findOrFail($id);

        if ($lpo->status !== 'pending_director') {
            return redirect()->back()->with('error', 'Only pending LPOs can be approved.');
        }

        $lpo->status = 'director_approved';
        $lpo->approved_by = Auth::id();
        $lpo->approved_at = now();

        // Save director notes
        if ($request->filled('director_notes')) {
            $lpo->director_notes = $request->director_notes;
        }
        $lpo->save();

        DB::commit();

        Log::info('LPO approved by Director', [
            'lpo_id' => $lpo->id,
            'lpo_number' => $lpo->lpo_number,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('director.lpos.index')
            ->with('success', 'LPO #' . $lpo->lpo_number . ' approved successfully with notes.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error approving LPO', [
            'lpo_id' => $id,
            'error' => $e->getMessage()
        ]);
        return redirect()->back()->with('error', 'Error approving LPO: ' . $e->getMessage());
    }
}

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            $lpo = Lpo::findOrFail($id);

            if ($lpo->status !== 'pending_director') {
                return redirect()->back()->with('error', 'Only pending LPOs can be rejected.');
            }

            $lpo->status = 'director_rejected';
            $lpo->rejection_reason = $request->rejection_reason;
            $lpo->approved_by = Auth::id();
            $lpo->approved_at = now();
            $lpo->save();

            DB::commit();

            Log::info('LPO rejected by Director', [
                'lpo_id' => $lpo->id,
                'lpo_number' => $lpo->lpo_number,
                'user_id' => Auth::id(),
                'reason' => $request->rejection_reason
            ]);

            return redirect()->route('director.lpos.index')
                ->with('success', 'LPO #' . $lpo->lpo_number . ' rejected.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting LPO', [
                'lpo_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Error rejecting LPO: ' . $e->getMessage());
        }
    }

    // Download PDF (for already approved LPOs)
    public function downloadPdf($id)
    {
        $lpo = Lpo::with(['vendor', 'items.inventoryItem'])->findOrFail($id);

        $pdf = Pdf::loadView('director.lpos.pdf', compact('lpo'));

        return $pdf->download('LPO_' . $lpo->lpo_number . '.pdf');
    }
}
