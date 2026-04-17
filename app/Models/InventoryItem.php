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
        'is_active',
        'is_perishable',
        'is_taxable',
        'shelf_life_days',
        'storage_conditions',
        'manufacturer',
        'brand',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'minimum_stock' => 'decimal:6',
            'maximum_stock' => 'decimal:6',
            'reorder_quantity' => 'decimal:6',
            'unit_cost' => 'decimal:2',
            'last_purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'current_stock' => 'decimal:6',
            'is_active' => 'boolean',
            'is_perishable' => 'boolean',
            'is_taxable' => 'boolean',
            'shelf_life_days' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePerishable($query)
    {
        return $query->where('is_perishable', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    public function needsReorder(): bool
    {
        return ($this->current_stock ?? 0) <= ($this->minimum_stock ?? 0);
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function unitOfMeasure()
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

    public function units()
    {
        return $this->hasMany(ItemUnit::class, 'inventory_item_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'inventory_item_id');
    }

    public function goodsReceivedNoteItems()
    {
        return $this->hasMany(GoodsReceivedNoteItem::class, 'inventory_item_id');
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
}