<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecipeItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recipe_items';

    protected $fillable = [
        'menu_item_id',
        'inventory_item_id',
        'quantity_required',
        'unit_of_measure_id',
        'wastage_percentage',
        'unit_cost_at_creation',
        'sort_order',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'quantity_required' => 'decimal:4',
        'wastage_percentage' => 'decimal:2',
        'unit_cost_at_creation' => 'decimal:2',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // =====================================================
    // Relationships
    // =====================================================

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function unitOfMeasure()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unit_of_measure_id');
    }

    // =====================================================
    // Accessors
    // =====================================================

    public function getTotalCostAttribute(): float
    {
        if (!$this->inventoryItem) {
            return 0;
        }

        $cost = $this->quantity_required * $this->inventoryItem->unit_cost;

        if ($this->wastage_percentage > 0) {
            $cost = $cost * (1 + ($this->wastage_percentage / 100));
        }

        return round($cost, 2);
    }

    public function getQuantityDisplayAttribute(): string
    {
        $unit = $this->unitOfMeasure ? $this->unitOfMeasure->symbol : 'unit';
        return $this->quantity_required . ' ' . $unit;
    }
}
