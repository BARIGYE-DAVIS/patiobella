<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\StockMovementType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockMovementController extends Controller
{
    /**
     * Display a listing of stock movements.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Debug log to see what's happening
        Log::info('StockMovement index accessed', [
            'user_id' => $user->id,
            'user_department' => $user->department ? $user->department->name : 'no department',
            'department_check' => ($user->department && $user->department->name === 'STORE')
        ]);

        // Fix: Check if department exists and matches STORE (case-sensitive)
        if (!$user->department || $user->department->name !== 'STORE') {
            Log::warning('Unauthorized access to stock movements', [
                'user_id' => $user->id,
                'department' => $user->department->name ?? 'none',
            ]);
            return redirect()->route('store.dashboard')->with('error', 'Unauthorized access to stock movements');
        }

        try {
            $query = StockMovement::with(['inventoryItem', 'movementType', 'approvedBy']);

            // Filter by item
            if ($request->filled('item_id')) {
                $query->where('inventory_item_id', $request->item_id);
            }

            // Filter by movement type
            if ($request->filled('movement_type_id')) {
                $query->where('movement_type_id', $request->movement_type_id);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('movement_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('movement_date', '<=', $request->date_to);
            }

            $movements = $query->orderBy('created_at', 'desc')->paginate(20);

            // Get data for filters
            $items = InventoryItem::where('is_active', true)->orderBy('name')->get();
            $movementTypes = StockMovementType::where('is_active', true)->orderBy('name')->get();

            return view('store.stock_movements.index', compact('movements', 'items', 'movementTypes'));

        } catch (\Exception $e) {
            Log::error('Failed to load stock movements', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('store.dashboard')->with('error', 'Failed to load stock movements: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified stock movement.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            Log::warning('Unauthorized access to stock movement detail', [
                'user_id' => $user->id,
                'movement_id' => $id,
                'department' => $user->department->name ?? 'none',
            ]);
            return redirect()->route('store.dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $movement = StockMovement::with(['inventoryItem', 'inventoryItem.category', 'movementType', 'approvedBy'])
                ->findOrFail($id);

            return view('store.stock_movements.show', compact('movement'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Stock movement not found', [
                'user_id' => Auth::id(),
                'movement_id' => $id,
            ]);
            return redirect()->route('store.stock-movements.index')->with('error', 'Stock movement not found.');

        } catch (\Exception $e) {
            Log::error('Failed to load stock movement detail', [
                'user_id' => Auth::id(),
                'movement_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('store.stock-movements.index')->with('error', 'Failed to load stock movement details.');
        }
    }

    /**
     * Show form to create manual stock adjustment.
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('store.dashboard')->with('error', 'Unauthorized access');
        }

        $items = InventoryItem::where('is_active', true)->orderBy('name')->get();

        return view('store.stock_movements.create', compact('items'));
    }

    /**
     * Store manual stock adjustment.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('store.dashboard')->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'adjustment_type' => 'required|in:add,subtract',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $item = InventoryItem::findOrFail($validated['inventory_item_id']);
            $quantity = $validated['quantity'];

            if ($validated['adjustment_type'] === 'add') {
                $newStock = ($item->current_stock ?? 0) + $quantity;
                $movementTypeId = 2; // Manual Stock In (+)
            } else {
                if (($item->current_stock ?? 0) < $quantity) {
                    throw new \Exception('Insufficient stock to subtract. Current stock: ' . ($item->current_stock ?? 0));
                }
                $newStock = ($item->current_stock ?? 0) - $quantity;
                $movementTypeId = 3; // Manual Stock Out (-)
            }

            // Update inventory current stock
            $item->current_stock = $newStock;
            $item->save();

            // Create stock movement record
            $movementNumber = 'STK-ADJ-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'inventory_item_id' => $item->id,
                'store_id' => 1,
                'movement_type_id' => $movementTypeId,
                'quantity' => $quantity,
                'unit_id' => null,
                'quantity_in_base_unit' => $quantity,
                'unit_cost' => $item->unit_cost ?? 0,
                'total_value' => $quantity * ($item->unit_cost ?? 0),
                'reason' => $validated['reason'],
                'movement_date' => now(),
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Stock adjustment created', [
                'user_id' => Auth::id(),
                'movement_id' => $movement->id,
                'item_id' => $item->id,
                'adjustment_type' => $validated['adjustment_type'],
                'quantity' => $quantity,
                'new_stock' => $newStock,
            ]);

            return redirect()->route('store.stock-movements.show', $movement->id)
                ->with('success', 'Stock adjustment completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create stock adjustment', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except('_token'),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to adjust stock: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get stock movements for an item via AJAX.
     */
    public function getItemMovements($itemId)
    {
        try {
            $movements = StockMovement::with(['movementType'])
                ->where('inventory_item_id', $itemId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'movements' => $movements->map(function ($movement) {
                    return [
                        'id' => $movement->id,
                        'movement_number' => $movement->movement_number,
                        'type' => $movement->movementType->name ?? 'N/A',
                        'sign' => $movement->movementType->sign ?? '',
                        'quantity' => $movement->quantity,
                        'quantity_in_base_unit' => $movement->quantity_in_base_unit,
                        'unit_cost' => $movement->unit_cost,
                        'total_value' => $movement->total_value,
                        'reason' => $movement->reason,
                        'date' => $movement->movement_date->format('Y-m-d'),
                        'pack_type' => $movement->pack_type,
                        'pack_size' => $movement->pack_size,
                        'number_of_packs' => $movement->number_of_packs,
                    ];
                }),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch item movements', [
                'user_id' => Auth::id(),
                'item_id' => $itemId,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch movements',
            ], 500);
        }
    }
}
