<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */public function index(Request $request)
{
    if (!Auth::user()->is_super_admin) {
        return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to view permissions.');
    }

    $query = Permission::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('group', 'like', "%{$search}%");
        });
    }

    if ($request->filled('group')) {
        $query->where('group', $request->group);
    }

    if ($request->filled('is_active')) {
        $query->where('is_active', $request->is_active);
    }

    // Get ALL permissions - no pagination for live search
    $permissions = $query->orderBy('group')->orderBy('sort_order')->orderBy('name')->get();
    $groups = Permission::select('group')->distinct()->pluck('group');

    return view('admin.permissions.index', compact('permissions', 'groups'));
}
    /**
     * Show form to create a new permission.
     */
    public function create()
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'You do not have permission to create permissions.');
        }

        return view('admin.permissions.create');
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->is_super_admin) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return redirect()->route('admin.permissions.index')
                ->with('error', 'You do not have permission to create permissions.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:permissions,code',
            'name' => 'required|string|max:255',
            'group' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $maxSortOrder = Permission::where('group', $validated['group'])->max('sort_order');
        $validated['sort_order'] = ($maxSortOrder ?? 0) + 1;
        $validated['is_active'] = $request->input('is_active', true);

        $permission = Permission::create($validated);

        Log::info('Permission created', [
            'user_id' => Auth::id(),
            'permission_id' => $permission->id,
            'permission_code' => $permission->code,
            'permission_name' => $permission->name,
            'group' => $permission->group
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
                'permission' => [
                    'id' => $permission->id,
                    'code' => $permission->code,
                    'name' => $permission->name,
                    'group' => $permission->group,
                    'description' => $permission->description
                ]
            ]);
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$permission->name}' created successfully.");
    }

    /**
     * Display the specified permission.
     */
    public function show($id)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'You do not have permission to view permissions.');
        }

        $permission = Permission::findOrFail($id);

        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Show form to edit a permission.
     */
    public function edit($id)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'You do not have permission to edit permissions.');
        }

        $permission = Permission::findOrFail($id);

        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'You do not have permission to update permissions.');
        }

        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', Rule::unique('permissions', 'code')->ignore($id)],
            'name' => 'required|string|max:255',
            'group' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $permission->update($validated);

        Log::info('Permission updated', [
            'user_id' => Auth::id(),
            'permission_id' => $permission->id,
            'permission_code' => $permission->code,
            'permission_name' => $permission->name
        ]);

        return redirect()->route('admin.permissions.show', $permission->id)
            ->with('success', "Permission '{$permission->name}' updated successfully.");
    }

    /**
     * Delete the specified permission.
     */
    public function destroy($id)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'You do not have permission to delete permissions.');
        }

        $permission = Permission::findOrFail($id);

        if ($permission->roles()->count() > 0) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'Cannot delete permission that is assigned to roles. Remove it from roles first.');
        }

        if ($permission->users()->count() > 0) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'Cannot delete permission that is assigned to users as extra permission. Remove it from users first.');
        }

        $permissionName = $permission->name;

        Log::warning('Permission deleted', [
            'user_id' => Auth::id(),
            'permission_id' => $permission->id,
            'permission_code' => $permission->code,
            'permission_name' => $permission->name
        ]);

        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$permissionName}' deleted successfully.");
    }

    /**
     * Activate a permission.
     */
    public function activate($id)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'You do not have permission to activate permissions.');
        }

        $permission = Permission::findOrFail($id);
        $permission->update(['is_active' => true]);

        Log::info('Permission activated', [
            'user_id' => Auth::id(),
            'permission_id' => $permission->id,
            'permission_name' => $permission->name
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$permission->name}' activated successfully.");
    }

    /**
     * Deactivate a permission.
     */
    public function deactivate($id)
    {
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('permissions.index')
                ->with('error', 'You do not have permission to deactivate permissions.');
        }

        $permission = Permission::findOrFail($id);
        $permission->update(['is_active' => false]);

        Log::info('Permission deactivated', [
            'user_id' => Auth::id(),
            'permission_id' => $permission->id,
            'permission_name' => $permission->name
        ]);

        return redirect()->route('permissions.index')
            ->with('success', "Permission '{$permission->name}' deactivated successfully.");
    }

    /**
     * Get permissions by group (for AJAX).
     */
    public function getByGroup($group)
    {
        $permissions = Permission::where('group', $group)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        return response()->json($permissions);
    }

    /**
     * Get all groups (for AJAX).
     */
    public function getGroups()
    {
        $groups = Permission::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return response()->json($groups);
    }
}
