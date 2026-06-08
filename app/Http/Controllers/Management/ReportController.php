<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Batch;
use App\Models\Department;
use App\Models\DepartmentStockMovement;
use App\Models\PerformanceReport;
use App\Models\StockMovement;
use App\Models\Category;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;

class ReportController extends Controller
{
    private function checkAuthorization()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return false;
        }

        $role = Role::find($user->role_id);
        $roleName = $role ? $role->name : ($user->role ?? null);

        if ($roleName !== 'General Manager' && $roleName !== 'Admin') {
            return false;
        }

        return true;
    }

    public function index(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $departments = Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE', 'RESTAURANT'])->get();
        $categories = Category::where('is_active', true)->get();

        $reportType = $request->get('report_type', 'dashboard');
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        return view('management.reports.index', compact('departments', 'categories', 'reportType', 'dateFrom', 'dateTo'));
    }

    public function getData(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $reportType = $request->get('report_type', 'dashboard');
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $departmentId = $request->get('department_id');

        $data = [];

        switch ($reportType) {
            case 'dashboard':
                $data = $this->getDashboardData($dateFrom, $dateTo);
                break;
            case 'top_moving_items':
                $data = $this->getTopMovingItems($dateFrom, $dateTo);
                break;
            case 'low_stock':
                $data = $this->getLowStockReport();
                break;
            case 'cogs_vs_sales':
                $data = $this->getCogsVsSales($dateFrom, $dateTo);
                break;
            case 'performance':
                $data = $this->getPerformanceReport($departmentId, $dateFrom, $dateTo);
                break;
            case 'stock_trends':
                $data = $this->getStockTrends($dateFrom, $dateTo);
                break;
            default:
                $data = $this->getDashboardData($dateFrom, $dateTo);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * MAIN DASHBOARD - All key metrics in one place
     */
    private function getDashboardData($dateFrom, $dateTo)
    {
        // Top Moving Items
        $topMovingItems = $this->getTopMovingItems($dateFrom, $dateTo);

        // Low Stock Items
        $lowStockItems = $this->getLowStockReport();

        // COGS vs Sales vs Profit
        $cogsVsSales = $this->getCogsVsSales($dateFrom, $dateTo);

        // Performance by Department
        $performance = $this->getPerformanceReport(null, $dateFrom, $dateTo);

        // Stock Trends
        $stockTrends = $this->getStockTrends($dateFrom, $dateTo);

        // Summary Cards
        $summary = [
            'total_sales' => $cogsVsSales['total_sales'],
            'total_cogs' => $cogsVsSales['total_cogs'],
            'total_profit' => $cogsVsSales['total_profit'],
            'profit_margin' => $cogsVsSales['total_sales'] > 0 ? ($cogsVsSales['total_profit'] / $cogsVsSales['total_sales']) * 100 : 0,
        ];

        // Stock Value
        $stockValue = Batch::where('batch_status', 'active')->sum(DB::raw('remaining_quantity * unit_cost'));
        $summary['stock_value'] = $stockValue;

        // Department Performance for Pie Chart
        $deptPerformance = [];
        foreach ($performance['departments'] as $dept) {
            $deptPerformance[] = [
                'name' => $dept['name'],
                'profit' => $dept['profit'],
            ];
        }

        return [
            'summary' => $summary,
            'top_moving_items' => $topMovingItems['items'],
            'low_stock_items' => $lowStockItems['items'],
            'cogs_vs_sales' => $cogsVsSales,
            'performance_by_department' => $deptPerformance,
            'stock_trends' => $stockTrends,
            'weekly_trend' => $cogsVsSales['weekly_trend'],
        ];
    }

    /**
     * 1. TOP MOVING ITEMS (Most used ingredients)
     */
    private function getTopMovingItems($dateFrom, $dateTo)
    {
        $items = DB::table('department_stock_movements')
            ->select(
                'inventory_items.id',
                'inventory_items.name',
                'inventory_items.item_code',
                'inventory_items.unit_of_measurement',
                DB::raw('SUM(used_quantity) as total_used')
            )
            ->join('inventory_items', 'department_stock_movements.inventory_item_id', '=', 'inventory_items.id')
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->where('used_quantity', '>', 0)
            ->groupBy('inventory_items.id', 'inventory_items.name', 'inventory_items.item_code', 'inventory_items.unit_of_measurement')
            ->orderBy('total_used', 'desc')
            ->limit(20)
            ->get();

        return ['items' => $items];
    }

    /**
     * 2. LOW STOCK ITEMS
     */
    private function getLowStockReport()
    {
        $items = DB::table('inventory_items')
            ->select(
                'inventory_items.id',
                'inventory_items.name',
                'inventory_items.item_code',
                'categories.name as category',
                'inventory_items.unit_of_measurement',
                'inventory_items.minimum_stock',
                DB::raw('(SELECT COALESCE(SUM(remaining_quantity), 0) FROM batches WHERE batches.inventory_item_id = inventory_items.id AND batches.batch_status = "active") as current_stock')
            )
            ->leftJoin('categories', 'inventory_items.category_id', '=', 'categories.id')
            ->where('inventory_items.is_active', true)
            ->where('inventory_items.minimum_stock', '>', 0)
            ->havingRaw('current_stock <= minimum_stock')
            ->orderByRaw('(current_stock / minimum_stock) ASC')
            ->limit(20)
            ->get();

        return ['items' => $items];
    }

    /**
     * 3. COGS vs SALES vs PROFIT with trends
     */
    private function getCogsVsSales($dateFrom, $dateTo)
    {
        // Get performance reports data
        $reports = PerformanceReport::whereBetween('report_date', [$dateFrom, $dateTo])
            ->get();

        $totalSales = $reports->sum('total_sales');
        $totalCogs = $reports->sum('total_cogs');
        $totalProfit = $totalSales - $totalCogs;

        // Weekly trend data
        $weeklyTrend = [];
        $startDate = \Carbon\Carbon::parse($dateFrom);
        $endDate = \Carbon\Carbon::parse($dateTo);

        for ($date = $startDate->copy(); $date <= $endDate; $date->addWeek()) {
            $weekStart = $date->copy()->startOfWeek()->toDateString();
            $weekEnd = $date->copy()->endOfWeek()->toDateString();

            $weekReports = PerformanceReport::whereBetween('report_date', [$weekStart, $weekEnd])->get();

            $weekSales = $weekReports->sum('total_sales');
            $weekCogs = $weekReports->sum('total_cogs');

            $weeklyTrend[] = [
                'week' => 'Week of ' . $weekStart,
                'sales' => $weekSales,
                'cogs' => $weekCogs,
                'profit' => $weekSales - $weekCogs,
            ];
        }

        // Daily trend for charts (last 14 days)
        $dailyTrend = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = \Carbon\Carbon::now()->subDays($i)->toDateString();
            $dayReports = PerformanceReport::whereDate('report_date', $day)->get();

            $daySales = $dayReports->sum('total_sales');
            $dayCogs = $dayReports->sum('total_cogs');

            $dailyTrend[] = [
                'date' => $day,
                'sales' => $daySales,
                'cogs' => $dayCogs,
                'profit' => $daySales - $dayCogs,
            ];
        }

        return [
            'total_sales' => $totalSales,
            'total_cogs' => $totalCogs,
            'total_profit' => $totalProfit,
            'profit_margin' => $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0,
            'weekly_trend' => $weeklyTrend,
            'daily_trend' => $dailyTrend,
        ];
    }

    /**
     * 4. PERFORMANCE REPORTS BY DEPARTMENT (Kitchen, Bar, Cafe)
     */
    private function getPerformanceReport($departmentId, $dateFrom, $dateTo)
    {
        $query = PerformanceReport::with('department')
            ->whereBetween('report_date', [$dateFrom, $dateTo]);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $reports = $query->orderBy('report_date', 'desc')->get();

        // Summary by department
        $departments = Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE', 'RESTAURANT'])->get();
        $deptSummary = [];

        foreach ($departments as $dept) {
            $deptReports = $reports->where('department_id', $dept->id);
            $deptSales = $deptReports->sum('total_sales');
            $deptCogs = $deptReports->sum('total_cogs');
            $deptProfit = $deptSales - $deptCogs;

            $deptSummary[] = [
                'id' => $dept->id,
                'name' => $dept->name,
                'sales' => $deptSales,
                'cogs' => $deptCogs,
                'profit' => $deptProfit,
                'margin' => $deptSales > 0 ? ($deptProfit / $deptSales) * 100 : 0,
                'report_count' => $deptReports->count(),
            ];
        }

        $totalSales = $reports->sum('total_sales');
        $totalCogs = $reports->sum('total_cogs');
        $totalProfit = $totalSales - $totalCogs;

        // Department trend data for charts (last 30 days by department)
        $deptTrend = [];
        foreach ($departments as $dept) {
            $trendData = [];
            for ($i = 29; $i >= 0; $i--) {
                $day = \Carbon\Carbon::now()->subDays($i)->toDateString();
                $dayReports = PerformanceReport::where('department_id', $dept->id)
                    ->whereDate('report_date', $day)
                    ->get();

                $trendData[] = [
                    'date' => $day,
                    'profit' => $dayReports->sum('total_profit'),
                ];
            }
            $deptTrend[$dept->name] = $trendData;
        }

        return [
            'reports' => $reports,
            'departments' => $deptSummary,
            'total_sales' => $totalSales,
            'total_cogs' => $totalCogs,
            'total_profit' => $totalProfit,
            'dept_trend' => $deptTrend,
        ];
    }

    /**
     * 5. STOCK TRENDS (Stock IN vs Stock OUT)
     */
    private function getStockTrends($dateFrom, $dateTo)
    {
        $dailyTrend = [];

        for ($i = 29; $i >= 0; $i--) {
            $day = \Carbon\Carbon::now()->subDays($i)->toDateString();

            // Stock IN movements (movement_type with sign '+')
            $stockIn = StockMovement::whereDate('movement_date', $day)
                ->whereHas('movementType', function($q) {
                    $q->where('sign', '+');
                })
                ->sum('quantity_in_base_unit');

            // Stock OUT movements (movement_type with sign '-')
            $stockOut = StockMovement::whereDate('movement_date', $day)
                ->whereHas('movementType', function($q) {
                    $q->where('sign', '-');
                })
                ->sum('quantity_in_base_unit');

            $dailyTrend[] = [
                'date' => $day,
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
                'net_change' => $stockIn - $stockOut,
            ];
        }

        // Monthly trend
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = \Carbon\Carbon::now()->subMonths($i)->format('Y-m');
            $monthStart = \Carbon\Carbon::now()->subMonths($i)->startOfMonth()->toDateString();
            $monthEnd = \Carbon\Carbon::now()->subMonths($i)->endOfMonth()->toDateString();

            $stockIn = StockMovement::whereBetween('movement_date', [$monthStart, $monthEnd])
                ->whereHas('movementType', function($q) {
                    $q->where('sign', '+');
                })
                ->sum('quantity_in_base_unit');

            $stockOut = StockMovement::whereBetween('movement_date', [$monthStart, $monthEnd])
                ->whereHas('movementType', function($q) {
                    $q->where('sign', '-');
                })
                ->sum('quantity_in_base_unit');

            $monthlyTrend[] = [
                'month' => $month,
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
            ];
        }

        return [
            'daily_trend' => $dailyTrend,
            'monthly_trend' => $monthlyTrend,
        ];
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $reportType = $request->get('report_type', 'dashboard');
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $departmentId = $request->get('department_id');

        $data = [];
        $title = '';

        switch ($reportType) {
            case 'dashboard':
                $data = $this->getDashboardData($dateFrom, $dateTo);
                $title = 'Executive Dashboard Report';
                break;
            case 'top_moving_items':
                $data = $this->getTopMovingItems($dateFrom, $dateTo);
                $title = 'Top Moving Items Report';
                break;
            case 'low_stock':
                $data = $this->getLowStockReport();
                $title = 'Low Stock Alert Report';
                break;
            case 'cogs_vs_sales':
                $data = $this->getCogsVsSales($dateFrom, $dateTo);
                $title = 'COGS vs Sales vs Profit Report';
                break;
            case 'performance':
                $data = $this->getPerformanceReport($departmentId, $dateFrom, $dateTo);
                $title = 'Department Performance Report';
                break;
            case 'stock_trends':
                $data = $this->getStockTrends($dateFrom, $dateTo);
                $title = 'Stock Trends Report';
                break;
            default:
                $data = $this->getDashboardData($dateFrom, $dateTo);
                $title = 'Executive Dashboard Report';
        }

        $pdfData = [
            'title' => $title,
            'report_type' => $reportType,
            'data' => $data,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'generated_date' => now()->format('F d, Y H:i:s'),
            'generated_by' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
        ];

        $pdf = Pdf::loadView('management.reports.export_pdf', $pdfData);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($title . '_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $reportType = $request->get('report_type', 'dashboard');
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $departmentId = $request->get('department_id');

        $data = [];
        $title = '';

        switch ($reportType) {
            case 'dashboard':
                $data = $this->getDashboardData($dateFrom, $dateTo);
                $title = 'Executive Dashboard Report';
                break;
            case 'top_moving_items':
                $data = $this->getTopMovingItems($dateFrom, $dateTo);
                $title = 'Top Moving Items Report';
                break;
            case 'low_stock':
                $data = $this->getLowStockReport();
                $title = 'Low Stock Alert Report';
                break;
            case 'cogs_vs_sales':
                $data = $this->getCogsVsSales($dateFrom, $dateTo);
                $title = 'COGS vs Sales vs Profit Report';
                break;
            case 'performance':
                $data = $this->getPerformanceReport($departmentId, $dateFrom, $dateTo);
                $title = 'Department Performance Report';
                break;
            case 'stock_trends':
                $data = $this->getStockTrends($dateFrom, $dateTo);
                $title = 'Stock Trends Report';
                break;
            default:
                $data = $this->getDashboardData($dateFrom, $dateTo);
                $title = 'Executive Dashboard Report';
        }

        return Excel::download(new ReportsExport($data, $title, $reportType), $title . '_' . date('Y-m-d') . '.xlsx');
    }
}
