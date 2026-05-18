<?php
// app/Http/Controllers/Bar/BarCashierController.php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\DepartmentRequisitionItem;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BarCashierSalesExport;

class BarCashierController extends Controller
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
     * Bar Cashier Dashboard
     */
    public function dashboard()
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Bar Cashier' && $roleName !== 'Cashier') {
                return redirect()->route('bar.dashboard')->with('error', 'Cashier access only.');
            }

            $cashierId = Auth::id();
            $departmentId = $user->department_id;

            // Today's sales for THIS CASHIER ONLY
            $todaySales = SalesOrder::whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->where('cashier_id', $cashierId)
                ->sum('total_amount');

            // Today's orders for THIS CASHIER ONLY
            $todayOrders = SalesOrder::whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->where('cashier_id', $cashierId)
                ->count();

            // Unpaid orders for THIS CASHIER ONLY
            $unpaidOrders = SalesOrder::where('payment_status', 'unpaid')
                ->where('cashier_id', $cashierId)
                ->count();

            // Calculate low stock count for the bar department
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

            // Recent orders for THIS CASHIER ONLY
            $recentOrders = SalesOrder::with('items')
                ->where('cashier_id', $cashierId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('bar.cashier.dashboard', compact(
                'todaySales',
                'todayOrders',
                'unpaidOrders',
                'lowStockCount',
                'recentOrders'
            ));

        } catch (\Exception $e) {
            Log::error('Bar cashier dashboard error', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->route('bar.dashboard')->with('error', 'Failed to load dashboard.');
        }
    }

    /**
     * Cashiers Management Index (Manager view all cashiers)
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Manager' && $roleName !== 'Admin') {
                return redirect()->route('bar.dashboard')->with('error', 'Manager access only.');
            }

            $cashiers = \App\Models\User::with(['role', 'department'])
                ->where('department_id', $user->department_id)
                ->whereHas('role', function($q) {
                    $q->where('name', 'Cashier');
                })
                ->orderBy('first_name')
                ->paginate(20);

            return view('bar.cashiers.index', compact('cashiers'));

        } catch (\Exception $e) {
            Log::error('Bar cashiers index error', ['error' => $e->getMessage()]);
            return redirect()->route('bar.dashboard')->with('error', 'Failed to load cashiers.');
        }
    }

    /**
     * Cashier Sales Report (Manager view specific cashier sales)
     */
    public function sales($id, Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Manager' && $roleName !== 'Admin') {
                return redirect()->route('bar.dashboard')->with('error', 'Manager access only.');
            }

            $cashier = \App\Models\User::findOrFail($id);

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
                    case 'this_week':
                        $from = now()->startOfWeek()->format('Y-m-d');
                        $to = now()->endOfWeek()->format('Y-m-d');
                        break;
                    case 'this_month':
                        $from = now()->startOfMonth()->format('Y-m-d');
                        $to = now()->endOfMonth()->format('Y-m-d');
                        break;
                    default:
                        $from = now()->format('Y-m-d');
                        $to = now()->format('Y-m-d');
                }
            }

            $orders = SalesOrder::with('items')
                ->where('cashier_id', $id)
                ->where('payment_status', 'paid')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->orderBy('created_at', 'desc')
                ->get();

            $totalSales = $orders->sum('total_amount');
            $totalOrders = $orders->count();
            $totalItems = $orders->sum(function($order) {
                return $order->items->sum('quantity');
            });

            return view('bar.cashiers.sales', compact('cashier', 'orders', 'totalSales', 'totalOrders', 'totalItems', 'period', 'from', 'to'));

        } catch (\Exception $e) {
            Log::error('Bar cashier sales error', ['error' => $e->getMessage()]);
            return redirect()->route('bar.cashiers.index')->with('error', 'Failed to load cashier sales.');
        }
    }

    /**
     * Cashier My Sales (Cashier sees their own sales)
     */
    public function mySales(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Bar Cashier' && $roleName !== 'Cashier') {
                return redirect()->route('bar.dashboard')->with('error', 'Cashier access only.');
            }

            $period = $request->get('period', 'today');
            $search = $request->get('search', '');
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
                    default:
                        $from = now()->format('Y-m-d');
                        $to = now()->format('Y-m-d');
                }
            }

            $query = SalesOrder::with(['items'])
                ->where('cashier_id', Auth::id())
                ->where('payment_status', 'paid')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhereHas('items', function($sq) use ($search) {
                          $sq->where('item_name', 'like', "%{$search}%");
                      });
                });
            }

            $orders = $query->orderBy('created_at', 'desc')->paginate(15);

            $stats = [
                'total_sales' => SalesOrder::where('cashier_id', Auth::id())
                    ->where('payment_status', 'paid')
                    ->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to)
                    ->sum('total_amount'),
                'total_orders' => SalesOrder::where('cashier_id', Auth::id())
                    ->where('payment_status', 'paid')
                    ->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to)
                    ->count(),
                'total_items' => SalesOrderItem::whereHas('salesOrder', function($q) use ($from, $to) {
                    $q->where('cashier_id', Auth::id())
                      ->where('payment_status', 'paid')
                      ->whereDate('created_at', '>=', $from)
                      ->whereDate('created_at', '<=', $to);
                })->sum('quantity'),
            ];

            $stats['avg_order'] = $stats['total_orders'] > 0 ? $stats['total_sales'] / $stats['total_orders'] : 0;

            session(['bar_cashier_sales_filters' => [
                'from' => $from,
                'to' => $to,
                'search' => $search
            ]]);

            if ($request->ajax()) {
                $html = view('bar.cashier._my_sales_rows', compact('orders'))->render();
                return response()->json([
                    'html' => $html,
                    'pagination' => $orders->links()->toHtml(),
                    'stats' => $stats
                ]);
            }

            return view('bar.cashier.my-sales', compact('orders', 'stats', 'period', 'from', 'to', 'search'));

        } catch (\Exception $e) {
            Log::error('Bar my sales error', ['error' => $e->getMessage()]);
            return redirect()->route('bar.cashier.dashboard')->with('error', 'Failed to load sales report.');
        }
    }


    /**
     * Reports Index
     */
    public function reports()
    {
        return view('bar.cashier.reports');
    }


    /**
 * Monthly Report
 */
