<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceivedNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grn_number',
        'purchase_order_id',
        'vendor_id',
        'store_id',
        'received_by',
        'received_date',
        'delivery_note_number',
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
            'received_date' => 'date',
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
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Scopes
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

    // Helper methods
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

    /**
     * Calculate totals from items.
     */
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total_cost');
        $this->total_amount = $this->subtotal + $this->tax_amount;
        $this->saveQuietly();
    }

    /**
     * Complete the GRN and create stock movements.
     */
    public function complete(): void
    {
        if (!$this->canComplete()) {
            throw new \Exception('Cannot complete this GRN');
        }

        $this->status = self::STATUS_COMPLETED;
        $this->save();

        // Create stock movements for each item
        foreach ($this->items as $item) {
            $item->createStockMovement();
        }
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

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'goods_received_note_id');
    }
}