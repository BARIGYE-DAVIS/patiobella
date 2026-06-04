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
        'empty_bottle_weight',
        'default_unit_of_measure_id',  // The receiving/pack unit  e.g. carton, kg, pcs
        'base_unit',                   // The individual sell/consume unit e.g. bottle, piece, kg
        'minimum_stock',               // Always in base units
        'maximum_stock',               // Always in base units
        'reorder_quantity',            // Always in base units
        'unit_cost',                   // Cost per base unit
        'last_purchase_price',         // Last cost per base unit
        'selling_price',               // Selling price per base unit (set by sales management)
        'is_sellable',              // Whether this item is sold directly to customers (e.g. water, soda, beer)
        'current_stock',               // Always in base units
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
        'minimum_stock'      => 'decimal:6',
        'empty_bottle_weight' => 'decimal:6',
        'maximum_stock'      => 'decimal:6',
        'reorder_quantity'   => 'decimal:6',
        'unit_cost'          => 'decimal:2',
        'last_purchase_price'=> 'decimal:2',
        'selling_price'      => 'decimal:2',
        'is_sellable'        => 'boolean',
        'current_stock'      => 'decimal:6',
        'is_perishable'      => 'boolean',
        'is_taxable'         => 'boolean',
        'is_active'          => 'boolean',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
        'deleted_at'         => 'datetime',
    ];

    // ─────────────────────────────────────────────────────────────────────────────
    // Pack metrics that contain multiple base units inside
    // ─────────────────────────────────────────────────────────────────────────────

    const BULK_METRICS = ['box', 'carton', 'crate', 'dozen', 'pack', 'sack', 'set'];

    const SIMPLE_METRICS = ['kg', 'litres', 'pcs', 'grams', 'millilitres'];

    // ─────────────────────────────────────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    /**
     * The receiving/pack unit (carton, crate, kg, pcs, etc.)
     * This is what appears on purchase orders and GRNs.
     */
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

    // ─────────────────────────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────────────────────────

    /** Only active items */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Items whose current_stock (in base units) is at or below minimum_stock.
     * Because both columns are in base units, this comparison is always correct.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock')
                     ->where('minimum_stock', '>', 0);
    }

    /** Items with zero stock */
    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Whether this item uses a bulk/pack receiving unit.
     * True  → received in cartons, crates, boxes etc.
     * False → received directly in base units (kg, litres, pcs etc.)
     */
    public function getIsBulkItemAttribute(): bool
    {
        return in_array($this->default_unit_of_measure_id, self::BULK_METRICS);
    }

    /**
     * The label to show for the base/selling unit.
     * Falls back to the receiving unit if base_unit was never set.
     */
    public function getBaseUnitLabelAttribute(): string
    {
        return $this->base_unit ?? $this->default_unit_of_measure_id ?? 'unit';
    }

    /**
     * Human-readable stock status.
     * All comparisons in base units — always accurate.
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        }
        if ($this->minimum_stock > 0 && $this->current_stock <= $this->minimum_stock) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    /**
     * Tailwind/CSS colour key for stock status badges.
     */
    public function getStockStatusColorAttribute(): string
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'red',
            'low_stock'    => 'yellow',
            default        => 'green',
        };
    }

    /**
     * Formatted stock display string.
     * Examples:
     *   "72 bottles"
     *   "300 kg"
     *   "0 pieces"
     */
    public function getStockDisplayAttribute(): string
    {
        $qty   = number_format((float) $this->current_stock, 2);
        $unit  = $this->base_unit_label;
        return "{$qty} {$unit}(s)";
    }

    /**
     * Formatted receiving unit display string.
     * Only meaningful for bulk items.
     * Example: "received as carton"
     */
    public function getReceivingUnitDisplayAttribute(): string
    {
        if ($this->is_bulk_item) {
            return "received as {$this->default_unit_of_measure_id}";
        }
        return $this->base_unit_label;
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // Helper methods
    // ─────────────────────────────────────────────────────────────────────────────

    /**
     * Convert a pack quantity into base units.
     *
     * Use this anywhere you need to know how many base units
     * a given number of packs represents.
     *
     * @param  float $numberOfPacks  Number of packs (cartons, crates, etc.)
     * @param  int   $packSize       Items per pack for this specific delivery
     * @return float                 Total base units
     */
    public function toBaseUnits(float $numberOfPacks, int $packSize): float
    {
        if (!$this->is_bulk_item || $packSize <= 0) {
            return $numberOfPacks;
        }
        return $numberOfPacks * $packSize;
    }

    /**
     * Check whether the item has enough stock to fulfill a request.
     * The $quantity requested must always be in base units.
     *
     * @param  float $quantity  Quantity in base units
     * @return bool
     */
    public function hasEnoughStock(float $quantity): bool
    {
        return $this->current_stock >= $quantity;
    }

    /**
     * Deduct stock in base units and save.
     * Throws an exception if stock is insufficient.
     *
     * @param  float  $quantity   Base units to deduct
     * @param  string $context    For logging (e.g. 'requisition', 'sale')
     * @return float              New stock level
     * @throws \Exception
     */
    public function deductStock(float $quantity, string $context = 'deduction'): float
    {
        if (!$this->hasEnoughStock($quantity)) {
            throw new \Exception(
                "Insufficient stock for {$this->name}. " .
                "Available: {$this->current_stock} {$this->base_unit_label}(s). " .
                "Requested: {$quantity} {$this->base_unit_label}(s)."
            );
        }

        $this->current_stock = $this->current_stock - $quantity;
        $this->save();

        return (float) $this->current_stock;
    }

    /**
     * Add stock in base units and save.
     *
     * @param  float $quantity  Base units to add
     * @return float            New stock level
     */
    public function addStock(float $quantity): float
    {
        $this->current_stock = $this->current_stock + $quantity;
        $this->save();

        return (float) $this->current_stock;
    }


    // In app/Models/InventoryItem.php
public function departmentRequisitionItems()
{
    return $this->hasMany(DepartmentRequisitionItem::class, 'inventory_item_id');
}
 // Helper method to get empty bottle weight in kg (standardized)
    public function getEmptyBottleWeightInKgAttribute()
    {
        return (float) $this->empty_bottle_weight;
    }

 public function hasEmptyBottleWeight()
    {
        return $this->empty_bottle_weight > 0;
    }

}