public function monthlyReport(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'BAR') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $month = $request->get('month', now()->format('Y-m'));
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $orders = SalesOrder::with('items')
            ->where('cashier_id', Auth::id())
            ->where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSales = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $totalItems = $orders->sum(function($order) {
            return $order->items->sum('quantity');
        });
        $avgOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        if ($request->ajax()) {
            return view('bar.cashier._monthly_report_content', compact('orders', 'totalSales', 'totalOrders', 'totalItems', 'avgOrder', 'month'));
        }

        return view('bar.cashier.monthly-report', compact('orders', 'totalSales', 'totalOrders', 'totalItems', 'avgOrder', 'month'));

    } catch (\Exception $e) {
        Log::error('Bar monthly report error', ['error' => $e->getMessage()]);
        if ($request->ajax()) {
            return response('<div class="text-center py-8 text-red-500">Failed to load report</div>', 500);
        }
        return redirect()->route('bar.cashier.reports')->with('error', 'Failed to load report.');
    }
}

/**
 * Export My Sales to Excel
 */
public function exportExcel(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'BAR') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $exportType = $request->get('export_type', 'current');

        if ($exportType === 'all') {
            // Export ALL sales (no date filter)
            $startDate = 'All Time';
            $endDate = 'All Time';

            $orders = SalesOrder::with(['items', 'cashier'])
                ->where('cashier_id', Auth::id())
                ->where('payment_status', 'paid')
                ->orderBy('created_at', 'desc')
                ->get();

            $filename = 'bar_my_sales_all_time.xlsx';
            return Excel::download(new BarCashierSalesExport($orders, $startDate, $endDate, $exportType), $filename);

        } elseif ($exportType === 'custom') {
            // Custom date range
            $from = $request->get('start_date');
            $to = $request->get('end_date');
            $startDate = $from;
            $endDate = $to;

            $orders = SalesOrder::with(['items', 'cashier'])
                ->where('cashier_id', Auth::id())
                ->where('payment_status', 'paid')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->orderBy('created_at', 'desc')
                ->get();

            $filename = 'bar_my_sales_' . $from . '_to_' . $to . '.xlsx';
            return Excel::download(new BarCashierSalesExport($orders, $startDate, $endDate, $exportType), $filename);

        } else {
            // Current filtered period (from the session)
            $filters = session('bar_cashier_sales_filters', [
                'from' => now()->startOfMonth()->format('Y-m-d'),
                'to' => now()->format('Y-m-d'),
                'search' => ''
            ]);
            $from = $filters['from'];
            $to = $filters['to'];
            $startDate = $from;
            $endDate = $to;

            $orders = SalesOrder::with(['items', 'cashier'])
                ->where('cashier_id', Auth::id())
                ->where('payment_status', 'paid')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->when($filters['search'], function($q) use ($filters) {
                    $q->where('order_number', 'like', "%{$filters['search']}%");
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $filename = 'bar_my_sales_' . $from . '_to_' . $to . '.xlsx';
            return Excel::download(new BarCashierSalesExport($orders, $startDate, $endDate, $exportType), $filename);
        }

    } catch (\Exception $e) {
        Log::error('Bar export excel error', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
    }
}

/**
 * Export My Sales to PDF
 */
public function exportPdf(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'BAR') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $exportType = $request->get('export_type', 'current');

        if ($exportType === 'all') {
            $startDate = 'All Time';
            $endDate = 'All Time';

            $orders = SalesOrder::with(['items', 'cashier'])
                ->where('cashier_id', Auth::id())
                ->where('payment_status', 'paid')
                ->orderBy('created_at', 'desc')
                ->get();

        } elseif ($exportType === 'custom') {
            $from = $request->get('start_date');
            $to = $request->get('end_date');
            $startDate = $from;
            $endDate = $to;

            $orders = SalesOrder::with(['items', 'cashier'])
                ->where('cashier_id', Auth::id())
                ->where('payment_status', 'paid')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->orderBy('created_at', 'desc')
                ->get();

        } else {
            $filters = session('bar_cashier_sales_filters', [
                'from' => now()->startOfMonth()->format('Y-m-d'),
                'to' => now()->format('Y-m-d'),
                'search' => ''
            ]);
            $from = $filters['from'];
            $to = $filters['to'];
            $startDate = $from;
            $endDate = $to;

            $orders = SalesOrder::with(['items', 'cashier'])
                ->where('cashier_id', Auth::id())
                ->where('payment_status', 'paid')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->when($filters['search'], function($q) use ($filters) {
                    $q->where('order_number', 'like', "%{$filters['search']}%");
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $stats = [
            'total_sales' => $orders->sum('total_amount'),
            'total_orders' => $orders->count(),
            'total_items' => $orders->sum(function($order) {
                return $order->items->sum('quantity');
            }),
            'avg_order_value' => $orders->count() > 0 ? $orders->sum('total_amount') / $orders->count() : 0,
        ];

        $pdf = Pdf::loadView('bar.cashier.my-sales-pdf', compact('orders', 'stats', 'startDate', 'endDate', 'exportType'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'bar_my_sales_' . ($exportType === 'all' ? 'all_time' : $startDate . '_to_' . $endDate) . '.pdf';
        return $pdf->download($filename);

    } catch (\Exception $e) {
        Log::error('Bar export pdf error', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
    }
}



}
