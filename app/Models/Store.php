<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'location',
        'store_type_id',
        'manager_id',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relationships
    public function storeType()
    {
        return $this->belongsTo(StoreType::class, 'store_type_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

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
        return $this->hasMany(PurchaseOrder::class, 'store_id');
    }

    public function goodsReceivedNotes()
    {
        return $this->hasMany(GoodsReceivedNote::class, 'store_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'store_id');
    }

    public function stockBalances()
    {
        return $this->hasMany(StockBalance::class, 'store_id');
    }
}