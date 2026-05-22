<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\StockCount;
use App\Models\StockCountItem;
use App\Models\InventoryItem;
use App\Models\Department;
use App\Models\DepartmentRequisitionItem;
use App\Models\StockVarianceReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentStockCountController extends Controller
{
    /**
     * Display a listing of stock counts for departments.
     */
    public function index(Request $request)
    {
        $query = StockCount::with(['creator', 'completer', 'items', 'location'])
            ->where('location_type', StockCount::LOCATION_DEPARTMENT);

        if ($request->filled('department_id')) {
            $query->where('location_id', $request->department_id);
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

        return view('management.department-stock-counts.index', compact('stockCounts', 'departments', 'statuses'));
    }

    /**
     * Show form to create a new department stock count.
     */
    public function create(Request $request)
    {
        $departments = Department::where('is_active', true)
            ->whereIn('name', ['KITCHEN', 'RESTAURANT', 'BAR'])
            ->orderBy('name')
            ->get();

        $selectedDepartment = null;
        $items = collect();

        if ($request->filled('department_id')) {
            $selectedDepartment = Department::find($request->department_id);

            if ($selectedDepartment) {
                // Get all requisition items issued to this department with their calculations
                $items = DepartmentRequisitionItem::with(['inventoryItem', 'departmentRequisition'])
                    ->whereHas('departmentRequisition', function($query) use ($selectedDepartment) {
                        $query->where('department_id', $selectedDepartment->id)
                            ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned', 'completed']);
                    })
                    ->get()
                    ->groupBy('inventory_item_id')
                    ->map(function($group) {
                        $firstItem = $group->first();
                        $totalIssued = $group->sum('issued_total_pieces');
                        $totalConsumed = $group->sum('quantity_consumed');
                        $totalReturned = $group->sum('returned_total_pieces');
                        $totalSold = $group->sum('quantity_sold');

                        // Expected = Issued - Consumed - Returned - Sold
                        $expected = $totalIssued - $totalConsumed - $totalReturned - $totalSold;

                        return (object) [
                            'inventory_item_id' => $firstItem->inventory_item_id,
                            'inventoryItem' => $firstItem->inventoryItem,
                            'expected_quantity' => max(0, $expected),
                            'total_issued' => $totalIssued,
                            'total_consumed' => $totalConsumed,
                            'total_returned' => $totalReturned,
                            'total_sold' => $totalSold,
                            'unit_cost' => $firstItem->inventoryItem->unit_cost ?? 0,
                            'base_unit' => $firstItem->inventoryItem->base_unit ?? 'units',
                            'metrics' => $firstItem->metrics ?? ($firstItem->inventoryItem->base_unit ?? 'units'),
                        ];
                    })
                    ->values();
            }
        }

        return view('management.department-stock-counts.create', compact('departments', 'selectedDepartment', 'items'));
    }

    /**
     * Store a new department stock count.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'count_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.physical_quantity' => 'required|numeric|min:0',
            'items.*.expected_quantity' => 'required|numeric|min:0',
            'items.*.unit_cost' => 'nullable|numeric',
        ]);

        DB::beginTransaction();

        try {
            $countNumber = StockCount::generateCountNumber();

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
                    'unit_cost' => $itemData['unit_cost'] ?? 0,
                ]);
            }

            DB::commit();

            Log::info('Department stock count created', [
                'user_id' => Auth::id(),
                'count_number' => $countNumber,
                'department_id' => $validated['department_id'],
                'count_id' => $stockCount->id,
            ]);

            return redirect()->route('management.department-stock-counts.show', $stockCount->id)
                ->with('success', "Stock count {$countNumber} created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create department stock count', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create stock count: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified department stock count.
     */
    public function show($id)
    {
        $stockCount = StockCount::with(['items.inventoryItem', 'creator', 'completer', 'location'])
            ->where('location_type', StockCount::LOCATION_DEPARTMENT)
            ->findOrFail($id);

        $reasons = StockVarianceReason::active()
            ->orderBy('sort_order')
            ->get();

        return view('management.department-stock-counts.show', compact('stockCount', 'reasons'));
    }

    /**
     * Update physical quantities for items.
     */
    public function updateItems(Request $request, $id)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:stock_count_items,id',
            'items.*.physical_quantity' => 'required|numeric|min:0',
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
                $countItem->save();
            }

            DB::commit();

            return redirect()->route('management.department-stock-counts.show', $stockCount->id)
                ->with('success', 'Stock count items updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update items: ' . $e->getMessage());
        }
    }

    /**
     * Submit stock count for review (change status to in_progress).
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

            return redirect()->route('management.department-stock-counts.show', $stockCount->id)
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

            return redirect()->route('management.department-stock-counts.show', $stockCount->id)
                ->with('success', 'Variance approved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve variance: ' . $e->getMessage());
        }
    }

    /**
     * Complete the stock count (all variances approved).
     */
    public function complete($id)
    {
        DB::beginTransaction();

        try {
            $stockCount = StockCount::with('items')->findOrFail($id);

            if ($stockCount->status !== StockCount::STATUS_IN_PROGRESS) {
                return redirect()->back()->with('error', 'Only in-progress counts can be completed.');
            }

            // Check if all items with variances are approved
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

            return redirect()->route('management.department-stock-counts.show', $stockCount->id)
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

            return redirect()->route('management.department-stock-counts.index')
                ->with('success', 'Stock count cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to cancel: ' . $e->getMessage());
        }
    }

    /**
     * Get department items with expected quantities for AJAX.
     */
    public function getDepartmentItems($departmentId)
    {
        try {
            $department = Department::findOrFail($departmentId);

            $items = DepartmentRequisitionItem::with(['inventoryItem', 'departmentRequisition'])
                ->whereHas('departmentRequisition', function($query) use ($departmentId) {
                    $query->where('department_id', $departmentId)
                        ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned', 'completed']);
                })
                ->get()
                ->groupBy('inventory_item_id')
                ->map(function($group) {
                    $firstItem = $group->first();
                    $totalIssued = $group->sum('issued_total_pieces');
                    $totalConsumed = $group->sum('quantity_consumed');
                    $totalReturned = $group->sum('returned_total_pieces');
                    $totalSold = $group->sum('quantity_sold');

                    $expected = max(0, $totalIssued - $totalConsumed - $totalReturned - $totalSold);

                    return [
                        'inventory_item_id' => $firstItem->inventory_item_id,
                        'item_name' => $firstItem->inventoryItem->name ?? 'N/A',
                        'item_code' => $firstItem->inventoryItem->item_code ?? 'N/A',
                        'expected_quantity' => $expected,
                        'total_issued' => $totalIssued,
                        'total_consumed' => $totalConsumed,
                        'total_returned' => $totalReturned,
                        'total_sold' => $totalSold,
                        'unit_cost' => $firstItem->inventoryItem->unit_cost ?? 0,
                        'base_unit' => $firstItem->inventoryItem->base_unit ?? 'units',
                        'metrics' => $firstItem->metrics ?? ($firstItem->inventoryItem->base_unit ?? 'units'),
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'items' => $items,
                'department_name' => $department->name,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load department items: ' . $e->getMessage(),
            ], 500);
        }
    }
}
