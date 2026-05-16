<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'department_id',
        'role_id',       // ← use role_id as the FK
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

    /**
     * Role relationship — uses role_id FK (correct)
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Helper to get role name regardless of how it's stored.
     * Handles legacy data where role column has the numeric id.
     */
    public function getRoleName(): ?string
    {
        // Preferred: role_id FK is set
        if ($this->role_id) {
            return optional(Role::find($this->role_id))->name;
        }

        // Legacy fallback: role column has a numeric id
        if (!empty($this->role) && is_numeric($this->role)) {
            return optional(Role::find($this->role))->name;
        }

        // Legacy fallback: role column has the name directly
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
}
