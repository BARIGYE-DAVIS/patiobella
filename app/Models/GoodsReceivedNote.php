<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceivedNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'goods_received_notes';

    protected $fillable = [
        'grn_number',
        'purchase_order_id',
        'vendor_id',
        'received_date',
        'delivery_note_number',
        'po_total_amount',
        'grn_total_amount',
        'subtotal',
        'tax_amount',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'received_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'po_total_amount' => 'decimal:2',
        'grn_total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function items()
    {
        return $this->hasMany(GoodsReceivedNoteItem::class, 'goods_received_note_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInventoryUpdated($query)
    {
        return $query->where('status', 'inventory_updated');
    }

    // Helper methods
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isInventoryUpdated()
    {
        return $this->status === 'inventory_updated';
    }

    public function canUpdateInventory()
    {
        return $this->status === 'completed';
    }
}
