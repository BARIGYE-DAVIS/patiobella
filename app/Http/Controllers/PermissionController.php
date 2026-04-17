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
     */
    public function index(Request $request)
    {
        // Check permission
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to view permissions.');
        }

        $query = Permission::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('group', 'like', "%{$search}%");
            });
        }

        // Filter by group
        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $permissions = $query->orderBy('group')->orderBy('sort_order')->orderBy('name')->paginate(20);
        $groups = Permission::select('group')->distinct()->pluck('group');

        return view('permissions.index', compact('permissions', 'groups'));
    }

    /**
     * Show form to create a new permission.
     */
    public function create()
    {
        // Check permission
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('permissions.index')
                ->with('error', 'You do not have permission to create permissions.');
        }

        $groups = Permission::select('group')->distinct()->pluck('group');

        return view('permissions.create', compact('groups'));
    }

 /**
 * Store a newly created permission (AJAX and normal request).
 */
public function store(Request $request)
{
    // Check permission
    if (!Auth::user()->is_super_admin) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        return redirect()->route('permissions.index')
            ->with('error', 'You do not have permission to create permissions.');
    }

    Log::info('Attempting to create permission', [
        'user_id' => Auth::id(),
        'data' => $request->only(['code', 'name', 'group', 'description'])
    ]);

    $validated = $request->validate([
        'code' => 'nullable|string|max:100|unique:permissions,code',
        'name' => 'required|string|max:255',
        'group' => 'required|string|max:100',
        'description' => 'nullable|string',
        'is_active' => 'sometimes|boolean',
    ]);
    
    // Auto-generate code if not provided
    if (empty($validated['code'])) {
        $validated['code'] = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $validated['name']));
        // Ensure code is unique by adding number if exists
        $originalCode = $validated['code'];
        $counter = 1;
        while (Permission::where('code', $validated['code'])->exists()) {
            $validated['code'] = $originalCode . '_' . $counter;
            $counter++;
        }
    }

    // Get the max sort_order for this group
    $maxSortOrder = Permission::where('group', $validated['group'])->max('sort_order');
    $validated['sort_order'] = ($maxSortOrder ?? 0) + 1;
    $validated['is_active'] = $request->input('is_active', true);

    $permission = Permission::create($validated);

    Log::info('Permission created successfully', [
        'user_id' => Auth::id(),
        'permission_id' => $permission->id,
        'permission_code' => $permission->code,
        'permission_name' => $permission->name,
        'group' => $permission->group
    ]);

    // Return JSON for AJAX requests
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

    return redirect()->route('permissions.index')
        ->with('success', "Permission '{$permission->name}' created successfully.");
}

    /**
     * Display the specified permission.
     */
    public function show($id)
    {
        // Check permission
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('permissions.index')
                ->with('error', 'You do not have permission to view permissions.');
        }

        $permission = Permission::findOrFail($id);

        // Get roles that have this permission
        $roles = $permission->roles()->get();

        return view('permissions.show', compact('permission', 'roles'));
    }

    /**
     * Show form to edit a permission.
     */
    public function edit($id)
    {
        // Check permission
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('permissions.index')
                ->with('error', 'You do not have permission to edit permissions.');
        }

        $permission = Permission::findOrFail($id);
        $groups = Permission::select('group')->distinct()->pluck('group');

        return view('permissions.edit', compact('permission', 'groups'));
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, $id)
    {
        // Check permission
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('permissions.index')
                ->with('error', 'You do not have permission to update permissions.');
        }

        $permission = Permission::findOrFail($id);

        Log::info('Attempting to update permission', [
            'user_id' => Auth::id(),
            'permission_id' => $permission->id,
            'old_code' => $permission->code,
            'new_data' => $request->only(['code', 'name', 'group', 'description'])
        ]);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', Rule::unique('permissions', 'code')->ignore($id)],
            'name' => 'required|string|max:255',
            'group' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $permission->update($validated);

        Log::info('Permission updated successfully', [
            'user_id' => Auth::id(),
            'permission_id' => $permission->id,
            'permission_code' => $permission->code,
            'permission_name' => $permission->name
        ]);

        return redirect()->route('permissions.show', $permission->id)
            ->with('success', "Permission '{$permission->name}' updated successfully.");
    }

    /**
     * Delete (soft delete) the specified permission.
     */
    public function destroy($id)
    {
        // Check permission
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('permissions.index')
                ->with('error', 'You do not have permission to delete permissions.');
        }

        $permission = Permission::findOrFail($id);

        // Check if permission is assigned to any role
        if ($permission->roles()->count() > 0) {
            return redirect()->route('permissions.index')
                ->with('error', 'Cannot delete permission that is assigned to roles. Remove it from roles first.');
        }

        $permissionName = $permission->name;

        Log::warning('Permission soft deleted', [
            'user_id' => Auth::id(),
            'permission_id' => $permission->id,
            'permission_code' => $permission->code,
            'permission_name' => $permission->name
        ]);

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', "Permission '{$permissionName}' deleted successfully.");
    }

    /**
     * Activate a permission.
     */
    public function activate($id)
    {
        // Check permission
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('permissions.index')
                ->with('error', 'You do not have permission to activate permissions.');
        }

        $permission = Permission::findOrFail($id);

        Log::info('Permission activated', [
            'user_id' => Auth::id(),
            'permission_id' => $permission->id,
            'permission_code' => $permission->code,
            'permission_name' => $permission->name
        ]);

        $permission->update(['is_active' => true]);

        return redirect()->route('permissions.index')
            ->with('success', "Permission '{$permission->name}' activated successfully.");
    }

    /**
     * Deactivate a permission.
     */
    public function deactivate($id)
    {
        // Check permission
        if (!Auth::user()->is_super_admin) {
            return redirect()->route('permissions.index')
                ->with('error', 'You do not have permission to deactivate permissions.');
        }

        $permission = Permission::findOrFail($id);

        Log::info('Permission deactivated', [
            'user_id' => Auth::id(),
            'permission_id' => $permission->id,
            'permission_code' => $permission->code,
            'permission_name' => $permission->name
        ]);

        $permission->update(['is_active' => false]);

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