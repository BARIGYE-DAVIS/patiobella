<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tickets';

    protected $fillable = [
        'ticket_number',
        'ticket_type',
        'sales_order_id',
        'table_number',
        'waiter_name',
        'comments',
        'supplement',
        'items',
        'is_printed',
        'printed_at',
        'created_by',
    ];

    protected $casts = [
        'items' => 'array',
        'is_printed' => 'boolean',
        'printed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Ticket type constants
     */
    const TYPE_KITCHEN = 'kitchen';
    const TYPE_BAR = 'bar';
    const TYPE_CAFE = 'cafe';

    /**
     * Get all ticket types
     */
    public static function getTypes()
    {
        return [
            self::TYPE_KITCHEN => 'Kitchen (KOT)',
            self::TYPE_BAR => 'Bar (BOT)',
            self::TYPE_CAFE => 'Cafe (COT)',
        ];
    }

    /**
     * Get ticket type badge HTML
     */
    public function getTypeBadgeAttribute()
    {
        $badges = [
            self::TYPE_KITCHEN => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">🍳 KOT - Kitchen</span>',
            self::TYPE_BAR => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">🍸 BOT - Bar</span>',
            self::TYPE_CAFE => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">☕ COT - Cafe</span>',
        ];

        return $badges[$this->ticket_type] ?? '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Unknown</span>';
    }

    /**
     * Get printed status badge
     */
    public function getPrintedBadgeAttribute()
    {
        if ($this->is_printed) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">✓ Printed</span>';
        }
        return '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">⏳ Pending</span>';
    }

    /**
     * Relationship: Sales Order
     */
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    /**
     * Relationship: User who created the ticket
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Mark ticket as printed
     */
    public function markAsPrinted()
    {
        $this->update([
            'is_printed' => true,
            'printed_at' => now(),
        ]);
    }

    /**
     * Get items as array
     */
    public function getItemsArray()
    {
        return is_array($this->items) ? $this->items : json_decode($this->items, true);
    }

    /**
     * Scope for unprinted tickets
     */
    public function scopeUnprinted($query)
    {
        return $query->where('is_printed', false);
    }

    /**
     * Scope for printed tickets
     */
    public function scopePrinted($query)
    {
        return $query->where('is_printed', true);
    }

    /**
     * Scope by ticket type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('ticket_type', $type);
    }
}
