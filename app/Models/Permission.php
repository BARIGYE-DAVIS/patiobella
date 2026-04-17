<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'permissions';

    protected $fillable = [
        'code',
        'name',
        'group',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Scope for active permissions only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get permissions by group
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Get all unique permission groups
     */
    public static function getGroups()
    {
        return self::where('is_active', true)
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');
    }

    /**
     * Get permissions grouped by their group
     */
    public static function getGroupedPermissions()
    {
        $permissions = self::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        return $permissions->groupBy('group');
    }

    /**
     * Relationship: Roles that have this permission
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission', 'permission_id', 'role_id')
                    ->withTimestamps();
    }
}