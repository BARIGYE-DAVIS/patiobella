<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'Location',
        'Description',
        'department_type_id',
        'manager_id',
        'default_store_id',
        'is_active',
        'monthly_budget',
        'yearly_budget',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'monthly_budget' => 'decimal:2',
            'yearly_budget' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relationships
    public function departmentType()
    {
        return $this->belongsTo(DepartmentType::class, 'department_type_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function defaultStore()
    {
        return $this->belongsTo(Store::class, 'default_store_id');
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
        return $this->hasMany(StockMovement::class, 'department_id');
    }
}