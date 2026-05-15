<?php
// app/Http/Controllers/Restaurant/RestaurantStockController.php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DepartmentRequisition;
use App\Models\DepartmentRequisitionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantStockController extends Controller
{
    /**
     * Display current stock for the restaurant department.
     *
     * Current stock per item (in base units) =
     *   SUM(issued_total_pieces) - SUM(returned_total_pieces) - SUM(quantity_consumed) - SUM(quantity_sold)
     *
     * All four columns are already stored in base units in department_requisition_items,
     * so we aggregate directly — no pack arithmetic needed here.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // Use the department's actual ID from the loaded relationship — never trust
        // $user->department_id alone since it can be null if the FK isn't eager-loaded.
        $departmentId = $user->department->id;

        // ── Base query: all issued items for this department ──────────────────
        $query = DepartmentRequisitionItem::with([
                'inventoryItem.category',
                'departmentRequisition',
            ])
            ->whereHas('departmentRequisition', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId)
                  ->whereIn('status', [
                      'issued',
                      'partially_issued',
                      'partially_consumed',
                      'partially_returned',
                      'fully_consumed',
                      'completed',
                      'returned',
                  ]);
            })
            ->where('issued_total_pieces', '>', 0);

        // ── Search filter ─────────────────────────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('inventoryItem', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        // ── Category filter ───────────────────────────────────────────────────
        if ($request->filled('category_id')) {
            $query->whereHas('inventoryItem', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $rows = $query->get();

        // ── Aggregate per inventory item ──────────────────────────────────────
        // Each inventory item can appear across multiple requisitions.
        // We sum all base-unit columns and derive current stock once.
        $stockMap = [];

        foreach ($rows as $row) {
            $id = $row->inventory_item_id;

            if (!isset($stockMap[$id])) {
                $stockMap[$id] = [
                    'item_id'              => $id,
                    'item_name'            => $row->inventoryItem->name ?? 'N/A',
                    'item_code'            => $row->inventoryItem->item_code ?? '',
                    'category'             => $row->inventoryItem->category->name ?? 'N/A',
                    // Prefer metrics from the item row; fall back to inventory base_unit
                    'unit'                 => $row->metrics ?? ($row->inventoryItem->base_unit ?? 'units'),
                    // Aggregated base-unit totals (keys match view expectations)
                    'total_issued_pieces'  => 0.0,   // issued_total_pieces (base units)
                    'total_returned'       => 0.0,   // returned_total_pieces (base units)
                    'total_consumed'       => 0.0,   // quantity_consumed (base units)
                    'total_sold'           => 0.0,   // quantity_sold (base units)
                    'current_stock'        => 0.0,
                    'requisition_count'    => 0,
                ];
            }

            $stockMap[$id]['total_issued_pieces'] += (float) ($row->issued_total_pieces   ?? 0);
            $stockMap[$id]['total_returned']       += (float) ($row->returned_total_pieces ?? 0);
            $stockMap[$id]['total_consumed']       += (float) ($row->quantity_consumed     ?? 0);
            $stockMap[$id]['total_sold']           += (float) ($row->quantity_sold         ?? 0);
            $stockMap[$id]['requisition_count']  += 1;
        }

        // ── Derive current stock & filter out zeroes/negatives ────────────────
        foreach ($stockMap as &$data) {
            $data['current_stock'] = $data['total_issued_pieces']
                - $data['total_returned']
                - $data['total_consumed']
                - $data['total_sold'];
        }
        unset($data);

        // Only show items still in hand
        $stockData = array_filter($stockMap, fn($d) => $d['current_stock'] > 0);

        // Sort alphabetically by item name
        usort($stockData, fn($a, $b) => strcmp($a['item_name'], $b['item_name']));

        // ── Summary statistics ────────────────────────────────────────────────
        $totalItems      = count($stockData);
        $totalIssued     = array_sum(array_column($stockData, 'total_issued_pieces'));
        $totalConsumed   = array_sum(array_column($stockData, 'total_consumed'));
        $totalReturned   = array_sum(array_column($stockData, 'total_returned'));
        $totalSold       = array_sum(array_column($stockData, 'total_sold'));
        $totalStockValue = array_sum(array_column($stockData, 'current_stock'));

        // ── Categories for filter dropdown ────────────────────────────────────
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('restaurant.stock.index', compact(
            'stockData',
            'categories',
            'totalItems',
            'totalStockValue',
            'totalIssued',
            'totalConsumed',
            'totalReturned',
            'totalSold'
        ));
    }

    /**
     * Stock summary for dashboard widget (AJAX).
     * Low-stock threshold: current stock < 10 base units.
     */
    public function getSummary()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $departmentId = $user->department->id;

        $rows = DepartmentRequisitionItem::whereHas('departmentRequisition', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId)
                  ->whereIn('status', [
                      'issued',
                      'partially_issued',
                      'partially_consumed',
                      'partially_returned',
                      'fully_consumed',
                      'completed',
                      'returned',
                  ]);
            })
            ->where('issued_total_pieces', '>', 0)
            ->get(['inventory_item_id', 'issued_total_pieces', 'returned_total_pieces', 'quantity_consumed', 'quantity_sold']);

        // Aggregate per item
        $totals = [];
        foreach ($rows as $row) {
            $id = $row->inventory_item_id;
            $totals[$id] = ($totals[$id] ?? 0)
                + (float) $row->issued_total_pieces
                - (float) $row->returned_total_pieces
                - (float) $row->quantity_consumed
                - (float) $row->quantity_sold;
        }

        $totalItems    = 0;
        $lowStockItems = 0;

        foreach ($totals as $stock) {
            if ($stock > 0) {
                $totalItems++;
                if ($stock < 10) {
                    $lowStockItems++;
                }
            }
        }

        return response()->json([
            'success'         => true,
            'total_items'     => $totalItems,
            'low_stock_items' => $lowStockItems,
        ]);
    }
}
