<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\DepartmentRequisition;
use App\Models\DepartmentRequisitionItem;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // ========== KPI DATA ==========
        $totalStockValue = InventoryItem::sum(DB::raw('current_stock * unit_cost'));

        // Today's stock movements
        $stockInToday = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))
            ->whereDate('created_at', today())
            ->sum('quantity_in_base_unit');

        $stockOutToday = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))
            ->whereDate('created_at', today())
            ->sum('quantity_in_base_unit');

        $netChangeToday = $stockInToday - $stockOutToday;
        $todayIssuesCount = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))->whereDate('created_at', today())->count();
        $todayReturnsCount = StockMovement::whereHas('movementType', fn($q) => $q->where('name', 'LIKE', '%RETURN%'))->whereDate('created_at', today())->count();

        // Stock alerts
        $lowStockItems = InventoryItem::where('is_active', true)
            ->whereNotNull('minimum_stock')
            ->where('current_stock', '<', DB::raw('minimum_stock'))
            ->where('current_stock', '>', 0)
            ->get();

        $outOfStockItems = InventoryItem::where('is_active', true)
            ->where('current_stock', '<=', 0)
            ->get();

        $healthyStockCount = InventoryItem::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('minimum_stock')->orWhere('current_stock', '>=', DB::raw('minimum_stock'));
            })->where('current_stock', '>', 0)->count();

        $lowStockCount = $lowStockItems->count();
        $outOfStockCount = $outOfStockItems->count();
        $totalActiveItems = InventoryItem::where('is_active', true)->count();

        $healthyStockPercent = $totalActiveItems > 0 ? round(($healthyStockCount / $totalActiveItems) * 100) : 0;
        $lowStockPercent = $totalActiveItems > 0 ? round(($lowStockCount / $totalActiveItems) * 100) : 0;
        $outOfStockPercent = $totalActiveItems > 0 ? round(($outOfStockCount / $totalActiveItems) * 100) : 0;

        // Pending requisitions
        $pendingRequisitions = DepartmentRequisition::with(['department', 'items'])
            ->where('status', 'pending')
            ->orderByRaw("CASE WHEN date_needed <= NOW() THEN 0 ELSE 1 END")
            ->orderBy('date_needed', 'asc')
            ->limit(10)
            ->get();

        $urgentRequests = DepartmentRequisition::with(['department', 'items.inventoryItem'])
            ->where('status', 'pending')
            ->whereDate('date_needed', '<=', now()->addDays(2))
            ->whereDate('date_needed', '>=', now())
            ->orderBy('date_needed', 'asc')
            ->get();

        // Today's activities
        $todayActivities = StockMovement::with(['inventoryItem', 'movementType', 'department', 'createdBy'])
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Department request volume
        $deptRequests = DepartmentRequisition::select('department_id', DB::raw('COUNT(*) as count'))
            ->where('status', 'pending')
            ->groupBy('department_id')
            ->get();

        $deptRequestLabels = [];
        $deptRequestData = [];
        foreach ($deptRequests as $dept) {
            if ($dept->department) {
                $deptRequestLabels[] = $dept->department->name;
                $deptRequestData[] = $dept->count;
            }
        }

        // ========== DEPARTMENT CONSUMPTION DATA ==========
        $deptSummaries = DepartmentRequisitionItem::select(
            'departments.name as department_name',
            DB::raw('SUM(issued_total_pieces) as total_issued'),
            DB::raw('SUM(returned_total_pieces) as total_returned'),
            DB::raw('SUM(quantity_consumed) as total_consumed')
        )
        ->join('department_requisitions', 'department_requisition_items.department_requisition_id', '=', 'department_requisitions.id')
        ->join('departments', 'department_requisitions.department_id', '=', 'departments.id')
        ->whereNull('department_requisitions.deleted_at')
        ->groupBy('departments.name')
        ->get();

        $deptLabels = $deptSummaries->pluck('department_name')->toArray();
        $deptIssuedData = $deptSummaries->pluck('total_issued')->toArray();
        $deptReturnedData = $deptSummaries->pluck('total_returned')->toArray();
        $deptConsumedData = $deptSummaries->pluck('total_consumed')->toArray();

        // ========== DAILY TREND DATA (Last 30 Days) ==========
        $dailyLabels = [];
        $dailyStockInValues = [];
        $dailyStockOutValues = [];
        $dailyIssuesValues = [];
        $dailyReturnsValues = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyLabels[] = now()->subDays($i)->format('M d');

            // Inventory Updates (Stock IN/OUT)
            $dailyStockInValues[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))
                ->whereDate('movement_date', $date)
                ->sum('quantity_in_base_unit');

            $dailyStockOutValues[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))
                ->whereDate('movement_date', $date)
                ->sum('quantity_in_base_unit');

            // Department Movements from department_requisition_items
            $dailyIssuesValues[] = DepartmentRequisitionItem::whereDate('created_at', $date)
                ->where('quantity_issued', '>', 0)
                ->sum('issued_total_pieces');

            $dailyReturnsValues[] = DepartmentRequisitionItem::whereDate('returned_at', $date)
                ->where('quantity_returned', '>', 0)
                ->sum('returned_total_pieces');
        }

        // Daily averages
        $avgDailyStockIn = count($dailyStockInValues) > 0 ? array_sum($dailyStockInValues) / count($dailyStockInValues) : 0;
        $avgDailyStockOut = count($dailyStockOutValues) > 0 ? array_sum($dailyStockOutValues) / count($dailyStockOutValues) : 0;
        $avgDailyIssues = count($dailyIssuesValues) > 0 ? array_sum($dailyIssuesValues) / count($dailyIssuesValues) : 0;
        $avgDailyReturns = count($dailyReturnsValues) > 0 ? array_sum($dailyReturnsValues) / count($dailyReturnsValues) : 0;

        // ========== WEEKLY TREND DATA (Last 12 Weeks) ==========
        $weeklyLabels = [];
        $weeklyStockInValues = [];
        $weeklyStockOutValues = [];
        $weeklyIssuesValues = [];
        $weeklyReturnsValues = [];

        for ($i = 11; $i >= 0; $i--) {
            $startDate = now()->subWeeks($i)->startOfWeek();
            $endDate = now()->subWeeks($i)->endOfWeek();
            $weekNumber = now()->subWeeks($i)->weekOfYear;
            $weeklyLabels[] = 'W' . $weekNumber;

            // Inventory Updates
            $weeklyStockInValues[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))
                ->whereBetween('movement_date', [$startDate, $endDate])
                ->sum('quantity_in_base_unit');

            $weeklyStockOutValues[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))
                ->whereBetween('movement_date', [$startDate, $endDate])
                ->sum('quantity_in_base_unit');

            // Department Movements
            $weeklyIssuesValues[] = DepartmentRequisitionItem::whereBetween('created_at', [$startDate, $endDate])
                ->where('quantity_issued', '>', 0)
                ->sum('issued_total_pieces');

            $weeklyReturnsValues[] = DepartmentRequisitionItem::whereBetween('returned_at', [$startDate, $endDate])
                ->where('quantity_returned', '>', 0)
                ->sum('returned_total_pieces');
        }

        // Weekly averages
        $avgWeeklyStockIn = count($weeklyStockInValues) > 0 ? array_sum($weeklyStockInValues) / count($weeklyStockInValues) : 0;
        $avgWeeklyStockOut = count($weeklyStockOutValues) > 0 ? array_sum($weeklyStockOutValues) / count($weeklyStockOutValues) : 0;
        $avgWeeklyIssues = count($weeklyIssuesValues) > 0 ? array_sum($weeklyIssuesValues) / count($weeklyIssuesValues) : 0;
        $avgWeeklyReturns = count($weeklyReturnsValues) > 0 ? array_sum($weeklyReturnsValues) / count($weeklyReturnsValues) : 0;

        // Week-over-Week change
        $wowStockInChange = count($weeklyStockInValues) >= 2 ? (($weeklyStockInValues[0] - $weeklyStockInValues[1]) / max($weeklyStockInValues[1], 1)) * 100 : 0;
        $wowStockOutChange = count($weeklyStockOutValues) >= 2 ? (($weeklyStockOutValues[0] - $weeklyStockOutValues[1]) / max($weeklyStockOutValues[1], 1)) * 100 : 0;
        $wowIssuesChange = count($weeklyIssuesValues) >= 2 ? (($weeklyIssuesValues[0] - $weeklyIssuesValues[1]) / max($weeklyIssuesValues[1], 1)) * 100 : 0;
        $wowReturnsChange = count($weeklyReturnsValues) >= 2 ? (($weeklyReturnsValues[0] - $weeklyReturnsValues[1]) / max($weeklyReturnsValues[1], 1)) * 100 : 0;

        // ========== MONTHLY TREND DATA (Last 6 Months) ==========
        $monthlyLabels = [];
        $monthlyStockInValues = [];
        $monthlyStockOutValues = [];
        $monthlyIssuesValues = [];
        $monthlyReturnsValues = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');

            // Inventory Updates
            $monthlyStockInValues[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))
                ->whereYear('movement_date', $month->year)
                ->whereMonth('movement_date', $month->month)
                ->sum('quantity_in_base_unit');

            $monthlyStockOutValues[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))
                ->whereYear('movement_date', $month->year)
                ->whereMonth('movement_date', $month->month)
                ->sum('quantity_in_base_unit');

            // Department Movements
            $monthlyIssuesValues[] = DepartmentRequisitionItem::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('quantity_issued', '>', 0)
                ->sum('issued_total_pieces');

            $monthlyReturnsValues[] = DepartmentRequisitionItem::whereYear('returned_at', $month->year)
                ->whereMonth('returned_at', $month->month)
                ->where('quantity_returned', '>', 0)
                ->sum('returned_total_pieces');
        }

        // Monthly averages
        $avgMonthlyStockIn = count($monthlyStockInValues) > 0 ? array_sum($monthlyStockInValues) / count($monthlyStockInValues) : 0;
        $avgMonthlyStockOut = count($monthlyStockOutValues) > 0 ? array_sum($monthlyStockOutValues) / count($monthlyStockOutValues) : 0;
        $avgMonthlyIssues = count($monthlyIssuesValues) > 0 ? array_sum($monthlyIssuesValues) / count($monthlyIssuesValues) : 0;
        $avgMonthlyReturns = count($monthlyReturnsValues) > 0 ? array_sum($monthlyReturnsValues) / count($monthlyReturnsValues) : 0;

        // Month-over-Month change
        $momStockInChange = count($monthlyStockInValues) >= 2 ? (($monthlyStockInValues[0] - $monthlyStockInValues[1]) / max($monthlyStockInValues[1], 1)) * 100 : 0;
        $momStockOutChange = count($monthlyStockOutValues) >= 2 ? (($monthlyStockOutValues[0] - $monthlyStockOutValues[1]) / max($monthlyStockOutValues[1], 1)) * 100 : 0;
        $momIssuesChange = count($monthlyIssuesValues) >= 2 ? (($monthlyIssuesValues[0] - $monthlyIssuesValues[1]) / max($monthlyIssuesValues[1], 1)) * 100 : 0;
        $momReturnsChange = count($monthlyReturnsValues) >= 2 ? (($monthlyReturnsValues[0] - $monthlyReturnsValues[1]) / max($monthlyReturnsValues[1], 1)) * 100 : 0;

        // ========== PIE CHART DATA ==========

        // 1. Stock Health Pie Chart
        $stockHealthData = [
            'labels' => ['Healthy Stock', 'Low Stock', 'Out of Stock'],
            'data' => [$healthyStockCount, $lowStockCount, $outOfStockCount],
            'colors' => ['#10b981', '#f59e0b', '#ef4444']
        ];

        // 2. Today's Activity Pie Chart
        $todayActivityData = [
            'labels' => ['Stock IN', 'Stock OUT (Issues)', 'Returns'],
            'data' => [$stockInToday, $stockOutToday, $todayReturnsCount > 0 ? $todayReturnsCount : 1],
            'colors' => ['#10b981', '#ef4444', '#3b82f6']
        ];

        // 3. Department Consumption Pie Chart
        $deptConsumptionData = [
            'labels' => $deptLabels,
            'data' => $deptConsumedData,
            'colors' => ['#3b82f6', '#8b5cf6', '#ec489a', '#f59e0b']
        ];

        // 4. Inventory Source Pie Chart (Stock IN by Source)
        $grnTotal = StockMovement::whereHas('movementType', fn($q) => $q->where('name', 'LIKE', '%GRN%'))->sum('quantity_in_base_unit');
        $poTotal = StockMovement::whereHas('movementType', fn($q) => $q->where('name', 'LIKE', '%PURCHASE%'))->sum('quantity_in_base_unit');
        $returnTotal = StockMovement::whereHas('movementType', fn($q) => $q->where('name', 'LIKE', '%RETURN%'))->sum('quantity_in_base_unit');
        $manualTotal = StockMovement::whereHas('movementType', fn($q) => $q->where('name', 'LIKE', '%MANUAL%'))->sum('quantity_in_base_unit');

        $inventorySourceData = [
            'labels' => ['GRN Receipts', 'Purchase Orders', 'Returns to Store', 'Manual Adjustments'],
            'data' => [$grnTotal, $poTotal, $returnTotal, $manualTotal],
            'colors' => ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b']
        ];

        // 5. Pending Requisitions by Department Pie Chart
        $pendingRequisitionData = [
            'labels' => $deptRequestLabels,
            'data' => $deptRequestData,
            'colors' => ['#f59e0b', '#ef4444', '#10b981', '#3b82f6']
        ];

        // Top moving items today
        $topItemsTodayRaw = StockMovement::select('inventory_item_id', DB::raw('SUM(quantity_in_base_unit) as total'))
            ->whereDate('created_at', today())
            ->groupBy('inventory_item_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $topItemsToday = [];
        $topItemsTodayMax = 0;
        foreach ($topItemsTodayRaw as $item) {
            $inventoryItem = InventoryItem::find($item->inventory_item_id);
            if ($inventoryItem) {
                $topItemsToday[] = [
                    'name' => $inventoryItem->name,
                    'quantity' => $item->total
                ];
                if ($item->total > $topItemsTodayMax) $topItemsTodayMax = $item->total;
            }
        }

        return view('store.dashboard', compact(
            'totalStockValue',
            'stockInToday', 'stockOutToday', 'netChangeToday', 'todayIssuesCount', 'todayReturnsCount',
            'lowStockItems', 'outOfStockItems',
            'healthyStockCount', 'lowStockCount', 'outOfStockCount', 'totalActiveItems',
            'healthyStockPercent', 'lowStockPercent', 'outOfStockPercent',
            'pendingRequisitions', 'urgentRequests', 'todayActivities',
            'deptRequestLabels', 'deptRequestData',
            'topItemsToday', 'topItemsTodayMax',
            // Daily trend data
            'dailyLabels', 'dailyStockInValues', 'dailyStockOutValues',
            'dailyIssuesValues', 'dailyReturnsValues',
            'avgDailyStockIn', 'avgDailyStockOut', 'avgDailyIssues', 'avgDailyReturns',
            // Weekly trend data
            'weeklyLabels', 'weeklyStockInValues', 'weeklyStockOutValues',
            'weeklyIssuesValues', 'weeklyReturnsValues',
            'avgWeeklyStockIn', 'avgWeeklyStockOut', 'avgWeeklyIssues', 'avgWeeklyReturns',
            'wowStockInChange', 'wowStockOutChange', 'wowIssuesChange', 'wowReturnsChange',
            // Monthly trend data
            'monthlyLabels', 'monthlyStockInValues', 'monthlyStockOutValues',
            'monthlyIssuesValues', 'monthlyReturnsValues',
            'avgMonthlyStockIn', 'avgMonthlyStockOut', 'avgMonthlyIssues', 'avgMonthlyReturns',
            'momStockInChange', 'momStockOutChange', 'momIssuesChange', 'momReturnsChange',
            // Pie chart data
            'stockHealthData', 'todayActivityData', 'deptConsumptionData',
            'inventorySourceData', 'pendingRequisitionData',
            // Department data
            'deptLabels', 'deptIssuedData', 'deptReturnedData', 'deptConsumedData'
        ));
    }
}
