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
     * Store a newly created requisition.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'date_needed' => 'nullable|date',
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

    // Debug: Log the incoming request
    Log::info('Update Requisition Request - Full Data', $request->all());

    $validated = $request->validate([
        'date_needed' => 'nullable|date',
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
            // Get the inventory item to fetch its category
            $inventoryItem = \App\Models\InventoryItem::with('category')->find($item['item_id']);
            $categoryName = $inventoryItem && $inventoryItem->category ? $inventoryItem->category->name : null;

            // Make sure quantity is set
            $quantity = isset($item['quantity']) ? $item['quantity'] : 0;

            Log::info('Creating requisition item', [
                'requisition_id' => $requisition->id,
                'inventory_item_id' => $item['item_id'],
                'quantity_requested' => $quantity,
                'metrics' => $item['metrics'] ?? null,
                'category_name' => $categoryName,
                'notes' => $item['notes'] ?? null
            ]);

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

        Log::info('Requisition updated successfully', [
            'user_id' => Auth::id(),
            'requisition_id' => $requisition->id,
            'requisition_number' => $requisition->requisition_number
        ]);

        return redirect()->route('store.requisitions.show', $requisition->id)
            ->with('success', 'Requisition updated successfully and resubmitted for approval.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating requisition: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);
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

        Log::info('Requisition cancelled', [
            'user_id' => Auth::id(),
            'requisition_id' => $requisition->id,
            'requisition_number' => $requisition->requisition_number
        ]);

        return redirect()->route('store.requisitions.index')
            ->with('success', 'Requisition cancelled successfully.');
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
}
