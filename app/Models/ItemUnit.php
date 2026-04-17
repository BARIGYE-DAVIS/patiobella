<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'unit_of_measure_id',
        'is_base_unit',
        'quantity_in_base_unit',
        'last_purchase_price',
        'average_purchase_price',
        'selling_price',
        'barcode',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_base_unit' => 'boolean',
            'quantity_in_base_unit' => 'decimal:6',
            'last_purchase_price' => 'decimal:2',
            'average_purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBaseUnit($query)
    {
        return $query->where('is_base_unit', true);
    }

    /**
     * Convert quantity from this unit to base unit.
     */
    public function toBaseUnit(float $quantity): float
    {
        return $quantity * $this->quantity_in_base_unit;
    }

    /**
     * Convert quantity from base unit to this unit.
     */
    public function fromBaseUnit(float $quantity): float
    {
        return $quantity / $this->quantity_in_base_unit;
    }

    /**
     * Calculate total cost for a quantity at last purchase price.
     */
    public function calculateTotalCost(float $quantity): float
    {
        return $quantity * $this->last_purchase_price;
    }

    /**
     * Calculate total selling price for a quantity.
     */
    public function calculateTotalSellingPrice(float $quantity): float
    {
        return $quantity * $this->selling_price;
    }

    // Relationships
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function unitOfMeasure()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unit_of_measure_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'unit_id');
    }

    public function goodsReceivedNoteItems()
    {
        return $this->hasMany(GoodsReceivedNoteItem::class, 'unit_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'unit_id');
    }
}