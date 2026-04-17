<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceivedNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_received_note_id',
        'purchase_order_item_id',
        'inventory_item_id',
        'unit_id',
        'quantity_received',
        'unit_cost',
        'total_cost',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity_received' => 'decimal:6',
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Calculate total cost dynamically.
     */
    public function calculateTotalCost(): float
    {
        return $this->quantity_received * $this->unit_cost;
    }

    /**
     * Get quantity in base unit.
     */
    public function getQuantityInBaseUnitAttribute(): float
    {
        return $this->unit->toBaseUnit($this->quantity_received);
    }

    /**
     * Get total value in base unit currency.
     */
    public function getTotalValueAttribute(): float
    {
        return $this->quantity_received * $this->unit_cost;
    }

    /**
     * Create stock movement for this received item.
     */
    public function createStockMovement(): void
    {
        // Get the item unit to calculate base unit quantity
        $itemUnit = $this->unit;
        
        // Calculate quantity in base unit
        $quantityInBaseUnit = $itemUnit->toBaseUnit($this->quantity_received);
        
        // Generate unique movement number
        $movementNumber = 'GRN-' . $this->goods_received_note_id . '-' . $this->id;
        
        // Create stock movement (STOCK IN)
        StockMovement::create([
            'movement_number' => $movementNumber,
            'inventory_item_id' => $this->inventory_item_id,
            'store_id' => $this->goodsReceivedNote->store_id,
            'movement_type_id' => $this->getMovementTypeId(), // Purchase/GRN type (sign = '+')
            'department_id' => null,
            'quantity' => $this->quantity_received,
            'unit_id' => $this->unit_id,
            'quantity_in_base_unit' => $quantityInBaseUnit,
            'unit_cost' => $this->unit_cost,
            'total_value' => $this->total_cost,
            'reason' => 'Goods received from PO: ' . $this->goodsReceivedNote->purchaseOrder->po_number,
            'movement_date' => $this->goodsReceivedNote->received_date,
            'approved_at' => now(),
            'approved_by' => $this->goodsReceivedNote->received_by,
            'purchase_order_id' => $this->goodsReceivedNote->purchase_order_id,
            'goods_received_note_id' => $this->goods_received_note_id,
            'created_by' => $this->created_by,
        ]);
        
        // Update the purchase order item received quantity
        $this->purchaseOrderItem->addReceivedQuantity($this->quantity_received);
        
        // Update or create stock balance
        $this->updateStockBalance();
    }
    
    /**
     * Get movement type ID for purchase/GRN (sign = '+')
     */
    private function getMovementTypeId(): int
    {
        // Find or create the movement type for purchases
        $movementType = StockMovementType::firstOrCreate(
            ['code' => 'PURCHASE'],
            [
                'name' => 'Purchase Receipt',
                'sign' => '+',
                'description' => 'Goods received from vendor',
                'requires_approval' => false,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );
        
        return $movementType->id;
    }
    
    /**
     * Update or create stock balance for this item and store.
     */
    private function updateStockBalance(): void
    {
        $balanceDate = $this->goodsReceivedNote->received_date;
        $storeId = $this->goodsReceivedNote->store_id;
        $itemId = $this->inventory_item_id;
        $quantityInBaseUnit = $this->quantity_in_base_unit;
        
        // Get the latest balance for this item and store
        $latestBalance = StockBalance::where('inventory_item_id', $itemId)
            ->where('store_id', $storeId)
            ->where('balance_date', '<=', $balanceDate)
            ->orderBy('balance_date', 'desc')
            ->first();
        
        $currentQuantity = $latestBalance ? $latestBalance->quantity : 0;
        $newQuantity = $currentQuantity + $quantityInBaseUnit;
        
        // Calculate new average cost
        $currentTotalValue = $latestBalance ? $latestBalance->quantity * $latestBalance->average_cost : 0;
        $newTotalValue = $currentTotalValue + $this->total_cost;
        $newAverageCost = $newQuantity > 0 ? $newTotalValue / $newQuantity : 0;
        
        // Create new balance record for this date
        StockBalance::updateOrCreate(
            [
                'inventory_item_id' => $itemId,
                'store_id' => $storeId,
                'balance_date' => $balanceDate,
            ],
            [
                'quantity' => $newQuantity,
                'average_cost' => $newAverageCost,
            ]
        );
    }

    // Relationships
    public function goodsReceivedNote()
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'goods_received_note_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function unit()
    {
        return $this->belongsTo(ItemUnit::class, 'unit_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}