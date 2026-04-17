<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_code',
        'name',
        'contact_person',
        'email',
        'phone',
        'alternative_phone',
        'address',
        'city',
        'country',
        'tax_id',
        'payment_method',
        'credit_limit',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->address, $this->city, $this->country]);
        return implode(', ', $parts);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByPaymentMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'vendor_id');
    }

    public function goodsReceivedNotes()
    {
        return $this->hasMany(GoodsReceivedNote::class, 'vendor_id');
    }
}