<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $users = User::with(['role', 'department'])
            ->when($request->filled('role_id'), function ($query) use ($request) {
                return $query->where('role_id', $request->role_id);
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

        return view('users.index', compact('users', 'roles', 'departments'));
    }

    /**
     * Show form to create a new user.
     */
    public function create()
    {
        try {
            // Only super admin or users with can_create_users permission can create users
            if (!Auth::user()->is_super_admin && !Auth::user()->can_create_users) {
                Log::warning('Unauthorized create attempt', ['user_id' => Auth::id()]);
                return redirect()->route('users.index')
                    ->with('error', 'You do not have permission to create users.');
            }

            $roles = Role::where('is_active', true)->orderBy('name')->get();
            $departments = Department::where('is_active', true)->orderBy('name')->get();

            Log::info('Create user form accessed', [
                'user_id' => Auth::id(),
                'roles_count' => $roles->count(),
                'departments_count' => $departments->count()
            ]);

            return view('users.create', compact('roles', 'departments'));
        } catch (\Exception $e) {
            Log::error('Error in create method: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('users.index')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        try {
            if (!Auth::user()->is_super_admin && !Auth::user()->can_create_users) {
                return redirect()->route('users.index')
                    ->with('error', 'You do not have permission to create users.');
            }

            $validated = $request->validate([
                'first_name'       => 'required|string|max:100',
                'last_name'        => 'nullable|string|max:100',
                'email'            => 'required|email|unique:users,email',
                'password'         => 'required|string|min:8',
                'role_id'          => 'required|exists:roles,id',
                'department_id'    => 'nullable|exists:departments,id',
                'is_active'        => 'sometimes|boolean',
                'can_create_users' => 'sometimes|boolean',
            ]);

            // Get the role for logging only
            $role = Role::find($validated['role_id']);

            $validated['password'] = Hash::make($validated['password']);
            $validated['role'] = $validated['role_id'];  // Save INTEGER, same as role_id
            $validated['is_super_admin'] = false;
            $validated['created_by'] = Auth::id();

            $user = User::create($validated);

            Log::info('User created', [
                'user_id' => $user->id,
                'role_id' => $validated['role_id'],
                'role_name' => $role->name
            ]);

            return redirect()->route('users.index')->with('success', 'User created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
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
            Log::info('Show user method called', ['user_id' => $id, 'auth_user' => Auth::id()]);

            $user = User::with(['department', 'creator', 'updater'])->findOrFail($id);
            $roles = Role::where('is_active', true)->orderBy('name')->get();

            return view('users.show', compact('user', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error in show method: ' . $e->getMessage(), [
                'user_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('users.index')->with('error', 'User not found.');
        }
    }

    /**
     * Show form to edit a user.
     */
    public function edit($id)
    {
        try {
            Log::info('Edit method called', ['user_id' => $id, 'auth_user' => Auth::id()]);

            // Only super admin or users with can_create_users permission can edit users
            if (!Auth::user()->is_super_admin && !Auth::user()->can_create_users) {
                Log::warning('Unauthorized edit attempt', ['user_id' => Auth::id(), 'target_user' => $id]);
                return redirect()->route('users.index')
                    ->with('error', 'You do not have permission to edit users.');
            }

            $user = User::findOrFail($id);

            // Cannot edit super admin unless you are super admin
            if ($user->is_super_admin && !Auth::user()->is_super_admin) {
                Log::warning('Attempt to edit super admin', ['user_id' => Auth::id(), 'target_user' => $id]);
                return redirect()->route('users.index')
                    ->with('error', 'You cannot edit a super administrator.');
            }

            $roles = Role::where('is_active', true)->orderBy('name')->get();
            $departments = Department::where('is_active', true)->orderBy('name')->get();

            return view('users.edit', compact('user', 'roles', 'departments'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('User not found in edit method', ['user_id' => $id]);
            return redirect()->route('users.index')->with('error', 'User not found.');
        } catch (\Exception $e) {
            Log::error('Error in edit method: ' . $e->getMessage(), [
                'user_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('users.index')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        try {
            if (!Auth::user()->is_super_admin && !Auth::user()->can_create_users) {
                return redirect()->route('users.index')
                    ->with('error', 'You do not have permission to update users.');
            }

            $user = User::findOrFail($id);

            if ($user->is_super_admin && !Auth::user()->is_super_admin) {
                return redirect()->route('users.index')
                    ->with('error', 'You cannot edit a super administrator.');
            }

            $validated = $request->validate([
                'first_name'       => 'required|string|max:100',
                'last_name'        => 'nullable|string|max:100',
                'email'            => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
                'role_id'          => 'required|exists:roles,id',
                'department_id'    => 'nullable|exists:departments,id',
                'is_active'        => 'sometimes|boolean',
                'can_create_users' => 'sometimes|boolean',
            ]);

            // Get the role for logging only
            $role = Role::find($validated['role_id']);

            $validated['role'] = $validated['role_id'];  // Save INTEGER, same as role_id
            $validated['updated_by'] = Auth::id();

            if ($request->filled('password')) {
                $request->validate(['password' => 'required|string|min:8|confirmed']);
                $validated['password'] = Hash::make($request->password);
            }

            $user->update($validated);

            Log::info('User updated', [
                'user_id' => $user->id,
                'role_id' => $validated['role_id'],
                'role_name' => $role->name
            ]);

            return redirect()->route('users.index')->with('success', 'User updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
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
            if (!Auth::user()->is_super_admin && !Auth::user()->can_create_users) {
                return redirect()->route('users.index')->with('error', 'You do not have permission.');
            }

            $user = User::findOrFail($id);

            $validated = $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user->update([
                'password' => Hash::make($validated['password']),
                'updated_by' => Auth::user()->id
            ]);

            Log::info('Password updated for user', ['user_id' => $id, 'updated_by' => Auth::id()]);

            return redirect()->route('users.edit', $id)->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating password: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update password.')->withInput();
        }
    }

    /**
     * Delete (soft delete) the specified user.
     */
    public function destroy($id)
    {
        try {
            // Only super admin can delete users
            if (!Auth::user()->is_super_admin) {
                Log::warning('Unauthorized delete attempt', ['user_id' => Auth::id(), 'target_user' => $id]);
                return redirect()->route('users.index')
                    ->with('error', 'Only super administrator can delete users.');
            }

            $user = User::findOrFail($id);

            // Cannot delete yourself
            if ($user->id === Auth::user()->id) {
                Log::warning('Attempt to delete own account', ['user_id' => Auth::id()]);
                return redirect()->route('users.index')
                    ->with('error', 'You cannot delete your own account.');
            }

            $user->delete();
            Log::info('User deleted successfully', ['user_id' => $id, 'deleted_by' => Auth::id()]);

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in destroy method: ' . $e->getMessage(), [
                'user_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('users.index')->with('error', 'An error occurred while deleting the user.');
        }
    }

    /**
     * Activate a user.
     */
    public function activate($id)
    {
        try {
            if (!Auth::user()->is_super_admin && !Auth::user()->can_create_users) {
                Log::warning('Unauthorized activate attempt', ['user_id' => Auth::id(), 'target_user' => $id]);
                return redirect()->route('users.index')
                    ->with('error', 'You do not have permission to activate users.');
            }

            $user = User::findOrFail($id);
            $user->update([
                'is_active' => true,
                'updated_by' => Auth::user()->id
            ]);

            Log::info('User activated successfully', ['user_id' => $id, 'activated_by' => Auth::id()]);

            return redirect()->route('users.index')
                ->with('success', 'User activated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in activate method: ' . $e->getMessage(), [
                'user_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('users.index')->with('error', 'An error occurred while activating the user.');
        }
    }

    /**
     * Deactivate a user.
     */
    public function deactivate($id)
    {
        try {
            if (!Auth::user()->is_super_admin && !Auth::user()->can_create_users) {
                Log::warning('Unauthorized deactivate attempt', ['user_id' => Auth::id(), 'target_user' => $id]);
                return redirect()->route('users.index')
                    ->with('error', 'You do not have permission to deactivate users.');
            }

            $user = User::findOrFail($id);

            // Cannot deactivate yourself
            if ($user->id === Auth::user()->id) {
                Log::warning('Attempt to deactivate own account', ['user_id' => Auth::id()]);
                return redirect()->route('users.index')
                    ->with('error', 'You cannot deactivate your own account.');
            }

            $user->update([
                'is_active' => false,
                'updated_by' => Auth::user()->id
            ]);

            Log::info('User deactivated successfully', ['user_id' => $id, 'deactivated_by' => Auth::id()]);

            return redirect()->route('users.index')
                ->with('success', 'User deactivated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in deactivate method: ' . $e->getMessage(), [
                'user_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->route('users.index')->with('error', 'An error occurred while deactivating the user.');
        }
    }
}
