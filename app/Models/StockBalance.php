<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'store_id',
        'quantity',
        'average_cost',
        'balance_date',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:6',
            'average_cost' => 'decimal:2',
            'balance_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Scopes
    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeByItem($query, $itemId)
    {
        return $query->where('inventory_item_id', $itemId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('balance_date', $date);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('balance_date', 'desc');
    }

    /**
     * Get current stock quantity.
     */
    public function getCurrentQuantityAttribute(): float
    {
        return $this->quantity;
    }

    /**
     * Get total stock value (quantity × average cost).
     */
    public function getTotalValueAttribute(): float
    {
        return $this->quantity * $this->average_cost;
    }

    /**
     * Check if stock is below minimum threshold.
     */
    public function isBelowMinimum(): bool
    {
        $item = $this->inventoryItem;
        return $item && $this->quantity < $item->minimum_stock;
    }

    /**
     * Check if stock is above maximum threshold.
     */
    public function isAboveMaximum(): bool
    {
        $item = $this->inventoryItem;
        return $item && $item->maximum_stock && $this->quantity > $item->maximum_stock;
    }

    /**
     * Get reorder recommendation.
     */
    public function getReorderRecommendation(): ?float
    {
        $item = $this->inventoryItem;
        
        if (!$item || !$this->isBelowMinimum()) {
            return null;
        }
        
        // Calculate how much to order to reach minimum + reorder quantity
        $shortage = $item->minimum_stock - $this->quantity;
        
        if ($item->reorder_quantity) {
            // Order in multiples of reorder quantity
            $needed = ceil($shortage / $item->reorder_quantity) * $item->reorder_quantity;
        } else {
            $needed = $shortage;
        }
        
        return $needed;
    }

    // Relationships
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}