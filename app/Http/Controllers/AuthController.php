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
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Login attempt', ['email' => $request->email]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Update last login time
            $user->update(['last_login_at' => now()]);

            Log::info('Login successful', [
                'user_id'       => $user->id,
                'email'         => $user->email,
                'department_id' => $user->department_id,
                'role'          => $user->role,
            ]);

            return $this->redirectBasedOnDepartment($user);
        }

        Log::warning('Login failed', ['email' => $request->email]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Redirect user based on their department (and role within department).
     */
    protected function redirectBasedOnDepartment($user)
    {
        if ($user->department) {
            $departmentName = $user->department->name;

            if ($departmentName === 'STORE') {
                return redirect()->route('store.dashboard')
                    ->with('success', 'Welcome to Store Module, ' . $user->first_name);
            }

            if ($departmentName === 'PROCUREMENT') {
                return redirect()->route('procurement.dashboard')
                    ->with('success', 'Welcome to Procurement Module, ' . $user->first_name);
            }

            if ($departmentName === 'GENERAL MANAGEMENT') {
                return redirect()->route('management.dashboard')
                    ->with('success', 'Welcome to Management Module, ' . $user->first_name);
            }

            if ($departmentName === 'DIRECTORS') {
                return redirect()->route('director.dashboard')
                    ->with('success', 'Welcome to Director Dashboard, ' . $user->first_name);
            }

            if ($departmentName === 'KITCHEN') {
                return redirect()->route('kitchen.dashboard')
                    ->with('success', 'Welcome to Kitchen Dashboard, ' . $user->first_name);
            }

            if ($departmentName === 'RESTAURANT') {
                // ── Resolve role name ─────────────────────────────────────────
                // Role may be stored as a string on the user directly,
                // or via a role_id foreign key — handle both.
                $roleName = null;

                if ($user->role_id) {
                    $role     = Role::find($user->role_id);
                    $roleName = $role->name ?? null;
                } elseif (!empty($user->role)) {
                    $roleName = $user->role;
                }

                // ── CASHIER gets their own dashboard ──────────────────────────
                // This check MUST come before the generic restaurant redirect.
                if ($roleName === 'Cashier') {
                    return redirect()->route('restaurant.cashier.dashboard')
                        ->with('success', 'Welcome to Cashier Dashboard, ' . $user->first_name);
                }

                // ── All other restaurant staff ────────────────────────────────
                return redirect()->route('restaurant.dashboard')
                    ->with('success', 'Welcome to Restaurant Dashboard, ' . $user->first_name);
            }
        }

        return redirect()->route('dashboard')
            ->with('success', 'Welcome back, ' . $user->first_name);
    }

    /**
     * Show registration form (only for first user).
     */
    public function showRegistrationForm()
    {
        if (User::count() > 0) {
            return redirect()->route('login.form')
                ->with('error', 'Registration is closed. Only the first user can register.');
        }

        return view('auth.register');
    }

    /**
     * Handle registration request (only for first user).
     */
    public function register(Request $request)
    {
        if (User::count() > 0) {
            return redirect()->route('login.form')
                ->with('error', 'Registration is closed. Only the first user can register.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|string|min:8|confirmed',
        ]);

        $validated['password']       = Hash::make($validated['password']);
        $validated['is_super_admin'] = true;
        $validated['is_active']      = true;

        $user = User::create($validated);

        Auth::login($user);

        Log::info('First user registered', ['user_id' => $user->id, 'email' => $user->email]);

        return redirect()->route('dashboard')
            ->with('success', 'Registration successful! Welcome to the system.');
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
     * Show main dashboard (fallback).
     */
    public function dashboard()
    {
        $usersCount       = User::count();
        $activeUsersCount = User::where('is_active', true)->count();

        return view('dashboard', compact('usersCount', 'activeUsersCount'));
    }
}
