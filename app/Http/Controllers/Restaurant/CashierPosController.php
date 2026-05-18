<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\InventoryItem;
use App\Models\DepartmentRequisitionItem;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashierPosController extends Controller
{
    /**
     * Resolve the authenticated user's role name.
     */
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
     * Cashier Dashboard
     */
    public function dashboard()
    {
        try {
            Log::info('Cashier dashboard accessed', ['user_id' => Auth::id()]);

            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier') {
                return redirect()->route('dashboard')->with('error', 'Cashier access only.');
            }

            $todaySales   = SalesOrder::whereDate('created_at', today())->sum('total_amount');
            $todayOrders  = SalesOrder::whereDate('created_at', today())->count();
            $unpaidOrders = SalesOrder::where('payment_status', 'unpaid')->count();

            $recentOrders = SalesOrder::with('items.menuItem')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('restaurant.cashier.dashboard', compact('todaySales', 'todayOrders', 'unpaidOrders', 'recentOrders'));

        } catch (\Exception $e) {
            Log::error('Cashier dashboard error', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'Failed to load dashboard.');
        }
    }

    /**
     * Point of Sale (POS) Screen
     */
    public function pos()
    {
        try {
            Log::info('POS screen accessed', ['user_id' => Auth::id()]);

            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier') {
                return redirect()->route('dashboard')->with('error', 'Cashier access only.');
            }

            $menuItems    = MenuItem::where('is_active', true)
                ->orderBy('category')
                ->orderBy('name')
                ->get();

            $departmentId = $user->department_id;

            $requisitionItems = DepartmentRequisitionItem::with(['inventoryItem'])
                ->whereHas('departmentRequisition', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId)
                      ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
                })
                ->where('issued_total_pieces', '>', 0)
                ->get();

            $sellableItems = [];
            $currentStock  = [];

            foreach ($requisitionItems as $item) {
                $itemId        = $item->inventory_item_id;
                $inventoryItem = $item->inventoryItem;

                if (!$inventoryItem || !$inventoryItem->is_active) continue;

                $issued    = (float) ($item->issued_total_pieces ?? 0);
                $consumed  = (float) ($item->quantity_consumed ?? 0);
                $returned  = (float) ($item->returned_total_pieces ?? 0);
                $sold      = (float) ($item->quantity_sold ?? 0);
                $remaining = $issued - ($consumed + $returned + $sold);

                if ($remaining > 0) {
                    if (!isset($currentStock[$itemId])) {
                        $currentStock[$itemId] = $remaining;
                        $sellableItems[]        = $inventoryItem;
                    } else {
                        $currentStock[$itemId] += $remaining;
                    }
                }
            }

            $categories = ['Appetizer', 'Main', 'Dessert', 'Beverage', 'Side'];

            return view('restaurant.cashier.pos', compact('menuItems', 'sellableItems', 'currentStock', 'categories'));

        } catch (\Exception $e) {
            Log::error('POS screen error', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->route('restaurant.cashier.dashboard')->with('error', 'Failed to load POS.');
        }
    }

    /**
     * Menu (Read-only for cashier)
     */
    public function menu()
    {
        try {
            Log::info('Cashier menu accessed', ['user_id' => Auth::id()]);

            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier') {
                return redirect()->route('dashboard')->with('error', 'Cashier access only.');
            }

            $menuItems = MenuItem::where('is_active', true)
                ->orderBy('category')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            return view('restaurant.cashier.menu', compact('menuItems'));

        } catch (\Exception $e) {
            Log::error('Cashier menu error', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->route('restaurant.cashier.dashboard')->with('error', 'Failed to load menu.');
        }
    }

    /**
     * Orders List
     */
    public function orders(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier') {
                return redirect()->route('dashboard')->with('error', 'Cashier access only.');
            }

            $status = $request->get('status', 'unpaid');
            $search = $request->get('search', '');

            $orders = SalesOrder::with(['cashier', 'items'])
                ->when($status === 'unpaid', fn($q) => $q->where('payment_status', 'unpaid'))
                ->when($status === 'paid',   fn($q) => $q->where('payment_status', 'paid'))
                ->when($search, fn($q)       => $q->where('order_number', 'like', "%{$search}%"))
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            if ($request->ajax()) {
                $tableBody = '';
                foreach ($orders as $order) {
                    $tableBody .= '
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="p-3 font-mono text-xs font-bold">' . e($order->order_number) . '</td>
                        <td class="p-3 text-xs">' . $order->created_at->format('d/m/Y h:i A') . '</td>
                        <td class="p-3 text-xs">' . e($order->cashier->first_name ?? 'N/A') . '</td>
                        <td class="p-3 text-right font-semibold">UGX ' . number_format($order->total_amount, 0) . '</td>
                        <td class="p-3 text-center">';
                    if ($order->payment_status === 'unpaid') {
                        $tableBody .= '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Unpaid</span>';
                    } else {
                        $tableBody .= '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Paid</span>';
                    }
                    $tableBody .= '</td>
                        <td class="p-3 text-center">
                            <a href="' . route('restaurant.cashier.orders.show', $order->id) . '" class="text-orange-600 hover:text-orange-800 text-sm">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                        </td>
                    </tr>';
                }

                if (count($orders) == 0) {
                    $tableBody = '<tr><td colspan="6" class="p-8 text-center text-gray-400">
                        <i class="fas fa-receipt text-4xl mb-2 block"></i>
                        ' . ($search ? 'No orders found for invoice # "' . e($search) . '"' : 'No orders found') . '
                    </td></tr>';
                }

                $pagination = $orders->appends(['status' => $status, 'search' => $search])->links()->toHtml();

                return response()->json([
                    'tableBody'   => $tableBody,
                    'pagination'  => $pagination,
                    'unpaidCount' => SalesOrder::where('payment_status', 'unpaid')->count(),
                    'paidCount'   => SalesOrder::where('payment_status', 'paid')->count(),
                ]);
            }

            return view('restaurant.cashier.orders', compact('orders', 'status'));

        } catch (\Exception $e) {
            Log::error('Orders list error', ['error' => $e->getMessage()]);
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return redirect()->route('restaurant.cashier.dashboard')->with('error', 'Failed to load orders.');
        }
    }

    /**
     * Show a single order
     */
    public function showOrder($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier') {
                return redirect()->route('dashboard')->with('error', 'Cashier access only.');
            }

            $order = SalesOrder::with('items')->findOrFail($id);

            return $order->payment_status === 'unpaid'
                ? view('restaurant.cashier.invoice', compact('order'))
                : view('restaurant.cashier.receipt', compact('order'));

        } catch (\Exception $e) {
            Log::error('Show order error', ['order_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('restaurant.cashier.orders')->with('error', 'Order not found.');
        }
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid($id, Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier') {
                return response()->json(['success' => false, 'message' => 'Cashier access only'], 403);
            }

            $request->validate([
                'payment_method' => 'required|in:cash,card,mobile_money',
                'amount_paid'    => 'nullable|numeric',
                'change_amount'  => 'nullable|numeric',
            ]);

            DB::beginTransaction();

            $order = SalesOrder::findOrFail($id);

            if ($order->payment_status === 'paid') {
                return response()->json(['success' => false, 'message' => 'Order already paid'], 400);
            }

            $order->payment_method = $request->payment_method;
            $order->amount_paid    = $request->amount_paid ?? $order->total_amount;
            $order->change_amount  = $request->change_amount ?? 0;
            $order->status         = 'completed';
            $order->payment_status = 'paid';
            $order->save();

            DB::commit();

            Log::info('Order marked as paid', [
                'cashier_id'     => Auth::id(),
                'order_id'       => $order->id,
                'order_number'   => $order->order_number,
                'payment_method' => $request->payment_method,
                'amount_paid'    => $request->amount_paid,
                'change_amount'  => $request->change_amount,
            ]);

            return response()->json([
                'success'       => true,
                'order_id'      => $order->id,
                'order_number'  => $order->order_number,
                'total_amount'  => $order->total_amount,
                'amount_paid'   => $request->amount_paid,
                'change_amount' => $request->change_amount,
                'message'       => 'Payment successful',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment failed', ['order_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Process direct sale (legacy - without invoice)
     */
    public function processSale(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier') {
                return response()->json(['success' => false, 'message' => 'Cashier access only'], 403);
            }

            $request->validate([
                'items'        => 'required|array|min:1',
                'total_amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:cash,card,mobile_money',
            ]);

            DB::beginTransaction();

            $orderNumber = 'SALE-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $order = SalesOrder::create([
                'order_number'   => $orderNumber,
                'cashier_id'     => Auth::id(),
                'department_id'  => $user->department_id,  // ✅ stored
                'customer_type'  => $request->customer_type ?? 'dine_in',
                'subtotal'       => $request->total_amount,
                'tax_amount'     => 0,
                'total_amount'   => $request->total_amount,
                'payment_method' => null,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
            ]);

            DB::commit();

            return response()->json([
                'success'      => true,
                'order_id'     => $order->id,
                'order_number' => $orderNumber,
                'message'      => 'Sale processed successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Process sale failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create Invoice (pending payment) - deducts stock for inventory items
     */
    public function createInvoice(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier') {
                return response()->json(['success' => false, 'message' => 'Cashier access only'], 403);
            }

            $request->validate([
                'items'              => 'required|array|min:1',
                'items.*.item_id'    => 'required',
                'items.*.item_type'  => 'required|in:menu,inventory',
                'items.*.quantity'   => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'required|numeric|min:0',
                'total_amount'       => 'required|numeric|min:0',
                'customer_type'      => 'nullable|in:dine_in,takeaway,delivery',
            ]);

            DB::beginTransaction();

            $orderNumber  = 'INV-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $departmentId = $user->department_id;

            // ✅ department_id now included
            $order = SalesOrder::create([
                'order_number'   => $orderNumber,
                'cashier_id'     => Auth::id(),
                'department_id'  => $departmentId,
                'customer_type'  => $request->customer_type ?? 'dine_in',
                'subtotal'       => $request->total_amount,
                'tax_amount'     => 0,
                'total_amount'   => $request->total_amount,
                'payment_method' => null,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
            ]);

            $itemsList = [];

            foreach ($request->items as $itemData) {
                if ($itemData['item_type'] === 'menu') {
                    $menuItem = MenuItem::find($itemData['item_id']);
                    if (!$menuItem) {
                        throw new \Exception("Menu item not found: " . $itemData['item_id']);
                    }

                    SalesOrderItem::create([
                        'sales_order_id' => $order->id,
                        'menu_item_id'   => $itemData['item_id'],
                        'item_name'      => $menuItem->name,
                        'quantity'       => $itemData['quantity'],
                        'unit_price'     => $menuItem->selling_price,
                        'total_price'    => $itemData['quantity'] * $menuItem->selling_price,
                    ]);

                    $itemsList[] = [
                        'name'       => $menuItem->name,
                        'quantity'   => $itemData['quantity'],
                        'unit_price' => $menuItem->selling_price,
                    ];

                } else {
                    // INVENTORY ITEMS - check and deduct stock from requisitions
                    $inventoryItem = InventoryItem::find($itemData['item_id']);
                    if (!$inventoryItem) {
                        throw new \Exception("Inventory item not found: " . $itemData['item_id']);
                    }

                    $quantityToDeduct = (float) $itemData['quantity'];

                    $reqItems = DepartmentRequisitionItem::with(['departmentRequisition'])
                        ->whereHas('departmentRequisition', function ($q) use ($departmentId) {
                            $q->where('department_id', $departmentId)
                              ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
                        })
                        ->where('inventory_item_id', $itemData['item_id'])
                        ->where('issued_total_pieces', '>', 0)
                        ->orderBy('created_at', 'asc')
                        ->get();

                    foreach ($reqItems as $reqItem) {
                        if ($quantityToDeduct <= 0) break;

                        $issued    = (float) ($reqItem->issued_total_pieces ?? 0);
                        $consumed  = (float) ($reqItem->quantity_consumed ?? 0);
                        $returned  = (float) ($reqItem->returned_total_pieces ?? 0);
                        $sold      = (float) ($reqItem->quantity_sold ?? 0);
                        $available = $issued - ($consumed + $returned + $sold);

                        if ($available <= 0) continue;

                        $deductAmount          = min($quantityToDeduct, $available);
                        $reqItem->quantity_sold = $sold + $deductAmount;
                        $reqItem->last_sold_at  = now();
                        $reqItem->save();

                        $quantityToDeduct -= $deductAmount;
                    }

                    if ($quantityToDeduct > 0) {
                        throw new \Exception("Insufficient stock for {$inventoryItem->name}.");
                    }

                    $unitPrice = $inventoryItem->selling_price ?? $itemData['unit_price'];

                    SalesOrderItem::create([
                        'sales_order_id'    => $order->id,
                        'inventory_item_id' => $itemData['item_id'],
                        'item_name'         => $inventoryItem->name,
                        'quantity'          => $itemData['quantity'],
                        'unit_price'        => $unitPrice,
                        'total_price'       => $itemData['quantity'] * $unitPrice,
                    ]);

                    $itemsList[] = [
                        'name'       => $inventoryItem->name,
                        'quantity'   => $itemData['quantity'],
                        'unit_price' => $unitPrice,
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'order_id'     => $order->id,
                'order_number' => $orderNumber,
                'total_amount' => $request->total_amount,
                'items'        => $itemsList,
                'message'      => 'Invoice created successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create invoice failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store Order (direct sale with immediate payment)
     */
    public function storeOrder(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier') {
                return response()->json(['success' => false, 'message' => 'Cashier access only'], 403);
            }

            $request->validate([
                'items'                  => 'required|array|min:1',
                'items.*.menu_item_id'   => 'required|exists:menu_items,id',
                'items.*.quantity'       => 'required|integer|min:1',
                'items.*.unit_price'     => 'required|numeric|min:0',
                'subtotal'               => 'required|numeric|min:0',
                'tax_amount'             => 'required|numeric|min:0',
                'total_amount'           => 'required|numeric|min:0',
                'payment_method'         => 'required|in:cash,card,mobile_money',
                'customer_type'          => 'nullable|in:dine_in,takeaway,delivery',
                'table_number'           => 'nullable|string',
            ]);

            DB::beginTransaction();

            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $order = SalesOrder::create([
                'order_number'   => $orderNumber,
                'cashier_id'     => Auth::id(),
                'department_id'  => $user->department_id,  // ✅ stored
                'customer_type'  => $request->customer_type ?? 'dine_in',
                'table_number'   => $request->table_number,
                'subtotal'       => $request->subtotal,
                'tax_amount'     => $request->tax_amount,
                'total_amount'   => $request->total_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid',
                'status'         => 'completed',
            ]);

            foreach ($request->items as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'menu_item_id'   => $item['menu_item_id'],
                    'item_name'      => $menuItem->name,
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['unit_price'],
                    'total_price'    => $item['quantity'] * $item['unit_price'],
                ]);

                if ($menuItem && $menuItem->inventory_item_id) {
                    $inventory = InventoryItem::find($menuItem->inventory_item_id);
                    if ($inventory) {
                        $inventory->current_stock = max(0, $inventory->current_stock - $item['quantity']);
                        $inventory->save();
                    }
                }
            }

            DB::commit();

            Log::info('Order completed', ['cashier_id' => Auth::id(), 'order_number' => $orderNumber]);

            return response()->json(['success' => true, 'order_id' => $order->id, 'order_number' => $orderNumber]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * View Receipt for paid order
     */
    public function getReceipt($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $order = SalesOrder::with('items')->findOrFail($id);

            if ($order->payment_status !== 'paid') {
                return redirect()->route('restaurant.cashier.orders')->with('error', 'Order not paid yet.');
            }

            return view('restaurant.cashier.receipt', compact('order'));

        } catch (\Exception $e) {
            Log::error('Receipt error', ['order_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('restaurant.cashier.orders')->with('error', 'Order not found.');
        }
    }

    /**
     * Get Invoice for unpaid order
     */
    public function getInvoice($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier') {
                return redirect()->route('dashboard')->with('error', 'Cashier access only.');
            }

            $order = SalesOrder::with('items')->findOrFail($id);

            if ($order->payment_status !== 'unpaid') {
                return redirect()->route('restaurant.cashier.orders')->with('error', 'Order already paid.');
            }

            return view('restaurant.cashier.invoice', compact('order'));

        } catch (\Exception $e) {
            Log::error('Invoice error', ['order_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('restaurant.cashier.orders')->with('error', 'Order not found.');
        }
    }

    /**
     * Reports Index
     */
    public function reports()
    {
        return view('restaurant.cashier.reports');
    }

    /**
     * Daily Report
     */
    public function dailyReport(Request $request)
    {
        try {
            $date = $request->get('date', today()->toDateString());

            $orders = SalesOrder::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->with('items')
                ->get();

            $totalSales  = $orders->sum('total_amount');
            $totalOrders = $orders->count();

            return view('restaurant.cashier.reports-daily', compact('orders', 'totalSales', 'totalOrders', 'date'));

        } catch (\Exception $e) {
            Log::error('Daily report error', ['error' => $e->getMessage()]);
            return redirect()->route('restaurant.cashier.reports')->with('error', 'Failed to load report.');
        }
    }

    public function exportExcel(Request $request)
    {
        return redirect()->back()->with('info', 'Excel export coming soon.');
    }

    public function exportPdf(Request $request)
    {
        return redirect()->back()->with('info', 'PDF export coming soon.');
    }
}
