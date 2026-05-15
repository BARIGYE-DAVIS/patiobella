<?php
// app/Http/Controllers/Restaurant/CashierController.php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\DepartmentRequisitionItem;
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
        $unpaidOrders = SalesOrder::where('payment_status', 'unpaid')->count();

        // Calculate low stock count
        $departmentId = $user->department_id;
        $lowStockCount = 0;

        $stockItems = DepartmentRequisitionItem::with(['inventoryItem'])
            ->whereHas('departmentRequisition', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId)
                  ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
            })
            ->where('issued_total_pieces', '>', 0)
            ->get();

        foreach ($stockItems as $item) {
            $issued = (float) ($item->issued_total_pieces ?? 0);
            $consumed = (float) ($item->quantity_consumed ?? 0);
            $returned = (float) ($item->returned_total_pieces ?? 0);
            $sold = (float) ($item->quantity_sold ?? 0);
            $remaining = $issued - ($consumed + $returned + $sold);
            if ($remaining > 0 && $remaining < 10) {
                $lowStockCount++;
            }
        }

        $recentOrders = SalesOrder::with('items.menuItem')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('restaurant.cashier.dashboard', compact(
            'todaySales',
            'todayOrders',
            'unpaidOrders',
            'lowStockCount',
            'recentOrders'
        ));

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
     * Orders List - Show all orders with unpaid/paid filter
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
                ->when($status === 'unpaid', function($q) {
                    $q->where('payment_status', 'unpaid');
                })
                ->when($status === 'paid', function($q) {
                    $q->where('payment_status', 'paid');
                })
                ->when($search, function($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            if ($request->ajax()) {
                $html = view('restaurant.cashier._orders_rows', compact('orders'))->render();
                $pagination = $orders->appends(['status' => $status, 'search' => $search])->links()->toHtml();

                return response()->json([
                    'html' => $html,
                    'pagination' => $pagination,
                    'unpaidCount' => SalesOrder::where('payment_status', 'unpaid')->count(),
                    'paidCount' => SalesOrder::where('payment_status', 'paid')->count(),
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
     * Show a single order (invoice if unpaid, receipt if paid)
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

            if ($order->payment_status !== 'paid') {
                return view('restaurant.cashier.invoice', compact('order'));
            } else {
                return view('restaurant.cashier.receipt', compact('order'));
            }

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
            ]);

            DB::beginTransaction();

            $order = SalesOrder::findOrFail($id);

            if ($order->payment_status === 'paid') {
                return response()->json(['success' => false, 'message' => 'Order already paid'], 400);
            }

            $order->payment_method = $request->payment_method;
            $order->status = 'completed';
            $order->payment_status = 'paid';
            $order->save();

            DB::commit();

            Log::info('Order marked as paid', [
                'cashier_id' => Auth::id(),
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_method' => $request->payment_method
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'message' => 'Payment successful'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment failed', ['order_id' => $id, 'error' => $e->getMessage()]);
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
