<?php
// app/Http/Controllers/Bar/BarStockController.php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use App\Models\DepartmentRequisitionItem;
use App\Models\InventoryItem;
use App\Models\Category;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BarStockController extends Controller
{
    private function getRoleName($user): ?string
    {
        try {
            if ($user->role_id) {
                $role = Role::find($user->role_id);
                return $role->name ?? null;
            }
            return !empty($user->role) ? $user->role : null;
        } catch (\Exception $e) {
            Log::error('Error resolving role name', ['user_id' => $user->id ?? null, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }

            $departmentId = $user->department_id;

            // Get all requisition items for BAR department
            $query = DepartmentRequisitionItem::with(['inventoryItem', 'departmentRequisition'])
                ->whereHas('departmentRequisition', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId)
                      ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
                })
                ->where('issued_total_pieces', '>', 0);

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('inventoryItem', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('item_code', 'like', "%{$search}%");
                });
            }

            // Apply category filter
            if ($request->filled('category_id')) {
                $query->whereHas('inventoryItem', function($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                });
            }

            $requisitionItems = $query->orderBy('created_at', 'desc')->get();

            // Aggregate stock by inventory item
            $stockData = [];
            $totalIssued = 0;
            $totalConsumed = 0;
            $totalSold = 0;
            $totalReturned = 0;
            $totalStockValue = 0;

            foreach ($requisitionItems as $item) {
                $itemId = $item->inventory_item_id;
                $inventoryItem = $item->inventoryItem;

                if (!$inventoryItem) continue;

                $issued = (float) ($item->issued_total_pieces ?? 0);
                $consumed = (float) ($item->quantity_consumed ?? 0);
                $sold = (float) ($item->quantity_sold ?? 0);
                $returned = (float) ($item->returned_total_pieces ?? 0);
                $remaining = $issued - ($consumed + $sold + $returned);

                if (!isset($stockData[$itemId])) {
                    $stockData[$itemId] = [
                        'inventory_item_id' => $itemId,
                        'item_name' => $inventoryItem->name,
                        'item_code' => $inventoryItem->item_code ?? 'N/A',
                        'unit' => $inventoryItem->base_unit ?? 'units',
                        'category_id' => $inventoryItem->category_id,
                        'issued' => 0,
                        'consumed' => 0,
                        'sold' => 0,
                        'returned' => 0,
                        'current_stock' => 0,
                        'pack_type' => $item->issued_pack_type,
                        'pack_size' => $item->issued_pack_size,
                    ];
                }

                $stockData[$itemId]['issued'] += $issued;
                $stockData[$itemId]['consumed'] += $consumed;
                $stockData[$itemId]['sold'] += $sold;
                $stockData[$itemId]['returned'] += $returned;
                $stockData[$itemId]['current_stock'] += $remaining;

                $totalIssued += $issued;
                $totalConsumed += $consumed;
                $totalSold += $sold;
                $totalReturned += $returned;
                $totalStockValue += $remaining;
            }

            // Convert to array and remove items with zero stock if needed
            $stockData = array_values($stockData);

            // Filter out zero stock items if requested
            if ($request->filled('hide_zero') && $request->hide_zero == '1') {
                $stockData = array_filter($stockData, function($item) {
                    return $item['current_stock'] > 0;
                });
            }

            // Get categories for filter
            $categories = Category::whereHas('inventoryItems', function($q) {
                $q->where('is_active', true);
            })->orderBy('name')->get();

            $totalItems = count($stockData);

            Log::info('Bar stock accessed', [
                'user_id' => $user->id,
                'total_items' => $totalItems,
                'total_stock_value' => $totalStockValue
            ]);

            return view('bar.stock.index', compact(
                'stockData',
                'totalItems',
                'totalIssued',
                'totalConsumed',
                'totalSold',
                'totalReturned',
                'totalStockValue',
                'categories'
            ));

        } catch (\Exception $e) {
            Log::error('Bar stock error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('bar.dashboard')->with('error', 'Failed to load stock: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }

            $inventoryItem = InventoryItem::findOrFail($id);

            // Get all requisition items for this inventory item
            $requisitionItems = DepartmentRequisitionItem::with(['departmentRequisition'])
                ->whereHas('departmentRequisition', function($q) use ($user) {
                    $q->where('department_id', $user->department_id)
                      ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
                })
                ->where('inventory_item_id', $id)
                ->where('issued_total_pieces', '>', 0)
                ->get();

            $totalIssued = 0;
            $totalConsumed = 0;
            $totalSold = 0;
            $totalReturned = 0;

            foreach ($requisitionItems as $item) {
                $totalIssued += (float) ($item->issued_total_pieces ?? 0);
                $totalConsumed += (float) ($item->quantity_consumed ?? 0);
                $totalSold += (float) ($item->quantity_sold ?? 0);
                $totalReturned += (float) ($item->returned_total_pieces ?? 0);
            }

            $currentStock = $totalIssued - ($totalConsumed + $totalSold + $totalReturned);

            return view('bar.stock.show', compact(
                'inventoryItem',
                'totalIssued',
                'totalConsumed',
                'totalSold',
                'totalReturned',
                'currentStock',
                'requisitionItems'
            ));

        } catch (\Exception $e) {
            Log::error('Bar stock show error', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('bar.stock.index')->with('error', 'Item not found.');
        }
    }
}
