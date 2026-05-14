<?php
// app/Http/Controllers/Restaurant/CashierController.php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\InventoryItem;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashierController extends Controller
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

            $todaySales = SalesOrder::whereDate('created_at', today())->sum('total_amount');
            $todayOrders = SalesOrder::whereDate('created_at', today())->count();
            $pendingOrders = SalesOrder::where('status', 'pending')->count();

            $recentOrders = SalesOrder::with('items.menuItem')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('restaurant.cashier.dashboard', compact('todaySales', 'todayOrders', 'pendingOrders', 'recentOrders'));

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

            $menuItems = MenuItem::where('is_active', true)->orderBy('category')->orderBy('name')->get();
            $categories = ['Appetizer', 'Main', 'Dessert', 'Beverage', 'Side'];

            return view('restaurant.cashier.pos', compact('menuItems', 'categories'));

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
     * Store Order
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
                'items' => 'required|array|min:1',
                'items.*.menu_item_id' => 'required|exists:menu_items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'tax_amount' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:cash,card,mobile_money',
                'customer_type' => 'nullable|in:dine_in,takeaway,delivery',
                'table_number' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $order = SalesOrder::create([
                'order_number' => $orderNumber,
                'cashier_id' => Auth::id(),
                'customer_type' => $request->customer_type ?? 'dine_in',
                'table_number' => $request->table_number,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount,
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
            ]);

            foreach ($request->items as $item) {
                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);

                // Deduct inventory if linked
                $menuItem = MenuItem::find($item['menu_item_id']);
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
     * View Receipt
     */
    public function getReceipt($id)
    {
        try {
            $order = SalesOrder::with('items.menuItem')->findOrFail($id);
            return view('restaurant.cashier.receipt', compact('order'));

        } catch (\Exception $e) {
            Log::error('Receipt error', ['order_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('restaurant.cashier.dashboard')->with('error', 'Order not found.');
        }
    }

    /**
     * Orders List
     */
    public function orders(Request $request)
    {
        try {
            $orders = SalesOrder::with('items.menuItem')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('restaurant.cashier.orders', compact('orders'));

        } catch (\Exception $e) {
            Log::error('Orders list error', ['error' => $e->getMessage()]);
            return redirect()->route('restaurant.cashier.dashboard')->with('error', 'Failed to load orders.');
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
                ->with('items.menuItem')
                ->get();

            $totalSales = $orders->sum('total_amount');
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
