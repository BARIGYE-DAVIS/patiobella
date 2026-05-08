<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    // Display a listing of categories
    public function index()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $categories = Category::orderBy('name')->paginate(20);

        return view('store.categories.index', compact('categories'));
    }

    // Show form to create a new category
    public function create()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        return view('store.categories.create');
    }

    // Store a newly created category
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'code' => 'nullable|string|max:50|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Category::create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('store.categories.index')
            ->with('success', 'Category created successfully.');
    }

    // Show form to edit a category
    public function edit($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $category = Category::findOrFail($id);

        return view('store.categories.edit', compact('category'));
    }

    // Update the specified category
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $id,
            'code' => 'nullable|string|max:50|unique:categories,code,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('store.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    // Delete the specified category
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $category = Category::findOrFail($id);

        // Check if category has items
        if ($category->inventoryItems()->count() > 0) {
            return redirect()->route('store.categories.index')
                ->with('error', 'Cannot delete category because it has items associated with it.');
        }

        $category->delete();

        return redirect()->route('store.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
