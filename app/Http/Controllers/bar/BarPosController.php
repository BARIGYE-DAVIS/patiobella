<?php
// app/Http/Controllers/Bar/BarPosController.php

namespace App\Http\Controllers\Bar;

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

class BarPosController extends Controller
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
     * Display POS interface.
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier' && $roleName !== 'Bar Cashier' && $roleName !== 'Bar Manager' && $roleName !== 'BAR MANAGER' && $roleName !== 'Admin' && $roleName !== 'ADMIN') {
                return redirect()->route('bar.dashboard')->with('error', 'Unauthorized access.');
            }

            // Get active menu items (bar-specific - beverages, wines, beers, spirits, cocktails)
            $menuItems = MenuItem::where('is_active', true)
                ->whereIn('category', ['Beverage', 'Wine', 'Beer', 'Spirits', 'Cocktails', 'Juice', 'Soft Drinks'])
                ->orderBy('category')
                ->orderBy('name')
                ->get();

            // Get sellable inventory items (bottles, cans, etc.)
            $sellableItems = InventoryItem::where('is_active', true)
                ->where('is_sellable', true)
                ->orderBy('name')
                ->get();

            // Get bar's current stock from requisitions
            $departmentId = $user->department_id;
            $currentStock = $this->getCurrentStockForDepartment($departmentId);

            return view('bar.pos', compact('menuItems', 'sellableItems', 'currentStock'));

        } catch (\Exception $e) {
            Log::error('Bar POS error', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->route('bar.dashboard')->with('error', 'Failed to load POS.');
        }
    }

    /**
     * Calculate current available stock for all inventory items in department
     */
    private function getCurrentStockForDepartment($departmentId)
    {
        $stockItems = DepartmentRequisitionItem::with(['inventoryItem', 'departmentRequisition'])
            ->whereHas('departmentRequisition', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId)
                  ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
            })
            ->where('issued_total_pieces', '>', 0)
            ->get();

        $currentStock = [];
        foreach ($stockItems as $item) {
            $itemId = $item->inventory_item_id;
            $issued = (float) ($item->issued_total_pieces ?? 0);
            $consumed = (float) ($item->quantity_consumed ?? 0);
            $returned = (float) ($item->returned_total_pieces ?? 0);
            $sold = (float) ($item->quantity_sold ?? 0);
            $remaining = $issued - ($consumed + $returned + $sold);

            if ($remaining > 0) {
                if (!isset($currentStock[$itemId])) {
                    $currentStock[$itemId] = $remaining;
                } else {
                    $currentStock[$itemId] += $remaining;
                }
            }
        }

        return $currentStock;
    }

    /**
     * Create invoice
     */
    public function createInvoice(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required',
                'items.*.item_type' => 'required|in:menu,inventory',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'customer_type' => 'nullable|in:dine_in,takeaway,delivery',
            ]);

            DB::beginTransaction();

            $orderNumber = 'BAR-INV-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

