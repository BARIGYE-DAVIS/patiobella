<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemCategory;
use App\Models\Department;
use App\Models\Role;
use App\Models\InventoryItem;
use App\Models\RecipeItem;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    private function getRoleName($user): ?string
    {
        try {
            if ($user->role_id) {
                $role = Role::find($user->role_id);
                return $role->name ?? null;
            }
            return !empty($user->role) ? $user->role : null;
        } catch (\Exception $e) {
            Log::error('Error resolving role name', ['user_id' => $user->id ?? null, 'error' => $e->getMessage()]);
            return null;
        }
    }

    private function checkAuthorization()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return false;
        }

        $roleName = $this->getRoleName($user);
        if ($roleName !== 'General Manager' && $roleName !== 'Admin') {
            return false;
        }

        return true;
    }

    /**
     * Convert quantity to base unit
     */
    private function convertToBaseUnit($quantity, $fromUnit, $toBaseUnit)
    {
        $value = floatval($quantity);

        if ($toBaseUnit === 'kg') {
            if ($fromUnit === 'g')  return $value / 1000;
            if ($fromUnit === 'kg') return $value;
        } elseif ($toBaseUnit === 'litre') {
            if ($fromUnit === 'ml')    return $value / 1000;
            if ($fromUnit === 'litre') return $value;
        } elseif ($toBaseUnit === 'piece') {
            if ($fromUnit === 'piece') return $value;
        }

        return $value;
    }

    /**
     * Display list of all menus
     */
    public function index()
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
        }

        $menus = Menu::with(['department', 'items'])
            ->orderBy('department_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('management.menus.index', compact('menus'));
    }

    /**
     * Show form to create a new menu
     */
    public function create()
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
        }

        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = MenuItemCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $inventoryItems = InventoryItem::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('management.menus.create', compact('departments', 'categories', 'inventoryItems'));
    }

    /**
     * Store a new menu with items and recipe ingredients
     */
    public function store(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
        }

        $validated = $request->validate([
            'name'                                          => 'required|string|max:100',
            'department_id'                                 => 'required|exists:departments,id',
            'description'                                   => 'nullable|string',
            'sort_order'                                    => 'nullable|integer',
            'is_active'                                     => 'nullable|string',

            'items'                                         => 'nullable|array',
            'items.*.name'                                  => 'required_with:items|string|max:255',
            'items.*.selling_price'                         => 'nullable|numeric|min:0',
            'items.*.desired_margin'                        => 'nullable|numeric|min:0|max:99.9',
            'items.*.menu_item_category_id'                 => 'nullable|exists:menu_item_categories,id',
            'items.*.description'                           => 'nullable|string',
            'items.*.is_active'                             => 'nullable|string',
            'items.*.material_cost'                         => 'nullable|numeric',
            'items.*.mark_up'                               => 'nullable|numeric',
            'items.*.age_margins'                           => 'nullable|numeric',
            'items.*.age_cost'                              => 'nullable|numeric',
            'items.*.glovo_selling_price'                   => 'nullable|numeric',
            'items.*.glovo_commission'                      => 'nullable|numeric',
            'items.*.final_margin'                          => 'nullable|numeric',

            'items.*.ingredients'                           => 'nullable|array',
            'items.*.ingredients.*.inventory_item_id'       => 'required_with:items.*.ingredients|exists:inventory_items,id',
            'items.*.ingredients.*.quantity'                => 'required_with:items.*.ingredients|numeric|min:0.001',
            'items.*.ingredients.*.unit'                    => 'required_with:items.*.ingredients|string',
            'items.*.ingredients.*.base_unit'               => 'required_with:items.*.ingredients|string',
            'items.*.ingredients.*.unit_cost'               => 'required_with:items.*.ingredients|numeric',
            'items.*.ingredients.*.wastage_percentage'      => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create the menu
            $menu = Menu::create([
                'name'          => $validated['name'],
                'department_id' => $validated['department_id'],
                'description'   => $validated['description'] ?? null,
                'sort_order'    => $validated['sort_order'] ?? 0,
                'is_active'     => ($request->input('is_active', '1') === '1'),
                'created_by'    => Auth::id(),
            ]);

            // 2. Create menu items and their recipe ingredients
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemIndex => $itemData) {

                    $materialCost = floatval($itemData['material_cost'] ?? 0);
                    $sellingPrice = floatval($itemData['selling_price'] ?? 0);
                    $markUp       = floatval($itemData['mark_up'] ?? 0);
                    $ageMargins   = floatval($itemData['age_margins'] ?? 0);
                    $ageCost      = floatval($itemData['age_cost'] ?? 0);
                    $glovoSellingPrice = floatval($itemData['glovo_selling_price'] ?? 0);
                    $glovoCommission   = floatval($itemData['glovo_commission'] ?? 0);
                    $finalMargin       = floatval($itemData['final_margin'] ?? 0);

                    // Calculate Glovo figures if selling price is provided
                    if ($sellingPrice > 0 && empty($glovoSellingPrice)) {
                        $commissionPct = 20;
                        $glovoSellingPrice = $sellingPrice * (1 + $commissionPct / 100);
                        $glovoCommission = $glovoSellingPrice * ($commissionPct / 100);
                        $finalMargin = $glovoSellingPrice - $materialCost - $glovoCommission;
                    }

                    // Item active status
                    $itemIsActive = !empty($itemData['is_active']) && $itemData['is_active'] === 'on';

                    // FIXED: Only use menu_item_category_id, ignore 'category' column
                    $menuItem = MenuItem::create([
                        'name'                      => $itemData['name'],
                        'description'               => $itemData['description'] ?? null,
                        'menu_id'                   => $menu->id,
                        'menu_item_category_id'     => $itemData['menu_item_category_id'] ?? null,
                        'selling_price'             => $sellingPrice,
                        'is_active'                 => $itemIsActive,
                        'created_by'                => Auth::id(),
                        'm_cost'                    => $materialCost,
                        'mark_up'                   => $markUp,
                        'age_margins'               => $ageMargins,
                        'age_cost'                  => $ageCost,
                        'glovo_selling_price'       => $glovoSellingPrice,
                        'glovo_commission'          => $glovoCommission,
                        'final_margin'              => $finalMargin,
                    ]);

                    // 3. Create recipe ingredients
                    if (!empty($itemData['ingredients'])) {
                        foreach ($itemData['ingredients'] as $ingredientIdx => $ingredientData) {

                            $unitOfMeasure = UnitOfMeasure::where('code', $ingredientData['unit'])
                                ->orWhere('symbol', $ingredientData['unit'])
                                ->first();

                            if (!$unitOfMeasure) {
                                $unitOfMeasure = UnitOfMeasure::create([
                                    'code'      => $ingredientData['unit'],
                                    'name'      => $ingredientData['unit'],
                                    'symbol'    => $ingredientData['unit'],
                                    'is_active' => true,
                                ]);
                            }

                            $qtyInBaseUnit = $this->convertToBaseUnit(
                                $ingredientData['quantity'],
                                $ingredientData['unit'],
                                $ingredientData['base_unit']
                            );

                            RecipeItem::create([
                                'menu_item_id'        => $menuItem->id,
                                'inventory_item_id'   => $ingredientData['inventory_item_id'],
                                'quantity_required'   => $qtyInBaseUnit,
                                'unit_of_measure_id'  => $unitOfMeasure->id,
                                'wastage_percentage'  => $ingredientData['wastage_percentage'] ?? 0,
                                'unit_cost_at_creation' => $ingredientData['unit_cost'],
                                'sort_order'          => $ingredientIdx,
                                'created_at'          => now(),
                                'updated_at'          => now(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            Log::info('Menu created', [
                'user_id'     => Auth::id(),
                'menu_id'     => $menu->id,
                'menu_name'   => $menu->name,
                'items_count' => count($validated['items'] ?? []),
            ]);

            return redirect()->route('management.menus.index')
                ->with('success', "Menu '{$menu->name}' created successfully with " . count($validated['items'] ?? []) . " item(s).")
                ->with('clear_menu_draft', true);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Menu creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create menu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show a specific menu
     */
    public function show($id)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
        }

        $menu = Menu::with(['department', 'items.category', 'items.recipeItems.inventoryItem', 'creator', 'updater'])
            ->findOrFail($id);

        return view('management.menus.show', compact('menu'));
    }

    /**
     * Show form to edit a menu
     */
    public function edit($id)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
        }

        $menu           = Menu::with(['items.recipeItems.inventoryItem', 'items.category'])->findOrFail($id);
        $departments    = Department::where('is_active', true)->orderBy('name')->get();
        $categories     = MenuItemCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $inventoryItems = InventoryItem::where('is_active', true)->orderBy('name')->get();

        return view('management.menus.edit', compact('menu', 'departments', 'categories', 'inventoryItems'));
    }

    /**
     * Update a menu
     */
    public function update(Request $request, $id)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
        }

        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'description'   => 'nullable|string',
            'sort_order'    => 'nullable|integer',
            'is_active'     => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $menu->update([
                'name'          => $validated['name'],
                'department_id' => $validated['department_id'],
                'description'   => $validated['description'] ?? null,
                'sort_order'    => $validated['sort_order'] ?? 0,
                'is_active'     => ($request->input('is_active', '1') === '1'),
                'updated_by'    => Auth::id(),
            ]);

            DB::commit();

            Log::info('Menu updated', ['user_id' => Auth::id(), 'menu_id' => $menu->id]);

            return redirect()->route('management.menus.index')
                ->with('success', "Menu '{$menu->name}' updated successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Menu update failed', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Failed to update menu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete a menu
     */
    public function destroy($id)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
        }

        try {
            $menu     = Menu::findOrFail($id);
            $menuName = $menu->name;
            $menu->delete();

            Log::info('Menu deleted', ['user_id' => Auth::id(), 'menu_id' => $id, 'menu_name' => $menuName]);

            return redirect()->route('management.menus.index')
                ->with('success', "Menu '{$menuName}' deleted successfully.");

        } catch (\Exception $e) {
            Log::error('Menu deletion failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete menu: ' . $e->getMessage());
        }
    }

    /**
     * Toggle menu active status
     */
    public function toggleStatus($id)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $menu             = Menu::findOrFail($id);
            $menu->is_active  = !$menu->is_active;
            $menu->updated_by = Auth::id();
            $menu->save();

            return response()->json([
                'success'   => true,
                'is_active' => $menu->is_active,
                'message'   => $menu->is_active ? 'Menu activated' : 'Menu deactivated',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle status: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =====================================================
    // MENU ITEMS MANAGEMENT
    // =====================================================

    public function items($menuId)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
        }

        $menu      = Menu::with(['department'])->findOrFail($menuId);
        $menuItems = MenuItem::with(['category', 'recipeItems.inventoryItem'])
            ->where('menu_id', $menuId)
            ->orderBy('menu_item_category_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categories     = MenuItemCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $inventoryItems = InventoryItem::where('is_active', true)->orderBy('name')->get();
        $unitsOfMeasure = UnitOfMeasure::where('is_active', true)->orderBy('name')->get();

        return view('management.menus.items', compact('menu', 'menuItems', 'categories', 'inventoryItems', 'unitsOfMeasure'));
    }

    public function storeItem(Request $request, $menuId)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'description'           => 'nullable|string',
            'menu_item_category_id' => 'nullable|exists:menu_item_categories,id',
            'selling_price'         => 'required|numeric|min:0',
            'preparation_time'      => 'nullable|integer|min:0',
            'inventory_item_id'     => 'nullable|exists:inventory_items,id',
            'allergen_info'         => 'nullable|string',
            'sort_order'            => 'nullable|integer',
            'is_active'             => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $menuItem = MenuItem::create([
                'name'                  => $validated['name'],
                'description'           => $validated['description'] ?? null,
                'menu_id'               => $menuId,
                'menu_item_category_id' => $validated['menu_item_category_id'] ?? null,
                'selling_price'         => $validated['selling_price'],
                'preparation_time'      => $validated['preparation_time'] ?? null,
                'inventory_item_id'     => $validated['inventory_item_id'] ?? null,
                'allergen_info'         => $validated['allergen_info'] ?? null,
                'sort_order'            => $validated['sort_order'] ?? 0,
                'is_active'             => !empty($validated['is_active']),
                'created_by'            => Auth::id(),
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Menu item added successfully', 'item' => $menuItem]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to add menu item: ' . $e->getMessage()], 500);
        }
    }

    public function updateItem(Request $request, $menuId, $itemId)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $menuItem  = MenuItem::where('menu_id', $menuId)->findOrFail($itemId);
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'description'           => 'nullable|string',
            'menu_item_category_id' => 'nullable|exists:menu_item_categories,id',
            'selling_price'         => 'required|numeric|min:0',
            'preparation_time'      => 'nullable|integer|min:0',
            'inventory_item_id'     => 'nullable|exists:inventory_items,id',
            'allergen_info'         => 'nullable|string',
            'sort_order'            => 'nullable|integer',
            'is_active'             => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $menuItem->update([
                'name'                  => $validated['name'],
                'description'           => $validated['description'] ?? null,
                'menu_item_category_id' => $validated['menu_item_category_id'] ?? null,
                'selling_price'         => $validated['selling_price'],
                'preparation_time'      => $validated['preparation_time'] ?? null,
                'inventory_item_id'     => $validated['inventory_item_id'] ?? null,
                'allergen_info'         => $validated['allergen_info'] ?? null,
                'sort_order'            => $validated['sort_order'] ?? 0,
                'is_active'             => !empty($validated['is_active']),
                'updated_by'            => Auth::id(),
            ]);

            $this->updateMenuItemMaterialCost($menuItem->id);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Menu item updated successfully', 'item' => $menuItem->fresh()]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update menu item: ' . $e->getMessage()], 500);
        }
    }

    public function deleteItem($menuId, $itemId)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $menuItem     = MenuItem::where('menu_id', $menuId)->findOrFail($itemId);
            $menuItemName = $menuItem->name;
            $menuItem->delete();

            return response()->json(['success' => true, 'message' => "Menu item '{$menuItemName}' deleted successfully"]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete menu item: ' . $e->getMessage()], 500);
        }
    }

    public function toggleItemStatus($menuId, $itemId)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $menuItem             = MenuItem::where('menu_id', $menuId)->findOrFail($itemId);
            $menuItem->is_active  = !$menuItem->is_active;
            $menuItem->updated_by = Auth::id();
            $menuItem->save();

            return response()->json([
                'success'   => true,
                'is_active' => $menuItem->is_active,
                'message'   => $menuItem->is_active ? 'Item activated' : 'Item deactivated',
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to toggle status: ' . $e->getMessage()], 500);
        }
    }

    // =====================================================
    // RECIPE ITEMS MANAGEMENT
    // =====================================================

    public function getRecipeItems($menuId, $itemId)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $menuItem    = MenuItem::where('menu_id', $menuId)->findOrFail($itemId);
        $recipeItems = RecipeItem::with(['inventoryItem', 'unitOfMeasure'])
            ->where('menu_item_id', $itemId)
            ->orderBy('sort_order')
            ->get();

        $materialCost = 0;
        foreach ($recipeItems as $ri) {
            if ($ri->inventoryItem) {
                $cost = $ri->quantity_required * $ri->inventoryItem->unit_cost;
                if ($ri->wastage_percentage > 0) {
                    $cost *= (1 + $ri->wastage_percentage / 100);
                }
                $materialCost += $cost;
            }
        }

        return response()->json([
            'success'        => true,
            'recipe_items'   => $recipeItems,
            'material_cost'  => round($materialCost, 2),
            'selling_price'  => $menuItem->selling_price,
            'current_margin' => $menuItem->selling_price > 0
                ? round((($menuItem->selling_price - $materialCost) / $menuItem->selling_price) * 100, 2)
                : 0,
        ]);
    }

    public function storeRecipeItem(Request $request, $menuId, $itemId)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $menuItem  = MenuItem::where('menu_id', $menuId)->findOrFail($itemId);
        $validated = $request->validate([
            'inventory_item_id'  => 'required|exists:inventory_items,id',
            'quantity_required'  => 'required|numeric|min:0.001',
            'unit_of_measure_id' => 'required|exists:units_of_measure,id',
            'wastage_percentage' => 'nullable|numeric|min:0|max:100',
            'sort_order'         => 'nullable|integer',
            'notes'              => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $existing = RecipeItem::where('menu_item_id', $itemId)
                ->where('inventory_item_id', $validated['inventory_item_id'])
                ->first();

            if ($existing) {
                return response()->json(['success' => false, 'message' => 'This ingredient is already added to this menu item'], 422);
            }

            $inventoryItem = InventoryItem::find($validated['inventory_item_id']);

            $recipeItem = RecipeItem::create([
                'menu_item_id'          => $itemId,
                'inventory_item_id'     => $validated['inventory_item_id'],
                'quantity_required'     => $validated['quantity_required'],
                'unit_of_measure_id'    => $validated['unit_of_measure_id'],
                'wastage_percentage'    => $validated['wastage_percentage'] ?? 0,
                'unit_cost_at_creation' => $inventoryItem->unit_cost,
                'sort_order'            => $validated['sort_order'] ?? 0,
                'notes'                 => $validated['notes'] ?? null,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            $this->updateMenuItemMaterialCost($itemId);
            DB::commit();

            return response()->json([
                'success'     => true,
                'message'     => 'Ingredient added successfully',
                'recipe_item' => $recipeItem->load(['inventoryItem', 'unitOfMeasure']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to add ingredient: ' . $e->getMessage()], 500);
        }
    }

    public function updateRecipeItem(Request $request, $menuId, $itemId, $recipeItemId)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $recipeItem = RecipeItem::where('menu_item_id', $itemId)->findOrFail($recipeItemId);
        $validated  = $request->validate([
            'quantity_required'  => 'required|numeric|min:0.001',
            'unit_of_measure_id' => 'required|exists:units_of_measure,id',
            'wastage_percentage' => 'nullable|numeric|min:0|max:100',
            'sort_order'         => 'nullable|integer',
            'notes'              => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $inventoryItem = InventoryItem::find($recipeItem->inventory_item_id);

            $recipeItem->update([
                'quantity_required'     => $validated['quantity_required'],
                'unit_of_measure_id'    => $validated['unit_of_measure_id'],
                'wastage_percentage'    => $validated['wastage_percentage'] ?? 0,
                'unit_cost_at_creation' => $inventoryItem->unit_cost,
                'sort_order'            => $validated['sort_order'] ?? 0,
                'notes'                 => $validated['notes'] ?? null,
                'updated_at'            => now(),
            ]);

            $this->updateMenuItemMaterialCost($itemId);
            DB::commit();

            return response()->json([
                'success'     => true,
                'message'     => 'Ingredient updated successfully',
                'recipe_item' => $recipeItem->load(['inventoryItem', 'unitOfMeasure']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update ingredient: ' . $e->getMessage()], 500);
        }
    }

    public function deleteRecipeItem($menuId, $itemId, $recipeItemId)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $recipeItem = RecipeItem::where('menu_item_id', $itemId)->findOrFail($recipeItemId);
            $recipeItem->delete();

            $this->updateMenuItemMaterialCost($itemId);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Ingredient removed successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to remove ingredient: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update menu item material cost (m_cost) and margin (age_margins)
     */
    private function updateMenuItemMaterialCost($menuItemId)
    {
        $recipeItems  = RecipeItem::where('menu_item_id', $menuItemId)->get();
        $materialCost = 0;

        foreach ($recipeItems as $ri) {
            if ($ri->inventoryItem) {
                $cost = $ri->quantity_required * $ri->inventoryItem->unit_cost;
                if ($ri->wastage_percentage > 0) {
                    $cost *= (1 + $ri->wastage_percentage / 100);
                }
                $materialCost += $cost;
            }
        }

        $menuItem = MenuItem::find($menuItemId);
        if ($menuItem) {
            $margin = 0;
            if ($menuItem->selling_price > 0 && $materialCost > 0) {
                $margin = (($menuItem->selling_price - $materialCost) / $menuItem->selling_price) * 100;
            }

            $menuItem->update([
                'm_cost'      => round($materialCost, 2),
                'age_margins' => round($margin, 2),
            ]);
        }
    }

    public function recalculateAllCosts()
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
        }

        try {
            $menuItems = MenuItem::where('is_active', true)->get();
            foreach ($menuItems as $menuItem) {
                $this->updateMenuItemMaterialCost($menuItem->id);
            }
            return redirect()->back()->with('success', "Recalculated costs for {$menuItems->count()} menu items.");

        } catch (\Exception $e) {
            Log::error('Bulk cost recalculation failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to recalculate costs: ' . $e->getMessage());
        }
    }

    public function getInventoryItems(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $search = $request->get('q', '');
        $query  = InventoryItem::where('is_active', true);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->limit(50)->get();

        return response()->json([
            'success' => true,
            'items'   => $items->map(fn($item) => [
                'id'            => $item->id,
                'name'          => $item->name,
                'item_code'     => $item->item_code,
                'base_unit'     => $item->base_unit,
                'unit_cost'     => $item->unit_cost,
                'current_stock' => $item->current_stock,
            ]),
        ]);
    }

    // =====================================================
    // STANDALONE MENU ITEMS PAGE (READ-ONLY)
    // =====================================================

    public function allItems()
    {
        $menuItems = MenuItem::with(['menu', 'category', 'recipeItems.inventoryItem'])
            ->orderBy('name')
            ->get();

        $categories = MenuItemCategory::where('is_active', true)->get();
        $menus = Menu::where('is_active', true)->get();

        return view('management.menu-items.index', compact('menuItems', 'categories', 'menus'));
    }

    public function getMenuItem($id)
    {
        try {
            $item = MenuItem::with(['menu', 'category'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'item' => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'selling_price' => $item->selling_price,
                    'menu_item_category_id' => $item->menu_item_category_id,
                    'category_name' => $item->category->name ?? 'Uncategorized',
                    'menu_name' => $item->menu->name ?? 'No Menu',
                    'is_active' => $item->is_active,
                    'allergen_info' => $item->allergen_info,
                    'm_cost' => $item->m_cost,
                    'age_margins' => $item->age_margins,
                    'glovo_selling_price' => $item->glovo_selling_price ?? ($item->selling_price * 1.2),
                    'glovo_commission' => $item->glovo_commission,
                    'final_margin' => $item->final_margin,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }
    }

    public function getMenuItemRecipe($id)
    {
        try {
            $menuItem = MenuItem::findOrFail($id);
            $recipeItems = RecipeItem::with(['inventoryItem', 'unitOfMeasure'])
                ->where('menu_item_id', $id)
                ->get();

            $materialCost = 0;
            $ingredients = [];

            foreach ($recipeItems as $ri) {
                if ($ri->inventoryItem) {
                    $cost = $ri->quantity_required * $ri->inventoryItem->unit_cost;
                    if ($ri->wastage_percentage > 0) {
                        $cost *= (1 + $ri->wastage_percentage / 100);
                    }
                    $materialCost += $cost;

                    $ingredients[] = [
                        'name' => $ri->inventoryItem->name,
                        'quantity' => $ri->quantity_required,
                        'unit' => $ri->inventoryItem->base_unit,
                        'unit_cost' => $ri->inventoryItem->unit_cost,
                        'total_cost' => $cost,
                        'wastage_percentage' => $ri->wastage_percentage,
                    ];
                }
            }

            $commissionPct = 20;
            $glovoSellingPrice = $menuItem->glovo_selling_price ?? ($menuItem->selling_price * (1 + $commissionPct / 100));
            $glovoCommission = $menuItem->glovo_commission ?? ($glovoSellingPrice * ($commissionPct / 100));
            $finalMargin = $menuItem->final_margin ?? ($glovoSellingPrice - $materialCost - $glovoCommission);
            $margin = ($menuItem->selling_price > 0 && $materialCost > 0)
                ? (($menuItem->selling_price - $materialCost) / $menuItem->selling_price) * 100
                : 0;

            return response()->json([
                'success' => true,
                'item' => [
                    'id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'description' => $menuItem->description,
                    'selling_price' => $menuItem->selling_price,
                    'material_cost' => $materialCost,
                    'margin' => $margin,
                    'glovo_selling_price' => $glovoSellingPrice,
                    'glovo_commission' => $glovoCommission,
                    'final_margin' => $finalMargin,
                    'allergen_info' => $menuItem->allergen_info,
                ],
                'ingredients' => $ingredients
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to load recipe: ' . $e->getMessage()], 500);
        }
    }
}
