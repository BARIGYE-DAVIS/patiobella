<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'performance_reports';

    protected $fillable = [
        'report_number',
        'department_id',
        'report_date',
        'total_sales',
        'total_cogs',
        'total_profit',
        'profit_margin',
        'gifts_amount',
        'sales_without_gifts',
        'profit_without_gifts',
        'profit_margin_without_gifts',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'report_date' => 'date',
        'total_sales' => 'decimal:2',
        'total_cogs' => 'decimal:2',
        'total_profit' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'gifts_amount' => 'decimal:2',
        'sales_without_gifts' => 'decimal:2',
        'profit_without_gifts' => 'decimal:2',
        'profit_margin_without_gifts' => 'decimal:2',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function items()
    {
        return $this->hasMany(PerformanceItem::class, 'performance_report_id');
    }

    // Accessors
    public function getFormattedTotalSalesAttribute()
    {
        return number_format($this->total_sales, 0) . ' UGX';
    }

    public function getFormattedTotalCogsAttribute()
    {
        return number_format($this->total_cogs, 0) . ' UGX';
    }

    public function getFormattedTotalProfitAttribute()
    {
        return number_format($this->total_profit, 0) . ' UGX';
    }

    public function getProfitMarginFormattedAttribute()
    {
        return number_format($this->profit_margin, 2) . '%';
    }

    // New Accessors for Gifts
    public function getFormattedGiftsAmountAttribute()
    {
        return number_format($this->gifts_amount ?? 0, 0) . ' UGX';
    }

    public function getFormattedSalesWithoutGiftsAttribute()
    {
        return number_format($this->sales_without_gifts ?? ($this->total_sales - ($this->gifts_amount ?? 0)), 0) . ' UGX';
    }

    public function getFormattedProfitWithoutGiftsAttribute()
    {
        return number_format($this->profit_without_gifts ?? (($this->total_sales - ($this->gifts_amount ?? 0)) - $this->total_cogs), 0) . ' UGX';
    }

    public function getProfitMarginWithoutGiftsFormattedAttribute()
    {
        return number_format($this->profit_margin_without_gifts ?? 0, 2) . '%';
    }

    // Helper method to get COGS percentage
    public function getCogsPercentageAttribute()
    {
        return $this->total_sales > 0 ? ($this->total_cogs / $this->total_sales) * 100 : 0;
    }

    public function getCogsPercentageWithoutGiftsAttribute()
    {
        $salesWithoutGifts = $this->sales_without_gifts ?? ($this->total_sales - ($this->gifts_amount ?? 0));
        return $salesWithoutGifts > 0 ? ($this->total_cogs / $salesWithoutGifts) * 100 : 0;
    }
}
