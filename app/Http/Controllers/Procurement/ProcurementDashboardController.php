<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Lpo;
use App\Models\DepartmentRequisition;
use App\Models\Vendor;
use App\Models\GoodsReceivedNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcurementDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'PROCUREMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // ========== KPI DATA ==========
        $totalPOsThisMonth = PurchaseOrder::whereMonth('po_date', now()->month)
            ->whereYear('po_date', now()->year)
            ->count();

        $lastMonthPOs = PurchaseOrder::whereMonth('po_date', now()->subMonth()->month)
            ->whereYear('po_date', now()->subMonth()->year)
            ->count();

        $poGrowth = $lastMonthPOs > 0 ? (($totalPOsThisMonth - $lastMonthPOs) / $lastMonthPOs) * 100 : 0;

        $totalPOValue = PurchaseOrder::sum('total_amount');

        $lastMonthValue = PurchaseOrder::whereMonth('po_date', now()->subMonth()->month)
            ->whereYear('po_date', now()->subMonth()->year)
            ->sum('total_amount');

        $valueGrowth = $lastMonthValue > 0 ? (($totalPOValue - $lastMonthValue) / $lastMonthValue) * 100 : 0;

        // Pending LPOs (awaiting director approval)
        $pendingLposCount = Lpo::where('status', 'pending_director')->count();
        $pendingLpos = Lpo::with('vendor')->where('status', 'pending_director')->orderBy('created_at', 'asc')->limit(10)->get();

        // Overdue deliveries (status = 'sent' and expected date passed)
        $overdueDeliveriesCount = PurchaseOrder::where('status', 'sent')
            ->whereNotNull('expected_delivery_date')
            ->where('expected_delivery_date', '<', now())
            ->count();

        $overdueDeliveries = PurchaseOrder::with('vendor')
            ->where('status', 'sent')
            ->whereNotNull('expected_delivery_date')
            ->where('expected_delivery_date', '<', now())
            ->orderBy('expected_delivery_date', 'asc')
            ->limit(10)
            ->get();

        // Pending GRNs (status = 'sent' - not yet received at all)
        $pendingGrns = PurchaseOrder::with('vendor')
            ->where('status', 'sent')
            ->orderBy('expected_delivery_date', 'asc')
            ->limit(10)
            ->get();

        // Partially received POs (for information only - already acted upon)
        $partiallyReceivedPOs = PurchaseOrder::with('vendor')
            ->where('status', 'partially_received')
            ->orderBy('expected_delivery_date', 'asc')
            ->limit(10)
            ->get();

        // PO Status counts
        $poDraftCount = PurchaseOrder::where('status', 'draft')->count();
        $poApprovedCount = PurchaseOrder::where('status', 'approved')->count();
        $poSentCount = PurchaseOrder::where('status', 'sent')->count();
        $poPartialReceivedCount = PurchaseOrder::where('status', 'partially_received')->count();
        $poFullyReceivedCount = PurchaseOrder::where('status', 'fully_received')->count();

        // Requisitions from Store (pending approval from procurement)
        $requisitionsFromStore = DepartmentRequisition::with(['department', 'items'])
            ->where('status', 'pending')
            ->orderByRaw("CASE WHEN date_needed <= NOW() THEN 0 ELSE 1 END")
            ->orderBy('date_needed', 'asc')
            ->limit(10)
            ->get();

        $pendingRequisitionsCount = DepartmentRequisition::where('status', 'pending')->count();

        // Monthly trend data
        $monthlyData = PurchaseOrder::select(
            DB::raw("DATE_FORMAT(po_date, '%Y-%m') as month"),
            DB::raw('SUM(total_amount) as total')
        )
        ->whereYear('po_date', now()->year)
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

        $monthlyLabels = [];
        $monthlyValues = [];
        foreach ($monthlyData as $data) {
            $monthlyLabels[] = date('M Y', strtotime($data->month . '-01'));
            $monthlyValues[] = (float)$data->total;
        }

        // Top vendors by PO value
        $topVendorsRaw = PurchaseOrder::select('vendor_id', DB::raw('SUM(total_amount) as total'))
            ->with('vendor')
            ->groupBy('vendor_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $topVendors = [];
        $topVendorsMax = 0;
        foreach ($topVendorsRaw as $vendor) {
            if ($vendor->vendor) {
                $topVendors[] = [
                    'name' => $vendor->vendor->name,
                    'total' => $vendor->total
                ];
                if ($vendor->total > $topVendorsMax) $topVendorsMax = $vendor->total;
            }
        }

        // Requisitions by department
        $deptRequisitions = DepartmentRequisition::select('department_id', DB::raw('COUNT(*) as count'))
            ->with('department')
            ->where('status', 'pending')
            ->groupBy('department_id')
            ->get();

        $deptRequisitionLabels = [];
        $deptRequisitionData = [];
        foreach ($deptRequisitions as $dept) {
            if ($dept->department) {
                $deptRequisitionLabels[] = $dept->department->name;
                $deptRequisitionData[] = $dept->count;
            }
        }

        // Top items requisitioned
        $topItemsRaw = DB::table('department_requisition_items')
            ->select('inventory_item_id', DB::raw('SUM(quantity_requested) as total'))
            ->groupBy('inventory_item_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $topItemsRequisitioned = [];
        foreach ($topItemsRaw as $item) {
            $inventoryItem = \App\Models\InventoryItem::find($item->inventory_item_id);
            if ($inventoryItem) {
                $topItemsRequisitioned[] = [
                    'name' => $inventoryItem->name,
                    'quantity' => $item->total
                ];
            }
        }

        return view('procurement.dashboard', compact(
            'totalPOsThisMonth', 'poGrowth', 'totalPOValue', 'valueGrowth',
            'pendingLposCount', 'pendingLpos', 'overdueDeliveriesCount', 'overdueDeliveries',
            'pendingGrns', 'partiallyReceivedPOs', 'requisitionsFromStore', 'pendingRequisitionsCount',
            'poDraftCount', 'poApprovedCount', 'poSentCount', 'poPartialReceivedCount', 'poFullyReceivedCount',
            'monthlyLabels', 'monthlyValues', 'topVendors', 'topVendorsMax',
            'deptRequisitionLabels', 'deptRequisitionData', 'topItemsRequisitioned'
        ));
    }
}
