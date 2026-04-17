<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index(Request $request)
    {
        Log::info('Department index accessed', [
            'user_id' => Auth::id(),
            'filters' => $request->only(['search'])
        ]);

        $query = Department::query();
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('Location', 'like', "%{$search}%")
                  ->orWhere('Description', 'like', "%{$search}%");
            });
        }
        
        $departments = $query->orderBy('code')->paginate(15);
        
        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        Log::info('Department create form accessed', [
            'user_id' => Auth::id()
        ]);
        
        return view('departments.create');
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        Log::info('Attempting to create department', [
            'user_id' => Auth::id(),
            'data' => $request->only(['code', 'name', 'Location', 'Description'])
        ]);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:departments,code',
            'name' => 'required|string|max:255',
            'Location' => 'nullable|string|max:255',
            'Description' => 'nullable|string',
        ]);
        
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;
        
        $department = Department::create($validated);

        Log::info('Department created successfully', [
            'user_id' => Auth::id(),
            'department_id' => $department->id,
            'department_code' => $department->code,
            'department_name' => $department->name
        ]);
        
        return redirect()->route('departments.index')
            ->with('success', "Department '{$department->name}' created successfully.");
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department)
    {
        Log::info('Department viewed', [
            'user_id' => Auth::id(),
            'department_id' => $department->id,
            'department_code' => $department->code,
            'department_name' => $department->name
        ]);
        
        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department)
    {
        Log::info('Department edit form accessed', [
            'user_id' => Auth::id(),
            'department_id' => $department->id,
            'department_code' => $department->code,
            'department_name' => $department->name
        ]);
        
        return view('departments.edit', compact('department'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, Department $department)
    {
        Log::info('Attempting to update department', [
            'user_id' => Auth::id(),
            'department_id' => $department->id,
            'department_code' => $department->code,
            'old_name' => $department->name,
            'new_data' => $request->only(['code', 'name', 'Location', 'Description'])
        ]);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'name' => 'required|string|max:255',
            'Location' => 'nullable|string|max:255',
            'Description' => 'nullable|string',
        ]);
        
        $validated['updated_by'] = Auth::id();
        
        $department->update($validated);

        Log::info('Department updated successfully', [
            'user_id' => Auth::id(),
            'department_id' => $department->id,
            'department_code' => $department->code,
            'old_name' => $department->getOriginal('name'),
            'new_name' => $department->name
        ]);
        
        return redirect()->route('departments.show', $department)
            ->with('success', "Department '{$department->name}' updated successfully.");
    }

    /**
     * Remove the specified department from storage (soft delete).
     */
    public function destroy(Department $department)
    {
        Log::warning('Department soft deleted', [
            'user_id' => Auth::id(),
            'department_id' => $department->id,
            'department_code' => $department->code,
            'department_name' => $department->name
        ]);

        $departmentName = $department->name;
        $department->delete();
        
        return redirect()->route('departments.index')
            ->with('success', "Department '{$departmentName}' deleted successfully.");
    }
    
    /**
     * Activate the specified department.
     */
    public function activate(Department $department)
    {
        Log::info('Department activated', [
            'user_id' => Auth::id(),
            'department_id' => $department->id,
            'department_code' => $department->code,
            'department_name' => $department->name
        ]);

        $department->update([
            'is_active' => true,
            'updated_by' => Auth::id()
        ]);
        
        return redirect()->route('departments.show', $department)
            ->with('success', "Department '{$department->name}' activated successfully.");
    }
    
    /**
     * Deactivate the specified department.
     */
    public function deactivate(Department $department)
    {
        Log::info('Department deactivated', [
            'user_id' => Auth::id(),
            'department_id' => $department->id,
            'department_code' => $department->code,
            'department_name' => $department->name
        ]);

        $department->update([
            'is_active' => false,
            'updated_by' => Auth::id()
        ]);
        
        return redirect()->route('departments.show', $department)
            ->with('success', "Department '{$department->name}' deactivated successfully.");
    }
    
    /**
     * Restore a soft-deleted department.
     */
    public function restore($id)
    {
        Log::info('Attempting to restore department', [
            'user_id' => Auth::id(),
            'department_id' => $id
        ]);

        $department = Department::withTrashed()->findOrFail($id);
        $department->restore();
        $department->update(['updated_by' => Auth::id()]);

        Log::info('Department restored successfully', [
            'user_id' => Auth::id(),
            'department_id' => $department->id,
            'department_code' => $department->code,
            'department_name' => $department->name
        ]);
        
        return redirect()->route('departments.show', $department)
            ->with('success', "Department '{$department->name}' restored successfully.");
    }
    
    /**
     * Permanently delete a department.
     */
    public function forceDelete($id)
    {
        Log::alert('Department permanently deleted', [
            'user_id' => Auth::id(),
            'department_id' => $id
        ]);

        $department = Department::withTrashed()->findOrFail($id);
        $departmentName = $department->name;
        $department->forceDelete();

        Log::alert('Department permanently deleted confirmed', [
            'user_id' => Auth::id(),
            'department_id' => $id,
            'department_name' => $departmentName
        ]);
        
        return redirect()->route('departments.index')
            ->with('success', "Department '{$departmentName}' permanently deleted.");
    }
}