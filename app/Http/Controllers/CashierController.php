<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashierController extends Controller
{
    public function index()
    {
        $tables = RestaurantTable::where('is_active', true)
            ->orderBy('table_number')
            ->get();

        return view('cashier.index', compact('tables'));
    }

    public function getOrderByTable($tableId)
    {
        try {
            $order = SalesOrder::where('table_id', $tableId)
                ->where('payment_status', 'unpaid')
                ->where('status', '!=', 'cancelled')
                ->with(['items.menuItem', 'table', 'waiter'])
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'No unpaid order found for this table'
                ]);
            }

            $items = [];
            $totalVat = 0;

            foreach ($order->items as $item) {
                $menuItem = $item->menuItem;
                $vatAmount = ($menuItem->vat_amount ?? 0) * $item->quantity;
                $totalVat += $vatAmount;

                $items[] = [
                    'name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'vat' => $vatAmount
                ];
            }

            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'table_number' => $order->table_number,
                    'waiter_name' => $order->waiter->first_name . ' ' . $order->waiter->last_name,
                    'total_amount' => $order->total_amount,
                    'total_vat' => $totalVat,
                    'items' => $items
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function processPayment(Request $request, $id)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,mobile_money'
        ]);

        DB::beginTransaction();

        try {
            $order = SalesOrder::findOrFail($id);

            $amountPaid = $request->amount_paid;
            $totalAmount = $order->total_amount;
            $changeAmount = $amountPaid - $totalAmount;

            if ($amountPaid < $totalAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Amount paid is less than total due'
                ], 400);
            }

            $order->amount_paid = $amountPaid;
            $order->change_amount = $changeAmount;
            $order->payment_method = $request->payment_method;
            $order->payment_status = 'paid';
            $order->status = 'completed';
            $order->cashier_id = Auth::id();
            $order->updated_by = Auth::id();
            $order->save();

            if ($order->table_id) {
                RestaurantTable::where('id', $order->table_id)->update(['is_occupied' => 0]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'order_number' => $order->order_number,
                'total_amount' => $totalAmount,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function printReceipt($id)
    {
        $order = SalesOrder::with(['items.menuItem', 'table', 'waiter'])->findOrFail($id);

        $totalVat = 0;
        foreach ($order->items as $item) {
            $menuItem = $item->menuItem;
            $totalVat += ($menuItem->vat_amount ?? 0) * $item->quantity;
        }

        return view('cashier.receipt', compact('order', 'totalVat'));
    }
}
