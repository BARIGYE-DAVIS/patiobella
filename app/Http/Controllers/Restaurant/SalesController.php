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
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesController extends Controller
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
            Log::error('Error resolving role name in SalesController', [
                'user_id' => $user->id ?? null,
                'error'   => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Resolve date range from request.
     * Default period is 'all_time' — shows every sale ever recorded.
     */
    private function resolveDateRange(Request $request): array
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $period    = $request->get('period', 'all_time');

        if ($startDate && $endDate) {
            return [$startDate, $endDate, 'custom'];
        }

        switch ($period) {
            case 'today':
                $from = now()->format('Y-m-d');
                $to   = now()->format('Y-m-d');
                break;
            case 'yesterday':
                $from = now()->subDay()->format('Y-m-d');
                $to   = now()->subDay()->format('Y-m-d');
                break;
            case 'this_week':
                $from = now()->startOfWeek()->format('Y-m-d');
                $to   = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $from = now()->startOfMonth()->format('Y-m-d');
                $to   = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $from = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $to   = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'all_time':
            default:
                // Earliest sale date → today
                $earliest = SalesOrder::where('payment_status', 'paid')->min('created_at');
                $from = $earliest
                    ? \Carbon\Carbon::parse($earliest)->format('Y-m-d')
                    : now()->format('Y-m-d');
                $to   = now()->format('Y-m-d');
                $period = 'all_time';
                break;
        }

        return [$from, $to, $period];
    }

    /**
     * Authorization check — reused by every action.
     */
    private function authorize($user): ?string
    {
        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return 'wrong_department';
        }
        $role = $this->getRoleName($user);
        if (!in_array($role, ['Manager', 'Admin', 'Accountant'])) {
            return 'insufficient_role';
        }
        return null;
    }

    // =========================================================
    // MAIN DASHBOARD
    // =========================================================

    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            $authError = $this->authorize($user);
            if ($authError === 'wrong_department') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }
            if ($authError === 'insufficient_role') {
                return redirect()->route('restaurant.dashboard')
                    ->with('error', 'Unauthorized access. Manager, Admin, or Accountant only.');
            }

            [$from, $to, $period] = $this->resolveDateRange($request);

            $salesData      = $this->getSalesData($from, $to);
            $topProducts    = $this->getTopProducts($from, $to);
            $hourlySales    = $this->getHourlySales($from, $to);
            $dailyTrend     = $this->getDailyTrend($from, $to);
            $paymentMethods = $this->getPaymentMethodStats($from, $to);
            $menuItemNames  = MenuItem::where('is_active', true)->pluck('name')->toArray();

            // Fetch sales list here so it's available everywhere in the view
            $salesList = SalesOrder::with('items')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->where('payment_status', 'paid')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Sales dashboard loaded', [
                'user_id'      => $user->id,
                'period'       => $period,
                'from'         => $from,
                'to'           => $to,
                'total_orders' => $salesList->count(),
            ]);

            return view('restaurant.sales.index', compact(
                'salesData', 'topProducts', 'hourlySales', 'dailyTrend',
                'paymentMethods', 'period', 'from', 'to', 'menuItemNames', 'salesList'
            ));

        } catch (\Exception $e) {
            Log::error('Sales dashboard error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('restaurant.dashboard')
                ->with('error', 'Failed to load sales report: ' . $e->getMessage());
        }
    }

    // =========================================================
    // EXPORT — EXCEL
    // =========================================================

    public function exportExcel(Request $request)
    {
        try {
            $user = Auth::user();
            if ($this->authorize($user)) {
                return redirect()->back()->with('error', 'Unauthorized');
            }

            [$from, $to, $period] = $this->resolveDateRange($request);

            $filename = 'sales_report_' . $from . '_to_' . $to . '.xlsx';

            return Excel::download(
                new \App\Exports\SalesReportExport($from, $to),
                $filename
            );

        } catch (\Exception $e) {
            Log::error('Excel export error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Excel export failed: ' . $e->getMessage());
        }
    }

    // =========================================================
    // EXPORT — PDF
    // =========================================================

    public function exportPdf(Request $request)
    {
        try {
            $user = Auth::user();
            if ($this->authorize($user)) {
                return redirect()->back()->with('error', 'Unauthorized');
            }

            [$from, $to, $period] = $this->resolveDateRange($request);

            $salesData   = $this->getSalesData($from, $to);
            $topProducts = $this->getTopProducts($from, $to, 20);
            $salesList   = SalesOrder::with('items')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->where('payment_status', 'paid')
                ->orderBy('created_at', 'desc')
                ->get();

            $pdf = Pdf::loadView('restaurant.sales.export-pdf', compact(
                'salesData', 'topProducts', 'salesList', 'from', 'to'
            ))->setPaper('a4', 'landscape');

            $filename = 'sales_report_' . $from . '_to_' . $to . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('PDF export error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'PDF export failed: ' . $e->getMessage());
        }
    }

    // =========================================================
    // POS
    // =========================================================

    public function pos()
    {
        try {
            $user = Auth::user();
            if ($this->authorize($user)) {
                return redirect()->route('restaurant.dashboard')->with('error', 'Unauthorized access.');
            }
            $menuItems = MenuItem::where('is_active', true)->orderBy('category')->orderBy('name')->get();
            return view('restaurant.sales.pos', compact('menuItems'));
        } catch (\Exception $e) {
            Log::error('POS error', ['error' => $e->getMessage()]);
            return redirect()->route('restaurant.dashboard')->with('error', 'Failed to load POS.');
        }
    }

    // =========================================================
    // STORE SALE
    // =========================================================

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if ($this->authorize($user)) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'items'                  => 'required|array|min:1',
                'items.*.menu_item_id'   => 'required|exists:menu_items,id',
                'items.*.quantity'       => 'required|integer|min:1',
                'items.*.unit_price'     => 'required|numeric|min:0',
                'total_amount'           => 'required|numeric|min:0',
                'payment_method'         => 'required|in:cash,card,mobile_money',
                'customer_type'          => 'nullable|in:dine_in,takeaway,delivery',
            ]);

            DB::beginTransaction();

            $orderNumber = 'SALE-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $order = SalesOrder::create([
                'order_number'   => $orderNumber,
                'cashier_id'     => Auth::id(),
                'customer_type'  => $request->customer_type ?? 'dine_in',
                'subtotal'       => $request->total_amount,
                'tax_amount'     => 0,
                'total_amount'   => $request->total_amount,
                'payment_method' => $request->payment_method,
                'amount_paid'    => $request->total_amount,
                'change_amount'  => 0,
                'status'         => 'completed',
                'payment_status' => 'paid',
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
                    $this->deductInventoryStock($menuItem->inventory_item_id, $item['quantity']);
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'order_id' => $order->id, 'order_number' => $orderNumber]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store sale failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =========================================================
    // SHOW / RECEIPT / DAILY REPORT
    // =========================================================

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
            return redirect()->route('restaurant.sales.index')->with('error', 'Sale not found.');
        }
    }

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
            return redirect()->route('restaurant.sales.index')->with('error', 'Receipt not found.');
        }
    }

    public function dailyReport(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }
            $date   = $request->get('date', today()->toDateString());
            $orders = SalesOrder::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->with('items')
                ->get();

            return view('restaurant.sales.daily-report', [
                'orders'      => $orders,
                'totalSales'  => $orders->sum('total_amount'),
                'totalOrders' => $orders->count(),
                'date'        => $date,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('restaurant.sales.index')->with('error', 'Failed to load report.');
        }
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    private function getSalesData($from, $to): array
    {
        $orders = SalesOrder::whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->where('payment_status', 'paid')
            ->get();

        return [
            'total_sales'         => $orders->sum('total_amount'),
            'total_orders'        => $orders->count(),
            'average_order_value' => $orders->count() > 0
                ? $orders->sum('total_amount') / $orders->count()
                : 0,
            'total_items_sold'    => SalesOrderItem::whereHas('salesOrder', fn($q) =>
                $q->whereDate('created_at', '>=', $from)
                  ->whereDate('created_at', '<=', $to)
                  ->where('payment_status', 'paid')
            )->sum('quantity'),
            'cash_sales'          => $orders->where('payment_method', 'cash')->sum('total_amount'),
            'card_sales'          => $orders->where('payment_method', 'card')->sum('total_amount'),
            'mobile_sales'        => $orders->where('payment_method', 'mobile_money')->sum('total_amount'),
        ];
    }

    private function getTopProducts($from, $to, $limit = 10)
    {
        return SalesOrderItem::select(
                'item_name',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->whereHas('salesOrder', fn($q) =>
                $q->whereDate('created_at', '>=', $from)
                  ->whereDate('created_at', '<=', $to)
                  ->where('payment_status', 'paid')
            )
            ->groupBy('item_name')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getHourlySales($from, $to): array
    {
        $hourlyData = array_fill(0, 24, 0);

        SalesOrder::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->where('payment_status', 'paid')
            ->groupBy('hour')
            ->get()
            ->each(fn($row) => $hourlyData[$row->hour] = (float) $row->total);

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

    private function deductInventoryStock($inventoryItemId, $quantity): void
    {
        $departmentId = Auth::user()->department_id;

        $reqItems = DepartmentRequisitionItem::with('departmentRequisition')
            ->whereHas('departmentRequisition', fn($q) =>
                $q->where('department_id', $departmentId)
                  ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned'])
            )
            ->where('inventory_item_id', $inventoryItemId)
            ->where('issued_total_pieces', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        $remaining = $quantity;

        foreach ($reqItems as $item) {
            if ($remaining <= 0) break;

            $available = (float) $item->issued_total_pieces
                - ((float) $item->quantity_consumed + (float) $item->returned_total_pieces + (float) $item->quantity_sold);

            if ($available <= 0) continue;

            $deduct = min($remaining, $available);
            $item->quantity_sold  = (float) $item->quantity_sold + $deduct;
            $item->last_sold_at   = now();
            $item->save();

            $remaining -= $deduct;
        }
    }
}
