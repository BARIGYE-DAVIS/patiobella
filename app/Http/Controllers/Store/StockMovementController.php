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
    // ─────────────────────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('store.dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $query = StockMovement::with(['inventoryItem', 'movementType', 'approvedBy']);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('movement_number', 'like', "%{$search}%")
                      ->orWhereHas('inventoryItem', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                      ->orWhere('reason', 'like', "%{$search}%");
                });
            }

            if ($request->filled('item_id'))          $query->where('inventory_item_id', $request->item_id);
            if ($request->filled('movement_type_id')) $query->where('movement_type_id', $request->movement_type_id);
            if ($request->filled('date_from'))        $query->whereDate('movement_date', '>=', $request->date_from);
            if ($request->filled('date_to'))          $query->whereDate('movement_date', '<=', $request->date_to);

            // ── Read stock_before and stock_after directly from the DB ────────
            $movements = $query
                ->orderBy('movement_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $items         = InventoryItem::where('is_active', true)->orderBy('name')->get();
            $movementTypes = StockMovementType::where('is_active', true)->orderBy('name')->get();

            if ($request->ajax() || $request->has('ajax')) {
                $html       = view('store.stock_movements.partials.table_rows', compact('movements'))->render();
                $pagination = $movements->appends($request->query())->links()->toHtml();
                return response()->json(['html' => $html, 'pagination' => $pagination, 'total' => $movements->total()]);
            }

            return view('store.stock_movements.index', compact('movements', 'items', 'movementTypes'));

        } catch (\Exception $e) {
            Log::error('Failed to load stock movements', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->route('store.dashboard')->with('error', 'Failed to load stock movements: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('store.dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $movement = StockMovement::with([
                'inventoryItem',
                'inventoryItem.category',
                'movementType',
                'approvedBy',
                'createdBy',
                'updatedBy',
            ])->findOrFail($id);

            // Read directly from stored columns — no calculation needed
            $stockBefore = $movement->stock_before;
            $stockAfter  = $movement->stock_after;

            return view('store.stock_movements.show', compact('movement', 'stockBefore', 'stockAfter'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('store.stock-movements.index')->with('error', 'Stock movement not found.');
        } catch (\Exception $e) {
            Log::error('Failed to load stock movement detail', ['user_id' => Auth::id(), 'id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('store.stock-movements.index')->with('error', 'Failed to load stock movement details.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────────────────────────

    public function create()
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('store.dashboard')->with('error', 'Unauthorized access');
        }
        $items = InventoryItem::where('is_active', true)->orderBy('name')->get();
        return view('store.stock_movements.create', compact('items'));
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // STORE (manual adjustment)
    // ─────────────────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('store.dashboard')->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'adjustment_type'   => 'required|in:add,subtract',
            'quantity'          => 'required|numeric|min:0.01',
            'reason'            => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $item        = InventoryItem::findOrFail($validated['inventory_item_id']);
            $quantity    = (float) $validated['quantity'];
            $stockBefore = (float) ($item->current_stock ?? 0);

            if ($validated['adjustment_type'] === 'add') {
                $newStock       = $stockBefore + $quantity;
                $movementTypeId = 2;    // MANUAL_IN
            } else {
                if ($stockBefore < $quantity) {
                    throw new \Exception('Insufficient stock. Current: ' . $stockBefore . ' ' . $item->base_unit . '(s).');
                }
                $newStock       = $stockBefore - $quantity;
                $movementTypeId = 3;    // MANUAL_OUT
            }

            $item->current_stock = $newStock;
            $item->save();

            $movementNumber = 'STK-ADJ-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $newMovement = StockMovement::create([
                'movement_number'       => $movementNumber,
                'inventory_item_id'     => $item->id,
                'store_id'              => 1,
                'movement_type_id'      => $movementTypeId,
                'quantity'              => $quantity,
                'pack_type'             => null,
                'pack_size'             => null,
                'number_of_packs'       => null,
                'base_unit'             => $item->base_unit ?? 'units',
                'unit_id'               => null,
                'quantity_in_base_unit' => $quantity,
                'unit_cost'             => $item->unit_cost ?? 0,
                'total_value'           => $quantity * ($item->unit_cost ?? 0),
                'reason'                => $validated['reason'] . ' (Manual adjustment)',
                'movement_date'         => now(),
                'approved_at'           => now(),
                'approved_by'           => Auth::id(),
                'created_by'            => Auth::id(),
                'stock_before'          => $stockBefore,
                'stock_after'           => $newStock,
            ]);

            DB::commit();

            Log::info('Stock adjustment created', [
                'user_id'      => Auth::id(),
                'movement'     => $newMovement->movement_number,
                'item_id'      => $item->id,
                'qty'          => $quantity,
                'stock_before' => $stockBefore,
                'stock_after'  => $newStock,
            ]);

            return redirect()->route('store.stock-movements.index')
                ->with('success', "Stock adjusted. New balance: {$newStock} {$item->base_unit}(s).");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create stock adjustment', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Failed to adjust stock: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // GET ITEM MOVEMENTS (AJAX)
    // ─────────────────────────────────────────────────────────────────────────────

    public function getItemMovements($itemId)
    {
        try {
            $item     = InventoryItem::findOrFail($itemId);
            $baseUnit = $item->base_unit ?? 'units';

            $movements = StockMovement::with('movementType')
                ->where('inventory_item_id', $itemId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success'   => true,
                'movements' => $movements->map(fn($m) => [
                    'id'                    => $m->id,
                    'movement_number'       => $m->movement_number,
                    'type'                  => $m->movementType->name ?? 'N/A',
                    'sign'                  => $m->movementType->sign ?? '',
                    'quantity'              => $m->quantity,
                    'pack_type'             => $m->pack_type,
                    'pack_size'             => $m->pack_size,
                    'number_of_packs'       => $m->number_of_packs,
                    'base_unit'             => $m->base_unit ?? $baseUnit,
                    'quantity_in_base_unit' => $m->quantity_in_base_unit,
                    'stock_before'          => $m->stock_before,
                    'stock_after'           => $m->stock_after,
                    'unit_cost'             => $m->unit_cost,
                    'total_value'           => $m->total_value,
                    'reason'                => $m->reason,
                    'date'                  => $m->movement_date->format('Y-m-d'),
                ]),
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch movements'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // EXPORT EXCEL (CSV)
    // ─────────────────────────────────────────────────────────────────────────────

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $movements = $this->buildExportData($request);

        $exportData = [];

        foreach ($movements as $movement) {
            $isIn     = $movement->movementType && $movement->movementType->sign === '+';
            $baseUnit = $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'units';

            $breakdown = $movement->pack_type
                ? number_format($movement->number_of_packs) . ' ' . ucfirst($movement->pack_type)
                  . ($movement->pack_size ? ' (× ' . number_format($movement->pack_size) . " {$baseUnit}/{$movement->pack_type})" : '')
                : number_format($movement->quantity, 2) . ' ' . ($movement->inventoryItem->default_unit_of_measure_id ?? 'units');

            $exportData[] = [
                'movement_number' => $movement->movement_number,
                'item_name'       => $movement->inventoryItem->name ?? 'N/A',
                'item_code'       => $movement->inventoryItem->item_code ?? 'N/A',
                'type'            => $movement->movementType->name ?? 'N/A',
                'direction'       => $isIn ? 'IN' : 'OUT',
                'quantity'        => $movement->pack_type ? $movement->number_of_packs : $movement->quantity,
                'breakdown'       => $breakdown,
                'total_units'     => $movement->quantity_in_base_unit,
                'unit'            => $baseUnit,
                'stock_before'    => $movement->stock_before,
                'stock_after'     => $movement->stock_after,
                'unit_cost'       => $movement->unit_cost ?? 0,
                'total_value'     => $movement->total_value ?? 0,
                'movement_date'   => $movement->movement_date->format('Y-m-d'),
                'reason'          => $movement->reason,
            ];
        }

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_movements_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($exportData) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Movement #', 'Item Name', 'Item Code', 'Type', 'Direction', 'Quantity', 'Breakdown', 'Total Units', 'Unit', 'Stock BEFORE', 'Stock AFTER', 'Unit Cost (UGX)', 'Total Value (UGX)', 'Movement Date', 'Reason']);
            foreach ($exportData as $row) {
                fputcsv($file, array_values($row));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // EXPORT PDF
    // ─────────────────────────────────────────────────────────────────────────────

 /**
 * EXPORT PDF
 */
/**
 * EXPORT PDF
 */
public function exportPdf(Request $request)
{
    $user = Auth::user();
    if (!$user->department || $user->department->name !== 'STORE') {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }

    $movements = $this->buildExportData($request);
    $exportData = [];
    $totalValue = 0;

    foreach ($movements as $movement) {
        // Get relationships safely
        $movementType = $movement->movementType;
        $inventoryItem = $movement->inventoryItem;

        $isIn = $movementType && $movementType->sign === '+';
        $baseUnit = $movement->base_unit ?? ($inventoryItem->base_unit ?? 'units');

        // Calculate breakdown
        if ($movement->pack_type) {
            $breakdown = number_format($movement->number_of_packs ?? $movement->quantity) . ' ' . ucfirst($movement->pack_type);
            if ($movement->pack_size) {
                $breakdown .= ' (× ' . number_format($movement->pack_size) . " {$baseUnit}/{$movement->pack_type})";
            }
        } else {
            $breakdown = number_format($movement->quantity, 2) . ' ' . ($movement->pack_type ?? $baseUnit);
        }

        // Create object with all needed properties
        $exportItem = new \stdClass();
        $exportItem->movement_number = $movement->movement_number;
        $exportItem->item_name = $inventoryItem->name ?? 'N/A';
        $exportItem->item_code = $inventoryItem->item_code ?? 'N/A';
        $exportItem->type = $movementType->name ?? 'N/A';
        $exportItem->direction = $isIn ? 'IN' : 'OUT';
        $exportItem->quantity = $movement->pack_type ? ($movement->number_of_packs ?? $movement->quantity) : $movement->quantity;  // ADD THIS LINE
        $exportItem->breakdown = $breakdown;
        $exportItem->total_units = $movement->quantity_in_base_unit ?? 0;
        $exportItem->unit = $baseUnit;
        $exportItem->stock_before = $movement->stock_before ?? 0;
        $exportItem->stock_after = $movement->stock_after ?? 0;
        $exportItem->unit_cost = $movement->unit_cost ?? 0;
        $exportItem->total_value = $movement->total_value ?? 0;
        $exportItem->movement_date = $movement->movement_date ? $movement->movement_date->format('Y-m-d') : 'N/A';
        $exportItem->reason = $movement->reason ?? 'N/A';

        $exportData[] = $exportItem;
        $totalValue += $movement->total_value ?? 0;
    }

    $pdf = Pdf::loadView('store.stock_movements.export_pdf', [
        'movements' => $exportData,
        'export_date' => now()->format('F d, Y H:i:s'),
        'total_movements' => count($exportData),
        'total_value' => $totalValue,
    ]);

    return $pdf->download('stock_movements_' . date('Y-m-d') . '.pdf');
}

    // ─────────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPER — build export query (shared by Excel & PDF)
    // No longer needs opening balance calculation — reads from stored columns
    // ─────────────────────────────────────────────────────────────────────────────

  /**
 * PRIVATE HELPER — build export query (shared by Excel & PDF)
 */
/**
 * PRIVATE HELPER — build export query (shared by Excel & PDF)
 */
private function buildExportData(Request $request)
{
    $query = StockMovement::with(['inventoryItem', 'movementType'])
        ->orderBy('movement_date', 'asc')
        ->orderBy('created_at', 'asc');

    if ($request->filled('search')) {
        $s = $request->search;
        $query->where(function($q) use ($s) {
            $q->where('movement_number', 'like', "%{$s}%")
              ->orWhereHas('inventoryItem', function($sq) use ($s) {
                  $sq->where('name', 'like', "%{$s}%");
              })
              ->orWhere('reason', 'like', "%{$s}%");
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

    $results = $query->get();

    // Load any missing relationships if needed
    foreach ($results as $result) {
        if (!$result->relationLoaded('inventoryItem')) {
            $result->load('inventoryItem');
        }
        if (!$result->relationLoaded('movementType')) {
            $result->load('movementType');
        }
    }

    return $results;
}

}
