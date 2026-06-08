<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Batch;
use App\Models\StockMovement;
use App\Models\StockMovementType;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StoreDashboardController extends Controller
{
    private function checkAuthorization()
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'STORE') {
            return false;
        }
        return true;
    }

    public function index(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        // Get filter values
        $dateRange = $request->get('date_range', 'all');
        $categoryFilter = $request->get('category_id', '');
        $departmentFilter = $request->get('department_id', '');

        // Get date range based on filter
        $dates = $this->getDateRange($dateRange);

        // Get categories and departments for filters
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        // Dashboard Data
        $stockStatus = $this->getStockStatus();
        $stockValue = $this->getStockValue();
        $movementAnalytics = $this->getMovementAnalytics($dates);
        $departmentConsumption = $this->getDepartmentConsumption($dates, $departmentFilter);
        $importAnalytics = $this->getImportAnalytics();
        $lowStockAlerts = $this->getLowStockAlerts();
        $financialMetrics = $this->getFinancialMetrics();
        $performanceKpis = $this->getPerformanceKpis($dates);

        // Chart Data
        $chartData = $this->getChartData($dates, $categoryFilter);

        // Recent Activities
        $recentMovements = $this->getRecentMovements(10);
        $recentImports = $this->getRecentImports(5);

        return view('store.dashboard', compact(
            'stockStatus',
            'stockValue',
            'movementAnalytics',
            'departmentConsumption',
            'importAnalytics',
            'lowStockAlerts',
            'financialMetrics',
            'performanceKpis',
            'recentMovements',
            'recentImports',
            'chartData',
            'dateRange',
            'categories',
            'departments',
            'categoryFilter',
            'departmentFilter'
        ));
    }

    /**
     * 1. Stock Status Overview
     */
    private function getStockStatus()
    {
        $totalItems = InventoryItem::where('is_active', true)->count();

        $inStockItems = DB::table('inventory_items')
            ->where('is_active', true)
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('batches')
                    ->whereColumn('batches.inventory_item_id', 'inventory_items.id')
                    ->where('batches.batch_status', 'active')
                    ->where('batches.remaining_quantity', '>', 0);
            })
            ->count();

        $outOfStockItems = $totalItems - $inStockItems;

        $lowStockItems = DB::table('inventory_items')
            ->where('is_active', true)
            ->where('minimum_stock', '>', 0)
            ->whereRaw('(SELECT COALESCE(SUM(remaining_quantity), 0) FROM batches WHERE batches.inventory_item_id = inventory_items.id AND batches.batch_status = "active") <= minimum_stock')
            ->count();

        return (object)[
            'total_items' => $totalItems,
            'in_stock' => $inStockItems,
            'out_of_stock' => $outOfStockItems,
            'low_stock' => $lowStockItems,
            'in_stock_percentage' => $totalItems > 0 ? round(($inStockItems / $totalItems) * 100, 1) : 0,
        ];
    }

    /**
     * 2. Stock Value Analytics
     */
    private function getStockValue()
    {
        $currentValue = Batch::where('batch_status', 'active')
            ->sum(DB::raw('remaining_quantity * unit_cost'));

        return (object)[
            'current_value' => $currentValue,
            'current_value_formatted' => number_format($currentValue, 2),
        ];
    }

    /**
     * 3. Movement Analytics
     */
    private function getMovementAnalytics($dates)
    {
        $stockIn = StockMovement::whereBetween('movement_date', [$dates['start'], $dates['end']])
            ->whereHas('movementType', function($q) {
                $q->where('sign', '+');
            })
            ->select(DB::raw('COALESCE(SUM(quantity_in_base_unit), 0) as total_quantity'), DB::raw('COUNT(*) as total_count'))
            ->first();

        $stockOut = StockMovement::whereBetween('movement_date', [$dates['start'], $dates['end']])
            ->whereHas('movementType', function($q) {
                $q->where('sign', '-');
            })
            ->select(DB::raw('COALESCE(SUM(quantity_in_base_unit), 0) as total_quantity'), DB::raw('COUNT(*) as total_count'))
            ->first();

        $netChange = ($stockIn->total_quantity ?? 0) - ($stockOut->total_quantity ?? 0);

        $movementTypes = StockMovement::whereBetween('movement_date', [$dates['start'], $dates['end']])
            ->select('stock_movement_types.name', 'stock_movement_types.sign', DB::raw('COUNT(*) as count'))
            ->join('stock_movement_types', 'stock_movements.movement_type_id', '=', 'stock_movement_types.id')
            ->groupBy('stock_movement_types.id', 'stock_movement_types.name', 'stock_movement_types.sign')
            ->get();

        return (object)[
            'stock_in_quantity' => $stockIn->total_quantity ?? 0,
            'stock_in_count' => $stockIn->total_count ?? 0,
            'stock_out_quantity' => $stockOut->total_quantity ?? 0,
            'stock_out_count' => $stockOut->total_count ?? 0,
            'net_change' => $netChange,
            'movement_types' => $movementTypes,
        ];
    }

    /**
     * 4. Department Consumption
     */
    private function getDepartmentConsumption($dates, $departmentFilter)
    {
        $query = DB::table('department_requisition_items')
            ->select(
                'departments.id',
                'departments.name',
                DB::raw('COALESCE(SUM(department_requisition_items.quantity_issued), 0) as total_issued'),
                DB::raw('COALESCE(SUM(department_requisition_items.quantity_consumed), 0) as total_consumed'),
                DB::raw('COALESCE(SUM(department_requisition_items.quantity_returned), 0) as total_returned')
            )
            ->join('department_requisitions', 'department_requisition_items.department_requisition_id', '=', 'department_requisitions.id')
            ->join('departments', 'department_requisitions.department_id', '=', 'departments.id');

        if ($dates['start'] && $dates['end']) {
            $query->whereBetween('department_requisitions.created_at', [$dates['start'] . ' 00:00:00', $dates['end'] . ' 23:59:59']);
        }

        if ($departmentFilter) {
            $query->where('departments.id', $departmentFilter);
        }

        $topDepartments = $query->groupBy('departments.id', 'departments.name')
            ->orderBy('total_consumed', 'desc')
            ->limit(5)
            ->get();

        return (object)[
            'top_departments' => $topDepartments,
        ];
    }

    /**
     * 5. Import Analytics
     */
    private function getImportAnalytics()
    {
        $totalImported = Batch::where('notes', 'Imported from CSV')
            ->orWhere('batch_number', 'like', 'BAT-IMP-%')
            ->count();

        $totalQuantity = Batch::where('notes', 'Imported from CSV')
            ->orWhere('batch_number', 'like', 'BAT-IMP-%')
            ->sum('initial_quantity');

        return (object)[
            'total_imported_items' => $totalImported,
            'total_imported_quantity' => $totalQuantity,
        ];
    }

    /**
     * 6. Low Stock Alerts
     */
    private function getLowStockAlerts()
    {
        $criticalStock = DB::table('inventory_items')
            ->select('id', 'name', 'unit_of_measurement', 'minimum_stock')
            ->where('is_active', true)
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('batches')
                    ->whereColumn('batches.inventory_item_id', 'inventory_items.id')
                    ->where('batches.batch_status', 'active')
                    ->where('batches.remaining_quantity', '>', 0);
            })
            ->limit(10)
            ->get();

        $lowStock = DB::table('inventory_items')
            ->select(
                'inventory_items.id',
                'inventory_items.name',
                'inventory_items.unit_of_measurement',
                'inventory_items.minimum_stock',
                DB::raw('(SELECT COALESCE(SUM(remaining_quantity), 0) FROM batches WHERE batches.inventory_item_id = inventory_items.id AND batches.batch_status = "active") as current_stock')
            )
            ->where('is_active', true)
            ->where('minimum_stock', '>', 0)
            ->whereRaw('(SELECT COALESCE(SUM(remaining_quantity), 0) FROM batches WHERE batches.inventory_item_id = inventory_items.id AND batches.batch_status = "active") <= minimum_stock')
            ->limit(10)
            ->get();

        return (object)[
            'critical_stock' => $criticalStock,
            'critical_count' => $criticalStock->count(),
            'low_stock' => $lowStock,
            'low_stock_count' => $lowStock->count(),
        ];
    }

    /**
     * 7. Financial Metrics
     */
    private function getFinancialMetrics()
    {
        $totalCost = Batch::where('batch_status', 'active')
            ->sum(DB::raw('remaining_quantity * unit_cost'));

        return (object)[
            'total_inventory_cost' => $totalCost,
            'total_inventory_cost_formatted' => number_format($totalCost, 2),
        ];
    }

    /**
     * 8. Performance KPIs
     */
    private function getPerformanceKpis($dates)
    {
        $totalOut = StockMovement::whereBetween('movement_date', [$dates['start'], $dates['end']])
            ->whereHas('movementType', function($q) {
                $q->where('sign', '-');
            })
            ->sum('quantity_in_base_unit');

        $averageStock = DB::table('batches')
            ->where('batch_status', 'active')
            ->avg('remaining_quantity');

        $turnoverRate = ($averageStock > 0 && $averageStock !== null) ? round($totalOut / $averageStock, 2) : 0;

        $totalRequested = DB::table('department_requisition_items')
            ->whereBetween('created_at', [$dates['start'] . ' 00:00:00', $dates['end'] . ' 23:59:59'])
            ->sum('quantity_requested');

        $totalIssued = DB::table('department_requisition_items')
            ->whereBetween('created_at', [$dates['start'] . ' 00:00:00', $dates['end'] . ' 23:59:59'])
            ->sum('quantity_issued');

        $fulfillmentRate = $totalRequested > 0 ? round(($totalIssued / $totalRequested) * 100, 1) : 0;

        return (object)[
            'stock_turnover_rate' => $turnoverRate,
            'issue_fulfillment_rate' => $fulfillmentRate,
        ];
    }

    /**
     * Chart Data for Visualizations
     */
    private function getChartData($dates, $categoryFilter)
    {
        // 1. Stock Status Pie Chart Data
        $stockStatusPie = [
            'labels' => ['In Stock', 'Out of Stock', 'Low Stock'],
            'data' => [
                $this->getStockStatus()->in_stock,
                $this->getStockStatus()->out_of_stock,
                $this->getStockStatus()->low_stock
            ],
            'colors' => ['#10b981', '#ef4444', '#f59e0b']
        ];

        // 2. Stock Value by Category - Pie Chart
        $valueByCategory = DB::table('categories')
            ->select('categories.name', DB::raw('COALESCE(SUM(batches.remaining_quantity * batches.unit_cost), 0) as total_value'))
            ->join('inventory_items', 'inventory_items.category_id', '=', 'categories.id')
            ->join('batches', 'batches.inventory_item_id', '=', 'inventory_items.id')
            ->where('batches.batch_status', 'active');

        if ($categoryFilter) {
            $valueByCategory->where('categories.id', $categoryFilter);
        }

        $valueByCategory = $valueByCategory->groupBy('categories.id', 'categories.name')
            ->orderBy('total_value', 'desc')
            ->limit(6)
            ->get();

        $categoryPie = [
            'labels' => $valueByCategory->pluck('name')->toArray(),
            'data' => $valueByCategory->pluck('total_value')->map(function($val) { return round($val, 2); })->toArray(),
        ];

        // 3. Movement Types Distribution - Pie Chart
        $movementTypes = StockMovement::whereBetween('movement_date', [$dates['start'], $dates['end']])
            ->select('stock_movement_types.name', DB::raw('COUNT(*) as count'))
            ->join('stock_movement_types', 'stock_movements.movement_type_id', '=', 'stock_movement_types.id')
            ->groupBy('stock_movement_types.id', 'stock_movement_types.name')
            ->get();

        $movementPie = [
            'labels' => $movementTypes->pluck('name')->toArray(),
            'data' => $movementTypes->pluck('count')->toArray(),
        ];

        // 4. Stock Value Trend (Line Chart - Last 12 months)
        $valueTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth()->toDateString();
            $endOfMonth = $date->copy()->endOfMonth()->toDateString();

            $value = Batch::where('batch_status', 'active')
                ->where('created_at', '<=', $endOfMonth . ' 23:59:59')
                ->sum(DB::raw('remaining_quantity * unit_cost'));

            $valueTrend['labels'][] = $date->format('M Y');
            $valueTrend['data'][] = round($value, 2);
        }

        // 5. Movement Trend (Line Chart - Last 30 days)
        $movementTrend = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();

            $in = StockMovement::whereDate('movement_date', $date)
                ->whereHas('movementType', function($q) {
                    $q->where('sign', '+');
                })
                ->sum('quantity_in_base_unit');

            $out = StockMovement::whereDate('movement_date', $date)
                ->whereHas('movementType', function($q) {
                    $q->where('sign', '-');
                })
                ->sum('quantity_in_base_unit');

            $movementTrend['labels'][] = Carbon::now()->subDays($i)->format('M d');
            $movementTrend['in'][] = round($in, 2);
            $movementTrend['out'][] = round($out, 2);
        }

        // 6. Department Consumption - Pie Chart
        $deptConsumption = DB::table('department_requisition_items')
            ->select('departments.name', DB::raw('COALESCE(SUM(department_requisition_items.quantity_consumed), 0) as total_consumed'))
            ->join('department_requisitions', 'department_requisition_items.department_requisition_id', '=', 'department_requisitions.id')
            ->join('departments', 'department_requisitions.department_id', '=', 'departments.id')
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('total_consumed', 'desc')
            ->limit(5)
            ->get();

        $deptConsumptionPie = [
            'labels' => $deptConsumption->pluck('name')->toArray(),
            'data' => $deptConsumption->pluck('total_consumed')->map(function($val) { return round($val, 2); })->toArray(),
        ];

        return (object)[
            'stock_status_pie' => $stockStatusPie,
            'category_pie' => $categoryPie,
            'movement_pie' => $movementPie,
            'value_trend' => $valueTrend,
            'movement_trend' => $movementTrend,
            'dept_consumption_pie' => $deptConsumptionPie,
        ];
    }

    /**
     * Recent stock movements
     */
    private function getRecentMovements($limit = 10)
    {
        return StockMovement::with(['inventoryItem', 'movementType'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Recent imports
     */
    private function getRecentImports($limit = 5)
    {
        return Batch::with('inventoryItem')
            ->where('notes', 'Imported from CSV')
            ->orWhere('batch_number', 'like', 'BAT-IMP-%')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get date range based on filter
     */
    private function getDateRange($range)
    {
        switch ($range) {
            case 'today':
                return [
                    'start' => Carbon::today()->toDateString(),
                    'end' => Carbon::today()->toDateString(),
                ];
            case 'week':
                return [
                    'start' => Carbon::now()->startOfWeek()->toDateString(),
                    'end' => Carbon::now()->endOfWeek()->toDateString(),
                ];
            case 'month':
                return [
                    'start' => Carbon::now()->startOfMonth()->toDateString(),
                    'end' => Carbon::now()->endOfMonth()->toDateString(),
                ];
            case 'year':
                return [
                    'start' => Carbon::now()->startOfYear()->toDateString(),
                    'end' => Carbon::now()->endOfYear()->toDateString(),
                ];
            case 'all':
            default:
                return [
                    'start' => '1970-01-01',
                    'end' => Carbon::now()->toDateString(),
                ];
        }
    }
}
