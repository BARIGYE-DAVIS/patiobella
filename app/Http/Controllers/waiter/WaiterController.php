<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Models\MenuItemCategory;
use App\Models\MenuItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Ticket;
use App\Models\DepartmentRequisitionItem;
use App\Models\RecipeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WaiterController extends Controller
{
    /**
     * Display waiter dashboard.
     */
public function index()
{
    $tables = RestaurantTable::where('is_active', true)
        ->orderBy('table_number')
        ->get();

    // Get unpaid orders with printed status for each table
    $orders = SalesOrder::where('payment_status', 'unpaid')
        ->where('status', '!=', 'cancelled')
        ->get()
        ->keyBy('table_id');

    // Get all active menus
    $menus = \App\Models\Menu::where('is_active', true)
        ->with('department')
        ->orderBy('name')
        ->get();

    $categories = MenuItemCategory::where('is_active', true)
        ->orderBy('sort_order')
        ->get();

    return view('waiter.index', compact('tables', 'categories', 'menus', 'orders'));
}
    /**
     * Get products by category (AJAX).
     */
    public function getProductsByCategory(Request $request, $categoryId)
    {
        $menuId = $request->get('menu_id');

        $query = MenuItem::where('is_active', true)->with('menuItemCategory');

        // Filter by menu if provided
        if ($menuId) {
            $query->where('menu_id', $menuId);
        }

        // Filter by category
        if ($categoryId != 'all') {
            $query->where('menu_item_category_id', $categoryId);
        }

        $products = $query->get();

        return response()->json($products);
    }

    /**
     * Search products (AJAX).
     */
    public function searchProducts(Request $request)
    {
        $search = $request->get('search');
        $menuId = $request->get('menu_id');

        $query = MenuItem::where('is_active', true)
            ->where('name', 'like', "%{$search}%")
            ->with('menuItemCategory');

        if ($menuId) {
            $query->where('menu_id', $menuId);
        }

        $products = $query->limit(20)->get();

        return response()->json($products);
    }

    /**
     * Get active orders for current waiter (AJAX).
     */
    public function getActiveOrders()
    {
        $orders = SalesOrder::where('waiter_id', Auth::id())
            ->where('payment_status', 'unpaid')
            ->where('status', '!=', 'cancelled')
            ->with('table')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    /**
     * Get order status (AJAX).
     */
    public function getOrderStatus($orderId)
    {
        $order = SalesOrder::findOrFail($orderId);

        return response()->json([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'is_paid' => $order->payment_status === 'paid'
        ]);
    }

    /**
     * Check department stock for a specific inventory item
     */private function checkDepartmentStock($departmentId, $inventoryItemId, $requiredQuantity)
{
    $departmentStock = DepartmentRequisitionItem::where('inventory_item_id', $inventoryItemId)
        ->whereHas('requisition', function($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->selectRaw('SUM(quantity_issued - COALESCE(quantity_consumed, 0) - COALESCE(quantity_returned, 0)) as available')
        ->first();

    return $departmentStock->available ?? 0;
}

    /**
     * Update department consumption after order
     */
private function updateDepartmentConsumption($departmentId, $inventoryItemId, $consumedQuantity)
{
    $requisitionItems = DepartmentRequisitionItem::where('inventory_item_id', $inventoryItemId)
        ->whereHas('requisition', function($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->whereRaw('(quantity_issued - COALESCE(quantity_consumed, 0) - COALESCE(quantity_returned, 0)) > 0')
        ->orderBy('created_at', 'asc')
        ->get();

    $remainingToConsume = $consumedQuantity;

    foreach ($requisitionItems as $item) {
        if ($remainingToConsume <= 0) break;

        $currentAvailable = $item->quantity_issued - ($item->quantity_consumed ?? 0) - ($item->quantity_returned ?? 0);
        $toConsume = min($remainingToConsume, $currentAvailable);

        $item->quantity_consumed = ($item->quantity_consumed ?? 0) + $toConsume;
        $item->last_consumed_at = now();
        $item->save();

        $remainingToConsume -= $toConsume;
    }
}

/**
 * Place order (AJAX).
 */
public function placeOrder(Request $request)
{
    $request->validate([
        'table_id' => 'required|exists:restaurant_tables,id',
        'items' => 'required|array|min:1',
        'items.*.id' => 'required|exists:menu_items,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric',
        'items.*.comments' => 'nullable|string',
        'items.*.supplement' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $total = 0;
        $stockWarnings = [];
        $departmentConsumptions = [];

        // First, check stock for all items and collect warnings
        foreach ($request->items as $itemData) {
            $menuItem = MenuItem::find($itemData['id']);
            $quantity = $itemData['quantity'];
            $total += $itemData['price'] * $quantity;

            // Get department from menu
            $departmentId = $menuItem->menu->department_id ?? null;
            $departmentName = $menuItem->menu->department->name ?? 'Unknown';

            if (!$departmentId) {
                continue;
            }

            // Get all ingredients for this menu item (recipe items)
            $recipeItems = RecipeItem::where('menu_item_id', $menuItem->id)
                ->with('inventoryItem')
                ->get();

            if ($recipeItems->isNotEmpty()) {
                // This is a recipe item (food that needs preparation)
                foreach ($recipeItems as $recipeItem) {
                    $inventoryItem = $recipeItem->inventoryItem;
                    if (!$inventoryItem) continue;

                    $requiredQty = $recipeItem->quantity_required * $quantity;
                    $requiredQtyWithWastage = $requiredQty * (1 + ($recipeItem->wastage_percentage / 100));

                    $availableQty = $this->checkDepartmentStock($departmentId, $inventoryItem->id, $requiredQtyWithWastage);

                    if ($availableQty < $requiredQtyWithWastage) {
                        $stockWarnings[] = [
                            'department' => $departmentName,
                            'item_name' => $menuItem->name,
                            'ingredient' => $inventoryItem->name,
                            'required' => round($requiredQtyWithWastage, 2),
                            'available' => round($availableQty, 2),
                            'unit' => $inventoryItem->base_unit,
                            'type' => 'recipe'
                        ];
                    }

                    // Track consumption
                    $key = $departmentId . '_' . $inventoryItem->id;
                    if (!isset($departmentConsumptions[$key])) {
                        $departmentConsumptions[$key] = [
                            'department_id' => $departmentId,
                            'inventory_item_id' => $inventoryItem->id,
                            'quantity' => 0
                        ];
                    }
                    $departmentConsumptions[$key]['quantity'] += $requiredQtyWithWastage;
                }
            } else if ($menuItem->inventory_item_id) {
                // This is a direct sale item (beverage)
                $inventoryItemId = $menuItem->inventory_item_id;
                $inventoryItem = $menuItem->inventoryItem;
                $requiredQty = $quantity;

                $availableQty = $this->checkDepartmentStock($departmentId, $inventoryItemId, $requiredQty);

                if ($availableQty < $requiredQty) {
                    $stockWarnings[] = [
                        'department' => $departmentName,
                        'item_name' => $menuItem->name,
                        'ingredient' => $inventoryItem->name ?? $menuItem->name,
                        'required' => $requiredQty,
                        'available' => round($availableQty, 2),
                        'unit' => $inventoryItem->base_unit ?? 'piece',
                        'type' => 'direct'
                    ];
                }

                // Track consumption for direct sale items
                $key = $departmentId . '_' . $inventoryItemId;
                if (!isset($departmentConsumptions[$key])) {
                    $departmentConsumptions[$key] = [
                        'department_id' => $departmentId,
                        'inventory_item_id' => $inventoryItemId,
                        'quantity' => 0
                    ];
                }
                $departmentConsumptions[$key]['quantity'] += $requiredQty;
            }
        }

        // Get table number
        $table = RestaurantTable::find($request->table_id);

        // Create sales order
        $orderNumber = $this->generateOrderNumber();

        $order = SalesOrder::create([
            'order_number' => $orderNumber,
            'table_id' => $request->table_id,
            'table_number' => $table->table_number,
            'waiter_id' => Auth::id(),
            'notes' => $request->notes,
            'subtotal' => $total,
            'tax_amount' => 0,
            'total_amount' => $total,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'customer_type' => 'dine_in',
            'created_by' => Auth::id(),
        ]);

        // Create order items and group by department for tickets
        $kitchenItems = [];
        $barItems = [];
        $cafeItems = [];

        foreach ($request->items as $itemData) {
            $menuItem = MenuItem::find($itemData['id']);
            $quantity = $itemData['quantity'];

            // Create order item
            SalesOrderItem::create([
                'sales_order_id' => $order->id,
                'menu_item_id' => $itemData['id'],
                'item_name' => $itemData['name'],
                'quantity' => $quantity,
                'unit_price' => $itemData['price'],
                'total_price' => $itemData['price'] * $quantity,
                'notes' => $itemData['comments'] ?? null,
            ]);

            // Determine department based on menu's department
            $department = $this->getDepartmentForMenuItem($menuItem);

            // Get ingredients for this menu item
            $recipeItems = RecipeItem::where('menu_item_id', $menuItem->id)
                ->with('inventoryItem')
                ->get();

            $ingredientsList = [];
            foreach ($recipeItems as $recipeItem) {
                if ($recipeItem->inventoryItem) {
                    $ingredientsList[] = [
                        'name' => $recipeItem->inventoryItem->name,
                        'quantity' => $recipeItem->quantity_required * $quantity,
                        'unit' => $recipeItem->inventoryItem->base_unit,
                        'wastage' => $recipeItem->wastage_percentage ?? 0
                    ];
                }
            }

            $itemWithDetails = [
                'menu_item_id' => $menuItem->id,
                'item_name' => $menuItem->name,
                'quantity' => $quantity,
                'comments' => $itemData['comments'] ?? null,
                'supplement' => $itemData['supplement'] ?? null,
                'ingredients' => $ingredientsList  // ADD INGREDIENTS HERE
            ];

            if ($department === 'kitchen') {
                $kitchenItems[] = $itemWithDetails;
            } elseif ($department === 'bar') {
                $barItems[] = $itemWithDetails;
            } elseif ($department === 'cafe') {
                $cafeItems[] = $itemWithDetails;
            }
        }

        // Create tickets for each department
        $waiterName = Auth::user()->first_name . ' ' . Auth::user()->last_name;

        if (!empty($kitchenItems)) {
            $this->createTicket('kitchen', $order->id, $table->table_number, $waiterName, $kitchenItems);
        }

        if (!empty($barItems)) {
            $this->createTicket('bar', $order->id, $table->table_number, $waiterName, $barItems);
        }

        if (!empty($cafeItems)) {
            $this->createTicket('cafe', $order->id, $table->table_number, $waiterName, $cafeItems);
        }

        // Update department consumption
        foreach ($departmentConsumptions as $consumption) {
            $this->updateDepartmentConsumption(
                $consumption['department_id'],
                $consumption['inventory_item_id'],
                $consumption['quantity']
            );
        }

        // Mark table as occupied
        RestaurantTable::where('id', $request->table_id)->update(['is_occupied' => true]);

        DB::commit();

        // Build warning message
        $warningMessage = null;
        if (!empty($stockWarnings)) {
            $warningMessage = "⚠️ STOCK ALERT:\n";
            $groupedWarnings = [];
            foreach ($stockWarnings as $warning) {
                $key = $warning['department'] . ' - ' . $warning['item_name'];
                if (!isset($groupedWarnings[$key])) {
                    $groupedWarnings[$key] = [];
                }
                $groupedWarnings[$key][] = $warning;
            }

            foreach ($groupedWarnings as $itemKey => $warnings) {
                $warningMessage .= "\n📌 " . $itemKey . ":\n";
                foreach ($warnings as $warning) {
                    if ($warning['type'] === 'recipe') {
                        $warningMessage .= "   • Missing {$warning['ingredient']}: Need {$warning['required']} {$warning['unit']}, Only {$warning['available']} {$warning['unit']} available in {$warning['department']}\n";
                    } else {
                        $warningMessage .= "   • Low stock: Need {$warning['required']} {$warning['unit']}(s), Only {$warning['available']} {$warning['unit']}(s) available in {$warning['department']}\n";
                    }
                }
                $warningMessage .= "     → Order will still be processed. Items will be restocked soon.\n";
            }
        }

        Log::info('Order placed', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'waiter_id' => Auth::id(),
            'table_id' => $request->table_id,
            'total' => $total,
            'kitchen_items' => count($kitchenItems),
            'bar_items' => count($barItems),
            'cafe_items' => count($cafeItems),
            'warnings' => count($stockWarnings)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total' => $total,
            'warnings' => $stockWarnings,
            'warning_message' => $warningMessage
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Order placement failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to place order: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Determine which department handles a menu item based on menu's department.
     */
    private function getDepartmentForMenuItem($menuItem)
    {
        // Get department from the menu
        if ($menuItem->menu && $menuItem->menu->department) {
            $departmentName = strtolower($menuItem->menu->department->name);

            if ($departmentName === 'bar') {
                return 'bar';
            }
            if ($departmentName === 'kitchen') {
                return 'kitchen';
            }
            if ($departmentName === 'cafe') {
                return 'cafe';
            }
        }

        // Fallback: check inventory item category
        if ($menuItem->inventory_item_id) {
            $inventoryItem = $menuItem->inventoryItem;
            if ($inventoryItem && $inventoryItem->category_id) {
                $barCategories = [1, 2, 3, 4, 5, 6];
                $kitchenCategories = [8, 9, 10, 11, 12, 13, 14];
                $cafeCategories = [15, 16];

                if (in_array($inventoryItem->category_id, $barCategories)) {
                    return 'bar';
                }
                if (in_array($inventoryItem->category_id, $kitchenCategories)) {
                    return 'kitchen';
                }
                if (in_array($inventoryItem->category_id, $cafeCategories)) {
                    return 'cafe';
                }
            }
        }

        return 'kitchen';
    }

    /**
     * Create a ticket for a department.
     */
    private function createTicket($ticketType, $orderId, $tableNumber, $waiterName, $items)
    {
        $ticketNumber = $this->generateTicketNumber($ticketType);

        return Ticket::create([
            'ticket_number' => $ticketNumber,
            'ticket_type' => $ticketType,
            'sales_order_id' => $orderId,
            'table_number' => $tableNumber,
            'waiter_name' => $waiterName,
            'items' => json_encode($items),
            'is_printed' => false,
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Generate unique ticket number.
     */
    private function generateTicketNumber($ticketType)
    {
        $prefix = '';
        switch ($ticketType) {
            case 'kitchen':
                $prefix = 'KOT';
                break;
            case 'bar':
                $prefix = 'BOT';
                break;
            case 'cafe':
                $prefix = 'COT';
                break;
        }

        $date = date('Ymd');
        $lastTicket = Ticket::where('ticket_type', $ticketType)
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTicket) {
            $lastNumber = intval(substr($lastTicket->ticket_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $date . $newNumber;
    }

    /**
     * Generate unique order number.
     */
    private function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = date('Ymd');
        $lastOrder = SalesOrder::whereDate('created_at', today())->orderBy('id', 'desc')->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $date . $newNumber;
    }

    /**
     * Get all bills for the current waiter
     */
    public function getBills()
    {
        $orders = SalesOrder::where('waiter_id', Auth::id())
            ->with('table')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('waiter.bills.index', compact('orders'));
    }

    /**
     * Print a specific bill
     */
public function printBill($orderId)
{
    $order = SalesOrder::with(['items.menuItem', 'table', 'waiter'])->findOrFail($orderId);

    // Only waiter who created the order or admin can view
    if ($order->waiter_id != Auth::id() && !Auth::user()->is_super_admin) {
        abort(403, 'Unauthorized access');
    }

    // Mark order as printed in database
    $order->is_printed = 1;
    $order->save();

    // Calculate totals using stored VAT from each menu item
    $totalSellingPrice = 0;
    $totalVatAmount = 0;
    $totalNetPrice = 0;

    foreach ($order->items as $item) {
        $menuItem = $item->menuItem;
        $quantity = $item->quantity;

        $itemVat = ($menuItem->vat_amount ?? 0) * $quantity;
        $itemNet = ($menuItem->net_price ?? $menuItem->selling_price) * $quantity;
        $itemSelling = $menuItem->selling_price * $quantity;

        $totalSellingPrice += $itemSelling;
        $totalVatAmount += $itemVat;
        $totalNetPrice += $itemNet;
    }

    return view('waiter.bills.print', compact('order', 'totalSellingPrice', 'totalVatAmount', 'totalNetPrice'));
}
    /**
     * Get order bill for printing (legacy).
     */
    public function getBill($orderId)
    {
        $order = SalesOrder::with(['items', 'table', 'waiter'])->findOrFail($orderId);

        // Only waiter who created the order or admin can view
        if ($order->waiter_id != Auth::id() && !Auth::user()->is_super_admin) {
            abort(403, 'Unauthorized access');
        }

        return view('waiter.bill', compact('order'));
    }
}
