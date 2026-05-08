<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'lpo_id',
        'requisition_item_id',
        'inventory_item_id',
        'quantity_approved',
        'unit_cost',
        'total_cost',
        'metrics',
        'notes',
    ];

    protected $casts = [
        'quantity_approved' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function lpo()
    {
        return $this->belongsTo(Lpo::class);
    }

    public function requisitionItem()
    {
        return $this->belongsTo(RequisitionItem::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    // Dynamic accessor to get category name from inventory item
    public function getCategoryNameAttribute()
    {
        return $this->inventoryItem?->category?->name ?? null;
    }
}
