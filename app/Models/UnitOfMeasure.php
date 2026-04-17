<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitOfMeasure extends Model
{
    use HasFactory;

    protected $table = 'units_of_measure';

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'description',
        'is_base_unit',
        'base_unit_id',
        'conversion_factor',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_base_unit' => 'boolean',
            'is_active' => 'boolean',
            'conversion_factor' => 'decimal:6',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBaseUnits($query)
    {
        return $query->where('is_base_unit', true);
    }

    /**
     * Convert quantity from this unit to base unit.
     */
    public function toBaseUnit(float $quantity): float
    {
        if ($this->is_base_unit) {
            return $quantity;
        }
        
        return $quantity * $this->conversion_factor;
    }

    /**
     * Convert quantity from base unit to this unit.
     */
    public function fromBaseUnit(float $quantity): float
    {
        if ($this->is_base_unit) {
            return $quantity;
        }
        
        return $quantity / $this->conversion_factor;
    }

    // Relationships
    public function baseUnit()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'base_unit_id');
    }

    public function childUnits()
    {
        return $this->hasMany(UnitOfMeasure::class, 'base_unit_id');
    }

    public function itemUnits()
    {
        return $this->hasMany(ItemUnit::class, 'unit_of_measure_id');
    }
}