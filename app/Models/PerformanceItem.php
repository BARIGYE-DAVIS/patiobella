<?php
// app/Models/PerformanceItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceItem extends Model
{
    use HasFactory;

    protected $table = 'performance_items';

    protected $fillable = [
        'performance_summary_id',
        'menu_item_id',
        'ranking_type',
        'rank_position',
        'quantity_sold',
        'sales_amount',
        'percentage_of_total_sales',
        'cogs',
        'profit',
        'profit_margin',
    ];

    protected $casts = [
        'quantity_sold' => 'decimal:2',
        'sales_amount' => 'decimal:2',
        'percentage_of_total_sales' => 'decimal:2',
        'cogs' => 'decimal:2',
        'profit' => 'decimal:2',
        'profit_margin' => 'decimal:2',
    ];

    // Relationships
    public function performanceSummary()
    {
        return $this->belongsTo(PerformanceSummary::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    // Accessors
    public function getProfitMarginFormattedAttribute()
    {
        return number_format($this->profit_margin, 2) . '%';
    }

    public function getSalesAmountFormattedAttribute()
    {
        return 'UGX ' . number_format($this->sales_amount, 2);
    }

    // Scope for top selling items
    public function scopeTopSelling($query, $limit = 10)
    {
        return $query->where('ranking_type', 'top')
            ->orderBy('rank_position', 'asc')
            ->limit($limit);
    }

    // Scope for bottom selling items
    public function scopeBottomSelling($query, $limit = 10)
    {
        return $query->where('ranking_type', 'bottom')
            ->orderBy('rank_position', 'asc')
            ->limit($limit);
    }
}
