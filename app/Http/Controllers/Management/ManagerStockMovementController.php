<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\StockMovementType;
use App\Models\DepartmentRequisition;
use App\Models\DepartmentRequisitionItem;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ManagerStockMovementController extends Controller
{
    /**
     * Display stock movements with distribution analytics and charts.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check if user is GENERAL MANAGEMENT
        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            // Base query for stock movements
            $query = StockMovement::with(['inventoryItem', 'movementType', 'approvedBy', 'createdBy']);

            // Apply filters
            if ($request->filled('date_from')) {
                $query->whereDate('movement_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('movement_date', '<=', $request->date_to);
            }
            if ($request->filled('item_id')) {
                $query->where('inventory_item_id', $request->item_id);
            }
            if ($request->filled('movement_type_id')) {
                $query->where('movement_type_id', $request->movement_type_id);
            }
            if ($request->filled('department_id')) {
                $query->where('department_id', $request->department_id);
            }

            // Get paginated movements
            $movements = $query->orderBy('movement_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Get distribution data (issues to departments)
            $distributionQuery = DepartmentRequisitionItem::with([
                'departmentRequisition',
                'departmentRequisition.department',
                'inventoryItem'
            ]);

            if ($request->filled('date_from')) {
                $distributionQuery->whereHas('departmentRequisition', function($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->date_from);
                });
            }
            if ($request->filled('date_to')) {
                $distributionQuery->whereHas('departmentRequisition', function($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->date_to);
                });
            }

            $distributions = $distributionQuery
                ->where('quantity_issued', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            // Get returns data
            $returnsQuery = DepartmentRequisitionItem::with([
                'departmentRequisition',
                'departmentRequisition.department',
                'inventoryItem'
            ])->where('quantity_returned', '>', 0);

            if ($request->filled('date_from')) {
                $returnsQuery->whereHas('departmentRequisition', function($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->date_from);
                });
            }
            if ($request->filled('date_to')) {
                $returnsQuery->whereHas('departmentRequisition', function($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->date_to);
                });
            }

            $returns = $returnsQuery
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            // Get stock additions (IN movements)
            $stockInQuery = StockMovement::with(['inventoryItem', 'movementType'])
                ->whereHas('movementType', function($q) {
                    $q->where('sign', '+');
                });

            if ($request->filled('date_from')) {
                $stockInQuery->whereDate('movement_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $stockInQuery->whereDate('movement_date', '<=', $request->date_to);
            }

            $stockAdditions = $stockInQuery
                ->orderBy('movement_date', 'desc')
                ->limit(50)
                ->get();

            // Summary statistics
            $totalStockIn = StockMovement::whereHas('movementType', function($q) {
                $q->where('sign', '+');
            })->sum('quantity_in_base_unit');

            $totalStockOut = StockMovement::whereHas('movementType', function($q) {
                $q->where('sign', '-');
            })->sum('quantity_in_base_unit');

            $netChange = $totalStockIn - $totalStockOut;

            // Department summaries
            $departmentSummaries = DepartmentRequisitionItem::select(
                'departments.id as department_id',
                'departments.name as department_name',
                DB::raw('SUM(department_requisition_items.issued_total_pieces) as total_issued'),
                DB::raw('SUM(department_requisition_items.returned_total_pieces) as total_returned'),
                DB::raw('SUM(department_requisition_items.quantity_consumed) as total_consumed')
            )
            ->join('department_requisitions', 'department_requisition_items.department_requisition_id', '=', 'department_requisitions.id')
            ->join('departments', 'department_requisitions.department_id', '=', 'departments.id')
            ->whereNull('department_requisitions.deleted_at')
            ->where('departments.name', '!=', 'STORE')
            ->groupBy('departments.id', 'departments.name')
            ->get();

            // ============================================================
            // CHART DATA - PIE CHART: IN vs OUT
            // ============================================================
            $inOutChart = [
                'labels' => ['Stock IN', 'Stock OUT'],
                'data' => [(float)$totalStockIn, (float)$totalStockOut],
                'colors' => ['#10b981', '#ef4444']
            ];

            // ============================================================
            // CHART DATA - DEPARTMENT CONSUMPTION PIE
            // ============================================================
            $deptConsumption = [
                'labels' => [],
                'data' => [],
                'colors' => ['#3b82f6', '#8b5cf6', '#ec489a', '#f59e0b', '#14b8a6']
            ];
            foreach ($departmentSummaries as $index => $dept) {
                $deptConsumption['labels'][] = $dept->department_name;
                $deptConsumption['data'][] = (float)$dept->total_consumed;
            }
            if (empty($deptConsumption['labels'])) {
                $deptConsumption = [
                    'labels' => ['No Data'],
                    'data' => [1],
                    'colors' => ['#9ca3af']
                ];
            }

            // ============================================================
            // CHART DATA - WEEKLY TREND (Last 7 days)
            // ============================================================
            $weeklyTrend = [
                'labels' => [],
                'in' => [],
                'out' => []
            ];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $weeklyTrend['labels'][] = now()->subDays($i)->format('M d');

                $in = StockMovement::whereHas('movementType', function($q) { $q->where('sign', '+'); })
                    ->whereDate('movement_date', $date)
                    ->sum('quantity_in_base_unit');

                $out = StockMovement::whereHas('movementType', function($q) { $q->where('sign', '-'); })
                    ->whereDate('movement_date', $date)
                    ->sum('quantity_in_base_unit');

                $weeklyTrend['in'][] = (float)$in;
                $weeklyTrend['out'][] = (float)$out;
            }

            // ============================================================
            // CHART DATA - TOP 5 MOVING ITEMS
            // ============================================================
            $topItems = StockMovement::select('inventory_item_id', DB::raw('SUM(quantity_in_base_unit) as total_moved'))
                ->with('inventoryItem')
                ->groupBy('inventory_item_id')
                ->orderBy('total_moved', 'desc')
                ->limit(5)
                ->get();

            $topItemsChart = [
                'labels' => [],
                'data' => [],
                'colors' => ['#f59e0b', '#ef4444', '#10b981', '#3b82f6', '#8b5cf6']
            ];
            foreach ($topItems as $index => $item) {
                $topItemsChart['labels'][] = $item->inventoryItem->name ?? 'Unknown';
                $topItemsChart['data'][] = (float)$item->total_moved;
            }

            // ============================================================
            // CHART DATA - STOCK IN SOURCE BREAKDOWN
            // ============================================================
            $stockInBySource = StockMovement::select('movement_type_id', DB::raw('SUM(quantity_in_base_unit) as total'))
                ->with('movementType')
                ->whereHas('movementType', function($q) { $q->where('sign', '+'); })
                ->groupBy('movement_type_id')
                ->get();

            $sourceChart = [
                'labels' => [],
                'data' => [],
                'colors' => ['#3b82f6', '#8b5cf6', '#ec489a', '#f59e0b', '#14b8a6']
            ];
            foreach ($stockInBySource as $index => $source) {
                $sourceChart['labels'][] = $source->movementType->name ?? 'Other';
                $sourceChart['data'][] = (float)$source->total;
            }
            if (empty($sourceChart['labels'])) {
                $sourceChart = [
                    'labels' => ['No Data'],
                    'data' => [1],
                    'colors' => ['#9ca3af']
                ];
            }

            // Filters data
            $items = InventoryItem::where('is_active', true)->orderBy('name')->get();
            $movementTypes = StockMovementType::where('is_active', true)->orderBy('name')->get();
            $departments = Department::where('is_active', true)
                ->where('name', '!=', 'STORE')
                ->orderBy('name')
                ->get();

            return view('management.stock_movements.index', compact(
                'movements',
                'distributions',
                'returns',
                'stockAdditions',
                'departmentSummaries',
                'totalStockIn',
                'totalStockOut',
                'netChange',
                'items',
                'movementTypes',
                'departments',
                'inOutChart',
                'deptConsumption',
                'weeklyTrend',
                'topItemsChart',
                'sourceChart'
            ));

        } catch (\Exception $e) {
            Log::error('Failed to load manager stock movements', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('management.dashboard')->with('error', 'Failed to load stock movements: ' . $e->getMessage());
        }
    }

    /**
     * Display single stock movement detail.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $movement = StockMovement::with([
                'inventoryItem',
                'inventoryItem.category',
                'movementType',
                'approvedBy',
                'createdBy',
                'updatedBy',
            ])->findOrFail($id);

            $relatedDistribution = null;
            if ($movement->movementType && $movement->movementType->sign === '-') {
                $relatedDistribution = DepartmentRequisitionItem::with(['departmentRequisition.department'])
                    ->where('issued_total_pieces', $movement->quantity_in_base_unit)
                    ->where('quantity_issued', '>', 0)
                    ->first();
            }

            $relatedReturn = null;
            if ($movement->movementType && $movement->movementType->name === 'RETURN') {
                $relatedReturn = DepartmentRequisitionItem::with(['departmentRequisition.department'])
                    ->where('returned_total_pieces', $movement->quantity_in_base_unit)
                    ->where('quantity_returned', '>', 0)
                    ->first();
            }

            return view('management.stock_movements.show', compact('movement', 'relatedDistribution', 'relatedReturn'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('management.stock-movements.index')->with('error', 'Stock movement not found.');
        } catch (\Exception $e) {
            Log::error('Failed to load stock movement detail', [
                'user_id' => Auth::id(),
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('management.stock-movements.index')->with('error', 'Failed to load stock movement details.');
        }
    }

    /**
     * Get distribution by department (AJAX)
     */
    public function getDistributionByDepartment(Request $request)
    {
        try {
            $query = DepartmentRequisitionItem::with([
                'departmentRequisition.department',
                'inventoryItem'
            ])->where('quantity_issued', '>', 0);

            if ($request->filled('date_from')) {
                $query->whereHas('departmentRequisition', function($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->date_from);
                });
            }
            if ($request->filled('date_to')) {
                $query->whereHas('departmentRequisition', function($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->date_to);
                });
            }
            if ($request->filled('department_id')) {
                $query->whereHas('departmentRequisition', function($q) use ($request) {
                    $q->where('department_id', $request->department_id);
                });
            }

            $distributions = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $distributions->map(function($item) {
                    return [
                        'date' => $item->created_at ? $item->created_at->format('Y-m-d') : 'N/A',
                        'requisition_number' => $item->departmentRequisition->requisition_number ?? 'N/A',
                        'department' => $item->departmentRequisition->department->name ?? 'N/A',
                        'item_name' => $item->inventoryItem->name ?? 'N/A',
                        'quantity_issued' => $item->quantity_issued,
                        'pack_type' => $item->issued_pack_type,
                        'pack_size' => $item->issued_pack_size,
                        'total_pieces' => $item->issued_total_pieces,
                        'taken_by' => $item->departmentRequisition->taken_by ?? 'N/A',
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export to Excel (CSV) with chart summaries
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $movements = $this->getExportData($request);
        $chartData = $this->getChartDataForExport($request);

        $exportData = [];

        foreach ($movements as $movement) {
            $movementType = $movement->movementType;
            $inventoryItem = $movement->inventoryItem;
            $isIn = $movementType && $movementType->sign === '+';
            $baseUnit = $movement->base_unit ?? ($inventoryItem->base_unit ?? 'units');

            $exportData[] = [
                'movement_number' => $movement->movement_number,
                'date' => $movement->movement_date ? $movement->movement_date->format('Y-m-d') : 'N/A',
                'item_name' => $inventoryItem->name ?? 'N/A',
                'item_code' => $inventoryItem->item_code ?? 'N/A',
                'type' => $movementType->name ?? 'N/A',
                'direction' => $isIn ? 'IN' : 'OUT',
                'quantity' => $movement->quantity_in_base_unit ?? 0,
                'unit' => $baseUnit,
                'stock_before' => $movement->stock_before ?? 0,
                'stock_after' => $movement->stock_after ?? 0,
                'unit_cost' => $movement->unit_cost ?? 0,
                'total_value' => $movement->total_value ?? 0,
                'reason' => $movement->reason ?? 'N/A',
                'taken_by' => $movement->taken_by ?? 'N/A',
                'returned_by' => $movement->returned_by ?? 'N/A',
                'created_by' => $movement->createdBy->name ?? 'System',
            ];
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_movements_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($exportData, $chartData) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            // Summary Section
            fputcsv($file, ['=== STOCK MOVEMENTS SUMMARY REPORT ===']);
            fputcsv($file, ['Generated on:', now()->format('F d, Y H:i:s')]);
            fputcsv($file, []);

            // Summary Statistics
            fputcsv($file, ['SUMMARY STATISTICS']);
            fputcsv($file, ['Total Stock IN (units):', $chartData['totalStockIn']]);
            fputcsv($file, ['Total Stock OUT (units):', $chartData['totalStockOut']]);
            fputcsv($file, ['Net Change (units):', $chartData['netChange']]);
            fputcsv($file, ['Total Value Moved (UGX):', number_format($chartData['totalValue'], 2)]);
            fputcsv($file, []);

            // Department Consumption
            fputcsv($file, ['DEPARTMENT CONSUMPTION']);
            fputcsv($file, ['Department', 'Issued (units)', 'Returned (units)', 'Consumed (units)']);
            foreach ($chartData['departmentSummaries'] as $dept) {
                fputcsv($file, [$dept->department_name, $dept->total_issued, $dept->total_returned, $dept->total_consumed]);
            }
            fputcsv($file, []);

            // Top Items
            fputcsv($file, ['TOP 5 MOVING ITEMS']);
            fputcsv($file, ['Item Name', 'Total Moved (units)']);
            foreach ($chartData['topItems'] as $item) {
                fputcsv($file, [$item['name'], $item['total']]);
            }
            fputcsv($file, []);

            // Detailed Movements
            fputcsv($file, ['=== DETAILED MOVEMENTS ===']);
            fputcsv($file, ['Movement #', 'Date', 'Item Name', 'Item Code', 'Type', 'Direction', 'Quantity', 'Unit', 'Stock BEFORE', 'Stock AFTER', 'Unit Cost (UGX)', 'Total Value (UGX)', 'Reason', 'Taken By', 'Returned By', 'Created By']);
            foreach ($exportData as $row) {
                fputcsv($file, array_values($row));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF with charts
     */
    /**
 * Export to PDF with all charts and data
 */
public function exportPdf(Request $request)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    // Get movements with filters
    $query = StockMovement::with(['inventoryItem', 'movementType', 'createdBy'])
        ->orderBy('movement_date', 'asc');

    if ($request->filled('date_from')) {
        $query->whereDate('movement_date', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('movement_date', '<=', $request->date_to);
    }
    if ($request->filled('item_id')) {
        $query->where('inventory_item_id', $request->item_id);
    }
    if ($request->filled('movement_type_id')) {
        $query->where('movement_type_id', $request->movement_type_id);
    }

    $movements = $query->get();

    // Prepare movements data for PDF
    $movementsData = [];
    foreach ($movements as $movement) {
        $movementType = $movement->movementType;
        $inventoryItem = $movement->inventoryItem;
        $isIn = $movementType && $movementType->sign === '+';
        $baseUnit = $movement->base_unit ?? ($inventoryItem->base_unit ?? 'units');

        $movementsData[] = (object)[
            'date' => $movement->movement_date ? $movement->movement_date->format('Y-m-d') : 'N/A',
            'movement_number' => $movement->movement_number,
            'item_name' => $inventoryItem->name ?? 'N/A',
            'item_code' => $inventoryItem->item_code ?? 'N/A',
            'type' => $movementType->name ?? 'N/A',
            'direction' => $isIn ? 'IN' : 'OUT',
            'quantity' => $movement->quantity_in_base_unit ?? 0,
            'unit' => $baseUnit,
            'stock_before' => $movement->stock_before ?? 0,
            'stock_after' => $movement->stock_after ?? 0,
            'unit_cost' => $movement->unit_cost ?? 0,
            'total_value' => $movement->total_value ?? 0,
            'reason' => $movement->reason ?? 'N/A',
            'taken_by' => $movement->taken_by ?? 'N/A',
            'returned_by' => $movement->returned_by ?? 'N/A',
        ];
    }

    // Calculate summary statistics with filters
    $stockInQuery = StockMovement::whereHas('movementType', function($q) { $q->where('sign', '+'); });
    $stockOutQuery = StockMovement::whereHas('movementType', function($q) { $q->where('sign', '-'); });

    if ($request->filled('date_from')) {
        $stockInQuery->whereDate('movement_date', '>=', $request->date_from);
        $stockOutQuery->whereDate('movement_date', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $stockInQuery->whereDate('movement_date', '<=', $request->date_to);
        $stockOutQuery->whereDate('movement_date', '<=', $request->date_to);
    }

    $totalStockIn = $stockInQuery->sum('quantity_in_base_unit');
    $totalStockOut = $stockOutQuery->sum('quantity_in_base_unit');
    $netChange = $totalStockIn - $totalStockOut;
    $totalValue = $movements->sum('total_value');

    // Department summaries
    $deptQuery = DepartmentRequisitionItem::select(
        'departments.name as department_name',
        DB::raw('SUM(department_requisition_items.issued_total_pieces) as total_issued'),
        DB::raw('SUM(department_requisition_items.returned_total_pieces) as total_returned'),
        DB::raw('SUM(department_requisition_items.quantity_consumed) as total_consumed')
    )
    ->join('department_requisitions', 'department_requisition_items.department_requisition_id', '=', 'department_requisitions.id')
    ->join('departments', 'department_requisitions.department_id', '=', 'departments.id')
    ->whereNull('department_requisitions.deleted_at')
    ->where('departments.name', '!=', 'STORE')
    ->groupBy('departments.name');

    if ($request->filled('date_from')) {
        $deptQuery->whereDate('department_requisitions.created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $deptQuery->whereDate('department_requisitions.created_at', '<=', $request->date_to);
    }

    $departmentSummaries = $deptQuery->get();

    // Weekly trend data
    $weeklyLabels = [];
    $weeklyInData = [];
    $weeklyOutData = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i)->format('Y-m-d');
        $weeklyLabels[] = now()->subDays($i)->format('M d');
        $in = StockMovement::whereHas('movementType', function($q) { $q->where('sign', '+'); })
            ->whereDate('movement_date', $date)
            ->sum('quantity_in_base_unit');
        $out = StockMovement::whereHas('movementType', function($q) { $q->where('sign', '-'); })
            ->whereDate('movement_date', $date)
            ->sum('quantity_in_base_unit');
        $weeklyInData[] = (float)$in;
        $weeklyOutData[] = (float)$out;
    }

    // Top 5 moving items
    $topItemsQuery = StockMovement::select('inventory_item_id', DB::raw('SUM(quantity_in_base_unit) as total_moved'))
        ->with('inventoryItem')
        ->groupBy('inventory_item_id')
        ->orderBy('total_moved', 'desc')
        ->limit(5);

    if ($request->filled('date_from')) {
        $topItemsQuery->whereDate('movement_date', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $topItemsQuery->whereDate('movement_date', '<=', $request->date_to);
    }

    $topItemsRaw = $topItemsQuery->get();
    $topItemsData = [];
    foreach ($topItemsRaw as $item) {
        $topItemsData[] = [
            'name' => $item->inventoryItem->name ?? 'Unknown',
            'total' => (float)$item->total_moved
        ];
    }

    // Stock IN by source
    $grnTotal = StockMovement::whereHas('movementType', function($q) { $q->where('name', 'LIKE', '%GRN%'); })
        ->when($request->filled('date_from'), fn($q) => $q->whereDate('movement_date', '>=', $request->date_from))
        ->when($request->filled('date_to'), fn($q) => $q->whereDate('movement_date', '<=', $request->date_to))
        ->sum('quantity_in_base_unit');

    $purchaseTotal = StockMovement::whereHas('movementType', function($q) { $q->where('name', 'LIKE', '%PURCHASE%'); })
        ->when($request->filled('date_from'), fn($q) => $q->whereDate('movement_date', '>=', $request->date_from))
        ->when($request->filled('date_to'), fn($q) => $q->whereDate('movement_date', '<=', $request->date_to))
        ->sum('quantity_in_base_unit');

    $returnTotal = StockMovement::whereHas('movementType', function($q) { $q->where('name', 'LIKE', '%RETURN%'); })
        ->when($request->filled('date_from'), fn($q) => $q->whereDate('movement_date', '>=', $request->date_from))
        ->when($request->filled('date_to'), fn($q) => $q->whereDate('movement_date', '<=', $request->date_to))
        ->sum('quantity_in_base_unit');

    $manualTotal = StockMovement::whereHas('movementType', function($q) { $q->where('name', 'LIKE', '%MANUAL%'); })
        ->when($request->filled('date_from'), fn($q) => $q->whereDate('movement_date', '>=', $request->date_from))
        ->when($request->filled('date_to'), fn($q) => $q->whereDate('movement_date', '<=', $request->date_to))
        ->sum('quantity_in_base_unit');

    $stockInBySource = [];
    if ($grnTotal > 0) $stockInBySource[] = ['name' => 'GRN Receipts', 'total' => $grnTotal];
    if ($purchaseTotal > 0) $stockInBySource[] = ['name' => 'Purchase Orders', 'total' => $purchaseTotal];
    if ($returnTotal > 0) $stockInBySource[] = ['name' => 'Returns from Depts', 'total' => $returnTotal];
    if ($manualTotal > 0) $stockInBySource[] = ['name' => 'Manual Adjustments', 'total' => $manualTotal];

    // Get distributions and returns for detail tables
    $distributionQuery = DepartmentRequisitionItem::with([
        'departmentRequisition.department',
        'inventoryItem'
    ])->where('quantity_issued', '>', 0);

    $returnsQuery = DepartmentRequisitionItem::with([
        'departmentRequisition.department',
        'inventoryItem'
    ])->where('quantity_returned', '>', 0);

    if ($request->filled('date_from')) {
        $distributionQuery->whereHas('departmentRequisition', function($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->date_from);
        });
        $returnsQuery->whereHas('departmentRequisition', function($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->date_from);
        });
    }
    if ($request->filled('date_to')) {
        $distributionQuery->whereHas('departmentRequisition', function($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->date_to);
        });
        $returnsQuery->whereHas('departmentRequisition', function($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->date_to);
        });
    }

    $distributions = $distributionQuery->orderBy('created_at', 'desc')->limit(50)->get();
    $returns = $returnsQuery->orderBy('created_at', 'desc')->limit(50)->get();

    // Stock additions for detail table
    $stockAdditionsQuery = StockMovement::with(['inventoryItem', 'movementType', 'createdBy'])
        ->whereHas('movementType', function($q) { $q->where('sign', '+'); });

    if ($request->filled('date_from')) {
        $stockAdditionsQuery->whereDate('movement_date', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $stockAdditionsQuery->whereDate('movement_date', '<=', $request->date_to);
    }

    $stockAdditions = $stockAdditionsQuery->orderBy('movement_date', 'desc')->limit(50)->get();

    // Department consumption data for chart
    $deptConsumptionData = [];
    $deptConsumptionLabels = [];
    foreach ($departmentSummaries as $dept) {
        $deptConsumptionLabels[] = $dept->department_name;
        $deptConsumptionData[] = (float)$dept->total_consumed;
    }

    // Prepare all chart data for the view
    $chartData = [
        'totalStockIn' => $totalStockIn,
        'totalStockOut' => $totalStockOut,
        'netChange' => $netChange,
        'totalValue' => $totalValue,
        'departmentSummaries' => $departmentSummaries,
        'weeklyTrend' => [
            'labels' => $weeklyLabels,
            'in' => $weeklyInData,
            'out' => $weeklyOutData,
        ],
        'topItems' => $topItemsData,
        'stockInBySource' => $stockInBySource,
        'deptConsumption' => [
            'labels' => $deptConsumptionLabels,
            'data' => $deptConsumptionData,
        ],
        'distributions' => $distributions,
        'returns' => $returns,
        'stockAdditions' => $stockAdditions,
    ];

    $pdf = Pdf::loadView('management.stock_movements.export_pdf', [
        'movements' => $movementsData,
        'export_date' => now()->format('F d, Y H:i:s'),
        'total_movements' => count($movementsData),
        'total_value' => $totalValue,
        'chartData' => $chartData,
    ]);

    return $pdf->download('stock_movements_' . date('Y-m-d') . '.pdf');
}
    /**
     * Private helper - get export data
     */
    private function getExportData(Request $request)
    {
        $query = StockMovement::with(['inventoryItem', 'movementType', 'createdBy'])
            ->orderBy('movement_date', 'asc');

        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }
        if ($request->filled('item_id')) {
            $query->where('inventory_item_id', $request->item_id);
        }
        if ($request->filled('movement_type_id')) {
            $query->where('movement_type_id', $request->movement_type_id);
        }

        return $query->get();
    }

    /**
     * Private helper - get chart data for export
     */
    private function getChartDataForExport(Request $request)
    {
        // Apply date filters to queries
        $stockInQuery = StockMovement::whereHas('movementType', function($q) { $q->where('sign', '+'); });
        $stockOutQuery = StockMovement::whereHas('movementType', function($q) { $q->where('sign', '-'); });

        if ($request->filled('date_from')) {
            $stockInQuery->whereDate('movement_date', '>=', $request->date_from);
            $stockOutQuery->whereDate('movement_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $stockInQuery->whereDate('movement_date', '<=', $request->date_to);
            $stockOutQuery->whereDate('movement_date', '<=', $request->date_to);
        }

        $totalStockIn = $stockInQuery->sum('quantity_in_base_unit');
        $totalStockOut = $stockOutQuery->sum('quantity_in_base_unit');
        $netChange = $totalStockIn - $totalStockOut;
        $totalValue = $stockInQuery->sum('total_value');

        // Department summaries
        $deptQuery = DepartmentRequisitionItem::select(
            'departments.name as department_name',
            DB::raw('SUM(department_requisition_items.issued_total_pieces) as total_issued'),
            DB::raw('SUM(department_requisition_items.returned_total_pieces) as total_returned'),
            DB::raw('SUM(department_requisition_items.quantity_consumed) as total_consumed')
        )
        ->join('department_requisitions', 'department_requisition_items.department_requisition_id', '=', 'department_requisitions.id')
        ->join('departments', 'department_requisitions.department_id', '=', 'departments.id')
        ->whereNull('department_requisitions.deleted_at')
        ->where('departments.name', '!=', 'STORE')
        ->groupBy('departments.name');

        if ($request->filled('date_from')) {
            $deptQuery->whereDate('department_requisitions.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $deptQuery->whereDate('department_requisitions.created_at', '<=', $request->date_to);
        }

        $departmentSummaries = $deptQuery->get();

        // Top items
        $topItemsQuery = StockMovement::select('inventory_item_id', DB::raw('SUM(quantity_in_base_unit) as total_moved'))
            ->with('inventoryItem')
            ->groupBy('inventory_item_id')
            ->orderBy('total_moved', 'desc')
            ->limit(5);

        if ($request->filled('date_from')) {
            $topItemsQuery->whereDate('movement_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $topItemsQuery->whereDate('movement_date', '<=', $request->date_to);
        }

        $topItemsRaw = $topItemsQuery->get();
        $topItems = [];
        foreach ($topItemsRaw as $item) {
            $topItems[] = [
                'name' => $item->inventoryItem->name ?? 'Unknown',
                'total' => $item->total_moved
            ];
        }

        return [
            'totalStockIn' => $totalStockIn,
            'totalStockOut' => $totalStockOut,
            'netChange' => $netChange,
            'totalValue' => $totalValue,
            'departmentSummaries' => $departmentSummaries,
            'topItems' => $topItems,
            'inOutChart' => [
                'labels' => ['Stock IN', 'Stock OUT'],
                'data' => [(float)$totalStockIn, (float)$totalStockOut]
            ],
            'deptConsumption' => [
                'labels' => $departmentSummaries->pluck('department_name')->toArray(),
                'data' => $departmentSummaries->pluck('total_consumed')->toArray()
            ]
        ];
    }
}
