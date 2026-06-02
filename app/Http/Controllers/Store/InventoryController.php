<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\Batch;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────────
    // Constants
    // ─────────────────────────────────────────────────────────────────────────────

    const BULK_METRICS   = ['box', 'carton', 'crate', 'dozen', 'pack', 'sack', 'set'];
    const SIMPLE_METRICS = ['kg', 'litres', 'pcs', 'grams', 'millilitres'];

    // ─────────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPER - Get total stock from batches
    // ─────────────────────────────────────────────────────────────────────────────

    private function getTotalStockFromBatches(int $inventoryItemId): float
    {
        return (float) Batch::where('inventory_item_id', $inventoryItemId)
            ->where('batch_status', 'active')
            ->sum('remaining_quantity');
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // BARCODE LOOKUP
    // ─────────────────────────────────────────────────────────────────────────────

    public function barcodeLookup(Request $request)
    {
        $request->validate(['barcode' => 'required|string|max:100']);

        $barcode = trim($request->barcode);

        $item = InventoryItem::with(['category'])
            ->where('barcode', $barcode)
            ->orWhere('item_code', $barcode)
            ->first();

        if (!$item) {
            return response()->json(['found' => false]);
        }

        // Get total stock from batches
        $totalStock = $this->getTotalStockFromBatches($item->id);

        return response()->json([
            'found' => true,
            'item'  => [
                'id'            => $item->id,
                'name'          => $item->name,
                'item_code'     => $item->item_code,
                'barcode'       => $item->barcode,
                'category_id'   => $item->category_id,
                'category_name' => $item->category?->name,
                'unit_of_measurement' => $item->unit_of_measurement ?? $item->base_unit,
                'current_stock' => $totalStock,
                'notes'         => $item->notes,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // INDEX - Show inventory list with stock from batches
    // ─────────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = InventoryItem::with(['category'])->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->paginate(20);

        // Add stock from batches to each item
        foreach ($items as $item) {
            $item->total_stock = $this->getTotalStockFromBatches($item->id);
        }

        if ($request->ajax()) {
            $html       = view('store.inventory.partials.table_rows', compact('items'))->render();
            $pagination = $items->appends(['search' => $request->search])->links()->toHtml();

            return response()->json([
                'html'       => $html,
                'pagination' => $pagination,
                'total'      => $items->total(),
            ]);
        }

        return view('store.inventory.index', compact('items'));
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // CREATE FORM
    // ─────────────────────────────────────────────────────────────────────────────

    public function create()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            Log::warning('Unauthorized access to inventory create', [
                'user_id'    => $user->id,
                'department' => $user->department->name ?? 'none',
            ]);
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $categories = Category::where('is_active', true)->orderBy('name')->get();
            $vendors    = Vendor::where('status', 'active')->orderBy('name')->get();
            $grns       = GoodsReceivedNote::with(['vendor'])
                            ->whereIn('status', ['completed'])
                            ->orderBy('created_at', 'desc')
                            ->get();

            return view('store.inventory.create', compact('categories', 'vendors', 'grns'));

        } catch (\Exception $e) {
            Log::error('Failed to load inventory create form', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return redirect()->route('store.inventory.index')
                ->with('error', 'Failed to load create form.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // STORE — Manual Entry (Creates inventory item AND batch)
    // ─────────────────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $metrics = $request->metrics;
        $isBulk  = in_array($metrics, self::BULK_METRICS);

        $rules = [
            'item_name'   => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'item_code'   => 'nullable|string|max:50|unique:inventory_items,item_code',
            'barcode'     => 'nullable|string|max:100|unique:inventory_items,barcode',
            'metrics'     => 'required|string',
            'unit_of_measurement' => 'required|string|max:50',
            'vendor_id'   => 'nullable|exists:vendors,id',
            'notes'       => 'nullable|string',
            'manufacture_date' => 'nullable|date',
            'expiry_date'      => 'nullable|date|after:manufacture_date',
            'unit_cost'        => 'nullable|numeric|min:0',
            'pack_cost'        => 'nullable|numeric|min:0',
            'pack_size'        => 'nullable|integer|min:1',
            'number_of_packs'  => 'nullable|integer|min:1',
        ];

        if ($isBulk) {
            $rules['number_of_packs'] = 'required|numeric|min:1';
            $rules['pack_size'] = 'required|numeric|min:1';
        } else {
            $rules['quantity'] = 'required|numeric|min:0.01';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $packType       = null;
            $packSize       = null;
            $numberOfPacks  = null;
            $totalQuantity  = 0;
            $unitCost       = 0;
            $packCost       = null;
            $totalCost      = 0;

            if ($isBulk) {
                $packType       = $metrics;
                $packSize       = (int) $validated['pack_size'];
                $numberOfPacks  = (int) $validated['number_of_packs'];
                $totalQuantity  = $packSize * $numberOfPacks;
                $packCost       = isset($validated['pack_cost']) ? (float) $validated['pack_cost'] : 0;
                $unitCost       = $packCost > 0 ? $packCost / $packSize : 0;
                $totalCost      = $packCost * $numberOfPacks;
            } else {
                $totalQuantity = (float) $validated['quantity'];
                $unitCost = (float) ($validated['unit_cost'] ?? 0);
                $totalCost = $totalQuantity * $unitCost;
            }

            $itemCode = $validated['item_code'] ?? null;
            if (empty($itemCode)) {
                $itemCode = 'ITEM-' . strtoupper(uniqid());
            }

            $barcode = !empty($validated['barcode']) ? $validated['barcode'] : null;

            // Create inventory item (NO stock fields)
            $inventoryItem = InventoryItem::create([
                'item_code'                  => $itemCode,
                'barcode'                    => $barcode,
                'name'                       => $validated['item_name'],
                'category_id'                => $validated['category_id'],
                'default_unit_of_measure_id' => $metrics,
                'unit_of_measurement'        => $validated['unit_of_measurement'],
                'notes'                      => $validated['notes'] ?? null,
                'is_active'                  => true,
                'created_by'                 => Auth::id(),
            ]);

            Log::info('Inventory item created (manual)', [
                'user_id'          => Auth::id(),
                'item_id'          => $inventoryItem->id,
                'item_name'        => $inventoryItem->name,
                'barcode'          => $barcode,
                'unit_of_measurement' => $validated['unit_of_measurement'],
            ]);

            // Create stock movement
            $movementNumber = 'STK-IN-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            StockMovement::create([
                'movement_number'       => $movementNumber,
                'inventory_item_id'     => $inventoryItem->id,
                'store_id'              => $request->store_id ?? 1,
                'movement_type_id'      => 2,
                'quantity'              => $isBulk ? $numberOfPacks : $totalQuantity,
                'pack_type'             => $packType,
                'pack_size'             => $packSize,
                'number_of_packs'       => $numberOfPacks,
                'unit_of_measurement'   => $validated['unit_of_measurement'],
                'quantity_in_base_unit' => $totalQuantity,
                'unit_cost'             => $unitCost,
                'total_value'           => $totalCost,
                'reason'                => $validated['notes'] ?? 'Manual inventory entry',
                'movement_date'         => now(),
                'approved_at'           => now(),
                'approved_by'           => Auth::id(),
                'created_by'            => Auth::id(),
                'stock_before'          => 0,
                'stock_after'           => $totalQuantity,
            ]);

            // Create batch for this stock
            $batchNumber = 'BAT-MAN-' . date('Ymd') . '-' . str_pad($inventoryItem->id, 6, '0', STR_PAD_LEFT);

            Batch::create([
                'batch_number' => $batchNumber,
                'inventory_item_id' => $inventoryItem->id,
                'goods_received_note_id' => null,
                'supplier_id' => $validated['vendor_id'] ?? null,
                'initial_quantity' => $totalQuantity,
                'remaining_quantity' => $totalQuantity,
                'unit_cost' => $unitCost,
                'pack_cost' => $packCost,
                'total_cost' => $totalCost,
                'unit_of_measurement' => $validated['unit_of_measurement'],
                'pack_type' => $packType,
                'pack_size' => $packSize,
                'number_of_packs' => $numberOfPacks,
                'total_quantity' => $totalQuantity,
                'manufacture_date' => $request->manufacture_date ?? null,
                'expiry_date' => $request->expiry_date ?? null,
                'batch_status' => 'active',
                'notes' => $validated['notes'] ?? 'Manual stock entry',
            ]);

            DB::commit();

            return redirect()->route('store.inventory.index')
                ->with('success', "Inventory item created successfully. {$totalQuantity} {$validated['unit_of_measurement']}(s) added to stock.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating inventory item', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create inventory item: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // GET GRN ITEMS (AJAX)
    // ─────────────────────────────────────────────────────────────────────────────

    public function getGrnItems($grnId)
    {
        try {
            $grnItems = GoodsReceivedNoteItem::with(['inventoryItem.category'])
                ->where('goods_received_note_id', $grnId)
                ->get();

            return response()->json([
                'success' => true,
                'items'   => $grnItems->map(function ($item) {
                    $inv = $item->inventoryItem;
                    return [
                        'id'                    => $item->id,
                        'inventory_item_id'     => $item->inventory_item_id,
                        'item_name'             => $inv->name ?? 'Unknown Item',
                        'item_code'             => $inv->item_code ?? '',
                        'barcode'               => $inv->barcode ?? '',
                        'category'              => $inv->category->name ?? '',
                        'quantity_accepted'     => $item->quantity_accepted,
                        'unit_cost'             => $item->unit_cost,
                        'total_cost'            => $item->total_cost,
                        'unit_of_measurement'   => $inv->unit_of_measurement ?? 'pcs',
                        'pack_type'             => $item->pack_type,
                        'pack_size'             => $item->pack_size,
                        'number_of_packs'       => $item->number_of_packs,
                        'quantity_in_base_unit' => $item->quantity_in_base_unit,
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch GRN items', [
                'user_id' => Auth::id(),
                'grn_id'  => $grnId,
                'error'   => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to load GRN items'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // STORE FROM GRN — Creates batches (inventory item stock NOT updated)
    // ─────────────────────────────────────────────────────────────────────────────

    public function storeFromGrn(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'grn_id'                              => 'required|exists:goods_received_notes,id',
            'items'                               => 'required|array|min:1',
            'items.*.grn_item_id'                 => 'required|exists:goods_received_items,id',
            'items.*.inventory_item_id'           => 'required|exists:inventory_items,id',
            'items.*.receiving_metrics'           => 'required|string',
            'items.*.unit_of_measurement'         => 'required|string|max:50',
            'items.*.quantity'                    => 'required|numeric|min:0.01',
            'items.*.unit_cost'                   => 'required|numeric|min:0',
            'items.*.pack_size'                   => 'nullable|numeric|min:1',
            'items.*.pack_cost'                   => 'nullable|numeric|min:0',
            'manufacture_date'                    => 'nullable|date',
            'expiry_date'                         => 'nullable|date|after:manufacture_date',
        ]);

        DB::beginTransaction();

        try {
            $grn = GoodsReceivedNote::findOrFail($request->grn_id);
            $itemsSummary = [];

            foreach ($request->items as $idx => $itemData) {
                $inventoryItem = InventoryItem::find($itemData['inventory_item_id']);
                if (!$inventoryItem) continue;

                $receivingMetrics = trim($itemData['receiving_metrics']);
                $isBulk = in_array($receivingMetrics, self::BULK_METRICS);
                $quantityReceived = (float) $itemData['quantity'];
                $unitOfMeasurement = trim($itemData['unit_of_measurement']);
                $unitCost = (float) $itemData['unit_cost'];

                $packSize = null;
                $packCost = null;
                $numberOfPacks = null;
                $totalQuantity = $quantityReceived;

                if ($isBulk) {
                    $packSize = isset($itemData['pack_size']) ? (int) $itemData['pack_size'] : 0;
                    $packCost = isset($itemData['pack_cost']) ? (float) $itemData['pack_cost'] : 0;
                    $numberOfPacks = (int) $quantityReceived;
                    $totalQuantity = $packSize * $numberOfPacks;

                    if ($packSize < 1) {
                        throw new \Exception('Pack size must be at least 1 for item: ' . $inventoryItem->name);
                    }
                }

                // Update inventory item master data (but NOT stock)
                $inventoryItem->unit_of_measurement = $unitOfMeasurement;
                $inventoryItem->default_unit_of_measure_id = $receivingMetrics;
                $inventoryItem->updated_by = Auth::id();
                $inventoryItem->save();

                // Update GRN item
                $grnItem = GoodsReceivedNoteItem::find($itemData['grn_item_id']);
                if ($grnItem) {
                    $grnItem->pack_type = $isBulk ? $receivingMetrics : null;
                    $grnItem->pack_size = $packSize;
                    $grnItem->number_of_packs = $isBulk ? $numberOfPacks : null;
                    $grnItem->quantity_in_base_unit = $totalQuantity;
                    $grnItem->unit_of_measurement = $unitOfMeasurement;
                    $grnItem->updated_by = Auth::id();
                    $grnItem->save();
                }

                // Create stock movement
                $movementNumber = 'STK-GRN-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

                StockMovement::create([
                    'movement_number'        => $movementNumber,
                    'inventory_item_id'      => $inventoryItem->id,
                    'store_id'               => 1,
                    'movement_type_id'       => 1,
                    'quantity'               => $quantityReceived,
                    'pack_type'              => $isBulk ? $receivingMetrics : null,
                    'pack_size'              => $packSize,
                    'number_of_packs'        => $isBulk ? $numberOfPacks : null,
                    'unit_of_measurement'    => $unitOfMeasurement,
                    'quantity_in_base_unit'  => $totalQuantity,
                    'unit_cost'              => $unitCost,
                    'total_value'            => $totalQuantity * $unitCost,
                    'reason'                 => 'Received from GRN: ' . $grn->grn_number,
                    'movement_date'          => now(),
                    'approved_at'            => now(),
                    'approved_by'            => Auth::id(),
                    'goods_received_note_id' => $grn->id,
                    'created_by'             => Auth::id(),
                    'stock_before'           => 0,
                    'stock_after'            => $totalQuantity,
                ]);

                // Create batch for this receipt
                $batchNumber = 'BAT-GRN-' . date('Ymd') . '-' . str_pad($inventoryItem->id, 6, '0', STR_PAD_LEFT) . '-' . str_pad($idx, 3, '0', STR_PAD_LEFT);

                Batch::create([
                    'batch_number' => $batchNumber,
                    'inventory_item_id' => $inventoryItem->id,
                    'goods_received_note_id' => $grn->id,
                    'supplier_id' => $grn->vendor_id,
                    'initial_quantity' => $totalQuantity,
                    'remaining_quantity' => $totalQuantity,
                    'unit_cost' => $unitCost,
                    'pack_cost' => $packCost,
                    'total_cost' => $totalQuantity * $unitCost,
                    'unit_of_measurement' => $unitOfMeasurement,
                    'pack_type' => $isBulk ? $receivingMetrics : null,
                    'pack_size' => $packSize,
                    'number_of_packs' => $numberOfPacks,
                    'total_quantity' => $totalQuantity,
                    'manufacture_date' => $request->manufacture_date ?? null,
                    'expiry_date' => $request->expiry_date ?? null,
                    'batch_status' => 'active',
                    'notes' => 'Received from GRN: ' . $grn->grn_number,
                ]);

                $itemsSummary[] = "{$inventoryItem->name}: +{$totalQuantity} {$unitOfMeasurement}(s)";
            }

            $grn->status = 'inventory_updated';
            $grn->updated_by = Auth::id();
            $grn->save();

            DB::commit();

            $summary = implode(', ', array_slice($itemsSummary, 0, 5));
            if (count($itemsSummary) > 5) {
                $summary .= ' … and ' . (count($itemsSummary) - 5) . ' more';
            }

            return redirect()->route('store.inventory.index')
                ->with('success', "GRN {$grn->grn_number} received into inventory. {$summary}.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error receiving goods from GRN', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to receive goods: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // SHOW - Display item with batch information
    // ─────────────────────────────────────────────────────────────────────────────

    public function show(int $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $item = InventoryItem::with(['category', 'creator', 'updater'])->findOrFail($id);

        // Get all batches for this item
        $batches = Batch::where('inventory_item_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalStock = $batches->where('batch_status', 'active')->sum('remaining_quantity');

        return view('store.inventory.show', compact('item', 'batches', 'totalStock'));
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $item       = InventoryItem::findOrFail($id);
            $categories = Category::where('is_active', true)->orderBy('name')->get();
            $vendors    = Vendor::where('status', 'active')->orderBy('name')->get();

            $unitsOfMeasure = \App\Models\UnitOfMeasure::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            $existingUnits = InventoryItem::where('is_active', true)
                ->whereNotNull('unit_of_measurement')
                ->distinct()
                ->orderBy('unit_of_measurement')
                ->pluck('unit_of_measurement')
                ->toArray();

            return view('store.inventory.edit', compact('item', 'categories', 'vendors', 'unitsOfMeasure', 'existingUnits'));

        } catch (\Exception $e) {
            return redirect()->route('store.inventory.index')->with('error', 'Failed to load edit form.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────────────────────────

public function update(Request $request, $id)
{
    // ... auth check ...

    $item = InventoryItem::findOrFail($id);

    $validated = $request->validate([
        // Remove name, category_id, unit_of_measurement — they're read-only
        'empty_bottle_weight' => 'nullable|numeric|min:0',
        'selling_price'       => 'nullable|numeric|min:0',
        'is_active'           => 'nullable|boolean',
        'notes'               => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $item->update([
            // Keep existing values for read-only fields
            'empty_bottle_weight' => $validated['empty_bottle_weight'] ?? $item->empty_bottle_weight,
            'selling_price'       => $validated['selling_price'] ?? $item->selling_price,
            'is_active'           => $request->has('is_active') ? 1 : 0,
            'notes'               => $validated['notes'] ?? $item->notes,
            'updated_by'          => Auth::id(),
        ]);

        DB::commit();

        return redirect()->route('store.inventory.show', $item->id)
            ->with('success', 'Inventory item updated successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Failed to update: ' . $e->getMessage())
            ->withInput();
    }
}
    // ─────────────────────────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $item = InventoryItem::findOrFail($id);

            // Check if there are any batches
            if (Batch::where('inventory_item_id', $id)->exists()) {
                return redirect()->route('store.inventory.index')
                    ->with('error', 'Cannot delete item that has existing batches.');
            }

            $item->delete();

            return redirect()->route('store.inventory.index')
                ->with('success', 'Inventory item deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->route('store.inventory.index')
                ->with('error', 'Failed to delete inventory item: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // ADJUST STOCK - Affects batches directly
    // ─────────────────────────────────────────────────────────────────────────────

    public function adjustStock(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity'        => 'required|numeric|min:0.01',
            'batch_id'        => 'nullable|exists:batches,id',
            'reason'          => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $item = InventoryItem::findOrFail($id);

            // If specific batch is selected, adjust that batch
            if ($request->filled('batch_id')) {
                $batch = Batch::findOrFail($request->batch_id);
                $oldQuantity = $batch->remaining_quantity;
                $newQuantity = $oldQuantity;
                $quantityChange = 0;

                if ($request->adjustment_type === 'add') {
                    $newQuantity = $oldQuantity + $request->quantity;
                    $quantityChange = $request->quantity;
                } elseif ($request->adjustment_type === 'subtract') {
                    if ($request->quantity > $oldQuantity) {
                        throw new \Exception('Insufficient stock in batch.');
                    }
                    $newQuantity = $oldQuantity - $request->quantity;
                    $quantityChange = -$request->quantity;
                } else {
                    $quantityChange = $request->quantity - $oldQuantity;
                    $newQuantity = $request->quantity;
                }

                $batch->remaining_quantity = $newQuantity;
                if ($newQuantity <= 0) {
                    $batch->batch_status = 'depleted';
                } elseif ($newQuantity < $batch->initial_quantity) {
                    $batch->batch_status = 'partially_used';
                }
                $batch->save();

                // Create stock movement
                $movementNumber = 'STK-ADJ-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $movementTypeId = $quantityChange > 0 ? 2 : 3;

                StockMovement::create([
                    'movement_number'       => $movementNumber,
                    'inventory_item_id'     => $item->id,
                    'store_id'              => 1,
                    'movement_type_id'      => $movementTypeId,
                    'quantity'              => abs($quantityChange),
                    'unit_of_measurement'   => $batch->unit_of_measurement,
                    'quantity_in_base_unit' => abs($quantityChange),
                    'unit_cost'             => $batch->unit_cost,
                    'total_value'           => abs($quantityChange) * $batch->unit_cost,
                    'reason'                => $request->reason . ' (Batch: ' . $batch->batch_number . ')',
                    'movement_date'         => now(),
                    'approved_at'           => now(),
                    'approved_by'           => Auth::id(),
                    'created_by'            => Auth::id(),
                    'stock_before'          => $oldQuantity,
                    'stock_after'           => $newQuantity,
                ]);

                DB::commit();

                return redirect()->route('store.inventory.show', $item->id)
                    ->with('success', "Batch {$batch->batch_number} adjusted. New quantity: {$newQuantity}");

            } else {
                // If no specific batch, find oldest active batch (FIFO)
                $batch = Batch::where('inventory_item_id', $item->id)
                    ->where('batch_status', 'active')
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->first();

                if (!$batch) {
                    throw new \Exception('No active batch found for this item.');
                }

                $oldQuantity = $batch->remaining_quantity;
                $newQuantity = $oldQuantity;
                $quantityChange = 0;

                if ($request->adjustment_type === 'add') {
                    $newQuantity = $oldQuantity + $request->quantity;
                    $quantityChange = $request->quantity;
                } elseif ($request->adjustment_type === 'subtract') {
                    if ($request->quantity > $oldQuantity) {
                        throw new \Exception('Insufficient stock in batch.');
                    }
                    $newQuantity = $oldQuantity - $request->quantity;
                    $quantityChange = -$request->quantity;
                }

                $batch->remaining_quantity = $newQuantity;
                if ($newQuantity <= 0) {
                    $batch->batch_status = 'depleted';
                }
                $batch->save();

                // Create stock movement
                $movementNumber = 'STK-ADJ-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $movementTypeId = $quantityChange > 0 ? 2 : 3;

                StockMovement::create([
                    'movement_number'       => $movementNumber,
                    'inventory_item_id'     => $item->id,
                    'store_id'              => 1,
                    'movement_type_id'      => $movementTypeId,
                    'quantity'              => abs($quantityChange),
                    'unit_of_measurement'   => $batch->unit_of_measurement,
                    'quantity_in_base_unit' => abs($quantityChange),
                    'unit_cost'             => $batch->unit_cost,
                    'total_value'           => abs($quantityChange) * $batch->unit_cost,
                    'reason'                => $request->reason . ' (Batch: ' . $batch->batch_number . ')',
                    'movement_date'         => now(),
                    'approved_at'           => now(),
                    'approved_by'           => Auth::id(),
                    'created_by'            => Auth::id(),
                    'stock_before'          => $oldQuantity,
                    'stock_after'           => $newQuantity,
                ]);

                DB::commit();

                return redirect()->route('store.inventory.show', $item->id)
                    ->with('success', "Stock adjusted from batch {$batch->batch_number}. New quantity: {$newQuantity}");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
