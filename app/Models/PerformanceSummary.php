<?php
// app/Models/PerformanceSummary.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceSummary extends Model
{
    use HasFactory;

    protected $table = 'performance_summaries';

    protected $fillable = [
        'department_id',
        'report_date',
        'period_type',
        'period_start',
        'period_end',
        'total_quantity_sold',
        'total_sales_amount',
        'total_cogs',
        'total_profit',
        'cogs_percentage',
        'profit_margin',
        'status',
        'notes',
    ];

    protected $casts = [
        'report_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'total_quantity_sold' => 'decimal:2',
        'total_sales_amount' => 'decimal:2',
        'total_cogs' => 'decimal:2',
        'total_profit' => 'decimal:2',
        'cogs_percentage' => 'decimal:2',
        'profit_margin' => 'decimal:2',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function topItems()
    {
        return $this->hasMany(PerformanceItem::class)->where('ranking_type', 'top')->orderBy('rank_position', 'asc');
    }

    public function bottomItems()
    {
        return $this->hasMany(PerformanceItem::class)->where('ranking_type', 'bottom')->orderBy('rank_position', 'asc');
    }

    public function allItems()
    {
        return $this->hasMany(PerformanceItem::class);
    }

    // Calculate percentages automatically
    public function calculatePercentages()
    {
        if ($this->total_sales_amount > 0) {
            $this->cogs_percentage = ($this->total_cogs / $this->total_sales_amount) * 100;
            $this->profit_margin = ($this->total_profit / $this->total_sales_amount) * 100;
        } else {
            $this->cogs_percentage = 0;
            $this->profit_margin = 0;
        }

        return $this;
    }

    // Generate summary from daily sales
    public static function generateFromDailySales($departmentId, $startDate, $endDate, $periodType = 'daily')
    {
        $dailySales = DailySale::where('department_id', $departmentId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->get();

        $summary = new static();
        $summary->department_id = $departmentId;
        $summary->report_date = now();
        $summary->period_type = $periodType;
        $summary->period_start = $startDate;
        $summary->period_end = $endDate;
        $summary->total_quantity_sold = $dailySales->sum('quantity_sold');
        $summary->total_sales_amount = $dailySales->sum('total_amount');
        $summary->total_cogs = $dailySales->sum('calculated_cogs');
        $summary->total_profit = $dailySales->sum('calculated_profit');
        $summary->calculatePercentages();
        $summary->status = 'draft';

        return $summary;
    }

    // Generate top and bottom selling items
    public function generateTopBottomItems($limit = 10)
    {
        // Group daily sales by menu item
        $salesByItem = DailySale::where('department_id', $this->department_id)
            ->whereBetween('sale_date', [$this->period_start, $this->period_end])
            ->selectRaw('menu_item_id,
                         SUM(quantity_sold) as total_quantity,
                         SUM(total_amount) as total_sales,
                         SUM(calculated_cogs) as total_cogs,
                         SUM(calculated_profit) as total_profit')
            ->groupBy('menu_item_id')
            ->get();

        $totalSales = $this->total_sales_amount;

        foreach ($salesByItem as $item) {
            $menuItem = MenuItem::find($item->menu_item_id);
            if ($menuItem) {
                $percentage = $totalSales > 0 ? ($item->total_sales / $totalSales) * 100 : 0;
                $profitMargin = $item->total_sales > 0 ? ($item->total_profit / $item->total_sales) * 100 : 0;

                PerformanceItem::updateOrCreate(
                    [
                        'performance_summary_id' => $this->id,
                        'menu_item_id' => $item->menu_item_id,
                    ],
                    [
                        'quantity_sold' => $item->total_quantity,
                        'sales_amount' => $item->total_sales,
                        'percentage_of_total_sales' => $percentage,
                        'cogs' => $item->total_cogs,
                        'profit' => $item->total_profit,
                        'profit_margin' => $profitMargin,
                    ]
                );
            }
        }

        // Set rankings for top items
        $topItems = PerformanceItem::where('performance_summary_id', $this->id)
            ->orderBy('sales_amount', 'desc')
            ->limit($limit)
            ->get();

        foreach ($topItems as $index => $item) {
            $item->ranking_type = 'top';
            $item->rank_position = $index + 1;
            $item->save();
        }

        // Set rankings for bottom items
        $bottomItems = PerformanceItem::where('performance_summary_id', $this->id)
            ->orderBy('sales_amount', 'asc')
            ->limit($limit)
            ->get();

        foreach ($bottomItems as $index => $item) {
            $item->ranking_type = 'bottom';
            $item->rank_position = $index + 1;
            $item->save();
        }

        return $this;
    }
}
