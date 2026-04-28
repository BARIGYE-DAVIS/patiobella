<?php

namespace App\Http\Controllers\Management;

use Illuminate\Routing\Controller as BaseController;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceivedNote;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagementController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user->department || $user->department->name !== ' GENERAL MANAGEMENT') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        // Get statistics for dashboard
        $stats = [
            'total_pos' => PurchaseOrder::count(),
            'sent_pos' => PurchaseOrder::where('status', 'sent')->count(),
            'received_pos' => PurchaseOrder::where('status', 'received')->count(),
            'total_grns' => GoodsReceivedNote::count(),
            'total_vendors' => Vendor::count(),
            'total_po_value' => PurchaseOrder::sum('total_amount'),
            'total_grn_value' => GoodsReceivedNote::sum('total_amount'),
        ];

        // Get recent POs
        $recentPos = PurchaseOrder::with(['vendor'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent GRNs
        $recentGrns = GoodsReceivedNote::with(['vendor', 'purchaseOrder'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get monthly data for chart
        $monthlyData = $this->getMonthlyData();

        return view('management.dashboard', compact('stats', 'recentPos', 'recentGrns', 'monthlyData'));
    }

    private function getMonthlyData()
    {
        $months = [];
        $poTotals = [];
        $grnTotals = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M Y');
            
            $poTotal = PurchaseOrder::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
            
            $grnTotal = GoodsReceivedNote::whereYear('received_date', $month->year)
                ->whereMonth('received_date', $month->month)
                ->sum('total_amount');
            
            $poTotals[] = $poTotal;
            $grnTotals[] = $grnTotal;
        }

        return [
            'months' => $months,
            'po_totals' => $poTotals,
            'grn_totals' => $grnTotals
        ];
    }

    public function reportsPurchaseOrders(Request $request)
    {
        $query = PurchaseOrder::with(['vendor']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $purchaseOrders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('management.reports.purchase_orders', compact('purchaseOrders'));
    }

    public function analyticsProcurement()
    {
        // Vendor performance
        $vendorPerformance = Vendor::withCount('purchaseOrders')
            ->withSum('purchaseOrders', 'total_amount')
            ->orderBy('purchase_orders_count', 'desc')
            ->limit(10)
            ->get();

        // PO Status distribution
        $statusDistribution = PurchaseOrder::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Monthly trend
        $monthlyTrend = $this->getMonthlyData();

        return view('management.analytics.procurement', compact('vendorPerformance', 'statusDistribution', 'monthlyTrend'));
    }

    public function vendorsIndex()
    {
        $vendors = Vendor::withCount('purchaseOrders')
            ->withSum('purchaseOrders', 'total_amount')
            ->orderBy('name')
            ->paginate(20);

        return view('management.vendors.index', compact('vendors'));
    }

    public function vendorsShow($id)
    {
        $vendor = Vendor::with(['purchaseOrders' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(20);
        }])->findOrFail($id);

        return view('management.vendors.show', compact('vendor'));
    }
}