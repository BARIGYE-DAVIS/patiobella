<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentRequisition extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'department_requisitions';

    protected $fillable = [
        'requisition_number',
        'department_id',
        'requested_by',
        'approved_by',
        'date_needed',
        'status',
        'store_notes',
        'department_notes',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'date_needed' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PARTIALLY_ISSUED = 'partially_issued';
    const STATUS_ISSUED = 'issued';
    const STATUS_PARTIALLY_CONSUMED = 'partially_consumed';
    const STATUS_FULLY_CONSUMED = 'fully_consumed';
    const STATUS_PARTIALLY_RETURNED = 'partially_returned';
    const STATUS_RETURNED = 'returned';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
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
        return $this->hasMany(DepartmentRequisitionItem::class, 'department_requisition_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'department_requisition_id');
    }

    // Helper methods - Status checks
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isIssued()
    {
        return $this->status === self::STATUS_ISSUED;
    }

    public function isPartiallyIssued()
    {
        return $this->status === self::STATUS_PARTIALLY_ISSUED;
    }

    public function isPartiallyConsumed()
    {
        return $this->status === self::STATUS_PARTIALLY_CONSUMED;
    }

    public function isFullyConsumed()
    {
        return $this->status === self::STATUS_FULLY_CONSUMED;
    }

    public function isPartiallyReturned()
    {
        return $this->status === self::STATUS_PARTIALLY_RETURNED;
    }

    public function isReturned()
    {
        return $this->status === self::STATUS_RETURNED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    // Helper methods - Action availability
    public function canIssue()
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PARTIALLY_ISSUED]);
    }

    public function canRecordConsumption()
    {
        return in_array($this->status, [
            self::STATUS_ISSUED,
            self::STATUS_PARTIALLY_CONSUMED
        ]);
    }

    public function canReturn()
    {
        return in_array($this->status, [
            self::STATUS_ISSUED,
            self::STATUS_PARTIALLY_CONSUMED,
            self::STATUS_PARTIALLY_RETURNED
        ]);
    }

    // Helper methods - Calculations
    public function getTotalIssuedPiecesAttribute()
    {
        return $this->items->sum('issued_total_pieces');
    }

    public function getTotalReturnedPiecesAttribute()
    {
        return $this->items->sum('returned_total_pieces');
    }

    public function getTotalConsumedPiecesAttribute()
    {
        return $this->items->sum('quantity_consumed');
    }

    public function getTotalSoldPiecesAttribute()
    {
        return $this->items->sum('quantity_sold');
    }

    public function getTotalRemainingPiecesAttribute()
    {
        return $this->items->sum(function($item) {
            return $item->quantity_issued - ($item->quantity_sold + $item->quantity_consumed);
        });
    }

    public function getConsumptionPercentageAttribute()
    {
        $totalIssued = $this->items->sum('quantity_issued');
        $totalConsumed = $this->items->sum('quantity_consumed');
        $totalSold = $this->items->sum('quantity_sold');

        if ($totalIssued <= 0) return 0;

        return round((($totalConsumed + $totalSold) / $totalIssued) * 100, 2);
    }

    // Update status based on consumption
    public function updateStatusBasedOnConsumption()
    {
        $totalIssued = $this->items->sum('quantity_issued');
        $totalConsumed = $this->items->sum('quantity_consumed');
        $totalSold = $this->items->sum('quantity_sold');
        $totalUsed = $totalConsumed + $totalSold;

        if ($totalUsed >= $totalIssued && $totalIssued > 0) {
            $this->status = self::STATUS_FULLY_CONSUMED;
        } elseif ($totalUsed > 0) {
            $this->status = self::STATUS_PARTIALLY_CONSUMED;
        } else {
            // Keep existing status if no consumption yet
        }

        $this->save();

        return $this;
    }
}
