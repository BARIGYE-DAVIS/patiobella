<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockMovementController extends Controller
{
    /**
     * Display a listing of stock movements.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->department || $user->department->name !== 'STORE') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = StockMovement::with(['inventoryItem', 'createdBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('movement_number', 'like', "%{$search}%");
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('store.stock-movements.index', compact('movements'));
    }
}