<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovementType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'sign',
        'description',
        'requires_approval',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Movement sign constants
    const SIGN_IN = '+';
    const SIGN_OUT = '-';

    // Predefined movement type codes
    const TYPE_PURCHASE = 'PURCHASE';
    const TYPE_GRN = 'GRN';
    const TYPE_RETURN_TO_VENDOR = 'RETURN_TO_VENDOR';
    const TYPE_ISSUE = 'ISSUE';
    const TYPE_TRANSFER_IN = 'TRANSFER_IN';
    const TYPE_TRANSFER_OUT = 'TRANSFER_OUT';
    const TYPE_ADJUSTMENT_IN = 'ADJUSTMENT_IN';
    const TYPE_ADJUSTMENT_OUT = 'ADJUSTMENT_OUT';
    const TYPE_WASTE = 'WASTE';
    const TYPE_RETURN_TO_STORE = 'RETURN_TO_STORE';

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInbound($query)
    {
        return $query->where('sign', self::SIGN_IN);
    }

    public function scopeOutbound($query)
    {
        return $query->where('sign', self::SIGN_OUT);
    }

    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Helper methods
    public function isInbound(): bool
    {
        return $this->sign === self::SIGN_IN;
    }

    public function isOutbound(): bool
    {
        return $this->sign === self::SIGN_OUT;
    }

    public function needsApproval(): bool
    {
        return $this->requires_approval;
    }

    // Relationships
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'movement_type_id');
    }
}