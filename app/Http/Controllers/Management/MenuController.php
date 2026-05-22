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

    // =====================================================
    // MENUS MANAGEMENT (Simplified - No Items)
    // =====================================================

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

        return view('management.menus.create', compact('departments'));
    }

    /**
     * Store a new menu (without items)
     */
    public function store(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
        }

        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'description'   => 'nullable|string',
            'sort_order'    => 'nullable|integer',
            'is_active'     => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $menu = Menu::create([
                'name'          => $validated['name'],
                'department_id' => $validated['department_id'],
                'description'   => $validated['description'] ?? null,
                'sort_order'    => $validated['sort_order'] ?? 0,
                'is_active'     => ($request->input('is_active', '1') === '1'),
                'created_by'    => Auth::id(),
            ]);

            DB::commit();

            Log::info('Menu created', [
                'user_id'     => Auth::id(),
                'menu_id'     => $menu->id,
                'menu_name'   => $menu->name,
            ]);

            return redirect()->route('management.menus.index')
                ->with('success', "Menu '{$menu->name}' created successfully. You can now add items to it.");

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

        $menu        = Menu::findOrFail($id);
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('management.menus.edit', compact('menu', 'departments'));
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
    // MENU ITEMS MANAGEMENT (Standalone)
    // =====================================================

    /**
     * Display all menu items across all menus (with filters)
     */
    public function allItems(Request $request)
    {
        $query = MenuItem::with(['menu', 'category', 'recipeItems.inventoryItem']);

        // Filter by menu if provided
        if ($request->has('menu_id') && $request->menu_id) {
            $query->where('menu_id', $request->menu_id);
        }

        // Filter by category if provided
        if ($request->has('category_id') && $request->category_id) {
            $query->where('menu_item_category_id', $request->category_id);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $menuItems = $query->orderBy('name')->get();
        $categories = MenuItemCategory::where('is_active', true)->get();
        $menus = Menu::where('is_active', true)->get();

        return view('management.menu-items.index', compact('menuItems', 'categories', 'menus'));
    }

    /**
     * Show form to create a new menu item
     */
    public function createItem()
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
        }

        $menus = Menu::with('department')->where('is_active', true)->orderBy('name')->get();
        $categories = MenuItemCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $inventoryItems = InventoryItem::where('is_active', true)->orderBy('name')->get();

        return view('management.menu-items.create', compact('menus', 'categories', 'inventoryItems'));
    }

    /**
     * Store a new menu item (standalone)
     */
    public function storeItemStandalone(Request $request)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
        }

        $validated = $request->validate([
            'menu_id'                           => 'required|exists:menus,id',
            'name'                              => 'required|string|max:255',
            'description'                       => 'nullable|string',
            'menu_item_category_id'             => 'nullable|exists:menu_item_categories,id',
            'allergen_info'                     => 'nullable|string',
            'is_active'                         => 'nullable|string',
            'selling_price'                     => 'nullable|numeric|min:0',
            'desired_margin'                    => 'nullable|numeric|min:0|max:99.9',
            'glovo_commission_percentage'       => 'nullable|numeric|min:0',
            'glovo_selling_price'               => 'nullable|numeric',
            'glovo_commission'                  => 'nullable|numeric',
            'final_margin'                      => 'nullable|numeric',
            'mark_up'                           => 'nullable|numeric',
            'age_margins'                       => 'nullable|numeric',
            'age_cost'                          => 'nullable|numeric',
            'material_cost'                     => 'nullable|numeric',
            'ingredients'                       => 'nullable|array',
            'ingredients.*.inventory_item_id'   => 'required_with:ingredients|exists:inventory_items,id',
            'ingredients.*.quantity'            => 'required_with:ingredients|numeric|min:0.001',
            'ingredients.*.unit'                => 'required_with:ingredients|string',
            'ingredients.*.base_unit'           => 'required_with:ingredients|string',
            'ingredients.*.unit_cost'           => 'required_with:ingredients|numeric',
            'ingredients.*.wastage_percentage'  => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            $materialCost = floatval($validated['material_cost'] ?? 0);
            $sellingPrice = floatval($validated['selling_price'] ?? 0);
            $markUp       = floatval($validated['mark_up'] ?? 0);
            $ageMargins   = floatval($validated['age_margins'] ?? 0);
            $ageCost      = floatval($validated['age_cost'] ?? 0);
            $glovoSellingPrice = floatval($validated['glovo_selling_price'] ?? 0);
            $glovoCommission   = floatval($validated['glovo_commission'] ?? 0);
            $finalMargin       = floatval($validated['final_margin'] ?? 0);

            // Calculate Glovo figures if selling price is provided
            if ($sellingPrice > 0 && empty($glovoSellingPrice)) {
                $commissionPct = floatval($validated['glovo_commission_percentage'] ?? 20);
                $glovoSellingPrice = $sellingPrice * (1 + $commissionPct / 100);
                $glovoCommission = $glovoSellingPrice * ($commissionPct / 100);
                $finalMargin = $glovoSellingPrice - $materialCost - $glovoCommission;
            }

            $itemIsActive = !empty($validated['is_active']) && $validated['is_active'] === '1';

            $menuItem = MenuItem::create([
                'name'                      => $validated['name'],
                'description'               => $validated['description'] ?? null,
                'menu_id'                   => $validated['menu_id'],
                'menu_item_category_id'     => $validated['menu_item_category_id'] ?? null,
                'selling_price'             => $sellingPrice,
                'is_active'                 => $itemIsActive,
                'allergen_info'             => $validated['allergen_info'] ?? null,
                'created_by'                => Auth::id(),
                'm_cost'                    => $materialCost,
                'mark_up'                   => $markUp,
                'age_margins'               => $ageMargins,
                'age_cost'                  => $ageCost,
                'glovo_selling_price'       => $glovoSellingPrice,
                'glovo_commission'          => $glovoCommission,
                'final_margin'              => $finalMargin,
            ]);

            // Create recipe ingredients
            if (!empty($validated['ingredients'])) {
                foreach ($validated['ingredients'] as $ingredientIdx => $ingredientData) {

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

            DB::commit();

            Log::info('Menu item created', [
                'user_id'       => Auth::id(),
                'menu_item_id'  => $menuItem->id,
                'menu_item_name'=> $menuItem->name,
                'menu_id'       => $validated['menu_id'],
            ]);

            return redirect()->route('management.menu-items.index')
                ->with('success', "Menu item '{$menuItem->name}' created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Menu item creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create menu item: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show form to edit a menu item
     */
    public function editItem($id)
    {
        if (!$this->checkAuthorization()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
        }

        $menuItem = MenuItem::with(['menu', 'category', 'recipeItems.inventoryItem'])->findOrFail($id);
        $menus = Menu::with('department')->where('is_active', true)->orderBy('name')->get();
        $categories = MenuItemCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $inventoryItems = InventoryItem::where('is_active', true)->orderBy('name')->get();

        return view('management.menu-items.edit', compact('menuItem', 'menus', 'categories', 'inventoryItems'));
    }

    /**
     * Get a single menu item (AJAX)
     */
    public function getMenuItem($id)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

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
                    'category_name' => $item->category?->name ?? 'Uncategorized',
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

    /**
     * Update a menu item (AJAX)
     */
   /**
 * Update a menu item (Form Submission - NOT AJAX)
 */
/**
 * Update a menu item (Form Submission)
 */
public function updateMenuItem(Request $request, $id)
{
    if (!$this->checkAuthorization()) {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access. Manager only.');
    }

    try {
        $menuItem = MenuItem::findOrFail($id);

        // Log the incoming request to debug
        Log::info('Update Menu Item Request', [
            'id' => $id,
            'name' => $request->input('name'),
            'all_data' => $request->all()
        ]);

        // Get pricing mode
        $pricingMode = $request->input('pricing_mode', 'price');

        // Validate based on pricing mode
        $rules = [
            'name'                  => 'required|string|max:255',
            'menu_id'               => 'required|exists:menus,id',
            'description'           => 'nullable|string',
            'menu_item_category_id' => 'nullable|exists:menu_item_categories,id',
            'allergen_info'         => 'nullable|string',
            'is_active'             => 'nullable|string',
            'glovo_commission_percentage' => 'nullable|numeric|min:0',
        ];

        if ($pricingMode === 'price') {
            $rules['selling_price'] = 'required|numeric|min:0';
            $rules['desired_margin'] = 'nullable|numeric|min:0|max:99.9';
        } else {
            $rules['selling_price'] = 'nullable|numeric|min:0';
            $rules['desired_margin'] = 'required|numeric|min:0.1|max:99.9';
        }

        $validated = $request->validate($rules);

        // Calculate material cost from ingredients
        $materialCost = 0;
        if ($request->has('ingredients')) {
            foreach ($request->ingredients as $ingredientData) {
                if (isset($ingredientData['quantity']) && $ingredientData['quantity'] > 0) {
                    $qtyInBaseUnit = $this->convertToBaseUnit(
                        $ingredientData['quantity'],
                        $ingredientData['unit'],
                        $ingredientData['base_unit']
                    );
                    $materialCost += $qtyInBaseUnit * $ingredientData['unit_cost'] * (1 + ($ingredientData['wastage_percentage'] ?? 0) / 100);
                }
            }
        }

        // Calculate selling price and margin
        $sellingPrice = 0;
        $ageMargins = 0;

        if ($pricingMode === 'price') {
            $sellingPrice = floatval($validated['selling_price'] ?? 0);
            if ($sellingPrice > 0 && $materialCost > 0) {
                $ageMargins = (($sellingPrice - $materialCost) / $sellingPrice) * 100;
            }
        } else {
            $desiredMargin = floatval($validated['desired_margin'] ?? 0);
            if ($desiredMargin > 0 && $desiredMargin < 100 && $materialCost > 0) {
                $sellingPrice = $materialCost / (1 - $desiredMargin / 100);
                $ageMargins = $desiredMargin;
            }
        }

        $markUp = $sellingPrice - $materialCost;
        $ageCost = ($sellingPrice > 0 && $materialCost > 0) ? ($materialCost / $sellingPrice) * 100 : 0;

        // Glovo calculations
        $commissionPct = floatval($validated['glovo_commission_percentage'] ?? 20);
        $glovoSellingPrice = $sellingPrice * (1 + $commissionPct / 100);
        $glovoCommission = $glovoSellingPrice * ($commissionPct / 100);
        $finalMargin = $glovoSellingPrice - $materialCost - $glovoCommission;

        $itemIsActive = !empty($validated['is_active']) && $validated['is_active'] === '1';

        // Update menu item - MAKE SURE NAME IS INCLUDED
        $updateData = [
            'name'                      => $validated['name'],  // THIS IS THE FIX - name is explicitly set
            'menu_id'                   => $validated['menu_id'],
            'description'               => $validated['description'] ?? null,
            'menu_item_category_id'     => $validated['menu_item_category_id'] ?? null,
            'selling_price'             => round($sellingPrice, 2),
            'is_active'                 => $itemIsActive,
            'allergen_info'             => $validated['allergen_info'] ?? null,
            'updated_by'                => Auth::id(),
            'm_cost'                    => round($materialCost, 2),
            'mark_up'                   => round($markUp, 2),
            'age_margins'               => round($ageMargins, 2),
            'age_cost'                  => round($ageCost, 2),
            'glovo_selling_price'       => round($glovoSellingPrice, 2),
            'glovo_commission'          => round($glovoCommission, 2),
            'final_margin'              => round($finalMargin, 2),
        ];

        $menuItem->update($updateData);

        Log::info('Menu Item Updated', ['new_name' => $menuItem->name, 'id' => $menuItem->id]);

        // Handle ingredients (update existing, create new, delete removed)
        $existingRecipeIds = $menuItem->recipeItems->pluck('id')->toArray();
        $processedIds = [];

        if ($request->has('ingredients')) {
            foreach ($request->ingredients as $ingredientData) {
                if (isset($ingredientData['recipe_id']) && !empty($ingredientData['recipe_id'])) {
                    $recipeItem = RecipeItem::find($ingredientData['recipe_id']);
                    if ($recipeItem && $recipeItem->menu_item_id == $menuItem->id) {
                        $qtyInBaseUnit = $this->convertToBaseUnit(
                            $ingredientData['quantity'],
                            $ingredientData['unit'],
                            $ingredientData['base_unit']
                        );
                        $recipeItem->update([
                            'quantity_required'   => $qtyInBaseUnit,
                            'wastage_percentage'  => $ingredientData['wastage_percentage'] ?? 0,
                            'unit_cost_at_creation' => $ingredientData['unit_cost'],
                        ]);
                        $processedIds[] = $recipeItem->id;
                    }
                } elseif (isset($ingredientData['inventory_item_id']) && !empty($ingredientData['inventory_item_id'])) {
                    $unitOfMeasure = UnitOfMeasure::where('code', $ingredientData['unit'])
                        ->orWhere('symbol', $ingredientData['unit'])
                        ->first();
                    if (!$unitOfMeasure) {
                        $unitOfMeasure = UnitOfMeasure::create([
                            'code' => $ingredientData['unit'],
                            'name' => $ingredientData['unit'],
                            'symbol' => $ingredientData['unit'],
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
                        'sort_order' => 0,
                    ]);
                }
            }
        }

        // Delete removed ingredients
        $deletedIds = array_diff($existingRecipeIds, $processedIds);
        if (!empty($deletedIds)) {
            RecipeItem::whereIn('id', $deletedIds)->delete();
        }

        // Final recalculation
        $this->updateMenuItemMaterialCost($menuItem->id);

        return redirect()->route('management.menu-items.index')
            ->with('success', "Menu item '{$menuItem->name}' updated successfully.");

    } catch (\Exception $e) {
        Log::error('Menu item update failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->back()
            ->with('error', 'Failed to update menu item: ' . $e->getMessage())
            ->withInput();
    }
}
    /**
     * Delete a menu item (AJAX)
     */
    public function deleteMenuItem($id)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $menuItem = MenuItem::findOrFail($id);
            $menuItemName = $menuItem->name;
            $menuItem->delete();

            return response()->json([
                'success' => true,
                'message' => "Menu item '{$menuItemName}' deleted successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete menu item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get item recipe with full details for modal (AJAX)
     */
    public function getMenuItemRecipe($id)
    {
        if (!$this->checkAuthorization()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

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

    // =====================================================
    // RECIPE ITEMS MANAGEMENT (Helper Methods)
    // =====================================================

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
}
