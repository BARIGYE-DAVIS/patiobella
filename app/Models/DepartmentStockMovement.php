<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentStockMovement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'department_stock_movements';

    protected $fillable = [
        'movement_number',
        'department_id',
        'inventory_item_id',
        'batch_id',
        'requisition_item_id',
        'opening_balance',
        'added_quantity',
        'used_quantity',
        'returned_quantity',
        'closing_balance',
        'movement_type',
        'movement_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:6',
        'added_quantity' => 'decimal:6',
        'used_quantity' => 'decimal:6',
        'returned_quantity' => 'decimal:6',
        'closing_balance' => 'decimal:6',
        'movement_date' => 'date',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function requisitionItem()
    {
        return $this->belongsTo(DepartmentRequisitionItem::class, 'requisition_item_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Generate unique movement number
    public static function generateMovementNumber()
    {
        $last = self::orderBy('id', 'desc')->first();
        if ($last) {
            $num = intval(substr($last->movement_number, -4)) + 1;
            $newNum = str_pad($num, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }
        return 'DEPT-STK-' . date('Ymd') . '-' . $newNum;
    }

    // Get current balance for a department and item
    public static function getCurrentBalance($departmentId, $inventoryItemId)
    {
        $last = self::where('department_id', $departmentId)
            ->where('inventory_item_id', $inventoryItemId)
            ->orderBy('movement_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $last ? $last->closing_balance : 0;
    }
}
