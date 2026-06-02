<?php
// app/Http/Controllers/Performance/DailySaleController.php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\DailySale;
use App\Models\MenuItem;
use App\Models\Department;
use App\Models\PerformanceSummary;  // ADD THIS LINE
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailySaleController extends Controller
{
    /**
     * Display list of daily sales entries.
     */
    public function index(Request $request)
    {
        $query = DailySale::with(['menuItem', 'department', 'createdBy']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        $sales = $query->orderBy('sale_date', 'desc')->orderBy('created_at', 'desc')->paginate(20);

        $departments = Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE'])->get();
        $menuItems = MenuItem::where('is_active', true)->orderBy('name')->get();

        return view('performance.daily-sales.index', compact('sales', 'departments', 'menuItems'));
    }

    /**
     * Show form to create daily sales entry.
     */
    public function create()
    {
        $departments = Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE'])->get();
        $menuItems = MenuItem::where('is_active', true)->orderBy('name')->get();

        return view('performance.daily-sales.create', compact('departments', 'menuItems'));
    }

    /**
     * Store daily sales entry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'sale_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity_sold' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['items'] as $itemData) {
                $menuItem = MenuItem::find($itemData['menu_item_id']);

                // Get selling price from menu item
                $sellingPrice = $menuItem->selling_price;

                // Create daily sale record
                $dailySale = DailySale::create([
                    'menu_item_id' => $itemData['menu_item_id'],
                    'department_id' => $validated['department_id'],
                    'sale_date' => $validated['sale_date'],
                    'quantity_sold' => $itemData['quantity_sold'],
                    'unit_price' => $sellingPrice,
                    'created_by' => Auth::id(),
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Recalculate COGS and profit from recipe
                $dailySale->recalculateFromRecipe();
                $dailySale->save();

                Log::info('Daily sale recorded', [
                    'user_id' => Auth::id(),
                    'menu_item_id' => $itemData['menu_item_id'],
                    'quantity' => $itemData['quantity_sold'],
                    'cogs' => $dailySale->calculated_cogs,
                    'profit' => $dailySale->calculated_profit,
                ]);
            }

            DB::commit();

            return redirect()->route('performance.daily-sales.index')
                ->with('success', 'Daily sales recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to record daily sales', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to record sales: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show form to edit daily sales entry.
     */
    public function edit($id)
    {
        $sale = DailySale::with(['menuItem', 'department'])->findOrFail($id);
        $departments = Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE'])->get();
        $menuItems = MenuItem::where('is_active', true)->orderBy('name')->get();

        return view('performance.daily-sales.edit', compact('sale', 'departments', 'menuItems'));
    }

    /**
     * Update daily sales entry.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'sale_date' => 'required|date',
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity_sold' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $sale = DailySale::findOrFail($id);
            $menuItem = MenuItem::find($validated['menu_item_id']);

            $sale->update([
                'department_id' => $validated['department_id'],
                'sale_date' => $validated['sale_date'],
                'menu_item_id' => $validated['menu_item_id'],
                'quantity_sold' => $validated['quantity_sold'],
                'unit_price' => $menuItem->selling_price,
                'notes' => $validated['notes'] ?? null,
            ]);

            $sale->recalculateFromRecipe();
            $sale->save();

            DB::commit();

            return redirect()->route('performance.daily-sales.index')
                ->with('success', 'Daily sale updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete daily sales entry.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $sale = DailySale::findOrFail($id);
            $sale->delete();

            DB::commit();

            return redirect()->route('performance.daily-sales.index')
                ->with('success', 'Daily sale deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }

    /**
     * Generate performance summary from daily sales.
     */
    public function generateSummary(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'period_type' => 'required|in:daily,weekly,monthly',
        ]);

        try {
            // Check if summary already exists
            $existing = PerformanceSummary::where('department_id', $validated['department_id'])
                ->where('period_start', $validated['period_start'])
                ->where('period_end', $validated['period_end'])
                ->first();

            if ($existing) {
                return redirect()->route('performance.summaries.show', $existing->id)
                    ->with('info', 'Summary already exists for this period.');
            }

            // Generate new summary
            $summary = PerformanceSummary::generateFromDailySales(
                $validated['department_id'],
                $validated['period_start'],
                $validated['period_end'],
                $validated['period_type']
            );

            $summary->save();
            $summary->generateTopBottomItems();

            return redirect()->route('performance.summaries.show', $summary->id)
                ->with('success', 'Performance summary generated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to generate performance summary', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to generate summary: ' . $e->getMessage());
        }
    }
}
