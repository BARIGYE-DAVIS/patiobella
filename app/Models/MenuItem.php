<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'menu_items';

    protected $fillable = [
        'name',
        'description',
        'category',
        'menu_id',
        'menu_item_category_id',
        'selling_price',
        'preparation_time',
        'is_active',
        'inventory_item_id',
        'allergen_info',
        'image_url',
        'sort_order',
        'notes',
        'material_cost',
        'labour_cost',
        'overhead_cost',
        'total_cost',
        'target_margin_percentage',
        'current_margin_percentage',
        'last_costed_at',
        'm_cost',
        'vat',
        'mark_up',
        'age_margins',
        'age_cost',
        'discount',
        'glovo_selling_price',
        'glovo_commission',
        'final_margin',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'vat_rate',
        'vat_amount',
        'vat_inclusive',
        'net_price',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'material_cost' => 'decimal:2',
        'labour_cost' => 'decimal:2',
        'overhead_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'target_margin_percentage' => 'decimal:2',
        'current_margin_percentage' => 'decimal:2',
        'm_cost' => 'decimal:2',
        'vat' => 'decimal:2',
        'mark_up' => 'decimal:2',
        'age_margins' => 'decimal:2',
        'age_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'glovo_selling_price' => 'decimal:2',
        'glovo_commission' => 'decimal:2',
        'final_margin' => 'decimal:2',
        'is_active' => 'boolean',
        'preparation_time' => 'integer',
        'sort_order' => 'integer',
        'last_costed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',

        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'vat_inclusive' => 'boolean',
        'net_price' => 'decimal:2',
    ];

    // =====================================================
    // Relationships
    // =====================================================

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function category()
    {
        return $this->belongsTo(MenuItemCategory::class, 'menu_item_category_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    /**
     * Get the recipe items (ingredients) for this menu item
     */
    public function recipeItems()
    {
        return $this->hasMany(RecipeItem::class, 'menu_item_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


    public function menuItemCategory()
{
    return $this->belongsTo(MenuItemCategory::class, 'menu_item_category_id');
}
    // =====================================================
    // Scopes
    // =====================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByMenu($query, $menuId)
    {
        return $query->where('menu_id', $menuId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('menu_item_category_id', $categoryId);
    }

    // =====================================================
    // Accessors
    // =====================================================

    public function getMaterialCostDisplayAttribute(): string
    {
        return number_format($this->material_cost ?? 0, 2) . ' UGX';
    }

    public function getSellingPriceDisplayAttribute(): string
    {
        return number_format($this->selling_price, 2) . ' UGX';
    }

    public function getProfitAttribute(): float
    {
        return ($this->selling_price - ($this->material_cost ?? 0));
    }

    public function getProfitDisplayAttribute(): string
    {
        return number_format($this->profit, 2) . ' UGX';
    }

    public function getMarginDisplayAttribute(): string
    {
        $margin = $this->current_margin_percentage ?? 0;
        $color = $margin >= 50 ? 'success' : ($margin >= 30 ? 'warning' : 'danger');
        return '<span class="badge bg-' . $color . '">' . number_format($margin, 2) . '%</span>';
    }
}
