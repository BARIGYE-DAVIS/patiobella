<?php
// app/Http/Middleware/BarMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Role;

class BarMiddleware
{
    /**
     * Resolve role name from user.
     */
    private function getRoleName($user): ?string
    {
        try {
            if ($user->role_id) {
                $role = Role::find($user->role_id);
                return $role->name ?? null;
            }
            return !empty($user->role) ? $user->role : null;
        } catch (\Exception $e) {
            Log::error('Error resolving role name in BarMiddleware', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('Bar access denied: User not authenticated');
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has a department
        if (!$user->department) {
            Log::warning('Bar access denied: No department assigned', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            return redirect()->route('dashboard')->with('error', 'No department assigned to your account.');
        }

        // Check if department is BAR
        if ($user->department->name !== 'BAR') {
            Log::warning('Bar access denied: Wrong department', [
                'user_id' => $user->id,
                'department' => $user->department->name,
                'required' => 'BAR'
            ]);

            // Redirect based on their actual department
            if ($user->department->name === 'RESTAURANT') {
                return redirect()->route('restaurant.dashboard')->with('error', 'This area is for Bar department only.');
            } elseif ($user->department->name === 'STORE') {
                return redirect()->route('store.dashboard')->with('error', 'This area is for Bar department only.');
            } elseif ($user->department->name === 'KITCHEN') {
                return redirect()->route('kitchen.dashboard')->with('error', 'This area is for Bar department only.');
            }

            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Bar department only.');
        }

        // Get user role
        $roleName = $this->getRoleName($user);

        // Check access based on route path
        $path = $request->path();

        // Bar Manager can access everything
        if ($roleName === 'Bar Manager' || $roleName === 'Admin' || $roleName === 'BAR MANAGER' || $roleName === 'ADMIN') {
            Log::info('Bar Manager/Admin access granted', [
                'user_id' => $user->id,
                'role' => $roleName,
                'path' => $path
            ]);
            return $next($request);
        }

        // Bar Cashier access - only specific routes
        if ($roleName === 'Bar Cashier' || $roleName === 'Cashier' || $roleName === 'CASHIER') {
            // Cashier allowed routes
            $allowedRoutes = [
                'bar/pos',
                'bar/cashier/dashboard',
                'bar/cashier/orders',
                'bar/cashier/invoice',
                'bar/cashier/receipt',
                'bar/cashier/my-sales',
            ];

            $isAllowed = false;
            foreach ($allowedRoutes as $allowedRoute) {
                if (str_starts_with($path, $allowedRoute)) {
                    $isAllowed = true;
                    break;
                }
            }

            // Also allow main dashboard for cashier
            if ($path === 'bar/dashboard') {
                $isAllowed = true;
            }

            if (!$isAllowed) {
                Log::warning('Bar Cashier access denied - unauthorized route', [
                    'user_id' => $user->id,
                    'role' => $roleName,
                    'path' => $path
                ]);
                return redirect()->route('bar.cashier.dashboard')->with('error', 'You do not have permission to access this page.');
            }

            Log::info('Bar Cashier access granted', [
                'user_id' => $user->id,
                'role' => $roleName,
                'path' => $path
            ]);
            return $next($request);
        }

        // Unknown role
        Log::warning('Bar access denied - unknown role', [
            'user_id' => $user->id,
            'role' => $roleName,
            'path' => $path
        ]);
        return redirect()->route('dashboard')->with('error', 'Unauthorized access. Invalid role for Bar department.');
    }
}
