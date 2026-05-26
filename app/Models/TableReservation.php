<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TableReservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'table_reservations';

    protected $fillable = [
        'restaurant_table_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'reservation_date',
        'reservation_time',
        'duration_hours',
        'number_of_guests',
        'notes',
        'status',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'reservation_time' => 'datetime',
        'duration_hours' => 'integer',
        'number_of_guests' => 'integer',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_SEATED = 'seated';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';

    /**
     * All available statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_SEATED => 'Seated',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_NO_SHOW => 'No Show',
        ];
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            self::STATUS_CONFIRMED => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Confirmed</span>',
            self::STATUS_SEATED => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Seated</span>',
            self::STATUS_COMPLETED => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Completed</span>',
            self::STATUS_CANCELLED => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Cancelled</span>',
            self::STATUS_NO_SHOW => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">No Show</span>',
        ];

        return $badges[$this->status] ?? '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Unknown</span>';
    }

    /**
     * Scope for pending reservations
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for confirmed reservations
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope for active reservations (not cancelled or completed)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_SEATED]);
    }

    /**
     * Scope for today's reservations
     */
    public function scopeToday($query)
    {
        return $query->where('reservation_date', date('Y-m-d'));
    }

    /**
     * Scope for upcoming reservations
     */
    public function scopeUpcoming($query)
    {
        return $query->where('reservation_date', '>=', date('Y-m-d'))
            ->where('status', '!=', self::STATUS_CANCELLED)
            ->where('status', '!=', self::STATUS_COMPLETED);
    }

    /**
     * Scope for reservations by date range
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('reservation_date', [$startDate, $endDate]);
    }

    /**
     * Relationship: The table being reserved
     */
    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'restaurant_table_id');
    }

    /**
     * Relationship: User who created this reservation
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: User who last updated this reservation
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship: User who cancelled this reservation
     */
    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Check if reservation can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    /**
     * Check if reservation can be confirmed
     */
    public function canBeConfirmed()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if reservation can be marked as seated
     */
    public function canBeSeated()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Check if reservation can be completed
     */
    public function canBeCompleted()
    {
        return $this->status === self::STATUS_SEATED;
    }

    /**
     * Get reservation time formatted
     */
    public function getFormattedTimeAttribute()
    {
        return date('h:i A', strtotime($this->reservation_time));
    }

    /**
     * Get reservation date formatted
     */
    public function getFormattedDateAttribute()
    {
        return date('M d, Y', strtotime($this->reservation_date));
    }
}
