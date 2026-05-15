<?php
// app/Http/Controllers/Restaurant/RestaurantStockController.php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DepartmentRequisitionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantStockController extends Controller
{
    /**
     * Display current stock for the restaurant department.
     * Shows only requisitions that still have remaining stock.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $departmentId = $user->department_id;

        // Get all requisition items for this department
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
                  ]);
            })
            ->where('issued_total_pieces', '>', 0);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('inventoryItem', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->whereHas('inventoryItem', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $rows = $query->get();

        // Calculate remaining stock PER REQUISITION ITEM
        // Only include items where remaining > 0
        $stockData = [];

        foreach ($rows as $row) {
            $issued = (float) ($row->issued_total_pieces ?? 0);
            $consumed = (float) ($row->quantity_consumed ?? 0);
            $returned = (float) ($row->returned_total_pieces ?? 0);
            $sold = (float) ($row->quantity_sold ?? 0);

            $remaining = $issued - ($consumed + $returned + $sold);

            // ONLY include if remaining stock > 0
            if ($remaining > 0) {
                $stockData[] = [
                    'requisition_number'   => $row->departmentRequisition->requisition_number ?? 'N/A',
                    'item_id'              => $row->inventory_item_id,
                    'item_name'            => $row->inventoryItem->name ?? 'N/A',
                    'item_code'            => $row->inventoryItem->item_code ?? '',
                    'category'             => $row->inventoryItem->category->name ?? 'N/A',
                    'unit'                 => $row->metrics ?? ($row->inventoryItem->base_unit ?? 'units'),
                    'issued'               => $issued,
                    'consumed'             => $consumed,
                    'returned'             => $returned,
                    'sold'                 => $sold,
                    'current_stock'        => $remaining,
                    'pack_type'            => $row->issued_pack_type,
                    'pack_size'            => $row->issued_pack_size,
                ];
            }
        }

        // Sort by item name
        usort($stockData, fn($a, $b) => strcmp($a['item_name'], $b['item_name']));

        // Summary statistics (only from active stock)
        $totalItems      = count($stockData);
        $totalIssued     = array_sum(array_column($stockData, 'issued'));
        $totalConsumed   = array_sum(array_column($stockData, 'consumed'));
        $totalReturned   = array_sum(array_column($stockData, 'returned'));
        $totalSold       = array_sum(array_column($stockData, 'sold'));
        $totalStockValue = array_sum(array_column($stockData, 'current_stock'));

        // Categories for filter
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
     */
    public function getSummary()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $departmentId = $user->department_id;

        $rows = DepartmentRequisitionItem::with(['departmentRequisition'])
            ->whereHas('departmentRequisition', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId)
                  ->whereIn('status', [
                      'issued',
                      'partially_issued',
                      'partially_consumed',
                      'partially_returned',
                  ]);
            })
            ->where('issued_total_pieces', '>', 0)
            ->get();

        $totalItems = 0;
        $lowStockItems = 0;

        foreach ($rows as $row) {
            $issued = (float) ($row->issued_total_pieces ?? 0);
            $consumed = (float) ($row->quantity_consumed ?? 0);
            $returned = (float) ($row->returned_total_pieces ?? 0);
            $sold = (float) ($row->quantity_sold ?? 0);
            $remaining = $issued - ($consumed + $returned + $sold);

            if ($remaining > 0) {
                $totalItems++;
                if ($remaining < 10) {
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
