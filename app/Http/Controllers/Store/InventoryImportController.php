<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\UnitOfMeasure;
use App\Models\InventoryItem;
use App\Models\Batch;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryImportController extends Controller
{
    private function checkAuthorization()
    {
        $user = Auth::user();

        // Check if user has store department access
        if (!$user->department || $user->department->name !== 'STORE') {
            return false;
        }

        return true;
    }

    /**
     * Generate unique category code
     */
    private function generateCategoryCode()
    {
        $lastCategory = Category::orderBy('id', 'desc')->first();
        if ($lastCategory && preg_match('/CAT-(\d+)/', $lastCategory->code, $matches)) {
            $newNumber = intval($matches[1]) + 1;
        } else {
            $newNumber = 1;
        }
        return 'CAT-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Find or create category
     */
    private function findOrCreateCategory($categoryName)
    {
        if (empty($categoryName)) {
            return null;
        }

        $category = Category::where('name', $categoryName)->first();

        if (!$category) {
            $category = Category::create([
                'code' => $this->generateCategoryCode(),
                'name' => $categoryName,
                'is_active' => true,
            ]);
        }

        return $category;
    }

    /**
     * Generate unique UOM code
     */
    private function generateUomCode($name)
    {
        $code = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 5));
        $existing = UnitOfMeasure::where('code', $code)->exists();

        if ($existing) {
            $code = $code . rand(10, 99);
        }

        return $code;
    }

    /**
     * Find or create unit of measurement
     */
    private function findOrCreateUom($uomName)
    {
        if (empty($uomName)) {
            return null;
        }

        // Check by name first
        $uom = UnitOfMeasure::where('name', $uomName)->first();

        if (!$uom) {
            // Check by symbol
            $uom = UnitOfMeasure::where('symbol', $uomName)->first();
        }

        if (!$uom) {
            $code = $this->generateUomCode($uomName);
            $uom = UnitOfMeasure::create([
                'code' => $code,
                'name' => $uomName,
                'symbol' => $uomName,
                'is_base_unit' => 1,
                'is_active' => true,
            ]);
        }

        return $uom;
    }

    /**
     * Generate item code
     */
    private function generateItemCode($name)
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 3));
        $lastItem = InventoryItem::where('item_code', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastItem) {
            $lastNumber = intval(substr($lastItem->item_code, 3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Generate batch number
     */
    private function generateBatchNumber()
    {
        $lastBatch = Batch::orderBy('id', 'desc')->first();
        if ($lastBatch) {
            $lastNumber = intval(substr($lastBatch->batch_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'BAT-IMP-' . date('Ymd') . '-' . $newNumber;
    }

    /**
     * Generate stock movement number
     */
    private function generateMovementNumber()
    {
        $lastMovement = StockMovement::orderBy('id', 'desc')->first();
        if ($lastMovement) {
            $lastNumber = intval(substr($lastMovement->movement_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'STK-IN-' . date('Ymd') . '-' . $newNumber;
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Store department only.');
        }

        return view('store.inventory.import');
    }

    /**
     * Download template CSV file
     */
    public function downloadTemplate()
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        // Updated headers to include empty_bottle_weight
        $headers = ['name', 'category', 'uom', 'quantity', 'unit_cost', 'empty_bottle_weight', 'expiry_date'];

        $exampleData = [
            ['Chicken Breast', 'Meat', 'kg', 100, 12.50, 0, '2026-12-31'],
            ['Basmati Rice', 'Dry Goods', 'kg', 50, 2.30, 0, '2026-12-31'],
            ['Cooking Oil', 'Oils & Fats', 'litre', 30, 4.50, 0, '2026-12-31'],
            ['Tomatoes', 'Vegetables', 'kg', 20, 1.20, 0, '2026-06-30'],
            ['Onions', 'Vegetables', 'kg', 25, 0.80, 0, '2026-06-30'],
            ['Mountain Dew', 'Soft Drinks', 'bottle', 60, 0.75, 0.050, '2026-12-31'],
            ['Fanta Orange', 'Soft Drinks', 'bottle', 55, 0.75, 0.050, '2026-12-31'],
            ['Four Cousins Wine', 'Wine', 'bottle', 30, 35.00, 0.900, '2026-12-31'],
        ];

        $output = fopen('php://temp', 'w');

        fputcsv($output, $headers);

        foreach ($exampleData as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="inventory_import_template.csv"');
    }

    /**
     * Process import
     */
    public function import(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv|max:5120',
        ]);

        $results = [
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        DB::beginTransaction();

        try {
            $file = $request->file('file');
            $data = $this->parseCSV($file);

            // Skip header row
            $rows = array_slice($data, 1);
            $results['total'] = count($rows);

            foreach ($rows as $rowIndex => $row) {
                try {
                    $rowNumber = $rowIndex + 2;

                    // Validate required fields
                    $name = trim($row[0] ?? '');
                    $categoryName = trim($row[1] ?? '');
                    $uomName = trim($row[2] ?? '');
                    $quantity = floatval($row[3] ?? 0);

                    // Unit cost - column 4
                    $unitCost = isset($row[4]) ? floatval($row[4]) : 0;

                    // Empty bottle weight - column 5 (in kg)
                    $emptyBottleWeight = isset($row[5]) ? floatval($row[5]) : 0;

                    // Expiry date - column 6
                    $expiryDate = isset($row[6]) && !empty($row[6]) ? trim($row[6]) : null;

                    // Backward compatibility: if fewer columns, adjust
                    if (count($row) <= 5) {
                        // Old format: name, category, uom, quantity, expiry_date
                        $expiryDate = isset($row[4]) && !empty($row[4]) ? trim($row[4]) : null;
                        $unitCost = 0;
                        $emptyBottleWeight = 0;
                    } elseif (count($row) == 6) {
                        // Format without empty_bottle_weight: name, category, uom, quantity, unit_cost, expiry_date
                        $expiryDate = isset($row[5]) && !empty($row[5]) ? trim($row[5]) : null;
                        $emptyBottleWeight = 0;
                    }

                    if (empty($name)) {
                        throw new \Exception("Row {$rowNumber}: Name is required");
                    }

                    if (empty($categoryName)) {
                        throw new \Exception("Row {$rowNumber}: Category is required");
                    }

                    if (empty($uomName)) {
                        throw new \Exception("Row {$rowNumber}: Unit of Measurement is required");
                    }

                    if ($quantity <= 0) {
                        throw new \Exception("Row {$rowNumber}: Quantity must be greater than 0");
                    }

                    if ($unitCost < 0) {
                        throw new \Exception("Row {$rowNumber}: Unit cost cannot be negative");
                    }

                    if ($emptyBottleWeight < 0) {
                        throw new \Exception("Row {$rowNumber}: Empty bottle weight cannot be negative");
                    }

                    // Find or create category
                    $category = $this->findOrCreateCategory($categoryName);

                    // Find or create UOM (for reference only)
                    $uom = $this->findOrCreateUom($uomName);

                    // Check if item already exists by name
                    $existingItem = InventoryItem::where('name', $name)->first();

                    if ($existingItem) {
                        // Item exists - add new batch only
                        $inventoryItem = $existingItem;

                        // Update empty bottle weight if provided and different
                        if ($emptyBottleWeight > 0 && $inventoryItem->empty_bottle_weight != $emptyBottleWeight) {
                            $inventoryItem->empty_bottle_weight = $emptyBottleWeight;
                            $inventoryItem->save();
                        }

                        // Generate batch number
                        $batchNumber = $this->generateBatchNumber();

                        // Create batch with the provided unit cost
                        $batch = Batch::create([
                            'batch_number' => $batchNumber,
                            'inventory_item_id' => $inventoryItem->id,
                            'initial_quantity' => $quantity,
                            'remaining_quantity' => $quantity,
                            'unit_cost' => $unitCost,
                            'total_cost' => $unitCost * $quantity,
                            'unit_of_measurement' => $uomName,
                            'expiry_date' => $expiryDate ? date('Y-m-d', strtotime($expiryDate)) : null,
                            'batch_status' => 'active',
                            'notes' => 'Imported from CSV',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Update last_purchase_price on inventory item
                        if ($unitCost > 0) {
                            $inventoryItem->last_purchase_price = $unitCost;
                            $inventoryItem->save();
                        }

                        // Create stock movement
                        $this->createStockMovement($inventoryItem, $batch, $quantity, $unitCost, 'Manual Import - Added to existing item');

                    } else {
                        // New item - create everything
                        $itemCode = $this->generateItemCode($name);
                        $batchNumber = $this->generateBatchNumber();

                        // Create inventory item
                        $inventoryItem = InventoryItem::create([
                            'item_code' => $itemCode,
                            'name' => $name,
                            'category_id' => $category->id,
                            'unit_of_measurement' => $uomName,
                            'empty_bottle_weight' => $emptyBottleWeight,
                            'minimum_stock' => 0,
                            'maximum_stock' => 0,
                            'reorder_quantity' => 0,
                            'last_purchase_price' => $unitCost,
                            'selling_price' => 0,
                            'is_sellable' => false,
                            'is_perishable' => !empty($expiryDate),
                            'is_taxable' => true,
                            'is_active' => true,
                            'created_by' => Auth::id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Create batch
                        $batch = Batch::create([
                            'batch_number' => $batchNumber,
                            'inventory_item_id' => $inventoryItem->id,
                            'initial_quantity' => $quantity,
                            'remaining_quantity' => $quantity,
                            'unit_cost' => $unitCost,
                            'total_cost' => $unitCost * $quantity,
                            'unit_of_measurement' => $uomName,
                            'expiry_date' => $expiryDate ? date('Y-m-d', strtotime($expiryDate)) : null,
                            'batch_status' => 'active',
                            'notes' => 'Imported from CSV',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Create stock movement
                        $this->createStockMovement($inventoryItem, $batch, $quantity, $unitCost, 'Initial stock import');
                    }

                    $results['success']++;

                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = $e->getMessage();
                    Log::warning('Row import failed', [
                        'row' => $rowNumber ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            $message = "Import completed. Success: {$results['success']}, Failed: {$results['failed']}";

            return response()->json([
                'success' => true,
                'message' => $message,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Inventory import failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'results' => $results
            ], 500);
        }
    }

    /**
     * Create stock movement record
     */
    private function createStockMovement($inventoryItem, $batch, $quantity, $unitCost, $reason)
    {
        $movementNumber = $this->generateMovementNumber();

        // Calculate stock before (excluding current batch if it's new)
        $stockBefore = Batch::where('inventory_item_id', $inventoryItem->id)
            ->where('id', '!=', $batch->id ?? 0)
            ->where('batch_status', 'active')
            ->sum('remaining_quantity');

        // If batch is new and not yet saved, stock before is just existing stock
        if (!$batch->id) {
            $stockBefore = Batch::where('inventory_item_id', $inventoryItem->id)
                ->where('batch_status', 'active')
                ->sum('remaining_quantity');
        }

        $stockAfter = $stockBefore + $quantity;

        StockMovement::create([
            'movement_number' => $movementNumber,
            'inventory_item_id' => $inventoryItem->id,
            'batch_id' => $batch->id,
            'store_id' => 1,
            'movement_type_id' => 2, // Manual Stock In
            'quantity' => $quantity,
            'pack_type' => null,
            'pack_size' => null,
            'number_of_packs' => null,
            'base_unit' => $inventoryItem->unit_of_measurement,
            'unit_id' => null,
            'quantity_in_base_unit' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'unit_cost' => $unitCost,
            'total_value' => $unitCost * $quantity,
            'reason' => $reason,
            'movement_date' => now()->toDateString(),
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'created_by' => Auth::id(),
            'taken_by' => null,
            'returned_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Parse CSV file
     */
    private function parseCSV($file)
    {
        $data = [];
        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        return $data;
    }
}
