<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StoreMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Check if user is logged in
        if (!$user) {
            return redirect()->route('login.form');
        }

        // Check if user has a department
        if (!$user->department) {
            Log::warning('User has no department assigned', ['user_id' => $user->id]);
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a department assigned. Please contact administrator.');
        }

        // Clean the department name (remove any hidden spaces)
        $departmentName = trim($user->department->name);
        
        Log::info('Store middleware check', [
            'user_id' => $user->id,
            'department_name' => $departmentName,
            'department_name_raw' => bin2hex($departmentName) // This shows hidden characters
        ]);
        
        // Check if department is STORE (using strcmp for exact match)
        if (strcmp($departmentName, 'STORE') !== 0) {
            Log::warning('Unauthorized access to store module', [
                'user_id' => $user->id,
                'department' => $departmentName,
                'expected' => 'STORE'
            ]);
            return redirect()->route('dashboard')
                ->with('error', 'You do not have access to the Store module. Your department is: "' . $departmentName . '"');
        }

        return $next($request);
    }
}