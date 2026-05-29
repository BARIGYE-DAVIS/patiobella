<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $users = User::with(['roles', 'department'])
            ->when($request->filled('role_id'), function ($query) use ($request) {
                return $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('role_id', $request->role_id);
                });
            })
            ->when($request->filled('department_id'), function ($query) use ($request) {
                return $query->where('department_id', $request->department_id);
            })
            ->when($request->filled('is_active'), function ($query) use ($request) {
                return $query->where('is_active', $request->is_active);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                return $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $roles = Role::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles', 'departments'));
    }

    /**
     * Show form to create a new user.
     */
    public function create()
    {
        try {
            if (!$this->canManageUsers()) {
                Log::warning('Unauthorized create attempt', ['user_id' => Auth::id()]);
                return redirect()->route('users.index')
                    ->with('error', 'You do not have permission to create users.');
            }

            $roles = Role::where('is_active', true)->orderBy('name')->get();
            $departments = Department::where('is_active', true)->orderBy('name')->get();
            $permissions = Permission::where('is_active', true)->orderBy('group')->orderBy('sort_order')->get();

            return view('admin.users.create', compact('roles', 'departments', 'permissions'));
        } catch (\Exception $e) {
            Log::error('Error in create method: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        try {
            if (!$this->canManageUsers()) {
                return redirect()->route('users.index')
                    ->with('error', 'You do not have permission to create users.');
            }

            $validated = $request->validate([
                'first_name'           => 'required|string|max:100',
                'last_name'            => 'nullable|string|max:100',
                'email'                => 'required|email|unique:users,email',
                'password'             => 'required|string|min:8',
                'role_ids'             => 'required|array|min:1',
                'role_ids.*'           => 'exists:roles,id',
                'department_id'        => 'nullable|exists:departments,id',
                'is_active'            => 'sometimes|boolean',
                'can_create_users'     => 'sometimes|boolean',
                'extra_permissions'    => 'sometimes|array',
                'extra_permissions.*'  => 'exists:permissions,id',
            ]);

            $userData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'department_id' => $validated['department_id'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'can_create_users' => $validated['can_create_users'] ?? false,
                'is_super_admin' => false,
                'created_by' => Auth::id(),
            ];

            DB::beginTransaction();

            $user = User::create($userData);

            // Assign roles
            $user->roles()->sync($validated['role_ids']);

            // Assign extra permissions (user-specific)
            if (!empty($validated['extra_permissions'])) {
                $extraPermissions = [];
                foreach ($validated['extra_permissions'] as $permId) {
                    $extraPermissions[$permId] = ['is_allowed' => true];
                }
                $user->userPermissions()->sync($extraPermissions);
            }

            // Set primary role for backward compatibility
            $primaryRoleId = $validated['role_ids'][0];
            $user->role_id = $primaryRoleId;
            $user->role = $primaryRoleId;
            $user->saveQuietly();

            DB::commit();

            $roleCount = count($validated['role_ids']);
            $permCount = count($validated['extra_permissions'] ?? []);
            return redirect()->route('users.index')->with('success', "User created successfully with {$roleCount} role(s) and {$permCount} extra permission(s).");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        try {
            $user = User::with(['roles', 'department', 'creator', 'updater', 'userPermissions'])->findOrFail($id);
            $roles = Role::where('is_active', true)->orderBy('name')->get();

            return view('admin.users.show', compact('user', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error in show method: ' . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        }
    }

    /**
     * Show form to edit a user.
     */
    public function edit($id)
    {
        try {
            if (!$this->canManageUsers()) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'You do not have permission to edit users.');
            }

            $user = User::with(['roles', 'userPermissions'])->findOrFail($id);

            if ($user->is_super_admin && !Auth::user()->is_super_admin) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'You cannot edit a super administrator.');
            }

            $roles = Role::where('is_active', true)->orderBy('name')->get();
            $departments = Department::where('is_active', true)->orderBy('name')->get();
            $permissions = Permission::where('is_active', true)->orderBy('group')->orderBy('sort_order')->get();

            $userRoleIds = $user->roles->pluck('id')->toArray();

            // Get user's extra permissions (only allowed ones)
            $extraPermissionIds = $user->userPermissions()
                ->wherePivot('is_allowed', true)
                ->pluck('permissions.id')
                ->toArray();

            return view('admin.users.edit', compact('user', 'roles', 'departments', 'userRoleIds', 'permissions', 'extraPermissionIds'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        } catch (\Exception $e) {
            Log::error('Error in edit method: ' . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        try {
            if (!$this->canManageUsers()) {
                return redirect()->route('users.index')
                    ->with('error', 'You do not have permission to update users.');
            }

            $user = User::findOrFail($id);

            if ($user->is_super_admin && !Auth::user()->is_super_admin) {
                return redirect()->route('users.index')
                    ->with('error', 'You cannot edit a super administrator.');
            }

            $validated = $request->validate([
                'first_name'           => 'required|string|max:100',
                'last_name'            => 'nullable|string|max:100',
                'email'                => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
                'role_ids'             => 'required|array|min:1',
                'role_ids.*'           => 'exists:roles,id',
                'department_id'        => 'nullable|exists:departments,id',
                'is_active'            => 'sometimes|boolean',
                'can_create_users'     => 'sometimes|boolean',
                'extra_permissions'    => 'sometimes|array',
                'extra_permissions.*'  => 'exists:permissions,id',
            ]);

            $userData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'department_id' => $validated['department_id'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'can_create_users' => $validated['can_create_users'] ?? false,
                'updated_by' => Auth::id(),
            ];

            if ($request->filled('password')) {
                $request->validate(['password' => 'required|string|min:8|confirmed']);
                $userData['password'] = Hash::make($request->password);
            }

            DB::beginTransaction();

            $user->update($userData);

            // Sync roles
            $user->roles()->sync($validated['role_ids']);

            // Handle extra permissions (user-specific)
            $extraPermissions = $request->input('extra_permissions', []);

            // Prepare data for sync (all are allowed = true)
            $permissionsData = [];
            foreach ($extraPermissions as $permId) {
                $permissionsData[$permId] = ['is_allowed' => true];
            }

            // Sync user permissions - this will add/remove based on what is checked
            $user->userPermissions()->sync($permissionsData);

            // Set primary role for backward compatibility
            $primaryRoleId = $validated['role_ids'][0];
            $user->role_id = $primaryRoleId;
            $user->role = $primaryRoleId;
            $user->saveQuietly();

            DB::commit();

            $roleCount = count($validated['role_ids']);
            $permCount = count($extraPermissions);
            return redirect()->route('users.index')->with('success', "User updated successfully with {$roleCount} role(s) and {$permCount} extra permission(s).");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update password only.
     */
    public function updatePassword(Request $request, $id)
    {
        try {
            if (!$this->canManageUsers()) {
                return redirect()->route('admin.users.index')->with('error', 'You do not have permission.');
            }

            $user = User::findOrFail($id);

            $validated = $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user->update([
                'password' => Hash::make($validated['password']),
                'updated_by' => Auth::user()->id
            ]);

            return redirect()->route('admin.users.edit', $id)->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating password: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update password.')->withInput();
        }
    }

    /**
     * Delete the specified user.
     */
    public function destroy($id)
    {
        try {
            if (!Auth::user()->is_super_admin) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Only super administrator can delete users.');
            }

            $user = User::findOrFail($id);

            if ($user->id === Auth::user()->id) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'You cannot delete your own account.');
            }

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in destroy method: ' . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'An error occurred while deleting the user.');
        }
    }

    /**
     * Activate a user.
     */
    public function activate($id)
    {
        try {
            if (!$this->canManageUsers()) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'You do not have permission to activate users.');
            }

            $user = User::findOrFail($id);
            $user->update([
                'is_active' => true,
                'updated_by' => Auth::user()->id
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User activated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in activate method: ' . $e->getMessage());
            return redirect()->route('admin.users.index')->with('error', 'An error occurred while activating the user.');
        }
    }

    /**
     * Deactivate a user.
     */
    public function deactivate($id)
    {
        try {
            if (!$this->canManageUsers()) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'You do not have permission to deactivate users.');
            }

            $user = User::findOrFail($id);

            if ($user->id === Auth::user()->id) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'You cannot deactivate your own account.');
            }

            $user->update([
                'is_active' => false,
                'updated_by' => Auth::user()->id
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User deactivated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in deactivate method: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'An error occurred while deactivating the user.');
        }
    }

    /**
     * Check if current user can manage users.
     */
    private function canManageUsers()
    {
        $user = Auth::user();

        if ($user->is_super_admin) {
            return true;
        }

        return $user->can_create_users;
    }

    /**
     * Get user's merged permissions from all roles and user-specific permissions.
     */
    public function getUserPermissions($userId)
    {
        $user = User::with(['roles.permissions', 'userPermissions'])->find($userId);

        if ($user->is_super_admin) {
            return Permission::where('is_active', true)->get();
        }

        // Get permissions from all roles
        $rolePermissions = collect();
        foreach ($user->roles as $role) {
            $rolePermissions = $rolePermissions->merge($role->permissions);
        }

        // Get user-specific extra permissions (allowed)
        $extraPermissions = $user->userPermissions()
            ->wherePivot('is_allowed', true)
            ->get();

        // Merge role permissions with extra user permissions
        $allPermissions = $rolePermissions->merge($extraPermissions);

        return $allPermissions->unique('id');
    }

    /**
     * Check if a user has a specific permission.
     */
    public function checkPermission($userId, $permissionCode)
    {
        $user = User::find($userId);

        if (!$user) return false;
        if ($user->is_super_admin) return true;

        $permissions = $this->getUserPermissions($userId);
        return $permissions->contains('code', $permissionCode);
    }
}
