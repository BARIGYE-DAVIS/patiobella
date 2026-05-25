<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceivedNote;
use App\Models\DepartmentRequisitionItem;
use App\Models\DepartmentRequisition;
use App\Models\InventoryItem;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagementController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // ============================================================
        // STOCK MOVEMENTS DATA
        // ============================================================
        $totalStockIn = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))->sum('quantity_in_base_unit');
        $totalStockOut = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))->sum('quantity_in_base_unit');
        $netChange = $totalStockIn - $totalStockOut;
        $totalValueMoved = StockMovement::sum('total_value');
        $totalStockValue = InventoryItem::sum(DB::raw('current_stock * unit_cost'));
        // Add these to your existing dashboard() method in ManagementController.php

// ========== STOCK DAILY/WEEKLY/MONTHLY DATA ==========
$dailyLabels = [];
$dailyStockInValues = [];
$dailyStockOutValues = [];
$dailyStockInCounts = [];
$dailyStockOutCounts = [];
for ($i = 29; $i >= 0; $i--) {
    $date = now()->subDays($i)->format('Y-m-d');
    $dailyLabels[] = now()->subDays($i)->format('M d');
    $dailyStockInValues[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))->whereDate('movement_date', $date)->sum('quantity_in_base_unit');
    $dailyStockOutValues[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))->whereDate('movement_date', $date)->sum('quantity_in_base_unit');
    $dailyStockInCounts[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))->whereDate('movement_date', $date)->count();
    $dailyStockOutCounts[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))->whereDate('movement_date', $date)->count();
}

$weeklyLabels = [];
$weeklyStockInValues = [];
$weeklyStockOutValues = [];
$weeklyStockInCounts = [];
$weeklyStockOutCounts = [];
for ($i = 11; $i >= 0; $i--) {
    $startDate = now()->subWeeks($i)->startOfWeek();
    $endDate = now()->subWeeks($i)->endOfWeek();
    $weeklyLabels[] = 'W' . now()->subWeeks($i)->weekOfYear;
    $weeklyStockInValues[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))->whereBetween('movement_date', [$startDate, $endDate])->sum('quantity_in_base_unit');
    $weeklyStockOutValues[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))->whereBetween('movement_date', [$startDate, $endDate])->sum('quantity_in_base_unit');
    $weeklyStockInCounts[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))->whereBetween('movement_date', [$startDate, $endDate])->count();
    $weeklyStockOutCounts[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))->whereBetween('movement_date', [$startDate, $endDate])->count();
}

$monthlyLabels = [];
$monthlyStockInValues = [];
$monthlyStockOutValues = [];
$monthlyStockInCounts = [];
$monthlyStockOutCounts = [];
for ($i = 5; $i >= 0; $i--) {
    $monthlyLabels[] = now()->subMonths($i)->format('M Y');
    $monthlyStockInValues[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))->whereMonth('movement_date', now()->subMonths($i)->month)->whereYear('movement_date', now()->subMonths($i)->year)->sum('quantity_in_base_unit');
    $monthlyStockOutValues[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))->whereMonth('movement_date', now()->subMonths($i)->month)->whereYear('movement_date', now()->subMonths($i)->year)->sum('quantity_in_base_unit');
    $monthlyStockInCounts[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))->whereMonth('movement_date', now()->subMonths($i)->month)->whereYear('movement_date', now()->subMonths($i)->year)->count();
    $monthlyStockOutCounts[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))->whereMonth('movement_date', now()->subMonths($i)->month)->whereYear('movement_date', now()->subMonths($i)->year)->count();
}

// ========== PO DAILY/WEEKLY/MONTHLY DATA ==========
$poDailyValues = []; $poDailyCounts = [];
$poWeeklyValues = []; $poWeeklyCounts = [];
$poMonthlyValues = []; $poMonthlyCounts = [];
foreach ($dailyLabels as $i => $label) { $date = now()->subDays(29 - $i)->format('Y-m-d'); $poDailyValues[] = PurchaseOrder::whereDate('po_date', $date)->sum('total_amount'); $poDailyCounts[] = PurchaseOrder::whereDate('po_date', $date)->count(); }
foreach ($weeklyLabels as $i => $label) { $startDate = now()->subWeeks(11 - $i)->startOfWeek(); $endDate = now()->subWeeks(11 - $i)->endOfWeek(); $poWeeklyValues[] = PurchaseOrder::whereBetween('po_date', [$startDate, $endDate])->sum('total_amount'); $poWeeklyCounts[] = PurchaseOrder::whereBetween('po_date', [$startDate, $endDate])->count(); }
foreach ($monthlyLabels as $i => $label) { $poMonthlyValues[] = PurchaseOrder::whereMonth('po_date', now()->subMonths(5 - $i)->month)->whereYear('po_date', now()->subMonths(5 - $i)->year)->sum('total_amount'); $poMonthlyCounts[] = PurchaseOrder::whereMonth('po_date', now()->subMonths(5 - $i)->month)->whereYear('po_date', now()->subMonths(5 - $i)->year)->count(); }

