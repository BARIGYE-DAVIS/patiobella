<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProcurementDashboardController extends Controller
{
    /**
     * Display procurement dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user belongs to Procurement department
        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            Log::warning('Unauthorized access to procurement module', [
                'user_id' => $user->id,
                'department' => $user->department ? $user->department->name : 'No department'
            ]);
            return redirect()->route('dashboard')
                ->with('error', 'You do not have access to the Procurement module.');
        }

        // Get dynamic counts from database
        $pendingRequisitions = Requisition::where('status', 'pending')->count();
        $pendingPurchaseOrders = PurchaseOrder::where('status', 'pending')->count();
        $activeVendors = Vendor::where('status', 'active')->count();
        $pendingApprovals = $pendingRequisitions + $pendingPurchaseOrders;
        
        // Get recent requisitions
        $recentRequisitions = Requisition::with(['store', 'requestedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        Log::info('Procurement dashboard accessed', [
            'user_id' => Auth::id(),
            'pending_requisitions' => $pendingRequisitions,
            'pending_pos' => $pendingPurchaseOrders,
            'active_vendors' => $activeVendors
        ]);

        return view('procurement.dashboard', compact(
            'pendingRequisitions',
            'pendingPurchaseOrders',
            'activeVendors',
            'pendingApprovals',
            'recentRequisitions'
        ));
    }
}