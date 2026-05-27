<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentRequisitionItem extends Model
{
    use HasFactory;

    protected $table = 'department_requisition_items';

    protected $fillable = [
        'department_requisition_id',
        'inventory_item_id',
        'department_id',
        'quantity_requested',
        'quantity_approved',
        'approved_pack_type',
        'approved_pack_size',
        'approved_metrics',
        'approval_notes',
        'requested_pack_type',
        'requested_pack_size',
        'quantity_issued',
        'issued_pack_type',
        'issued_pack_size',
        'issued_total_pieces',
        'quantity_returned',
        'returned_pack_type',
        'returned_pack_size',
        'returned_total_pieces',
        'quantity_sold',
        'quantity_consumed',
        'empty_bottle_weight',
        'return_reason',
        'returned_at',
        'last_consumed_at',
        'last_sold_at',
        'metrics',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:2',
        'quantity_approved' => 'decimal:2',
        'department_id' => 'integer',
        'requested_pack_size' => 'integer',
        'approved_pack_size' => 'integer',
        'quantity_issued' => 'decimal:2',
        'issued_pack_size' => 'integer',
        'issued_total_pieces' => 'decimal:2',
        'quantity_returned' => 'decimal:2',
        'returned_pack_size' => 'integer',
        'returned_total_pieces' => 'decimal:2',
        'quantity_sold' => 'decimal:2',
        'quantity_consumed' => 'decimal:2',
        'empty_bottle_weight' => 'decimal:6',
        'returned_at' => 'datetime',
        'last_consumed_at' => 'datetime',
        'last_sold_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function departmentRequisition()
    {
        return $this->belongsTo(DepartmentRequisition::class, 'department_requisition_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    // Helper methods - Remaining calculations
    public function getRemainingToIssueAttribute()
    {
        $approved = $this->quantity_approved ?? $this->quantity_requested;
        return max(0, $approved - ($this->quantity_issued ?? 0));
    }

    public function getRemainingToReturnAttribute()
    {
        return ($this->issued_total_pieces ?? 0) -
               (($this->quantity_consumed ?? 0) +
                ($this->quantity_returned ?? 0) +
                ($this->quantity_sold ?? 0));
    }

    public function getRemainingInDepartmentAttribute()
    {
        return max(0, ($this->issued_total_pieces ?? 0) -
               (($this->quantity_consumed ?? 0) +
                ($this->quantity_returned ?? 0) +
                ($this->quantity_sold ?? 0)));
    }

    // Helper method for "Used" (consumed + sold)
    public function getUsedAttribute()
    {
        return ($this->quantity_consumed ?? 0) + ($this->quantity_sold ?? 0);
    }

    // Helper methods - Status checks
    public function getIsFullyIssuedAttribute()
    {
        $approved = $this->quantity_approved ?? $this->quantity_requested;
        return ($this->quantity_issued ?? 0) >= $approved;
    }

    public function getIsFullyReturnedAttribute()
    {
        return $this->getRemainingToReturnAttribute() <= 0;
    }

    // Helper methods - Calculations
    public function calculateIssuedTotalPieces()
    {
        if ($this->issued_pack_type && $this->issued_pack_size) {
            return $this->quantity_issued * $this->issued_pack_size;
        }
        return $this->quantity_issued;
    }

    public function calculateReturnedTotalPieces()
    {
        if ($this->returned_pack_type && $this->returned_pack_size) {
            return $this->quantity_returned * $this->returned_pack_size;
        }
        return $this->quantity_returned;
    }

    // Helper methods - Empty bottle weight
    public function hasEmptyBottleWeight()
    {
        return $this->empty_bottle_weight > 0;
    }

    public function calculateNetWeight($grossWeight)
    {
        if ($this->hasEmptyBottleWeight()) {
            return max(0, $grossWeight - $this->empty_bottle_weight);
        }
        return $grossWeight;
    }

    // Helper methods - Update actions
    public function updateConsumedQuantity($consumed)
    {
        $this->quantity_consumed = $consumed;
        $this->last_consumed_at = now();
        $this->save();

        return $this;
    }

    public function updateSoldQuantity($sold)
    {
        $this->quantity_sold = $sold;
        $this->last_sold_at = now();
        $this->save();

        return $this;
    }

    public function recordReturn($quantity, $packType = null, $packSize = null, $reason = null)
    {
        $this->quantity_returned = $quantity;
        $this->returned_pack_type = $packType;
        $this->returned_pack_size = $packSize;
        $this->returned_total_pieces = $this->calculateReturnedTotalPieces();
        $this->return_reason = $reason;
        $this->returned_at = now();
        $this->save();

        return $this;
    }

    public function recordIssue($quantity, $packType = null, $packSize = null)
    {
        $this->quantity_issued = $quantity;
        $this->issued_pack_type = $packType;
        $this->issued_pack_size = $packSize;
        $this->issued_total_pieces = $this->calculateIssuedTotalPieces();
        $this->save();

        return $this;
    }

    // Scope filters
    public function scopeWithRemainingStock($query)
    {
        return $query->whereRaw('issued_total_pieces - COALESCE(quantity_consumed, 0) - COALESCE(quantity_returned, 0) - COALESCE(quantity_sold, 0) > 0');
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeApproved($query)
    {
        return $query->where('quantity_approved', '>', 0);
    }


    // In app/Models/DepartmentRequisitionItem.php
public function requisition()
{
    return $this->belongsTo(DepartmentRequisition::class, 'department_requisition_id');
}

}
