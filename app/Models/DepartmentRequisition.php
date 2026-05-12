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

    // Helper methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function canIssue()
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_PARTIALLY_ISSUED]);
    }

    public function canReturn()
    {
        return in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PARTIALLY_RETURNED]);
    }

    // Get total issued pieces
    public function getTotalIssuedPiecesAttribute()
    {
        return $this->items->sum('issued_total_pieces');
    }

    // Get total returned pieces
    public function getTotalReturnedPiecesAttribute()
    {
        return $this->items->sum('returned_total_pieces');
    }
    public function stockMovements()
{
    return $this->hasMany(StockMovement::class, 'department_requisition_id');
}

    // Get total consumed pieces
    public function getTotalConsumedPiecesAttribute()
    {
        return $this->items->sum('quantity_consumed');
    }
}
