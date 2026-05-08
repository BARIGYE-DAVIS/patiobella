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
        'inventory_item_id',
        'item_name',
        'quantity_requested',
        'metrics',
        'category_name',
        'quantity_approved',
        'notes',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:2',
        'quantity_approved' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function getItemDisplayNameAttribute()
    {
        if ($this->inventory_item_id) {
            return $this->inventoryItem ? $this->inventoryItem->name : 'Unknown Item';
        }
        return $this->item_name ?: 'Unknown Item';
    }
}
