<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requisition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'requisition_number',
        'requisition_type',
        'store_id',
        'requested_by',
        'approved_by',
        'date_needed',
        'status',
        'notes',
        'gm_notes',
        'gm_edited_by',
        'gm_edited_at',
        'rejection_reason',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'date_needed' => 'date',
            'approved_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FULFILLED = 'fulfilled';
    const STATUS_ORDERED = 'ordered';
    const STATUS_LPO_CREATED = 'lpo_created';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_FULFILLED => 'Fulfilled',
            self::STATUS_ORDERED => 'Ordered',
            self::STATUS_LPO_CREATED => 'LPO Created',
        ];
    }

    // Relationships
    public function store()
    {
        return $this->belongsTo(Department::class, 'store_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(RequisitionItem::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isFulfilled()
    {
        return $this->status === self::STATUS_FULFILLED;
    }

    public function approve($userId, $notes = null)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
            'gm_notes' => $notes,
        ]);
    }

    public function reject($userId, $reason)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $userId,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    // Batch-specific helper methods
    public function getUniqueBatchIds()
    {
        return $this->items->pluck('batch_id')->filter()->unique();
    }

    public function getExpiringBatches()
    {
        return $this->items->filter(function ($item) {
            return $item->batch && $item->batch->expiry_date &&
                   $item->batch->expiry_date->diffInDays(now()) <= 30;
        });
    }
}
