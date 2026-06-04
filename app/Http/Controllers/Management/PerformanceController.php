<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DepartmentRequisitionItem;
use App\Models\InventoryItem;
use App\Models\MenuItem;
use App\Models\PerformanceReport;
use App\Models\PerformanceItem;
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
     * Get current department stock for an inventory item
     */
    private function getDepartmentStock($departmentId, $inventoryItemId)
    {
        $stock = DepartmentRequisitionItem::whereHas('requisition', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId)
                      ->where('status', '!=', 'cancelled');
            })
            ->where('inventory_item_id', $inventoryItemId)
            ->select(DB::raw('
                COALESCE(SUM(issued_total_pieces), 0) -
                (COALESCE(SUM(quantity_sold), 0) +
                 COALESCE(SUM(quantity_consumed), 0) +
                 COALESCE(SUM(quantity_returned), 0)) as current_stock
            '))
            ->value('current_stock');

        return max(0, (float)$stock);
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

            $items = [];

            foreach ($menuItems as $menuItem) {
                // Get recipe ingredients for this menu item
                $recipeItems = RecipeItem::where('menu_item_id', $menuItem->id)
                    ->with('inventoryItem')
                    ->get();

                if ($recipeItems->isEmpty()) {
                    // Beverage item - has direct inventory_item_id
                    $inventoryItemId = $menuItem->inventory_item_id;

                    if ($inventoryItemId) {
                        $inventoryItem = InventoryItem::find($inventoryItemId);
                        $openingStock = $this->getDepartmentStock($departmentId, $inventoryItemId);

                        $items[] = [
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
                                    'opening_stock' => $openingStock,
                                ]
                            ]
                        ];
                    }
                } else {
                    // Recipe item - has multiple ingredients
                    $ingredients = [];

                    foreach ($recipeItems as $recipe) {
                        if ($recipe->inventoryItem) {
                            $inventoryItemId = $recipe->inventory_item_id;
                            $openingStock = $this->getDepartmentStock($departmentId, $inventoryItemId);

                            $ingredients[] = [
                                'inventory_item_id' => $inventoryItemId,
                                'inventory_item_name' => $recipe->inventoryItem->name,
                                'uom' => $recipe->inventoryItem->unit_of_measurement ?? 'piece',
                                'quantity_required' => (float)$recipe->quantity_required,
                                'unit_cost' => (float)($menuItem->m_cost ?? 0),
                                'opening_stock' => $openingStock,
                            ];
                        }
                    }

                    $items[] = [
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
                'items' => $items,
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
     * Store performance stock take data
     */
    public function store(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'report_date' => 'required|date',
            'sales_data' => 'required|array',
            'sales_data.*.menu_item_id' => 'required|exists:menu_items,id',
            'sales_data.*.quantity_sold' => 'required|numeric|min:0',
            'sales_data.*.selling_price' => 'required|numeric|min:0',
            'sales_data.*.ingredients' => 'required|array',
            'sales_data.*.ingredients.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'sales_data.*.ingredients.*.quantity_required' => 'required|numeric|min:0',
            'sales_data.*.ingredients.*.used_quantity' => 'required|numeric|min:0',
            'sales_data.*.ingredients.*.opening_stock' => 'required|numeric|min:0',
            'sales_data.*.ingredients.*.closing_stock' => 'required|numeric|min:0',
            'sales_data.*.ingredients.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $totalSales = 0;
            $totalCogs = 0;
            $totalProfit = 0;

            // Create performance report
            $report = PerformanceReport::create([
                'report_number' => $this->generateReportNumber(),
                'department_id' => $validated['department_id'],
                'report_date' => $validated['report_date'],
                'total_sales' => 0,
                'total_cogs' => 0,
                'total_profit' => 0,
                'profit_margin' => 0,
                'created_by' => Auth::id(),
                'status' => 'completed',
            ]);

            foreach ($validated['sales_data'] as $saleData) {
                $quantitySold = (float)$saleData['quantity_sold'];
                $sellingPrice = (float)$saleData['selling_price'];
                $salesAmount = $quantitySold * $sellingPrice;

                $totalSales += $salesAmount;

                $menuItemCogs = 0;

                foreach ($saleData['ingredients'] as $ingredient) {
                    $usedQuantity = (float)$ingredient['used_quantity'];
                    $unitCost = (float)$ingredient['unit_cost'];
                    $ingredientCogs = $usedQuantity * $unitCost;
                    $menuItemCogs += $ingredientCogs;

                    PerformanceItem::create([
                        'performance_report_id' => $report->id,
                        'menu_item_id' => $saleData['menu_item_id'],
                        'inventory_item_id' => $ingredient['inventory_item_id'],
                        'quantity_required' => $ingredient['quantity_required'],
                        'quantity_sold' => $quantitySold,
                        'used_quantity' => $usedQuantity,
                        'opening_stock' => $ingredient['opening_stock'],
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

            $profitMargin = $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0;

            $report->update([
                'total_sales' => $totalSales,
                'total_cogs' => $totalCogs,
                'total_profit' => $totalProfit,
                'profit_margin' => $profitMargin,
            ]);

            DB::commit();

            Log::info('Performance stock take saved', [
                'user_id' => Auth::id(),
                'report_id' => $report->id,
                'department_id' => $validated['department_id'],
            ]);

            return redirect()->route('management.performance.show', $report->id)
                ->with('success', 'Performance stock take saved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save performance stock take', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to save performance stock take: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show a specific performance report
     */
    public function show($id)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $report = PerformanceReport::with(['department', 'createdBy', 'items.menuItem', 'items.inventoryItem'])
            ->findOrFail($id);

        return view('management.performance.show', compact('report'));
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
}
