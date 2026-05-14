<?php
// app/Http/Controllers/Procurement/CostPriceController.php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\CostPriceHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CostPriceController extends Controller
{
    /**
     * Display cost price management page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = InventoryItem::where('is_active', true);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $items = $query->orderBy('name')->paginate(20);

        // Get categories for filter
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

        // For each item, get latest price history
        foreach ($items as $item) {
            $item->priceHistory = CostPriceHistory::where('inventory_item_id', $item->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('procurement.cost-prices.index', compact('items', 'categories'));
    }

    /**
     * Show form to update cost price for a single item.
     */
    public function edit($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $item = InventoryItem::findOrFail($id);

        // Get price history
        $priceHistory = CostPriceHistory::where('inventory_item_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('procurement.cost-prices.edit', compact('item', 'priceHistory'));
    }

    /**
     * Update cost price for simple item (per unit).
     */
    public function updateSimple(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'unit_cost' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $item = InventoryItem::findOrFail($id);
            $oldCost = $item->unit_cost;
            $newCost = $request->unit_cost;

            // Update item cost
            $item->unit_cost = $newCost;
            $item->last_purchase_price = $newCost;
            $item->updated_by = Auth::id();
            $item->save();

            // Record history
            CostPriceHistory::create([
                'inventory_item_id' => $item->id,
                'old_unit_cost' => $oldCost,
                'new_unit_cost' => $newCost,
                'reason' => $request->reason ?? 'Manual cost update',
                'changed_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Cost price updated (simple)', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'item_name' => $item->name,
                'old_cost' => $oldCost,
                'new_cost' => $newCost,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Cost price updated from UGX " . number_format($oldCost, 2) . " to UGX " . number_format($newCost, 2),
                'new_cost' => number_format($newCost, 2),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update cost price', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to update cost price: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update cost price for bulk item (per pack).
     */
    public function updateBulk(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'pack_type' => 'required|string|max:50',
            'pack_size' => 'required|integer|min:1',
            'number_of_packs' => 'required|integer|min:1',
            'pack_cost' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $item = InventoryItem::findOrFail($id);
            $oldCost = $item->unit_cost;

            $packSize = $request->pack_size;
            $numberOfPacks = $request->number_of_packs;
            $packCost = $request->pack_cost;

            // Calculate new unit cost (cost per single unit)
            $totalBaseUnits = $packSize * $numberOfPacks;
            $newUnitCost = $packCost / $totalBaseUnits;

            // Round to 2 decimal places
            $newUnitCost = round($newUnitCost, 2);

            // Update item cost
            $item->unit_cost = $newUnitCost;
            $item->last_purchase_price = $newUnitCost;
            $item->default_unit_of_measure_id = $request->pack_type;
            $item->updated_by = Auth::id();
            $item->save();

            // Record history with pack details
            CostPriceHistory::create([
                'inventory_item_id' => $item->id,
                'old_unit_cost' => $oldCost,
                'new_unit_cost' => $newUnitCost,
                'pack_type' => $request->pack_type,
                'pack_size' => $packSize,
                'number_of_packs' => $numberOfPacks,
                'total_base_units' => $totalBaseUnits,
                'reason' => $request->reason ?? 'Bulk cost update - ' . $numberOfPacks . ' ' . $request->pack_type . '(s)',
                'changed_by' => Auth::id(),
            ]);

            DB::commit();

            $message = sprintf(
                "Cost updated: %d %s(s) × %d = %d units @ UGX %s = UGX %.2f per unit",
                $numberOfPacks,
                $request->pack_type,
                $packSize,
                $totalBaseUnits,
                number_format($packCost, 2),
                $newUnitCost
            );

            Log::info('Cost price updated (bulk)', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'item_name' => $item->name,
                'old_cost' => $oldCost,
                'new_cost' => $newUnitCost,
                'pack_type' => $request->pack_type,
                'pack_size' => $packSize,
                'number_of_packs' => $numberOfPacks,
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'new_unit_cost' => number_format($newUnitCost, 2),
                'new_unit_cost_raw' => $newUnitCost,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update bulk cost price', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to update cost price: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get price history for an item (AJAX).
     */
    public function getHistory($id)
    {
        try {
            $history = CostPriceHistory::where('inventory_item_id', $id)
                ->with('changedBy')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'history' => $history->map(function($h) {
                    return [
                        'date' => $h->created_at->format('Y-m-d H:i'),
                        'old_cost' => number_format($h->old_unit_cost, 2),
                        'new_cost' => number_format($h->new_unit_cost, 2),
                        'changed_by' => $h->changedBy->name ?? 'System',
                        'reason' => $h->reason,
                        'pack_info' => $h->pack_type ? $h->number_of_packs . ' ' . $h->pack_type . '(s) × ' . $h->pack_size . ' = ' . $h->total_base_units . ' units' : null,
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to load history'], 500);
        }
    }

    /**
     * Bulk update multiple items.
     */
    public function bulkUpdate(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inventory_items,id',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $updatedCount = 0;

            foreach ($request->items as $itemData) {
                $item = InventoryItem::find($itemData['id']);
                if ($item) {
                    $oldCost = $item->unit_cost;
                    $newCost = $itemData['unit_cost'];

                    if ($oldCost != $newCost) {
                        $item->unit_cost = $newCost;
                        $item->last_purchase_price = $newCost;
                        $item->updated_by = Auth::id();
                        $item->save();

                        CostPriceHistory::create([
                            'inventory_item_id' => $item->id,
                            'old_unit_cost' => $oldCost,
                            'new_unit_cost' => $newCost,
                            'reason' => $request->reason ?? 'Bulk cost update',
                            'changed_by' => Auth::id(),
                        ]);

                        $updatedCount++;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} item(s) updated successfully",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update items'], 500);
        }
    }
}
