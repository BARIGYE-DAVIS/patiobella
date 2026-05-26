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
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to view roles.');
        }

        $query = Role::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

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
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to create roles.');
        }

        return view('roles.create');
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to create roles.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:roles,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'is_system_role' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->input('is_active', true);
        $validated['is_system_role'] = $request->input('is_system_role', false);
        $validated['created_by'] = Auth::id();

        $role = Role::create($validated);

        Log::info('Role created', [
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
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to view roles.');
        }

        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::where('is_active', true)->orderBy('group')->orderBy('sort_order')->get();

        return view('roles.show', compact('role', 'permissions'));
    }

    /**
     * Show form to edit a role.
     */
public function edit($id)
{
    if (!Auth::user()->is_super_admin) {
        return redirect()->route('roles.index')
            ->with('error', 'You do not have permission to edit roles.');
    }

    $role = Role::findOrFail($id);

    return view('roles.edit', compact('role'));
}
    /**
     * Update the specified role.
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to update roles.');
        }

        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', Rule::unique('roles', 'code')->ignore($id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'is_system_role' => 'sometimes|boolean',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $validated['is_active'] = $request->input('is_active', true);
        $validated['is_system_role'] = $request->input('is_system_role', false);
        $validated['updated_by'] = Auth::id();

        $role->update($validated);

        // Sync permissions (only if permissions are provided in request)
        if ($request->has('permissions')) {
            $role->permissions()->sync($validated['permissions'] ?? []);
        }

        Log::info('Role updated', [
            'user_id' => Auth::id(),
            'role_id' => $role->id,
            'role_code' => $role->code,
            'role_name' => $role->name
        ]);

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    /**
     * Delete the specified role.
     */
    public function destroy($id)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to delete roles.');
        }

        $role = Role::findOrFail($id);

        // Check if system role
        if ($role->is_system_role) {
            return redirect()->route('roles.index')
                ->with('error', 'System roles cannot be deleted.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role that is assigned to users. Reassign users first.');
        }

        $roleName = $role->name;

        Log::warning('Role deleted', [
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
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to activate roles.');
        }

        $role = Role::findOrFail($id);
        $role->update(['is_active' => true]);

        Log::info('Role activated', [
            'user_id' => Auth::id(),
            'role_id' => $role->id,
            'role_name' => $role->name
        ]);

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->name}' activated successfully.");
    }

    /**
     * Deactivate a role.
     */
    public function deactivate($id)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('roles.index')
                ->with('error', 'You do not have permission to deactivate roles.');
        }

        $role = Role::findOrFail($id);
        $role->update(['is_active' => false]);

        Log::info('Role deactivated', [
            'user_id' => Auth::id(),
            'role_id' => $role->id,
            'role_name' => $role->name
        ]);

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->name}' deactivated successfully.");
    }
}
