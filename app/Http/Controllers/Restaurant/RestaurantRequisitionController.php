<?php
// app/Http/Controllers/Restaurant/RestaurantRequisitionController.php

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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by requisition number
        if ($request->filled('search')) {
            $query->where('requisition_number', 'like', '%' . $request->search . '%');
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

        $statuses = ['pending', 'approved', 'partially_issued', 'issued', 'partially_returned', 'returned', 'rejected', 'cancelled'];

        return view('restaurant.requisitions.index', compact('requisitions', 'statuses'));
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

        // Get all active inventory items
        $items = InventoryItem::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('restaurant.requisitions.create', compact('items'));
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
            'date_needed' => 'nullable|date',
            'department_notes' => 'nullable|string|max:500',
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
            $requisitionNumber = 'REST-REQ-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // Create requisition
            $requisition = DepartmentRequisition::create([
                'requisition_number' => $requisitionNumber,
                'department_id' => $user->department_id,
                'requested_by' => Auth::id(),
                'date_needed' => $request->date_needed,
                'status' => 'pending',
                'department_notes' => $request->department_notes,
            ]);

            // Create requisition items
            foreach ($request->items as $itemData) {
                $quantity = $itemData['quantity'];
                $packType = $itemData['pack_type'] ?? null;
                $packSize = $itemData['pack_size'] ?? null;

                // Calculate total pieces if pack is used
                $totalPieces = ($packType && $packSize) ? $quantity * $packSize : $quantity;

                DepartmentRequisitionItem::create([
                    'department_requisition_id' => $requisition->id,
                    'inventory_item_id' => $itemData['inventory_item_id'],
                    'quantity_requested' => $quantity,
                    'requested_pack_type' => $packType,
                    'requested_pack_size' => $packSize,
                    'metrics' => $itemData['metrics'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            DB::commit();

            Log::info('Restaurant requisition created', [
                'user_id' => Auth::id(),
                'requisition_id' => $requisition->id,
                'requisition_number' => $requisitionNumber,
            ]);

            return redirect()->route('restaurant.requisitions.show', $requisition->id)
                ->with('success', "Requisition {$requisitionNumber} created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create restaurant requisition', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
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
            'items.inventoryItem'
        ])->findOrFail($id);

        // Ensure the requisition belongs to the restaurant department
        if ($requisition->department_id != $user->department_id) {
            return redirect()->route('restaurant.requisitions.index')
                ->with('error', 'You do not have permission to view this requisition.');
        }

        return view('restaurant.requisitions.show', compact('requisition'));
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
                'user_id' => Auth::id(),
                'requisition_id' => $requisition->id,
                'requisition_number' => $requisition->requisition_number,
            ]);

            return redirect()->route('restaurant.requisitions.index')
                ->with('success', "Requisition {$requisition->requisition_number} cancelled successfully.");

        } catch (\Exception $e) {
            Log::error('Failed to cancel restaurant requisition', [
                'user_id' => Auth::id(),
                'requisition_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to cancel requisition: ' . $e->getMessage());
        }
    }
}
