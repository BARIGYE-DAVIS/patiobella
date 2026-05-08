<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DirectorMiddleware
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
        
        if ($departmentName !== 'DIRECTORS') {
            Log::warning('Unauthorized access to director module', [
                'user_id' => $user->id,
                'department' => $departmentName,
                'expected' => 'DIRECTORS'
            ]);
            return redirect()->route('dashboard')
                ->with('error', 'You do not have access to the Directors module.');
        }

        return $next($request);
    }
}