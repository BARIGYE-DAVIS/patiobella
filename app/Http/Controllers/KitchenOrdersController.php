<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KitchenOrdersController extends Controller
{
    /**
     * Display kitchen order tickets
     */
    public function index()
    {
        $activeTickets = Ticket::where('ticket_type', 'kitchen')
            ->where('is_printed', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        $completedTickets = Ticket::where('ticket_type', 'kitchen')
            ->where('is_printed', 1)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('kitchen.order-tickets', compact('activeTickets', 'completedTickets'));
    }

    /**
     * Print ticket
     */
    public function printTicket($id)
    {
        $ticket = Ticket::with('salesOrder')->findOrFail($id);
        $items = json_decode($ticket->items, true);

        return view('kitchen.print-ticket', compact('ticket', 'items'));
    }

    /**
     * Complete ticket
     */
    public function completeTicket($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->is_printed = 1;
            $ticket->printed_at = now();
            $ticket->save();

            return response()->json([
                'success' => true,
                'message' => 'Ticket completed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
