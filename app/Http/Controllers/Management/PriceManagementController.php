<?php
// app/Http/Controllers/Management/PriceManagementController.php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PriceManagementController extends Controller
{
    /**
     * Display price management page with two tabs.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // Get all menu items (prepared dishes)
        $menuItems = MenuItem::orderBy('category')
            ->orderBy('name')
            ->get();

        // Get all ready-to-sell inventory items (is_sellable = true)
        $readyToSellItems = InventoryItem::where('is_active', true)
            ->where('is_sellable', true)
            ->orderBy('name')
            ->get();

        // Get all non-sellable inventory items (available to be made sellable)
        $nonSellableItems = InventoryItem::where('is_active', true)
            ->where(function($q) {
                $q->where('is_sellable', false)
                  ->orWhereNull('is_sellable');
            })
            ->orderBy('name')
            ->get();

        return view('management.prices.index', compact('menuItems', 'readyToSellItems', 'nonSellableItems'));
    }

    /**
     * Toggle sellable status for an inventory item (Make sellable / Unmake sellable)
     */
    public function toggleSellable($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $item = InventoryItem::findOrFail($id);
            $oldStatus = $item->is_sellable;
            $item->is_sellable = !$oldStatus;
            $item->updated_by = Auth::id();
            $item->save();

            Log::info('Inventory item sellable status toggled', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'item_name' => $item->name,
                'old_status' => $oldStatus,
                'new_status' => $item->is_sellable,
            ]);

            $statusText = $item->is_sellable ? 'added to sellable list' : 'removed from sellable list';

            return response()->json([
                'success' => true,
                'is_sellable' => $item->is_sellable,
                'message' => "Item '{$item->name}' {$statusText}",
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to toggle sellable status', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to update status'], 500);
        }
    }

    /**
     * Bulk make sellable (add multiple items to sellable list)
     */
    public function bulkMakeSellable(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inventory_items,id',
        ]);

        DB::beginTransaction();

        try {
            $updatedCount = 0;

            foreach ($request->items as $itemData) {
                $item = InventoryItem::find($itemData['id']);
                if ($item && !$item->is_sellable) {
                    $item->is_sellable = true;
                    $item->updated_by = Auth::id();
                    $item->save();
                    $updatedCount++;
                }
            }

            DB::commit();

            Log::info('Bulk make sellable completed', [
                'user_id' => Auth::id(),
                'updated_count' => $updatedCount,
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} items added to sellable list",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk make sellable failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to update items'], 500);
        }
    }

    /**
     * Bulk remove from sellable list
     */
    public function bulkRemoveSellable(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inventory_items,id',
        ]);

        DB::beginTransaction();

        try {
            $updatedCount = 0;

            foreach ($request->items as $itemData) {
                $item = InventoryItem::find($itemData['id']);
                if ($item && $item->is_sellable) {
                    $item->is_sellable = false;
                    $item->updated_by = Auth::id();
                    $item->save();
                    $updatedCount++;
                }
            }

            DB::commit();

            Log::info('Bulk remove sellable completed', [
                'user_id' => Auth::id(),
                'updated_count' => $updatedCount,
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} items removed from sellable list",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk remove sellable failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to update items'], 500);
        }
    }

    /**
     * Update menu item selling price.
     */
    public function updateMenuItemPrice(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'selling_price' => 'required|numeric|min:0',
        ]);

        try {
            $menuItem = MenuItem::findOrFail($id);
            $oldPrice = $menuItem->selling_price;
            $menuItem->selling_price = $request->selling_price;
            $menuItem->updated_by = Auth::id();
            $menuItem->save();

            Log::info('Menu item price updated', [
                'user_id' => Auth::id(),
                'menu_item_id' => $id,
                'menu_item_name' => $menuItem->name,
                'old_price' => $oldPrice,
                'new_price' => $request->selling_price,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Price updated successfully',
                'new_price' => number_format($request->selling_price, 2),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update menu item price', [
                'user_id' => Auth::id(),
                'menu_item_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to update price'], 500);
        }
    }

    /**
     * Update inventory item (ready-to-sell) selling price.
     */
    public function updateInventoryPrice(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'selling_price' => 'required|numeric|min:0',
        ]);

        try {
            $inventoryItem = InventoryItem::findOrFail($id);
            $oldPrice = $inventoryItem->selling_price;
            $inventoryItem->selling_price = $request->selling_price;
            $inventoryItem->updated_by = Auth::id();
            $inventoryItem->save();

            Log::info('Inventory item price updated', [
                'user_id' => Auth::id(),
                'inventory_item_id' => $id,
                'item_name' => $inventoryItem->name,
                'old_price' => $oldPrice,
                'new_price' => $request->selling_price,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Price updated successfully',
                'new_price' => number_format($request->selling_price, 2),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update inventory item price', [
                'user_id' => Auth::id(),
                'inventory_item_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to update price'], 500);
        }
    }
}
