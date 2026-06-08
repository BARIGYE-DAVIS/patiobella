<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DepartmentRequisitionItem;
use App\Models\InventoryItem;
use App\Models\BusinessSetting;
use App\Models\MenuItem;
use App\Models\DepartmentStockMovement;
use App\Models\PerformanceReport;
use App\Models\PerformanceItem;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PerformanceReportExport;
use App\Models\RecipeItem;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceController extends Controller
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

    private function checkAuthorization()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return false;
        }

        $roleName = $this->getRoleName($user);
        if ($roleName !== 'General Manager' && $roleName !== 'Admin') {
            return false;
        }

        return true;
    }

    /**
     * Get OPENING stock for an inventory item (stock BEFORE the selected date)
     */
/**
 * Get OPENING stock for an inventory item (stock issued BEFORE the selected date)
 */
/**
 * Get OPENING stock for a department item (balance BEFORE the selected date)
 */
private function getOpeningStock($departmentId, $inventoryItemId, $date)
{
    $movement = DepartmentStockMovement::where('department_id', $departmentId)
        ->where('inventory_item_id', $inventoryItemId)
        ->where('movement_date', '<', $date)
        ->orderBy('movement_date', 'desc')
        ->orderBy('id', 'desc')
        ->first();

    return $movement ? (float)$movement->closing_balance : 0;
}

/**
 * Get ADDED stock for a department item (stock issued ON the selected date)
 */
