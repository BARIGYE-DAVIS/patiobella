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
        $requisition = DepartmentRequisition::findOrFail($id);
        $anyIssued = false;
        $allFullyIssued = true;

        foreach ($request->items as $itemIndex => $itemData) {
            $reqItem = DepartmentRequisitionItem::findOrFail($itemData['item_id']);
            $inventoryItem = InventoryItem::find($reqItem->inventory_item_id);

            $totalToIssue = (float) $itemData['quantity_issued'];
            $approvedQty = (float) ($reqItem->quantity_approved ?? $reqItem->quantity_requested);
            $alreadyIssued = (float) ($reqItem->issued_total_pieces ?? 0);

            if ($totalToIssue <= 0) {
                if ($alreadyIssued < $approvedQty) {
                    $allFullyIssued = false;
                }
                continue;
            }

            $anyIssued = true;

            // Get batch details from the request
            $batchData = $request->input('batches.' . $itemIndex, []);
            $remainingToIssue = $totalToIssue;
            $batchesUsed = [];
            $unit = $inventoryItem->unit_of_measurement ?? 'piece';

            foreach ($batchData as $batchItem) {
                if ($remainingToIssue <= 0) break;

                $batchId = $batchItem['batch_id'] ?? null;
                $quantityFromBatch = (float) ($batchItem['quantity'] ?? 0);

                if (!$batchId || $quantityFromBatch <= 0) continue;

                $batch = Batch::find($batchId);
                if (!$batch || $batch->remaining_quantity < $quantityFromBatch) {
                    throw new \Exception("Insufficient stock in batch {$batch->batch_number}");
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
                    'batch_id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'quantity' => $takeFromBatch,
                    'unit_cost' => $batch->unit_cost,
                ];

                // Create stock movement for this batch
                $movementNumber = 'ISS-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

                StockMovement::create([
                    'movement_number' => $movementNumber,
                    'inventory_item_id' => $inventoryItem->id,
                    'batch_id' => $batch->id,
                    'store_id' => 1,
                    'movement_type_id' => 5,
                    'quantity' => $takeFromBatch,
                    'pack_type' => null,
                    'pack_size' => null,
                    'number_of_packs' => null,
                    'base_unit' => $unit,
                    'quantity_in_base_unit' => $takeFromBatch,
                    'unit_cost' => $batch->unit_cost,
                    'total_value' => $takeFromBatch * $batch->unit_cost,
                    'reason' => 'Issued to ' . ($requisition->department->name ?? 'Department') . ' - Req: ' . $requisition->requisition_number,
                    'movement_date' => now()->toDateString(),
                    'approved_at' => now(),
                    'approved_by' => Auth::id(),
                    'created_by' => Auth::id(),
                    'taken_by' => $request->taken_by,
                ]);

                $remainingToIssue -= $takeFromBatch;
            }

            if ($remainingToIssue > 0) {
                throw new \Exception("Could not fulfill full quantity for {$inventoryItem->name}. Missing: {$remainingToIssue} {$unit}");
            }

            // Update requisition item with JSON batch data
            $newIssuedTotal = $alreadyIssued + $totalToIssue;
            $reqItem->issued_total_pieces = $newIssuedTotal;
            $reqItem->quantity_issued = $totalToIssue;
            $reqItem->batch_issuances = json_encode($batchesUsed);

            // Keep first batch_id for backward compatibility
            $reqItem->batch_id = $batchesUsed[0]['batch_id'] ?? null;
            $reqItem->save();

            Log::info('Item issued with batch tracking', [
                'requisition_id' => $requisition->id,
                'item_id' => $inventoryItem->id,
                'total_issued' => $totalToIssue,
                'batches_used' => $batchesUsed,
                'taken_by' => $request->taken_by,
            ]);

            // Check if fully issued
            if ($newIssuedTotal < $approvedQty) {
                $allFullyIssued = false;
            }
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

        return redirect()->route('store.department-requisitions.show', $requisition->id)
            ->with('success', 'Items issued successfully. Taken by: ' . $request->taken_by);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error issuing items', [
            'requisition_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return redirect()->back()
            ->with('error', 'Error issuing items: ' . $e->getMessage())
            ->withInput();
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
        'items'                     => 'required|array',
        'items.*.item_id'           => 'required|exists:department_requisition_items,id',
        'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
        'items.*.batch_id'          => 'nullable|exists:batches,id',
        'items.*.quantity_returned' => 'nullable|numeric|min:0',
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
        $returnsData = [];

        foreach ($request->items as $itemData) {
            $reqItem = DepartmentRequisitionItem::findOrFail($itemData['item_id']);
            $inventoryItem = InventoryItem::find($itemData['inventory_item_id']);

            $totalPiecesReturned = (float) ($itemData['quantity_returned'] ?? 0);
            $batchId = $itemData['batch_id'] ?? null;
            $returnReason = $itemData['return_reason'] ?? $globalReason;

            if ($totalPiecesReturned <= 0) {
                continue;
            }

            // Validate not exceeding issued quantity
            $alreadyIssued = (float) ($reqItem->issued_total_pieces ?? 0);
            $alreadyReturned = (float) ($reqItem->returned_total_pieces ?? 0);
            $availableToReturn = $alreadyIssued - $alreadyReturned;

            if ($totalPiecesReturned > $availableToReturn) {
                throw new \Exception(
                    'Cannot return more than issued. Item: ' . ($inventoryItem->name ?? 'N/A') .
                    ', Issued: ' . $alreadyIssued .
                    ', Already Returned: ' . $alreadyReturned .
                    ', Attempting: ' . $totalPiecesReturned
                );
            }

            $unit = $inventoryItem->unit_of_measurement ?? 'piece';

            // Get existing batch returns or initialize empty array
            $existingReturns = json_decode($reqItem->batch_returns, true) ?? [];

            // Add this return to the batch returns array
            $existingReturns[] = [
                'batch_id' => $batchId,
                'quantity' => $totalPiecesReturned,
                'reason' => $returnReason,
                'returned_at' => now()->toDateTimeString(),
                'returned_by' => $request->returned_by,
            ];

            // Update requisition item
            $newTotalReturned = $alreadyReturned + $totalPiecesReturned;
            $reqItem->returned_total_pieces = $newTotalReturned;
            $reqItem->quantity_returned = $totalPiecesReturned;
            $reqItem->batch_returns = json_encode($existingReturns);
            $reqItem->return_reason = $returnReason;
            $reqItem->returned_at = now();
            $reqItem->save();

            // If batch_id is provided, add stock back to that specific batch
            if ($batchId) {
                $batch = Batch::find($batchId);
                if ($batch) {
                    $batch->remaining_quantity += $totalPiecesReturned;
                    if ($batch->batch_status === 'depleted') {
                        $batch->batch_status = 'active';
                    }
                    $batch->save();
                    $usedBatchNumber = $batch->batch_number;
                } else {
                    $usedBatchNumber = 'unknown';
                }
            } else {
                // Find oldest active batch to add stock back
                $batch = Batch::where('inventory_item_id', $inventoryItem->id)
                    ->where('batch_status', 'active')
                    ->orderBy('expiry_date', 'asc')
                    ->first();

                if ($batch) {
                    $batch->remaining_quantity += $totalPiecesReturned;
                    $batch->save();
                    $usedBatchNumber = $batch->batch_number;
                } else {
                    // Create new batch for returned items
                    $newBatchNumber = 'BAT-RET-' . date('Ymd') . '-' . str_pad($inventoryItem->id, 6, '0', STR_PAD_LEFT);
                    $batch = Batch::create([
                        'batch_number' => $newBatchNumber,
                        'inventory_item_id' => $inventoryItem->id,
                        'initial_quantity' => $totalPiecesReturned,
                        'remaining_quantity' => $totalPiecesReturned,
                        'unit_cost' => $inventoryItem->last_purchase_price ?? 0,
                        'unit_of_measurement' => $unit,
                        'batch_status' => 'active',
                        'notes' => 'Returned from requisition: ' . $requisition->requisition_number,
                    ]);
                    $usedBatchNumber = $newBatchNumber;
                }
            }

            // Create stock movement for return
            $movementNumber = 'RET-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            StockMovement::create([
                'movement_number' => $movementNumber,
                'inventory_item_id' => $inventoryItem->id,
                'batch_id' => $batchId ?? ($batch->id ?? null),
                'store_id' => 1,
                'movement_type_id' => 2,
                'quantity' => $totalPiecesReturned,
                'base_unit' => $unit,
                'quantity_in_base_unit' => $totalPiecesReturned,
                'unit_cost' => $inventoryItem->last_purchase_price ?? 0,
                'total_value' => $totalPiecesReturned * ($inventoryItem->last_purchase_price ?? 0),
                'reason' => 'RETURN from ' . ($requisition->department->name ?? 'Department') .
                           ' - Req: ' . $requisition->requisition_number .
                           ($returnReason ? ' - Reason: ' . $returnReason : ''),
                'movement_date' => now()->toDateString(),
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),
                'returned_by' => $request->returned_by,
            ]);

            $returnsData[] = [
                'item' => $inventoryItem->name,
                'quantity' => $totalPiecesReturned,
                'batch' => $usedBatchNumber,
                'reason' => $returnReason,
            ];

            $anyReturned = true;

            Log::info('Items returned with batch tracking', [
                'requisition_id' => $requisition->id,
                'item_id' => $inventoryItem->id,
                'item_name' => $inventoryItem->name,
                'quantity_returned' => $totalPiecesReturned,
                'batch_used' => $usedBatchNumber,
                'returned_by' => $request->returned_by,
                'reason' => $returnReason,
            ]);
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
            }

            $requisition->returned_by = $request->returned_by;
        }

        if ($request->filled('store_notes')) {
            $existing = $requisition->store_notes;
            $newNote = now()->format('Y-m-d H:i') . ' - Return Notes: ' . $request->store_notes;
            $requisition->store_notes = $existing ? $existing . "\n" . $newNote : $newNote;
        }

        $requisition->save();

        DB::commit();

        Log::info('Returns processed successfully', [
            'requisition_id' => $requisition->id,
            'returns_count' => count($returnsData),
            'returned_by' => $request->returned_by,
        ]);

        return redirect()->route('store.department-requisitions.show', $requisition->id)
            ->with('success', 'Items returned successfully by: ' . $request->returned_by);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error processing return', [
            'requisition_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return redirect()->back()
            ->with('error', 'Error processing return: ' . $e->getMessage())
            ->withInput();
    }
}
}
