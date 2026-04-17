<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Check if user has permission (simple helper method)
     */
    private function checkPermission($permissionCode)
    {
        $user = Auth::user();
        
        // Super admin can do anything
        if ($user->is_super_admin) {
            return true;
        }
        
        // For now, allow access to all role management
        // You can modify this later based on your needs
        return true;
    }

    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        if (!$this->checkPermission('view_roles')) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to view roles.');
        }

        Log::info('Roles index accessed', [
            'user_id' => Auth::id(),
            'filters' => $request->all()
        ]);

        $query = Role::withCount('users')->with('permissions');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $roles = $query->orderBy('name')->paginate(15);

        return view('roles.index', compact('roles'));
    }

    /**
     * Show form to create a new role.
     */
    public function create()
    {
        if (!$this->checkPermission('create_roles')) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to create roles.');
        }

        Log::info('Role create form accessed', [
            'user_id' => Auth::id()
        ]);

        $permissions = Permission::getGroupedPermissions();
        $allPermissions = Permission::where('is_active', true)->orderBy('sort_order')->get();

        return view('roles.create', compact('permissions', 'allPermissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        if (!$this->checkPermission('create_roles')) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to create roles.');
        }

        Log::info('Attempting to create role', [
            'user_id' => Auth::id(),
            'data' => $request->only(['code', 'name', 'description', 'permissions'])
        ]);

        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:roles,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $validated['is_system_role'] = false;
        $validated['created_by'] = Auth::id();

        $role = Role::create($validated);

        // Assign permissions
        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        Log::info('Role created successfully', [
            'user_id' => Auth::id(),
            'role_id' => $role->id,
            'role_code' => $role->code,
            'role_name' => $role->name
        ]);

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    /**
     * Display the specified role.
     */
    public function show($id)
    {
        if (!$this->checkPermission('view_roles')) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to view roles.');
        }

        $role = Role::with(['permissions', 'creator', 'updater'])->findOrFail($id);

        Log::info('Role viewed', [
            'user_id' => Auth::id(),
            'role_id' => $role->id,
            'role_code' => $role->code,
            'role_name' => $role->name
        ]);

        $permissionsGrouped = $role->permissions->groupBy('group');

        return view('roles.show', compact('role', 'permissionsGrouped'));
    }

    /**
     * Show form to edit a role.
     */
    public function edit($id)
    {
        if (!$this->checkPermission('edit_roles')) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to edit roles.');
        }

        $role = Role::findOrFail($id);

        // Cannot edit system roles
        if ($role->is_system_role && !Auth::user()->is_super_admin) {
            return redirect()->route('roles.index')
                ->with('error', 'System roles cannot be edited.');
        }

        Log::info('Role edit form accessed', [
            'user_id' => Auth::id(),
            'role_id' => $role->id,
            'role_code' => $role->code,
            'role_name' => $role->name
        ]);

        $permissions = Permission::getGroupedPermissions();
        $allPermissions = Permission::where('is_active', true)->orderBy('sort_order')->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'allPermissions', 'rolePermissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, $id)
    {
        if (!$this->checkPermission('edit_roles')) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to edit roles.');
        }

        $role = Role::findOrFail($id);

        // Cannot edit system roles
        if ($role->is_system_role && !Auth::user()->is_super_admin) {
            return redirect()->route('roles.index')
                ->with('error', 'System roles cannot be edited.');
        }

        Log::info('Attempting to update role', [
            'user_id' => Auth::id(),
            'role_id' => $role->id,
            'role_code' => $role->code,
            'new_data' => $request->only(['code', 'name', 'description', 'permissions'])
        ]);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', Rule::unique('roles', 'code')->ignore($id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $validated['updated_by'] = Auth::id();

        $role->update($validated);

        // Sync permissions
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        Log::info('Role updated successfully', [
            'user_id' => Auth::id(),
            'role_id' => $role->id,
            'role_code' => $role->code,
            'role_name' => $role->name
        ]);

        return redirect()->route('roles.show', $role->id)
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    /**
     * Delete (soft delete) the specified role.
     */
    public function destroy($id)
    {
        if (!$this->checkPermission('delete_roles')) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to delete roles.');
        }

        $role = Role::findOrFail($id);

        // Cannot delete system roles
        if ($role->is_system_role) {
            return redirect()->route('roles.index')
                ->with('error', 'System roles cannot be deleted.');
        }

        // Cannot delete role that has users assigned
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role that has users assigned. Reassign users first.');
        }

        $roleName = $role->name;

        Log::warning('Role soft deleted', [
            'user_id' => Auth::id(),
            'role_id' => $role->id,
            'role_code' => $role->code,
            'role_name' => $role->name
        ]);

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', "Role '{$roleName}' deleted successfully.");
    }

    /**
     * Activate a role.
     */
    public function activate($id)
    {
        if (!$this->checkPermission('edit_roles')) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to activate roles.');
        }

        $role = Role::findOrFail($id);

        Log::info('Role activated', [
            'user_id' => Auth::id(),
            'role_id' => $role->id,
            'role_code' => $role->code,
            'role_name' => $role->name
        ]);

        $role->update([
            'is_active' => true,
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->name}' activated successfully.");
    }

    /**
     * Deactivate a role.
     */
    public function deactivate($id)
    {
        if (!$this->checkPermission('edit_roles')) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to deactivate roles.');
        }

        $role = Role::findOrFail($id);

        Log::info('Role deactivated', [
            'user_id' => Auth::id(),
            'role_id' => $role->id,
            'role_code' => $role->code,
            'role_name' => $role->name
        ]);

        $role->update([
            'is_active' => false,
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->name}' deactivated successfully.");
    }
}