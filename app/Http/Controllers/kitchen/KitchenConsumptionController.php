<?php
// app/Http/Controllers/Kitchen/KitchenConsumptionController.php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\DepartmentRequisition;
use App\Models\DepartmentRequisitionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KitchenConsumptionController extends Controller
{
    /**
     * Display list of requisitions with remaining stock.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // Get all requisitions that have issued items and not fully consumed
        $requisitions = DepartmentRequisition::with(['items.inventoryItem'])
            ->where('department_id', $user->department_id)
            ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate remaining quantities for each requisition item
        foreach ($requisitions as $requisition) {
            foreach ($requisition->items as $item) {
                $item->total_consumed = ($item->quantity_consumed ?? 0) + ($item->quantity_sold ?? 0);
                $item->remaining_pieces = ($item->issued_total_pieces ?? 0) - $item->total_consumed;

                // Calculate remaining in packs and pieces
                if ($item->issued_pack_type && $item->issued_pack_size > 0) {
                    $packSize = $item->issued_pack_size;
                    $remainingPieces = $item->remaining_pieces;
                    $item->remaining_packs = floor($remainingPieces / $packSize);
                    $item->remaining_pieces_extra = $remainingPieces % $packSize;
                } else {
                    $item->remaining_packs = 0;
                    $item->remaining_pieces_extra = $item->remaining_pieces;
                }
            }
        }

        return view('kitchen.consumption.index', compact('requisitions'));
    }

    /**
     * Show consumption form for a specific requisition.
     */
    public function create($requisitionId)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = DepartmentRequisition::with(['items.inventoryItem', 'department'])
            ->where('department_id', $user->department_id)
            ->findOrFail($requisitionId);

        // Calculate remaining quantities
        foreach ($requisition->items as $item) {
            $item->total_consumed = ($item->quantity_consumed ?? 0) + ($item->quantity_sold ?? 0);
            $item->remaining_pieces = ($item->issued_total_pieces ?? 0) - $item->total_consumed;

            // Calculate remaining in packs and pieces
            if ($item->issued_pack_type && $item->issued_pack_size > 0) {
                $packSize = $item->issued_pack_size;
                $remainingPieces = $item->remaining_pieces;
                $item->remaining_packs = floor($remainingPieces / $packSize);
                $item->remaining_pieces_extra = $remainingPieces % $packSize;
                $item->pack_size = $packSize;
                $item->pack_type = $item->issued_pack_type;
            } else {
                $item->remaining_packs = 0;
                $item->remaining_pieces_extra = $item->remaining_pieces;
                $item->pack_size = 1;
                $item->pack_type = null;
            }
        }

        return view('kitchen.consumption.create', compact('requisition'));
    }

    /**
     * Store consumption records (NO stock movement).
     */
    public function store(Request $request, $requisitionId)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:department_requisition_items,id',
            'items.*.packs_consumed' => 'nullable|numeric|min:0',
            'items.*.pieces_consumed' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $requisition = DepartmentRequisition::findOrFail($requisitionId);
            $totalPiecesConsumed = 0;
            $anyConsumed = false;

            foreach ($request->items as $itemData) {
                $reqItem = DepartmentRequisitionItem::findOrFail($itemData['item_id']);

                $packsConsumed = isset($itemData['packs_consumed']) ? (float) $itemData['packs_consumed'] : 0;
                $piecesConsumed = isset($itemData['pieces_consumed']) ? (float) $itemData['pieces_consumed'] : 0;

                // Get pack size from issued data
                $packSize = $reqItem->issued_pack_size ?? 1;

                // Calculate total pieces consumed
                $totalPiecesToConsume = ($packsConsumed * $packSize) + $piecesConsumed;

                if ($totalPiecesToConsume <= 0) {
                    continue;
                }

                // Calculate current remaining
                $totalConsumedSoFar = ($reqItem->quantity_consumed ?? 0) + ($reqItem->quantity_sold ?? 0);
                $remainingPieces = ($reqItem->issued_total_pieces ?? 0) - $totalConsumedSoFar;

                // Validate
                if ($totalPiecesToConsume > $remainingPieces) {
                    throw new \Exception(
                        "Cannot consume more than available. Item: {$reqItem->inventoryItem->name}, " .
                        "Available: {$remainingPieces} pieces, Requested: {$totalPiecesToConsume} pieces"
                    );
                }

                // Update quantity_consumed (increment)
                $newConsumed = ($reqItem->quantity_consumed ?? 0) + $totalPiecesToConsume;
                $reqItem->quantity_consumed = $newConsumed;

                // Store consumption details in notes (optional - track what was consumed)
                $consumptionDetail = [];
                if ($packsConsumed > 0) {
                    $consumptionDetail[] = "{$packsConsumed} {$reqItem->issued_pack_type}(s)";
                }
                if ($piecesConsumed > 0) {
                    $consumptionDetail[] = "{$piecesConsumed} pieces";
                }
                $consumptionText = implode(' + ', $consumptionDetail);

                $existingNotes = $reqItem->notes;
                $newNote = date('Y-m-d H:i') . " - Kitchen consumed: {$consumptionText} ({$totalPiecesToConsume} pieces)";
                $reqItem->notes = $existingNotes ? $existingNotes . "\n" . $newNote : $newNote;

                $reqItem->save();

                $totalPiecesConsumed += $totalPiecesToConsume;
                $anyConsumed = true;

                Log::info('Kitchen consumption recorded', [
                    'user_id' => Auth::id(),
                    'requisition_id' => $requisitionId,
                    'item_id' => $reqItem->id,
                    'item_name' => $reqItem->inventoryItem->name,
                    'packs_consumed' => $packsConsumed,
                    'pieces_consumed' => $piecesConsumed,
                    'total_pieces' => $totalPiecesToConsume,
                    'pack_type' => $reqItem->issued_pack_type,
                    'pack_size' => $packSize,
                ]);
            }

            // Update requisition status based on consumption
            if ($anyConsumed) {
                $totalIssuedPieces = $requisition->items->sum('issued_total_pieces');
                $totalConsumedPieces = $requisition->items->sum(function($item) {
                    return ($item->quantity_consumed ?? 0) + ($item->quantity_sold ?? 0);
                });

                if ($totalConsumedPieces >= $totalIssuedPieces && $totalIssuedPieces > 0) {
                    $requisition->status = 'fully_consumed';
                } elseif ($totalConsumedPieces > 0) {
                    $requisition->status = 'partially_consumed';
                }
                $requisition->save();
            }

            DB::commit();

            $message = $totalPiecesConsumed > 0
                ? "Consumption recorded successfully. Total consumed: {$totalPiecesConsumed} pieces."
                : "No consumption recorded.";

            return redirect()->route('kitchen.consumption.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to record kitchen consumption', [
                'user_id' => Auth::id(),
                'requisition_id' => $requisitionId,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to record consumption: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * View consumption history.
     */
    public function history()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $consumptions = DepartmentRequisitionItem::with(['departmentRequisition', 'inventoryItem'])
            ->whereHas('departmentRequisition', function($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })
            ->where('quantity_consumed', '>', 0)
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('kitchen.consumption.history', compact('consumptions'));
    }
}
