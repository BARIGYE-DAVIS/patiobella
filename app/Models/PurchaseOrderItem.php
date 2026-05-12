<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'inventory_item_id',
        'unit_id',
        'quantity_ordered',
        'quantity_received',
        'unit_cost',
        'total_cost',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity_ordered' => 'decimal:6',
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
        return $this->quantity_ordered * $this->unit_cost;
    }

    /**
     * Get remaining quantity to be received.
     */
    public function getRemainingQuantityAttribute(): float
    {
        return $this->quantity_ordered - $this->quantity_received;
    }

    /**
     * Check if item is fully received.
     */
    public function isFullyReceived(): bool
    {
        return $this->quantity_received >= $this->quantity_ordered;
    }

    /**
     * Check if item is partially received.
     */
    public function isPartiallyReceived(): bool
    {
        return $this->quantity_received > 0 && $this->quantity_received < $this->quantity_ordered;
    }

    /**
     * Check if nothing has been received yet.
     */
    public function isNotReceived(): bool
    {
        return $this->quantity_received == 0;
    }

    /**
     * Add received quantity (called when GRN is created).
     */
    public function addReceivedQuantity(float $quantity): void
    {
        $this->quantity_received += $quantity;
        $this->save();

        // Update parent PO status
        $this->purchaseOrder->updateStatus();
    }

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
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

    public function goodsReceivedNoteItems()
    {
        return $this->hasMany(GoodsReceivedNoteItem::class, 'purchase_order_item_id');
    }


    // In app/Models/PurchaseOrderItem.php

public function goodsReceivedItems()
{
    return $this->hasMany(GoodsReceivedNoteItem::class, 'purchase_order_item_id');
}

public function getRemainingToReceiveAttribute()
{
    return $this->quantity_ordered - $this->quantity_received;
}

public function requisitions()
{
    return $this->hasMany(DepartmentRequisition::class, 'department_id');
}

}
