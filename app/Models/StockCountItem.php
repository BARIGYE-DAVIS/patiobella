<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCountItem extends Model
{
    use HasFactory;

    protected $table = 'stock_count_items';

    protected $fillable = [
        'stock_count_id',
        'inventory_item_id',
        'batch_id',
        'system_quantity',
        'physical_quantity',
        'physical_quantity_is_gross',  // ADDED - whether physical_quantity is gross weight or net
        'variance',
        'unit_cost',
        'variance_value',
        'reason_code',
        'reason_notes',
        'approved_by',
        'approved_at',
        'adjustment_movement_id',
    ];

    protected $casts = [
        'system_quantity' => 'decimal:6',
        'physical_quantity' => 'decimal:6',
        'physical_quantity_is_gross' => 'boolean',  // ADDED
        'variance' => 'decimal:6',
        'unit_cost' => 'decimal:2',
        'variance_value' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function stockCount()
    {
        return $this->belongsTo(StockCount::class, 'stock_count_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function adjustmentMovement()
    {
        return $this->belongsTo(StockMovement::class, 'adjustment_movement_id');
    }

    public function varianceReason()
    {
        return $this->belongsTo(StockVarianceReason::class, 'reason_code', 'code');
    }

    // Helper method - Calculate net quantity from gross weight
    public function calculateNetQuantity()
    {
        if ($this->physical_quantity_is_gross && $this->inventoryItem && $this->inventoryItem->empty_bottle_weight > 0) {
            return max(0, $this->physical_quantity - $this->inventoryItem->empty_bottle_weight);
        }
        return $this->physical_quantity;
    }

    // Get net quantity (actual product quantity)
    public function getNetQuantityAttribute()
    {
        return $this->calculateNetQuantity();
    }

    // Variance checks
    public function isNegativeVariance()
    {
        return $this->variance < 0;
    }

    public function isPositiveVariance()
    {
        return $this->variance > 0;
    }

    public function hasVariance()
    {
        return $this->variance != 0;
    }

    public function needsApproval()
    {
        if (!$this->hasVariance()) {
            return false;
        }

        $reason = $this->varianceReason;
        if ($reason) {
            return $reason->requires_approval;
        }

        return true;
    }

    public function isApproved()
    {
        return !is_null($this->approved_by) && !is_null($this->approved_at);
    }

    public function calculateVarianceValue()
    {
        if ($this->unit_cost && $this->variance) {
            return $this->variance * $this->unit_cost;
        }
        return 0;
    }

    // Auto-calculate variance before saving
    protected static function booted()
    {
        static::saving(function ($item) {
            // Calculate variance using net quantity
            $netQuantity = $item->calculateNetQuantity();
            $item->variance = $netQuantity - $item->system_quantity;

            if ($item->unit_cost) {
                $item->variance_value = $item->variance * $item->unit_cost;
            }
        });
    }
}
