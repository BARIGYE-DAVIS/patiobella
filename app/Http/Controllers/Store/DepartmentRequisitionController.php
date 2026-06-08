<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\DepartmentRequisition;
use App\Models\DepartmentRequisitionItem;
use App\Models\InventoryItem;
use App\Models\DepartmentStockMovement;
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

/**
 * SHOW - Display requisition details with batch information
 */
public function show($id)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'STORE') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $requisition = DepartmentRequisition::with([
        'department',
        'requestedBy',
        'approvedBy',
        'items.inventoryItem'
    ])->findOrFail($id);

    // Decode batch issuances and returns for each item
    foreach ($requisition->items as $item) {
        // Decode batch issuances JSON
        if ($item->batch_issuances) {
            $item->batch_issuances = json_decode($item->batch_issuances, true);
        } else {
            $item->batch_issuances = [];
        }

        // Decode batch returns JSON
        if ($item->batch_returns) {
            $item->batch_returns = json_decode($item->batch_returns, true);
        } else {
            $item->batch_returns = [];
        }

        // Get batch details for display
        $batchIds = array_column($item->batch_issuances, 'batch_id');
        if (!empty($batchIds)) {
            $item->used_batches = Batch::whereIn('id', $batchIds)->get()->keyBy('id');
        } else {
            $item->used_batches = collect();
        }
    }

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

/**
 * ISSUE FORM - Show form to issue items with batch selection
 */
public function issueForm($id)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'STORE') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $requisition = DepartmentRequisition::with([
        'items.inventoryItem',
        'department',
        'requestedBy',
        'approvedBy'
    ])->findOrFail($id);

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

