<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\DepartmentRequisition;
use App\Models\DepartmentRequisitionItem;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentRequisitionController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = DepartmentRequisition::with(['department', 'requestedBy', 'items']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

        $departments = \App\Models\Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE', 'RESTAURANT'])->get();

        return view('store.department_requisitions.index', compact('requisitions', 'departments'));
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = DepartmentRequisition::with(['department', 'requestedBy', 'items.inventoryItem'])
            ->findOrFail($id);

        return view('store.department_requisitions.show', compact('requisition'));
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // APPROVE
    // ─────────────────────────────────────────────────────────────────────────────

    public function approve($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        DB::beginTransaction();

        try {
            $requisition = DepartmentRequisition::findOrFail($id);

            if ($requisition->status !== 'approved') {
                return redirect()->back()->with('error', 'Only already approved requisitions.');
            }

            $requisition->status      = 'approved';
            $requisition->approved_by = Auth::id();
            $requisition->approved_at = now();
            $requisition->save();

            DB::commit();

            Log::info('Department requisition approved', [
                'user_id'            => Auth::id(),
                'requisition_id'     => $requisition->id,
                'requisition_number' => $requisition->requisition_number,
            ]);

            return redirect()->route('store.department-requisitions.show', $requisition->id)
                ->with('success', 'Requisition approved. You can now issue items.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving requisition', ['requisition_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error approving requisition: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // REJECT
    // ─────────────────────────────────────────────────────────────────────────────

    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate(['rejection_reason' => 'required|string|max:500']);

        DB::beginTransaction();

        try {
            $requisition = DepartmentRequisition::findOrFail($id);

            if ($requisition->status !== 'approved') {
                return redirect()->back()->with('error', 'you cannot reject non-approved requisitions.');
            }

            $requisition->status           = 'rejected';
            $requisition->rejection_reason = $request->rejection_reason;
            $requisition->approved_by      = Auth::id();
            $requisition->approved_at      = now();
            $requisition->save();

            DB::commit();

            Log::info('Department requisition rejected', [
                'user_id'        => Auth::id(),
                'requisition_id' => $requisition->id,
                'reason'         => $request->rejection_reason,
            ]);

            return redirect()->route('store.department-requisitions.index')
                ->with('success', 'Requisition rejected.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting requisition', ['requisition_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error rejecting requisition: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // ISSUE FORM
    // ─────────────────────────────────────────────────────────────────────────────

    public function issueForm($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = DepartmentRequisition::with(['items.inventoryItem', 'department'])
            ->findOrFail($id);

        if ($requisition->status === 'issued') {
            return redirect()->route('store.department-requisitions.show', $requisition->id)
                ->with('error', 'This requisition has already been fully issued.');
        }

        if (!in_array($requisition->status, ['approved', 'partially_issued'])) {
            return redirect()->route('store.department-requisitions.show', $requisition->id)
                ->with('error', 'Only approved requisitions can be issued.');
        }

        return view('store.department_requisitions.issue', compact('requisition'));
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // ISSUE — Process issuing items with BATCH support
    // ─────────────────────────────────────────────────────────────────────────────

    public function issue(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'items'                   => 'required|array',
            'items.*.item_id'         => 'required|exists:department_requisition_items,id',
            'items.*.quantity_issued' => 'required|numeric|min:0',
            'items.*.pack_type'       => 'nullable|string',
            'items.*.pack_size'       => 'required_with:items.*.pack_type|nullable|numeric|min:1',
            'taken_by'                => 'required|string|max:255',
            'store_notes'             => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $requisition = DepartmentRequisition::findOrFail($id);

            if ($requisition->status === 'issued') {
                return redirect()->route('store.department-requisitions.show', $requisition->id)
                    ->with('error', 'This requisition has already been fully issued.');
            }

            $allFullyIssued = true;
            $anyIssued      = false;
            $batchMovements = []; // Track which batches were used

            foreach ($request->items as $itemData) {
                $reqItem        = DepartmentRequisitionItem::findOrFail($itemData['item_id']);
                $quantityIssued = (float) $itemData['quantity_issued'];
                $packType       = $itemData['pack_type'] ?? null;
                $packSize       = isset($itemData['pack_size']) ? (float) $itemData['pack_size'] : null;

                if ($quantityIssued <= 0) {
                    // Check whether this item has been fully covered in base-unit pieces
                    $requestedPieces = ($reqItem->requested_pack_type && $reqItem->requested_pack_size)
                        ? $reqItem->quantity_requested * $reqItem->requested_pack_size
                        : $reqItem->quantity_requested;

                    if (($reqItem->issued_total_pieces ?? 0) < $requestedPieces) {
                        $allFullyIssued = false;
                    }
                    continue;
                }

                // Total pieces in base units to issue
                $totalPiecesToIssue = ($packType && $packSize)
                    ? $quantityIssued * $packSize
                    : $quantityIssued;

                $inventoryItem = InventoryItem::find($reqItem->inventory_item_id);

                // Get total available stock from inventory item summary
                $totalStockBefore = (float) ($inventoryItem->current_stock ?? 0);

                if ($totalPiecesToIssue > $totalStockBefore) {
                    throw new \Exception(
                        'Insufficient stock for: ' . ($inventoryItem->name ?? 'item') .
                        '. Available: ' . $totalStockBefore . ' ' . ($inventoryItem->base_unit ?? 'units') .
                        ', Attempting to issue: ' . $totalPiecesToIssue . ' ' . ($inventoryItem->base_unit ?? 'units')
                    );
                }

                // ─────────────────────────────────────────────────────────────
                // BATCH-AWARE DEDUCTION — FIFO (First In, First Out)
                // Get active batches ordered by creation date (oldest first)
                // ─────────────────────────────────────────────────────────────
                $batches = Batch::where('inventory_item_id', $inventoryItem->id)
                    ->where('batch_status', 'active')
                    ->where('remaining_quantity', '>', 0)
                    ->where(function($q) {
                        $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();

                if ($batches->sum('remaining_quantity') < $totalPiecesToIssue) {
                    throw new \Exception(
                        'Batch stock mismatch for: ' . ($inventoryItem->name ?? 'item') .
                        '. Batch total: ' . $batches->sum('remaining_quantity') .
                        ', Attempting to issue: ' . $totalPiecesToIssue
                    );
                }

                $remainingToIssue = $totalPiecesToIssue;
                $deductionDetails = [];

                foreach ($batches as $batch) {
                    if ($remainingToIssue <= 0) break;

                    $availableInBatch = $batch->remaining_quantity;
                    $takeFromBatch = min($remainingToIssue, $availableInBatch);

                    // Reduce the batch quantity
                    $batch->remaining_quantity -= $takeFromBatch;

                    // Update batch status if depleted
                    if ($batch->remaining_quantity <= 0) {
                        $batch->batch_status = Batch::STATUS_DEPLETED;
                    } elseif ($batch->remaining_quantity < $batch->initial_quantity) {
                        $batch->batch_status = Batch::STATUS_PARTIALLY_USED;
                    }

                    $batch->save();

                    $deductionDetails[] = [
                        'batch_id' => $batch->id,
                        'batch_number' => $batch->batch_number,
                        'quantity' => $takeFromBatch,
                        'unit_cost' => $batch->unit_cost,
                        'total_value' => $takeFromBatch * $batch->unit_cost
                    ];

                    $remainingToIssue -= $takeFromBatch;
                }

                // Calculate total stock after deduction
                $totalStockAfter = $totalStockBefore - $totalPiecesToIssue;

                // Update inventory item summary stock
                $inventoryItem->current_stock = $totalStockAfter;
                $inventoryItem->save();

                // Update requisition item
                $reqItem->quantity_issued     = $quantityIssued;
                $reqItem->issued_pack_type    = $packType;
                $reqItem->issued_pack_size    = $packSize;
                $reqItem->issued_total_pieces = $totalPiecesToIssue;
                $reqItem->save();

                // Create stock movement record with batch details
                $movementNumber = 'ISS-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

                $stockMovement = StockMovement::create([
                    'movement_number'       => $movementNumber,
                    'inventory_item_id'     => $inventoryItem->id,
                    'store_id'              => 1,
                    'movement_type_id'      => 5,
                    'quantity'              => $quantityIssued,
                    'pack_type'             => $packType,
                    'pack_size'             => $packSize,
                    'number_of_packs'       => $packType ? $quantityIssued : null,
                    'base_unit'             => $inventoryItem->base_unit ?? 'units',
                    'unit_id'               => null,
                    'quantity_in_base_unit' => $totalPiecesToIssue,
                    'unit_cost'             => $inventoryItem->unit_cost ?? 0,
                    'total_value'           => $totalPiecesToIssue * ($inventoryItem->unit_cost ?? 0),
                    'reason'                => 'Issued to ' . ($requisition->department->name ?? 'Department') . ' - Req: ' . $requisition->requisition_number,
                    'movement_date'         => now()->toDateString(),
                    'approved_at'           => now(),
                    'approved_by'           => Auth::id(),
                    'created_by'            => Auth::id(),
                    'taken_by'              => $request->taken_by,
                    'stock_before'          => $totalStockBefore,
                    'stock_after'           => $totalStockAfter,
                ]);

                // Store batch deduction details in a separate log or JSON field (optional)
                // You can add a 'batch_details' JSON column to stock_movements if needed
                Log::info('Batch deduction details', [
                    'movement_number' => $movementNumber,
                    'item_id' => $inventoryItem->id,
                    'item_name' => $inventoryItem->name,
                    'total_issued' => $totalPiecesToIssue,
                    'batches_used' => $deductionDetails
                ]);

                Log::info('Item issued to department with batch tracking', [
                    'user_id'         => Auth::id(),
                    'requisition_id'  => $requisition->id,
                    'item_id'         => $inventoryItem->id,
                    'quantity_issued' => $quantityIssued,
                    'pack_type'       => $packType,
                    'pack_size'       => $packSize,
                    'total_pieces'    => $totalPiecesToIssue,
                    'stock_before'    => $totalStockBefore,
                    'stock_after'     => $totalStockAfter,
                    'taken_by'        => $request->taken_by,
                    'batches_used'    => $deductionDetails,
                ]);

                $anyIssued = true;

                // Compare issued base-unit pieces against what was originally requested
                $requestedPieces = ($reqItem->requested_pack_type && $reqItem->requested_pack_size)
                    ? $reqItem->quantity_requested * $reqItem->requested_pack_size
                    : $reqItem->quantity_requested;

                if (($reqItem->issued_total_pieces ?? 0) < $requestedPieces) {
                    $allFullyIssued = false;
                }
            }

            // Update requisition status
            if ($allFullyIssued && $anyIssued) {
                $requisition->status = 'issued';
            } elseif ($anyIssued) {
                $requisition->status = 'partially_issued';
            }

            if ($anyIssued) {
                $requisition->taken_by = $request->taken_by;
            }

            if ($request->filled('store_notes')) {
                $requisition->store_notes = $request->store_notes;
            }

            $requisition->save();

            DB::commit();

            Log::info('Items issued to department with batch tracking', [
                'user_id'            => Auth::id(),
                'requisition_id'     => $requisition->id,
                'requisition_number' => $requisition->requisition_number,
                'taken_by'           => $request->taken_by,
            ]);

            return redirect()->route('store.department-requisitions.show', $requisition->id)
                ->with('success', 'Items issued successfully. Taken by: ' . $request->taken_by);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error issuing items', [
                'requisition_id' => $id,
                'error'          => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Error issuing items: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // RETURN FORM
    // ─────────────────────────────────────────────────────────────────────────────

    public function returnForm($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = DepartmentRequisition::with(['items.inventoryItem', 'department'])
            ->whereIn('status', ['issued', 'partially_issued', 'partially_returned', 'partially_consumed'])
            ->findOrFail($id);

        foreach ($requisition->items as $item) {
            Log::info('Return form item data', [
                'item_id'             => $item->id,
                'item_name'           => $item->inventoryItem->name ?? 'N/A',
                'issued_pack_type'    => $item->issued_pack_type,
                'issued_pack_size'    => $item->issued_pack_size,
                'quantity_issued'     => $item->quantity_issued,
                'quantity_consumed'   => $item->quantity_consumed,
                'quantity_returned'   => $item->quantity_returned,
                'issued_total_pieces' => $item->issued_total_pieces,
            ]);
        }

        return view('store.department_requisitions.return', compact('requisition'));
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // PROCESS RETURN
    // ─────────────────────────────────────────────────────────────────────────────

    public function processReturn(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'items'                     => 'required|array',
            'items.*.item_id'           => 'required|exists:department_requisition_items,id',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.pack_size'         => 'nullable|numeric|min:1',
            'items.*.number_of_packs'   => 'nullable|numeric|min:0',
            'items.*.quantity_returned' => 'nullable|numeric|min:0',
            'items.*.total_pieces'      => 'nullable|numeric|min:0',
            'items.*.return_reason'     => 'nullable|string',
            'global_return_reason'      => 'nullable|string',
            'returned_by'               => 'required|string|max:255',
            'store_notes'               => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $requisition  = DepartmentRequisition::findOrFail($id);
            $anyReturned  = false;
            $globalReason = $request->global_return_reason;

            foreach ($request->items as $itemData) {
                $reqItem       = DepartmentRequisitionItem::findOrFail($itemData['item_id']);
                $inventoryItem = InventoryItem::find($itemData['inventory_item_id']);

                $packSize = $reqItem->issued_pack_size ?? 1;
                $packType = $reqItem->issued_pack_type ?? 'unit';

                $packsReturned       = isset($itemData['number_of_packs']) ? (float) $itemData['number_of_packs'] : 0;
                $packReturnPieces    = $packsReturned * $packSize;
                $pieceReturnPieces   = isset($itemData['quantity_returned']) ? (float) $itemData['quantity_returned'] : 0;
                $totalPiecesReturned = $packReturnPieces + $pieceReturnPieces;

                if ($totalPiecesReturned <= 0) {
                    continue;
                }

                // Validate not exceeding available
                $alreadyReturnedPieces = (float) ($reqItem->returned_total_pieces ?? 0);
                $availableToReturn     = (float) ($reqItem->issued_total_pieces ?? 0) - $alreadyReturnedPieces;

                if ($totalPiecesReturned > $availableToReturn) {
                    throw new \Exception(
                        'Cannot return more than available. Item: ' . ($reqItem->inventoryItem->name ?? 'N/A') .
                        ', Available: ' . $availableToReturn . ' pieces' .
                        ', Attempting: ' . $totalPiecesReturned . ' pieces'
                    );
                }

                $newTotalReturnedPieces         = $alreadyReturnedPieces + $totalPiecesReturned;
                $reqItem->returned_total_pieces = $newTotalReturnedPieces;

                if ($packsReturned > 0) {
                    $reqItem->quantity_returned  = ($reqItem->quantity_returned ?? 0) + $packsReturned;
                    $reqItem->returned_pack_type = $packType;
                    $reqItem->returned_pack_size = $packSize;
                }

                $reqItem->return_reason = $itemData['return_reason'] ?? $globalReason;
                $reqItem->returned_at   = now();
                $reqItem->save();

                // Add back to inventory stock (summary)
                $stockBefore = (float) ($inventoryItem->current_stock ?? 0);
                $stockAfter  = $stockBefore + $totalPiecesReturned;

                $inventoryItem->current_stock = $stockAfter;
                $inventoryItem->save();

                // Create a new batch for returned items or add to oldest active batch
                // For simplicity, we'll add to an existing active batch or create a new one
                $existingBatch = Batch::where('inventory_item_id', $inventoryItem->id)
                    ->where('batch_status', 'active')
                    ->orderBy('created_at', 'asc')
                    ->first();

                if ($existingBatch && $existingBatch->unit_cost == ($inventoryItem->unit_cost ?? 0)) {
                    // Add to existing batch
                    $existingBatch->remaining_quantity += $totalPiecesReturned;
                    $existingBatch->save();
                    $usedBatchNumber = $existingBatch->batch_number;
                } else {
                    // Create new batch for returned items
                    $newBatchNumber = 'BAT-RET-' . date('Ymd') . '-' . str_pad($inventoryItem->id, 6, '0', STR_PAD_LEFT);
                    $newBatch = Batch::create([
                        'batch_number' => $newBatchNumber,
                        'inventory_item_id' => $inventoryItem->id,
                        'goods_received_note_id' => null,
                        'supplier_id' => null,
                        'initial_quantity' => $totalPiecesReturned,
                        'remaining_quantity' => $totalPiecesReturned,
                        'unit_cost' => $inventoryItem->unit_cost ?? 0,
                        'total_cost' => $totalPiecesReturned * ($inventoryItem->unit_cost ?? 0),
                        'base_unit' => $inventoryItem->base_unit ?? 'pcs',
                        'batch_status' => 'active',
                        'notes' => 'Returned from requisition: ' . $requisition->requisition_number,
                    ]);
                    $usedBatchNumber = $newBatch->batch_number;
                }

                // Build reason text
                $returnDetails = [];
                if ($packsReturned > 0) {
                    $returnDetails[] = "{$packsReturned} {$packType}(s) ({$packReturnPieces} pieces)";
                }
                if ($pieceReturnPieces > 0) {
                    $returnDetails[] = "{$pieceReturnPieces} individual pieces";
                }
                $returnDetailsText = implode(' + ', $returnDetails);

                $movementNumber = 'RET-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

                StockMovement::create([
                    'movement_number'       => $movementNumber,
                    'inventory_item_id'     => $inventoryItem->id,
                    'store_id'              => 1,
                    'movement_type_id'      => 2,
                    'quantity'              => $totalPiecesReturned,
                    'pack_type'             => $packsReturned > 0 ? $packType : null,
                    'pack_size'             => $packSize,
                    'number_of_packs'       => $packsReturned > 0 ? $packsReturned : null,
                    'base_unit'             => $inventoryItem->base_unit ?? 'units',
                    'unit_id'               => null,
                    'quantity_in_base_unit' => $totalPiecesReturned,
                    'unit_cost'             => $inventoryItem->unit_cost ?? 0,
                    'total_value'           => $totalPiecesReturned * ($inventoryItem->unit_cost ?? 0),
                    'reason'                => 'RETURN from ' . ($requisition->department->name ?? 'Department') .
                                              ' - Req: ' . $requisition->requisition_number .
                                              ' - ' . $returnDetailsText .
                                              ($globalReason ? ' - ' . $globalReason : ''),
                    'movement_date'         => now()->toDateString(),
                    'approved_at'           => now(),
                    'approved_by'           => Auth::id(),
                    'created_by'            => Auth::id(),
                    'returned_by'           => $request->returned_by,
                    'stock_before'          => $stockBefore,
                    'stock_after'           => $stockAfter,
                ]);

                Log::info('Items returned to store with batch tracking', [
                    'user_id'             => Auth::id(),
                    'requisition_id'      => $requisition->id,
                    'item_id'             => $inventoryItem->id,
                    'item_name'           => $inventoryItem->name,
                    'packs_returned'      => $packsReturned,
                    'piece_return_pieces' => $pieceReturnPieces,
                    'total_pieces'        => $totalPiecesReturned,
                    'stock_before'        => $stockBefore,
                    'stock_after'         => $stockAfter,
                    'returned_by'         => $request->returned_by,
                    'batch_updated'       => $usedBatchNumber,
                ]);

                $anyReturned = true;
            }

            // Update requisition status
            if ($anyReturned) {
                $requisition->load('items');

                $totalIssued    = (float) $requisition->items->sum('issued_total_pieces');
                $totalConsumed  = (float) $requisition->items->sum('quantity_consumed');
                $totalSold      = (float) $requisition->items->sum('quantity_sold');
                $totalReturned  = (float) $requisition->items->sum('returned_total_pieces');
                $totalProcessed = $totalConsumed + $totalSold + $totalReturned;

                if ($totalIssued > 0 && $totalProcessed >= $totalIssued) {
                    $requisition->status = 'completed';
                } elseif ($totalReturned > 0) {
                    $requisition->status = 'partially_returned';
                } elseif ($totalConsumed > 0) {
                    $requisition->status = 'partially_consumed';
                }

                $requisition->returned_by = $request->returned_by;
            }

            if ($request->filled('store_notes')) {
                $existing = $requisition->store_notes;
                $newNote  = 'Return Notes: ' . $request->store_notes;
                $requisition->store_notes = $existing ? $existing . "\n\n" . $newNote : $newNote;
            }

            $requisition->save();

            DB::commit();

            return redirect()->route('store.department-requisitions.show', $requisition->id)
                ->with('success', 'Items returned successfully by: ' . $request->returned_by . '. Stock added back to inventory.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing return', [
                'requisition_id' => $id,
                'error'          => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Error processing return: ' . $e->getMessage())
                ->withInput();
        }
    }
}
