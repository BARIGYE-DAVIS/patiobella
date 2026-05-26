<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'roles';

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'is_system_role',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_system_role' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Scope for active roles only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for non-system roles (can be deleted)
     */
    public function scopeNonSystem($query)
    {
        return $query->where('is_system_role', false);
    }

    /**
     * Relationship: Permissions assigned to this role
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id')
                    ->withTimestamps();
    }

    /**
     * Relationship: Users with this role
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    /**
     * Relationship: Created by user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Updated by user
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission($permissionCode)
    {
        return $this->permissions()->where('code', $permissionCode)->exists();
    }

    /**
     * Check if role has any of the given permissions
     */
    public function hasAnyPermission($permissionCodes)
    {
        return $this->permissions()->whereIn('code', $permissionCodes)->exists();
    }

    /**
     * Check if role has all of the given permissions
     */
    public function hasAllPermissions($permissionCodes)
    {
        $permissionCount = $this->permissions()->whereIn('code', $permissionCodes)->count();
        return $permissionCount === count($permissionCodes);
    }

    /**
     * Assign a permission to this role
     */
    public function assignPermission($permissionId)
    {
        if (!$this->permissions()->where('permission_id', $permissionId)->exists()) {
            $this->permissions()->attach($permissionId);
        }
        return $this;
    }

    /**
     * Remove a permission from this role
     */
    public function removePermission($permissionId)
    {
        $this->permissions()->detach($permissionId);
        return $this;
    }

    /**
     * Sync permissions for this role
     */
    public function syncPermissions(array $permissionIds)
    {
        $this->permissions()->sync($permissionIds);
        return $this;
    }


}
