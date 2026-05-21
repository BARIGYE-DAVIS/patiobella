<?php
// app/Models/MenuItemCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItemCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'menu_item_categories';

    protected $fillable = [
        'name',
        'code',
        'description',
        'sort_order',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'menu_item_category_id');
    }

    public function activeMenuItems()
    {
        return $this->hasMany(MenuItem::class, 'menu_item_category_id')->where('is_active', true);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // =====================================================
    // ACCESSORS
    // =====================================================

    public function getStatusBadgeAttribute()
    {
        return $this->is_active
            ? '<span class="badge-approved"><i class="fas fa-check-circle"></i> Active</span>'
            : '<span class="badge-pending"><i class="fas fa-ban"></i> Inactive</span>';
    }

    public function getItemsCountAttribute()
    {
        return $this->menuItems()->count();
    }

    public function getActiveItemsCountAttribute()
    {
        return $this->menuItems()->where('is_active', true)->count();
    }
}
