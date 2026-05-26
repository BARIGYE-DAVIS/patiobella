<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Role;  // ← ADD THIS LINE
use App\Models\Permission;  // ← ADD THIS LINE
use App\Models\Department;  // ← ADD THIS LINE
use App\Models\PurchaseOrder;  // ← ADD THIS LINE
use App\Models\GoodsReceivedNote;  // ← ADD THIS LINE
use App\Models\StockMovement;  // ← ADD THIS LINE

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'department_id',
        'role_id',
        'role',
        'password',
        'is_active',
        'can_create_users',
        'is_super_admin',
        'last_login_at',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'last_login_at'      => 'datetime',
            'is_active'          => 'boolean',
            'can_create_users'   => 'boolean',
            'is_super_admin'     => 'boolean',
            'deleted_at'         => 'datetime',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function getRoleName(): ?string
    {
        if ($this->role_id) {
            return optional(Role::find($this->role_id))->name;
        }

        if (!empty($this->role) && is_numeric($this->role)) {
            return optional(Role::find($this->role))->name;
        }

        if (!empty($this->role)) {
            return $this->role;
        }

        return null;
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    public function updatedUsers()
    {
        return $this->hasMany(User::class, 'updated_by');
    }

    public function purchaseOrdersCreated()
    {
        return $this->hasMany(PurchaseOrder::class, 'ordered_by');
    }

    public function purchaseOrdersApproved()
    {
        return $this->hasMany(PurchaseOrder::class, 'approved_by');
    }

    public function goodsReceivedNotes()
    {
        return $this->hasMany(GoodsReceivedNote::class, 'received_by');
    }

    public function approvedStockMovements()
    {
        return $this->hasMany(StockMovement::class, 'approved_by');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
            ->withTimestamps();
    }

    public function userPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id')
            ->withPivot('is_allowed')
            ->withTimestamps();
    }

    public function hasPermission($permissionCode)
    {
        if ($this->is_super_admin) {
            return true;
        }

        foreach ($this->roles as $role) {
            foreach ($role->permissions as $permission) {
                if ($permission->code === $permissionCode) {
                    return true;
                }
            }
        }

        foreach ($this->userPermissions as $permission) {
            if ($permission->pivot->is_allowed && $permission->code === $permissionCode) {
                return true;
            }
        }

        return false;
    }

    public function getAllPermissions()
    {
        if ($this->is_super_admin) {
            return Permission::where('is_active', true)->get();
        }

        $permissions = collect();

        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        foreach ($this->userPermissions as $permission) {
            if ($permission->pivot->is_allowed) {
                $permissions = $permissions->push($permission);
            }
        }

        return $permissions->unique('id');
    }
}
