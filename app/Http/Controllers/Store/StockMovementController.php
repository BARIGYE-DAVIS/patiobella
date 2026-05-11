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
use Barryvdh\DomPDF\Facade\Pdf;

class StockMovementController extends Controller
{
    /**
     * Display a listing of stock movements.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('store.dashboard')->with('error', 'Unauthorized access to stock movements');
        }

        try {
            $query = StockMovement::with(['inventoryItem', 'movementType', 'approvedBy']);

            // Live search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('movement_number', 'like', "%{$search}%")
                      ->orWhereHas('inventoryItem', function($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhere('reason', 'like', "%{$search}%");
                });
            }

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

            // Get ALL movements ordered chronologically (oldest first for correct running stock)
            $allMovements = $query->orderBy('movement_date', 'asc')
                                  ->orderBy('created_at', 'asc')
                                  ->get();

            // Calculate running stock PER ITEM
            $runningStocks = [];
            $movementsWithStock = [];

            foreach ($allMovements as $movement) {
                $itemId = $movement->inventory_item_id;
                $isIn = $movement->movementType && $movement->movementType->sign == '+';
                $quantity = $movement->quantity_in_base_unit;

                // Get current running stock for this item
                $currentStock = $runningStocks[$itemId] ?? 0;

                // Stock BEFORE this movement
                $stockBefore = $currentStock;

                // Update stock AFTER this movement
                if ($isIn) {
                    $currentStock += $quantity;
                } else {
                    $currentStock -= $quantity;
                }
                $stockAfter = $currentStock;

                // Store the running stock for this item
                $runningStocks[$itemId] = $currentStock;

                // Add to movement with stock info
                $movementsWithStock[] = (object)[
                    'movement' => $movement,
                    'stock_before' => max(0, $stockBefore),
                    'stock_after' => max(0, $stockAfter),
                ];
            }

            // Now paginate the movements with stock info
            $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
            $perPage = 20;
            $currentItems = array_slice($movementsWithStock, ($currentPage - 1) * $perPage, $perPage);

            $movements = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                count($movementsWithStock),
                $perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );

            // Get data for filters
            $items = InventoryItem::where('is_active', true)->orderBy('name')->get();
            $movementTypes = StockMovementType::where('is_active', true)->orderBy('name')->get();

            // Return JSON for AJAX requests
            if ($request->ajax() || $request->has('ajax')) {
                $html = view('store.stock_movements.partials.table_rows', compact('movements'))->render();
                $pagination = $movements->appends($request->query())->links()->toHtml();

                return response()->json([
                    'html' => $html,
                    'pagination' => $pagination,
                    'total' => $movements->total()
                ]);
            }

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
            return redirect()->route('store.dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $movement = StockMovement::with(['inventoryItem', 'inventoryItem.category', 'movementType', 'approvedBy'])
                ->findOrFail($id);

            // Get all movements for this item BEFORE this movement (chronologically)
            $previousMovements = StockMovement::where('inventory_item_id', $movement->inventory_item_id)
                ->where(function($q) use ($movement) {
                    $q->where('movement_date', '<', $movement->movement_date)
                      ->orWhere(function($sub) use ($movement) {
                          $sub->where('movement_date', '=', $movement->movement_date)
                              ->where('created_at', '<', $movement->created_at);
                      });
                })
                ->orderBy('movement_date', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            // Calculate stock BEFORE this movement
            $stockBefore = 0;
            foreach ($previousMovements as $prev) {
                $isIn = $prev->movementType && $prev->movementType->sign == '+';
                if ($isIn) {
                    $stockBefore += $prev->quantity_in_base_unit;
                } else {
                    $stockBefore -= $prev->quantity_in_base_unit;
                }
            }
            $stockBefore = max(0, $stockBefore);

            // Stock AFTER is stock before + this movement
            $isIn = $movement->movementType && $movement->movementType->sign == '+';
            $stockAfter = $stockBefore + ($isIn ? $movement->quantity_in_base_unit : -$movement->quantity_in_base_unit);
            $stockAfter = max(0, $stockAfter);

            return view('store.stock_movements.show', compact('movement', 'stockBefore', 'stockAfter'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('store.stock-movements.index')->with('error', 'Stock movement not found.');
        } catch (\Exception $e) {
            Log::error('Failed to load stock movement detail', [
                'user_id' => Auth::id(),
                'movement_id' => $id,
                'error' => $e->getMessage(),
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

            // Create stock movement record WITH pack fields
            $movementNumber = 'STK-ADJ-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            StockMovement::create([
                'movement_number' => $movementNumber,
                'inventory_item_id' => $item->id,
                'store_id' => 1,
                'movement_type_id' => $movementTypeId,
                'quantity' => $quantity,
                'pack_type' => null,  // Adjustment has no pack type
                'pack_size' => null,  // Adjustment has no pack size
                'number_of_packs' => null,  // Adjustment has no pack count
                'base_unit' => $item->base_unit ?? 'units',
                'unit_id' => null,
                'quantity_in_base_unit' => $quantity,
                'unit_cost' => $item->unit_cost ?? 0,
                'total_value' => $quantity * ($item->unit_cost ?? 0),
                'reason' => $validated['reason'] . ' (Manual adjustment)',
                'movement_date' => now(),
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Stock adjustment created', [
                'user_id' => Auth::id(),
                'movement_id' => $movement->id ?? 'created',
                'item_id' => $item->id,
                'adjustment_type' => $validated['adjustment_type'],
                'quantity' => $quantity,
                'new_stock' => $newStock,
            ]);

            return redirect()->route('store.stock-movements.index')
                ->with('success', "Stock adjusted. New balance: {$newStock} {$item->base_unit}(s).");

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
            $item = InventoryItem::find($itemId);
            $baseUnit = $item->base_unit ?? 'units';

            $movements = StockMovement::with(['movementType'])
                ->where('inventory_item_id', $itemId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'movements' => $movements->map(function ($movement) use ($baseUnit) {
                    return [
                        'id' => $movement->id,
                        'movement_number' => $movement->movement_number,
                        'type' => $movement->movementType->name ?? 'N/A',
                        'sign' => $movement->movementType->sign ?? '',
                        'quantity' => $movement->quantity,
                        'pack_type' => $movement->pack_type,
                        'pack_size' => $movement->pack_size,
                        'number_of_packs' => $movement->number_of_packs,
                        'base_unit' => $movement->base_unit ?? $baseUnit,
                        'quantity_in_base_unit' => $movement->quantity_in_base_unit,
                        'unit_cost' => $movement->unit_cost,
                        'total_value' => $movement->total_value,
                        'reason' => $movement->reason,
                        'date' => $movement->movement_date->format('Y-m-d'),
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



    // Add these methods to StockMovementController

/**
 * Export stock movements to Excel (CSV format) with correct Before/After stock
 */
/**
 * Export stock movements to Excel (CSV format)
 */
public function exportExcel(Request $request)
{
    $user = Auth::user();
    if (!$user->department || $user->department->name !== 'STORE') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $query = StockMovement::with(['inventoryItem', 'movementType'])
        ->orderBy('movement_date', 'asc')
        ->orderBy('created_at', 'asc');

    // Apply filters
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('movement_number', 'like', "%{$search}%")
              ->orWhereHas('inventoryItem', function($sq) use ($search) {
                  $sq->where('name', 'like', "%{$search}%");
              })
              ->orWhere('reason', 'like', "%{$search}%");
        });
    }

    if ($request->filled('item_id')) {
        $query->where('inventory_item_id', $request->item_id);
    }

    if ($request->filled('movement_type_id')) {
        $query->where('movement_type_id', $request->movement_type_id);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('movement_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('movement_date', '<=', $request->date_to);
    }

    $movements = $query->get();

    // Calculate running stock PER ITEM
    $runningStocks = [];
    $exportData = [];

    foreach ($movements as $movement) {
        $itemId = $movement->inventory_item_id;
        $isIn = $movement->movementType && $movement->movementType->sign == '+';
        $receivingUnit = $movement->inventoryItem->default_unit_of_measure_id ?? 'units';
        $baseUnit = $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'units';
        $quantity = $movement->quantity_in_base_unit;

        // Format breakdown for display
        if ($movement->pack_type) {
            $breakdown = number_format($movement->number_of_packs) . ' ' . ucfirst($movement->pack_type);
            if ($movement->pack_size) {
                $breakdown .= " (× " . number_format($movement->pack_size) . " $baseUnit/$movement->pack_type)";
            }
        } else {
            $breakdown = number_format($movement->quantity, 2) . ' ' . $receivingUnit;
        }

        $currentStock = $runningStocks[$itemId] ?? 0;
        $stockBefore = $currentStock;

        if ($isIn) {
            $currentStock += $quantity;
        } else {
            $currentStock -= $quantity;
        }
        $stockAfter = $currentStock;
        $runningStocks[$itemId] = $currentStock;

        $exportData[] = [
            'movement_number' => $movement->movement_number,
            'item_name' => $movement->inventoryItem->name ?? 'N/A',
            'item_code' => $movement->inventoryItem->item_code ?? 'N/A',
            'type' => $movement->movementType->name ?? 'N/A',
            'direction' => $isIn ? 'IN' : 'OUT',
            'quantity' => $movement->pack_type ? $movement->number_of_packs : $movement->quantity,
            'breakdown' => $breakdown,
            'total_units' => $movement->quantity_in_base_unit,
            'unit' => $baseUnit,
            'stock_before' => max(0, $stockBefore),
            'stock_after' => max(0, $stockAfter),
            'unit_cost' => $movement->unit_cost ?? 0,
            'total_value' => $movement->total_value ?? 0,
            'movement_date' => $movement->movement_date->format('Y-m-d'),
            'reason' => $movement->reason,
        ];
    }

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="stock_movements_' . date('Y-m-d') . '.csv"',
    ];

    $callback = function() use ($exportData) {
        $file = fopen('php://output', 'w');
        fputs($file, "\xEF\xBB\xBF");
        fputcsv($file, [
            'Movement #', 'Item Name', 'Item Code', 'Type', 'Direction',
            'Quantity', 'Breakdown', 'Total Units', 'Unit',
            'Stock BEFORE', 'Stock AFTER', 'Unit Cost (UGX)', 'Total Value (UGX)',
            'Movement Date', 'Reason'
        ]);
        foreach ($exportData as $row) {
            fputcsv($file, [
                $row['movement_number'], $row['item_name'], $row['item_code'],
                $row['type'], $row['direction'], $row['quantity'],
                $row['breakdown'], $row['total_units'], $row['unit'],
                $row['stock_before'], $row['stock_after'],
                $row['unit_cost'], $row['total_value'],
                $row['movement_date'], $row['reason'],
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

/**
 * Export stock movements to PDF
 */
public function exportPdf(Request $request)
{
    $user = Auth::user();
    if (!$user->department || $user->department->name !== 'STORE') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $query = StockMovement::with(['inventoryItem', 'movementType'])
        ->orderBy('movement_date', 'asc')
        ->orderBy('created_at', 'asc');

    // Apply same filters...
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('movement_number', 'like', "%{$search}%")
              ->orWhereHas('inventoryItem', function($sq) use ($search) {
                  $sq->where('name', 'like', "%{$search}%");
              })
              ->orWhere('reason', 'like', "%{$search}%");
        });
    }

    if ($request->filled('item_id')) {
        $query->where('inventory_item_id', $request->item_id);
    }

    if ($request->filled('movement_type_id')) {
        $query->where('movement_type_id', $request->movement_type_id);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('movement_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('movement_date', '<=', $request->date_to);
    }

    $movements = $query->get();

    $runningStocks = [];
    $exportData = [];
    $totalValue = 0;

    foreach ($movements as $movement) {
        $itemId = $movement->inventory_item_id;
        $isIn = $movement->movementType && $movement->movementType->sign == '+';
        $receivingUnit = $movement->inventoryItem->default_unit_of_measure_id ?? 'units';
        $baseUnit = $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'units';
        $quantity = $movement->quantity_in_base_unit;

        // Format breakdown for display
        if ($movement->pack_type) {
            $breakdown = number_format($movement->number_of_packs) . ' ' . ucfirst($movement->pack_type);
            if ($movement->pack_size) {
                $breakdown .= " (× " . number_format($movement->pack_size) . " $baseUnit/$movement->pack_type)";
            }
        } else {
            $breakdown = number_format($movement->quantity, 2) . ' ' . $receivingUnit;
        }

        $currentStock = $runningStocks[$itemId] ?? 0;
        $stockBefore = $currentStock;

        if ($isIn) {
            $currentStock += $quantity;
        } else {
            $currentStock -= $quantity;
        }
        $stockAfter = $currentStock;
        $runningStocks[$itemId] = $currentStock;

        $exportData[] = (object)[
            'movement_number' => $movement->movement_number,
            'item_name' => $movement->inventoryItem->name ?? 'N/A',
            'item_code' => $movement->inventoryItem->item_code ?? 'N/A',
            'type' => $movement->movementType->name ?? 'N/A',
            'direction' => $isIn ? 'IN' : 'OUT',
            'quantity' => $movement->pack_type ? $movement->number_of_packs : $movement->quantity,
            'breakdown' => $breakdown,
            'total_units' => $movement->quantity_in_base_unit,
            'unit' => $baseUnit,
            'stock_before' => max(0, $stockBefore),
            'stock_after' => max(0, $stockAfter),
            'unit_cost' => $movement->unit_cost ?? 0,
            'total_value' => $movement->total_value ?? 0,
            'movement_date' => $movement->movement_date->format('Y-m-d'),
            'reason' => $movement->reason,
        ];
        $totalValue += $movement->total_value ?? 0;
    }

    $pdf = Pdf::loadView('store.stock_movements.export_pdf', [
        'movements' => $exportData,
        'export_date' => now()->format('F d, Y H:i:s'),
        'total_movements' => count($exportData),
        'total_value' => $totalValue
    ]);

    return $pdf->download('stock_movements_' . date('Y-m-d') . '.pdf');
}
}
