<?php
// app/Http/Controllers/Management/PerformanceController.php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DepartmentRequisitionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceController extends Controller
{

/**
 * Display list of stock take records.
 */
public function index(Request $request)
{
    $departments = Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE', 'RESTAURANT'])->get();

    // For now, show empty sales collection since we're using stock take
    $sales = collect(); // Empty collection

    return view('management.performance.index', compact('departments', 'sales'));
}
    public function create(Request $request)
    {
        $departments = Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE', 'RESTAURANT'])->get();
        return view('management.performance.create', compact('departments'));
    }

    /**
     * Get department stock data.
     * Opening = issued_total_pieces - (quantity_sold + quantity_consumed + quantity_returned)
     * Added = quantity_issued on the selected date
     */
    public function getDepartmentStockData($departmentId, Request $request)
    {
        try {
            $date = $request->get('date', date('Y-m-d'));

            $department = Department::find($departmentId);
            if (!$department) {
                return response()->json(['success' => false, 'message' => 'Department not found'], 404);
            }

            // Get all distinct items that have been issued to this department
            $items = DepartmentRequisitionItem::query()
                ->join('department_requisitions as dr', 'dr.id', '=', 'department_requisition_items.department_requisition_id')
                ->join('inventory_items as ii', 'ii.id', '=', 'department_requisition_items.inventory_item_id')
                ->where('dr.department_id', $departmentId)
                ->where('dr.status', '!=', 'cancelled')
                ->select(
                    'department_requisition_items.inventory_item_id',
                    'ii.name as item_name',
                    'ii.item_code',
                    'ii.unit_of_measurement',
                    'ii.last_purchase_price as unit_cost'
                )
                ->groupBy(
                    'department_requisition_items.inventory_item_id',
                    'ii.name',
                    'ii.item_code',
                    'ii.unit_of_measurement',
                    'ii.last_purchase_price'
                )
                ->get();

            foreach ($items as $item) {
                // Calculate OPENING STOCK = issued_total_pieces - (sold + consumed + returned)
                $opening = DepartmentRequisitionItem::query()
                    ->join('department_requisitions as dr', 'dr.id', '=', 'department_requisition_items.department_requisition_id')
                    ->where('dr.department_id', $departmentId)
                    ->where('dr.status', '!=', 'cancelled')
                    ->where('department_requisition_items.inventory_item_id', $item->inventory_item_id)
                    ->select(DB::raw('
                        COALESCE(SUM(issued_total_pieces), 0) -
                        (COALESCE(SUM(quantity_sold), 0) +
                         COALESCE(SUM(quantity_consumed), 0) +
                         COALESCE(SUM(quantity_returned), 0)) as opening
                    '))
                    ->value('opening');

                $item->opening_stock = (int)max(0, $opening);

                // Calculate ADDED TODAY = quantity_issued on this date
                $addedToday = DepartmentRequisitionItem::query()
                    ->join('department_requisitions as dr', 'dr.id', '=', 'department_requisition_items.department_requisition_id')
                    ->where('dr.department_id', $departmentId)
                    ->where('dr.status', '!=', 'cancelled')
                    ->where('department_requisition_items.inventory_item_id', $item->inventory_item_id)
                    ->whereDate('department_requisition_items.created_at', $date)
                    ->sum('department_requisition_items.quantity_issued');

                $item->added_today = (int)$addedToday;
                $item->unit_cost = (int)($item->unit_cost ?? 0);
            }

            return response()->json([
                'success' => true,
                'items' => $items,
                'department_name' => $department->name,
            ]);

        } catch (\Exception $e) {
            Log::error('getDepartmentStockData error', [
                'department_id' => $departmentId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load department data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store stock take data.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'stock_date' => 'required|date',
            'items' => 'required|array',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.used_quantity' => 'nullable|numeric|min:0',
            'items.*.closing_quantity' => 'nullable|numeric|min:0',
            'items.*.cogs' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $totalCogs = 0;

            foreach ($validated['items'] as $itemData) {
                $usedQuantity = (int)($itemData['used_quantity'] ?? 0);
                $cogs = (int)($itemData['cogs'] ?? 0);

                if ($usedQuantity == 0 && $cogs == 0) {
                    continue;
                }

                $totalCogs += $cogs;

                Log::info('Stock take item saved', [
                    'department_id' => $validated['department_id'],
                    'inventory_item_id' => $itemData['inventory_item_id'],
                    'used_quantity' => $usedQuantity,
                    'cogs' => $cogs,
                ]);
            }

            DB::commit();

            return redirect()->route('management.performance.index')
                ->with('success', 'Stock take recorded successfully. Total COGS: ' . number_format($totalCogs, 0));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save stock take', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to save stock take: ' . $e->getMessage())
                ->withInput();
        }
    }
}
