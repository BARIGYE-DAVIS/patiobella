<?php
// app/Http/Controllers/Bar/BarSalesController.php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\InventoryItem;
use App\Models\User;
use App\Models\DepartmentRequisitionItem;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BarSalesExport;

class BarSalesController extends Controller
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
     * Main Sales Dashboard
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Manager' && $roleName !== 'Admin' && $roleName !== 'BAR MANAGER') {
                return redirect()->route('bar.dashboard')->with('error', 'Manager access only.');
            }

            $departmentId = $user->department_id;

            // Get filter parameters
            $period = $request->get('period', 'this_month');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $cashierId = $request->get('cashier_id');
            $itemId = $request->get('item_id');

            // Set date range
            if ($startDate && $endDate) {
                $from = $startDate;
                $to = $endDate;
                $period = 'custom';
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
                    case 'last_week':
                        $from = now()->subWeek()->startOfWeek()->format('Y-m-d');
                        $to = now()->subWeek()->endOfWeek()->format('Y-m-d');
                        break;
                    case 'this_month':
                        $from = now()->startOfMonth()->format('Y-m-d');
                        $to = now()->endOfMonth()->format('Y-m-d');
                        break;
                    case 'last_month':
                        $from = now()->subMonth()->startOfMonth()->format('Y-m-d');
                        $to = now()->subMonth()->endOfMonth()->format('Y-m-d');
                        break;
                    case 'this_year':
                        $from = now()->startOfYear()->format('Y-m-d');
                        $to = now()->endOfYear()->format('Y-m-d');
                        break;
                    default:
                        $from = now()->startOfMonth()->format('Y-m-d');
                        $to = now()->endOfMonth()->format('Y-m-d');
                }
            }

            // Build base query
            $query = SalesOrder::with(['cashier', 'items'])
                ->where('department_id', $departmentId)
                ->where('payment_status', 'paid')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to);

            // Apply filters
            if ($cashierId) {
                $query->where('cashier_id', $cashierId);
            }

            if ($itemId) {
                $query->whereHas('items', function($q) use ($itemId) {
                    $q->where('inventory_item_id', $itemId);
                });
            }

            $orders = $query->orderBy('created_at', 'desc')->get();

            // Sales Statistics
            $stats = [
                'total_sales' => $orders->sum('total_amount'),
                'total_orders' => $orders->count(),
                'total_items' => $orders->sum(function($order) {
                    return $order->items->sum('quantity');
                }),
                'avg_order_value' => $orders->count() > 0 ? $orders->sum('total_amount') / $orders->count() : 0,
            ];

            // Daily Sales Trend
            $dailyTrend = $orders->groupBy(function($order) {
                return $order->created_at->format('Y-m-d');
            })->map(function($dayOrders) {
                return $dayOrders->sum('total_amount');
            })->sortKeys();

            // Weekly Sales Trend
            $weeklyTrend = $orders->groupBy(function($order) {
                return $order->created_at->format('Y-W');
            })->map(function($weekOrders) {
                return $weekOrders->sum('total_amount');
            })->sortKeys();

            // Monthly Sales Trend
            $monthlyTrend = $orders->groupBy(function($order) {
                return $order->created_at->format('Y-m');
            })->map(function($monthOrders) {
                return $monthOrders->sum('total_amount');
            })->sortKeys();

            // Top Selling Products
            $topProducts = SalesOrderItem::select(
                    'item_name',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(total_price) as total_revenue')
                )
                ->whereHas('salesOrder', function($q) use ($departmentId, $from, $to, $cashierId) {
                    $q->where('department_id', $departmentId)
                      ->where('payment_status', 'paid')
                      ->whereDate('created_at', '>=', $from)
                      ->whereDate('created_at', '<=', $to);
                    if ($cashierId) {
                        $q->where('cashier_id', $cashierId);
                    }
                })
                ->groupBy('item_name')
                ->orderBy('total_revenue', 'desc')
                ->limit(10)
                ->get();

            // Payment Methods Breakdown
            $paymentMethods = $orders->groupBy('payment_method')->map(function($methodOrders) {
                return [
                    'count' => $methodOrders->count(),
                    'total' => $methodOrders->sum('total_amount'),
                ];
            });

            // Best Performing Days of Week
            $dayPerformance = [];
            $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $dayRevenueData = [];
            $dayOrderData = [];

            foreach ($daysOfWeek as $index => $day) {
                $dayNumber = $index + 1;
                $dayOrders = $orders->filter(function($order) use ($dayNumber) {
                    return $order->created_at->dayOfWeek == $dayNumber;
                });
                $revenue = $dayOrders->sum('total_amount');
                $ordersCount = $dayOrders->count();

                $dayPerformance[$day] = [
                    'orders' => $ordersCount,
                    'revenue' => $revenue,
                ];

                $dayRevenueData[] = $revenue;
                $dayOrderData[] = $ordersCount;
            }

            // Best Performing Months
            $monthPerformance = $orders->groupBy(function($order) {
                return $order->created_at->format('F Y');
            })->map(function($monthOrders) {
                return [
                    'orders' => $monthOrders->count(),
                    'revenue' => $monthOrders->sum('total_amount'),
                ];
            })->sortByDesc('revenue');

            // Cashier Performance
            $cashierPerformance = $orders->groupBy('cashier_id')->map(function($cashierOrders) {
                $cashier = User::find($cashierOrders->first()->cashier_id);
                return [
                    'name' => $cashier ? ($cashier->first_name . ' ' . ($cashier->last_name ?? '')) : 'Unknown',
                    'orders' => $cashierOrders->count(),
                    'revenue' => $cashierOrders->sum('total_amount'),
                ];
            })->sortByDesc('revenue');

            // Hourly Sales Distribution
            $hourlySales = [];
            for ($i = 0; $i < 24; $i++) {
                $hourlySales[$i] = 0;
            }
            foreach ($orders as $order) {
                $hour = (int) $order->created_at->format('H');
                $hourlySales[$hour] += $order->total_amount;
            }

