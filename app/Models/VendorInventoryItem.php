<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorInventoryItem extends Model
{
    use HasFactory;

    protected $table = 'vendor_inventory_items';

    protected $fillable = [
        'vendor_id',
        'inventory_item_id',
        'last_purchase_price',
        'average_purchase_price',
        'is_preferred',
        'lead_time_days',
        'notes',
    ];

    protected $casts = [
        'last_purchase_price' => 'decimal:2',
        'average_purchase_price' => 'decimal:2',
        'is_preferred' => 'boolean',
        'lead_time_days' => 'integer',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
