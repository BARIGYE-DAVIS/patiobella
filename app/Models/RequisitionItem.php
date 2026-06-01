<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    use HasFactory;

    protected $table = 'requisition_items';

    protected $fillable = [
        'requisition_id',
        'batch_id',
        'inventory_item_id',
        'item_name',
        'quantity_requested',
        'unit_cost',
        'batch_stock_at_request',
        'total_stock_at_request',
        'metrics',
        'category_name',
        'quantity_approved',
        'notes',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'batch_stock_at_request' => 'decimal:2',
        'total_stock_at_request' => 'decimal:2',
        'quantity_approved' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function getItemDisplayNameAttribute()
    {
        if ($this->batch && $this->batch->inventoryItem) {
            return $this->batch->inventoryItem->name;
        }
        if ($this->inventory_item_id && $this->inventoryItem) {
            return $this->inventoryItem->name;
        }
        return $this->item_name ?: 'Unknown Item';
    }

    public function getBatchNumberAttribute()
    {
        return $this->batch ? $this->batch->batch_number : null;
    }

    public function getExpiryDateAttribute()
    {
        return $this->batch ? $this->batch->expiry_date : null;
    }

    public function getRemainingStockAttribute()
    {
        return $this->batch ? $this->batch->remaining_quantity : 0;
    }
}
