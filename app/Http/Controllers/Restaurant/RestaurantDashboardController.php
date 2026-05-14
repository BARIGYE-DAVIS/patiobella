<?php
// app/Http/Controllers/Restaurant/RestaurantDashboardController.php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\DepartmentRequisition;
use App\Models\DepartmentRequisitionItem;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RestaurantDashboardController extends Controller
{
    /**
     * Resolve the authenticated user's role name.
     * Handles both role_id (FK) and role (string column).
     */
    private function getRoleName($user): ?string
    {
        try {
            if ($user->role_id) {
                $role = Role::find($user->role_id);
                return $role->name ?? null;
            }

            return !empty($user->role) ? $user->role : null;
        } catch (\Exception $e) {
            Log::error('Error resolving role name in RestaurantDashboard', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function index()
    {
        try {
            Log::info('Restaurant dashboard accessed', ['user_id' => Auth::id()]);

            $user = Auth::user();

            // Check department
            if (!$user->department || $user->department->name !== 'RESTAURANT') {
                Log::warning('Restaurant dashboard access denied - wrong department', [
                    'user_id' => $user->id,
                    'department' => $user->department->name ?? 'none'
                ]);
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }

            // Check role - must NOT be Cashier
            $roleName = $this->getRoleName($user);
            if ($roleName === 'Cashier') {
                Log::warning('Restaurant dashboard access denied - Cashier role redirected', [
                    'user_id' => $user->id,
                    'role' => $roleName
                ]);
                return redirect()->route('restaurant.cashier.dashboard')
                    ->with('error', 'Cashiers must use the cashier dashboard.');
            }

            Log::info('Restaurant dashboard authorized', [
                'user_id' => $user->id,
                'role' => $roleName,
                'department_id' => $user->department_id
            ]);

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

            Log::info('Restaurant dashboard data loaded', [
                'user_id' => Auth::id(),
                'pending_requisitions' => $pendingRequisitions,
                'approved_requisitions' => $approvedRequisitions,
                'issued_this_month' => $issuedThisMonth,
                'returned_this_month' => $returnedThisMonth
            ]);

            return view('restaurant.dashboard', compact(
                'pendingRequisitions',
                'approvedRequisitions',
                'issuedThisMonth',
                'returnedThisMonth',
                'recentRequisitions',
                'recentMovements',
                'department'
            ));

        } catch (\Exception $e) {
            Log::error('Restaurant dashboard error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('dashboard')->with('error', 'Failed to load restaurant dashboard: ' . $e->getMessage());
        }
    }
}
