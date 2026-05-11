<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\StockMovement;
use App\Models\StockBalance;
use App\Models\ItemUnit;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'STORE') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $query = InventoryItem::with(['category']);

    // Live search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('item_code', 'like', "%{$search}%");
        });
    }

    $items = $query->orderBy('name')->paginate(20);

    // Return JSON for AJAX requests
    if ($request->ajax()) {
        $html = view('store.inventory.partials.table_rows', compact('items'))->render();
        $pagination = $items->appends(['search' => $request->search])->links()->toHtml();

        return response()->json([
            'html' => $html,
            'pagination' => $pagination,
            'total' => $items->total()
        ]);
    }

    return view('store.inventory.index', compact('items'));
}
    public function create()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            Log::warning('Unauthorized access to inventory create', [
                'user_id' => $user->id,
                'department' => $user->department->name ?? 'none',
            ]);
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $categories = Category::where('is_active', true)->orderBy('name')->get();
            $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
            $grns = GoodsReceivedNote::with(['vendor'])
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('store.inventory.create', compact('categories', 'vendors', 'grns'));
        } catch (\Exception $e) {
            Log::error('Failed to load inventory create form', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('store.inventory.index')->with('error', 'Failed to load create form.');
        }
    }

    public function getGrnItems($grnId)
    {
        try {
            $grnItems = GoodsReceivedNoteItem::with(['inventoryItem'])
                ->where('goods_received_note_id', $grnId)
                ->get();

            Log::info('GRN items fetched', [
                'user_id' => Auth::id(),
                'grn_id' => $grnId,
                'item_count' => $grnItems->count(),
            ]);

            return response()->json([
                'success' => true,
                'items' => $grnItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'inventory_item_id' => $item->inventory_item_id,
                        'item_name' => $item->inventoryItem->name ?? 'Unknown Item',
                        'quantity_accepted' => $item->quantity_accepted,
                        'unit_cost' => $item->unit_cost,
                        'total_cost' => $item->total_cost,
                        'metrics' => $item->inventoryItem->default_unit_of_measure_id ?? 'pcs',
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch GRN items', [
                'user_id' => Auth::id(),
                'grn_id' => $grnId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load GRN items: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function store(Request $request)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'STORE') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    // Get metrics to determine validation rules
    $metrics = $request->metrics;
    $bulkMetrics = ['box', 'carton', 'crate', 'dozen', 'pack', 'set'];

    $rules = [
        'item_name' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'item_code' => 'nullable|string|max:50|unique:inventory_items,item_code',
        'metrics' => 'required|string',
        'vendor_id' => 'nullable|exists:vendors,id',
        'notes' => 'nullable|string',
    ];

    if (in_array($metrics, $bulkMetrics)) {
        $rules['pieces_per_unit'] = 'required|numeric|min:1';
        $rules['number_of_units'] = 'required|numeric|min:1';
    } else {
        $rules['quantity'] = 'required|numeric|min:0.01';
    }

    $validated = $request->validate($rules);

    DB::beginTransaction();

    try {
        // Calculate total quantity based on metrics type
        $totalQuantity = 0;
        $packType = null;
        $packSize = null;
        $numberOfPacks = null;

        if (in_array($metrics, $bulkMetrics)) {
            $packSize = $validated['pieces_per_unit'];
            $numberOfPacks = $validated['number_of_units'];
            $totalQuantity = $packSize * $numberOfPacks;
            $packType = $metrics;
        } else {
            $totalQuantity = $validated['quantity'];
        }

        // Generate item code if not provided
        $itemCode = $validated['item_code'] ?? null;
        if (empty($itemCode)) {
            $itemCode = 'ITEM-' . strtoupper(uniqid());
        }

        $inventoryItem = InventoryItem::create([
            'item_code' => $itemCode,
            'name' => $validated['item_name'],
            'category_id' => $validated['category_id'],
            'default_unit_of_measure_id' => $metrics,
            'unit_cost' => 0,
            'current_stock' => $totalQuantity,
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        $movementNumber = 'STK-IN-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        StockMovement::create([
            'movement_number' => $movementNumber,
            'inventory_item_id' => $inventoryItem->id,
            'store_id' => 1,
            'movement_type_id' => 2,
            'quantity' => $numberOfPacks ?? $totalQuantity,
            'unit_id' => null,  // Set to null
            'quantity_in_base_unit' => $totalQuantity,
            'unit_cost' => 0,
            'total_value' => 0,
            'reason' => $validated['notes'] ?? 'Manual inventory entry',
            'movement_date' => now(),
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'created_by' => Auth::id(),
        ]);

        DB::commit();

        return redirect()->route('store.inventory.index')
            ->with('success', 'Inventory item created successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating inventory item: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Failed to create inventory item: ' . $e->getMessage())
            ->withInput();
    }
}


    public function storeFromGrn(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            Log::warning('Unauthorized store-from-GRN attempt', [
                'user_id' => $user->id,
                'department' => $user->department->name ?? 'none',
            ]);
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $request->validate([
                'grn_id'                       => 'required|exists:goods_received_notes,id',
                'items'                         => 'required|array',
                'items.*.grn_item_id'          => 'required|exists:goods_received_items,id',
                'items.*.inventory_item_id'    => 'required|exists:inventory_items,id',
                'items.*.quantity'             => 'required|numeric|min:0.01',
                'items.*.unit_cost'            => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Store-from-GRN validation failed', [
                'user_id' => Auth::id(),
                'grn_id' => $request->grn_id,
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        DB::beginTransaction();

        try {
            $grn = GoodsReceivedNote::findOrFail($request->grn_id);
            $totalReceived = 0;

            Log::info('Beginning GRN stock receipt', [
                'user_id' => Auth::id(),
                'grn_id' => $grn->id,
                'grn_number' => $grn->grn_number,
                'item_count' => count($request->items),
            ]);

            foreach ($request->items as $idx => $itemData) {
                $inventoryItem = InventoryItem::find($itemData['inventory_item_id']);

                if (!$inventoryItem) {
                    Log::warning('Inventory item not found during GRN receipt', [
                        'user_id' => Auth::id(),
                        'grn_id' => $grn->id,
                        'inventory_item_id' => $itemData['inventory_item_id'],
                        'index' => $idx,
                    ]);
                    continue;
                }

                $previousStock = $inventoryItem->current_stock ?? 0;
                $newStock = $previousStock + $itemData['quantity'];
                $inventoryItem->current_stock = $newStock;
                $inventoryItem->unit_cost = $itemData['unit_cost'];
                $inventoryItem->save();

                Log::info('Stock updated from GRN', [
                    'user_id' => Auth::id(),
                    'grn_id' => $grn->id,
                    'item_id' => $inventoryItem->id,
                    'item_name' => $inventoryItem->name,
                    'previous_stock' => $previousStock,
                    'quantity_added' => $itemData['quantity'],
                    'new_stock' => $newStock,
                ]);

                $movementNumber = 'STK-GRN-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

                $movement = StockMovement::create([
                    'movement_number'       => $movementNumber,
                    'inventory_item_id'     => $inventoryItem->id,
                    'store_id'              => 1,
                    'movement_type_id'      => 1,
                    'quantity'              => $itemData['quantity'],
                    'unit_id'              => null,
                    'quantity_in_base_unit' => $itemData['quantity'],
                    'unit_cost'             => $itemData['unit_cost'],
                    'total_value'           => $itemData['quantity'] * $itemData['unit_cost'],
                    'reason'                => 'Received from GRN: ' . $grn->grn_number,
                    'movement_date'         => now(),
                    'approved_at'           => now(),
                    'approved_by'           => Auth::id(),
                    'goods_received_note_id'=> $grn->id,
                    'created_by'            => Auth::id(),
                ]);

                Log::info('Stock movement created from GRN', [
                    'user_id' => Auth::id(),
                    'movement_id' => $movement->id,
                    'movement_number' => $movementNumber,
                    'grn_id' => $grn->id,
                    'item_id' => $inventoryItem->id,
                ]);

                $totalReceived += $itemData['quantity'];
            }

            $grn->status = 'inventory_updated';
            $grn->save();

            Log::info('GRN stock receipt completed', [
                'user_id' => Auth::id(),
                'grn_id' => $grn->id,
                'grn_number' => $grn->grn_number,
                'total_received' => $totalReceived,
            ]);

            DB::commit();

            return redirect()->route('store.inventory.index')
                ->with('success', 'Goods received from GRN and added to inventory successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing from GRN', [
                'user_id' => Auth::id(),
                'grn_id' => $request->grn_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to receive goods: ' . $e->getMessage());
        }
    }

   public function show(int $id)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'STORE') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $item = InventoryItem::with(['category', 'creator', 'updater'])->findOrFail($id);

    $stockMovements = StockMovement::where('inventory_item_id', $id)
        ->with(['movementType'])
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get();

    return view('store.inventory.show', compact('item', 'stockMovements'));
}
    public function edit($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            Log::warning('Unauthorized access to inventory edit', [
                'user_id' => $user->id,
                'item_id' => $id,
                'department' => $user->department->name ?? 'none',
            ]);
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $item = InventoryItem::findOrFail($id);
            $categories = Category::where('is_active', true)->orderBy('name')->get();
            $vendors = Vendor::where('status', 'active')->orderBy('name')->get();

            return view('store.inventory.edit', compact('item', 'categories', 'vendors'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Inventory item not found for edit', [
                'user_id' => Auth::id(),
                'item_id' => $id,
            ]);
            return redirect()->route('store.inventory.index')->with('error', 'Inventory item not found.');
        } catch (\Exception $e) {
            Log::error('Failed to load inventory edit form', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('store.inventory.index')->with('error', 'Failed to load edit form.');
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            Log::warning('Unauthorized inventory update attempt', [
                'user_id' => $user->id,
                'item_id' => $id,
                'department' => $user->department->name ?? 'none',
            ]);
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $item = InventoryItem::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Inventory item not found for update', [
                'user_id' => Auth::id(),
                'item_id' => $id,
            ]);
            return redirect()->route('store.inventory.index')->with('error', 'Inventory item not found.');
        }

        try {
            $validated = $request->validate([
                'name'          => 'required|string|max:255',
                'category_id'   => 'required|exists:categories,id',
                'item_code'     => 'nullable|string|max:50|unique:inventory_items,item_code,' . $id,
                'unit_cost'     => 'nullable|numeric|min:0',
                'selling_price' => 'nullable|numeric|min:0',
                'is_active'     => 'boolean',
                'notes'         => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Inventory update validation failed', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        try {
            $item->update([
                'name'          => $validated['name'],
                'category_id'   => $validated['category_id'],
                'item_code'     => $validated['item_code'] ?? $item->item_code,
                'unit_cost'     => $validated['unit_cost'] ?? $item->unit_cost,
                'selling_price' => $validated['selling_price'] ?? $item->selling_price,
                'is_active'     => $validated['is_active'] ?? $item->is_active,
                'notes'         => $validated['notes'] ?? $item->notes,
                'updated_by'    => Auth::id(),
            ]);

            Log::info('Inventory item updated', [
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'changes' => $item->getChanges(),
            ]);

            return redirect()->route('store.inventory.show', $item->id)
                ->with('success', 'Inventory item updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update inventory item', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update inventory item: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            Log::warning('Unauthorized inventory delete attempt', [
                'user_id' => $user->id,
                'item_id' => $id,
                'department' => $user->department->name ?? 'none',
            ]);
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $item = InventoryItem::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Inventory item not found for deletion', [
                'user_id' => Auth::id(),
                'item_id' => $id,
            ]);
            return redirect()->route('store.inventory.index')->with('error', 'Inventory item not found.');
        }

        try {
            if (StockMovement::where('inventory_item_id', $id)->exists()) {
                Log::warning('Attempted to delete inventory item with existing stock movements', [
                    'user_id' => Auth::id(),
                    'item_id' => $id,
                    'item_name' => $item->name,
                ]);
                return redirect()->route('store.inventory.index')
                    ->with('error', 'Cannot delete item that has stock movements.');
            }

            $item->delete();

            Log::info('Inventory item deleted', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'item_name' => $item->name,
            ]);

            return redirect()->route('store.inventory.index')
                ->with('success', 'Inventory item deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete inventory item', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('store.inventory.index')
                ->with('error', 'Failed to delete inventory item: ' . $e->getMessage());
        }
    }

    public function adjustStock(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            Log::warning('Unauthorized stock adjustment attempt', [
                'user_id' => $user->id,
                'item_id' => $id,
                'department' => $user->department->name ?? 'none',
            ]);
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $request->validate([
                'adjustment_type' => 'required|in:add,subtract',
                'quantity'        => 'required|numeric|min:0.01',
                'reason'          => 'required|string|max:500',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Stock adjustment validation failed', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        DB::beginTransaction();

        try {
            $item = InventoryItem::findOrFail($id);
            $quantity = $request->quantity;
            $previousStock = $item->current_stock ?? 0;

            if ($request->adjustment_type === 'add') {
                $newStock = $previousStock + $quantity;
                $movementTypeId = 2;
                $movementType = 'Stock Addition';
            } else {
                if ($previousStock < $quantity) {
                    Log::warning('Insufficient stock for adjustment', [
                        'user_id' => Auth::id(),
                        'item_id' => $id,
                        'item_name' => $item->name,
                        'current_stock' => $previousStock,
                        'requested_quantity' => $quantity,
                    ]);
                    throw new \Exception('Insufficient stock to subtract.');
                }
                $newStock = $previousStock - $quantity;
                $movementTypeId = 3;
                $movementType = 'Stock Reduction';
            }

            $item->current_stock = $newStock;
            $item->save();

            Log::info('Stock adjusted', [
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'item_name' => $item->name,
                'adjustment_type' => $request->adjustment_type,
                'previous_stock' => $previousStock,
                'quantity_adjusted' => $quantity,
                'new_stock' => $newStock,
                'reason' => $request->reason,
            ]);

            $movementNumber = 'STK-ADJ-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $movement = StockMovement::create([
                'movement_number'       => $movementNumber,
                'inventory_item_id'     => $item->id,
                'store_id'              => 1,
                'movement_type_id'      => $movementTypeId,
                'quantity'              => $quantity,
                'unit_id'              => null,
                'quantity_in_base_unit' => $quantity,
                'unit_cost'             => $item->unit_cost,
                'total_value'           => $quantity * ($item->unit_cost ?? 0),
                'reason'                => $request->reason . ' (Manual adjustment)',
                'movement_date'         => now(),
                'approved_at'           => now(),
                'approved_by'           => Auth::id(),
                'created_by'            => Auth::id(),
            ]);

            Log::info('Adjustment stock movement created', [
                'user_id' => Auth::id(),
                'movement_id' => $movement->id,
                'movement_number' => $movementNumber,
                'item_id' => $item->id,
            ]);

            DB::commit();

            return redirect()->route('store.inventory.show', $item->id)
                ->with('success', 'Stock adjusted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to adjust stock', [
                'user_id' => Auth::id(),
                'item_id' => $id,
                'adjustment_type' => $request->adjustment_type ?? null,
                'quantity' => $request->quantity ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
