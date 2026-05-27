@extends('layouts.bar')

@section('title', 'Bar Order Tickets')

@section('content')
<div class="max-w-7xl mx-auto px-4 pb-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Bar Order Tickets</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage drink orders from waiters</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Pending Orders</p>
                    <p class="text-2xl font-bold text-gray-800" id="pendingCount">{{ $activeTickets->count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-clock text-orange-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Completed Today</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $completedTickets->count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Tables</p>
                    @php
                        $uniqueTables = $activeTickets->pluck('table_number')->unique()->count();
                    @endphp
                    <p class="text-2xl font-bold text-gray-800">{{ $uniqueTables }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-chair text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Active Tickets --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-amber-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-cocktail text-orange-500 mr-2"></i>
                        Active Orders
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">Orders waiting to be prepared</p>
                </div>
                <button onclick="refreshTickets()" class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium rounded-lg transition">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                </button>
            </div>
        </div>

        <div id="ticketsContainer" class="divide-y divide-gray-200">
            @forelse($activeTickets as $ticket)
            <div class="ticket-item p-4 hover:bg-gray-50 transition" data-ticket-id="{{ $ticket->id }}">
                <div class="flex items-start justify-between flex-wrap gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 flex-wrap mb-2">
                            <span class="font-mono text-sm font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded">
                                {{ $ticket->ticket_number }}
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-chair mr-1"></i> Table {{ $ticket->table_number }}
                            </span>
                            <span class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($ticket->created_at)->format('h:i A') }}
                            </span>
                            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">
                                Pending
                            </span>
                        </div>

                        <div class="space-y-1">
                            @php
                                $items = is_string($ticket->items) ? json_decode($ticket->items, true) : $ticket->items;
                            @endphp
                            @if(is_array($items))
                                @foreach($items as $item)
                                <div class="text-sm">
                                    <span class="font-semibold text-gray-800">{{ $item['quantity'] }}x</span>
                                    <span class="text-gray-700">{{ $item['item_name'] }}</span>
                                    @if(!empty($item['supplement']))
                                    <span class="text-xs text-orange-600 ml-2">
                                        <i class="fas fa-plus-circle mr-1"></i>{{ $item['supplement'] }}
                                    </span>
                                    @endif
                                    @if(!empty($item['comments']))
                                    <div class="text-xs text-gray-500 ml-6 mt-0.5">
                                        <i class="fas fa-comment mr-1"></i> {{ $item['comments'] }}
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button onclick="printTicket({{ $ticket->id }})"
                                class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-medium rounded-lg transition">
                            <i class="fas fa-print mr-1"></i> Print
                        </button>
                        <button onclick="completeTicket({{ $ticket->id }})"
                                class="px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-600 text-xs font-medium rounded-lg transition">
                            <i class="fas fa-check mr-1"></i> Complete
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400">
                <i class="fas fa-check-circle text-3xl mb-2 block"></i>
                <p class="text-sm">No active orders</p>
                <p class="text-xs mt-1">New orders will appear here</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Completed Tickets History --}}
    @if($completedTickets->count() > 0)
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-600">
                <i class="fas fa-history mr-2"></i>
                Completed Orders (Last 20)
            </h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($completedTickets as $ticket)
            <div class="ticket-item p-3 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-bold text-gray-600">{{ $ticket->ticket_number }}</span>
                        <span class="text-xs text-gray-500">Table {{ $ticket->table_number }}</span>
                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($ticket->created_at)->format('h:i A') }}</span>
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Completed</span>
                    </div>
                    <button onclick="printTicket({{ $ticket->id }})"
                            class="text-blue-500 hover:text-blue-700 text-xs">
                        <i class="fas fa-print"></i> Reprint
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
    function refreshTickets() {
        location.reload();
    }

    function printTicket(ticketId) {
        window.open('/bar/tickets/' + ticketId + '/print', '_blank', 'width=400,height=500');
    }

    function completeTicket(ticketId) {
        if (!confirm('Mark this order as completed?')) return;

        fetch('/bar/tickets/' + ticketId + '/complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Order completed successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Error completing order', 'error');
        });
    }
</script>
@endsection
