<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AllowFirstUserRegistration
{
    public function handle(Request $request, Closure $next)
    {
        // Check if users table is empty
        if (User::count() === 0) {
            // Allow registration only if trying to access register endpoint
            if ($request->is('api/register') || $request->is('api/register/*')) {
                return $next($request);
            }
        } else {
            // Users exist, block registration access
            if ($request->is('api/register') || $request->is('api/register/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration is disabled. Please contact your administrator.'
                ], 403);
            }
        }

        return $next($request);
    }
}