// ========== DEPARTMENT DAILY/WEEKLY/MONTHLY DATA ==========
$deptDailyIssued = []; $deptDailyReturned = []; $deptDailyNet = [];
$deptWeeklyIssued = []; $deptWeeklyReturned = [];
$deptWeeklyKitchen = []; $deptWeeklyBar = []; $deptWeeklyCafe = [];
$deptMonthlyIssued = []; $deptMonthlyReturned = [];
$deptMonthlyKitchen = []; $deptMonthlyBar = []; $deptMonthlyCafe = [];
// (Similar loops using DepartmentRequisitionItem)

        // Monthly trends
        $monthlyStockIn = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))
            ->whereMonth('movement_date', now()->month)
            ->sum('quantity_in_base_unit');
        $monthlyStockOut = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))
            ->whereMonth('movement_date', now()->month)
            ->sum('quantity_in_base_unit');

        // Weekly trend data
        $weeklyLabels = [];
        $weeklyInData = [];
        $weeklyOutData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $weeklyLabels[] = now()->subDays($i)->format('M d');
            $weeklyInData[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))->whereDate('movement_date', $date)->sum('quantity_in_base_unit');
            $weeklyOutData[] = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))->whereDate('movement_date', $date)->sum('quantity_in_base_unit');
        }

        // Top items
        $topItems = StockMovement::select('inventory_item_id', DB::raw('SUM(quantity_in_base_unit) as total'))
            ->with('inventoryItem')
            ->groupBy('inventory_item_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        $topItemsLabels = [];
        $topItemsData = [];
        foreach ($topItems as $item) {
            $topItemsLabels[] = $item->inventoryItem->name ?? 'Unknown';
            $topItemsData[] = $item->total;
        }

        // Source breakdown
        $grnTotal = StockMovement::whereHas('movementType', fn($q) => $q->where('name', 'LIKE', '%GRN%'))->sum('quantity_in_base_unit');
        $poTotal = StockMovement::whereHas('movementType', fn($q) => $q->where('name', 'LIKE', '%PURCHASE%'))->sum('quantity_in_base_unit');
        $returnTotal = StockMovement::whereHas('movementType', fn($q) => $q->where('name', 'LIKE', '%RETURN%'))->sum('quantity_in_base_unit');
        $manualTotal = StockMovement::whereHas('movementType', fn($q) => $q->where('name', 'LIKE', '%MANUAL%'))->sum('quantity_in_base_unit');
        $sourceLabels = ['GRN Receipts', 'Purchase Orders', 'Returns', 'Manual'];
        $sourceData = [$grnTotal, $poTotal, $returnTotal, $manualTotal];

        // Department data
        $deptSummaries = DepartmentRequisitionItem::select('departments.name', DB::raw('SUM(issued_total_pieces) as issued'), DB::raw('SUM(returned_total_pieces) as returned'), DB::raw('SUM(quantity_consumed) as consumed'))
            ->join('department_requisitions', 'department_requisition_items.department_requisition_id', '=', 'department_requisitions.id')
            ->join('departments', 'department_requisitions.department_id', '=', 'departments.id')
            ->whereNull('department_requisitions.deleted_at')
            ->groupBy('departments.name')
            ->get();
        $deptLabels = $deptSummaries->pluck('name')->toArray();
        $deptData = $deptSummaries->pluck('consumed')->toArray();
        $deptIssuedData = $deptSummaries->pluck('issued')->toArray();
        $deptReturnedData = $deptSummaries->pluck('returned')->toArray();

        // Recent movements
        $recentMovements = StockMovement::with(['inventoryItem', 'movementType'])->orderBy('created_at', 'desc')->limit(10)->get();

        // ============================================================
        // PURCHASE ORDERS DATA
        // ============================================================
        $pendingApprovals = PurchaseOrder::where('status', 'draft')->count();
        $recentPOs = PurchaseOrder::with('vendor')->orderBy('created_at', 'desc')->limit(10)->get();
        $poMonthlyLabels = PurchaseOrder::select(DB::raw("DATE_FORMAT(po_date, '%Y-%m') as month"))->groupBy('month')->orderBy('month', 'desc')->limit(6)->pluck('month')->toArray();
        $poMonthlyData = [];
        foreach ($poMonthlyLabels as $month) {
            $poMonthlyData[] = PurchaseOrder::whereRaw("DATE_FORMAT(po_date, '%Y-%m') = ?", [$month])->sum('total_amount');
        }
        $poStatusLabels = ['Draft', 'Approved', 'Sent', 'Partially Received', 'Fully Received', 'Cancelled'];
        $poStatusData = [
            PurchaseOrder::where('status', 'draft')->count(),
            PurchaseOrder::where('status', 'approved')->count(),
            PurchaseOrder::where('status', 'sent')->count(),
            PurchaseOrder::where('status', 'partially_received')->count(),
            PurchaseOrder::where('status', 'fully_received')->count(),
            PurchaseOrder::where('status', 'cancelled')->count(),
        ];

        // ============================================================
        // GRN DATA
        // ============================================================
        $grnDraftCount = GoodsReceivedNote::where('status', 'draft')->count();
        $grnCompletedCount = GoodsReceivedNote::where('status', 'completed')->count();
        $grnInventoryUpdatedCount = GoodsReceivedNote::where('status', 'inventory_updated')->count();

        // Daily GRN data
        $dailyLabels = [];
        $dailyValues = [];
        $dailyCounts = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyLabels[] = now()->subDays($i)->format('M d');
            $dailyValues[] = GoodsReceivedNote::whereDate('received_date', $date)->sum('grn_total_amount');
            $dailyCounts[] = GoodsReceivedNote::whereDate('received_date', $date)->count();
        }

        // Weekly GRN data
        $weeklyGRNLabels = [];
        $weeklyGRNValues = [];
        $weeklyGRNCounts = [];
        for ($i = 11; $i >= 0; $i--) {
            $startDate = now()->subWeeks($i)->startOfWeek();
            $endDate = now()->subWeeks($i)->endOfWeek();
            $weeklyGRNLabels[] = 'Week ' . now()->subWeeks($i)->weekOfYear;
            $weeklyGRNValues[] = GoodsReceivedNote::whereBetween('received_date', [$startDate, $endDate])->sum('grn_total_amount');
            $weeklyGRNCounts[] = GoodsReceivedNote::whereBetween('received_date', [$startDate, $endDate])->count();
        }

        // Monthly GRN data
        $monthlyGRNData = GoodsReceivedNote::select(DB::raw("DATE_FORMAT(received_date, '%Y-%m') as month"), DB::raw('SUM(grn_total_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')->orderBy('month', 'desc')->limit(6)->get();
        $monthlyLabels = $monthlyGRNData->pluck('month')->reverse()->values()->toArray();
        $monthlyValues = $monthlyGRNData->pluck('total')->reverse()->values()->toArray();
        $monthlyCounts = $monthlyGRNData->pluck('count')->reverse()->values()->toArray();

        // Distribution data
        $distributions = DepartmentRequisitionItem::with(['departmentRequisition.department', 'inventoryItem'])
            ->where('quantity_issued', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('management.dashboard', compact(
            'totalStockIn', 'totalStockOut', 'netChange', 'totalValueMoved', 'totalStockValue',
            'monthlyStockIn', 'monthlyStockOut',
            'weeklyLabels', 'weeklyInData', 'weeklyOutData',
            'topItemsLabels', 'topItemsData',
            'sourceLabels', 'sourceData',
            'deptLabels', 'deptData', 'deptIssuedData', 'deptReturnedData',
            'recentMovements',
            'pendingApprovals', 'recentPOs',
            'poMonthlyLabels', 'poMonthlyData', 'poStatusLabels', 'poStatusData',
            'grnDraftCount', 'grnCompletedCount', 'grnInventoryUpdatedCount',
            'dailyLabels', 'dailyValues', 'dailyCounts',
            'weeklyGRNLabels', 'weeklyGRNValues', 'weeklyGRNCounts',
            'monthlyLabels', 'monthlyValues', 'monthlyCounts',
            'distributions'
        ));
    }



public function vendorsIndex()
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $vendors = \App\Models\Vendor::withCount(['purchaseOrders'])
        ->orderBy('name')
        ->paginate(20);

    return view('management.vendors', compact('vendors'));
}

}
