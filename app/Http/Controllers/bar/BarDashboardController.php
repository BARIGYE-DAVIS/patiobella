<?php
// app/Http/Controllers/Bar/BarDashboardController.php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\InventoryItem;
use App\Models\DepartmentRequisition;
use App\Models\DepartmentRequisitionItem;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BarDashboardController extends Controller
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

    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access. Bar department only.');
            }

            Log::info('Bar dashboard accessed', ['user_id' => $user->id, 'role' => $this->getRoleName($user)]);

            $departmentId = $user->department_id;

            // Today's sales for BAR department only
            $todaySales = SalesOrder::whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->where('department_id', $departmentId)
                ->sum('total_amount');

            $todayOrders = SalesOrder::whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->where('department_id', $departmentId)
                ->count();

            // Bar stock from requisitions
            $stockItems = DepartmentRequisitionItem::with(['inventoryItem'])
                ->whereHas('departmentRequisition', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId)
                      ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
                })
                ->where('issued_total_pieces', '>', 0)
                ->get();

            $totalStockValue = 0;
            $lowStockCount = 0;
            $stockList = [];

            foreach ($stockItems as $item) {
                $inventoryItem = $item->inventoryItem;
                if (!$inventoryItem) continue;

                $issued = (float) ($item->issued_total_pieces ?? 0);
                $consumed = (float) ($item->quantity_consumed ?? 0);
                $returned = (float) ($item->returned_total_pieces ?? 0);
                $sold = (float) ($item->quantity_sold ?? 0);
                $remaining = $issued - ($consumed + $returned + $sold);

                $unitCost = $inventoryItem->unit_cost ?? 0;
                $itemValue = $remaining * $unitCost;
                $totalStockValue += $itemValue;

                if ($remaining > 0 && $remaining < 10) {
                    $lowStockCount++;
                }

                if ($remaining > 0) {
                    $stockList[] = [
                        'name' => $inventoryItem->name,
                        'stock' => $remaining,
                        'unit' => $inventoryItem->base_unit ?? 'units'
                    ];
                }
            }

            // Recent orders for BAR department only
            $recentOrders = SalesOrder::with(['items'])
                ->where('department_id', $departmentId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Pending requisitions for BAR department
            $pendingRequisitions = DepartmentRequisition::where('department_id', $departmentId)
                ->where('status', 'pending')
                ->count();

            return view('bar.dashboard', compact(
                'todaySales',
                'todayOrders',
                'totalStockValue',
                'lowStockCount',
                'stockList',
                'recentOrders',
                'pendingRequisitions'
            ));

        } catch (\Exception $e) {
            Log::error('Bar dashboard error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('dashboard')->with('error', 'Failed to load bar dashboard: ' . $e->getMessage());
        }
    }
}
