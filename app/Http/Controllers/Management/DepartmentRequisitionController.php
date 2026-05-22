<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\DepartmentRequisition;
use App\Models\Department;
use App\Models\User;
use App\Models\InventoryItem;
use App\Models\DepartmentRequisitionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentRequisitionController extends Controller
{
    /**
     * Display a listing of department requisitions.
     */
    public function index(Request $request)
    {
        $query = DepartmentRequisition::with(['department', 'requestedBy', 'approvedBy', 'items']);

        // Filter by status
        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            if ($status === 'issued') {
                $query->whereIn('status', ['partially_issued', 'issued']);
            } else {
                $query->where('status', $status);
            }
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('requisition_number', 'like', "%{$search}%")
                  ->orWhereHas('department', function($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Add calculated fields
        foreach ($requisitions as $req) {
            $req->total_items = $req->items->count();
            $req->total_quantity_requested = $req->items->sum('quantity_requested');
            $req->requisition_type_label = $this->getRequisitionTypeLabel($req->requisition_type);
        }

        $departments = Department::where('is_active', true)
            ->whereIn('name', ['KITCHEN', 'RESTAURANT', 'BAR'])
            ->orderBy('name')
            ->get();

        $statuses = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'partially_issued' => 'Partially Issued',
            'issued' => 'Issued',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed'
        ];

        return view('management.department-requisitions.index', compact('requisitions', 'departments', 'statuses'));
    }

    /**
     * Get requisitions as JSON for AJAX.
     */
    public function getRequisitionsJson(Request $request)
    {
        $query = DepartmentRequisition::with(['department', 'requestedBy', 'approvedBy', 'items']);

        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            if ($status === 'issued') {
                $query->whereIn('status', ['partially_issued', 'issued']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('requisition_number', 'like', "%{$search}%")
                  ->orWhereHas('department', function($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

        foreach ($requisitions as $req) {
            $req->total_items = $req->items->count();
            $req->total_quantity_requested = $req->items->sum('quantity_requested');
            $req->requisition_type_label = $this->getRequisitionTypeLabel($req->requisition_type);
        }

        $summary = [
            'total' => DepartmentRequisition::count(),
            'pending' => DepartmentRequisition::where('status', 'pending')->count(),
            'approved' => DepartmentRequisition::where('status', 'approved')->count(),
            'issued' => DepartmentRequisition::whereIn('status', ['partially_issued', 'issued'])->count(),
            'rejected' => DepartmentRequisition::where('status', 'rejected')->count(),
            'completed' => DepartmentRequisition::where('status', 'completed')->count(),
        ];

        return response()->json([
            'success' => true,
            'requisitions' => $requisitions,
            'summary' => $summary
        ]);
    }

    /**
     * Display the specified requisition.
     */
    public function show($id)
    {
        $requisition = DepartmentRequisition::with([
            'department',
            'requestedBy',
            'approvedBy',
            'items.inventoryItem'
        ])->findOrFail($id);

        // Calculate totals
        $requisition->total_items = $requisition->items->count();
        $requisition->total_quantity_requested = $requisition->items->sum('quantity_requested');
        $requisition->total_quantity_issued = $requisition->items->sum('quantity_issued');
        $requisition->total_quantity_returned = $requisition->items->sum('quantity_returned');
        $requisition->total_quantity_consumed = $requisition->items->sum('quantity_consumed');
        $requisition->total_quantity_sold = $requisition->items->sum('quantity_sold');
        $requisition->requisition_type_label = $this->getRequisitionTypeLabel($requisition->requisition_type);

        // Calculate status percentage
        if ($requisition->total_quantity_requested > 0) {
            $requisition->issued_percentage = round(($requisition->total_quantity_issued / $requisition->total_quantity_requested) * 100, 2);
        } else {
            $requisition->issued_percentage = 0;
        }

        // Get available stock for each item
        foreach ($requisition->items as $item) {
            if ($item->inventoryItem) {
                $item->available_stock = $item->inventoryItem->current_stock ?? 0;
                $item->remaining_to_issue = max(0, $item->quantity_requested - ($item->quantity_issued ?? 0));
            } else {
                $item->available_stock = 0;
                $item->remaining_to_issue = 0;
            }
        }

        // Get approval history
        $requisition->approval_history = $this->getApprovalHistory($requisition);

        return view('management.department-requisitions.show', compact('requisition'));
    }

    /**
     * Show form to approve requisition with quantities.
     */
    public function approveForm($id)
    {
        $requisition = DepartmentRequisition::with([
            'department',
            'requestedBy',
            'items.inventoryItem'
        ])->findOrFail($id);

        if ($requisition->status !== 'pending') {
            return redirect()->route('management.department-requisitions.index')
                ->with('error', 'Only pending requisitions can be approved.');
        }

        return view('management.department-requisitions.approve', compact('requisition'));
    }

    /**
     * Process approval with quantities for each item.
     */
    public function processApproval(Request $request, $id)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:department_requisition_items,id',
            'items.*.quantity_approved' => 'required|numeric|min:0',
            'items.*.pack_type' => 'nullable|string',
            'items.*.pack_size' => 'nullable|numeric|min:1',
            'items.*.metrics' => 'nullable|string',
            'items.*.notes' => 'nullable|string',
            'store_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $requisition = DepartmentRequisition::findOrFail($id);

            if ($requisition->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requisitions can be approved.');
            }

            $hasApprovedItems = false;

            foreach ($validated['items'] as $itemData) {
                $reqItem = DepartmentRequisitionItem::findOrFail($itemData['item_id']);
                $quantityApproved = $itemData['quantity_approved'];

                if ($quantityApproved > 0) {
                    $hasApprovedItems = true;

                    // Update the requisition item with approved quantities
                    $reqItem->quantity_approved = $quantityApproved;
                    $reqItem->approved_pack_type = $itemData['pack_type'] ?? null;
                    $reqItem->approved_pack_size = $itemData['pack_size'] ?? null;
                    $reqItem->approved_metrics = $itemData['metrics'] ?? $reqItem->metrics;
                    $reqItem->approval_notes = $itemData['notes'] ?? null;
                    $reqItem->save();
                }
            }

            if (!$hasApprovedItems) {
                throw new \Exception('At least one item must have an approved quantity greater than zero.');
            }

            // Update requisition status
            $requisition->status = 'approved';
            $requisition->approved_by = Auth::id();
            $requisition->approved_at = now();
            $requisition->store_notes = $validated['store_notes'] ?? null;
            $requisition->save();

            DB::commit();

            Log::info('Requisition approved with quantities', [
                'user_id' => Auth::id(),
                'requisition_id' => $requisition->id,
                'requisition_number' => $requisition->requisition_number,
                'items_approved' => $hasApprovedItems
            ]);

            return redirect()->route('management.department-requisitions.show', $requisition->id)
                ->with('success', "Requisition {$requisition->requisition_number} approved successfully. Store can now issue the items.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve requisition', [
                'user_id' => Auth::id(),
                'requisition_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to approve requisition: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show form to reject requisition with reason.
     */
    public function rejectForm($id)
    {
        $requisition = DepartmentRequisition::with([
            'department',
            'requestedBy',
            'items.inventoryItem'
        ])->findOrFail($id);

        if ($requisition->status !== 'pending') {
            return redirect()->route('management.department-requisitions.index')
                ->with('error', 'Only pending requisitions can be rejected.');
        }

        return view('management.department-requisitions.reject', compact('requisition'));
    }

    /**
     * Process rejection with reason.
     */
    public function processRejection(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10',
            'rejection_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $requisition = DepartmentRequisition::findOrFail($id);

            if ($requisition->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requisitions can be rejected.');
            }

            $requisition->status = 'rejected';
            $requisition->rejection_reason = $validated['rejection_reason'];
            $requisition->store_notes = $validated['rejection_notes'] ?? null;
            $requisition->save();

            DB::commit();

            Log::info('Requisition rejected', [
                'user_id' => Auth::id(),
                'requisition_id' => $requisition->id,
                'requisition_number' => $requisition->requisition_number,
                'reason' => $validated['rejection_reason']
            ]);

            return redirect()->route('management.department-requisitions.index')
                ->with('success', "Requisition {$requisition->requisition_number} rejected.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject requisition', [
                'user_id' => Auth::id(),
                'requisition_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to reject requisition: ' . $e->getMessage());
        }
    }

    /**
     * Update approved quantities for an existing approved requisition.
     */
    public function updateApprovedQuantities(Request $request, $id)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:department_requisition_items,id',
            'items.*.quantity_approved' => 'required|numeric|min:0',
            'items.*.pack_type' => 'nullable|string',
            'items.*.pack_size' => 'nullable|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            $requisition = DepartmentRequisition::findOrFail($id);

            if (!in_array($requisition->status, ['approved', 'partially_issued'])) {
                return redirect()->back()->with('error', 'Only approved or partially issued requisitions can be modified.');
            }

            foreach ($validated['items'] as $itemData) {
                $reqItem = DepartmentRequisitionItem::findOrFail($itemData['item_id']);
                $reqItem->quantity_approved = $itemData['quantity_approved'];
                $reqItem->approved_pack_type = $itemData['pack_type'] ?? null;
                $reqItem->approved_pack_size = $itemData['pack_size'] ?? null;
                $reqItem->save();
            }

            DB::commit();

            return redirect()->route('management.department-requisitions.show', $requisition->id)
                ->with('success', 'Approved quantities updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update quantities: ' . $e->getMessage());
        }
    }

    /**
     * Get requisition summary counts for dashboard.
     */
    public function getSummary()
    {
        return response()->json([
            'success' => true,
            'pending' => DepartmentRequisition::where('status', 'pending')->count(),
            'approved' => DepartmentRequisition::where('status', 'approved')->count(),
            'issued' => DepartmentRequisition::whereIn('status', ['partially_issued', 'issued'])->count(),
            'rejected' => DepartmentRequisition::where('status', 'rejected')->count(),
            'completed' => DepartmentRequisition::where('status', 'completed')->count(),
        ]);
    }

    /**
     * Helper method to get requisition type label.
     */
    private function getRequisitionTypeLabel($type)
    {
        $labels = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly'
        ];
        return $labels[$type] ?? 'Not Specified';
    }

    /**
     * Helper method to get approval history.
     */
    private function getApprovalHistory($requisition)
    {
        $history = [];

        $history[] = [
            'action' => 'Created',
            'user' => $requisition->requestedBy->first_name ?? 'Unknown',
            'date' => $requisition->created_at,
            'icon' => 'fas fa-plus-circle',
            'color' => 'blue'
        ];

        if ($requisition->approved_at && $requisition->approvedBy) {
            $history[] = [
                'action' => 'Approved',
                'user' => $requisition->approvedBy->first_name ?? 'Unknown',
                'date' => $requisition->approved_at,
                'icon' => 'fas fa-check-circle',
                'color' => 'green'
            ];
        }

        if ($requisition->status === 'rejected' && $requisition->rejection_reason) {
            $history[] = [
                'action' => 'Rejected',
                'user' => $requisition->approvedBy->first_name ?? 'Management',
                'date' => $requisition->updated_at,
                'reason' => $requisition->rejection_reason,
                'icon' => 'fas fa-times-circle',
                'color' => 'red'
            ];
        }

        return $history;
    }
}
