<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KitchenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login.form');
        }

        if (!$user->department) {
            Log::warning('User has no department assigned', ['user_id' => $user->id]);
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a department assigned.');
        }

        $departmentName = strtoupper(trim($user->department->name));

        if ($departmentName !== 'KITCHEN') {
            Log::warning('Unauthorized access to kitchen module', [
                'user_id' => $user->id,
                'department' => $departmentName
            ]);
            return redirect()->route('dashboard')
                ->with('error', 'You do not have access to the Kitchen module.');
        }

        return $next($request);
    }
}