// Filters data - Products that belong to BAR department (from requisitions)
$products = InventoryItem::where('is_sellable', true)
    ->where('is_active', true)
    ->whereHas('departmentRequisitionItems', function($q) use ($departmentId) {
        $q->whereHas('departmentRequisition', function($sq) use ($departmentId) {
            $sq->where('department_id', $departmentId)
               ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
        })
        ->where('issued_total_pieces', '>', 0);
    })
    ->orderBy('name')
    ->get();
            // Cashiers for filter
            $cashiers = User::where('department_id', $departmentId)
                ->whereHas('role', function($q) {
                    $q->where('name', 'Cashier');
                })
                ->get();

            return view('bar.sales.index', compact(
                'stats', 'dailyTrend', 'weeklyTrend', 'monthlyTrend',
                'topProducts', 'paymentMethods', 'dayPerformance',
                'dayRevenueData', 'dayOrderData', 'daysOfWeek',
                'monthPerformance', 'cashierPerformance', 'hourlySales',
                'cashiers', 'products', 'period', 'from', 'to',
                'cashierId', 'itemId'
            ));

        } catch (\Exception $e) {
            Log::error('Bar sales index error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('bar.dashboard')->with('error', 'Failed to load sales report: ' . $e->getMessage());
        }
    }

    /**
 * Export to Excel
 */
public function exportExcel(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'BAR') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $departmentId = $user->department_id;
        $exportType = $request->get('export_type', 'current');

        if ($exportType === 'all') {
            // Export ALL sales (no date filter)
            $from = null;
            $to = null;
            $startDate = 'All Time';
            $endDate = 'All Time';

            $query = SalesOrder::with(['cashier', 'items'])
                ->where('department_id', $departmentId)
                ->where('payment_status', 'paid');
        } elseif ($exportType === 'custom') {
            // Custom date range
            $from = $request->get('start_date');
            $to = $request->get('end_date');
            $startDate = $from;
            $endDate = $to;

            $query = SalesOrder::with(['cashier', 'items'])
                ->where('department_id', $departmentId)
                ->where('payment_status', 'paid')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to);
        } else {
            // Current filtered period
            $from = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
            $to = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
            $startDate = $from;
            $endDate = $to;
            $cashierId = $request->get('cashier_id');
            $itemId = $request->get('item_id');

            $query = SalesOrder::with(['cashier', 'items'])
                ->where('department_id', $departmentId)
                ->where('payment_status', 'paid')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to);

            if ($cashierId) {
                $query->where('cashier_id', $cashierId);
            }
            if ($itemId) {
                $query->whereHas('items', function($q) use ($itemId) {
                    $q->where('inventory_item_id', $itemId);
                });
            }
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $filename = 'bar_sales_report_' . ($exportType === 'all' ? 'all_time' : $startDate . '_to_' . $endDate) . '.xlsx';
        return Excel::download(new BarSalesExport($orders, $startDate, $endDate, $exportType), $filename);

    } catch (\Exception $e) {
        Log::error('Bar sales export excel error', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
    }
}

/**
 * Export to PDF
 */
/**
 * Export to PDF
 */
public function exportPdf(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'BAR') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $departmentId = $user->department_id;
        $exportType = $request->get('export_type', 'current');

        if ($exportType === 'all') {
            // Export ALL sales (no date filter)
            $startDate = 'All Time';
            $endDate = 'All Time';

            $orders = SalesOrder::with(['cashier', 'items'])
                ->where('department_id', $departmentId)
                ->where('payment_status', 'paid')
                ->orderBy('created_at', 'desc')
                ->get();

        } elseif ($exportType === 'custom') {
            // Custom date range
            $from = $request->get('start_date');
            $to = $request->get('end_date');
            $startDate = $from;
            $endDate = $to;

            $orders = SalesOrder::with(['cashier', 'items'])
                ->where('department_id', $departmentId)
                ->where('payment_status', 'paid')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
                ->orderBy('created_at', 'desc')
                ->get();

        } else {
            // Current filtered period
            $from = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
            $to = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
            $startDate = $from;
            $endDate = $to;
            $cashierId = $request->get('cashier_id');
            $itemId = $request->get('item_id');

            $query = SalesOrder::with(['cashier', 'items'])
                ->where('department_id', $departmentId)
                ->where('payment_status', 'paid')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to);

            if ($cashierId) {
                $query->where('cashier_id', $cashierId);
            }
            if ($itemId) {
                $query->whereHas('items', function($q) use ($itemId) {
                    $q->where('inventory_item_id', $itemId);
                });
            }

            $orders = $query->orderBy('created_at', 'desc')->get();
        }

        $stats = [
            'total_sales' => $orders->sum('total_amount'),
            'total_orders' => $orders->count(),
            'total_items' => $orders->sum(function($order) {
                return $order->items->sum('quantity');
            }),
            'avg_order_value' => $orders->count() > 0 ? $orders->sum('total_amount') / $orders->count() : 0,
        ];

        $pdf = Pdf::loadView('bar.sales.export-pdf', compact('orders', 'stats', 'startDate', 'endDate', 'exportType'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'bar_sales_report_' . ($exportType === 'all' ? 'all_time' : $startDate . '_to_' . $endDate) . '.pdf';
        return $pdf->download($filename);

    } catch (\Exception $e) {
        Log::error('Bar sales export pdf error', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
    }
}
}
