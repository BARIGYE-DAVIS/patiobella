<?php
// app/Http/Middleware/RestaurantMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantMiddleware
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
                ->with('error', 'Unauthorized access. Restaurant access only.');
        }

        // Resolve role name
        $roleName = null;
        if ($user->role_id) {
            $role = \App\Models\Role::find($user->role_id);
            $roleName = $role->name ?? null;
        } elseif (!empty($user->role)) {
            $roleName = $user->role;
        }

        // Cashier handling
        if ($roleName === 'Cashier') {
            // Allow cashiers to VIEW menu (GET requests only)
            // Block all other restaurant routes (POST, PUT, DELETE, etc.)
            if ($request->isMethod('get') && $request->routeIs('restaurant.menu.*')) {
                return $next($request);
            }

            // Redirect cashiers away from all other restaurant routes
            return redirect()->route('restaurant.cashier.dashboard')
                ->with('error', 'Cashiers must use the cashier dashboard.');
        }

        // Non-cashier restaurant staff - full access
        return $next($request);
    }
}
