<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request - Password only (no email).
     */
    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $password = $request->input('password');

        Log::info('Login attempt', ['password_entered' => '******']);

        // Find user by checking password against all active users
        $users = User::where('is_active', true)->get();

        foreach ($users as $user) {
            if (Hash::check($password, $user->password)) {
                Auth::login($user, $request->remember);
                $request->session()->regenerate();

                $user->update(['last_login_at' => now()]);

                Log::info('Login successful', [
                    'user_id' => $user->id,
                    'user_name' => $user->first_name . ' ' . $user->last_name,
                    'department_id' => $user->department_id,
                ]);

                return $this->redirectBasedOnRoleAndDepartment($user);
            }
        }

        Log::warning('Login failed - invalid password or inactive account');

        return back()->withErrors([
            'password' => 'The provided password is incorrect or your account is inactive.',
        ])->onlyInput('password');
    }

    /**
     * Redirect user based on their role first, then department.
     * Only super admin can access main dashboard.
     */
    protected function redirectBasedOnRoleAndDepartment($user)
    {
        // Check if user is super admin (only super admin can access main dashboard)
        if ($user->is_super_admin == 1) {
            return redirect()->route('dashboard')
                ->with('success', 'Welcome to Admin Dashboard, ' . $user->first_name);
        }

        // Get user's role name
        $roleName = null;
        $userRoles = [];

        // Check from multiple roles (user_roles table)
        if ($user->roles && $user->roles->count() > 0) {
            foreach ($user->roles as $role) {
                $roleName = $role->name;
                $userRoles[] = strtolower($role->name);

                // Waiter/Waitress
                if (strtolower($roleName) === 'waiter' || strtolower($roleName) === 'waitress') {
                    return redirect()->route('waiter.dashboard')
                        ->with('success', 'Welcome to Waiter Portal, ' . $user->first_name);
                }

                // Cashier
                if (strtolower($roleName) === 'cashier' || strtolower($roleName) === 'bar cashier') {
                    return redirect()->route('cashier.index')
                        ->with('success', 'Welcome to Cashier Dashboard, ' . $user->first_name);
                }
            }
        }

        // Check single role (role_id column)
        if ($user->role_id) {
            $role = Role::find($user->role_id);
            if ($role) {
                $roleName = $role->name;
                $userRoles[] = strtolower($role->name);

                if (strtolower($role->name) === 'waiter' || strtolower($role->name) === 'waitress') {
                    return redirect()->route('waiter.dashboard')
                        ->with('success', 'Welcome to Waiter Portal, ' . $user->first_name);
                }

                if (strtolower($role->name) === 'cashier' || strtolower($role->name) === 'bar cashier') {
                    return redirect()->route('cashier.index')
                        ->with('success', 'Welcome to Cashier Dashboard, ' . $user->first_name);
                }
            }
        }

        // Check role column (legacy)
        if (!empty($user->role)) {
            $roleName = $user->role;
            $userRoles[] = strtolower($user->role);

            if (strtolower($user->role) === 'waiter' || strtolower($user->role) === 'waitress') {
                return redirect()->route('waiter.dashboard')
                    ->with('success', 'Welcome to Waiter Portal, ' . $user->first_name);
            }

            if (strtolower($user->role) === 'cashier' || strtolower($user->role) === 'bar cashier') {
                return redirect()->route('cashier.index')
                    ->with('success', 'Welcome to Cashier Dashboard, ' . $user->first_name);
            }
        }

        // If not super admin, not waiter, not cashier, redirect based on department
        return $this->redirectBasedOnDepartment($user);
    }

    /**
     * Redirect user based on their department.
     */
    protected function redirectBasedOnDepartment($user)
    {
        if ($user->department) {
            $departmentName = strtoupper($user->department->name);

            if ($departmentName === 'STORE') {
                return redirect()->route('store.dashboard')
                    ->with('success', 'Welcome to Store Module, ' . $user->first_name);
            }

            if ($departmentName === 'PROCUREMENT') {
                return redirect()->route('procurement.dashboard')
                    ->with('success', 'Welcome to Procurement Module, ' . $user->first_name);
            }

            if ($departmentName === 'GENERAL MANAGEMENT' || $departmentName === 'MANAGEMENT') {
                return redirect()->route('management.dashboard')
                    ->with('success', 'Welcome to Management Module, ' . $user->first_name);
            }

            if ($departmentName === 'DIRECTORS' || $departmentName === 'DIRECTOR') {
                return redirect()->route('director.dashboard')
                    ->with('success', 'Welcome to Director Dashboard, ' . $user->first_name);
            }

            if ($departmentName === 'KITCHEN') {
                return redirect()->route('kitchen.order-tickets')
                    ->with('success', 'Welcome to Kitchen Dashboard, ' . $user->first_name);
            }

            if ($departmentName === 'CAFE') {
                return redirect()->route('cafe.order-tickets')
                    ->with('success', 'Welcome to Cafe Dashboard, ' . $user->first_name);
            }

            if ($departmentName === 'BAR') {
                return redirect()->route('bar.order-tickets')
                    ->with('success', 'Welcome to Bar Dashboard, ' . $user->first_name);
            }

            if ($departmentName === 'RESTAURANT') {
                return redirect()->route('restaurant.dashboard')
                    ->with('success', 'Welcome to Restaurant Dashboard, ' . $user->first_name);
            }
        }

        // Dynamic warning based on user's role
        $roleName = $this->getUserRoleName($user);
        $errorMessage = $this->getAccessErrorMessage($roleName);

        Auth::logout();
        return redirect()->route('login.form')
            ->withErrors(['password' => $errorMessage]);
    }

    /**
     * Get user's role name
     */
    protected function getUserRoleName($user)
    {
        if ($user->roles && $user->roles->count() > 0) {
            return $user->roles->first()->name;
        }

        if ($user->role_id) {
            $role = Role::find($user->role_id);
            return $role ? $role->name : null;
        }

        return $user->role ?? null;
    }

    /**
     * Get dynamic error message based on user's role
     */
    protected function getAccessErrorMessage($roleName)
    {
        $messages = [
            'Waiter' => 'Your waiter account needs department assignment. Please contact administrator.',
            'Waitress' => 'Your waitress account needs department assignment. Please contact administrator.',
            'Cashier' => 'Your cashier account needs department assignment. Please contact administrator.',
            'Bar Cashier' => 'Your bar cashier account needs department assignment. Please contact administrator.',
            'Chef' => 'Your chef account needs kitchen department assignment. Please contact administrator.',
            'Kitchen Manager' => 'Your kitchen manager account needs department assignment. Please contact administrator.',
            'Bar Manager' => 'Your bar manager account needs bar department assignment. Please contact administrator.',
            'Store Keeper' => 'Your store keeper account needs store department assignment. Please contact administrator.',
            'Procurement Officer' => 'Your procurement account needs procurement department assignment. Please contact administrator.',
            'General Manager' => 'Your general manager account needs management department assignment. Please contact administrator.',
            'Accountant' => 'Your accountant account needs finance department assignment. Please contact administrator.',
        ];

        return $messages[$roleName] ?? 'Your account is not properly configured. Please contact administrator.';
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Log::info('User logged out', ['user_id' => Auth::id()]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form');
    }

    /**
     * Show main dashboard (ONLY for super admin).
     */
    public function dashboard()
    {
        // Only super admin can access main dashboard
        if (!Auth::check() || Auth::user()->is_super_admin != 1) {
            Auth::logout();
            return redirect()->route('login.form')
                ->withErrors(['password' => 'You do not have permission to access the admin dashboard.']);
        }

        $usersCount       = User::count();
        $activeUsersCount = User::where('is_active', true)->count();

        return view('dashboard', compact('usersCount', 'activeUsersCount'));
    }
}
