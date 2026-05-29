<?php
// app/Services/NavigationService.php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class NavigationService
{
    protected $user;
    protected $permissions = [];

    public function __construct()
    {
        $this->user = Auth::user();

        if ($this->user && !$this->user->is_super_admin) {
            $this->permissions = $this->user->userPermissions
                ->where('pivot.is_allowed', true)
                ->pluck('code')
                ->toArray();
        }
    }

    public function can(string $permission): bool
    {
        if (!$this->user) return false;
        if ($this->user->is_super_admin) return true;
        return in_array($permission, $this->permissions);
    }

    public function canAny(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->can($permission)) return true;
        }
        return false;
    }

    public function canAll(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->can($permission)) return false;
        }
        return true;
    }
}
