<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManagementRequisitionController extends Controller
{
    /**
     * List all requisitions with tabs and filters
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $tab = $request->get('tab', 'pending');

        $query = Requisition::with(['store', 'requestedBy', 'items.batch.inventoryItem', 'items.inventoryItem']);

        // Filter by tab
        if ($tab == 'pending') {
            $query->where('status', 'pending');
        } elseif ($tab == 'approved') {
            $query->where('status', 'approved');
        } elseif ($tab == 'rejected') {
            $query->where('status', 'rejected');
        }

        // Apply date filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('requisition_number', 'like', "%{$search}%")
                  ->orWhereHas('store', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get counts for tabs
        $pendingCount = Requisition::where('status', 'pending')->count();
        $approvedCount = Requisition::where('status', 'approved')->count();
        $rejectedCount = Requisition::where('status', 'rejected')->count();

        return view('management.requisitions.index', compact('requisitions', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    /**
     * Show single requisition details with batch information
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = Requisition::with([
            'store',
            'requestedBy',
            'approvedBy',
            'items.batch.inventoryItem',
            'items.inventoryItem'
        ])->findOrFail($id);

        return view('management.requisitions.show', compact('requisition'));
    }

    /**
     * Show approval form with batch details
     */
    public function approveForm($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = Requisition::with([
            'items.batch.inventoryItem',
            'items.inventoryItem',
            'store',
            'requestedBy'
        ])->findOrFail($id);

        if ($requisition->status !== 'pending') {
            return redirect()->route('management.requisitions.show', $requisition->id)
                ->with('error', 'Only pending requisitions can be approved.');
        }

        return view('management.requisitions.approve', compact('requisition'));
    }

    /**
     * Process approval with quantities
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:requisition_items,id',
            'items.*.quantity_approved' => 'required|numeric|min:0',
            'approval_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $requisition = Requisition::findOrFail($id);

            if ($requisition->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requisitions can be approved.');
            }

            $totalApprovedValue = 0;

            // Additional validation: Check each approved quantity doesn't exceed requested
            foreach ($request->items as $itemData) {
                $requisitionItem = RequisitionItem::findOrFail($itemData['item_id']);

                // Server-side check for exceeding requested quantity
                if ($itemData['quantity_approved'] > $requisitionItem->quantity_requested) {
                    throw new \Exception("Approved quantity (" . $itemData['quantity_approved'] . ") cannot exceed requested quantity (" . $requisitionItem->quantity_requested . ") for item: " .
                        ($requisitionItem->inventoryItem ? $requisitionItem->inventoryItem->name : 'Unknown Item'));
                }

                // Server-side check for negative quantity
                if ($itemData['quantity_approved'] < 0) {
                    throw new \Exception("Approved quantity cannot be negative.");
                }

                // Update the approved quantity
                $requisitionItem->quantity_approved = $itemData['quantity_approved'];
                $requisitionItem->save();

                // Calculate total approved value
                $unitCost = $requisitionItem->unit_cost ?? ($requisitionItem->batch ? $requisitionItem->batch->unit_cost : 0);
                $totalApprovedValue += $itemData['quantity_approved'] * $unitCost;
            }

            // Update requisition status
            $requisition->status = 'approved';
            $requisition->approved_by = Auth::id();
            $requisition->approved_at = now();

            // Add GM approval notes to gm_notes field
            if ($request->filled('approval_notes')) {
                $requisition->gm_notes = $request->approval_notes;
            }
            $requisition->save();

            DB::commit();

            Log::info('Requisition approved by GM', [
                'requisition_id' => $id,
                'requisition_number' => $requisition->requisition_number,
                'user_id' => Auth::id(),
                'items_approved' => count($request->items),
                'total_approved_value' => $totalApprovedValue
            ]);

            return redirect()->route('management.requisitions.index')
                ->with('success', 'Requisition #' . $requisition->requisition_number . ' approved successfully with specified quantities.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving requisition', [
                'requisition_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Error approving requisition: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Reject requisition
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            $requisition = Requisition::findOrFail($id);

            if ($requisition->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requisitions can be rejected.');
            }

            $requisition->status = 'rejected';
            $requisition->rejection_reason = $request->rejection_reason;
            $requisition->approved_by = Auth::id();
            $requisition->approved_at = now();
            $requisition->save();

            DB::commit();

            Log::info('Requisition rejected by GM', [
                'requisition_id' => $id,
                'requisition_number' => $requisition->requisition_number,
                'user_id' => Auth::id(),
                'reason' => $request->rejection_reason
            ]);

            return redirect()->route('management.requisitions.index')
                ->with('success', 'Requisition #' . $requisition->requisition_number . ' rejected.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting requisition', [
                'requisition_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Error rejecting requisition: ' . $e->getMessage());
        }
    }

    /**
     * Get all requisitions (for filtering)
     */
    public function all(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = Requisition::with(['store', 'requestedBy', 'items.batch.inventoryItem']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('management.requisitions.all', compact('requisitions'));
    }

    /**
     * Get requisition summary statistics for dashboard
     */
    public function getStatistics()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $statistics = [
            'pending' => Requisition::where('status', 'pending')->count(),
            'approved' => Requisition::where('status', 'approved')->count(),
            'rejected' => Requisition::where('status', 'rejected')->count(),
            'ordered' => Requisition::where('status', 'ordered')->count(),
            'fulfilled' => Requisition::where('status', 'fulfilled')->count(),
            'total_value_approved' => RequisitionItem::whereHas('requisition', function($q) {
                $q->where('status', 'approved');
            })->sum(DB::raw('quantity_approved * COALESCE(unit_cost, 0)'))
        ];

        return response()->json($statistics);
    }

    /**
     * Export requisition as PDF
     */
    public function exportPdf($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = Requisition::with([
            'store',
            'requestedBy',
            'approvedBy',
            'items.batch.inventoryItem',
            'items.inventoryItem'
        ])->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('management.requisitions.pdf', compact('requisition'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('requisition-' . $requisition->requisition_number . '.pdf');
    }
}
