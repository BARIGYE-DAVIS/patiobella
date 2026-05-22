<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\DepartmentRequisition;
use App\Models\DepartmentRequisitionItem;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RestaurantRequisitionController extends Controller
{
    /**
     * Display a listing of requisitions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = DepartmentRequisition::with(['department', 'items.inventoryItem'])
            ->where('department_id', $user->department_id);

        // Handle multiple status values (comma-separated)
        if ($request->filled('status')) {
            $statuses = explode(',', $request->status);
            $query->whereIn('status', $statuses);
        }

        // Filter by requisition type
        if ($request->filled('requisition_type')) {
            $query->where('requisition_type', $request->requisition_type);
        }

        if ($request->filled('search')) {
            $query->where('requisition_number', 'like', '%' . $request->search . '%');
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

        $statuses = ['pending', 'approved', 'partially_issued', 'issued', 'partially_consumed', 'fully_consumed', 'partially_returned', 'returned', 'completed', 'rejected', 'cancelled'];

        $requisitionTypes = DepartmentRequisition::getRequisitionTypes();

        return view('restaurant.requisitions.index', compact('requisitions', 'statuses', 'requisitionTypes'));
    }

    /**
     * Show the form for creating a new requisition.
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $items = InventoryItem::where('is_active', true)
            ->orderBy('name')
            ->get();

        $requisitionTypes = DepartmentRequisition::getRequisitionTypes();

        return view('restaurant.requisitions.create', compact('items', 'requisitionTypes'));
    }

    /**
     * API endpoint to get item details for auto-fill
     */
    public function getItemDetails($id)
    {
        try {
            $item = InventoryItem::findOrFail($id);

            $packType = null;
            $packSize = null;
            $baseUnit = $item->base_unit ?? 'pcs';
            $metrics = $item->base_unit ?? 'pcs';

            // Check if item has a default pack type
            if ($item->default_unit_of_measure_id) {
                $packTypeValue = strtolower($item->default_unit_of_measure_id);
                $packTypes = ['carton', 'box', 'crate', 'dozen', 'pack', 'bag', 'sack', 'bottle'];

                if (in_array($packTypeValue, $packTypes)) {
                    $packType = $packTypeValue;
                    $packSize = $item->pack_size ?? null;
                }
            }

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
     * Store a newly created requisition.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'requisition_type'             => 'required|in:daily,weekly,monthly',
            'date_needed'                  => 'nullable|date',
            'department_notes'             => 'nullable|string|max:500',
            'items'                        => 'required|array|min:1',
            'items.*.inventory_item_id'    => 'required|exists:inventory_items,id',
            'items.*.quantity'             => 'required|numeric|min:0.01',
            'items.*.pack_type'            => 'nullable|string',
            'items.*.pack_size'            => 'nullable|numeric|min:1',
            'items.*.metrics'              => 'nullable|string',
            'items.*.notes'                => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $requisitionNumber = 'REST-REQ-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $requisition = DepartmentRequisition::create([
                'requisition_number' => $requisitionNumber,
                'requisition_type'   => $request->requisition_type,
                'department_id'      => $user->department_id,
                'requested_by'       => Auth::id(),
                'date_needed'        => $request->date_needed,
                'status'             => 'pending',
                'department_notes'   => $request->department_notes,
            ]);

            foreach ($request->items as $itemData) {
                DepartmentRequisitionItem::create([
                    'department_requisition_id' => $requisition->id,
                    'inventory_item_id'         => $itemData['inventory_item_id'],
                    'quantity_requested'        => $itemData['quantity'],
                    'requested_pack_type'       => $itemData['pack_type'] ?? null,
                    'requested_pack_size'       => $itemData['pack_size'] ?? null,
                    'metrics'                   => $itemData['metrics'] ?? null,
                    'notes'                     => $itemData['notes'] ?? null,
                ]);
            }

            DB::commit();

            Log::info('Restaurant requisition created', [
                'user_id'              => Auth::id(),
                'requisition_id'       => $requisition->id,
                'requisition_number'   => $requisitionNumber,
                'requisition_type'     => $request->requisition_type,
            ]);

            return redirect()->route('restaurant.requisitions.show', $requisition->id)
                ->with('success', "Requisition {$requisitionNumber} created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create restaurant requisition', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
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

        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = DepartmentRequisition::with([
            'department',
            'requestedBy',
            'approvedBy',
            'items.inventoryItem',
        ])->findOrFail($id);

        if ($requisition->department_id != $user->department_id) {
            return redirect()->route('restaurant.requisitions.index')
                ->with('error', 'You do not have permission to view this requisition.');
        }

        return view('restaurant.requisitions.show', compact('requisition'));
    }

    /**
     * Show the consumption recording form.
     */
    public function consumeForm($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = DepartmentRequisition::with(['items.inventoryItem', 'department'])
            ->where('department_id', $user->department_id)
            ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned'])
            ->findOrFail($id);

        // Only show items that actually have something issued and still have remaining
        $consumableItems = $requisition->items->filter(function ($item) {
            $issued    = (float) ($item->issued_total_pieces   ?? 0);
            $consumed  = (float) ($item->quantity_consumed     ?? 0);
            $returned  = (float) ($item->returned_total_pieces ?? 0);
            $remaining = $issued - $consumed - $returned;
            return $issued > 0 && $remaining > 0;
        });

        if ($consumableItems->isEmpty()) {
            return redirect()->route('restaurant.requisitions.show', $id)
                ->with('error', 'No items available to record consumption for this requisition.');
        }

        return view('restaurant.requisitions.consume', compact('requisition', 'consumableItems'));
    }

    /**
     * Record consumption for a requisition.
     */
    public function recordConsumption(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'items'                     => 'required|array',
            'items.*.item_id'           => 'required|exists:department_requisition_items,id',
            'items.*.quantity_consumed' => 'required|numeric|min:0',
            'consumption_notes'         => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $requisition  = DepartmentRequisition::with('items')->findOrFail($id);
            $anyConsumed  = false;

            foreach ($request->items as $itemData) {
                $newConsumed = (float) $itemData['quantity_consumed'];

                if ($newConsumed <= 0) {
                    continue;
                }

                $reqItem  = DepartmentRequisitionItem::findOrFail($itemData['item_id']);

                // Calculate remaining available for consumption
                $issued    = (float) ($reqItem->issued_total_pieces   ?? 0);
                $consumed  = (float) ($reqItem->quantity_consumed     ?? 0);
                $returned  = (float) ($reqItem->returned_total_pieces ?? 0);
                $remaining = $issued - $consumed - $returned;

                // Cap at remaining so we never over-consume
                if ($newConsumed > $remaining) {
                    throw new \Exception(
                        'Cannot consume more than available for item: ' .
                        ($reqItem->inventoryItem->name ?? 'N/A') .
                        '. Available: ' . $remaining .
                        ', Attempting: ' . $newConsumed
                    );
                }

                // Accumulate consumption
                $reqItem->quantity_consumed   = $consumed + $newConsumed;
                $reqItem->last_consumed_at    = now();

                // Append consumption note to item notes
                $timestamp   = now()->format('Y-m-d H:i');
                $noteEntry   = "{$timestamp} - Restaurant consumed: {$newConsumed} " .
                               ($reqItem->metrics ?? ($reqItem->inventoryItem->base_unit ?? 'units'));
                $reqItem->notes = $reqItem->notes
                    ? $reqItem->notes . "\n" . $noteEntry
                    : $noteEntry;

                $reqItem->save();

                $anyConsumed = true;

                Log::info('Restaurant consumption recorded', [
                    'user_id'        => Auth::id(),
                    'requisition_id' => $requisition->id,
                    'item_id'        => $reqItem->id,
                    'item_name'      => $reqItem->inventoryItem->name ?? 'N/A',
                    'new_consumed'   => $newConsumed,
                    'total_consumed' => $reqItem->quantity_consumed,
                ]);
            }

            if ($anyConsumed) {
                // Refresh items from DB for accurate status calculation
                $requisition->load('items');

                $totalIssued    = (float) $requisition->items->sum('issued_total_pieces');
                $totalConsumed  = (float) $requisition->items->sum('quantity_consumed');
                $totalReturned  = (float) $requisition->items->sum('returned_total_pieces');
                $totalProcessed = $totalConsumed + $totalReturned;

                if ($totalIssued > 0 && $totalProcessed >= $totalIssued) {
                    $requisition->status = 'completed';
                } elseif ($totalConsumed > 0 && $totalConsumed < $totalIssued) {
                    $requisition->status = 'partially_consumed';
                }

                // Append consumption notes to requisition
                if ($request->filled('consumption_notes')) {
                    $existing = $requisition->department_notes;
                    $note     = now()->format('Y-m-d H:i') . ' - Consumption notes: ' . $request->consumption_notes;
                    $requisition->department_notes = $existing ? $existing . "\n" . $note : $note;
                }

                $requisition->save();
            }

            DB::commit();

            Log::info('Restaurant consumption saved', [
                'user_id'        => Auth::id(),
                'requisition_id' => $requisition->id,
                'status'         => $requisition->status,
            ]);

            return redirect()->route('restaurant.requisitions.show', $requisition->id)
                ->with('success', 'Consumption recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to record restaurant consumption', [
                'user_id'        => Auth::id(),
                'requisition_id' => $id,
                'error'          => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to record consumption: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cancel the specified requisition.
     */
    public function cancel($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $requisition = DepartmentRequisition::findOrFail($id);

            if ($requisition->department_id != $user->department_id) {
                return redirect()->route('restaurant.requisitions.index')
                    ->with('error', 'You do not have permission to cancel this requisition.');
            }

            if ($requisition->status !== 'pending') {
                return redirect()->back()
                    ->with('error', 'Only pending requisitions can be cancelled.');
            }

            $requisition->status = 'cancelled';
            $requisition->save();

            Log::info('Restaurant requisition cancelled', [
                'user_id'              => Auth::id(),
                'requisition_id'       => $requisition->id,
                'requisition_number'   => $requisition->requisition_number,
            ]);

            return redirect()->route('restaurant.requisitions.index')
                ->with('success', "Requisition {$requisition->requisition_number} cancelled successfully.");

        } catch (\Exception $e) {
            Log::error('Failed to cancel restaurant requisition', [
                'user_id'        => Auth::id(),
                'requisition_id' => $id,
                'error'          => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to cancel requisition: ' . $e->getMessage());
        }
    }
}