/**
 * ISSUE — Process issuing items with FIFO batch selection
 * Stores multiple batch data as JSON in batch_issuances column
 */
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
        'batches'                 => 'nullable|array',
        'batches.*.*.batch_id'    => 'nullable|exists:batches,id',
        'batches.*.*.quantity'    => 'nullable|numeric|min:0',
        'taken_by'                => 'required|string|max:255',
        'store_notes'             => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $requisition    = DepartmentRequisition::findOrFail($id);
        $anyIssued      = false;
        $allFullyIssued = true;

        foreach ($request->items as $itemIndex => $itemData) {
            $reqItem       = DepartmentRequisitionItem::findOrFail($itemData['item_id']);
            $inventoryItem = InventoryItem::find($reqItem->inventory_item_id);

            $totalToIssue  = (float) ($itemData['quantity_issued'] ?? 0);
            $approvedQty   = (float) ($reqItem->quantity_approved ?? $reqItem->quantity_requested);
            $alreadyIssued = (float) ($reqItem->issued_total_pieces ?? 0);
            $unit          = $inventoryItem->unit_of_measurement ?? 'piece';

            if ($totalToIssue > 0) {
                $anyIssued        = true;
                $batchData        = $request->input('batches.' . $itemIndex, []);
                $remainingToIssue = $totalToIssue;
                $batchesUsed      = [];
                $totalIssuedFromBatches = 0;

                foreach ($batchData as $batchItem) {
                    if ($remainingToIssue <= 0) break;

                    $batchId           = $batchItem['batch_id'] ?? null;
                    $quantityFromBatch = (float) ($batchItem['quantity'] ?? 0);

                    if (!$batchId || $quantityFromBatch <= 0) continue;

                    $batch = Batch::find($batchId);

                    if (!$batch) {
                        throw new \Exception("Batch not found: {$batchId}");
                    }

                    if ($batch->remaining_quantity < $quantityFromBatch) {
                        throw new \Exception("Insufficient stock in batch {$batch->batch_number}. Available: {$batch->remaining_quantity}, Requested: {$quantityFromBatch}");
                    }

                    $takeFromBatch = min($quantityFromBatch, $remainingToIssue, $batch->remaining_quantity);

                    if ($takeFromBatch <= 0) continue;

                    // Reduce batch quantity
                    $batch->remaining_quantity -= $takeFromBatch;

                    if ($batch->remaining_quantity <= 0) {
                        $batch->batch_status = 'depleted';
                    } elseif ($batch->remaining_quantity < $batch->initial_quantity) {
                        $batch->batch_status = 'partially_used';
                    }

                    $batch->save();

                    $batchesUsed[] = [
                        'batch_id'     => $batch->id,
                        'batch_number' => $batch->batch_number,
                        'quantity'     => $takeFromBatch,
                        'unit_cost'    => $batch->unit_cost,
                    ];

                    $totalIssuedFromBatches += $takeFromBatch;

                    // Stock movement for this batch
                    $movementNumber = 'ISS-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

                    StockMovement::create([
                        'movement_number'       => $movementNumber,
                        'inventory_item_id'     => $inventoryItem->id,
                        'batch_id'              => $batch->id,
                        'store_id'              => 1,
                        'movement_type_id'      => 5,
                        'quantity'              => $takeFromBatch,
                        'pack_type'             => null,
                        'pack_size'             => null,
                        'number_of_packs'       => null,
                        'base_unit'             => $unit,
                        'quantity_in_base_unit' => $takeFromBatch,
                        'unit_cost'             => $batch->unit_cost,
                        'total_value'           => $takeFromBatch * $batch->unit_cost,
                        'reason'                => 'Issued to ' . ($requisition->department->name ?? 'Department') . ' - Req: ' . $requisition->requisition_number,
                        'movement_date'         => now()->toDateString(),
                        'approved_at'           => now(),
                        'approved_by'           => Auth::id(),
                        'created_by'            => Auth::id(),
                        'taken_by'              => $request->taken_by,
                    ]);

                    $remainingToIssue -= $takeFromBatch;
                }

                // =========================================================
                // RECORD IN DEPARTMENT_STOCK_MOVEMENTS TABLE
                // =========================================================

                // Get current opening balance for this department & item
                $lastMovement = DepartmentStockMovement::where('department_id', $requisition->department_id)
                    ->where('inventory_item_id', $inventoryItem->id)
                    ->orderBy('movement_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $currentBalance = $lastMovement ? $lastMovement->closing_balance : 0;

                // Create department stock movement record
                DepartmentStockMovement::create([
                    'movement_number'     => DepartmentStockMovement::generateMovementNumber(),
                    'department_id'       => $requisition->department_id,
                    'inventory_item_id'   => $inventoryItem->id,
                    'batch_id'            => $batchesUsed[0]['batch_id'] ?? null,
                    'requisition_item_id' => $reqItem->id,
                    'opening_balance'     => $currentBalance,
                    'added_quantity'      => $totalIssuedFromBatches,
                    'used_quantity'       => 0,
                    'returned_quantity'   => 0,
                    'closing_balance'     => $currentBalance + $totalIssuedFromBatches,
                    'movement_type'       => 'issue',
                    'movement_date'       => now()->toDateString(),
                    'notes'               => $reqItem->notes ?? 'Issued from requisition: ' . $requisition->requisition_number,
                    'created_by'          => Auth::id(),
                ]);

                // Update item totals
                $newIssuedTotal               = $alreadyIssued + $totalToIssue;
                $reqItem->issued_total_pieces = $newIssuedTotal;
                $reqItem->quantity_issued     = $totalToIssue;
                $reqItem->batch_issuances     = json_encode($batchesUsed);
                $reqItem->batch_id            = $batchesUsed[0]['batch_id'] ?? $reqItem->batch_id;

                if ($newIssuedTotal < $approvedQty) {
                    $allFullyIssued = false;
                }

                Log::info('Item issued with batch tracking', [
                    'requisition_id' => $requisition->id,
                    'item_id'        => $inventoryItem->id,
                    'total_issued'   => $totalToIssue,
                    'batches_used'   => $batchesUsed,
                    'taken_by'       => $request->taken_by,
                ]);

            } else {
                $reqItem->quantity_issued = 0;

                if (is_null($reqItem->batch_issuances)) {
                    $reqItem->batch_issuances = json_encode([]);
                }

                if ($alreadyIssued < $approvedQty) {
                    $allFullyIssued = false;
                }

                Log::info('Item skipped — zero quantity to issue', [
                    'requisition_id' => $requisition->id,
                    'item_id'        => $reqItem->inventory_item_id,
                    'approved_qty'   => $approvedQty,
                    'already_issued' => $alreadyIssued,
                ]);
            }

            $reqItem->save();
        }

        // Update requisition status
        if ($allFullyIssued && $anyIssued) {
            $requisition->status = 'issued';
        } elseif ($anyIssued) {
            $requisition->status = 'partially_issued';
        }

        $requisition->taken_by = $request->taken_by;

        if ($request->filled('store_notes')) {
            $requisition->store_notes = $request->store_notes;
        }

        $requisition->save();

        DB::commit();

        Log::info('Requisition issue completed', [
            'requisition_id' => $requisition->id,
            'status'         => $requisition->status,
            'taken_by'       => $request->taken_by,
            'any_issued'     => $anyIssued,
            'all_fully'      => $allFullyIssued,
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
        return redirect()->back()
            ->with('error', 'Error issuing items: ' . $e->getMessage())
            ->withInput();
    }
}
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

    /**
 * PROCESS RETURN — Process returned items with batch tracking
 */
public function processReturn(Request $request, $id)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'STORE') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $request->validate([
        'items'                  => 'required|array',
        'items.*.item_id'        => 'required|exists:department_requisition_items,id',
        'items.*.quantity_returned' => 'required|numeric|min:0',
        'returned_by'            => 'required|string|max:255',
        'return_reason'          => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $requisition = DepartmentRequisition::findOrFail($id);
        $anyReturned = false;

        foreach ($request->items as $itemData) {
            $reqItem = DepartmentRequisitionItem::findOrFail($itemData['item_id']);
            $quantityToReturn = (float) ($itemData['quantity_returned'] ?? 0);

            if ($quantityToReturn <= 0) {
                continue;
            }

            $anyReturned = true;
            $inventoryItem = InventoryItem::find($reqItem->inventory_item_id);
            $unit = $inventoryItem->unit_of_measurement ?? 'piece';

            // Get current batch issuances
            $batchIssuances = json_decode($reqItem->batch_issuances, true) ?? [];

            // Track returns by batch (FIFO - return to most recent batch first)
            $remainingToReturn = $quantityToReturn;
            $returnsByBatch = [];

            // Reverse the batch issuances to return to most recent first
            $reversedBatches = array_reverse($batchIssuances);

            foreach ($reversedBatches as &$batchIssued) {
                if ($remainingToReturn <= 0) break;

                $batchId = $batchIssued['batch_id'];
                $issuedQuantity = $batchIssued['quantity'];

                // Check how much of this batch has already been returned
                $existingReturns = json_decode($reqItem->batch_returns, true) ?? [];
                $alreadyReturnedFromBatch = 0;
                foreach ($existingReturns as $ret) {
                    if ($ret['batch_id'] == $batchId) {
                        $alreadyReturnedFromBatch += $ret['quantity'];
                    }
                }

                $availableToReturn = $issuedQuantity - $alreadyReturnedFromBatch;

                if ($availableToReturn <= 0) continue;

                $returnFromThisBatch = min($remainingToReturn, $availableToReturn);

                // Return stock to batch
                $batch = Batch::find($batchId);
                if ($batch) {
                    $batch->remaining_quantity += $returnFromThisBatch;
                    if ($batch->remaining_quantity > 0 && $batch->batch_status == 'depleted') {
                        $batch->batch_status = 'partially_used';
                    } elseif ($batch->remaining_quantity == $batch->initial_quantity) {
                        $batch->batch_status = 'active';
                    }
                    $batch->save();
                }

                $returnsByBatch[] = [
                    'batch_id' => $batchId,
                    'batch_number' => $batchIssued['batch_number'],
                    'quantity' => $returnFromThisBatch,
                    'unit_cost' => $batchIssued['unit_cost'] ?? 0,
                ];

                $remainingToReturn -= $returnFromThisBatch;
            }

            // Update batch_returns in requisition item
            $existingReturns = json_decode($reqItem->batch_returns, true) ?? [];
            foreach ($returnsByBatch as $return) {
                $found = false;
                foreach ($existingReturns as &$existing) {
                    if ($existing['batch_id'] == $return['batch_id']) {
                        $existing['quantity'] += $return['quantity'];
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $existingReturns[] = $return;
                }
            }

            $reqItem->batch_returns = json_encode($existingReturns);
            $reqItem->quantity_returned = ($reqItem->quantity_returned ?? 0) + $quantityToReturn;
            $reqItem->return_reason = $request->return_reason ?? $reqItem->return_reason;
            $reqItem->returned_at = now();
            $reqItem->save();

            // =========================================================
            // RECORD RETURN IN DEPARTMENT_STOCK_MOVEMENTS TABLE
            // =========================================================

            // Get current balance before return
            $lastMovement = DepartmentStockMovement::where('department_id', $requisition->department_id)
                ->where('inventory_item_id', $inventoryItem->id)
                ->orderBy('movement_date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $currentBalance = $lastMovement ? $lastMovement->closing_balance : 0;

            // Create department stock movement record for return
            DepartmentStockMovement::create([
                'movement_number'     => DepartmentStockMovement::generateMovementNumber(),
                'department_id'       => $requisition->department_id,
                'inventory_item_id'   => $inventoryItem->id,
                'batch_id'            => $returnsByBatch[0]['batch_id'] ?? null,
                'requisition_item_id' => $reqItem->id,
                'opening_balance'     => $currentBalance,
                'added_quantity'      => 0,
                'used_quantity'       => 0,
                'returned_quantity'   => $quantityToReturn,
                'closing_balance'     => $currentBalance - $quantityToReturn,
                'movement_type'       => 'return',
                'movement_date'       => now()->toDateString(),
                'notes'               => 'Returned from requisition: ' . $requisition->requisization_number . ' - Reason: ' . ($request->return_reason ?? 'N/A'),
                'created_by'          => Auth::id(),
            ]);

            // Create stock movement record for audit
            $movementNumber = 'RET-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            StockMovement::create([
                'movement_number'       => $movementNumber,
                'inventory_item_id'     => $inventoryItem->id,
                'batch_id'              => $returnsByBatch[0]['batch_id'] ?? null,
                'store_id'              => 1,
                'movement_type_id'      => 6, // Return to store
                'quantity'              => $quantityToReturn,
                'pack_type'             => null,
                'pack_size'             => null,
                'number_of_packs'       => null,
                'base_unit'             => $unit,
                'quantity_in_base_unit' => $quantityToReturn,
                'unit_cost'             => $returnsByBatch[0]['unit_cost'] ?? 0,
                'total_value'           => $quantityToReturn * ($returnsByBatch[0]['unit_cost'] ?? 0),
                'reason'                => 'Return from ' . ($requisition->department->name ?? 'Department') . ' - Req: ' . $requisition->requisition_number . ' - Reason: ' . ($request->return_reason ?? 'N/A'),
                'movement_date'         => now()->toDateString(),
                'approved_at'           => now(),
                'approved_by'           => Auth::id(),
                'created_by'            => Auth::id(),
                'returned_by'           => $request->returned_by,
            ]);

            Log::info('Item returned with batch tracking', [
                'requisition_id' => $requisition->id,
                'item_id'        => $inventoryItem->id,
                'quantity_returned' => $quantityToReturn,
                'returns_by_batch' => $returnsByBatch,
                'returned_by'    => $request->returned_by,
            ]);
        }

        // Update requisition status
        if ($anyReturned) {
            // Check if all issued items have been returned
            $totalIssued = $requisition->items->sum('issued_total_pieces');
            $totalReturned = $requisition->items->sum('quantity_returned');

            if ($totalReturned >= $totalIssued && $totalIssued > 0) {
                $requisition->status = 'returned';
            } else {
                $requisition->status = 'partially_returned';
            }
        }

        $requisition->returned_by = $request->returned_by;
        $requisition->save();

        DB::commit();

        Log::info('Requisition return completed', [
            'requisition_id' => $requisition->id,
            'status'         => $requisition->status,
            'returned_by'    => $request->returned_by,
        ]);

        return redirect()->route('store.department-requisitions.show', $requisition->id)
            ->with('success', 'Items returned successfully. Returned by: ' . $request->returned_by);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error returning items', [
            'requisition_id' => $id,
            'error'          => $e->getMessage(),
            'trace'          => $e->getTraceAsString(),
        ]);
        return redirect()->back()
            ->with('error', 'Error returning items: ' . $e->getMessage())
            ->withInput();
    }
}


}
