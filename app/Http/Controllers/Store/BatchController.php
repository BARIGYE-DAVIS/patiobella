<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Batch;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BatchController extends Controller
{
    /**
     * Display all batches across all items (index page).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = Batch::with('inventoryItem');

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

        // Filter by batch status
        if ($request->filled('batch_status')) {
            $query->where('batch_status', $request->batch_status);
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

        $batches = $query->orderBy('expiry_date', 'asc')->paginate(20);

        return view('store.batches.index', compact('batches'));
    }

    /**
     * Show a specific batch.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $batch = Batch::with('inventoryItem')->findOrFail($id);
        $item = $batch->inventoryItem;

        return view('store.batches.show', compact('batch', 'item'));
    }

    /**
     * Show form to edit a specific batch.
     */
    public function edit($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $batch = Batch::with('inventoryItem')->findOrFail($id);
        $item = $batch->inventoryItem;

        return view('store.batches.edit', compact('batch', 'item'));
    }

    /**
     * Update expiry date for a batch.
     */
    public function updateExpiry(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'expiry_date' => 'required|date',
            'reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $batch = Batch::findOrFail($id);
            $oldExpiry = $batch->expiry_date;
            $newExpiry = $request->expiry_date;

            $batch->expiry_date = $newExpiry;
            $batch->save();

            Log::info('Batch expiry date updated', [
                'user_id' => Auth::id(),
                'batch_id' => $id,
                'batch_number' => $batch->batch_number,
                'old_expiry' => $oldExpiry,
                'new_expiry' => $newExpiry,
                'reason' => $request->reason,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Expiry date updated from " . ($oldExpiry ?? 'None') . " to " . $newExpiry,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update expiry date: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update manufacture date for a batch.
     */
    public function updateManufactureDate(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'manufacture_date' => 'required|date',
            'reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $batch = Batch::findOrFail($id);
            $oldManufactureDate = $batch->manufacture_date;
            $newManufactureDate = $request->manufacture_date;

            $batch->manufacture_date = $newManufactureDate;
            $batch->save();

            Log::info('Batch manufacture date updated', [
                'user_id' => Auth::id(),
                'batch_id' => $id,
                'batch_number' => $batch->batch_number,
                'old_manufacture_date' => $oldManufactureDate,
                'new_manufacture_date' => $newManufactureDate,
                'reason' => $request->reason,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Manufacture date updated from " . ($oldManufactureDate ?? 'None') . " to " . $newManufactureDate,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update manufacture date: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Adjust remaining quantity for a batch with stock movement recording.
     */
    public function adjustQuantity(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $batch = Batch::findOrFail($id);
            $oldQuantity = $batch->remaining_quantity;
            $newQuantity = $oldQuantity;
            $quantityChange = 0;
            $movementTypeId = null;

            if ($request->adjustment_type === 'add') {
                $newQuantity = $oldQuantity + $request->quantity;
                $quantityChange = $request->quantity;
                $movementTypeId = 2; // Manual Stock In
            } elseif ($request->adjustment_type === 'subtract') {
                if ($request->quantity > $oldQuantity) {
                    throw new \Exception('Cannot subtract more than available quantity.');
                }
                $newQuantity = $oldQuantity - $request->quantity;
                $quantityChange = -$request->quantity;
                $movementTypeId = 3; // Manual Stock Out
            } else {
                $quantityChange = $request->quantity - $oldQuantity;
                $newQuantity = $request->quantity;
                $movementTypeId = $quantityChange > 0 ? 2 : 3;
            }

            $batch->remaining_quantity = $newQuantity;

            if ($newQuantity <= 0) {
                $batch->batch_status = 'depleted';
            } elseif ($newQuantity < $batch->initial_quantity) {
                $batch->batch_status = 'partially_used';
            } else {
                $batch->batch_status = 'active';
            }

            $batch->total_cost = $newQuantity * $batch->unit_cost;
            $batch->save();

            $item = $batch->inventoryItem;
            $totalStock = Batch::where('inventory_item_id', $item->id)
                ->where('batch_status', 'active')
                ->sum('remaining_quantity');

            $item->current_stock = $totalStock;
            $item->save();

            if ($quantityChange != 0) {
                $movementNumber = 'STK-ADJ-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $absQuantity = abs($quantityChange);

                StockMovement::create([
                    'movement_number' => $movementNumber,
                    'inventory_item_id' => $item->id,
                    'store_id' => 1,
                    'movement_type_id' => $movementTypeId,
                    'quantity' => $absQuantity,
                    'pack_type' => null,
                    'pack_size' => null,
                    'number_of_packs' => null,
                    'base_unit' => $batch->base_unit,
                    'unit_id' => null,
                    'quantity_in_base_unit' => $absQuantity,
                    'unit_cost' => $batch->unit_cost,
                    'total_value' => $absQuantity * $batch->unit_cost,
                    'reason' => $request->reason . ' (Batch: ' . $batch->batch_number . ')',
                    'movement_date' => now()->toDateString(),
                    'approved_at' => now(),
                    'approved_by' => Auth::id(),
                    'created_by' => Auth::id(),
                    'stock_before' => $oldQuantity,
                    'stock_after' => $newQuantity,
                ]);
            }

            Log::info('Batch quantity adjusted', [
                'user_id' => Auth::id(),
                'batch_id' => $id,
                'batch_number' => $batch->batch_number,
                'adjustment_type' => $request->adjustment_type,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'quantity_change' => $quantityChange,
                'reason' => $request->reason,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Quantity adjusted from {$oldQuantity} to {$newQuantity}",
                'new_quantity' => $newQuantity,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
