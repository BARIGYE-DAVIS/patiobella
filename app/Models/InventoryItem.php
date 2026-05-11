<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_items';

    protected $fillable = [
        'item_code',
        'barcode',
        'name',
        'description',
        'category_id',
        'sub_category_id',
        'default_unit_of_measure_id',
        'minimum_stock',
        'maximum_stock',
        'reorder_quantity',
        'unit_cost',
        'last_purchase_price',
        'selling_price',
        'current_stock',
        'is_perishable',
        'is_taxable',
        'shelf_life_days',
        'storage_conditions',
        'manufacturer',
        'brand',
        'notes',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'minimum_stock' => 'decimal:6',
        'maximum_stock' => 'decimal:6',
        'reorder_quantity' => 'decimal:6',
        'unit_cost' => 'decimal:2',
        'last_purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'current_stock' => 'decimal:6',
        'is_perishable' => 'boolean',
        'is_taxable' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function defaultUnitOfMeasure()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'default_unit_of_measure_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'inventory_item_id');
    }

    public function stockBalances()
    {
        return $this->hasMany(StockBalance::class, 'inventory_item_id');
    }

    public function requisitionItems()
    {
        return $this->hasMany(RequisitionItem::class, 'inventory_item_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'inventory_item_id');
    }

    public function goodsReceivedItems()
    {
        return $this->hasMany(GoodsReceivedNoteItem::class, 'inventory_item_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    // Accessor for stock status
    public function getStockStatusAttribute()
    {
        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        }
        if ($this->minimum_stock && $this->current_stock <= $this->minimum_stock) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    // Accessor for stock status color
    public function getStockStatusColorAttribute()
    {
        switch ($this->stock_status) {
            case 'out_of_stock':
                return 'red';
            case 'low_stock':
                return 'yellow';
            default:
                return 'green';
        }
    }
}
