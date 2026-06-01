<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceivedNote;
use App\Models\GoodsReceivedNoteItem;
use App\Models\InventoryItem;
use App\Models\Batch;
use App\Models\Vendor;
use App\Models\Document;
use App\Models\StockMovement;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class GoodsReceivedController extends Controller
{
    /**
     * Display list of GRNs for Store module.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = GoodsReceivedNote::with(['vendor', 'purchaseOrder']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('grn_number', 'like', "%{$search}%")
                  ->orWhereHas('purchaseOrder', function($po) use ($search) {
                      $po->where('po_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $grns = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('store.goods_received.index', compact('grns'));
    }

    /**
     * Show form to select PO for receiving goods.
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = PurchaseOrder::with('vendor')
            ->whereIn('status', ['sent', 'partially_received'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhereHas('items.inventoryItem', function($qi) use ($search) {
                      $qi->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $purchaseOrders = $query->paginate(15);
        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();

        return view('store.goods_received.create', compact('purchaseOrders', 'vendors'));
    }

    /**
     * Show form to receive goods for a specific PO.
     */
    public function createForPo($poId)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $po = PurchaseOrder::with(['vendor', 'items.inventoryItem'])
            ->findOrFail($poId);

        return view('store.goods_received.create', compact('po'));
    }

    /**
     * Store GRN and create batches.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'received_date' => 'required|date',
            'delivery_note_number' => 'nullable|string|max:255',
            'received_by' => 'required|string|max:255',
            'delivered_by_name' => 'nullable|string|max:255',
            'delivered_by_phone' => 'nullable|string|max:50',
            'delivered_by_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.po_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'items.*.quantity_rejected' => 'nullable|numeric|min:0',
            'items.*.rejection_reason' => 'nullable|string|max:500',
            'items.*.pack_size' => 'nullable|numeric|min:1',
            'items.*.pack_cost' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $po = PurchaseOrder::with(['items.inventoryItem'])->findOrFail($request->purchase_order_id);

            // Get default store
            $store = Store::first();

            // Generate GRN Number
            $grnNumber = 'GRN-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $grn = GoodsReceivedNote::create([
                'grn_number' => $grnNumber,
                'purchase_order_id' => $po->id,
                'vendor_id' => $po->vendor_id,
                'received_date' => $request->received_date,
                'delivery_note_number' => $request->delivery_note_number,
                'received_by' => $request->received_by,
                'received_by_user_id' => Auth::id(),
                'delivered_by_name' => $request->delivered_by_name,
                'delivered_by_phone' => $request->delivered_by_phone,
                'delivered_by_email' => $request->delivered_by_email,
                'notes' => $request->notes,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            $totalReceivedValue = 0;

            foreach ($request->items as $itemData) {
                $poItem = PurchaseOrderItem::findOrFail($itemData['po_item_id']);
                $quantityReceived = floatval($itemData['quantity_received']);
                $quantityRejected = floatval($itemData['quantity_rejected'] ?? 0);
                $quantityAccepted = max(0, $quantityReceived - $quantityRejected);

                if ($quantityAccepted <= 0) {
                    continue;
                }

                $isBulk = isset($itemData['pack_size']) && $itemData['pack_size'] > 0;
                $packSize = $isBulk ? (int) $itemData['pack_size'] : 1;
                $packCost = isset($itemData['pack_cost']) ? floatval($itemData['pack_cost']) : null;

                // Calculate total base units and unit cost
                if ($isBulk) {
                    $totalBaseUnits = $quantityAccepted * $packSize;
                    // If pack_cost provided, calculate unit cost from pack cost
                    if ($packCost && $packCost > 0) {
                        $unitCost = $packCost / $packSize;
                    } else {
                        $unitCost = $poItem->unit_cost;
                    }
                } else {
                    $totalBaseUnits = $quantityAccepted;
                    $unitCost = $poItem->unit_cost;
                }

                $itemTotalCost = $totalBaseUnits * $unitCost;
                $totalReceivedValue += $itemTotalCost;

                // Get inventory item for unit of measurement
                $inventoryItem = InventoryItem::find($poItem->inventory_item_id);

                // Create GRN Item
                GoodsReceivedNoteItem::create([
                    'goods_received_note_id' => $grn->id,
                    'purchase_order_item_id' => $poItem->id,
                    'inventory_item_id' => $poItem->inventory_item_id,
                    'quantity_ordered' => $poItem->quantity_ordered,
                    'quantity_received' => $quantityReceived,
                    'quantity_accepted' => $quantityAccepted,
                    'quantity_rejected' => $quantityRejected,
                    'rejection_reason' => $itemData['rejection_reason'] ?? null,
                    'unit_cost' => $unitCost,
                    'total_cost' => $itemTotalCost,
                    'pack_type' => $isBulk ? $inventoryItem->default_unit_of_measure_id : null,
                    'pack_size' => $isBulk ? $packSize : null,
                    'number_of_packs' => $isBulk ? $quantityAccepted : null,
                    'quantity_in_base_unit' => $totalBaseUnits,
                    'notes' => $itemData['notes'] ?? null,
                    'created_by' => Auth::id(),
                ]);

                // Create Batch for accepted items
                $batchNumber = 'BAT-' . date('Ymd') . '-' . str_pad($grn->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($poItem->id, 4, '0', STR_PAD_LEFT);

                $batch = Batch::create([
                    'batch_number' => $batchNumber,
                    'inventory_item_id' => $poItem->inventory_item_id,
                    'goods_received_note_id' => $grn->id,
                    'supplier_id' => $po->vendor_id,
                    'initial_quantity' => $totalBaseUnits,
                    'remaining_quantity' => $totalBaseUnits,
                    'unit_cost' => $unitCost,
                    'pack_cost' => $packCost,
                    'total_cost' => $itemTotalCost,
                    'unit_of_measurement' => $inventoryItem->unit_of_measurement ?? $inventoryItem->base_unit ?? 'piece',
                    'pack_type' => $isBulk ? $inventoryItem->default_unit_of_measure_id : null,
                    'pack_size' => $isBulk ? $packSize : null,
                    'number_of_packs' => $isBulk ? $quantityAccepted : null,
                    'total_quantity' => $totalBaseUnits,
                    'batch_status' => 'active',
                    'notes' => 'Received from GRN: ' . $grnNumber,
                ]);

                // =============================================
                // CREATE STOCK MOVEMENT RECORD
                // =============================================
                $movementNumber = 'STK-GRN-' . date('Ymd') . '-' . str_pad($grn->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($poItem->id, 4, '0', STR_PAD_LEFT);

                // Calculate stock before this receipt
                $stockBefore = StockMovement::where('inventory_item_id', $poItem->inventory_item_id)
                    ->where('store_id', $store->id)
                    ->where('is_reversed', 0)
                    ->sum('quantity_in_base_unit');

                $stockAfter = $stockBefore + $totalBaseUnits;

                StockMovement::create([
                    'movement_number' => $movementNumber,
                    'inventory_item_id' => $poItem->inventory_item_id,
                    'batch_id' => $batch->id,
                    'store_id' => $store->id,
                    'movement_type_id' => 6, // GRN movement type
                    'department_id' => null,
                    'quantity' => $quantityAccepted,
                    'pack_type' => $isBulk ? ($itemData['pack_type'] ?? null) : null,
                    'base_unit' => $inventoryItem->unit_of_measurement ?? 'piece',
                    'pack_size' => $isBulk ? $packSize : null,
                    'number_of_packs' => $isBulk ? $quantityAccepted : null,
                    'quantity_in_base_unit' => $totalBaseUnits,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'unit_cost' => $unitCost,
                    'total_value' => $itemTotalCost,
                    'reason' => 'Received from GRN: ' . $grnNumber,
                    'taken_by' => null,
                    'returned_by' => null,
                    'movement_date' => $request->received_date,
                    'approved_at' => now(),
                    'approved_by' => Auth::id(),
                    'purchase_order_id' => $po->id,
                    'goods_received_note_id' => $grn->id,
                    'is_reversed' => 0,
                    'reversed_by_movement_id' => null,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                // Update PO item received quantity
                $poItem->quantity_received = ($poItem->quantity_received ?? 0) + $quantityAccepted;
                $poItem->save();
            }

            $grn->grn_total_amount = $totalReceivedValue;
            $grn->save();

            // Update PO status
            $po->refresh();
            $allItemsFullyReceived = $po->items->every(function($item) {
                return ($item->quantity_received ?? 0) >= $item->quantity_ordered;
            });

            if ($allItemsFullyReceived) {
                $po->status = 'fully_received';
            } elseif ($po->status == 'sent') {
                $po->status = 'partially_received';
            }
            $po->save();

            DB::commit();

            return redirect()->route('store.goods-received.show', $grn->id)
                ->with('success', 'Goods Received Note created successfully. Batches and stock movements have been created.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating GRN in Store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating GRN: ' . $e->getMessage());
        }
    }

    /**
     * Show GRN details with signatures.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $grn = GoodsReceivedNote::with([
            'vendor',
            'purchaseOrder',
            'items.inventoryItem',
            'createdBy',
            'receivedByUser'
        ])->findOrFail($id);

        $documents = Document::where('grn_id', $id)->get();

        return view('store.goods_received.show', compact('grn', 'documents'));
    }

    /**
     * Print GRN.
     */
    public function print($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $grn = GoodsReceivedNote::with([
            'vendor',
            'purchaseOrder',
            'items.inventoryItem',
            'createdBy',
            'receivedByUser'
        ])->findOrFail($id);

        return view('store.goods_received.print', compact('grn'));
    }

    /**
     * Download GRN PDF.
     */
    public function downloadPdf($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $grn = GoodsReceivedNote::with([
            'vendor',
            'purchaseOrder',
            'items.inventoryItem',
            'createdBy',
            'receivedByUser'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('store.goods_received.pdf', compact('grn'));
        return $pdf->download('GRN_' . $grn->grn_number . '.pdf');
    }
}
