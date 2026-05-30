<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use App\Models\RequisitionItem;
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

        $query = Requisition::with(['items', 'requestedBy']);

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
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $items = InventoryItem::where('is_active', true)->orderBy('name')->get();

        return view('store.requisitions.create', compact('items'));
    }

    /**
     * Get item stock for AJAX request.
     */
    public function getItemStock($id)
    {
        try {
            $item = InventoryItem::findOrFail($id);
            return response()->json([
                'success' => true,
                'current_stock' => $item->current_stock,
                'base_unit' => $item->base_unit ?? 'pcs',
                'item_name' => $item->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }
    }

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
            'items.*.item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
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
                // Get the inventory item to fetch its category
                $inventoryItem = InventoryItem::with('category')->find($item['item_id']);
                $categoryName = $inventoryItem && $inventoryItem->category ? $inventoryItem->category->name : null;

                RequisitionItem::create([
                    'requisition_id' => $requisition->id,
                    'inventory_item_id' => $item['item_id'],
                    'item_name' => null,
                    'quantity_requested' => $item['quantity'],
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

        $requisition = Requisition::with(['items.inventoryItem', 'requestedBy', 'approvedBy'])->findOrFail($id);

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

        $requisition = Requisition::with('items')->findOrFail($id);

        // Only allow editing if status is pending or rejected
        if ($requisition->status !== 'pending' && $requisition->status !== 'rejected') {
            return redirect()->route('store.requisitions.show', $requisition->id)
                ->with('error', 'Only pending or rejected requisitions can be edited.');
        }

        $items = InventoryItem::where('is_active', true)->orderBy('name')->get();

        return view('store.requisitions.edit', compact('requisition', 'items'));
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
            'items.*.item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
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
            foreach ($validated['items'] as $index => $item) {
                $inventoryItem = \App\Models\InventoryItem::with('category')->find($item['item_id']);
                $categoryName = $inventoryItem && $inventoryItem->category ? $inventoryItem->category->name : null;

                $quantity = isset($item['quantity']) ? $item['quantity'] : 0;

                RequisitionItem::create([
                    'requisition_id' => $requisition->id,
                    'inventory_item_id' => $item['item_id'],
                    'item_name' => null,
                    'quantity_requested' => $quantity,
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

        $requisition = Requisition::with(['items.inventoryItem', 'requestedBy', 'approvedBy'])->findOrFail($id);

        // Load PDF view
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('store.requisitions.pdf', compact('requisition'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('requisition-' . $requisition->requisition_number . '.pdf');
    }
}
