<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Store\Log;

class EmptyBottleWeightController extends Controller
{
    /**
     * Display a list of items that can have empty bottle weights.
     */
    public function index(Request $request)
    {
        // Get all active inventory items (user will select which ones to set weight for)
        $query = InventoryItem::where('is_active', 1);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('item_code', 'LIKE', "%{$search}%")
                  ->orWhere('barcode', 'LIKE', "%{$search}%");
            });
        }

        // Apply category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by weight status (has weight set or not)
        if ($request->filled('weight_status')) {
            if ($request->weight_status === 'has_weight') {
                $query->where('empty_bottle_weight', '>', 0);
            } elseif ($request->weight_status === 'no_weight') {
                $query->where('empty_bottle_weight', '=', 0);
            }
        }

        $items = $query->orderBy('name')->paginate(20)->withQueryString();

        // Get all active categories for filter dropdown (no limitation)
        $categories = Category::where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('store.empty-bottle-weights.index', compact('items', 'categories'));
    }

    /**
     * Show the form for editing a specific item's empty bottle weight.
     */
    public function edit($id)
    {
        $item = InventoryItem::findOrFail($id);

        return view('store.empty-bottle-weights.edit', compact('item'));
    }

    /**
     * Update the specified item's empty bottle weight.
     */
    public function update(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);

        $request->validate([
            'empty_bottle_weight' => 'required|numeric|min:0|max:999999.999999',
        ], [
            'empty_bottle_weight.required' => 'Please enter the empty bottle weight.',
            'empty_bottle_weight.numeric' => 'Weight must be a number.',
            'empty_bottle_weight.min' => 'Weight cannot be negative.',
            'empty_bottle_weight.max' => 'Weight is too large.',
        ]);

        $oldWeight = $item->empty_bottle_weight;
        $item->empty_bottle_weight = $request->empty_bottle_weight;
        $item->updated_by = Auth::id();
        $item->save();

        // Log the change
        Log::info('Empty bottle weight updated', [
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'item_name' => $item->name,
            'old_weight' => $oldWeight,
            'new_weight' => $item->empty_bottle_weight
        ]);

        return redirect()->route('store.empty-bottle-weights.index')
            ->with('success', "Empty bottle weight for '{$item->name}' has been updated successfully.");
    }
}
