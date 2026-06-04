<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * InventoryCostService
 *
 * Resolves the current unit cost for any inventory item using a 3-layer
 * FIFO strategy:
 *
 *   Layer 1 — Central store batches (oldest batch with remaining_quantity > 0)
 *   Layer 2 — Department stock (batch_issuances JSON on requisition items
 *              that still have unconsumed stock in the department)
 *   Layer 3 — Last known cost fallback (most recently depleted batch)
 *
 * Usage:
 *   $service = new InventoryCostService();
 *   $result  = $service->getCurrentUnitCost($inventoryItemId);
 *
 *   $result = [
 *       'unit_cost'    => 1500.00,
 *       'source'       => 'central_batch',   // or 'department_stock' or 'last_known'
 *       'batch_id'     => 258,
 *       'batch_number' => 'BAT-GRN-20260529-000001-002',
 *       'found'        => true,
 *   ]
 */
class InventoryCostService
{
    /**
     * Get the current unit cost for an inventory item.
     * Returns an array with the cost and metadata about where it came from.
     */
    public function getCurrentUnitCost(int $inventoryItemId): array
    {
        // -------------------------------------------------------
        // LAYER 1: Central store — oldest active batch with stock
        // -------------------------------------------------------
        $centralBatch = DB::table('batches')
            ->where('inventory_item_id', $inventoryItemId)
            ->where('remaining_quantity', '>', 0)
            ->whereNull('deleted_at')
            ->whereNotIn('batch_status', ['depleted', 'cancelled'])
            ->orderBy('created_at', 'asc')   // FIFO: oldest first
            ->orderBy('id', 'asc')
            ->select('id', 'batch_number', 'unit_cost', 'remaining_quantity')
            ->first();

        if ($centralBatch && $centralBatch->unit_cost > 0) {
            return [
                'unit_cost'    => (float) $centralBatch->unit_cost,
                'source'       => 'central_batch',
                'batch_id'     => $centralBatch->id,
                'batch_number' => $centralBatch->batch_number,
                'found'        => true,
            ];
        }

        // -------------------------------------------------------
        // LAYER 2: Department stock — items issued but not yet
        // fully consumed, reading cost from batch_issuances JSON
        // -------------------------------------------------------
        $departmentItems = DB::table('department_requisition_items')
            ->where('inventory_item_id', $inventoryItemId)
            ->whereNotNull('batch_issuances')
            ->where('batch_issuances', '!=', 'null')
            ->where('batch_issuances', '!=', '[]')
            ->whereRaw('(quantity_issued - quantity_consumed - quantity_returned) > 0')
            ->orderBy('created_at', 'asc')   // FIFO: oldest issuance first
            ->select('batch_issuances', 'quantity_issued', 'quantity_consumed', 'quantity_returned')
            ->get();

        foreach ($departmentItems as $deptItem) {
            $issuances = $this->parseJson($deptItem->batch_issuances);

            if (empty($issuances)) {
                continue;
            }

            // Walk through issuances in order (they are stored FIFO already)
            foreach ($issuances as $issuance) {
                $batchCost = (float) ($issuance['unit_cost'] ?? 0);

                if ($batchCost > 0) {
                    return [
                        'unit_cost'    => $batchCost,
                        'source'       => 'department_stock',
                        'batch_id'     => $issuance['batch_id'] ?? null,
                        'batch_number' => $issuance['batch_number'] ?? null,
                        'found'        => true,
                    ];
                }
            }
        }

        // -------------------------------------------------------
        // LAYER 3: Last known cost fallback — most recently
        // depleted/used batch so we never return zero
        // -------------------------------------------------------
        $lastBatch = DB::table('batches')
            ->where('inventory_item_id', $inventoryItemId)
            ->where('unit_cost', '>', 0)
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc')  // Most recently active
            ->orderBy('id', 'desc')
            ->select('id', 'batch_number', 'unit_cost')
            ->first();

        if ($lastBatch) {
            return [
                'unit_cost'    => (float) $lastBatch->unit_cost,
                'source'       => 'last_known',
                'batch_id'     => $lastBatch->id,
                'batch_number' => $lastBatch->batch_number,
                'found'        => true,
            ];
        }

        // Nothing found at all
        return [
            'unit_cost'    => 0.0,
            'source'       => 'none',
            'batch_id'     => null,
            'batch_number' => null,
            'found'        => false,
        ];
    }

