<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lpo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lpo_number',
        'type',
        'requisition_id',
        'vendor_id',
        'created_by',
        'approved_by',
        'director_notes',
        'lpo_date',
        'expected_delivery_date',
        'delivery_address',
        'delivery_instructions',
        'payment_method',
        'subtotal',
        'vat_rate',
        'vat_amount',
        'total_amount',
        'status',
        'notes',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'lpo_date' => 'date',
        'expected_delivery_date' => 'date',
        'approved_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Constants
    const TYPE_NORMAL = 'normal';
    const TYPE_EMERGENCY = 'emergency';

    const PAYMENT_CASH = 'cash';
    const PAYMENT_CREDIT = 'credit';
    const PAYMENT_BANK_TRANSFER = 'bank_transfer';
    const PAYMENT_MOBILE_MONEY = 'mobile_money';
    const PAYMENT_CHEQUE = 'cheque';

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(LpoItem::class);
    }
}
