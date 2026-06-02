<?php
// app/Models/DailySale.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySale extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'department_id',
        'sale_date',
        'quantity_sold',
        'unit_price',
        'total_amount',
        'calculated_cogs',
        'calculated_profit',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'quantity_sold' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'calculated_cogs' => 'decimal:2',
        'calculated_profit' => 'decimal:2',
    ];

    // Relationships
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Calculate total amount before saving
    protected static function booted()
    {
        static::saving(function ($dailySale) {
            $dailySale->total_amount = $dailySale->quantity_sold * $dailySale->unit_price;
        });
    }

    // Recalculate COGS and profit from recipe
    public function recalculateFromRecipe()
    {
        $totalCogs = 0;
        $menuItem = $this->menuItem;

        if ($menuItem && $menuItem->recipeItems) {
            foreach ($menuItem->recipeItems as $recipeItem) {
                // Get the ingredient cost from batches (FIFO)
                $ingredientCost = $this->getIngredientCost($recipeItem->inventory_item_id, $recipeItem->quantity_required);
                $totalCogs += $ingredientCost;
            }
        }

        $this->calculated_cogs = $totalCogs * $this->quantity_sold;
        $this->calculated_profit = $this->total_amount - $this->calculated_cogs;

        return $this;
    }

    protected function getIngredientCost($inventoryItemId, $quantityRequired)
    {
        // Get oldest active batch (FIFO)
        $batch = Batch::where('inventory_item_id', $inventoryItemId)
            ->where('batch_status', 'active')
            ->where('remaining_quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($batch) {
            return $quantityRequired * $batch->unit_cost;
        }

        return 0;
    }
}
