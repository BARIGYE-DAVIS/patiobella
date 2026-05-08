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
        'store_id',
        'received_by',
        'received_date',
        'delivery_note_number',
        'po_total_amount',
        'grn_total_amount',
        'subtotal',
        'tax_amount',
        'total_amount',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'received_date' => 'date',
        'po_total_amount' => 'decimal:2',
        'grn_total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('received_date', [$startDate, $endDate]);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function canComplete(): bool
    {
        return $this->status === self::STATUS_DRAFT && $this->items()->exists();
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total_cost');
        $this->total_amount = $this->subtotal + $this->tax_amount;
        $this->grn_total_amount = $this->total_amount;
        $this->saveQuietly();
    }

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // Matches controller eager load: 'createdBy'
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Keep for backward compatibility if used in views
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function items()
    {
        return $this->hasMany(GoodsReceivedNoteItem::class, 'goods_received_note_id');
    }
}
