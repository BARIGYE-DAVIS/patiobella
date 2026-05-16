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
    // PRIVATE HELPER
    // ─────────────────────────────────────────────────────────────────────────────

    private function getCurrentStock(int $inventoryItemId): float
    {
        return (float) (InventoryItem::find($inventoryItemId)?->current_stock ?? 0);
    }


    public function barcodeLookup(Request $request)
    {
        $request->validate(['barcode' => 'required|string|max:100']);

        $barcode = trim($request->barcode);

        // Search by barcode column OR item_code column (covers both use cases)
        $item = InventoryItem::with(['category'])
            ->where('barcode', $barcode)
            ->orWhere('item_code', $barcode)
            ->first();

        if (!$item) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'item'  => [
                'id'            => $item->id,
                'name'          => $item->name,
                'item_code'     => $item->item_code,
                'barcode'       => $item->barcode,
                'category_id'   => $item->category_id,
                'category_name' => $item->category?->name,
                'base_unit'     => $item->base_unit,
                'metrics'       => $item->default_unit_of_measure_id,
                'current_stock' => $item->current_stock,
                'unit_cost'     => $item->unit_cost,
                'notes'         => $item->notes,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = InventoryItem::with(['category']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");   // ◀ also search barcode
            });
        }

        $items = $query->orderBy('name')->paginate(20);

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
    // STORE — Manual Entry
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
            'barcode'     => 'nullable|string|max:100|unique:inventory_items,barcode', // ◀ NEW
            'metrics'     => 'required|string',
            'base_unit'   => 'required|string|max:50',
            'vendor_id'   => 'nullable|exists:vendors,id',
            'notes'       => 'nullable|string',
        ];

        if ($isBulk) {
            $rules['pieces_per_unit'] = 'required|numeric|min:1';
            $rules['number_of_units'] = 'required|numeric|min:1';
        } else {
            $rules['quantity'] = 'required|numeric|min:0.01';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $packType       = null;
            $packSize       = null;
            $numberOfPacks  = null;
            $totalBaseUnits = 0;

            if ($isBulk) {
                $packType       = $metrics;
                $packSize       = (int) $validated['pieces_per_unit'];
                $numberOfPacks  = (int) $validated['number_of_units'];
                $totalBaseUnits = $packSize * $numberOfPacks;
            } else {
                $totalBaseUnits = (float) $validated['quantity'];
            }

            $itemCode = $validated['item_code'] ?? null;
            if (empty($itemCode)) {
                $itemCode = 'ITEM-' . strtoupper(uniqid());
            }

            // ◀ NEW: save barcode (null if not provided)
            $barcode = !empty($validated['barcode']) ? $validated['barcode'] : null;

            $inventoryItem = InventoryItem::create([
                'item_code'                  => $itemCode,
                'barcode'                    => $barcode,          // ◀ NEW
                'name'                       => $validated['item_name'],
                'category_id'               => $validated['category_id'],
                'default_unit_of_measure_id' => $metrics,
                'base_unit'                  => $validated['base_unit'],
                'unit_cost'                  => 0,
                'current_stock'              => $totalBaseUnits,
                'notes'                      => $validated['notes'] ?? null,
                'is_active'                  => true,
                'created_by'                 => Auth::id(),
            ]);

            Log::info('Inventory item created (manual)', [
                'user_id'          => Auth::id(),
                'item_id'          => $inventoryItem->id,
                'item_name'        => $inventoryItem->name,
                'barcode'          => $barcode,                    // ◀ NEW
                'receiving_unit'   => $metrics,
                'base_unit'        => $validated['base_unit'],
                'pack_type'        => $packType,
                'pack_size'        => $packSize,
                'number_of_packs'  => $numberOfPacks,
                'total_base_units' => $totalBaseUnits,
            ]);

            $stockBefore = 0;
            $stockAfter  = $totalBaseUnits;

            $movementNumber = 'STK-IN-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            StockMovement::create([
                'movement_number'       => $movementNumber,
                'inventory_item_id'     => $inventoryItem->id,
                'store_id'              => $request->store_id ?? 1,
                'movement_type_id'      => 2,
                'quantity'              => $isBulk ? $numberOfPacks : $totalBaseUnits,
                'pack_type'             => $packType,
                'pack_size'             => $packSize,
                'number_of_packs'       => $numberOfPacks,
                'base_unit'             => $validated['base_unit'],
                'unit_id'               => null,
                'quantity_in_base_unit' => $totalBaseUnits,
                'unit_cost'             => 0,
                'total_value'           => 0,
                'reason'                => $validated['notes'] ?? 'Manual inventory entry',
                'movement_date'         => now(),
                'approved_at'           => now(),
                'approved_by'           => Auth::id(),
                'created_by'            => Auth::id(),
                'stock_before'          => $stockBefore,
                'stock_after'           => $stockAfter,
            ]);

            DB::commit();

            return redirect()->route('store.inventory.index')
                ->with('success', "Inventory item created. {$totalBaseUnits} {$validated['base_unit']}(s) added to stock.");

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
    // GET GRN ITEMS (AJAX) — unchanged
    // ─────────────────────────────────────────────────────────────────────────────

    public function getGrnItems($grnId)
    {
        try {
            $grnItems = GoodsReceivedNoteItem::with(['inventoryItem.category'])
                ->where('goods_received_note_id', $grnId)
                ->get();

            Log::info('GRN items fetched', [
                'user_id'    => Auth::id(),
                'grn_id'     => $grnId,
                'item_count' => $grnItems->count(),
            ]);

            return response()->json([
                'success' => true,
                'items'   => $grnItems->map(function ($item) {
                    $inv = $item->inventoryItem;
                    return [
                        'id'                    => $item->id,
                        'inventory_item_id'     => $item->inventory_item_id,
                        'item_name'             => $inv->name ?? 'Unknown Item',
                        'item_code'             => $inv->item_code ?? '',
                        'barcode'               => $inv->barcode ?? '',   // ◀ NEW
                        'category'              => $inv->category->name ?? '',
                        'quantity_accepted'     => $item->quantity_accepted,
                        'unit_cost'             => $item->unit_cost,
                        'total_cost'            => $item->total_cost,
                        'metrics'               => $inv->default_unit_of_measure_id ?? 'pcs',
                        'base_unit'             => $inv->base_unit ?? 'pcs',
                        'current_stock'         => $inv->current_stock ?? 0,
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
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load GRN items: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // STORE FROM GRN — unchanged (barcode already on inventory item)
    // ─────────────────────────────────────────────────────────────────────────────

    public function storeFromGrn(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $request->validate([
                'grn_id'                              => 'required|exists:goods_received_notes,id',
                'items'                               => 'required|array|min:1',
                'items.*.grn_item_id'                 => 'required|exists:goods_received_items,id',
                'items.*.inventory_item_id'           => 'required|exists:inventory_items,id',
                'items.*.receiving_metrics'           => 'required|string',
                'items.*.base_unit'                   => 'required|string|max:50',
                'items.*.quantity'                    => 'required|numeric|min:0.01',
                'items.*.unit_cost'                   => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        DB::beginTransaction();

        try {
            $grn           = GoodsReceivedNote::findOrFail($request->grn_id);
            $totalReceived = 0;
            $itemsSummary  = [];

            foreach ($request->items as $idx => $itemData) {

                $inventoryItem = InventoryItem::find($itemData['inventory_item_id']);
                if (!$inventoryItem) continue;

                $receivingMetrics = trim($itemData['receiving_metrics']);
                $isBulk           = in_array($receivingMetrics, self::BULK_METRICS);
                $quantityReceived  = (float) $itemData['quantity'];
                $baseUnit          = trim($itemData['base_unit']);
                $unitCost          = (float) $itemData['unit_cost'];

                $packSize = null;
                if ($isBulk) {
                    $packSize = isset($itemData['pack_size']) ? (int) $itemData['pack_size'] : 0;
                    if ($packSize < 1) {
                        DB::rollBack();
                        return redirect()->back()->with(
                            'error',
                            'Item "' . $inventoryItem->name . '": pack size must be at least 1 when receiving in ' . $receivingMetrics . '.'
                        );
                    }
                }

                $totalBaseUnits = ($isBulk && $packSize > 0)
                    ? $quantityReceived * $packSize
                    : $quantityReceived;

                if (!$isBulk) $packSize = null;

                $stockBefore = (float) ($inventoryItem->current_stock ?? 0);
                $stockAfter  = $stockBefore + $totalBaseUnits;

                $inventoryItem->current_stock              = $stockAfter;
                $inventoryItem->unit_cost                  = $unitCost;
                $inventoryItem->last_purchase_price        = $unitCost;
                $inventoryItem->default_unit_of_measure_id = $receivingMetrics;
                $inventoryItem->updated_by                 = Auth::id();

                if (empty($inventoryItem->base_unit) || $inventoryItem->base_unit === 'pcs') {
                    $inventoryItem->base_unit = $baseUnit;
                }

                $inventoryItem->save();

                $grnItem = GoodsReceivedNoteItem::find($itemData['grn_item_id']);
                if ($grnItem) {
                    $grnItem->pack_type             = $isBulk ? $receivingMetrics : null;
                    $grnItem->pack_size             = $packSize;
                    $grnItem->number_of_packs       = $isBulk ? (int) $quantityReceived : null;
                    $grnItem->quantity_in_base_unit = $totalBaseUnits;
                    $grnItem->base_unit             = $baseUnit;
                    $grnItem->updated_by            = Auth::id();
                    $grnItem->save();
                }

                $movementNumber = 'STK-GRN-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

                StockMovement::create([
                    'movement_number'        => $movementNumber,
                    'inventory_item_id'      => $inventoryItem->id,
                    'store_id'               => 1,
                    'movement_type_id'       => 1,
                    'quantity'               => $quantityReceived,
                    'pack_type'              => $isBulk ? $receivingMetrics : null,
                    'pack_size'              => $packSize,
                    'number_of_packs'        => $isBulk ? (int) $quantityReceived : null,
                    'base_unit'              => $baseUnit,
                    'unit_id'                => null,
                    'quantity_in_base_unit'  => $totalBaseUnits,
                    'unit_cost'              => $unitCost,
                    'total_value'            => $totalBaseUnits * $unitCost,
                    'reason'                 => 'Received from GRN: ' . $grn->grn_number,
                    'movement_date'          => now(),
                    'approved_at'            => now(),
                    'approved_by'            => Auth::id(),
                    'goods_received_note_id' => $grn->id,
                    'created_by'             => Auth::id(),
                    'stock_before'           => $stockBefore,
                    'stock_after'            => $stockAfter,
                ]);

                $totalReceived += $totalBaseUnits;
                $itemsSummary[] = "{$inventoryItem->name}: +{$totalBaseUnits} {$baseUnit}(s)";
            }

            $grn->status     = 'inventory_updated';
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
            return redirect()->back()
                ->with('error', 'Failed to receive goods: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────────────────────────────────────

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

            return view('store.inventory.edit', compact('item', 'categories', 'vendors'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('store.inventory.index')->with('error', 'Inventory item not found.');
        } catch (\Exception $e) {
            return redirect()->route('store.inventory.index')->with('error', 'Failed to load edit form.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // UPDATE — now saves barcode
    // ─────────────────────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $item = InventoryItem::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('store.inventory.index')->with('error', 'Inventory item not found.');
        }

        try {
            $validated = $request->validate([
                'name'          => 'required|string|max:255',
                'category_id'   => 'required|exists:categories,id',
                'item_code'     => 'nullable|string|max:50|unique:inventory_items,item_code,' . $id,
                'barcode'       => 'nullable|string|max:100|unique:inventory_items,barcode,' . $id, // ◀ NEW
                'base_unit'     => 'nullable|string|max:50',
                'unit_cost'     => 'nullable|numeric|min:0',
                'selling_price' => 'nullable|numeric|min:0',
                'is_active'     => 'boolean',
                'notes'         => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        try {
            $item->update([
                'name'          => $validated['name'],
                'category_id'   => $validated['category_id'],
                'item_code'     => $validated['item_code']     ?? $item->item_code,
                'barcode'       => $validated['barcode']       ?? $item->barcode,  // ◀ NEW
                'base_unit'     => $validated['base_unit']     ?? $item->base_unit,
                'unit_cost'     => $validated['unit_cost']     ?? $item->unit_cost,
                'selling_price' => $validated['selling_price'] ?? $item->selling_price,
                'is_active'     => $validated['is_active']     ?? $item->is_active,
                'notes'         => $validated['notes']         ?? $item->notes,
                'updated_by'    => Auth::id(),
            ]);

            return redirect()->route('store.inventory.show', $item->id)
                ->with('success', 'Inventory item updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update inventory item: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // DESTROY — unchanged
    // ─────────────────────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $item = InventoryItem::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('store.inventory.index')->with('error', 'Inventory item not found.');
        }

        try {
            if (StockMovement::where('inventory_item_id', $id)->exists()) {
                return redirect()->route('store.inventory.index')
                    ->with('error', 'Cannot delete item that has stock movements.');
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
    // ADJUST STOCK — unchanged
    // ─────────────────────────────────────────────────────────────────────────────

    public function adjustStock(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'adjustment_type' => 'required|in:add,subtract',
            'quantity'        => 'required|numeric|min:0.01',
            'reason'          => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $item        = InventoryItem::findOrFail($id);
            $quantity    = (float) $request->quantity;
            $stockBefore = (float) ($item->current_stock ?? 0);

            if ($request->adjustment_type === 'add') {
                $newStock       = $stockBefore + $quantity;
                $movementTypeId = 2;
            } else {
                if ($stockBefore < $quantity) {
                    throw new \Exception(
                        "Insufficient stock. Current: {$stockBefore} {$item->base_unit}(s). Requested: {$quantity}."
                    );
                }
                $newStock       = $stockBefore - $quantity;
                $movementTypeId = 3;
            }

            $item->current_stock = $newStock;
            $item->save();

            $movementNumber = 'STK-ADJ-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            StockMovement::create([
                'movement_number'       => $movementNumber,
                'inventory_item_id'     => $item->id,
                'store_id'              => 1,
                'movement_type_id'      => $movementTypeId,
                'quantity'              => $quantity,
                'pack_type'             => null,
                'pack_size'             => null,
                'number_of_packs'       => null,
                'base_unit'             => $item->base_unit,
                'unit_id'               => null,
                'quantity_in_base_unit' => $quantity,
                'unit_cost'             => $item->unit_cost,
                'total_value'           => $quantity * ($item->unit_cost ?? 0),
                'reason'                => $request->reason . ' (Manual adjustment)',
                'movement_date'         => now(),
                'approved_at'           => now(),
                'approved_by'           => Auth::id(),
                'created_by'            => Auth::id(),
                'stock_before'          => $stockBefore,
                'stock_after'           => $newStock,
            ]);

            DB::commit();

            return redirect()->route('store.inventory.show', $item->id)
                ->with('success', "Stock adjusted. New balance: {$newStock} {$item->base_unit}(s).");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