private function getAddedStock($departmentId, $inventoryItemId, $date)
{
    $added = DepartmentStockMovement::where('department_id', $departmentId)
        ->where('inventory_item_id', $inventoryItemId)
        ->where('movement_date', $date)
        ->where('movement_type', 'issue')
        ->sum('added_quantity');

    return (float)$added;
}

    /**
     * Display list of performance reports
     */
    public function index(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $departments = Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE', 'RESTAURANT'])->get();

        $reports = PerformanceReport::with(['department', 'createdBy'])
            ->when($request->department_id, function($query) use ($request) {
                return $query->where('department_id', $request->department_id);
            })
            ->when($request->from_date, function($query) use ($request) {
                return $query->whereDate('report_date', '>=', $request->from_date);
            })
            ->when($request->to_date, function($query) use ($request) {
                return $query->whereDate('report_date', '<=', $request->to_date);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('management.performance.index', compact('departments', 'reports'));
    }

    /**
     * Show form for performance stock take
     */
    public function create(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $departments = Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE', 'RESTAURANT'])->get();

        return view('management.performance.create', compact('departments'));
    }

    /**
     * Get department data with menu items, recipes, and current stock
     */
    public function getDepartmentStockData($departmentId, Request $request)
    {
        try {
            $date = $request->get('date', date('Y-m-d'));

            $department = Department::find($departmentId);
            if (!$department) {
                return response()->json(['success' => false, 'message' => 'Department not found'], 404);
            }

            // Get all menu items for this department
            $menuItems = MenuItem::whereHas('menu', function($query) use ($departmentId) {
                    $query->where('department_id', $departmentId);
                })
                ->where('is_active', true)
                ->get();

            $menuItemsData = [];
            $stockItemsMap = [];

            foreach ($menuItems as $menuItem) {
                $recipeItems = RecipeItem::where('menu_item_id', $menuItem->id)
                    ->with('inventoryItem')
                    ->get();

                if ($recipeItems->isEmpty()) {
                    // Beverage item
                    $inventoryItemId = $menuItem->inventory_item_id;

                    if ($inventoryItemId) {
                        $inventoryItem = InventoryItem::find($inventoryItemId);
                        $openingStock = $this->getOpeningStock($departmentId, $inventoryItemId, $date);
                        $addedToday = $this->getAddedStock($departmentId, $inventoryItemId, $date);

                        $menuItemsData[] = [
                            'menu_item_id' => $menuItem->id,
                            'menu_item_name' => $menuItem->name,
                            'selling_price' => (float)$menuItem->selling_price,
                            'is_beverage' => true,
                            'ingredients' => [
                                [
                                    'inventory_item_id' => $inventoryItemId,
                                    'inventory_item_name' => $inventoryItem ? $inventoryItem->name : 'Unknown',
                                    'uom' => $inventoryItem ? ($inventoryItem->unit_of_measurement ?? 'piece') : 'piece',
                                    'quantity_required' => 1,
                                    'unit_cost' => (float)($menuItem->m_cost ?? 0),
                                ]
                            ]
                        ];

                        if (!isset($stockItemsMap[$inventoryItemId])) {
                            $stockItemsMap[$inventoryItemId] = [
                                'inventory_item_id' => $inventoryItemId,
                                'inventory_item_name' => $inventoryItem ? $inventoryItem->name : 'Unknown',
                                'uom' => $inventoryItem ? ($inventoryItem->unit_of_measurement ?? 'piece') : 'piece',
                                'opening_stock' => $openingStock,
                                'added_today' => $addedToday,
                                'unit_cost' => (float)($menuItem->m_cost ?? 0),
                            ];
                        } else {
                            $stockItemsMap[$inventoryItemId]['opening_stock'] = $openingStock;
                            $stockItemsMap[$inventoryItemId]['added_today'] = $addedToday;
                        }
                    }
                } else {
                    // Recipe item
                    $ingredients = [];

                    foreach ($recipeItems as $recipe) {
                        if ($recipe->inventoryItem) {
                            $inventoryItemId = $recipe->inventory_item_id;
                            $quantityRequired = (float)$recipe->quantity_required;
                            $openingStock = $this->getOpeningStock($departmentId, $inventoryItemId, $date);
                            $addedToday = $this->getAddedStock($departmentId, $inventoryItemId, $date);

                            $ingredients[] = [
                                'inventory_item_id' => $inventoryItemId,
                                'inventory_item_name' => $recipe->inventoryItem->name,
                                'uom' => $recipe->inventoryItem->unit_of_measurement ?? 'piece',
                                'quantity_required' => $quantityRequired,
                                'unit_cost' => (float)($menuItem->m_cost ?? 0),
                            ];

                            if (!isset($stockItemsMap[$inventoryItemId])) {
                                $stockItemsMap[$inventoryItemId] = [
                                    'inventory_item_id' => $inventoryItemId,
                                    'inventory_item_name' => $recipe->inventoryItem->name,
                                    'uom' => $recipe->inventoryItem->unit_of_measurement ?? 'piece',
                                    'opening_stock' => $openingStock,
                                    'added_today' => $addedToday,
                                    'unit_cost' => (float)($menuItem->m_cost ?? 0),
                                ];
                            } else {
                                $stockItemsMap[$inventoryItemId]['opening_stock'] = $openingStock;
                                $stockItemsMap[$inventoryItemId]['added_today'] = $addedToday;
                            }
                        }
                    }

                    $menuItemsData[] = [
                        'menu_item_id' => $menuItem->id,
                        'menu_item_name' => $menuItem->name,
                        'selling_price' => (float)$menuItem->selling_price,
                        'is_beverage' => false,
                        'ingredients' => $ingredients,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'items' => $menuItemsData,
                'stock_items' => array_values($stockItemsMap),
                'department_name' => $department->name,
            ]);

        } catch (\Exception $e) {
            Log::error('getDepartmentStockData error', [
                'department_id' => $departmentId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load department data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store performance stock take data with Gifts support
     */
    public function store(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'report_date' => 'required|date',
            'gifts_amount' => 'nullable|numeric|min:0',
            'sales_data' => 'required|array',
            'sales_data.*.menu_item_id' => 'required|exists:menu_items,id',
            'sales_data.*.quantity_sold' => 'required|numeric|min:0',
            'sales_data.*.selling_price' => 'required|numeric|min:0',
            'sales_data.*.ingredients' => 'required|array',
            'sales_data.*.ingredients.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'sales_data.*.ingredients.*.quantity_required' => 'required|numeric|min:0',
            'sales_data.*.ingredients.*.used_quantity' => 'required|numeric|min:0',
            'sales_data.*.ingredients.*.opening_stock' => 'required|numeric|min:0',
            'sales_data.*.ingredients.*.added_stock' => 'required|numeric|min:0',
            'sales_data.*.ingredients.*.closing_stock' => 'required|numeric|min:0',
            'sales_data.*.ingredients.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $department = Department::find($validated['department_id']);
            $isKitchen = ($department && $department->name === 'KITCHEN');
            $giftsAmount = (float)($validated['gifts_amount'] ?? 0);

            $totalSales = 0;
            $totalCogs = 0;
            $totalProfit = 0;

            // Create performance report with gifts
            $report = PerformanceReport::create([
                'report_number' => $this->generateReportNumber(),
                'department_id' => $validated['department_id'],
                'report_date' => $validated['report_date'],
                'total_sales' => 0,
                'total_cogs' => 0,
                'total_profit' => 0,
                'profit_margin' => 0,
                'gifts_amount' => $giftsAmount,
                'created_by' => Auth::id(),
                'status' => 'completed',
            ]);

            foreach ($validated['sales_data'] as $saleData) {
                $quantitySold = (float)$saleData['quantity_sold'];
                $sellingPrice = (float)$saleData['selling_price'];
                $salesAmount = $quantitySold * $sellingPrice;

                // Check if has recipe
                $hasRecipe = (count($saleData['ingredients']) > 1 ||
                             (count($saleData['ingredients']) == 1 && $saleData['ingredients'][0]['quantity_required'] != 1));

                $totalSales += $salesAmount;
                $menuItemCogs = 0;

                foreach ($saleData['ingredients'] as $ingredient) {
                    $inventoryItemId = $ingredient['inventory_item_id'];
                    $usedQuantity = (float)$ingredient['used_quantity'];
                    $unitCost = (float)$ingredient['unit_cost'];
                    $ingredientCogs = $usedQuantity * $unitCost;
                    $menuItemCogs += $ingredientCogs;

                    // Update department stock
                    if ($isKitchen) {
                        $this->updateDepartmentStock($validated['department_id'], $inventoryItemId, $usedQuantity, 'consumed');
                    } else {
                        if ($hasRecipe) {
                            $this->updateDepartmentStock($validated['department_id'], $inventoryItemId, $usedQuantity, 'consumed');
                        } else {
                            $this->updateDepartmentStock($validated['department_id'], $inventoryItemId, $usedQuantity, 'sold');
                        }
                    }

                    PerformanceItem::create([
                        'performance_report_id' => $report->id,
                        'menu_item_id' => $saleData['menu_item_id'],
                        'inventory_item_id' => $inventoryItemId,
                        'quantity_required' => $ingredient['quantity_required'],
                        'quantity_sold' => $quantitySold,
                        'used_quantity' => $usedQuantity,
                        'opening_stock' => $ingredient['opening_stock'],
                        'added_stock' => $ingredient['added_stock'],
                        'closing_stock' => $ingredient['closing_stock'],
                        'unit_cost' => $unitCost,
                        'selling_price' => $sellingPrice,
                        'cogs' => $ingredientCogs,
                        'sales_amount' => $salesAmount,
                        'profit' => 0,
                        'profit_margin' => 0,
                    ]);
                }

                $totalCogs += $menuItemCogs;
                $profit = $salesAmount - $menuItemCogs;
                $totalProfit += $profit;
            }

            // Update profit and margin for each performance item
            foreach ($validated['sales_data'] as $saleData) {
                $quantitySold = (float)$saleData['quantity_sold'];
                $sellingPrice = (float)$saleData['selling_price'];
                $salesAmount = $quantitySold * $sellingPrice;

                $itemCogs = PerformanceItem::where('performance_report_id', $report->id)
                    ->where('menu_item_id', $saleData['menu_item_id'])
                    ->sum('cogs');

                $profit = $salesAmount - $itemCogs;
                $profitMargin = $salesAmount > 0 ? ($profit / $salesAmount) * 100 : 0;

                PerformanceItem::where('performance_report_id', $report->id)
                    ->where('menu_item_id', $saleData['menu_item_id'])
                    ->update([
                        'profit' => $profit,
                        'profit_margin' => $profitMargin,
                        'sales_amount' => $salesAmount,
                    ]);
            }

            $profitMargin = $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0;

            // Calculate WITHOUT gifts
            $salesWithoutGifts = $totalSales - $giftsAmount;
            $profitWithoutGifts = $salesWithoutGifts - $totalCogs;
            $profitMarginWithoutGifts = $salesWithoutGifts > 0 ? ($profitWithoutGifts / $salesWithoutGifts) * 100 : 0;

            // Update report with all calculations
            $report->update([
                'total_sales' => $totalSales,
                'total_cogs' => $totalCogs,
                'total_profit' => $totalProfit,
                'profit_margin' => $profitMargin,
                'sales_without_gifts' => $salesWithoutGifts,
                'profit_without_gifts' => $profitWithoutGifts,
                'profit_margin_without_gifts' => $profitMarginWithoutGifts,
            ]);

            DB::commit();

            Log::info('Performance stock take saved', [
                'user_id' => Auth::id(),
                'report_id' => $report->id,
                'department_id' => $validated['department_id'],
                'gifts_amount' => $giftsAmount,
            ]);

            return response()->json([
                'success' => true,
                'redirect' => route('management.performance.show', $report->id),
                'message' => 'Performance stock take saved successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save performance stock take', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save performance stock take: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update department requisition items with used quantity
     */
    private function updateDepartmentStock($departmentId, $inventoryItemId, $usedQuantity, $type)
    {
        $requisitionItems = DepartmentRequisitionItem::whereHas('requisition', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId)
                      ->where('status', '!=', 'cancelled')
                      ->where('status', '!=', 'rejected')
                      ->where('status', '!=', 'completed');
            })
            ->where('inventory_item_id', $inventoryItemId)
            ->whereRaw('issued_total_pieces - (COALESCE(quantity_sold, 0) + COALESCE(quantity_consumed, 0) + COALESCE(quantity_returned, 0)) > 0')
            ->orderBy('created_at', 'asc')
            ->get();

        $remainingToDeduct = $usedQuantity;

        foreach ($requisitionItems as $reqItem) {
            if ($remainingToDeduct <= 0) break;

            $currentRemaining = $reqItem->issued_total_pieces -
                               ($reqItem->quantity_sold + $reqItem->quantity_consumed + $reqItem->quantity_returned);

            $deductFromThis = min($remainingToDeduct, $currentRemaining);

            if ($type === 'sold') {
                $reqItem->quantity_sold += $deductFromThis;
                $reqItem->last_sold_at = now();
            } else {
                $reqItem->quantity_consumed += $deductFromThis;
                $reqItem->last_consumed_at = now();
            }

            $reqItem->save();
            $remainingToDeduct -= $deductFromThis;
        }

        if ($remainingToDeduct > 0) {
            Log::warning("Could not fully deduct used quantity", [
                'department_id' => $departmentId,
                'inventory_item_id' => $inventoryItemId,
                'used_quantity' => $usedQuantity,
                'remaining_not_deducted' => $remainingToDeduct
            ]);
        }
    }

    /**
     * Show a specific performance report with Gift calculations
     */
    public function show($id)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $report = PerformanceReport::with(['department', 'createdBy', 'items.menuItem', 'items.inventoryItem'])
            ->findOrFail($id);

        // Calculate summaries
        $summaryWithGifts = [
            'sales' => $report->total_sales,
            'cogs' => $report->total_cogs,
            'gifts' => $report->gifts_amount ?? 0,
            'profit' => $report->total_profit,
            'cogs_percentage' => $report->total_sales > 0 ? ($report->total_cogs / $report->total_sales) * 100 : 0,
            'profit_margin' => $report->profit_margin,
        ];

        $summaryWithoutGifts = [
            'sales' => $report->sales_without_gifts ?? ($report->total_sales - ($report->gifts_amount ?? 0)),
            'cogs' => $report->total_cogs,
            'profit' => $report->profit_without_gifts ?? (($report->total_sales - ($report->gifts_amount ?? 0)) - $report->total_cogs),
            'cogs_percentage' => ($report->sales_without_gifts ?? ($report->total_sales - ($report->gifts_amount ?? 0))) > 0
                ? ($report->total_cogs / ($report->sales_without_gifts ?? ($report->total_sales - ($report->gifts_amount ?? 0)))) * 100
                : 0,
            'profit_margin' => $report->profit_margin_without_gifts ?? 0,
        ];

        return view('management.performance.show', compact('report', 'summaryWithGifts', 'summaryWithoutGifts'));
    }

    /**
     * Delete a performance report
     */
    public function destroy($id)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        try {
            $report = PerformanceReport::findOrFail($id);
            $report->delete();

            Log::info('Performance report deleted', [
                'user_id' => Auth::id(),
                'report_id' => $id,
            ]);

            return redirect()->route('management.performance.index')
                ->with('success', 'Performance report deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete performance report', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to delete performance report: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique report number
     */
    private function generateReportNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastReport = PerformanceReport::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReport) {
            $lastNumber = intval(substr($lastReport->report_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'PERF-' . $year . $month . '-' . $newNumber;
    }

    /**
     * Get chart data for dashboard
     */
    public function chartData(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $departmentId = $request->get('department_id');
        $days = $request->get('days', 30);

        $reports = PerformanceReport::with(['items.menuItem'])
            ->when($departmentId, function($query) use ($departmentId) {
                return $query->where('department_id', $departmentId);
            })
            ->where('report_date', '>=', now()->subDays($days))
            ->orderBy('report_date', 'asc')
            ->get();

        $chartData = [
            'dates' => [],
            'sales' => [],
            'sales_without_gifts' => [],
            'cogs' => [],
            'profit' => [],
            'profit_without_gifts' => [],
            'margins' => [],
            'margins_without_gifts' => [],
        ];

        foreach ($reports as $report) {
            $chartData['dates'][] = $report->report_date->format('Y-m-d');
            $chartData['sales'][] = $report->total_sales;
            $chartData['sales_without_gifts'][] = $report->sales_without_gifts ?? ($report->total_sales - ($report->gifts_amount ?? 0));
            $chartData['cogs'][] = $report->total_cogs;
            $chartData['profit'][] = $report->total_profit;
            $chartData['profit_without_gifts'][] = $report->profit_without_gifts ?? (($report->total_sales - ($report->gifts_amount ?? 0)) - $report->total_cogs);
            $chartData['margins'][] = round($report->profit_margin, 2);
            $chartData['margins_without_gifts'][] = round($report->profit_margin_without_gifts ?? 0, 2);
        }

        return response()->json([
            'success' => true,
            'data' => $chartData,
        ]);
    }

    /**
     * Export Performance Report to PDF
     */
    public function exportPdf($id)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $report = PerformanceReport::with(['department', 'createdBy', 'items.menuItem', 'items.inventoryItem'])
            ->findOrFail($id);

        // Get business settings for dynamic company info
        $businessSettings = [];
        $settings = BusinessSetting::where('is_active', true)->get();
        foreach($settings as $setting) {
            $businessSettings[$setting->key] = $setting->value;
        }

        // Calculate summaries
        $salesWithoutGifts = $report->sales_without_gifts ?? ($report->total_sales - ($report->gifts_amount ?? 0));
        $profitWithoutGifts = $report->profit_without_gifts ?? ($salesWithoutGifts - $report->total_cogs);
        $marginWithoutGifts = $report->profit_margin_without_gifts ?? ($salesWithoutGifts > 0 ? ($profitWithoutGifts / $salesWithoutGifts) * 100 : 0);

        // Group menu items
        $groupedItems = $report->items->groupBy('menu_item_id');

        // Calculate top moving stock items
        $ingredientUsage = [];
        foreach($report->items as $item) {
            $inventoryId = $item->inventory_item_id;
            if (!isset($ingredientUsage[$inventoryId])) {
                $ingredientUsage[$inventoryId] = [
                    'name' => $item->inventoryItem->name ?? 'N/A',
                    'uom' => $item->inventoryItem->unit_of_measurement ?? 'piece',
                    'used' => 0,
                    'cogs' => 0
                ];
            }
            $ingredientUsage[$inventoryId]['used'] += $item->used_quantity;
            $ingredientUsage[$inventoryId]['cogs'] += $item->cogs;
        }
        usort($ingredientUsage, function($a, $b) {
            return $b['used'] <=> $a['used'];
        });
        $topMovingItems = array_slice($ingredientUsage, 0, 10);

        // Calculate stock summary
        $stockSummary = [];
        foreach($report->items as $item) {
            $inventoryId = $item->inventory_item_id;
            if (!isset($stockSummary[$inventoryId])) {
                $stockSummary[$inventoryId] = [
                    'name' => $item->inventoryItem->name ?? 'N/A',
                    'uom' => $item->inventoryItem->unit_of_measurement ?? 'piece',
                    'opening' => $item->opening_stock,
                    'added' => $item->added_stock ?? 0,
                    'used' => 0,
                    'closing' => $item->closing_stock,
                ];
            }
            $stockSummary[$inventoryId]['used'] += $item->used_quantity;
        }

        $data = [
            'report' => $report,
            'groupedItems' => $groupedItems,
            'topMovingItems' => $topMovingItems,
            'stockSummary' => $stockSummary,
            'salesWithoutGifts' => $salesWithoutGifts,
            'profitWithoutGifts' => $profitWithoutGifts,
            'marginWithoutGifts' => $marginWithoutGifts,
            'generated_date' => now()->format('F d, Y H:i:s'),
            'generated_by' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'businessSettings' => $businessSettings,
        ];

        $pdf = Pdf::loadView('management.performance.export_pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Performance_Report_' . $report->report_number . '.pdf');
    }

    /**
     * Export Performance Report to Excel
     */
    public function exportExcel($id)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $report = PerformanceReport::with(['department', 'createdBy', 'items.menuItem', 'items.inventoryItem'])
            ->findOrFail($id);

        return Excel::download(new PerformanceReportExport($report), 'Performance_Report_' . $report->report_number . '.xlsx');
    }
}
