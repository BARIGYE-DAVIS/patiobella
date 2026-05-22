<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockVarianceReason extends Model
{
    use HasFactory;

    protected $table = 'stock_variance_reasons';

    protected $fillable = [
        'code',
        'name',
        'description',
        'requires_approval',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true);
    }

    public function scopeAutoApproved($query)
    {
        return $query->where('requires_approval', false);
    }

    public static function getReasonOptions()
    {
        return self::active()
            ->orderBy('sort_order')
            ->get()
            ->pluck('name', 'code');
    }

    public function needsApproval()
    {
        return $this->requires_approval;
    }
}
