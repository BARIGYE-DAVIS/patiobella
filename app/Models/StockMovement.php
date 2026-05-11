<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    'movement_number',
    'inventory_item_id',
    'store_id',
    'movement_type_id',
    'quantity',
    'pack_type',
    'pack_size',
    'number_of_packs',
    'base_unit',
    'unit_id',
    'quantity_in_base_unit',
    'unit_cost',
    'total_value',
    'reason',
    'movement_date',
    'approved_at',
    'approved_by',
    'purchase_order_id',
    'goods_received_note_id',
    'is_reversed',
    'reversed_by_movement_id',
    'created_by',
    'updated_by',
];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:6',
            'quantity_in_base_unit' => 'decimal:6',
            'unit_cost' => 'decimal:2',
            'total_value' => 'decimal:2',
            'movement_date' => 'date',
            'approved_at' => 'datetime',
            'is_reversed' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Scopes
    public function scopeInbound($query)
    {
        return $query->whereHas('movementType', function ($q) {
            $q->where('sign', '+');
        });
    }

    public function scopeOutbound($query)
    {
        return $query->whereHas('movementType', function ($q) {
            $q->where('sign', '-');
        });
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeByItem($query, $itemId)
    {
        return $query->where('inventory_item_id', $itemId);
    }

    // Helper methods
    public function isInbound(): bool
    {
        return $this->movementType && $this->movementType->sign === '+';
    }

    public function isOutbound(): bool
    {
        return $this->movementType && $this->movementType->sign === '-';
    }

    public function isApproved(): bool
    {
        return !is_null($this->approved_at);
    }

    public function isReversed(): bool
    {
        return $this->is_reversed;
    }

    public function canReverse(): bool
    {
        return !$this->is_reversed && $this->isApproved();
    }

    /**
     * Calculate total value if not set.
     */
    public function calculateTotalValue(): float
    {
        return $this->quantity * $this->unit_cost;
    }

    /**
     * Calculate quantity in base unit.
     */
    public function calculateQuantityInBaseUnit(): float
    {
        if (!$this->unit) {
            return $this->quantity;
        }

        return $this->unit->toBaseUnit($this->quantity);
    }

    /**
     * Approve the stock movement.
     */
    public function approve(int $approvedByUserId): void
    {
        $this->approved_at = now();
        $this->approved_by = $approvedByUserId;
        $this->save();

        // Update stock balance after approval
        $this->updateStockBalance();
    }

    /**
     * Reverse this stock movement (create opposite movement).
     */
    public function reverse(int $createdByUserId): ?StockMovement
    {
        if (!$this->canReverse()) {
            throw new \Exception('Cannot reverse this stock movement');
        }

        // Determine reverse movement type
        $reverseSign = $this->isInbound() ? '-' : '+';
        $reverseTypeCode = $this->getReverseMovementTypeCode();

        $reverseType = StockMovementType::where('code', $reverseTypeCode)
            ->where('sign', $reverseSign)
            ->first();

        if (!$reverseType) {
            throw new \Exception('Reverse movement type not found');
        }

        // Generate reverse movement number
        $reverseNumber = 'REV-' . $this->movement_number;

        // Create reverse movement
        $reverseMovement = StockMovement::create([
            'movement_number' => $reverseNumber,
            'inventory_item_id' => $this->inventory_item_id,
            'store_id' => $this->store_id,
            'movement_type_id' => $reverseType->id,
            'department_id' => $this->department_id,
            'quantity' => $this->quantity,
            'unit_id' => $this->unit_id,
            'quantity_in_base_unit' => $this->quantity_in_base_unit,
            'unit_cost' => $this->unit_cost,
            'total_value' => $this->total_value,
            'reason' => 'Reversal of: ' . $this->movement_number,
            'movement_date' => now(),
            'approved_at' => now(),
            'approved_by' => $createdByUserId,
            'created_by' => $createdByUserId,
        ]);

        // Mark original as reversed
        $this->is_reversed = true;
        $this->reversed_by_movement_id = $reverseMovement->id;
        $this->save();

        // Update stock balance
        $reverseMovement->updateStockBalance();

        return $reverseMovement;
    }

    /**
     * Get reverse movement type code.
     */
    private function getReverseMovementTypeCode(): string
    {
        $typeCode = $this->movementType->code;

        $reverseMap = [
            'PURCHASE' => 'RETURN_TO_VENDOR',
            'GRN' => 'RETURN_TO_VENDOR',
            'ISSUE' => 'RETURN_TO_STORE',
            'TRANSFER_OUT' => 'TRANSFER_IN',
            'TRANSFER_IN' => 'TRANSFER_OUT',
            'ADJUSTMENT_IN' => 'ADJUSTMENT_OUT',
            'ADJUSTMENT_OUT' => 'ADJUSTMENT_IN',
            'WASTE' => 'ADJUSTMENT_IN',
        ];

        return $reverseMap[$typeCode] ?? 'ADJUSTMENT_OUT';
    }

    /**
     * Update stock balance for this movement.
     */
    public function updateStockBalance(): void
    {
        $balanceDate = $this->movement_date;
        $storeId = $this->store_id;
        $itemId = $this->inventory_item_id;

        // Get the latest balance before this movement date
        $latestBalance = StockBalance::where('inventory_item_id', $itemId)
            ->where('store_id', $storeId)
            ->where('balance_date', '<=', $balanceDate)
            ->orderBy('balance_date', 'desc')
            ->first();

        $currentQuantity = $latestBalance ? $latestBalance->quantity : 0;
        $currentAverageCost = $latestBalance ? $latestBalance->average_cost : 0;
        $currentTotalValue = $currentQuantity * $currentAverageCost;

        // Calculate new quantity based on movement sign
        if ($this->isInbound()) {
            $newQuantity = $currentQuantity + $this->quantity_in_base_unit;
            $newTotalValue = $currentTotalValue + $this->total_value;
        } else {
            $newQuantity = $currentQuantity - $this->quantity_in_base_unit;
            $newTotalValue = $currentTotalValue - $this->total_value;
        }

        $newAverageCost = $newQuantity > 0 ? $newTotalValue / $newQuantity : 0;

        // Create or update balance for this date
        StockBalance::updateOrCreate(
            [
                'inventory_item_id' => $itemId,
                'store_id' => $storeId,
                'balance_date' => $balanceDate,
            ],
            [
                'quantity' => $newQuantity,
                'average_cost' => $newAverageCost,
            ]
        );
    }

    // Relationships
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function movementType()
    {
        return $this->belongsTo(StockMovementType::class, 'movement_type_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function unit()
    {
        return $this->belongsTo(ItemUnit::class, 'unit_id');
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

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function goodsReceivedNote()
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'goods_received_note_id');
    }

    public function reversedByMovement()
    {
        return $this->belongsTo(StockMovement::class, 'reversed_by_movement_id');
    }

    public function reversedMovements()
    {
        return $this->hasMany(StockMovement::class, 'reversed_by_movement_id');
    }
}
