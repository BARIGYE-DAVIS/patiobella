<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceivedNoteItem extends Model
{
    use HasFactory;

    protected $table = 'goods_received_items';

    protected $fillable = [
        'goods_received_note_id',
        'purchase_order_item_id',
        'inventory_item_id',
        'unit_id',
        'quantity_ordered',
        'quantity_received',
        'quantity_accepted',
        'quantity_rejected',
        'rejection_reason',
        'unit_cost',
        'po_item_total_amount',
        'total_cost',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity_ordered' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'quantity_accepted' => 'decimal:2',
        'quantity_rejected' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'po_item_total_amount' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Calculate total cost dynamically.
     */
    public function calculateTotalCost(): float
    {
        return $this->quantity_accepted * $this->unit_cost;
    }

    /**
     * Get quantity in base unit.
     */
    public function getQuantityInBaseUnitAttribute(): float
    {
        if ($this->unit) {
            return $this->unit->toBaseUnit($this->quantity_accepted);
        }
        return $this->quantity_accepted;
    }

    /**
     * Get total value in base unit currency.
     */
    public function getTotalValueAttribute(): float
    {
        return $this->quantity_accepted * $this->unit_cost;
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