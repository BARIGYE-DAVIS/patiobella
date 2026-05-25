<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\StockCount;
use App\Models\StockCountItem;
use App\Models\InventoryItem;
use App\Models\Department;
use App\Models\Store;
use App\Models\DepartmentRequisitionItem;
use App\Models\StockVarianceReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
class StockCountController extends Controller
{
    /**
     * Display a listing of stock counts with tab filtering.
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'store');

        $query = StockCount::with(['creator', 'completer', 'items', 'location']);

        if ($type === 'store') {
            $query->where('location_type', StockCount::LOCATION_STORE);
        } else {
            $query->where('location_type', StockCount::LOCATION_DEPARTMENT);

            if ($request->filled('department_id')) {
                $query->where('location_id', $request->department_id);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('count_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('count_date', '<=', $request->date_to);
        }

        $stockCounts = $query->orderBy('created_at', 'desc')->paginate(20);

        $departments = Department::where('is_active', true)
            ->whereIn('name', ['KITCHEN', 'RESTAURANT', 'BAR'])
            ->orderBy('name')
            ->get();

        $statuses = [
            StockCount::STATUS_DRAFT,
            StockCount::STATUS_IN_PROGRESS,
            StockCount::STATUS_COMPLETED,
            StockCount::STATUS_CANCELLED,
        ];

        return view('management.stock-counts.index', compact('stockCounts', 'type', 'departments', 'statuses'));
    }

    /**
     * Show form to create a new stock count (unified with tabs).
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'store');

        // For Store counts
        $storeItems = InventoryItem::where('is_active', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        // For Department counts
        $departments = Department::where('is_active', true)
            ->whereIn('name', ['KITCHEN', 'RESTAURANT', 'BAR'])
            ->orderBy('name')
            ->get();

        $selectedDepartment = null;
        $departmentItems = collect();

        if ($request->filled('department_id')) {
            $selectedDepartment = Department::find($request->department_id);

            if ($selectedDepartment) {
                $departmentItems = $this->getDepartmentItemsCalculation($selectedDepartment->id);
            }
        }

        return view('management.stock-counts.create', compact('type', 'storeItems', 'departments', 'selectedDepartment', 'departmentItems'));
    }

    /**
     * Store a new stock count (handles both store and department).
     */
    public function store(Request $request)
    {
        $type = $request->input('type', 'store');

        if ($type === 'store') {
            $validated = $request->validate([
                'count_date' => 'required|date',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
                'items.*.physical_quantity' => 'required|numeric|min:0',
                'items.*.reason_notes' => 'nullable|string',
            ]);
        } else {
            $validated = $request->validate([
                'department_id' => 'required|exists:departments,id',
                'count_date' => 'required|date',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
                'items.*.physical_quantity' => 'required|numeric|min:0',
                'items.*.expected_quantity' => 'required|numeric|min:0',
                'items.*.unit_cost' => 'nullable|numeric',
                'items.*.reason_notes' => 'nullable|string',
            ]);
        }

        DB::beginTransaction();

        try {
            $countNumber = StockCount::generateCountNumber();

            if ($type === 'store') {
                $stockCount = StockCount::create([
                    'count_number' => $countNumber,
                    'location_type' => StockCount::LOCATION_STORE,
                    'location_id' => 1,
                    'count_date' => $validated['count_date'],
                    'status' => StockCount::STATUS_DRAFT,
                    'created_by' => Auth::id(),
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($validated['items'] as $itemData) {
                    $inventoryItem = InventoryItem::find($itemData['inventory_item_id']);

                    StockCountItem::create([
                        'stock_count_id' => $stockCount->id,
                        'inventory_item_id' => $itemData['inventory_item_id'],
                        'system_quantity' => $inventoryItem->current_stock ?? 0,
                        'physical_quantity' => $itemData['physical_quantity'],
                        'physical_quantity_is_gross' => false,
                        'unit_cost' => $inventoryItem->unit_cost ?? 0,
                        'reason_notes' => $itemData['reason_notes'] ?? null,
                    ]);
                }
            } else {
                $stockCount = StockCount::create([
                    'count_number' => $countNumber,
                    'location_type' => StockCount::LOCATION_DEPARTMENT,
                    'location_id' => $validated['department_id'],
                    'count_date' => $validated['count_date'],
                    'status' => StockCount::STATUS_DRAFT,
                    'created_by' => Auth::id(),
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($validated['items'] as $itemData) {
                    StockCountItem::create([
                        'stock_count_id' => $stockCount->id,
                        'inventory_item_id' => $itemData['inventory_item_id'],
                        'system_quantity' => $itemData['expected_quantity'],
                        'physical_quantity' => $itemData['physical_quantity'],
                        'physical_quantity_is_gross' => false,
                        'unit_cost' => $itemData['unit_cost'] ?? 0,
                        'reason_notes' => $itemData['reason_notes'] ?? null,
                    ]);
                }
            }

            DB::commit();

            Log::info('Stock count created', [
                'user_id' => Auth::id(),
                'count_number' => $countNumber,
                'type' => $type,
                'count_id' => $stockCount->id,
            ]);

            return redirect()->route('management.stock-counts.show', $stockCount->id)
                ->with('success', "Stock count {$countNumber} created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create stock count', [
                'user_id' => Auth::id(),
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create stock count: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified stock count.
     */
    public function show($id)
    {
        $stockCount = StockCount::with(['items.inventoryItem', 'creator', 'completer', 'location'])
            ->findOrFail($id);

        $reasons = StockVarianceReason::active()
            ->orderBy('sort_order')
            ->get();

        $type = $stockCount->location_type;

        return view('management.stock-counts.show', compact('stockCount', 'reasons', 'type'));
    }

    /**
 * Show edit form for stock count
 */
public function editCount($id)
{
    $stockCount = StockCount::with(['items.inventoryItem', 'creator', 'completer', 'location'])
        ->findOrFail($id);

    if ($stockCount->status !== StockCount::STATUS_DRAFT) {
        return redirect()->route('management.stock-counts.show', $stockCount->id)
            ->with('error', 'Only draft counts can be edited.');
    }

    $type = $stockCount->location_type;

    return view('management.stock-counts.edit', compact('stockCount', 'type'));
}
    /**
     * Update physical quantities for items.
     */
   /**
 * Update physical quantities for items.
 */
public function updateItems(Request $request, $id)
{
    $validated = $request->validate([
        'items' => 'required|array',
        'items.*.id' => 'required|exists:stock_count_items,id',
        'items.*.physical_quantity' => 'required|numeric|min:0',
        'items.*.reason_notes' => 'nullable|string',  // ← ADD THIS
        'items.*.reason_details' => 'nullable|string', // ← ADD THIS for details
    ]);

    DB::beginTransaction();

    try {
        $stockCount = StockCount::findOrFail($id);

        if ($stockCount->status !== StockCount::STATUS_DRAFT) {
            return redirect()->back()->with('error', 'Only draft counts can be edited.');
        }

        foreach ($validated['items'] as $itemData) {
            $countItem = StockCountItem::findOrFail($itemData['id']);
            $countItem->physical_quantity = $itemData['physical_quantity'];

            // Save reason notes
            if (isset($itemData['reason_notes'])) {
                $countItem->reason_notes = $itemData['reason_notes'];
            }

            // Save reason details (combine with notes if needed)
            if (isset($itemData['reason_details']) && !empty($itemData['reason_details'])) {
                $existingNotes = $countItem->reason_notes ?? '';
                $countItem->reason_notes = $existingNotes
                    ? $existingNotes . ' - ' . $itemData['reason_details']
                    : $itemData['reason_details'];
            }

            $countItem->save();
        }

        DB::commit();

        return redirect()->route('management.stock-counts.show', $stockCount->id)
            ->with('success', 'Stock count items updated successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Failed to update items: ' . $e->getMessage());
    }
}
    /**
     * Submit stock count for review.
     */
    public function submit($id)
    {
        DB::beginTransaction();

        try {
            $stockCount = StockCount::findOrFail($id);

            if ($stockCount->status !== StockCount::STATUS_DRAFT) {
                return redirect()->back()->with('error', 'Only draft counts can be submitted.');
            }

            $stockCount->status = StockCount::STATUS_IN_PROGRESS;
            $stockCount->save();

            DB::commit();

            return redirect()->route('management.stock-counts.show', $stockCount->id)
                ->with('success', 'Stock count submitted for review.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to submit: ' . $e->getMessage());
        }
    }

    /**
     * Approve variances for a stock count item.
     */
    public function approveVariance(Request $request, $id, $itemId)
    {
        $validated = $request->validate([
            'reason_code' => 'required|string|exists:stock_variance_reasons,code',
            'reason_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $countItem = StockCountItem::with(['stockCount', 'inventoryItem'])
                ->findOrFail($itemId);

            $stockCount = $countItem->stockCount;

            if ($stockCount->status !== StockCount::STATUS_IN_PROGRESS) {
                return redirect()->back()->with('error', 'Count must be in progress to approve variances.');
            }

            $countItem->reason_code = $validated['reason_code'];
            $countItem->reason_notes = $validated['reason_notes'] ?? null;
            $countItem->approved_by = Auth::id();
            $countItem->approved_at = now();
            $countItem->save();

            DB::commit();

            return redirect()->route('management.stock-counts.show', $stockCount->id)
                ->with('success', 'Variance approved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve variance: ' . $e->getMessage());
        }
    }

    /**
     * Complete the stock count.
     */
    public function complete($id)
    {
        DB::beginTransaction();

        try {
            $stockCount = StockCount::with('items')->findOrFail($id);

            if ($stockCount->status !== StockCount::STATUS_IN_PROGRESS) {
                return redirect()->back()->with('error', 'Only in-progress counts can be completed.');
            }

            $unapprovedVariances = $stockCount->items
                ->filter(function ($item) {
                    return $item->hasVariance() && !$item->isApproved();
                });

            if ($unapprovedVariances->count() > 0) {
                return redirect()->back()->with('error', 'All variances must be approved before completing.');
            }

            $stockCount->status = StockCount::STATUS_COMPLETED;
            $stockCount->completed_by = Auth::id();
            $stockCount->completed_at = now();
            $stockCount->save();

            DB::commit();

            return redirect()->route('management.stock-counts.show', $stockCount->id)
                ->with('success', 'Stock count completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to complete: ' . $e->getMessage());
        }
    }

    /**
     * Cancel the stock count.
     */
    public function cancel($id)
    {
        DB::beginTransaction();

        try {
            $stockCount = StockCount::findOrFail($id);

            if (in_array($stockCount->status, [StockCount::STATUS_COMPLETED, StockCount::STATUS_CANCELLED])) {
                return redirect()->back()->with('error', 'Completed or cancelled counts cannot be cancelled.');
            }

            $stockCount->status = StockCount::STATUS_CANCELLED;
            $stockCount->save();

            DB::commit();

            return redirect()->route('management.stock-counts.index')
                ->with('success', 'Stock count cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to cancel: ' . $e->getMessage());
        }
    }

    /**
     * Get store item current stock with empty bottle weight for AJAX.
     */
    public function getItemStock($id)
    {
        $item = InventoryItem::findOrFail($id);

        return response()->json([
            'success' => true,
            'current_stock' => $item->current_stock,
            'unit_cost' => $item->unit_cost,
            'base_unit' => $item->base_unit,
            'empty_bottle_weight' => $item->empty_bottle_weight ?? 0,
            'has_empty_bottle_weight' => ($item->empty_bottle_weight ?? 0) > 0,
        ]);
    }

    /**
     * Get department items with expected quantities and empty bottle weight for AJAX.
     */
public function getDepartmentItems($departmentId)
{
    try {
        $department = Department::findOrFail($departmentId);
        $items = $this->getDepartmentItemsCalculation($departmentId);

        return response()->json([
            'success'         => true,
            'items'           => $items,
            'department_name' => $department->name,
            'count'           => $items->count(),
        ]);

    } catch (\Exception $e) {
        Log::error('getDepartmentItems failed', [
            'department_id' => $departmentId,
            'error'         => $e->getMessage(),
            'trace'         => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to load department items: ' . $e->getMessage(),
        ], 500);
    }
}

    /**
     * Helper method to calculate department items expected quantities.
     * FIXED: Removed the status filter that was causing no items to load.
     */
private function getDepartmentItemsCalculation($departmentId)
{
    $rows = DB::table('department_requisition_items as dri')
        ->join('department_requisitions as dr', 'dr.id', '=', 'dri.department_requisition_id')
        ->join('inventory_items as ii', 'ii.id', '=', 'dri.inventory_item_id')
        ->where('dr.department_id', $departmentId)
        ->whereNull('dr.deleted_at')                          // respect soft deletes manually
        ->whereNotIn('dr.status', ['cancelled', 'rejected'])
        ->where('ii.is_active', true)
        ->select([
            'dri.inventory_item_id',
            'ii.name as item_name',
            'ii.item_code',
            'ii.base_unit',
            'ii.unit_cost',
            'ii.empty_bottle_weight',
            DB::raw('SUM(COALESCE(dri.issued_total_pieces, 0))   as total_issued'),
            DB::raw('SUM(COALESCE(dri.quantity_consumed, 0))     as total_consumed'),
            DB::raw('SUM(COALESCE(dri.returned_total_pieces, 0)) as total_returned'),
            DB::raw('SUM(COALESCE(dri.quantity_sold, 0))         as total_sold'),
        ])
        ->groupBy(
            'dri.inventory_item_id',
            'ii.name',
            'ii.item_code',
            'ii.base_unit',
            'ii.unit_cost',
            'ii.empty_bottle_weight'
        )
        ->get();

    if ($rows->isEmpty()) {
        return collect([]);
    }

    return $rows->map(function ($row) {
        $expected = max(0, $row->total_issued - $row->total_consumed - $row->total_returned - $row->total_sold);
        $emptyBottleWeight = (float) ($row->empty_bottle_weight ?? 0);

        return [
            'inventory_item_id'      => $row->inventory_item_id,
            'item_name'              => $row->item_name ?? 'N/A',
            'item_code'              => $row->item_code  ?? 'N/A',
            'expected_quantity'      => $expected,
            'total_issued'           => (float) $row->total_issued,
            'total_consumed'         => (float) $row->total_consumed,
            'total_returned'         => (float) $row->total_returned,
            'total_sold'             => (float) $row->total_sold,
            'unit_cost'              => (float) ($row->unit_cost ?? 0),
            'base_unit'              => $row->base_unit ?? 'units',
            'empty_bottle_weight'    => $emptyBottleWeight,
            'has_empty_bottle_weight'=> $emptyBottleWeight > 0,
        ];
    })->values();
}

    /**
     * Calculate net quantity from gross weight.
     */
    public function calculateNetQuantity(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'gross_weight' => 'required|numeric|min:0',
        ]);

        $item = InventoryItem::find($validated['inventory_item_id']);
        $emptyBottleWeight = $item->empty_bottle_weight ?? 0;
        $netQuantity = max(0, $validated['gross_weight'] - $emptyBottleWeight);

        return response()->json([
            'success' => true,
            'gross_weight' => $validated['gross_weight'],
            'empty_bottle_weight' => $emptyBottleWeight,
            'net_quantity' => $netQuantity,
            'base_unit' => $item->base_unit ?? 'units',
        ]);
    }

    /**
 * Show review page for stock count (management approval).
 */
public function review($id)
{
    $stockCount = StockCount::with(['items.inventoryItem', 'creator', 'completer', 'location'])
        ->findOrFail($id);

    if ($stockCount->status !== StockCount::STATUS_IN_PROGRESS) {
        return redirect()->route('management.stock-counts.show', $stockCount->id)
            ->with('error', 'Only in-progress counts can be reviewed.');
    }

    $reasons = StockVarianceReason::active()
        ->orderBy('sort_order')
        ->get();

    $type = $stockCount->location_type;

    return view('management.stock-counts.review', compact('stockCount', 'reasons', 'type'));
}

/**
 * Process review and approve variances.
 */
public function reviewApprove(Request $request, $id)
{
    $validated = $request->validate([
        'items' => 'required|array',
        'items.*.id' => 'required|exists:stock_count_items,id',
        'items.*.reason_code' => 'nullable|string|exists:stock_variance_reasons,code',
        'items.*.reason_notes' => 'nullable|string',
        'items.*.approved' => 'nullable|boolean',
        'review_notes' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $stockCount = StockCount::findOrFail($id);

        if ($stockCount->status !== StockCount::STATUS_IN_PROGRESS) {
            return redirect()->back()->with('error', 'Only in-progress counts can be reviewed.');
        }

        foreach ($validated['items'] as $itemData) {
            $countItem = StockCountItem::findOrFail($itemData['id']);
            $isApproved = isset($itemData['approved']) && $itemData['approved'] == 1;
            $hasVariance = $countItem->variance != 0;

            if ($hasVariance) {
                if ($isApproved && empty($itemData['reason_code'])) {
                    throw new \Exception('Reason code is required for approved variance on item: ' . ($countItem->inventoryItem->name ?? 'Unknown'));
                }

                if ($isApproved) {
                    $countItem->reason_code = $itemData['reason_code'];
                    $countItem->reason_notes = $itemData['reason_notes'] ?? null;
                    $countItem->approved_by = Auth::id();
                    $countItem->approved_at = now();
                    $countItem->save();
                }
            } else {
                // Auto-approve items without variance
                if (!$countItem->isApproved()) {
                    $countItem->approved_by = Auth::id();
                    $countItem->approved_at = now();
                    $countItem->save();
                }
            }
        }

        // Check if all variances have been approved
        $unapprovedVariances = $stockCount->items
            ->filter(function ($item) {
                return $item->variance != 0 && !$item->isApproved();
            });

        if ($unapprovedVariances->count() > 0) {
            DB::commit();
            return redirect()->route('management.stock-counts.show', $stockCount->id)
                ->with('warning', 'Review saved. ' . $unapprovedVariances->count() . ' item(s) still pending approval.');
        }

        // All variances approved, complete the count
        $stockCount->status = StockCount::STATUS_COMPLETED;
        $stockCount->completed_by = Auth::id();
        $stockCount->completed_at = now();

        if ($request->filled('review_notes')) {
            $existingNotes = $stockCount->notes;
            $stockCount->notes = $existingNotes
                ? $existingNotes . "\n\nReview Notes: " . $request->review_notes
                : "Review Notes: " . $request->review_notes;
        }

        $stockCount->save();

        DB::commit();

        return redirect()->route('management.stock-counts.show', $stockCount->id)
            ->with('success', 'Stock count completed successfully. All variances have been approved.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Failed to process review: ' . $e->getMessage());
    }
}

/**
 * Show approve count form.
 */
public function approveCountForm($id)
{
    $stockCount = StockCount::with(['items.inventoryItem', 'creator', 'completer', 'location'])
        ->findOrFail($id);

    if ($stockCount->status !== StockCount::STATUS_IN_PROGRESS) {
        return redirect()->route('management.stock-counts.show', $stockCount->id)
            ->with('error', 'Only in-progress counts can be approved.');
    }

    $type = $stockCount->location_type;

    return view('management.stock-counts.approve-count', compact('stockCount', 'type'));
}

/**
 * Submit approval for stock count.
 */
public function approveCountSubmit(Request $request, $id)
{
    $validated = $request->validate([
        'approval_notes' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $stockCount = StockCount::findOrFail($id);

        if ($stockCount->status !== StockCount::STATUS_IN_PROGRESS) {
            return redirect()->back()->with('error', 'Only in-progress counts can be approved.');
        }

        $stockCount->status = StockCount::STATUS_COMPLETED;
        $stockCount->completed_by = Auth::id();
        $stockCount->completed_at = now();

        if ($request->filled('approval_notes')) {
            $existingNotes = $stockCount->notes;
            $stockCount->notes = $existingNotes
                ? $existingNotes . "\n\nApproval Notes: " . $request->approval_notes
                : "Approval Notes: " . $request->approval_notes;
        }

        $stockCount->save();

        DB::commit();

        return redirect()->route('management.stock-counts.show', $stockCount->id)
            ->with('success', 'Stock count approved and completed successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Failed to approve stock count: ' . $e->getMessage());
    }
}



/**
 * Download stock count as PDF.
 */
public function downloadPdf($id)
{
    $stockCount = StockCount::with(['items.inventoryItem', 'creator', 'completer', 'location'])
        ->findOrFail($id);

    if ($stockCount->status !== StockCount::STATUS_COMPLETED) {
        return redirect()->back()->with('error', 'Only completed stock counts can be downloaded as PDF.');
    }

    $type = $stockCount->location_type;
    $totalVariance = $stockCount->getTotalVarianceAttribute();

    $pdf = Pdf::loadView('management.stock-counts.pdf', compact('stockCount', 'type', 'totalVariance'));

    return $pdf->download('stock-count-' . $stockCount->count_number . '.pdf');
}

}
