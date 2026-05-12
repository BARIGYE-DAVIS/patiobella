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

        return view('kitchen.requisitions.create', compact('items'));
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
            'date_needed' => 'nullable|date',
            'department_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.pack_type' => 'nullable|string',
            'items.*.pack_size' => 'nullable|numeric|min:1',
            'items.*.metrics' => 'nullable|string',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Generate requisition number
            $requisitionNumber = 'KIT-REQ-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // Create requisition
            $requisition = DepartmentRequisition::create([
                'requisition_number' => $requisitionNumber,
                'department_id' => $user->department_id,
                'requested_by' => $user->id,
                'date_needed' => $validated['date_needed'],
                'department_notes' => $validated['department_notes'],
                'status' => 'pending',
            ]);

            // Create requisition items
            foreach ($validated['items'] as $item) {
                $packType = $item['pack_type'] ?? null;
                $packSize = $item['pack_size'] ?? null;
                $quantity = $item['quantity'];

                // Calculate total pieces if pack type exists
                $totalPieces = $packType && $packSize ? $quantity * $packSize : $quantity;

                DepartmentRequisitionItem::create([
                    'department_requisition_id' => $requisition->id,
                    'inventory_item_id' => $item['inventory_item_id'],
                    'quantity_requested' => $quantity,
                    'requested_pack_type' => $packType,
                    'requested_pack_size' => $packSize,
                    'issued_total_pieces' => $totalPieces,
                    'metrics' => $item['metrics'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            Log::info('Kitchen requisition created', [
                'user_id' => Auth::id(),
                'requisition_id' => $requisition->id,
                'requisition_number' => $requisitionNumber
            ]);

            return redirect()->route('kitchen.requisitions.show', $requisition->id)
                ->with('success', 'Requisition #' . $requisitionNumber . ' created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating kitchen requisition', [
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

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = DepartmentRequisition::with(['items.inventoryItem', 'requestedBy', 'approvedBy'])
            ->where('department_id', $user->department_id)
            ->where('requested_by', $user->id)
            ->findOrFail($id);

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
