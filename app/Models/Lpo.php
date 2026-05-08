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
        'requisition_id',
        'vendor_id',
        'created_by',
        'approved_by',
        'director_notes',
        'lpo_date',
        'expected_delivery_date',
        'delivery_address',
        'delivery_terms',
        'subtotal',
        'tax_amount',
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
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

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