$order = SalesOrder::create([
    'order_number' => $orderNumber,
    'cashier_id' => Auth::id(),
    'department_id' => Auth::user()->department_id,
    'customer_type' => $request->customer_type ?? 'dine_in',
    'subtotal' => $request->total_amount,
    'tax_amount' => 0,
    'total_amount' => $request->total_amount,
    'payment_method' => null,
    'status' => 'pending',
    'payment_status' => 'unpaid',
]);

            $departmentId = $user->department_id;

            foreach ($request->items as $itemData) {
                if ($itemData['item_type'] === 'menu') {
                    $menuItem = MenuItem::find($itemData['item_id']);
                    if (!$menuItem) {
                        throw new \Exception("Menu item not found: " . $itemData['item_id']);
                    }

                    SalesOrderItem::create([
                        'sales_order_id' => $order->id,
                        'menu_item_id' => $itemData['item_id'],
                        'item_name' => $menuItem->name,
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $menuItem->selling_price,
                        'total_price' => $itemData['quantity'] * $menuItem->selling_price,
                    ]);
                } else {
                    $inventoryItem = InventoryItem::find($itemData['item_id']);
                    if (!$inventoryItem) {
                        throw new \Exception("Inventory item not found: " . $itemData['item_id']);
                    }

                    // Deduct stock for inventory items
                    $quantityToDeduct = (float) $itemData['quantity'];

                    $reqItems = DepartmentRequisitionItem::with(['departmentRequisition'])
                        ->whereHas('departmentRequisition', function($q) use ($departmentId) {
                            $q->where('department_id', $departmentId)
                              ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
                        })
                        ->where('inventory_item_id', $itemData['item_id'])
                        ->where('issued_total_pieces', '>', 0)
                        ->orderBy('created_at', 'asc')
                        ->get();

                    foreach ($reqItems as $reqItem) {
                        if ($quantityToDeduct <= 0) break;

                        $issued = (float) ($reqItem->issued_total_pieces ?? 0);
                        $consumed = (float) ($reqItem->quantity_consumed ?? 0);
                        $returned = (float) ($reqItem->returned_total_pieces ?? 0);
                        $sold = (float) ($reqItem->quantity_sold ?? 0);
                        $available = $issued - ($consumed + $returned + $sold);

                        if ($available <= 0) continue;

                        $deductAmount = min($quantityToDeduct, $available);
                        $reqItem->quantity_sold = $sold + $deductAmount;
                        $reqItem->last_sold_at = now();
                        $reqItem->save();

                        $quantityToDeduct -= $deductAmount;
                    }

                    if ($quantityToDeduct > 0) {
                        throw new \Exception("Insufficient stock for {$inventoryItem->name}.");
                    }

                    $unitPrice = $inventoryItem->selling_price ?? $itemData['unit_price'];

                    SalesOrderItem::create([
                        'sales_order_id' => $order->id,
                        'inventory_item_id' => $itemData['item_id'],
                        'item_name' => $inventoryItem->name,
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $unitPrice,
                        'total_price' => $itemData['quantity'] * $unitPrice,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $orderNumber,
                'total_amount' => $request->total_amount,
                'message' => 'Invoice created successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bar invoice creation failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid($id, Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'payment_method' => 'required|in:cash,card,mobile_money',
                'amount_paid' => 'nullable|numeric',
                'change_amount' => 'nullable|numeric',
            ]);

            DB::beginTransaction();

            $order = SalesOrder::findOrFail($id);

            if ($order->payment_status === 'paid') {
                return response()->json(['success' => false, 'message' => 'Order already paid'], 400);
            }

            $order->payment_method = $request->payment_method;
            $order->amount_paid = $request->amount_paid ?? $order->total_amount;
            $order->change_amount = $request->change_amount ?? 0;
            $order->status = 'completed';
            $order->payment_status = 'paid';
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'message' => 'Payment successful'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bar payment failed', ['order_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Orders list
     */
    public function orders(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $status = $request->get('status', 'unpaid');

            $orders = SalesOrder::with(['cashier', 'items'])
                ->when($status === 'unpaid', function($q) {
                    $q->where('payment_status', 'unpaid');
                })
                ->when($status === 'paid', function($q) {
                    $q->where('payment_status', 'paid');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('bar.orders', compact('orders', 'status'));

        } catch (\Exception $e) {
            Log::error('Bar orders error', ['error' => $e->getMessage()]);
            return redirect()->route('bar.cashier.dashboard')->with('error', 'Failed to load orders.');
        }
    }

    /**
     * Show single order
     */
    public function showOrder($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $order = SalesOrder::with('items')->findOrFail($id);

            if ($order->payment_status !== 'paid') {
                return view('bar.invoice', compact('order'));
            } else {
                return view('bar.receipt', compact('order'));
            }

        } catch (\Exception $e) {
            Log::error('Bar show order error', ['order_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('bar.cashier.orders')->with('error', 'Order not found.');
        }
    }

    /**
     * Get invoice
     */
    public function getInvoice($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $order = SalesOrder::with('items')->findOrFail($id);

            if ($order->payment_status !== 'unpaid') {
                return redirect()->route('bar.cashier.orders')->with('error', 'Order already paid.');
            }

            return view('bar.invoice', compact('order'));

        } catch (\Exception $e) {
            Log::error('Bar invoice error', ['order_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('bar.cashier.orders')->with('error', 'Order not found.');
        }
    }

    /**
     * Get receipt
     */
    public function getReceipt($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $order = SalesOrder::with('items')->findOrFail($id);

            if ($order->payment_status !== 'paid') {
                return redirect()->route('bar.cashier.orders')->with('error', 'Order not paid yet.');
            }

            return view('bar.receipt', compact('order'));

        } catch (\Exception $e) {
            Log::error('Bar receipt error', ['order_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('bar.cashier.orders')->with('error', 'Order not found.');
        }
    }
}
