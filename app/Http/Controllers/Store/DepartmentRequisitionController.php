<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\DepartmentRequisition;
use App\Models\DepartmentRequisitionItem;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentRequisitionController extends Controller
{
    /**
     * Display a listing of requisitions from all departments.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = DepartmentRequisition::with(['department', 'requestedBy', 'items']);

        // Filter by status — if none selected, return ALL statuses
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

        $departments = \App\Models\Department::whereIn('name', ['KITCHEN', 'BAR', 'CAFE'])->get();

        return view('store.department_requisitions.index', compact('requisitions', 'departments'));
    }

    /**
     * Display the specified requisition.
     */
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

    /**
     * Approve a requisition.
     */
    public function approve($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        DB::beginTransaction();

        try {
            $requisition = DepartmentRequisition::findOrFail($id);

            if ($requisition->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requisitions can be approved.');
            }

            $requisition->status = 'approved';
            $requisition->approved_by = Auth::id();
            $requisition->approved_at = now();
            $requisition->save();

            DB::commit();

            Log::info('Department requisition approved', [
                'user_id' => Auth::id(),
                'requisition_id' => $requisition->id,
                'requisition_number' => $requisition->requisition_number
            ]);

            return redirect()->route('store.department-requisitions.show', $requisition->id)
                ->with('success', 'Requisition approved. You can now issue items.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving requisition', [
                'requisition_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Error approving requisition: ' . $e->getMessage());
        }
    }

    /**
     * Reject a requisition.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            $requisition = DepartmentRequisition::findOrFail($id);

            if ($requisition->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requisitions can be rejected.');
            }

            $requisition->status = 'rejected';
            $requisition->rejection_reason = $request->rejection_reason;
            $requisition->approved_by = Auth::id();
            $requisition->approved_at = now();
            $requisition->save();

            DB::commit();

            Log::info('Department requisition rejected', [
                'user_id' => Auth::id(),
                'requisition_id' => $requisition->id,
                'reason' => $request->rejection_reason
            ]);

            return redirect()->route('store.department-requisitions.index')
                ->with('success', 'Requisition rejected.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting requisition', [
                'requisition_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Error rejecting requisition: ' . $e->getMessage());
        }
    }

    /**
     * Show form to issue items.
     */
    public function issueForm($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = DepartmentRequisition::with(['items.inventoryItem', 'department'])
            ->findOrFail($id);

        if (!in_array($requisition->status, ['approved', 'partially_issued'])) {
            return redirect()->route('store.department-requisitions.show', $requisition->id)
                ->with('error', 'Only approved requisitions can be issued.');
        }

        return view('store.department_requisitions.issue', compact('requisition'));
    }

    /**
     * Process issuing items.
     */
public function issue(Request $request, $id)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'STORE') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $request->validate([
        'items'                       => 'required|array',
        'items.*.item_id'             => 'required|exists:department_requisition_items,id',
        'items.*.quantity_issued'     => 'required|numeric|min:0',
        'items.*.pack_type'           => 'nullable|string',
        'items.*.pack_size'           => 'nullable|numeric|min:1',
        'taken_by'                    => 'required|string|max:255',  // NEW VALIDATION
        'store_notes'                 => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $requisition = DepartmentRequisition::findOrFail($id);
        $allFullyIssued = true;
        $anyIssued = false;

        foreach ($request->items as $itemData) {
            $reqItem        = DepartmentRequisitionItem::findOrFail($itemData['item_id']);
            $quantityIssued = $itemData['quantity_issued'];
            $packType       = $itemData['pack_type'] ?? null;
            $packSize       = $itemData['pack_size'] ?? null;

            // Calculate total pieces issued
            $totalPiecesIssued = $packType && $packSize
                ? $quantityIssued * $packSize
                : $quantityIssued;

            // Update requisition item
            $reqItem->quantity_issued      = $quantityIssued;
            $reqItem->issued_pack_type     = $packType;
            $reqItem->issued_pack_size     = $packSize;
            $reqItem->issued_total_pieces  = $totalPiecesIssued;
            $reqItem->save();

            // Decrease inventory
            $inventoryItem = InventoryItem::find($reqItem->inventory_item_id);
            $previousStock = $inventoryItem->current_stock ?? 0;
            $inventoryItem->current_stock = $previousStock - $totalPiecesIssued;
            $inventoryItem->save();

            // Record stock movement
            $movementNumber = 'ISS-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            StockMovement::create([
                'movement_number'      => $movementNumber,
                'inventory_item_id'    => $inventoryItem->id,
                'store_id'             => 1,
                'movement_type_id'     => 4,
                'quantity'             => $quantityIssued,
                'pack_type'            => $packType,
                'pack_size'            => $packSize,
                'number_of_packs'      => $quantityIssued,
                'base_unit'            => $reqItem->metrics ?? 'units',
                'quantity_in_base_unit'=> $totalPiecesIssued,
                'unit_cost'            => $inventoryItem->unit_cost,
                'total_value'          => $totalPiecesIssued * ($inventoryItem->unit_cost ?? 0),
                'reason'               => 'Issued to ' . $requisition->department->name . ' - Req: ' . $requisition->requisition_number,
                'movement_date'        => now(),
                'approved_at'          => now(),
                'approved_by'          => Auth::id(),
                'created_by'           => Auth::id(),
                'taken_by'             => $request->taken_by,  // NEW FIELD
            ]);

            if ($quantityIssued > 0) {
                $anyIssued = true;
            }

            if ($reqItem->quantity_issued < $reqItem->quantity_requested) {
                $allFullyIssued = false;
            }
        }

        // Update requisition status
        if ($allFullyIssued && $anyIssued) {
            $requisition->status = 'issued';
        } elseif ($anyIssued) {
            $requisition->status = 'partially_issued';
        }

        if ($request->filled('store_notes')) {
            $requisition->store_notes = $request->store_notes;
        }

        $requisition->save();

        DB::commit();

        Log::info('Items issued to department', [
            'user_id'          => Auth::id(),
            'requisition_id'   => $requisition->id,
            'requisition_number' => $requisition->requisition_number,
            'taken_by'         => $request->taken_by,
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

    /**
 * Show form to process returns from department.
 */
public function returnForm($id)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'STORE') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $requisition = DepartmentRequisition::with(['items.inventoryItem', 'department'])
        ->whereIn('status', ['issued', 'partially_returned'])
        ->findOrFail($id);

    // The issued_pack_type and issued_pack_size are already in the items
    // No need to reassign them - they are accessible directly from $item->issued_pack_type

    // For debugging - you can remove this after testing
    foreach ($requisition->items as $item) {
        \Illuminate\Support\Facades\Log::info('Return form item data', [
            'item_id' => $item->id,
            'item_name' => $item->inventoryItem->name ?? 'N/A',
            'issued_pack_type' => $item->issued_pack_type,
            'issued_pack_size' => $item->issued_pack_size,
            'quantity_issued' => $item->quantity_issued,
            'quantity_returned' => $item->quantity_returned,
            'issued_total_pieces' => $item->issued_total_pieces,
        ]);
    }

    return view('store.department_requisitions.return', compact('requisition'));
}


/**
 * Process returns from department (Stock IN).
 */
/**
 * Process returns from department (Stock IN).
 */

/**
 * Process returns from department (Stock IN).
 * Supports returning in packs, individual pieces, or a combination of both.
 */
public function processReturn(Request $request, $id)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'STORE') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $request->validate([
        'items' => 'required|array',
        'items.*.item_id' => 'required|exists:department_requisition_items,id',
        'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
        'items.*.pack_size' => 'nullable|numeric|min:1',
        'items.*.number_of_packs' => 'nullable|numeric|min:0',
        'items.*.quantity_returned' => 'nullable|numeric|min:0',
        'items.*.total_pieces' => 'nullable|numeric|min:0',
        'items.*.return_reason' => 'nullable|string',
        'global_return_reason' => 'nullable|string',
        'returned_by' => 'required|string|max:255',
        'store_notes' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $requisition = DepartmentRequisition::findOrFail($id);
        $anyReturned = false;
        $globalReason = $request->global_return_reason;

        foreach ($request->items as $itemData) {
            // Get the requisition item
            $reqItem = DepartmentRequisitionItem::findOrFail($itemData['item_id']);
            $inventoryItem = InventoryItem::find($itemData['inventory_item_id']);

            // Get pack information (from when it was issued)
            $packSize = $reqItem->issued_pack_size ?? 1;
            $packType = $reqItem->issued_pack_type ?? 'unit';

            // Calculate total pieces being returned
            // Option 1: Return in packs (number_of_packs × pack_size)
            $packsReturned = isset($itemData['number_of_packs']) ? (float) $itemData['number_of_packs'] : 0;
            $packReturnPieces = $packsReturned * $packSize;

            // Option 2: Return individual pieces (quantity_returned)
            $pieceReturnPieces = isset($itemData['quantity_returned']) ? (float) $itemData['quantity_returned'] : 0;

            // Total pieces returned = packs + individual pieces
            $totalPiecesReturned = $packReturnPieces + $pieceReturnPieces;

            // Skip if nothing is being returned
            if ($totalPiecesReturned <= 0) {
                continue;
            }

            // Calculate available to return (issued - already returned)
            $alreadyReturnedPieces = $reqItem->returned_total_pieces ?? 0;
            $availableToReturn = $reqItem->issued_total_pieces - $alreadyReturnedPieces;

            // Validate not exceeding available
            if ($totalPiecesReturned > $availableToReturn) {
                throw new \Exception(
                    "Cannot return more than available. Item: {$reqItem->inventoryItem->name}, " .
                    "Available: {$availableToReturn} pieces, Attempting to return: {$totalPiecesReturned} pieces"
                );
            }

            // Update requisition item with return information
            // Store the total pieces returned (can be from packs + pieces)
            $newTotalReturnedPieces = $alreadyReturnedPieces + $totalPiecesReturned;
            $reqItem->returned_total_pieces = $newTotalReturnedPieces;

            // Also store the pack return separately for tracking
            if ($packsReturned > 0) {
                $reqItem->quantity_returned = ($reqItem->quantity_returned ?? 0) + $packsReturned;
                $reqItem->returned_pack_type = $packType;
                $reqItem->returned_pack_size = $packSize;
            }

            // Store return reason (use item-specific or global)
            $reqItem->return_reason = $itemData['return_reason'] ?? $globalReason;
            $reqItem->returned_at = now();

            // Calculate consumed = issued_total_pieces - returned_total_pieces
            $reqItem->quantity_consumed = $reqItem->issued_total_pieces - $newTotalReturnedPieces;
            $reqItem->save();

            // Create STOCK IN movement (add back to inventory)
            $previousStock = $inventoryItem->current_stock ?? 0;
            $newStock = $previousStock + $totalPiecesReturned;
            $inventoryItem->current_stock = $newStock;
            $inventoryItem->save();

            // Record stock movement
            $movementNumber = 'RET-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // Build reason message with return details
            $returnDetails = [];
            if ($packsReturned > 0) {
                $returnDetails[] = "{$packsReturned} {$packType}(s) ({$packReturnPieces} pieces)";
            }
            if ($pieceReturnPieces > 0) {
                $returnDetails[] = "{$pieceReturnPieces} individual pieces";
            }
            $returnDetailsText = implode(' + ', $returnDetails);

            StockMovement::create([
                'movement_number' => $movementNumber,
                'inventory_item_id' => $inventoryItem->id,
                'store_id' => 1,
                'movement_type_id' => 2, // STOCK IN (+)
                'quantity' => $totalPiecesReturned,
                'pack_type' => $packsReturned > 0 ? $packType : null,
                'pack_size' => $packSize,
                'number_of_packs' => $packsReturned > 0 ? $packsReturned : null,
                'base_unit' => $inventoryItem->base_unit ?? 'units',
                'quantity_in_base_unit' => $totalPiecesReturned,
                'unit_cost' => $inventoryItem->unit_cost,
                'total_value' => $totalPiecesReturned * ($inventoryItem->unit_cost ?? 0),
                'reason' => 'RETURN from ' . $requisition->department->name .
                           ' - Req: ' . $requisition->requisition_number .
                           ' - Returned: ' . $returnDetailsText .
                           ($globalReason ? ' - ' . $globalReason : ''),
                'movement_date' => now(),
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),
                'returned_by' => $request->returned_by,
                'department_requisition_id' => $requisition->id,
            ]);

            Log::info('Items returned', [
                'user_id' => Auth::id(),
                'requisition_id' => $requisition->id,
                'item_id' => $reqItem->id,
                'item_name' => $inventoryItem->name,
                'packs_returned' => $packsReturned,
                'pack_type' => $packType,
                'pack_size' => $packSize,
                'pack_return_pieces' => $packReturnPieces,
                'piece_return_pieces' => $pieceReturnPieces,
                'total_pieces_returned' => $totalPiecesReturned,
                'returned_by' => $request->returned_by,
            ]);

            $anyReturned = true;
        }

        // Update requisition status
        if ($anyReturned) {
            // Check if all items are fully returned
            $allItemsFullyReturned = $requisition->items->every(function($item) {
                return ($item->returned_total_pieces ?? 0) >= ($item->issued_total_pieces ?? 0);
            });

            if ($allItemsFullyReturned) {
                $requisition->status = 'returned';
            } else {
                $requisition->status = 'partially_returned';
            }
        }

        // Save returned_by to requisition
        $requisition->returned_by = $request->returned_by;

        // Append store notes
        if ($request->filled('store_notes')) {
            $existingNotes = $requisition->store_notes;
            $newNote = "Return Notes: " . $request->store_notes;
            $requisition->store_notes = $existingNotes
                ? $existingNotes . "\n\n" . $newNote
                : $newNote;
        }
        $requisition->save();

        DB::commit();

        // Build success message
        $message = 'Items returned successfully by: ' . $request->returned_by . '. Stock added back to inventory.';

        return redirect()->route('store.department-requisitions.show', $requisition->id)
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error processing return', [
            'requisition_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all(),
        ]);
        return redirect()->back()
            ->with('error', 'Error processing return: ' . $e->getMessage())
            ->withInput();
    }
}

}
