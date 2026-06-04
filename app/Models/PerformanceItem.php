<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceItem extends Model
{
    use HasFactory;

    protected $table = 'performance_items';

    protected $fillable = [
        'performance_report_id',
        'menu_item_id',
        'inventory_item_id',
        'quantity_required',
        'quantity_sold',
        'opening_quantity',
        'added_quantity',
        'closing_quantity',
        'used_quantity',
        'opening_stock',
        'closing_stock',
        'unit_cost',
        'selling_price',
        'cogs',
        'sales_amount',
        'profit',
        'profit_margin',
    ];

    protected $casts = [
        'quantity_required' => 'decimal:4',
        'quantity_sold' => 'decimal:2',
        'opening_quantity' => 'decimal:2',
        'added_quantity' => 'decimal:2',
        'closing_quantity' => 'decimal:2',
        'used_quantity' => 'decimal:2',
        'opening_stock' => 'decimal:2',
        'closing_stock' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'cogs' => 'decimal:2',
        'sales_amount' => 'decimal:2',
        'profit' => 'decimal:2',
        'profit_margin' => 'decimal:2',
    ];

    // Relationships
    public function report()
    {
        return $this->belongsTo(PerformanceReport::class, 'performance_report_id');
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    // Accessors
    public function getFormattedUnitCostAttribute()
    {
        return number_format($this->unit_cost, 0) . ' UGX';
    }

    public function getFormattedSellingPriceAttribute()
    {
        return number_format($this->selling_price, 0) . ' UGX';
    }

    public function getFormattedCogsAttribute()
    {
        return number_format($this->cogs, 0) . ' UGX';
    }

    public function getFormattedSalesAmountAttribute()
    {
        return number_format($this->sales_amount, 0) . ' UGX';
    }

    public function getFormattedProfitAttribute()
    {
        return number_format($this->profit, 0) . ' UGX';
    }

    public function getProfitMarginFormattedAttribute()
    {
        return number_format($this->profit_margin, 2) . '%';
    }
}
