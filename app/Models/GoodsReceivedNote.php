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
        'received_by',
        'received_by_user_id',
        'verified_by',
        'verified_at',
        'delivered_by_name',
        'delivered_by_phone',
        'delivered_by_email',
        'po_total_amount',
        'grn_total_amount',
        'subtotal',
        'vat_rate',
        'vat_amount',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'received_date' => 'date',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'po_total_amount' => 'decimal:2',
        'grn_total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_COMPLETED = 'completed';
    const STATUS_INVENTORY_UPDATED = 'inventory_updated';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    // ─────────────────────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Get the purchase order associated with this GRN.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * Get the vendor associated with this GRN.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Get the items for this GRN.
     */
    public function items()
    {
        return $this->hasMany(GoodsReceivedNoteItem::class, 'goods_received_note_id');
    }

    /**
     * Get the user who created this GRN.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this GRN.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who physically received the goods.
     */
    public function receivedByUser()
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    /**
     * Get the user who verified this GRN.
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the rating for this GRN.
     */
    public function rating()
    {
        return $this->hasOne(VendorRating::class, 'goods_received_note_id');
    }

    /**
     * Get the batches created from this GRN.
     */
    public function batches()
    {
        return $this->hasMany(Batch::class, 'goods_received_note_id');
    }

    /**
     * Get the documents attached to this GRN.
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'grn_id');
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Scope a query to only include draft GRNs.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope a query to only include completed GRNs.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include inventory updated GRNs.
     */
    public function scopeInventoryUpdated($query)
    {
        return $query->where('status', self::STATUS_INVENTORY_UPDATED);
    }

    /**
     * Scope a query to only include verified GRNs.
     */
    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_VERIFIED);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // HELPER METHODS
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Check if GRN is draft.
     */
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if GRN is completed.
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if GRN inventory has been updated.
     */
    public function isInventoryUpdated()
    {
        return $this->status === self::STATUS_INVENTORY_UPDATED;
    }

    /**
     * Check if GRN is verified.
     */
    public function isVerified()
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    /**
     * Check if GRN is rejected.
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if GRN can update inventory.
     */
    public function canUpdateInventory()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if GRN can be verified.
     */
    public function canBeVerified()
    {
        return $this->status === self::STATUS_INVENTORY_UPDATED;
    }

    /**
     * Get total accepted quantity.
     */
    public function getTotalAcceptedQuantityAttribute()
    {
        return $this->items->sum('quantity_accepted');
    }

    /**
     * Get total rejected quantity.
     */
    public function getTotalRejectedQuantityAttribute()
    {
        return $this->items->sum('quantity_rejected');
    }

    /**
     * Get total payable amount.
     */
    public function getTotalPayableAttribute()
    {
        return $this->items->sum('total_cost');
    }

    /**
     * Check if this GRN has been rated.
     */
    public function isRated()
    {
        return $this->rating()->exists();
    }

    /**
     * Get the rating value if exists.
     */
    public function getRatingValueAttribute()
    {
        return $this->rating ? $this->rating->rating : null;
    }

    /**
     * Get the rating comment if exists.
     */
    public function getRatingCommentAttribute()
    {
        return $this->rating ? $this->rating->comment : null;
    }

    /**
     * Get average rating for vendor.
     */
    public function getVendorAverageRatingAttribute()
    {
        if ($this->vendor) {
            return VendorRating::where('vendor_id', $this->vendor_id)->avg('rating');
        }
        return null;
    }

    /**
     * Verify the GRN.
     */
    public function verify($userId, $notes = null)
    {
        $this->status = self::STATUS_VERIFIED;
        $this->verified_by = $userId;
        $this->verified_at = now();
        if ($notes) {
            $this->notes = ($this->notes ? $this->notes . "\n\n" : '') . "Verification notes: " . $notes;
        }
        return $this->save();
    }

    /**
     * Reject the GRN.
     */
    public function reject($userId, $reason)
    {
        $this->status = self::STATUS_REJECTED;
        $this->verified_by = $userId;
        $this->verified_at = now();
        $this->notes = ($this->notes ? $this->notes . "\n\n" : '') . "Rejection reason: " . $reason;
        return $this->save();
    }
}
