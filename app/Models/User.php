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
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'can_create_users' => 'boolean',
            'is_super_admin' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // role column stores the ID from roles table
    public function role()
    {
        return $this->belongsTo(Role::class, 'role', 'id');
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

    public function managedStores()
    {
        return $this->hasMany(Store::class, 'manager_id');
    }

    public function managedDepartments()
    {
        return $this->hasMany(Department::class, 'manager_id');
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