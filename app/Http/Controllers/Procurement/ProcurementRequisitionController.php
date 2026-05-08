<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcurementRequisitionController extends Controller
{
    /**
     * Display a listing of requisitions - ONLY GM APPROVED
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check if user belongs to Procurement department
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // ONLY show requisitions with status 'approved' (GM approved)
        $query = Requisition::with(['store', 'requestedBy', 'items'])
            ->where('status', 'approved');

        // Search by requisition number
        if ($request->filled('search')) {
            $query->where('requisition_number', 'like', '%' . $request->search . '%');
        }

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get stores for filter dropdown
        $stores = \App\Models\Department::where('name', 'STORE')->get();

        return view('procurement.requisitions.index', compact('requisitions', 'stores'));
    }

    /**
     * Display the specified requisition - ONLY if approved
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // Only show if status is 'approved'
        $requisition = Requisition::with(['store', 'requestedBy', 'approvedBy', 'items.inventoryItem'])
            ->where('status', 'approved')
            ->findOrFail($id);

        return view('procurement.requisitions.show', compact('requisition'));
    }

    /**
     * Redirect to LPO creation for approved requisition
     */
    public function createLpo($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $requisition = Requisition::with(['items.inventoryItem', 'store'])
            ->where('status', 'approved')
            ->findOrFail($id);

        return redirect()->route('procurement.lpo.create', $requisition->id)
            ->with('success', 'Create LPO from requisition #' . $requisition->requisition_number);
    }
}
