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
        'qauntity_sold',

        'return_reason',
        'returned_at',
        'quantity_consumed',
        'metrics',
        'notes',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:2',
        'department_id' => 'integer',
        'requested_pack_size' => 'integer',
        'quantity_issued' => 'decimal:2',
        'issued_pack_size' => 'integer',
        'issued_total_pieces' => 'decimal:2',
        'quantity_returned' => 'decimal:2',
        'returned_pack_size' => 'integer',
        'returned_total_pieces' => 'decimal:2',
        'quantity_consumed' => 'decimal:2',
        'returned_at' => 'datetime',
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

    // Helper methods
    public function getRemainingToIssueAttribute()
    {
        return $this->quantity_requested - $this->quantity_issued;
    }

    public function getRemainingToReturnAttribute()
    {
        return $this->issued_total_pieces - ($this->returned_total_pieces + $this->quantity_consumed);
    }

    public function getIsFullyIssuedAttribute()
    {
        return $this->quantity_issued >= $this->quantity_requested;
    }

    public function getIsFullyReturnedAttribute()
    {
        $remaining = $this->issued_total_pieces - ($this->returned_total_pieces + $this->quantity_consumed);
        return $remaining <= 0;
    }

    // Calculate issued total pieces
    public function calculateIssuedTotalPieces()
    {
        if ($this->issued_pack_type && $this->issued_pack_size) {
            return $this->quantity_issued * $this->issued_pack_size;
        }
        return $this->quantity_issued;
    }

    // Calculate returned total pieces
    public function calculateReturnedTotalPieces()
    {
        if ($this->returned_pack_type && $this->returned_pack_size) {
            return $this->quantity_returned * $this->returned_pack_size;
        }
        return $this->quantity_returned;
    }

    // Update consumed quantity
    public function updateConsumedQuantity($consumed)
    {
        $this->quantity_consumed = $consumed;
        $this->save();
    }

    // Record return
    public function recordReturn($quantity, $packType = null, $packSize = null, $reason = null)
    {
        $this->quantity_returned = $quantity;
        $this->returned_pack_type = $packType;
        $this->returned_pack_size = $packSize;
        $this->returned_total_pieces = $this->calculateReturnedTotalPieces();
        $this->return_reason = $reason;
        $this->returned_at = now();
        $this->save();
    }
}
