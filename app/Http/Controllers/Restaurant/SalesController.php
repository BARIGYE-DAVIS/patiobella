<?php
// app/Http/Controllers/Restaurant/SalesController.php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\MenuItem;
use App\Models\InventoryItem;
use App\Models\DepartmentRequisitionItem;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    /**
     * Resolve the authenticated user's role name.
     * Handles both role_id (FK) and role (string column).
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
            Log::error('Error resolving role name in SalesController', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Main Sales Report Dashboard with charts
     */
    public function index(Request $request)
    {
        try {
            Log::info('Sales report dashboard accessed', ['user_id' => Auth::id()]);

            $user = Auth::user();

            // Check department
            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                Log::warning('Sales report access denied - wrong department', [
                    'user_id' => $user->id,
                    'department' => $user->department->name ?? 'none'
                ]);
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }

            // Check role - must be Manager, Admin, or Accountant
            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Manager' && $roleName !== 'Admin' && $roleName !== 'Accountant') {
                Log::warning('Sales report access denied - insufficient role', [
                    'user_id' => $user->id,
                    'role' => $roleName
                ]);
                return redirect()->route('restaurant.dashboard')
                    ->with('error', 'Unauthorized access. Manager, Admin, or Accountant access only.');
            }

            Log::info('Sales report dashboard authorized', [
                'user_id' => $user->id,
                'role' => $roleName,
                'department_id' => $user->department_id
            ]);

            // Get date range filters
            $period = $request->get('period', 'today');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            if ($startDate && $endDate) {
                $from = $startDate;
                $to = $endDate;
            } else {
                switch ($period) {
                    case 'today':
                        $from = now()->format('Y-m-d');
                        $to = now()->format('Y-m-d');
                        break;
                    case 'yesterday':
                        $from = now()->subDay()->format('Y-m-d');
                        $to = now()->subDay()->format('Y-m-d');
                        break;
                    case 'this_week':
                        $from = now()->startOfWeek()->format('Y-m-d');
                        $to = now()->endOfWeek()->format('Y-m-d');
                        break;
                    case 'this_month':
                        $from = now()->startOfMonth()->format('Y-m-d');
                        $to = now()->endOfMonth()->format('Y-m-d');
                        break;
                    case 'last_month':
                        $from = now()->subMonth()->startOfMonth()->format('Y-m-d');
                        $to = now()->subMonth()->endOfMonth()->format('Y-m-d');
                        break;
                    default:
                        $from = now()->format('Y-m-d');
                        $to = now()->format('Y-m-d');
                }
            }

            // Get sales data
            $salesData = $this->getSalesData($from, $to);
            $topProducts = $this->getTopProducts($from, $to);
            $hourlySales = $this->getHourlySales($from, $to);
            $dailyTrend = $this->getDailyTrend($from, $to);
            $paymentMethods = $this->getPaymentMethodStats($from, $to);

            // Get menu item names for categorization
            $menuItemNames = MenuItem::where('is_active', true)->pluck('name')->toArray();

            Log::info('Sales report data loaded', [
                'user_id' => Auth::id(),
                'period' => $period,
                'from' => $from,
                'to' => $to,
                'total_sales' => $salesData['total_sales'],
                'total_orders' => $salesData['total_orders']
            ]);

            return view('restaurant.sales.index', compact(
                'salesData', 'topProducts', 'hourlySales', 'dailyTrend',
                'paymentMethods', 'period', 'from', 'to', 'menuItemNames'
            ));

        } catch (\Exception $e) {
            Log::error('Sales report dashboard error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('restaurant.dashboard')->with('error', 'Failed to load sales report: ' . $e->getMessage());
        }
    }

    /**
     * POS interface for direct sales
     */
    public function pos()
    {
        try {
            Log::info('Sales POS accessed', ['user_id' => Auth::id()]);

            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Manager' && $roleName !== 'Admin' && $roleName !== 'Accountant') {
                return redirect()->route('restaurant.dashboard')
                    ->with('error', 'Unauthorized access.');
            }

            $menuItems = MenuItem::where('is_active', true)->orderBy('category')->orderBy('name')->get();
            return view('restaurant.sales.pos', compact('menuItems'));

        } catch (\Exception $e) {
            Log::error('Sales POS error', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->route('restaurant.dashboard')->with('error', 'Failed to load POS.');
        }
    }

    /**
     * Store direct sale
     */
    public function store(Request $request)
    {
        try {
            Log::info('Store sale initiated', ['user_id' => Auth::id()]);

            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Manager' && $roleName !== 'Admin' && $roleName !== 'Accountant') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.menu_item_id' => 'required|exists:menu_items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:cash,card,mobile_money',
                'customer_type' => 'nullable|in:dine_in,takeaway,delivery',
            ]);

            DB::beginTransaction();

            $orderNumber = 'SALE-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $order = SalesOrder::create([
                'order_number' => $orderNumber,
                'cashier_id' => Auth::id(),
                'customer_type' => $request->customer_type ?? 'dine_in',
                'subtotal' => $request->total_amount,
                'tax_amount' => 0,
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method,
                'amount_paid' => $request->total_amount,
                'change_amount' => 0,
                'status' => 'completed',
                'payment_status' => 'paid',
            ]);

            foreach ($request->items as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'item_name' => $menuItem->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);

                // Deduct inventory if linked
                if ($menuItem && $menuItem->inventory_item_id) {
                    $this->deductInventoryStock($menuItem->inventory_item_id, $item['quantity']);
                }
            }

            DB::commit();

            Log::info('Sale completed', [
                'user_id' => Auth::id(),
                'order_number' => $orderNumber,
                'total_amount' => $request->total_amount
            ]);

            return response()->json(['success' => true, 'order_id' => $order->id, 'order_number' => $orderNumber]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store sale failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show sale details
     */
    public function show($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }

            $order = SalesOrder::with('items')->findOrFail($id);
            return view('restaurant.sales.show', compact('order'));

        } catch (\Exception $e) {
            Log::error('Show sale error', ['order_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('restaurant.sales.index')->with('error', 'Sale not found.');
        }
    }

    /**
     * Print receipt
     */
    public function receipt($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }

            $order = SalesOrder::with('items')->findOrFail($id);
            return view('restaurant.sales.receipt', compact('order'));

        } catch (\Exception $e) {
            Log::error('Receipt error', ['order_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('restaurant.sales.index')->with('error', 'Receipt not found.');
        }
    }

    /**
     * Daily report
     */
    public function dailyReport(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }

            $date = $request->get('date', today()->toDateString());

            $orders = SalesOrder::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->with('items')
                ->get();

            $totalSales = $orders->sum('total_amount');
            $totalOrders = $orders->count();

            return view('restaurant.sales.daily-report', compact('orders', 'totalSales', 'totalOrders', 'date'));

        } catch (\Exception $e) {
            Log::error('Daily report error', ['error' => $e->getMessage()]);
            return redirect()->route('restaurant.sales.index')->with('error', 'Failed to load report.');
        }
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        return redirect()->back()->with('info', 'Excel export coming soon.');
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        return redirect()->back()->with('info', 'PDF export coming soon.');
    }

    // ==============================================
    // PRIVATE HELPER METHODS
    // ==============================================

    private function getSalesData($from, $to)
    {
        $orders = SalesOrder::whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->where('payment_status', 'paid')
            ->get();

        return [
            'total_sales' => $orders->sum('total_amount'),
            'total_orders' => $orders->count(),
            'average_order_value' => $orders->count() > 0 ? $orders->sum('total_amount') / $orders->count() : 0,
            'total_items_sold' => SalesOrderItem::whereHas('salesOrder', function($q) use ($from, $to) {
                $q->whereDate('created_at', '>=', $from)
                  ->whereDate('created_at', '<=', $to)
                  ->where('payment_status', 'paid');
            })->sum('quantity'),
            'cash_sales' => $orders->where('payment_method', 'cash')->sum('total_amount'),
            'card_sales' => $orders->where('payment_method', 'card')->sum('total_amount'),
            'mobile_sales' => $orders->where('payment_method', 'mobile_money')->sum('total_amount'),
        ];
    }

    private function getTopProducts($from, $to, $limit = 10)
    {
        return SalesOrderItem::select(
                'item_name',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->whereHas('salesOrder', function($q) use ($from, $to) {
                $q->whereDate('created_at', '>=', $from)
                  ->whereDate('created_at', '<=', $to)
                  ->where('payment_status', 'paid');
            })
            ->groupBy('item_name')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getHourlySales($from, $to)
    {
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyData[$i] = 0;
        }

        $orders = SalesOrder::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->where('payment_status', 'paid')
            ->groupBy('hour')
            ->get();

        foreach ($orders as $order) {
            $hourlyData[$order->hour] = $order->total;
        }

        return $hourlyData;
    }

    private function getDailyTrend($from, $to)
    {
        return SalesOrder::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    private function getPaymentMethodStats($from, $to)
    {
        return SalesOrder::select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->where('payment_status', 'paid')
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();
    }

    private function deductInventoryStock($inventoryItemId, $quantity)
    {
        $departmentId = Auth::user()->department_id;

        $reqItems = DepartmentRequisitionItem::with(['departmentRequisition'])
            ->whereHas('departmentRequisition', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId)
                  ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
            })
            ->where('inventory_item_id', $inventoryItemId)
            ->where('issued_total_pieces', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        $quantityToDeduct = $quantity;

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
    }
}
