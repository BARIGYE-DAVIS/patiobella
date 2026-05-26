<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Models\TableReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TableReservationController extends Controller
{
    /**
     * Display a listing of reservations.
     */
    public function index(Request $request)
    {
        $query = TableReservation::with('table');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('reservation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('reservation_date', '<=', $request->date_to);
        }

        if ($request->filled('table_id')) {
            $query->where('restaurant_table_id', $request->table_id);
        }

        $reservations = $query->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->paginate(20);

        $tables = RestaurantTable::where('is_active', true)->orderBy('table_number')->get();
        $statuses = TableReservation::getStatuses();

        return view('management.reservations.index', compact('reservations', 'tables', 'statuses'));
    }

    /**
     * Show form to create a new reservation.
     */
    public function create(Request $request)
    {
        $tables = RestaurantTable::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('table_number')
            ->get();

        $selectedTableId = $request->get('table_id');

        return view('management.reservations.create', compact('tables', 'selectedTableId'));
    }

    /**
     * Store a newly created reservation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_table_id' => 'required|exists:restaurant_tables,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_email' => 'nullable|email|max:255',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required',
            'duration_hours' => 'required|integer|min:1|max:8',
            'number_of_guests' => 'required|integer|min:1|max:50',
            'notes' => 'nullable|string',
            'status' => 'sometimes|string|in:pending,confirmed',
        ]);

        // Check if table is available at that time
        $table = RestaurantTable::find($validated['restaurant_table_id']);

        if (!$table->is_active) {
            return redirect()->back()
                ->with('error', 'This table is inactive and cannot be reserved.')
                ->withInput();
        }

        $existingReservation = TableReservation::where('restaurant_table_id', $validated['restaurant_table_id'])
            ->where('reservation_date', $validated['reservation_date'])
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->exists();

        if ($existingReservation) {
            return redirect()->back()
                ->with('error', 'This table is already reserved for the selected date.')
                ->withInput();
        }

        $validated['status'] = $request->input('status', 'pending');
        $validated['created_by'] = Auth::id();

        $reservation = TableReservation::create($validated);

        // Mark table as reserved
        $table->is_reserved = true;
        $table->save();

        Log::info('Table reservation created', [
            'user_id' => Auth::id(),
            'reservation_id' => $reservation->id,
            'table_id' => $reservation->restaurant_table_id,
            'customer_name' => $reservation->customer_name
        ]);

        return redirect()->route('management.reservations.show', $reservation->id)
            ->with('success', 'Reservation created successfully.');
    }

    /**
     * Display the specified reservation.
     */
    public function show($id)
    {
        $reservation = TableReservation::with(['table', 'creator', 'updater', 'cancelledBy'])->findOrFail($id);

        return view('management.reservations.show', compact('reservation'));
    }

    /**
     * Show form to edit a reservation.
     */
    public function edit($id)
    {
        $reservation = TableReservation::findOrFail($id);
        $tables = RestaurantTable::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('table_number')
            ->get();

        return view('management.reservations.edit', compact('reservation', 'tables'));
    }

    /**
     * Update the specified reservation.
     */
    public function update(Request $request, $id)
    {
        $reservation = TableReservation::findOrFail($id);

        $validated = $request->validate([
            'restaurant_table_id' => 'required|exists:restaurant_tables,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_email' => 'nullable|email|max:255',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'duration_hours' => 'required|integer|min:1|max:8',
            'number_of_guests' => 'required|integer|min:1|max:50',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:pending,confirmed,seated,completed,cancelled,no_show',
        ]);

        // Check availability if table or date changed
        if ($reservation->restaurant_table_id != $validated['restaurant_table_id'] ||
            $reservation->reservation_date != $validated['reservation_date']) {

            $existingReservation = TableReservation::where('restaurant_table_id', $validated['restaurant_table_id'])
                ->where('reservation_date', $validated['reservation_date'])
                ->where('id', '!=', $id)
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'completed')
                ->exists();

            if ($existingReservation) {
                return redirect()->back()
                    ->with('error', 'This table is already reserved for the selected date.')
                    ->withInput();
            }
        }

        $validated['updated_by'] = Auth::id();
        $reservation->update($validated);

        // Update table reserved status
        $table = RestaurantTable::find($validated['restaurant_table_id']);
        $hasActiveReservations = TableReservation::where('restaurant_table_id', $table->id)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->exists();

        $table->is_reserved = $hasActiveReservations;
        $table->save();

        Log::info('Table reservation updated', [
            'user_id' => Auth::id(),
            'reservation_id' => $reservation->id
        ]);

        return redirect()->route('management.reservations.show', $reservation->id)
            ->with('success', 'Reservation updated successfully.');
    }

    /**
     * Update reservation status.
     */
    public function updateStatus(Request $request, $id)
    {
        $reservation = TableReservation::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,seated,completed,cancelled,no_show',
            'cancellation_reason' => 'required_if:status,cancelled|nullable|string',
        ]);

        $oldStatus = $reservation->status;
        $reservation->status = $validated['status'];

        if ($validated['status'] == 'cancelled') {
            $reservation->cancelled_by = Auth::id();
            $reservation->cancelled_at = now();
            $reservation->cancellation_reason = $validated['cancellation_reason'] ?? null;
        }

        $reservation->updated_by = Auth::id();
        $reservation->save();

        // Update table reserved status
        $table = RestaurantTable::find($reservation->restaurant_table_id);
        $hasActiveReservations = TableReservation::where('restaurant_table_id', $table->id)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->exists();

        $table->is_reserved = $hasActiveReservations;
        $table->save();

        Log::info('Reservation status updated', [
            'user_id' => Auth::id(),
            'reservation_id' => $reservation->id,
            'old_status' => $oldStatus,
            'new_status' => $reservation->status
        ]);

        return redirect()->route('management.reservations.show', $reservation->id)
            ->with('success', 'Reservation status updated successfully.');
    }

    /**
     * Delete the specified reservation.
     */
    public function destroy($id)
    {
        $reservation = TableReservation::findOrFail($id);

        $tableId = $reservation->restaurant_table_id;

        $reservation->delete();

        // Update table reserved status
        $table = RestaurantTable::find($tableId);
        if ($table) {
            $hasActiveReservations = TableReservation::where('restaurant_table_id', $tableId)
                ->where('status', '!=', 'cancelled')
                ->where('status', '!=', 'completed')
                ->exists();

            $table->is_reserved = $hasActiveReservations;
            $table->save();
        }

        Log::warning('Table reservation deleted', [
            'user_id' => Auth::id(),
            'reservation_id' => $reservation->id
        ]);

        return redirect()->route('management.reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }

    /**
     * Get available tables for a specific date (AJAX).
     */
    public function getAvailableTables(Request $request)
    {
        $date = $request->get('date');

        $reservedTableIds = TableReservation::where('reservation_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->pluck('restaurant_table_id')
            ->toArray();

        $availableTables = RestaurantTable::where('is_active', true)
            ->whereNotIn('id', $reservedTableIds)
            ->orderBy('sort_order')
            ->orderBy('table_number')
            ->get(['id', 'table_number', 'capacity', 'location']);

        return response()->json($availableTables);
    }
}
