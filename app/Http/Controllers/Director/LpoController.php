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

    /**
     * Display a listing of LPOs with filters (status, date range, search)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'DIRECTORS') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = Lpo::with(['vendor', 'requisition']);

        // Filter by status
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('lpo_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('lpo_date', '<=', $request->date_to);
        }
        if ($request->filled('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        // Search by LPO number, vendor name, or requisition number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('lpo_number', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function($vendorQ) use ($search) {
                      $vendorQ->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('requisition', function($reqQ) use ($search) {
                      $reqQ->where('requisition_number', 'like', "%{$search}%");
                  });
            });
        }

        $lpos = $query->orderBy('created_at', 'desc')->paginate(20);

        // For AJAX requests (live search)
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('director.lpos.partials.table_rows', compact('lpos'))->render(),
                'pagination' => (string) $lpos->appends(request()->query())->links(),
                'total' => $lpos->total(),
                'from' => $lpos->firstItem(),
                'to' => $lpos->lastItem(),
            ]);
        }

        // Counts for tabs/dashboard
        $allCount = Lpo::count();
        $pendingCount = Lpo::where('status', 'pending_director')->count();
        $approvedCount = Lpo::where('status', 'director_approved')->count();
        $rejectedCount = Lpo::where('status', 'director_rejected')->count();

        return view('director.lpos.index', compact('lpos', 'allCount', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    /**
     * Display the specified LPO
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'DIRECTORS') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $lpo = Lpo::with([
            'vendor',
            'requisition',
            'items.inventoryItem',
            'createdBy'
        ])->findOrFail($id);

        return view('director.lpos.show', compact('lpo'));
    }

    /**
     * UPDATE LPO items (quantities) - Director can edit quantities before approval
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:lpo_items,id',
            'items.*.quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $lpo = Lpo::with(['items'])->findOrFail($id);

            // Only allow editing if status is pending_director
            if ($lpo->status !== 'pending_director') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending LPOs can be edited.'
                ], 403);
            }

            // Update each item quantity
            foreach ($request->items as $itemData) {
                $lpoItem = $lpo->items()->find($itemData['item_id']);
                if ($lpoItem) {
                    $lpoItem->quantity_approved = $itemData['quantity'];
                    $lpoItem->save();
                }
            }

            // Recalculate totals
            $subtotal = $lpo->items->sum(function($item) {
                return $item->quantity_approved * $item->unit_cost;
            });

            $vatAmount = $subtotal * ($lpo->vat_rate / 100);
            $totalAmount = $subtotal + $vatAmount;

            $lpo->subtotal = $subtotal;
            $lpo->vat_amount = $vatAmount;
            $lpo->total_amount = $totalAmount;
            $lpo->save();

            DB::commit();

            Log::info('LPO quantities updated by Director', [
                'lpo_id' => $lpo->id,
                'lpo_number' => $lpo->lpo_number,
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount
            ]);

            // Return JSON response for AJAX, or redirect for regular form submission
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'LPO quantities updated successfully',
                    'data' => [
                        'subtotal' => $subtotal,
                        'vat_amount' => $vatAmount,
                        'total_amount' => $totalAmount
                    ]
                ]);
            }

            return redirect()->route('director.lpos.show', $lpo->id)
                ->with('success', 'LPO #' . $lpo->lpo_number . ' updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating LPO quantities', [
                'lpo_id' => $id,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating LPO: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error updating LPO: ' . $e->getMessage());
        }
    }

    /**
     * Approve LPO with director notes
     */
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
                ->with('success', 'LPO #' . $lpo->lpo_number . ' approved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving LPO', [
                'lpo_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Error approving LPO: ' . $e->getMessage());
        }
    }

    /**
     * Reject LPO with reason
     */
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

    /**
     * Download PDF for approved LPOs
     */
    public function downloadPdf($id)
    {
        $lpo = Lpo::with(['vendor', 'items.inventoryItem'])->findOrFail($id);

        $pdf = Pdf::loadView('director.lpos.pdf', compact('lpo'));

        return $pdf->download('LPO_' . $lpo->lpo_number . '.pdf');
    }
}
