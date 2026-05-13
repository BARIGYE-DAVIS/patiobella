<?php
// app/Models/MenuItem.php

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
        'selling_price',
        'preparation_time',
        'is_active',
        'inventory_item_id',
        'allergen_info',
        'image_url',
        'sort_order',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
        'preparation_time' => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Categories constants
    const CATEGORY_APPETIZER = 'Appetizer';
    const CATEGORY_MAIN = 'Main';
    const CATEGORY_DESSERT = 'Dessert';
    const CATEGORY_BEVERAGE = 'Beverage';
    const CATEGORY_SIDE = 'Side';

    public static function getCategories()
    {
        return [
            self::CATEGORY_APPETIZER,
            self::CATEGORY_MAIN,
            self::CATEGORY_DESSERT,
            self::CATEGORY_BEVERAGE,
            self::CATEGORY_SIDE,
        ];
    }

    // Relationships
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return 'UGX ' . number_format($this->selling_price, 2);
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_active
            ? '<span class="badge-approved"><i class="fas fa-check-circle"></i> Active</span>'
            : '<span class="badge-pending"><i class="fas fa-ban"></i> Inactive</span>';
    }
}
