<?php
// app/Http/Controllers/Bar/BarProductController.php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use App\Models\DepartmentRequisitionItem;
use App\Models\InventoryItem;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BarProductController extends Controller
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

    /**
     * Display list of products (sellable inventory items)
     * that have been issued to BAR department with available stock
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }

            $departmentId = $user->department_id;

            // Get all requisition items for BAR department that have been issued
            $requisitionItems = DepartmentRequisitionItem::with(['inventoryItem', 'departmentRequisition'])
                ->whereHas('departmentRequisition', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId)
                      ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
                })
                ->where('issued_total_pieces', '>', 0)
                ->get();

            // Aggregate products by inventory item
            $products = [];

            foreach ($requisitionItems as $item) {
                $inventoryItem = $item->inventoryItem;

                if (!$inventoryItem) continue;

                // Skip if not sellable
                if (!$inventoryItem->is_sellable) continue;

                $itemId = $inventoryItem->id;

                $issued = (float) ($item->issued_total_pieces ?? 0);
                $consumed = (float) ($item->quantity_consumed ?? 0);
                $sold = (float) ($item->quantity_sold ?? 0);
                $returned = (float) ($item->returned_total_pieces ?? 0);
                $available = $issued - ($consumed + $sold + $returned);

                if ($available <= 0) continue;

                if (!isset($products[$itemId])) {
                    $products[$itemId] = [
                        'id' => $inventoryItem->id,
                        'name' => $inventoryItem->name,
                        'item_code' => $inventoryItem->item_code ?? 'N/A',
                        'barcode' => $inventoryItem->barcode ?? null,
                        'selling_price' => $inventoryItem->selling_price ?? 0,
                        'base_unit' => $inventoryItem->base_unit ?? 'units',
                        'available_stock' => 0,
                        'requisitions' => []
                    ];
                }

                $products[$itemId]['available_stock'] += $available;
                $products[$itemId]['requisitions'][] = [
                    'requisition_number' => $item->departmentRequisition->requisition_number ?? 'N/A',
                    'issued' => $issued,
                    'consumed' => $consumed,
                    'sold' => $sold,
                    'returned' => $returned,
                    'available' => $available
                ];
            }

            // Convert to array and sort by name
            $products = collect($products)->sortBy('name')->values()->all();

            // Apply search filter
            if ($request->filled('search')) {
                $search = strtolower($request->search);
                $products = array_filter($products, function($product) use ($search) {
                    return strpos(strtolower($product['name']), $search) !== false ||
                           strpos(strtolower($product['item_code']), $search) !== false;
                });
            }

            // Calculate totals
            $totalProducts = count($products);
            $totalStockValue = array_sum(array_column($products, 'available_stock'));

            Log::info('Bar products accessed', [
                'user_id' => $user->id,
                'total_products' => $totalProducts,
                'total_stock_value' => $totalStockValue
            ]);

            return view('bar.cashier.products', compact('products', 'totalProducts', 'totalStockValue'));

        } catch (\Exception $e) {
            Log::error('Bar products error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('bar.cashier.dashboard')->with('error', 'Failed to load products: ' . $e->getMessage());
        }
    }

    /**
     * Show single product details with requisition history
     */
    public function show($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }

            $departmentId = $user->department_id;

            $inventoryItem = InventoryItem::findOrFail($id);

            // Get all requisition items for this specific product
            $requisitionItems = DepartmentRequisitionItem::with(['inventoryItem', 'departmentRequisition'])
                ->whereHas('departmentRequisition', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId)
                      ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
                })
                ->where('inventory_item_id', $id)
                ->where('issued_total_pieces', '>', 0)
                ->get();

            $totalIssued = 0;
            $totalConsumed = 0;
            $totalSold = 0;
            $totalReturned = 0;
            $availableStock = 0;

            foreach ($requisitionItems as $item) {
                $issued = (float) ($item->issued_total_pieces ?? 0);
                $consumed = (float) ($item->quantity_consumed ?? 0);
                $sold = (float) ($item->quantity_sold ?? 0);
                $returned = (float) ($item->returned_total_pieces ?? 0);
                $available = $issued - ($consumed + $sold + $returned);

                $totalIssued += $issued;
                $totalConsumed += $consumed;
                $totalSold += $sold;
                $totalReturned += $returned;
                $availableStock += $available;
            }

            return view('bar.cashier.product-show', compact(
                'inventoryItem',
                'requisitionItems',
                'totalIssued',
                'totalConsumed',
                'totalSold',
                'totalReturned',
                'availableStock'
            ));

        } catch (\Exception $e) {
            Log::error('Bar product show error', [
                'user_id' => Auth::id(),
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('bar.cashier.products')->with('error', 'Product not found.');
        }
    }
}
