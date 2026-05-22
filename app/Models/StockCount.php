<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockCount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_counts';

    protected $fillable = [
        'count_number',
        'location_type',
        'location_id',
        'count_date',
        'status',
        'created_by',
        'completed_by',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'count_date' => 'date',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Location type constants
    const LOCATION_STORE = 'store';
    const LOCATION_DEPARTMENT = 'department';

    // Relationships
    public function items()
    {
        return $this->hasMany(StockCountItem::class, 'stock_count_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // Polymorphic location (store or department)
    public function location()
    {
        if ($this->location_type === self::LOCATION_STORE) {
            return $this->belongsTo(Store::class, 'location_id');
        }
        return $this->belongsTo(Department::class, 'location_id');
    }

    // Helper methods - Calculations
    public function getTotalSystemQuantityAttribute()
    {
        return $this->items->sum('system_quantity');
    }

    public function getTotalPhysicalQuantityAttribute()
    {
        return $this->items->sum('physical_quantity');
    }

    public function getTotalNetQuantityAttribute()
    {
        return $this->items->sum(function($item) {
            return $item->net_quantity;
        });
    }

    public function getTotalVarianceAttribute()
    {
        return $this->items->sum('variance');
    }

    public function getTotalVarianceValueAttribute()
    {
        return $this->items->sum('variance_value');
    }

    // Helper methods - Status checks
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isInProgress()
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    // Helper methods - Variance checks
    public function hasVariances()
    {
        return $this->items->where('variance', '!=', 0)->count() > 0;
    }

    public function hasNegativeVariances()
    {
        return $this->items->where('variance', '<', 0)->count() > 0;
    }

    public function hasPositiveVariances()
    {
        return $this->items->where('variance', '>', 0)->count() > 0;
    }

    public function hasUnapprovedVariances()
    {
        return $this->items->filter(function($item) {
            return $item->hasVariance() && !$item->isApproved();
        })->count() > 0;
    }

    // Helper method - Generate unique count number
    public static function generateCountNumber()
    {
        $prefix = 'CNT-' . date('Ymd') . '-';
        $lastCount = self::where('count_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastCount) {
            $lastNumber = intval(substr($lastCount->count_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    // Scopes
    public function scopeForStore($query)
    {
        return $query->where('location_type', self::LOCATION_STORE);
    }

    public function scopeForDepartment($query)
    {
        return $query->where('location_type', self::LOCATION_DEPARTMENT);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
