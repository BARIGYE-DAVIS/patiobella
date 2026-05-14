<?php
// app/Models/CostPriceHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostPriceHistory extends Model
{
    use HasFactory;

    protected $table = 'cost_price_history';

    protected $fillable = [
        'inventory_item_id',
        'old_unit_cost',
        'new_unit_cost',
        'pack_type',
        'pack_size',
        'number_of_packs',
        'total_base_units',
        'reason',
        'changed_by',
    ];

    protected $casts = [
        'old_unit_cost' => 'decimal:2',
        'new_unit_cost' => 'decimal:2',
        'pack_size' => 'integer',
        'number_of_packs' => 'integer',
        'total_base_units' => 'decimal:6',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Accessors
    public function getFormattedOldCostAttribute()
    {
        return 'UGX ' . number_format($this->old_unit_cost, 2);
    }

    public function getFormattedNewCostAttribute()
    {
        return 'UGX ' . number_format($this->new_unit_cost, 2);
    }

    public function getPackSummaryAttribute()
    {
        if ($this->pack_type && $this->pack_size && $this->number_of_packs) {
            return $this->number_of_packs . ' ' . ucfirst($this->pack_type) . '(s) × ' . $this->pack_size . ' = ' . number_format($this->total_base_units, 2) . ' units';
        }
        return null;
    }
}
