<?php
// app/Http/Controllers/Procurement/CostPriceController.php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Batch;
use App\Models\CostPriceHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CostPriceController extends Controller
{
    /**
     * Display batch management page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = Batch::with('inventoryItem')->where('batch_status', 'active');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhereHas('inventoryItem', function($item) use ($search) {
                      $item->where('name', 'like', "%{$search}%")
                           ->orWhere('item_code', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by expiry status
        if ($request->filled('expiry_status')) {
            if ($request->expiry_status === 'expired') {
                $query->where('expiry_date', '<', now());
            } elseif ($request->expiry_status === 'expiring_soon') {
                $query->where('expiry_date', '<=', now()->addDays(30))
                      ->where('expiry_date', '>=', now());
            }
        }

        // Filter by batch status
        if ($request->filled('batch_status')) {
            $query->where('batch_status', $request->batch_status);
        }

        $batches = $query->orderBy('expiry_date', 'asc')->paginate(20);

        return view('procurement.cost-prices.index', compact('batches'));
    }

    /**
     * Show form to update batch for a single item.
     */
    public function edit($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $batch = Batch::with('inventoryItem')->findOrFail($id);
        $item = $batch->inventoryItem;

        // Get price history for this batch
        $priceHistory = CostPriceHistory::where('inventory_item_id', $item->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('procurement.cost-prices.edit', compact('batch', 'item', 'priceHistory'));
    }

    /**
     * Update batch unit cost for simple item (per unit).
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
            $batch = Batch::findOrFail($id);
            $oldCost = $batch->unit_cost;
            $newCost = $request->unit_cost;

            // Update batch cost
            $batch->unit_cost = $newCost;
            $batch->total_cost = $batch->remaining_quantity * $newCost;
            $batch->save();

            // Update inventory item average cost
            $item = $batch->inventoryItem;
            $activeBatches = Batch::where('inventory_item_id', $item->id)
                ->where('batch_status', 'active')
                ->where('remaining_quantity', '>', 0)
                ->get();

            $totalQty = $activeBatches->sum('remaining_quantity');
            $totalValue = $activeBatches->sum(function($b) {
                return $b->remaining_quantity * $b->unit_cost;
            });

            $avgCost = $totalQty > 0 ? $totalValue / $totalQty : $newCost;

            $item->unit_cost = $avgCost;
            $item->last_purchase_price = $newCost;
            $item->save();

            // Record history
            CostPriceHistory::create([
                'inventory_item_id' => $item->id,
                'old_unit_cost' => $oldCost,
                'new_unit_cost' => $newCost,
                'reason' => $request->reason ?? 'Manual batch cost update',
                'changed_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Batch cost price updated (simple)', [
                'user_id' => Auth::id(),
                'batch_id' => $id,
                'batch_number' => $batch->batch_number,
                'old_cost' => $oldCost,
                'new_cost' => $newCost,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Batch cost price updated from UGX " . number_format($oldCost, 2) . " to UGX " . number_format($newCost, 2),
                'new_cost' => number_format($newCost, 2),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update batch cost price', [
                'user_id' => Auth::id(),
                'batch_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to update batch cost price: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update batch cost price for bulk item (per pack).
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
            $batch = Batch::findOrFail($id);
            $oldCost = $batch->unit_cost;

            $packSize = $request->pack_size;
            $numberOfPacks = $request->number_of_packs;
            $packCost = $request->pack_cost;

            // Calculate new unit cost (cost per single unit)
            $totalBaseUnits = $packSize * $numberOfPacks;
            $newUnitCost = round($packCost / $totalBaseUnits, 2);

            // Update batch cost
            $batch->unit_cost = $newUnitCost;
            $batch->total_cost = $batch->remaining_quantity * $newUnitCost;
            $batch->save();

            // Update inventory item average cost
            $item = $batch->inventoryItem;
            $activeBatches = Batch::where('inventory_item_id', $item->id)
                ->where('batch_status', 'active')
                ->where('remaining_quantity', '>', 0)
                ->get();

            $totalQty = $activeBatches->sum('remaining_quantity');
            $totalValue = $activeBatches->sum(function($b) {
                return $b->remaining_quantity * $b->unit_cost;
            });

            $avgCost = $totalQty > 0 ? $totalValue / $totalQty : $newUnitCost;

            $item->unit_cost = $avgCost;
            $item->last_purchase_price = $newUnitCost;
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
                'reason' => $request->reason ?? 'Bulk batch cost update - ' . $numberOfPacks . ' ' . $request->pack_type . '(s)',
                'changed_by' => Auth::id(),
            ]);

            DB::commit();

            $message = sprintf(
                "Batch cost updated: %d %s(s) × %d = %d units @ UGX %s = UGX %.2f per unit",
                $numberOfPacks,
                $request->pack_type,
                $packSize,
                $totalBaseUnits,
                number_format($packCost, 2),
                $newUnitCost
            );

            Log::info('Batch cost price updated (bulk)', [
                'user_id' => Auth::id(),
                'batch_id' => $id,
                'batch_number' => $batch->batch_number,
                'old_cost' => $oldCost,
                'new_cost' => $newUnitCost,
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'new_unit_cost' => number_format($newUnitCost, 2),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update bulk batch cost price', [
                'user_id' => Auth::id(),
                'batch_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to update batch cost price: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get batch history for an item (AJAX).
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
     * Bulk update multiple batches.
     */
    public function bulkUpdate(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:batches,id',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $updatedCount = 0;

            foreach ($request->items as $itemData) {
                $batch = Batch::find($itemData['id']);
                if ($batch) {
                    $oldCost = $batch->unit_cost;
                    $newCost = $itemData['unit_cost'];

                    if ($oldCost != $newCost) {
                        $batch->unit_cost = $newCost;
                        $batch->total_cost = $batch->remaining_quantity * $newCost;
                        $batch->save();

                        // Update inventory item average cost
                        $item = $batch->inventoryItem;
                        $activeBatches = Batch::where('inventory_item_id', $item->id)
                            ->where('batch_status', 'active')
                            ->where('remaining_quantity', '>', 0)
                            ->get();

                        $totalQty = $activeBatches->sum('remaining_quantity');
                        $totalValue = $activeBatches->sum(function($b) {
                            return $b->remaining_quantity * $b->unit_cost;
                        });

                        $avgCost = $totalQty > 0 ? $totalValue / $totalQty : $newCost;

                        $item->unit_cost = $avgCost;
                        $item->save();

                        CostPriceHistory::create([
                            'inventory_item_id' => $item->id,
                            'old_unit_cost' => $oldCost,
                            'new_unit_cost' => $newCost,
                            'reason' => $request->reason ?? 'Bulk batch cost update',
                            'changed_by' => Auth::id(),
                        ]);

                        $updatedCount++;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} batch(es) updated successfully",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update batches'], 500);
        }
    }
}
