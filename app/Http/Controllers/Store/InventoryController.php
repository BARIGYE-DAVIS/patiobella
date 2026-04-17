<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Category;
use App\Models\UnitOfMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has store department
        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = InventoryItem::with(['category', 'unitOfMeasure']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $items = $query->orderBy('name')->paginate(20);
        
        // Get categories for filter dropdown
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('store.inventory.index', compact('items', 'categories'));
    }

    /**
     * Show form to create a new inventory item.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $units = UnitOfMeasure::where('is_active', true)->orderBy('name')->get();
        
        return view('store.inventory.create', compact('categories', 'units'));
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'item_code' => 'required|string|max:50|unique:inventory_items,item_code',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'default_unit_of_measure_id' => 'required|exists:units_of_measure,id',
            'minimum_stock' => 'nullable|numeric|min:0',
            'maximum_stock' => 'nullable|numeric|min:0',
            'reorder_quantity' => 'nullable|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'current_stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['current_stock'] = $validated['current_stock'] ?? 0;
        
        $item = InventoryItem::create($validated);

        Log::info('Inventory item created', [
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'item_code' => $item->item_code
        ]);

        return redirect()->route('store.inventory.index')
            ->with('success', "Item '{$item->name}' created successfully.");
    }

    /**
     * Display the specified inventory item.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $item = InventoryItem::with(['category', 'unitOfMeasure', 'creator', 'updater'])->findOrFail($id);
        
        return view('store.inventory.show', compact('item'));
    }

    /**
     * Show form to edit an inventory item.
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $item = InventoryItem::findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $units = UnitOfMeasure::where('is_active', true)->orderBy('name')->get();
        
        return view('store.inventory.edit', compact('item', 'categories', 'units'));
    }

    /**
     * Update the specified inventory item.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $item = InventoryItem::findOrFail($id);

        $validated = $request->validate([
            'item_code' => 'required|string|max:50|unique:inventory_items,item_code,' . $id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'default_unit_of_measure_id' => 'required|exists:units_of_measure,id',
            'minimum_stock' => 'nullable|numeric|min:0',
            'maximum_stock' => 'nullable|numeric|min:0',
            'reorder_quantity' => 'nullable|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'current_stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        
        $item->update($validated);

        Log::info('Inventory item updated', [
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'item_code' => $item->item_code
        ]);

        return redirect()->route('store.inventory.show', $item->id)
            ->with('success', "Item '{$item->name}' updated successfully.");
    }

    /**
     * Delete (soft delete) the specified inventory item.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $item = InventoryItem::findOrFail($id);
        $itemName = $item->name;
        
        $item->delete();

        Log::warning('Inventory item deleted', [
            'user_id' => Auth::id(),
            'item_id' => $id,
            'item_name' => $itemName
        ]);

        return redirect()->route('store.inventory.index')
            ->with('success', "Item '{$itemName}' deleted successfully.");
    }
}