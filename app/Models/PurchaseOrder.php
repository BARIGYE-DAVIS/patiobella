<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number',
        'vendor_id',
        'delivery_address',
        'delivery_terms',
        'notes',
        'store_id',
        'ordered_by',
        'approved_by',
        'approved_at',
        'po_date',
        'expected_delivery_date',
        'subtotal',
        'tax_amount',
        'total_amount',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'po_date' => 'date',
            'expected_delivery_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_APPROVED = 'approved';
    const STATUS_SENT = 'sent';
    const STATUS_PARTIALLY_RECEIVED = 'partially_received';
    const STATUS_FULLY_RECEIVED = 'fully_received';
    const STATUS_CANCELLED = 'cancelled';

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeFullyReceived($query)
    {
        return $query->where('status', self::STATUS_FULLY_RECEIVED);
    }

    // Helper methods
    public function isFullyReceived(): bool
    {
        return $this->status === self::STATUS_FULLY_RECEIVED;
    }

    public function isPartiallyReceived(): bool
    {
        return $this->status === self::STATUS_PARTIALLY_RECEIVED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function canApprove(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canSend(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function canReceive(): bool
    {
        return in_array($this->status, [self::STATUS_SENT, self::STATUS_PARTIALLY_RECEIVED]);
    }

    /**
     * Update PO status based on received quantities.
     */
    public function updateStatus(): void
    {
        if ($this->isCancelled()) {
            return;
        }

        $totalOrdered = $this->items->sum('quantity_ordered');
        $totalReceived = $this->items->sum('quantity_received');

        if ($totalReceived == 0) {
            // Keep current status (approved/sent)
            return;
        }

        if ($totalReceived >= $totalOrdered) {
            $this->status = self::STATUS_FULLY_RECEIVED;
        } else {
            $this->status = self::STATUS_PARTIALLY_RECEIVED;
        }

        $this->saveQuietly();
    }

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function orderedBy()
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

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
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public function goodsReceivedNotes()
    {
        return $this->hasMany(GoodsReceivedNote::class, 'purchase_order_id');
    }
}