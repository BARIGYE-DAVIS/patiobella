<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantTable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'restaurant_tables';

    protected $fillable = [
        'table_number',
        'capacity',
        'size',
        'location',
        'description',
        'is_reserved',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_reserved' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope for active tables only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for available tables (not reserved)
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_reserved', false)->where('is_active', true);
    }

    /**
     * Scope for reserved tables
     */
    public function scopeReserved($query)
    {
        return $query->where('is_reserved', true)->where('is_active', true);
    }

    /**
     * Scope for tables by location
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope for tables by minimum capacity
     */
    public function scopeMinCapacity($query, $capacity)
    {
        return $query->where('capacity', '>=', $capacity);
    }

    /**
     * Relationship: User who created this table
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: User who last updated this table
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship: Reservations for this table
     */
    public function reservations()
    {
        return $this->hasMany(TableReservation::class, 'restaurant_table_id');
    }

    /**
     * Relationship: Upcoming active reservations
     */
    public function upcomingReservations()
    {
        return $this->hasMany(TableReservation::class, 'restaurant_table_id')
            ->where('reservation_date', '>=', date('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->orderBy('reservation_date')
            ->orderBy('reservation_time');
    }

    /**
     * Check if table has any upcoming reservations
     */
    public function hasUpcomingReservations()
    {
        return $this->upcomingReservations()->exists();
    }

    /**
     * Get table status label
     */
    public function getStatusLabelAttribute()
    {
        if (!$this->is_active) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Inactive</span>';
        }
        if ($this->is_reserved) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Reserved</span>';
        }
        return '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Available</span>';
    }

    /**
     * Get size label based on capacity
     */
    public function getSizeLabelAttribute()
    {
        if ($this->size) {
            return $this->size;
        }

        if ($this->capacity <= 2) {
            return 'Small';
        } elseif ($this->capacity <= 4) {
            return 'Medium';
        } elseif ($this->capacity <= 6) {
            return 'Large';
        } else {
            return 'Extra Large';
        }
    }
}
