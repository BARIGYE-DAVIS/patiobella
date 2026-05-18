<?php
// app/Http/Controllers/Bar/BarInvoiceController.php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BarInvoiceController extends Controller
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
     * Display list of all invoices (Manager view)
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Manager' && $roleName !== 'Admin' && $roleName !== 'BAR MANAGER') {
                return redirect()->route('bar.dashboard')->with('error', 'Manager access only.');
            }

            $status = $request->get('status', 'all');
            $search = $request->get('search', '');

            $query = SalesOrder::with(['cashier', 'items'])
                ->where('department_id', $user->department_id);

            if ($status === 'unpaid') {
                $query->where('payment_status', 'unpaid');
            } elseif ($status === 'paid') {
                $query->where('payment_status', 'paid');
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhereHas('items', function($itemQuery) use ($search) {
                          $itemQuery->where('item_name', 'like', "%{$search}%");
                      });
                });
            }

            $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

            $stats = [
                'total_invoices' => SalesOrder::where('department_id', $user->department_id)->count(),
                'total_unpaid' => SalesOrder::where('department_id', $user->department_id)->where('payment_status', 'unpaid')->count(),
                'total_paid' => SalesOrder::where('department_id', $user->department_id)->where('payment_status', 'paid')->count(),
                'total_revenue' => SalesOrder::where('department_id', $user->department_id)->where('payment_status', 'paid')->sum('total_amount'),
            ];

            return view('bar.invoices.index', compact('invoices', 'stats', 'status'));

        } catch (\Exception $e) {
            Log::error('Bar invoices index error', ['error' => $e->getMessage()]);
            return redirect()->route('bar.dashboard')->with('error', 'Failed to load invoices.');
        }
    }

    /**
     * Show single invoice details
     */
    public function show($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $roleName = $this->getRoleName($user);
            if ($roleName !== 'Manager' && $roleName !== 'Admin' && $roleName !== 'BAR MANAGER') {
                return redirect()->route('bar.dashboard')->with('error', 'Manager access only.');
            }

            $invoice = SalesOrder::with(['cashier', 'items'])
                ->where('department_id', $user->department_id)
                ->findOrFail($id);

            return view('bar.invoices.show', compact('invoice'));

        } catch (\Exception $e) {
            Log::error('Bar invoice show error', ['error' => $e->getMessage()]);
            return redirect()->route('bar.invoices.index')->with('error', 'Invoice not found.');
        }
    }

    /**
     * Print receipt
     */
    public function receipt($id)
    {
        try {
            $user = Auth::user();

            if (!$user->department || $user->department->name !== 'BAR') {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }

            $order = SalesOrder::with('items')
                ->where('department_id', $user->department_id)
                ->findOrFail($id);

            return view('bar.invoices.receipt', compact('order'));

        } catch (\Exception $e) {
            Log::error('Bar invoice receipt error', ['error' => $e->getMessage()]);
            return redirect()->route('bar.invoices.index')->with('error', 'Receipt not found.');
        }
    }
}