    /**
     * Get costs for multiple inventory items in one call.
     * More efficient than calling getCurrentUnitCost() in a loop.
     *
     * Returns: [ inventory_item_id => result_array, ... ]
     */
    public function getBulkCurrentUnitCosts(array $inventoryItemIds): array
    {
        if (empty($inventoryItemIds)) {
            return [];
        }

        $results = [];

        // ----------------------------------------------------------
        // LAYER 1: Pull the oldest active batch per inventory item
        // using a GROUP BY trick so we get one row per item
        // ----------------------------------------------------------
        $centralBatches = DB::table('batches')
            ->whereIn('inventory_item_id', $inventoryItemIds)
            ->where('remaining_quantity', '>', 0)
            ->whereNull('deleted_at')
            ->whereNotIn('batch_status', ['depleted', 'cancelled'])
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->select('inventory_item_id', 'id', 'batch_number', 'unit_cost')
            ->get()
            ->groupBy('inventory_item_id');

        foreach ($centralBatches as $itemId => $batches) {
            $oldest = $batches->first();
            if ($oldest && $oldest->unit_cost > 0) {
                $results[$itemId] = [
                    'unit_cost'    => (float) $oldest->unit_cost,
                    'source'       => 'central_batch',
                    'batch_id'     => $oldest->id,
                    'batch_number' => $oldest->batch_number,
                    'found'        => true,
                ];
            }
        }

        // Find which items still need costing
        $remaining = array_diff($inventoryItemIds, array_keys($results));

        if (!empty($remaining)) {
            // ----------------------------------------------------------
            // LAYER 2: Department stock for items not found in central
            // ----------------------------------------------------------
            $deptItems = DB::table('department_requisition_items')
                ->whereIn('inventory_item_id', $remaining)
                ->whereNotNull('batch_issuances')
                ->where('batch_issuances', '!=', 'null')
                ->where('batch_issuances', '!=', '[]')
                ->whereRaw('(quantity_issued - quantity_consumed - quantity_returned) > 0')
                ->orderBy('created_at', 'asc')
                ->select('inventory_item_id', 'batch_issuances')
                ->get()
                ->groupBy('inventory_item_id');

            foreach ($deptItems as $itemId => $items) {
                if (isset($results[$itemId])) {
                    continue;
                }

                foreach ($items as $deptItem) {
                    $issuances = $this->parseJson($deptItem->batch_issuances);
                    foreach ($issuances as $issuance) {
                        $batchCost = (float) ($issuance['unit_cost'] ?? 0);
                        if ($batchCost > 0) {
                            $results[$itemId] = [
                                'unit_cost'    => $batchCost,
                                'source'       => 'department_stock',
                                'batch_id'     => $issuance['batch_id'] ?? null,
                                'batch_number' => $issuance['batch_number'] ?? null,
                                'found'        => true,
                            ];
                            break 2;
                        }
                    }
                }
            }

            // ----------------------------------------------------------
            // LAYER 3: Last known cost fallback for anything still missing
            // ----------------------------------------------------------
            $stillMissing = array_diff($remaining, array_keys($results));

            if (!empty($stillMissing)) {
                $lastBatches = DB::table('batches')
                    ->whereIn('inventory_item_id', $stillMissing)
                    ->where('unit_cost', '>', 0)
                    ->whereNull('deleted_at')
                    ->orderBy('updated_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->select('inventory_item_id', 'id', 'batch_number', 'unit_cost')
                    ->get()
                    ->groupBy('inventory_item_id');

                foreach ($lastBatches as $itemId => $batches) {
                    if (!isset($results[$itemId])) {
                        $last = $batches->first();
                        $results[$itemId] = [
                            'unit_cost'    => (float) $last->unit_cost,
                            'source'       => 'last_known',
                            'batch_id'     => $last->id,
                            'batch_number' => $last->batch_number,
                            'found'        => true,
                        ];
                    }
                }
            }
        }

        // Fill any completely missing items with zero
        foreach ($inventoryItemIds as $id) {
            if (!isset($results[$id])) {
                $results[$id] = [
                    'unit_cost'    => 0.0,
                    'source'       => 'none',
                    'batch_id'     => null,
                    'batch_number' => null,
                    'found'        => false,
                ];
            }
        }

        return $results;
    }

    /**
     * Safely parse a JSON string into an array.
     */
    private function parseJson(?string $json): array
    {
        if (empty($json)) {
            return [];
        }

        try {
            $decoded = json_decode($json, true);
            return is_array($decoded) ? $decoded : [];
        } catch (\Exception $e) {
            Log::warning('InventoryCostService: failed to parse batch_issuances JSON', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
