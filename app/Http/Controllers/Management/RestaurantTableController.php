<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Models\TableReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RestaurantTableController extends Controller
{
    /**
     * Display a listing of tables.
     */
    public function index(Request $request)
    {
        $query = RestaurantTable::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('table_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%");
            });
        }

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        if ($request->filled('capacity')) {
            $query->where('capacity', $request->capacity);
        }

        if ($request->filled('is_reserved')) {
            $query->where('is_reserved', $request->is_reserved);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $tables = $query->orderBy('sort_order')->orderBy('table_number')->paginate(15);

        $locations = RestaurantTable::select('location')->distinct()->pluck('location');
        $capacities = RestaurantTable::select('capacity')->distinct()->orderBy('capacity')->pluck('capacity');

        return view('management.tables.index', compact('tables', 'locations', 'capacities'));
    }

    /**
     * Show form to create a new table.
     */
    public function create()
    {
        // Get the last table to generate next number
        $lastTable = RestaurantTable::orderBy('id', 'desc')->first();

        if ($lastTable && $lastTable->table_number) {
            // Extract number from TB001 format
            $lastNumber = intval(substr($lastTable->table_number, 2));
            $nextNumber = $lastNumber + 1;
            $nextTableNumber = 'TB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        } else {
            $nextTableNumber = 'TB001';
        }

        return view('management.tables.create', compact('nextTableNumber'));
    }

    /**
     * Store a newly created table.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|max:50|unique:restaurant_tables,table_number',
            'capacity' => 'required|integer|min:1|max:50',
            'size' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_reserved' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_reserved'] = $request->input('is_reserved', false);
        $validated['is_active'] = $request->input('is_active', true);
        $validated['sort_order'] = $request->input('sort_order', 0);
        $validated['created_by'] = Auth::id();

        $table = RestaurantTable::create($validated);

        Log::info('Restaurant table created', [
            'user_id' => Auth::id(),
            'table_id' => $table->id,
            'table_number' => $table->table_number
        ]);

        return redirect()->route('management.tables.index')
            ->with('success', "Table '{$table->table_number}' created successfully.");
    }

    /**
     * Display the specified table.
     */
    public function show($id)
    {
        $table = RestaurantTable::with(['creator', 'updater'])->findOrFail($id);

        $upcomingReservations = TableReservation::where('restaurant_table_id', $id)
            ->where('reservation_date', '>=', date('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->get();

        return view('management.tables.show', compact('table', 'upcomingReservations'));
    }

    /**
     * Show form to edit a table.
     */
    public function edit($id)
    {
        $table = RestaurantTable::findOrFail($id);
        return view('management.tables.edit', compact('table'));
    }

    /**
     * Update the specified table.
     */
    public function update(Request $request, $id)
    {
        $table = RestaurantTable::findOrFail($id);

        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:50', Rule::unique('restaurant_tables', 'table_number')->ignore($id)],
            'capacity' => 'required|integer|min:1|max:50',
            'size' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_reserved' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['updated_by'] = Auth::id();

        $table->update($validated);

        Log::info('Restaurant table updated', [
            'user_id' => Auth::id(),
            'table_id' => $table->id,
            'table_number' => $table->table_number
        ]);

        return redirect()->route('management.tables.show', $table->id)
            ->with('success', "Table '{$table->table_number}' updated successfully.");
    }

    /**
     * Delete the specified table.
     */
    public function destroy($id)
    {
        $table = RestaurantTable::findOrFail($id);

        // Check if table has any active reservations
        $hasReservations = TableReservation::where('restaurant_table_id', $id)
            ->where('reservation_date', '>=', date('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($hasReservations) {
            return redirect()->route('management.tables.index')
                ->with('error', 'Cannot delete table with upcoming reservations.');
        }

        $tableNumber = $table->table_number;

        Log::warning('Restaurant table deleted', [
            'user_id' => Auth::id(),
            'table_id' => $table->id,
            'table_number' => $table->table_number
        ]);

        $table->delete();

        return redirect()->route('management.tables.index')
            ->with('success', "Table '{$tableNumber}' deleted successfully.");
    }

    /**
     * Toggle table reserved status.
     */
    public function toggleReserved($id)
    {
        $table = RestaurantTable::findOrFail($id);
        $table->is_reserved = !$table->is_reserved;
        $table->updated_by = Auth::id();
        $table->save();

        $status = $table->is_reserved ? 'reserved' : 'available';

        Log::info('Table reserved status toggled', [
            'user_id' => Auth::id(),
            'table_id' => $table->id,
            'table_number' => $table->table_number,
            'new_status' => $status
        ]);

        return redirect()->route('management.tables.show', $table->id)
            ->with('success', "Table '{$table->table_number}' is now {$status}.");
    }

    /**
     * Toggle table active status.
     */
    public function toggleActive($id)
    {
        $table = RestaurantTable::findOrFail($id);
        $table->is_active = !$table->is_active;
        $table->updated_by = Auth::id();
        $table->save();

        $status = $table->is_active ? 'activated' : 'deactivated';

        Log::info('Table active status toggled', [
            'user_id' => Auth::id(),
            'table_id' => $table->id,
            'table_number' => $table->table_number,
            'new_status' => $status
        ]);

        return redirect()->route('management.tables.index')
            ->with('success', "Table '{$table->table_number}' has been {$status}.");
    }
}
