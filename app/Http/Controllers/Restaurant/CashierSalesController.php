<?php
// app/Http/Controllers/Restaurant/CashierSalesController.php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CashierSalesExport;

class CashierSalesController extends Controller
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
            return null;
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'CAFE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $roleName = $this->getRoleName($user);
        if ($roleName !== 'Cashier') {
            return redirect()->route('restaurant.dashboard')->with('error', 'Cashier access only.');
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
                case 'last_month':
                    $from = now()->subMonth()->startOfMonth()->format('Y-m-d');
                    $to = now()->subMonth()->endOfMonth()->format('Y-m-d');
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

        // Stats
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

        // Store filters for export
        session(['cashier_sales_filters' => [
            'from' => $from,
            'to' => $to,
            'search' => $search
        ]]);

        if ($request->ajax()) {
            $html = view('restaurant.cashier._my_sales_rows', compact('orders'))->render();
            return response()->json([
                'html' => $html,
                'pagination' => $orders->links()->toHtml(),
                'stats' => $stats
            ]);
        }

        return view('restaurant.cashier.my-sales', compact('orders', 'stats', 'period', 'from', 'to', 'search'));
    }

    public function exportExcel()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'CAFE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $filters = session('cashier_sales_filters', [
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
            'search' => ''
        ]);

        $orders = SalesOrder::with(['items'])
            ->where('cashier_id', Auth::id())
            ->where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $filters['from'])
            ->whereDate('created_at', '<=', $filters['to'])
            ->when($filters['search'], function($q) use ($filters) {
                $q->where('order_number', 'like', "%{$filters['search']}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'my_sales_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new CashierSalesExport($orders), $filename);
    }

    public function exportPdf()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'CAFE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $filters = session('cashier_sales_filters', [
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
            'search' => ''
        ]);

        $orders = SalesOrder::with(['items'])
            ->where('cashier_id', Auth::id())
            ->where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $filters['from'])
            ->whereDate('created_at', '<=', $filters['to'])
            ->when($filters['search'], function($q) use ($filters) {
                $q->where('order_number', 'like', "%{$filters['search']}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_sales' => $orders->sum('total_amount'),
            'total_orders' => $orders->count(),
            'total_items' => $orders->sum(function($order) {
                return $order->items->sum('quantity');
            }),
        ];
        $stats['avg_order'] = $stats['total_orders'] > 0 ? $stats['total_sales'] / $stats['total_orders'] : 0;

        $pdf = Pdf::loadView('restaurant.cashier.my-sales-pdf', compact('orders', 'stats', 'filters'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('my_sales_' . date('Y-m-d_His') . '.pdf');
    }
}
