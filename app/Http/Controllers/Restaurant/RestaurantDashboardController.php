<?php
// app/Http/Controllers/Restaurant/RestaurantDashboardController.php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\DepartmentRequisition;
use App\Models\DepartmentRequisitionItem;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RestaurantDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'RESTAURANT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // ========== KPI DATA ==========

        // Total requisitions pending
        $pendingRequisitions = DepartmentRequisition::where('department_id', $user->department_id)
            ->where('status', 'pending')
            ->count();

        // Total requisitions approved but not yet issued
        $approvedRequisitions = DepartmentRequisition::where('department_id', $user->department_id)
            ->where('status', 'approved')
            ->count();

        // Total items issued to restaurant this month
        $issuedThisMonth = DepartmentRequisitionItem::whereHas('departmentRequisition', function($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })
            ->whereMonth('created_at', now()->month)
            ->sum('quantity_issued');

        // Total items returned this month
        $returnedThisMonth = DepartmentRequisitionItem::whereHas('departmentRequisition', function($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })
            ->whereMonth('returned_at', now()->month)
            ->sum('quantity_returned');

        // Recent requisitions
        $recentRequisitions = DepartmentRequisition::with(['items.inventoryItem'])
            ->where('department_id', $user->department_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent stock movements (issues and returns for this department)
        $recentMovements = StockMovement::with(['inventoryItem', 'movementType'])
            ->where('department_id', $user->department_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Department info
        $department = Department::find($user->department_id);

        return view('restaurant.dashboard', compact(
            'pendingRequisitions',
            'approvedRequisitions',
            'issuedThisMonth',
            'returnedThisMonth',
            'recentRequisitions',
            'recentMovements',
            'department'
        ));
    }
}
