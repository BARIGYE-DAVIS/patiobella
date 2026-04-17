<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcurementRequisitionController extends Controller
{
    /**
     * Display a listing of requisitions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check if user belongs to Procurement department
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = Requisition::with(['store', 'requestedBy', 'items']);

        // Search
        if ($request->filled('search')) {
            $query->where('requisition_number', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default show pending requisitions first
            $query->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected', 'fulfilled')");
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('procurement.requisitions.index', compact('requisitions'));
    }

    /**
     * Display the specified requisition.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = Requisition::with(['store', 'requestedBy', 'approvedBy', 'items.inventoryItem'])->findOrFail($id);

        return view('procurement.requisitions.show', compact('requisition'));
    }

    /**
     * Approve a requisition and redirect to PO creation.
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        DB::beginTransaction();

        try {
            $requisition = Requisition::findOrFail($id);

            if ($requisition->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requisitions can be approved.');
            }

            // Update requisition status
            $requisition->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Update approved quantities if provided
            if ($request->has('quantities')) {
                foreach ($request->quantities as $itemId => $quantity) {
                    RequisitionItem::where('id', $itemId)->update([
                        'quantity_approved' => $quantity
                    ]);
                }
            } else {
                // Approve all items with requested quantity
                foreach ($requisition->items as $item) {
                    $item->update([
                        'quantity_approved' => $item->quantity_requested
                    ]);
                }
            }

            DB::commit();

            Log::info('Requisition approved', [
                'user_id' => Auth::id(),
                'requisition_id' => $requisition->id,
                'requisition_number' => $requisition->requisition_number
            ]);

            // >>> CHANGE: redirect straight to Purchase Order creation with requisition_id <<<
            return redirect()->route('procurement.purchase-orders.create', ['requisition_id' => $requisition->id])
                ->with('success', 'Requisition approved successfully.<br>Now create the Purchase Order.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving requisition: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve requisition: ' . $e->getMessage());
        }
    }

    /**
     * Reject a requisition.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        DB::beginTransaction();

        try {
            $requisition = Requisition::findOrFail($id);

            if ($requisition->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requisitions can be rejected.');
            }

            $requisition->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,  // Use dedicated column
            ]);

            DB::commit();

            Log::info('Requisition rejected', [
                'user_id' => Auth::id(),
                'requisition_id' => $requisition->id,
                'requisition_number' => $requisition->requisition_number,
                'reason' => $request->rejection_reason
            ]);

            return redirect()->route('procurement.requisitions.index')
                ->with('success', 'Requisition rejected successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting requisition: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject requisition: ' . $e->getMessage());
        }
    }
}