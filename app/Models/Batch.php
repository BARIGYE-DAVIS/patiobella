<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'batch_number',
        'inventory_item_id',
        'goods_received_note_id',
        'supplier_id',
        'initial_quantity',
        'remaining_quantity',
        'unit_cost',
        'total_cost',
        'base_unit',
        'manufacture_date',
        'expiry_date',
        'batch_status',
        'supplier_batch_number',
        'notes'
    ];

    protected $casts = [
        'initial_quantity' => 'decimal:6',
        'remaining_quantity' => 'decimal:6',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_DEPLETED = 'depleted';
    const STATUS_EXPIRED = 'expired';
    const STATUS_PARTIALLY_USED = 'partially_used';

    // Relationships
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function goodsReceivedNote()
    {
        return $this->belongsTo(GoodsReceivedNote::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Vendor::class, 'supplier_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('batch_status', self::STATUS_ACTIVE)
                     ->where('remaining_quantity', '>', 0)
                     ->where(function($q) {
                         $q->whereNull('expiry_date')
                           ->orWhere('expiry_date', '>=', now());
                     });
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now())
                     ->where('remaining_quantity', '>', 0);
    }

    // Methods
    public function isActive()
    {
        return $this->batch_status === self::STATUS_ACTIVE
               && $this->remaining_quantity > 0
               && ($this->expiry_date === null || $this->expiry_date >= now());
    }

    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date < now() && $this->remaining_quantity > 0;
    }

    public function reduceQuantity($quantity, $reason = null)
    {
        if ($quantity > $this->remaining_quantity) {
            throw new \Exception("Insufficient quantity in batch {$this->batch_number}");
        }

        $this->remaining_quantity -= $quantity;

        if ($this->remaining_quantity <= 0) {
            $this->batch_status = self::STATUS_DEPLETED;
        } elseif ($this->remaining_quantity < $this->initial_quantity) {
            $this->batch_status = self::STATUS_PARTIALLY_USED;
        }

        $this->save();

        return $this;
    }

    public function getRemainingValue()
    {
        return $this->remaining_quantity * $this->unit_cost;
    }
}
