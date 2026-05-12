<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ManagerPurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check if user is GENERAL MANAGEMENT
        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $query = PurchaseOrder::with(['vendor', 'orderedBy', 'approvedBy']);

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('po_number', 'like', "%{$search}%")
                      ->orWhereHas('vendor', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('vendor_id')) {
                $query->where('vendor_id', $request->vendor_id);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('po_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('po_date', '<=', $request->date_to);
            }

            $purchaseOrders = $query->orderBy('po_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Statistics for cards
            $totalPOs = PurchaseOrder::count();
            $totalValue = PurchaseOrder::sum('total_amount');
            $pendingPOs = PurchaseOrder::where('status', 'draft')->count();
            $approvedPOs = PurchaseOrder::where('status', 'approved')->count();
            $completedPOs = PurchaseOrder::where('status', 'fully_received')->count();

            // Chart data: PO value by month
            $monthlyData = PurchaseOrder::select(
                DB::raw("DATE_FORMAT(po_date, '%Y-%m') as month"),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();

            // Chart data: Status distribution
            $statusData = [
                'labels' => ['Draft', 'Approved', 'Sent', 'Partially Received', 'Fully Received', 'Cancelled'],
                'data' => [
                    PurchaseOrder::where('status', 'draft')->count(),
                    PurchaseOrder::where('status', 'approved')->count(),
                    PurchaseOrder::where('status', 'sent')->count(),
                    PurchaseOrder::where('status', 'partially_received')->count(),
                    PurchaseOrder::where('status', 'fully_received')->count(),
                    PurchaseOrder::where('status', 'cancelled')->count(),
                ]
            ];

            // Filters data
            $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
            $statuses = ['draft', 'approved', 'sent', 'partially_received', 'fully_received', 'cancelled'];

            return view('management.purchase_orders.index', compact(
                'purchaseOrders',
                'totalPOs',
                'totalValue',
                'pendingPOs',
                'approvedPOs',
                'completedPOs',
                'monthlyData',
                'statusData',
                'vendors',
                'statuses'
            ));

        } catch (\Exception $e) {
            Log::error('Failed to load purchase orders', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return redirect()->route('management.dashboard')->with('error', 'Failed to load purchase orders.');
        }
    }

    /**
     * Display the specified purchase order.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        try {
            $purchaseOrder = PurchaseOrder::with([
                'vendor',
                'orderedBy',
                'approvedBy',
                'items',
                'items.inventoryItem',
                'goodsReceivedNotes'
            ])->findOrFail($id);

            return view('management.purchase_orders.show', compact('purchaseOrder'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('management.purchase-orders.index')->with('error', 'Purchase order not found.');
        } catch (\Exception $e) {
            Log::error('Failed to load purchase order', [
                'user_id' => Auth::id(),
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('management.purchase-orders.index')->with('error', 'Failed to load purchase order details.');
        }
    }

    /**
     * Export to Excel (CSV)
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = PurchaseOrder::with(['vendor', 'orderedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('po_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('po_date', '<=', $request->date_to);
        }

        $purchaseOrders = $query->orderBy('po_date', 'desc')->get();

        $exportData = [];
        foreach ($purchaseOrders as $po) {
            $exportData[] = [
                'PO Number' => $po->po_number,
                'PO Date' => $po->po_date->format('Y-m-d'),
                'Vendor' => $po->vendor->name ?? 'N/A',
                'Status' => ucfirst(str_replace('_', ' ', $po->status)),
                'Subtotal' => $po->subtotal,
                'Tax Amount' => $po->tax_amount,
                'Total Amount' => $po->total_amount,
                'Ordered By' => $po->orderedBy->name ?? 'N/A',
                'Approved By' => $po->approvedBy->name ?? 'N/A',
                'Expected Delivery' => $po->expected_delivery_date ?? 'N/A',
                'Notes' => $po->notes ?? 'N/A',
            ];
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="purchase_orders_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($exportData) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            if (!empty($exportData)) {
                fputcsv($file, array_keys($exportData[0]));
                foreach ($exportData as $row) {
                    fputcsv($file, array_values($row));
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'GENERAL MANAGEMENT') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $query = PurchaseOrder::with(['vendor', 'orderedBy', 'approvedBy', 'items.inventoryItem']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('po_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('po_date', '<=', $request->date_to);
        }

        $purchaseOrders = $query->orderBy('po_date', 'desc')->get();

        $totalValue = $purchaseOrders->sum('total_amount');

        $pdf = Pdf::loadView('management.purchase_orders.export_pdf', [
            'purchaseOrders' => $purchaseOrders,
            'export_date' => now()->format('F d, Y H:i:s'),
            'total_orders' => $purchaseOrders->count(),
            'total_value' => $totalValue,
        ]);

        return $pdf->download('purchase_orders_' . date('Y-m-d') . '.pdf');
    }
}
