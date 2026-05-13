<?php
// app/Http/Middleware/RestaurantMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')->with('error', 'Please login first.');
        }

        $user = Auth::user();

        // Check if user has a department and it's RESTAURANT
        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Restaurant access only.');
        }

        return $next($request);
    }
}
