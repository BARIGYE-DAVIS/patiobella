<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\MenuItemCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MenuItemCategoryController extends Controller
{
    public function index()
    {
        $categories = MenuItemCategory::withCount('menuItems')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('management.menu-item-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:menu_item_categories,name',
            'code' => 'nullable|string|max:50|unique:menu_item_categories,code',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            DB::beginTransaction();

            $code = $validated['code'] ?? strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $validated['name']), 0, 10));

            $category = MenuItemCategory::create([
                'name' => $validated['name'],
                'code' => $code,
                'description' => $validated['description'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $request->has('is_active'),
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Category created successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category creation failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to create category'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $category = MenuItemCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:menu_item_categories,name,' . $id,
            'code' => 'nullable|string|max:50|unique:menu_item_categories,code,' . $id,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            DB::beginTransaction();

            $category->update([
                'name' => $validated['name'],
                'code' => $validated['code'] ?? $category->code,
                'description' => $validated['description'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $request->has('is_active'),
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Category updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category update failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update category'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = MenuItemCategory::findOrFail($id);

            // Check if category has menu items
            if ($category->menuItems()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with ' . $category->menuItems()->count() . ' menu items. Remove or reassign items first.'
                ], 422);
            }

            $category->delete();

            return response()->json(['success' => true, 'message' => 'Category deleted successfully']);

        } catch (\Exception $e) {
            Log::error('Category deletion failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete category'], 500);
        }
    }
}
