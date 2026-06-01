<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Batch;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequisitionController extends Controller
{
    /**
     * Display a listing of requisitions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = Requisition::with(['items.batch', 'items.inventoryItem', 'requestedBy']);

        if ($request->filled('search')) {
            $query->where('requisition_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('requisition_type')) {
            $query->where('requisition_type', $request->requisition_type);
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('store.requisitions.index', compact('requisitions'));
    }

    /**
     * Show form to create a new requisition.
     * Loads BATCHES instead of inventory_items.
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // Get active batches with remaining stock > 0
        $batches = Batch::with(['inventoryItem.category'])
            ->where('remaining_quantity', '>', 0)
            ->where('batch_status', 'active')
            ->orderBy('expiry_date', 'asc')
            ->get();

        return view('store.requisitions.create', compact('batches'));
    }

    /**
     * Get batch details for AJAX request.
     */
    public function getBatchDetails($id)
    {
        try {
            $batch = Batch::with('inventoryItem.category')->findOrFail($id);

            $expiryStatus = 'ok';
            $daysLeft = null;
            if ($batch->expiry_date) {
                $daysLeft = now()->diffInDays($batch->expiry_date, false);
                if ($daysLeft <= 0) {
                    $expiryStatus = 'expired';
                } elseif ($daysLeft <= 30) {
                    $expiryStatus = 'expiring_soon';
                }
            }

            $stockStatus = 'ok';
            if ($batch->remaining_quantity <= 0) {
                $stockStatus = 'out_of_stock';
            } elseif ($batch->remaining_quantity < 10) {
                $stockStatus = 'low';
            }

            // Calculate total available stock for this item
            $totalStock = Batch::where('inventory_item_id', $batch->inventory_item_id)
                ->where('batch_status', 'active')
                ->where('remaining_quantity', '>', 0)
                ->sum('remaining_quantity');

            return response()->json([
                'success' => true,
                'batch' => [
                    'id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'remaining_quantity' => $batch->remaining_quantity,
                    'total_available_stock' => $totalStock,
                    'unit_of_measurement' => $batch->unit_of_measurement ?? 'piece',
                    'unit_cost' => $batch->unit_cost,
                    'expiry_date' => $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : null,
                    'expiry_status' => $expiryStatus,
                    'days_left' => $daysLeft,
                    'stock_status' => $stockStatus,
                    'pack_type' => $batch->pack_type,
                    'pack_size' => $batch->pack_size,
                    'item_name' => $batch->inventoryItem->name ?? 'Unknown',
                    'category_name' => $batch->inventoryItem->category->name ?? 'Uncategorized',
                    'item_code' => $batch->inventoryItem->item_code ?? null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found'
            ], 404);
        }
    }

    /**
     * Store a newly created requisition.
     */
    /**
 * Store a newly created requisition.
 */
public function store(Request $request)
{
    $user = Auth::user();

    if (!$user->department || $user->department->name !== 'STORE') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $validated = $request->validate([
        'requisition_type' => 'required|in:normal,emergency',
        'date_needed' => 'required|date|after_or_equal:today',
        'notes' => 'nullable|string',
        'items' => 'required|array|min:1',
        'items.*.batch_id' => 'required|exists:batches,id',
        'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.unit_cost' => 'required|numeric|min:0',
        'items.*.batch_stock' => 'nullable|numeric|min:0',
        'items.*.total_stock' => 'nullable|numeric|min:0',
        'items.*.metrics' => 'nullable|string|max:50',
        'items.*.notes' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        // Generate requisition number
        $requisitionNumber = $this->generateRequisitionNumber();

        // Create requisition
        $requisition = Requisition::create([
            'requisition_number' => $requisitionNumber,
            'requisition_type' => $validated['requisition_type'],
            'store_id' => $user->department_id,
            'requested_by' => Auth::id(),
            'date_needed' => $validated['date_needed'],
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);

        // Create requisition items
        foreach ($validated['items'] as $item) {
            $batch = Batch::with('inventoryItem.category')->find($item['batch_id']);

            if (!$batch) {
                throw new \Exception("Batch not found for ID: " . $item['batch_id']);
            }

            $categoryName = $batch->inventoryItem && $batch->inventoryItem->category
                ? $batch->inventoryItem->category->name
                : null;

            $itemNameSnapshot = $batch->inventoryItem->name ?? 'Unknown Item';

            RequisitionItem::create([
                'requisition_id' => $requisition->id,
                'batch_id' => $item['batch_id'],
                'inventory_item_id' => $item['inventory_item_id'],
                'item_name' => $itemNameSnapshot,
                'quantity_requested' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'batch_stock_at_request' => $item['batch_stock'] ?? $batch->remaining_quantity,
                'total_stock_at_request' => $item['total_stock'] ?? 0,
                'metrics' => $item['metrics'] ?? null,
                'category_name' => $categoryName,
                'quantity_approved' => 0,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        DB::commit();

        Log::info('Requisition created successfully', [
            'user_id' => Auth::id(),
            'requisition_id' => $requisition->id,
            'requisition_number' => $requisitionNumber,
            'requisition_type' => $validated['requisition_type'],
            'items_count' => count($validated['items'])
        ]);

        return redirect()->route('store.requisitions.show', $requisition->id)
            ->with('success', 'Requisition created successfully. Reference: ' . $requisitionNumber);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Error creating requisition: ' . $e->getMessage(), [
            'user_id' => Auth::id(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()
            ->with('error', 'Failed to create requisition: ' . $e->getMessage())
            ->withInput();
    }
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

        $requisition = Requisition::with([
            'items.batch.inventoryItem',
            'items.inventoryItem',
            'requestedBy',
            'approvedBy'
        ])->findOrFail($id);

        return view('store.requisitions.show', compact('requisition'));
    }

    /**
     * Show form to edit a requisition.
     */
    public function edit($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = Requisition::with('items.batch')->findOrFail($id);

        // Only allow editing if status is pending or rejected
        if ($requisition->status !== 'pending' && $requisition->status !== 'rejected') {
            return redirect()->route('store.requisitions.show', $requisition->id)
                ->with('error', 'Only pending or rejected requisitions can be edited.');
        }

        // Get available batches for selection
        $batches = Batch::with('inventoryItem.category')
            ->where('remaining_quantity', '>', 0)
            ->where('batch_status', 'active')
            ->orderBy('expiry_date', 'asc')
            ->get();

        return view('store.requisitions.edit', compact('requisition', 'batches'));
    }

    /**
     * Update the specified requisition.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = Requisition::findOrFail($id);

        // Only allow editing if status is pending or rejected
        if ($requisition->status !== 'pending' && $requisition->status !== 'rejected') {
            return redirect()->route('store.requisitions.show', $requisition->id)
                ->with('error', 'Only pending or rejected requisitions can be edited.');
        }

        $validated = $request->validate([
            'requisition_type' => 'required|in:normal,emergency',
            'date_needed' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.batch_id' => 'required|exists:batches,id',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.metrics' => 'nullable|string|max:50',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Update requisition header
            $requisition->update([
                'requisition_type' => $validated['requisition_type'],
                'date_needed' => $validated['date_needed'],
                'notes' => $validated['notes'],
            ]);

            // Reset status to pending if it was rejected
            if ($requisition->status === 'rejected') {
                $requisition->update([
                    'status' => 'pending',
                    'approved_by' => null,
                    'approved_at' => null,
                    'rejection_reason' => null,
                ]);
            }

            // Delete existing items
            $requisition->items()->delete();

            // Create new items
            foreach ($validated['items'] as $item) {
                $batch = Batch::with('inventoryItem.category')->find($item['batch_id']);

                if (!$batch) {
                    throw new \Exception("Batch not found for ID: " . $item['batch_id']);
                }

                $categoryName = $batch->inventoryItem && $batch->inventoryItem->category
                    ? $batch->inventoryItem->category->name
                    : null;

                $itemNameSnapshot = $batch->inventoryItem->name ?? 'Unknown Item';

                RequisitionItem::create([
                    'requisition_id' => $requisition->id,
                    'batch_id' => $item['batch_id'],
                    'inventory_item_id' => $item['inventory_item_id'],
                    'item_name' => $itemNameSnapshot,
                    'quantity_requested' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'metrics' => $item['metrics'] ?? null,
                    'category_name' => $categoryName,
                    'quantity_approved' => 0,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('store.requisitions.show', $requisition->id)
                ->with('success', 'Requisition updated successfully and resubmitted for approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating requisition: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update requisition: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cancel a requisition (only if pending).
     */
    public function cancel($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = Requisition::findOrFail($id);

        if ($requisition->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requisitions can be cancelled.');
        }

        $requisition->update(['status' => 'cancelled']);

        return redirect()->route('store.requisitions.index')
            ->with('success', 'Requisition cancelled successfully.');
    }

    /**
     * Delete a requisition (permanent delete).
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = Requisition::findOrFail($id);

        // Only allow deletion if status is pending or cancelled
        if (!in_array($requisition->status, ['pending', 'cancelled'])) {
            return redirect()->back()->with('error', 'Only pending or cancelled requisitions can be deleted.');
        }

        DB::beginTransaction();

        try {
            // Delete requisition items first
            $requisition->items()->delete();
            // Delete requisition
            $requisition->delete();

            DB::commit();

            Log::info('Requisition deleted', [
                'user_id' => Auth::id(),
                'requisition_id' => $requisition->id,
                'requisition_number' => $requisition->requisition_number
            ]);

            return redirect()->route('store.requisitions.index')
                ->with('success', 'Requisition deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting requisition: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete requisition: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique requisition number.
     */
    private function generateRequisitionNumber()
    {
        $prefix = 'REQ-' . date('Ymd');
        $lastRequisition = Requisition::where('requisition_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRequisition) {
            $lastNumber = intval(substr($lastRequisition->requisition_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Export requisition as PDF.
     */
    public function exportPdf($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = Requisition::with([
            'items.batch.inventoryItem',
            'items.inventoryItem',
            'requestedBy',
            'approvedBy'
        ])->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('store.requisitions.pdf', compact('requisition'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('requisition-' . $requisition->requisition_number . '.pdf');
    }
}
