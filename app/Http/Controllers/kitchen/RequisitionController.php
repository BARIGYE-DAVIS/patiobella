<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\DepartmentRequisition;
use App\Models\DepartmentRequisitionItem;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequisitionController extends Controller
{
    /**
     * Display a listing of kitchen requisitions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = DepartmentRequisition::with(['items', 'approvedBy'])
            ->where('department_id', $user->department_id)
            ->where('requested_by', $user->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by requisition type
        if ($request->filled('requisition_type')) {
            $query->where('requisition_type', $request->requisition_type);
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('kitchen.requisitions.index', compact('requisitions'));
    }

    /**
     * Show form to create a new requisition.
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $items = InventoryItem::where('is_active', true)->orderBy('name')->get();

        // Get requisition types for dropdown
        $requisitionTypes = DepartmentRequisition::getRequisitionTypes();

        return view('kitchen.requisitions.create', compact('items', 'requisitionTypes'));
    }

    /**
     * Store a newly created requisition.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'requisition_type'               => 'required|in:daily,weekly,monthly',
            'date_needed'                    => 'nullable|date',
            'department_notes'               => 'nullable|string',
            'items'                          => 'required|array|min:1',
            'items.*.inventory_item_id'      => 'required|exists:inventory_items,id',
            'items.*.quantity'               => 'required|numeric|min:0.01',
            'items.*.pack_type'              => 'nullable|string',
            'items.*.pack_size'              => 'nullable|numeric|min:1',
            'items.*.metrics'                => 'nullable|string',
            'items.*.notes'                  => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $requisitionNumber = 'KIT-REQ-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $requisition = DepartmentRequisition::create([
                'requisition_number' => $requisitionNumber,
                'requisition_type'   => $validated['requisition_type'],
                'department_id'      => $user->department_id,
                'requested_by'       => $user->id,
                'date_needed'        => $validated['date_needed'],
                'department_notes'   => $validated['department_notes'],
                'status'             => 'pending',
            ]);

            foreach ($validated['items'] as $item) {
                DepartmentRequisitionItem::create([
                    'department_requisition_id' => $requisition->id,
                    'inventory_item_id'         => $item['inventory_item_id'],
                    'quantity_requested'        => $item['quantity'],
                    'requested_pack_type'       => $item['pack_type'] ?? null,
                    'requested_pack_size'       => $item['pack_size'] ?? null,
                    'metrics'                   => $item['metrics'] ?? null,
                    'notes'                     => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            Log::info('Kitchen requisition created', [
                'user_id'              => Auth::id(),
                'requisition_id'       => $requisition->id,
                'requisition_number'   => $requisitionNumber,
                'requisition_type'     => $validated['requisition_type'],
            ]);

            return redirect()->route('kitchen.requisitions.show', $requisition->id)
                ->with('success', 'Requisition #' . $requisitionNumber . ' created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating kitchen requisition', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create requisition: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * API endpoint to get item details for auto-fill
     * This returns pack_type, pack_size, base_unit, etc. based on the selected item
     */
    public function getItemDetails($id)
    {
        try {
            $item = InventoryItem::findOrFail($id);

            // Determine pack type from the item's default_unit_of_measure_id or other fields
            // You can customize this logic based on your database structure
            $packType = null;
            $packSize = null;
            $baseUnit = $item->base_unit ?? 'pcs';
            $metrics = $item->base_unit ?? 'pcs';

            // Check if item has a default pack type stored
            // For example, if default_unit_of_measure_id is 'carton', 'box', 'crate', etc.
            if ($item->default_unit_of_measure_id) {
                // You might have a mapping table or logic to determine pack type
                // For now, we'll check common values
                $packTypeValue = strtolower($item->default_unit_of_measure_id);
                $packTypes = ['carton', 'box', 'crate', 'dozen', 'pack', 'bag', 'sack', 'bottle'];

                if (in_array($packTypeValue, $packTypes)) {
                    $packType = $packTypeValue;
                    // You might store pack_size in another column or calculate it
                    $packSize = $item->pack_size ?? null;
                }
            }

            // Determine if this item is packable (has pack type) or direct unit
            $isPackable = !is_null($packType) && $packSize > 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'item_code' => $item->item_code,
                    'base_unit' => $baseUnit,
                    'metrics' => $metrics,
                    'pack_type' => $packType,
                    'pack_size' => $packSize,
                    'is_packable' => $isPackable,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }
    }

    /**
     * Display the specified requisition.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = DepartmentRequisition::with([
            'items.inventoryItem',
            'requestedBy',
            'approvedBy'
        ])
        ->where('department_id', $user->department_id)
        ->where('requested_by', $user->id)
        ->findOrFail($id);

        // Debug: Log the returned quantities to verify they exist
        foreach ($requisition->items as $item) {
            Log::info('Kitchen requisition item data', [
                'item_id' => $item->id,
                'item_name' => $item->inventoryItem->name ?? 'N/A',
                'quantity_issued' => $item->quantity_issued,
                'quantity_consumed' => $item->quantity_consumed,
                'quantity_returned' => $item->quantity_returned,
                'returned_total_pieces' => $item->returned_total_pieces,
            ]);
        }

        return view('kitchen.requisitions.show', compact('requisition'));
    }

    /**
     * Cancel a requisition (only if pending).
     */
    public function cancel($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        DB::beginTransaction();

        try {
            $requisition = DepartmentRequisition::findOrFail($id);

            if ($requisition->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requisitions can be cancelled.');
            }

            $requisition->status = 'cancelled';
            $requisition->save();

            DB::commit();

            Log::info('Kitchen requisition cancelled', [
                'user_id' => Auth::id(),
                'requisition_id' => $requisition->id,
                'requisition_number' => $requisition->requisition_number
            ]);

            return redirect()->route('kitchen.requisitions.index')
                ->with('success', 'Requisition cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling kitchen requisition', [
                'user_id' => Auth::id(),
                'requisition_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Error cancelling requisition: ' . $e->getMessage());
        }
    }
}
