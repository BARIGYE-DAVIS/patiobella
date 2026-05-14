<?php
// app/Http/Middleware/CashierMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashierMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', 'Please login first.');
        }

        $user = Auth::user();

        // Must be in RESTAURANT department
        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return redirect()->route('dashboard')
                ->with('error', 'Unauthorized access. Cashier access only.');
        }

        // Resolve role name
        $roleName = null;
        if ($user->role_id) {
            $role     = \App\Models\Role::find($user->role_id);
            $roleName = $role->name ?? null;
        } elseif (!empty($user->role)) {
            $roleName = $user->role;
        }

        // Must be Cashier role
        if ($roleName !== 'Cashier') {
            return redirect()->route('restaurant.dashboard')
                ->with('error', 'Unauthorized. Cashier access only.');
        }

        return $next($request);
    }
}
