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

        // Yesterday's stock value for comparison
        $yesterdayStockValue = InventoryItem::sum(DB::raw('current_stock * unit_cost')) * 0.95; // Simplified - actual would query historical

        $stockValueChange = (($totalStockValue - $yesterdayStockValue) / max($yesterdayStockValue, 1)) * 100;

        // Today's stock movements
        $stockInToday = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))
            ->whereDate('created_at', today())
            ->sum('quantity_in_base_unit');

        $stockOutToday = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '-'))
            ->whereDate('created_at', today())
            ->sum('quantity_in_base_unit');

        $netChangeToday = $stockInToday - $stockOutToday;
        $todayStockInCount = StockMovement::whereHas('movementType', fn($q) => $q->where('sign', '+'))->whereDate('created_at', today())->count();
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
            ->orderBy('date_needed', 'asc')
            ->limit(10)
            ->get();

        // Urgent requests (date needed today or tomorrow)
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
            $inventoryItem = $item->inventoryItem;
            $topItemsToday[] = [
                'name' => $inventoryItem->name ?? 'Unknown',
                'quantity' => $item->total
            ];
            if ($item->total > $topItemsTodayMax) $topItemsTodayMax = $item->total;
        }

        return view('store.dashboard', compact(
            'totalStockValue', 'stockValueChange',
            'stockInToday', 'stockOutToday', 'netChangeToday', 'todayStockInCount', 'todayIssuesCount', 'todayReturnsCount',
            'lowStockItems', 'outOfStockItems',
            'healthyStockCount', 'lowStockCount', 'outOfStockCount', 'totalActiveItems',
            'healthyStockPercent', 'lowStockPercent', 'outOfStockPercent',
            'pendingRequisitions', 'urgentRequests', 'todayActivities',
            'deptRequestLabels', 'deptRequestData',
            'topItemsToday', 'topItemsTodayMax'
        ));
    }
}
