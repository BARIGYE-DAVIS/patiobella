<?php
// app/Http/Controllers/Restaurant/MenuController.php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    /**
     * Display a listing of menu items.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'CAFE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = MenuItem::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $menuItems = $query->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        $categories = MenuItem::getCategories();
        $inventoryItems = InventoryItem::where('is_active', true)->orderBy('name')->get();

        $categoryCounts = MenuItem::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get()
            ->pluck('count', 'category')
            ->toArray();

        return view('restaurant.menu.index', compact('menuItems', 'categories', 'inventoryItems', 'categoryCounts'));
    }

    /**
     * Show the form for creating a new menu item.
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'CAFE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $categories = MenuItem::getCategories();
        $inventoryItems = InventoryItem::where('is_active', true)->orderBy('name')->get();

        // No $menuItem passed - form will be in CREATE mode
        return view('restaurant.menu.form', compact('categories', 'inventoryItems'));
    }

    /**
     * Store a newly created menu item.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'CAFE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:' . implode(',', MenuItem::getCategories()),
            'selling_price' => 'required|numeric|min:0',
            'preparation_time' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
            'allergen_info' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'notes' => 'nullable|string',
            'image_url' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $menuItem = MenuItem::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'category' => $validated['category'],
                'selling_price' => $validated['selling_price'],
                'preparation_time' => $validated['preparation_time'] ?? null,
                'is_active' => $request->has('is_active'),
                'inventory_item_id' => $validated['inventory_item_id'] ?? null,
                'allergen_info' => $validated['allergen_info'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'image_url' => $validated['image_url'] ?? null,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Menu item created', [
                'user_id' => Auth::id(),
                'menu_item_id' => $menuItem->id,
                'menu_item_name' => $menuItem->name,
            ]);

            return redirect()->route('restaurant.menu.index')
                ->with('success', "Menu item '{$menuItem->name}' created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create menu item', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create menu item: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified menu item.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'CAFE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $menuItem = MenuItem::with(['inventoryItem', 'creator', 'updater'])->findOrFail($id);

        return view('restaurant.menu.show', compact('menuItem'));
    }

    /**
     * Show the form for editing the specified menu item.
     */
    public function edit($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'CAFE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $menuItem = MenuItem::findOrFail($id);
        $categories = MenuItem::getCategories();
        $inventoryItems = InventoryItem::where('is_active', true)->orderBy('name')->get();

        // Pass $menuItem - form will be in EDIT mode
        return view('restaurant.menu.form', compact('menuItem', 'categories', 'inventoryItems'));
    }

    /**
     * Update the specified menu item.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'CAFE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $menuItem = MenuItem::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:' . implode(',', MenuItem::getCategories()),
            'selling_price' => 'required|numeric|min:0',
            'preparation_time' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
            'allergen_info' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'notes' => 'nullable|string',
            'image_url' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $menuItem->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'category' => $validated['category'],
                'selling_price' => $validated['selling_price'],
                'preparation_time' => $validated['preparation_time'] ?? null,
                'is_active' => $request->has('is_active'),
                'inventory_item_id' => $validated['inventory_item_id'] ?? null,
                'allergen_info' => $validated['allergen_info'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'image_url' => $validated['image_url'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Menu item updated', [
                'user_id' => Auth::id(),
                'menu_item_id' => $menuItem->id,
                'menu_item_name' => $menuItem->name,
            ]);

            return redirect()->route('restaurant.menu.index')
                ->with('success', "Menu item '{$menuItem->name}' updated successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update menu item', [
                'user_id' => Auth::id(),
                'menu_item_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update menu item: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified menu item.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'CAFE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $menuItem = MenuItem::findOrFail($id);
            $menuItemName = $menuItem->name;
            $menuItem->delete();

            Log::info('Menu item deleted', [
                'user_id' => Auth::id(),
                'menu_item_id' => $id,
                'menu_item_name' => $menuItemName,
            ]);

            return redirect()->route('restaurant.menu.index')
                ->with('success', "Menu item '{$menuItemName}' deleted successfully.");

        } catch (\Exception $e) {
            Log::error('Failed to delete menu item', [
                'user_id' => Auth::id(),
                'menu_item_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to delete menu item: ' . $e->getMessage());
        }
    }

    /**
     * Toggle menu item active status.
     */
    public function toggleStatus($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'CAFE') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $menuItem = MenuItem::findOrFail($id);
            $menuItem->is_active = !$menuItem->is_active;
            $menuItem->updated_by = Auth::id();
            $menuItem->save();

            return response()->json([
                'success' => true,
                'is_active' => $menuItem->is_active,
                'message' => $menuItem->is_active ? 'Item activated' : 'Item deactivated'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle status: ' . $e->getMessage()
            ], 500);
        }
    }
}
