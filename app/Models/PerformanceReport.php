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
}
