<?php
// app/Http/Controllers/Bar/BarPosController.php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\InventoryItem;
use App\Models\DepartmentRequisitionItem;
use App\Models\Role;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BarPosController extends Controller
{
    /**
     * Resolve the authenticated user's role name.
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
            Log::error('Error resolving role name', ['user_id' => $user->id ?? null, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Bar Cashier Dashboard
     */
    public function dashboard()
    {
        try {
            Log::info('Bar cashier dashboard accessed', ['user_id' => Auth::id()]);

            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier' && $roleName !== 'Bar Cashier') {
                return redirect()->route('dashboard')->with('error', 'Cashier access only.');
            }

            $todaySales   = SalesOrder::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total_amount');
            $todayOrders  = SalesOrder::whereDate('created_at', today())->where('payment_status', 'paid')->count();
            $unpaidOrders = SalesOrder::where('payment_status', 'unpaid')->count();

            $recentOrders = SalesOrder::with('items')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('bar.cashier.dashboard', compact('todaySales', 'todayOrders', 'unpaidOrders', 'recentOrders'));

        } catch (\Exception $e) {
            Log::error('Bar cashier dashboard error', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'Failed to load dashboard.');
        }
    }

    /**
     * Point of Sale (POS) Screen
     */
    public function index()
    {
        try {
            Log::info('Bar POS screen accessed', ['user_id' => Auth::id()]);

            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier' && $roleName !== 'Bar Cashier') {
                return redirect()->route('dashboard')->with('error', 'Cashier access only.');
            }

            $departmentId = $user->department_id;

            $requisitionItems = DepartmentRequisitionItem::with(['inventoryItem.category'])
                ->whereHas('departmentRequisition', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId)
                      ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
                })
                ->where('issued_total_pieces', '>', 0)
                ->get();

            $sellableItems = [];
            $currentStock  = [];
            $categoryIdsWithStock = [];

            foreach ($requisitionItems as $item) {
                $itemId        = $item->inventory_item_id;
                $inventoryItem = $item->inventoryItem;

                if (!$inventoryItem || !$inventoryItem->is_active) continue;
                if (!$inventoryItem->is_sellable) continue;

                $issued    = (float) ($item->issued_total_pieces ?? 0);
                $consumed  = (float) ($item->quantity_consumed ?? 0);
                $returned  = (float) ($item->returned_total_pieces ?? 0);
                $sold      = (float) ($item->quantity_sold ?? 0);
                $remaining = $issued - ($consumed + $returned + $sold);

                if ($remaining > 0) {
                    if (!isset($currentStock[$itemId])) {
                        $currentStock[$itemId] = $remaining;
                        $sellableItems[]        = $inventoryItem;

                        if ($inventoryItem->category_id) {
                            $categoryIdsWithStock[$inventoryItem->category_id] = true;
                        }
                    } else {
                        $currentStock[$itemId] += $remaining;
                    }
                }
            }

            $barCategories = Category::where('is_active', true)
                ->whereIn('id', array_keys($categoryIdsWithStock))
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            return view('bar.cashier.pos', compact('sellableItems', 'currentStock', 'barCategories'));

        } catch (\Exception $e) {
            Log::error('Bar POS screen error', ['user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return redirect()->route('bar.cashier.dashboard')->with('error', 'Failed to load POS.');
        }
    }


    /**
 * Orders List
 */
public function orders(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'BAR') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $roleName = $this->getRoleName($user);
        if ($roleName !== 'Cashier' && $roleName !== 'Bar Cashier') {
            return redirect()->route('dashboard')->with('error', 'Cashier access only.');
        }

        $status = $request->get('status', 'unpaid');
        $search = $request->get('search', '');

        // Build query - Cashier sees ONLY their own orders within BAR department
        $orders = SalesOrder::with(['cashier', 'items'])
            ->where('department_id', $user->department_id)
            ->where('cashier_id', Auth::id())
            ->when($status === 'unpaid', fn($q) => $q->where('payment_status', 'unpaid'))
            ->when($status === 'paid', fn($q) => $q->where('payment_status', 'paid'))
            ->when($search, function($q) use ($search) {
                $q->where(function($sub) use ($search) {
                    $sub->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('items', function($itemQuery) use ($search) {
                            $itemQuery->where('item_name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if ($request->ajax()) {
            $tableBody = '';
            foreach ($orders as $order) {
                // Build items list HTML
                $itemsHtml = '';
                $itemsCount = 0;
                foreach ($order->items as $item) {
                    if ($itemsCount < 3) {
                        $itemsHtml .= '<span class="item-badge">' . e(\Str::limit($item->item_name, 20)) . ' (' . $item->quantity . ')</span> ';
                    }
                    $itemsCount++;
                }
                if ($order->items->count() > 3) {
                    $itemsHtml .= '<span class="item-badge">+' . ($order->items->count() - 3) . ' more</span>';
                }

                $tableBody .= '
                <tr class="order-row">
                    <td class="font-mono text-xs font-bold">' . e($order->order_number) . '</td>
                    <td class="text-xs">' . $order->created_at->format('d/m/Y h:i A') . '</td>
                    <td class="items-cell text-xs">' . $itemsHtml . '</td>
                    <td class="text-right font-semibold">UGX ' . number_format($order->total_amount, 0) . '</td>
                    <td class="text-center">';
                if ($order->payment_status === 'unpaid') {
                    $tableBody .= '<span class="status-badge status-unpaid">Unpaid</span>';
                } else {
                    $tableBody .= '<span class="status-badge status-paid">Paid</span>';
                }
                $tableBody .= '</td>
                    <td class="text-center">
                        <a href="' . route('bar.cashier.orders.show', $order->id) . '" class="btn-view">
                            <i class="fas fa-eye mr-1"></i> View
                        </a>
                    </td>
                </tr>';
            }

            if (count($orders) == 0) {
                $tableBody = '<tr><td colspan="6" class="text-center py-8 text-gray-400">
                    <i class="fas fa-receipt text-4xl mb-2 block"></i>
                    No orders found.
                </td></tr>';
            }

            $pagination = $orders->appends(['status' => $status, 'search' => $search])->links()->toHtml();

            return response()->json([
                'tableBody' => $tableBody,
                'pagination' => $pagination,
                'unpaidCount' => SalesOrder::where('payment_status', 'unpaid')
                    ->where('department_id', $user->department_id)
                    ->where('cashier_id', Auth::id())
                    ->count(),
                'paidCount' => SalesOrder::where('payment_status', 'paid')
                    ->where('department_id', $user->department_id)
                    ->where('cashier_id', Auth::id())
                    ->count(),
            ]);
        }

        return view('bar.cashier.orders', compact('orders', 'status'));

    } catch (\Exception $e) {
        Log::error('Bar orders list error', ['error' => $e->getMessage()]);
        if ($request->ajax()) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return redirect()->route('bar.cashier.dashboard')->with('error', 'Failed to load orders.');
    }
}
    /**
     * Show a single order
     */
    public function showOrder($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier' && $roleName !== 'Bar Cashier') {
                return redirect()->route('dashboard')->with('error', 'Cashier access only.');
            }

            $order = SalesOrder::with('items')->findOrFail($id);

            return $order->payment_status === 'unpaid'
                ? view('bar.cashier.invoice', compact('order'))
                : view('bar.cashier.receipt', compact('order'));

        } catch (\Exception $e) {
            Log::error('Bar show order error', ['order_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('bar.cashier.orders')->with('error', 'Order not found.');
        }
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid($id, Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier' && $roleName !== 'Bar Cashier') {
                return response()->json(['success' => false, 'message' => 'Cashier access only'], 403);
            }

            $request->validate([
                'payment_method' => 'required|in:cash,card,mobile_money',
                'amount_paid'    => 'nullable|numeric',
                'change_amount'  => 'nullable|numeric',
            ]);

            DB::beginTransaction();

            $order = SalesOrder::findOrFail($id);

            if ($order->payment_status === 'paid') {
                return response()->json(['success' => false, 'message' => 'Order already paid'], 400);
            }

            $order->payment_method = $request->payment_method;
            $order->amount_paid    = $request->amount_paid ?? $order->total_amount;
            $order->change_amount  = $request->change_amount ?? 0;
            $order->status         = 'completed';
            $order->payment_status = 'paid';
            $order->save();

            DB::commit();

            Log::info('Bar order marked as paid', [
                'cashier_id'     => Auth::id(),
                'order_id'       => $order->id,
                'order_number'   => $order->order_number,
                'payment_method' => $request->payment_method,
                'amount_paid'    => $request->amount_paid,
                'change_amount'  => $request->change_amount,
            ]);

            return response()->json([
                'success'       => true,
                'order_id'      => $order->id,
                'order_number'  => $order->order_number,
                'total_amount'  => $order->total_amount,
                'amount_paid'   => $request->amount_paid,
                'change_amount' => $request->change_amount,
                'message'       => 'Payment successful',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bar payment failed', ['order_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create Invoice (pending payment) - deducts stock for inventory items
     */
    public function createInvoice(Request $request)
    {
        try {
            Log::info('Bar createInvoice called', ['request' => $request->all()]);

            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                Log::error('Bar createInvoice: Unauthorized department');
                return response()->json(['success' => false, 'message' => 'Unauthorized department'], 403);
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Cashier' && $roleName !== 'Bar Cashier') {
                Log::error('Bar createInvoice: Unauthorized role', ['role' => $roleName]);
                return response()->json(['success' => false, 'message' => 'Cashier access only'], 403);
            }

            $validated = $request->validate([
                'items'              => 'required|array|min:1',
                'items.*.item_id'    => 'required',
                'items.*.item_type'  => 'required|in:menu,inventory',
                'items.*.quantity'   => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'required|numeric|min:0',
                'total_amount'       => 'required|numeric|min:0',
                'customer_type'      => 'nullable|in:dine_in,takeaway,delivery',
            ]);

            DB::beginTransaction();

            $orderNumber  = 'BAR-INV-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $departmentId = $user->department_id;

            $order = SalesOrder::create([
                'order_number'   => $orderNumber,
                'cashier_id'     => Auth::id(),
                'department_id'  => $departmentId,
                'customer_type'  => $request->customer_type ?? 'dine_in',
                'subtotal'       => $request->total_amount,
                'tax_amount'     => 0,
                'total_amount'   => $request->total_amount,
                'payment_method' => null,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
            ]);

            $itemsList = [];

            foreach ($request->items as $itemData) {
                // Bar only sells inventory items (no menu items)
                $inventoryItem = InventoryItem::find($itemData['item_id']);
                if (!$inventoryItem) {
                    throw new \Exception("Inventory item not found: " . $itemData['item_id']);
                }

                $quantityToDeduct = (float) $itemData['quantity'];

                $reqItems = DepartmentRequisitionItem::with(['departmentRequisition'])
                    ->whereHas('departmentRequisition', function ($q) use ($departmentId) {
                        $q->where('department_id', $departmentId)
                          ->whereIn('status', ['issued', 'partially_issued', 'partially_consumed', 'partially_returned']);
                    })
                    ->where('inventory_item_id', $itemData['item_id'])
                    ->where('issued_total_pieces', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->get();

                foreach ($reqItems as $reqItem) {
                    if ($quantityToDeduct <= 0) break;

                    $issued    = (float) ($reqItem->issued_total_pieces ?? 0);
                    $consumed  = (float) ($reqItem->quantity_consumed ?? 0);
                    $returned  = (float) ($reqItem->returned_total_pieces ?? 0);
                    $sold      = (float) ($reqItem->quantity_sold ?? 0);
                    $available = $issued - ($consumed + $returned + $sold);

                    if ($available <= 0) continue;

                    $deductAmount          = min($quantityToDeduct, $available);
                    $reqItem->quantity_sold = $sold + $deductAmount;
                    $reqItem->last_sold_at  = now();
                    $reqItem->save();

                    $quantityToDeduct -= $deductAmount;
                }

                if ($quantityToDeduct > 0) {
                    throw new \Exception("Insufficient stock for {$inventoryItem->name}.");
                }

                $unitPrice = $inventoryItem->selling_price ?? $itemData['unit_price'];

                SalesOrderItem::create([
                    'sales_order_id'    => $order->id,
                    'inventory_item_id' => $itemData['item_id'],
                    'item_name'         => $inventoryItem->name,
                    'quantity'          => $itemData['quantity'],
                    'unit_price'        => $unitPrice,
                    'total_price'       => $itemData['quantity'] * $unitPrice,
                ]);

                $itemsList[] = [
                    'name'       => $inventoryItem->name,
                    'quantity'   => $itemData['quantity'],
                    'unit_price' => $unitPrice,
                ];
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'order_id'     => $order->id,
                'order_number' => $orderNumber,
                'total_amount' => $request->total_amount,
                'items'        => $itemsList,
                'message'      => 'Invoice created successfully',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bar create invoice failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * View Receipt for paid order
     */
    public function getReceipt($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $order = SalesOrder::with('items')->findOrFail($id);

            if ($order->payment_status !== 'paid') {
                return redirect()->route('bar.cashier.orders')->with('error', 'Order not paid yet.');
            }

            return view('bar.cashier.receipt', compact('order'));

        } catch (\Exception $e) {
            Log::error('Bar receipt error', ['order_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('bar.cashier.orders')->with('error', 'Order not found.');
        }
    }

    /**
     * Get Invoice for unpaid order
     */
    public function getInvoice($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $order = SalesOrder::with('items')->findOrFail($id);

            if ($order->payment_status !== 'unpaid') {
                return redirect()->route('bar.cashier.orders')->with('error', 'Order already paid.');
            }

            return view('bar.cashier.invoice', compact('order'));

        } catch (\Exception $e) {
            Log::error('Bar invoice error', ['order_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('bar.cashier.orders')->with('error', 'Order not found.');
        }
    }

    /**
     * Reports Index
     */
    public function reports()
    {
        return view('bar.cashier.reports');
    }

    /**
     * Daily Report
     */
    public function dailyReport(Request $request)
    {
        try {
            $date = $request->get('date', today()->toDateString());

            $orders = SalesOrder::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->with('items')
                ->get();

            $totalSales  = $orders->sum('total_amount');
            $totalOrders = $orders->count();

            return view('bar.cashier.reports-daily', compact('orders', 'totalSales', 'totalOrders', 'date'));

        } catch (\Exception $e) {
            Log::error('Bar daily report error', ['error' => $e->getMessage()]);
            return redirect()->route('bar.cashier.reports')->with('error', 'Failed to load report.');
        }
    }

    /**
     * Daily Summary
     */
    public function dailySummary(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $date = $request->get('date', today()->toDateString());

            $sales = SalesOrder::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->orderBy('created_at', 'desc')
                ->get();

            $totalSales    = $sales->sum('total_amount');
            $totalOrders   = $sales->count();
            $averageOrder  = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

            return view('bar.cashier.daily-summary', compact('sales', 'totalSales', 'totalOrders', 'averageOrder', 'date'));

        } catch (\Exception $e) {
            Log::error('Bar daily summary error', ['error' => $e->getMessage()]);
            return redirect()->route('bar.cashier.dashboard')->with('error', 'Failed to load daily summary.');
        }
    }

    public function exportExcel(Request $request)
    {
        return redirect()->back()->with('info', 'Excel export coming soon.');
    }

    public function exportPdf(Request $request)
    {
        return redirect()->back()->with('info', 'PDF export coming soon.');
    }


    /**
 * Invoices List - Show all invoices with filter
 */
public function invoices(Request $request)
{
    try {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'BAR') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $status = $request->get('status', 'unpaid');
        $search = $request->get('search', '');

        $query = SalesOrder::with(['cashier', 'items'])
            ->where('department_id', $user->department_id);

        if ($status === 'unpaid') {
            $query->where('payment_status', 'unpaid');
        } elseif ($status === 'paid') {
            $query->where('payment_status', 'paid');
        }

        if ($search) {
            $query->where('order_number', 'like', "%{$search}%");
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

        // For the header count
        $unpaidInvoices = SalesOrder::where('department_id', $user->department_id)
            ->where('payment_status', 'unpaid')
            ->get();

        return view('bar.cashier.invoices', compact('invoices', 'unpaidInvoices'));

    } catch (\Exception $e) {
        Log::error('Bar invoices error', ['error' => $e->getMessage()]);
        return redirect()->route('bar.cashier.dashboard')->with('error', 'Failed to load invoices.');
    }
}
}
