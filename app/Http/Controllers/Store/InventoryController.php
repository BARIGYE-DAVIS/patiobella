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

    /**
     * Metrics that are pack types containing multiple base units inside.
     * When one of these is selected, the user must supply pack_size and number_of_units.
     */
    const BULK_METRICS = ['box', 'carton', 'crate', 'dozen', 'pack', 'sack', 'set'];

    /**
     * Metrics that are already base/direct units — no pack conversion needed.
     */
    const SIMPLE_METRICS = ['kg', 'litres', 'pcs', 'grams', 'millilitres'];

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
                  ->orWhere('item_code', 'like', "%{$search}%");
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
                            ->where('status', 'completed')
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

        // ── Validation rules ──────────────────────────────────────────────────
        $rules = [
            'item_name'   => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'item_code'   => 'nullable|string|max:50|unique:inventory_items,item_code',
            'metrics'     => 'required|string',
            'base_unit'   => 'required|string|max:50',
            'vendor_id'   => 'nullable|exists:vendors,id',
            'notes'       => 'nullable|string',
        ];

        if ($isBulk) {
            $rules['pieces_per_unit']  = 'required|numeric|min:1';
            $rules['number_of_units']  = 'required|numeric|min:1';
        } else {
            $rules['quantity'] = 'required|numeric|min:0.01';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            // ── Calculate quantities ──────────────────────────────────────────
            $packType       = null;
            $packSize       = null;
            $numberOfPacks  = null;
            $totalBaseUnits = 0;

            if ($isBulk) {
                $packType       = $metrics;
                $packSize       = (int) $validated['pieces_per_unit'];   // items per pack
                $numberOfPacks  = (int) $validated['number_of_units'];   // number of packs
                $totalBaseUnits = $packSize * $numberOfPacks;            // real stock count
            } else {
                $totalBaseUnits = (float) $validated['quantity'];
            }

            // ── Item code ─────────────────────────────────────────────────────
            $itemCode = $validated['item_code'] ?? null;
            if (empty($itemCode)) {
                $itemCode = 'ITEM-' . strtoupper(uniqid());
            }

            // ── Create inventory item ─────────────────────────────────────────
            // default_unit_of_measure_id = the receiving/pack unit (carton, kg, etc.)
            // base_unit                  = the individual selling/consumption unit (bottle, piece, etc.)
            // current_stock              = always in base units
            $inventoryItem = InventoryItem::create([
                'item_code'                  => $itemCode,
                'name'                       => $validated['item_name'],
                'category_id'                => $validated['category_id'],
                'default_unit_of_measure_id' => $metrics,
                'base_unit'                  => $validated['base_unit'],
                'unit_cost'                  => 0,
                'current_stock'              => $totalBaseUnits,   // stored in base units
                'notes'                      => $validated['notes'] ?? null,
                'is_active'                  => true,
                'created_by'                 => Auth::id(),
            ]);

            Log::info('Inventory item created (manual)', [
                'user_id'         => Auth::id(),
                'item_id'         => $inventoryItem->id,
                'item_name'       => $inventoryItem->name,
                'receiving_unit'  => $metrics,
                'base_unit'       => $validated['base_unit'],
                'pack_type'       => $packType,
                'pack_size'       => $packSize,
                'number_of_packs' => $numberOfPacks,
                'total_base_units'=> $totalBaseUnits,
            ]);

            // ── Create stock movement ─────────────────────────────────────────
            // quantity              = packs received (or direct qty for simple units)
            // pack_type             = carton / crate / box etc. (null for simple)
            // pack_size             = items per pack (null for simple)
            // number_of_packs       = packs received (null for simple)
            // quantity_in_base_unit = total base units (always)
            $movementNumber = 'STK-IN-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            StockMovement::create([
                'movement_number'       => $movementNumber,
                'inventory_item_id'     => $inventoryItem->id,
                'store_id'              => $request->store_id ?? 1,
                'movement_type_id'      => 2,                           // MANUAL_IN
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
            ]);

            Log::info('Stock movement created (manual)', [
                'user_id'               => Auth::id(),
                'movement_number'       => $movementNumber,
                'item_id'               => $inventoryItem->id,
                'pack_type'             => $packType,
                'pack_size'             => $packSize,
                'number_of_packs'       => $numberOfPacks,
                'quantity_in_base_unit' => $totalBaseUnits,
                'base_unit'             => $validated['base_unit'],
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
    // GET GRN ITEMS (AJAX)
    // ─────────────────────────────────────────────────────────────────────────────

    public function getGrnItems($grnId)
    {
        try {
            $grnItems = GoodsReceivedNoteItem::with(['inventoryItem'])
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
                    return [
                        'id'                  => $item->id,
                        'inventory_item_id'   => $item->inventory_item_id,
                        'item_name'           => $item->inventoryItem->name ?? 'Unknown Item',
                        'quantity_accepted'   => $item->quantity_accepted,
                        'unit_cost'           => $item->unit_cost,
                        'total_cost'          => $item->total_cost,
                        // The receiving/pack unit on the GRN line
                        'metrics'             => $item->inventoryItem->default_unit_of_measure_id ?? 'pcs',
                        // The base/selling unit already set on the item (may be null for new items)
                        'base_unit'           => $item->inventoryItem->base_unit ?? null,
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
    // STORE FROM GRN
    // ─────────────────────────────────────────────────────────────────────────────

    public function storeFromGrn(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            Log::warning('Unauthorized store-from-GRN attempt', [
                'user_id'    => $user->id,
                'department' => $user->department->name ?? 'none',
            ]);
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // ── Validation ────────────────────────────────────────────────────────
        try {
            $request->validate([
                'grn_id'                    => 'required|exists:goods_received_notes,id',
                'items'                     => 'required|array',
                'items.*.grn_item_id'       => 'required|exists:goods_received_items,id',
                'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
                'items.*.quantity'          => 'required|numeric|min:0.01',
                'items.*.unit_cost'         => 'required|numeric|min:0',
                'items.*.base_unit'         => 'required|string|max:50',
                // pack_size required only when the item has a bulk metrics
                'items.*.pack_size'         => 'nullable|numeric|min:1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Store-from-GRN validation failed', [
                'user_id' => Auth::id(),
                'grn_id'  => $request->grn_id,
                'errors'  => $e->errors(),
            ]);
            throw $e;
        }

        DB::beginTransaction();

        try {
            $grn           = GoodsReceivedNote::findOrFail($request->grn_id);
            $totalReceived = 0;

            Log::info('Beginning GRN stock receipt', [
                'user_id'    => Auth::id(),
                'grn_id'     => $grn->id,
                'grn_number' => $grn->grn_number,
                'item_count' => count($request->items),
            ]);

            foreach ($request->items as $idx => $itemData) {

                $inventoryItem = InventoryItem::find($itemData['inventory_item_id']);

                if (!$inventoryItem) {
                    Log::warning('Inventory item not found during GRN receipt', [
                        'user_id'           => Auth::id(),
                        'grn_id'            => $grn->id,
                        'inventory_item_id' => $itemData['inventory_item_id'],
                        'index'             => $idx,
                    ]);
                    continue;
                }

                // ── Determine receiving unit and calculate base units ──────────
                $receivingMetrics = $inventoryItem->default_unit_of_measure_id ?? 'pcs';
                $isBulk           = in_array($receivingMetrics, self::BULK_METRICS);
                $packSize         = isset($itemData['pack_size']) ? (int) $itemData['pack_size'] : null;
                $quantityReceived = (float) $itemData['quantity'];  // number of packs or direct qty
                $baseUnit         = $itemData['base_unit'];

                // Total base units = packs × items per pack  (or direct qty for simple units)
                if ($isBulk && $packSize > 0) {
                    $totalBaseUnits = $quantityReceived * $packSize;
                } else {
                    $totalBaseUnits = $quantityReceived;
                    $packSize       = null; // not a pack item
                }

                // ── Update inventory item stock (always in base units) ─────────
                $previousStock          = $inventoryItem->current_stock ?? 0;
                $newStock               = $previousStock + $totalBaseUnits;
                $inventoryItem->current_stock = $newStock;
                $inventoryItem->unit_cost     = $itemData['unit_cost'];

                // Set base_unit on the item if not already set
                if (empty($inventoryItem->base_unit)) {
                    $inventoryItem->base_unit = $baseUnit;
                }

                $inventoryItem->save();

                Log::info('Stock updated from GRN', [
                    'user_id'          => Auth::id(),
                    'grn_id'           => $grn->id,
                    'item_id'          => $inventoryItem->id,
                    'item_name'        => $inventoryItem->name,
                    'receiving_unit'   => $receivingMetrics,
                    'base_unit'        => $baseUnit,
                    'pack_size'        => $packSize,
                    'qty_received'     => $quantityReceived,
                    'total_base_units' => $totalBaseUnits,
                    'previous_stock'   => $previousStock,
                    'new_stock'        => $newStock,
                ]);

                // ── Also update goods_received_items with pack info ────────────
                $grnItem = GoodsReceivedNoteItem::find($itemData['grn_item_id']);
                if ($grnItem) {
                    $grnItem->pack_type             = $isBulk ? $receivingMetrics : null;
                    $grnItem->pack_size             = $packSize;
                    $grnItem->number_of_packs       = $isBulk ? (int) $quantityReceived : null;
                    $grnItem->quantity_in_base_unit = $totalBaseUnits;
                    $grnItem->base_unit             = $baseUnit;
                    $grnItem->save();
                }

                // ── Create stock movement ─────────────────────────────────────
                $movementNumber = 'STK-GRN-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

                $movement = StockMovement::create([
                    'movement_number'       => $movementNumber,
                    'inventory_item_id'     => $inventoryItem->id,
                    'store_id'              => 1,
                    'movement_type_id'      => 1,                       // PURCHASE
                    'quantity'              => $quantityReceived,        // packs or direct qty
                    'pack_type'             => $isBulk ? $receivingMetrics : null,
                    'pack_size'             => $packSize,
                    'number_of_packs'       => $isBulk ? (int) $quantityReceived : null,
                    'base_unit'             => $baseUnit,
                    'unit_id'               => null,
                    'quantity_in_base_unit' => $totalBaseUnits,         // real stock change
                    'unit_cost'             => $itemData['unit_cost'],
                    'total_value'           => $totalBaseUnits * $itemData['unit_cost'],
                    'reason'                => 'Received from GRN: ' . $grn->grn_number,
                    'movement_date'         => now(),
                    'approved_at'           => now(),
                    'approved_by'           => Auth::id(),
                    'goods_received_note_id'=> $grn->id,
                    'created_by'            => Auth::id(),
                ]);

                Log::info('Stock movement created from GRN', [
                    'user_id'               => Auth::id(),
                    'movement_id'           => $movement->id,
                    'movement_number'       => $movementNumber,
                    'grn_id'                => $grn->id,
                    'item_id'               => $inventoryItem->id,
                    'pack_type'             => $isBulk ? $receivingMetrics : null,
                    'pack_size'             => $packSize,
                    'quantity_in_base_unit' => $totalBaseUnits,
                    'base_unit'             => $baseUnit,
                ]);

                $totalReceived += $totalBaseUnits;
            }

            $grn->status = 'inventory_updated';
            $grn->save();

            Log::info('GRN stock receipt completed', [
                'user_id'        => Auth::id(),
                'grn_id'         => $grn->id,
                'grn_number'     => $grn->grn_number,
                'total_received' => $totalReceived,
            ]);

            DB::commit();

            return redirect()->route('store.inventory.index')
                ->with('success', "Goods received from GRN {$grn->grn_number} and added to inventory successfully. Total {$totalReceived} base units received.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing from GRN', [
                'user_id' => Auth::id(),
                'grn_id'  => $request->grn_id ?? null,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
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
            Log::warning('Unauthorized access to inventory edit', [
                'user_id'    => $user->id,
                'item_id'    => $id,
                'department' => $user->department->name ?? 'none',
            ]);
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $item       = InventoryItem::findOrFail($id);
            $categories = Category::where('is_active', true)->orderBy('name')->get();
            $vendors    = Vendor::where('status', 'active')->orderBy('name')->get();

            return view('store.inventory.edit', compact('item', 'categories', 'vendors'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Inventory item not found for edit', [
                'user_id' => Auth::id(), 'item_id' => $id,
            ]);
            return redirect()->route('store.inventory.index')
                ->with('error', 'Inventory item not found.');

        } catch (\Exception $e) {
            Log::error('Failed to load inventory edit form', [
                'user_id' => Auth::id(), 'item_id' => $id,
                'error'   => $e->getMessage(), 'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('store.inventory.index')
                ->with('error', 'Failed to load edit form.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            Log::warning('Unauthorized inventory update attempt', [
                'user_id' => $user->id, 'item_id' => $id,
            ]);
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $item = InventoryItem::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('store.inventory.index')
                ->with('error', 'Inventory item not found.');
        }

        try {
            $validated = $request->validate([
                'name'          => 'required|string|max:255',
                'category_id'   => 'required|exists:categories,id',
                'item_code'     => 'nullable|string|max:50|unique:inventory_items,item_code,' . $id,
                'base_unit'     => 'nullable|string|max:50',
                'unit_cost'     => 'nullable|numeric|min:0',
                'selling_price' => 'nullable|numeric|min:0',
                'is_active'     => 'boolean',
                'notes'         => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Inventory update validation failed', [
                'user_id' => Auth::id(), 'item_id' => $id, 'errors' => $e->errors(),
            ]);
            throw $e;
        }

        try {
            $item->update([
                'name'          => $validated['name'],
                'category_id'   => $validated['category_id'],
                'item_code'     => $validated['item_code'] ?? $item->item_code,
                'base_unit'     => $validated['base_unit'] ?? $item->base_unit,
                'unit_cost'     => $validated['unit_cost'] ?? $item->unit_cost,
                'selling_price' => $validated['selling_price'] ?? $item->selling_price,
                'is_active'     => $validated['is_active'] ?? $item->is_active,
                'notes'         => $validated['notes'] ?? $item->notes,
                'updated_by'    => Auth::id(),
            ]);

            Log::info('Inventory item updated', [
                'user_id' => Auth::id(), 'item_id' => $item->id,
                'changes' => $item->getChanges(),
            ]);

            return redirect()->route('store.inventory.show', $item->id)
                ->with('success', 'Inventory item updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update inventory item', [
                'user_id' => Auth::id(), 'item_id' => $id,
                'error'   => $e->getMessage(), 'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update inventory item: ' . $e->getMessage())
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('store.inventory.index')
                ->with('error', 'Inventory item not found.');
        }

        try {
            if (StockMovement::where('inventory_item_id', $id)->exists()) {
                Log::warning('Attempted to delete item with stock movements', [
                    'user_id' => Auth::id(), 'item_id' => $id, 'item_name' => $item->name,
                ]);
                return redirect()->route('store.inventory.index')
                    ->with('error', 'Cannot delete item that has stock movements.');
            }

            $item->delete();

            Log::info('Inventory item deleted', [
                'user_id' => Auth::id(), 'item_id' => $id, 'item_name' => $item->name,
            ]);

            return redirect()->route('store.inventory.index')
                ->with('success', 'Inventory item deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete inventory item', [
                'user_id' => Auth::id(), 'item_id' => $id,
                'error'   => $e->getMessage(), 'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('store.inventory.index')
                ->with('error', 'Failed to delete inventory item: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // ADJUST STOCK
    // ─────────────────────────────────────────────────────────────────────────────

    public function adjustStock(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
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
                'user_id' => Auth::id(), 'item_id' => $id, 'errors' => $e->errors(),
            ]);
            throw $e;
        }

        DB::beginTransaction();

        try {
            $item          = InventoryItem::findOrFail($id);
            $quantity      = (float) $request->quantity;   // always in base units
            $previousStock = $item->current_stock ?? 0;

            if ($request->adjustment_type === 'add') {
                $newStock       = $previousStock + $quantity;
                $movementTypeId = 2;   // MANUAL_IN
            } else {
                if ($previousStock < $quantity) {
                    throw new \Exception(
                        "Insufficient stock. Current: {$previousStock} {$item->base_unit}(s). Requested: {$quantity}."
                    );
                }
                $newStock       = $previousStock - $quantity;
                $movementTypeId = 3;   // MANUAL_OUT
            }

            $item->current_stock = $newStock;
            $item->save();

            Log::info('Stock adjusted', [
                'user_id'         => Auth::id(),
                'item_id'         => $item->id,
                'item_name'       => $item->name,
                'base_unit'       => $item->base_unit,
                'adjustment_type' => $request->adjustment_type,
                'previous_stock'  => $previousStock,
                'quantity'        => $quantity,
                'new_stock'       => $newStock,
                'reason'          => $request->reason,
            ]);

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
            ]);

            DB::commit();

            return redirect()->route('store.inventory.show', $item->id)
                ->with('success', "Stock adjusted. New balance: {$newStock} {$item->base_unit}(s).");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to adjust stock', [
                'user_id' => Auth::id(), 'item_id' => $id,
                'error'   => $e->getMessage(), 'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
