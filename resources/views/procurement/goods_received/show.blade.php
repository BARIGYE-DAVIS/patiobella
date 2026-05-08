@extends('layouts.procurement')
@section('title', 'Goods Received Note Details')
@section('page-title', 'Goods Received Note Details')

@section('content')
<style>
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-completed {
        background-color: #d1fae5;
        color: #065f46;
    }
    .status-draft {
        background-color: #fef3c7;
        color: #92400e;
    }
    .info-label {
        font-weight: 600;
        color: #4b5563;
        width: 140px;
        display: inline-block;
    }
</style>

@if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-800 to-green-700 px-6 py-4 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-white">GRN #{{ $grn->grn_number }}</h2>
                <p class="text-gray-300 text-sm mt-1">Created: {{ $grn->created_at->format('F d, Y H:i') }}</p>
            </div>
            <div class="flex space-x-3">
                @if($grn->status == 'completed')
                    <form action="{{ route('procurement.goods-received.send-to-store', $grn->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Send to Store
                        </button>
                    </form>
                @endif
                <a href="{{ route('procurement.goods-received.index') }}"
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- GRN Information --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden lg:col-span-2">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3">
                <h3 class="text-lg font-semibold text-white">Goods Received Information</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="pb-2 border-b">
                        <span class="info-label">GRN Number:</span>
                        <span class="text-gray-800">{{ $grn->grn_number }}</span>
                    </div>
                    <div class="pb-2 border-b">
                        <span class="info-label">PO Number:</span>
                        <span class="text-gray-800">{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</span>
                    </div>
                    <div class="pb-2 border-b">
                        <span class="info-label">Vendor:</span>
                        <span class="text-gray-800">{{ $grn->vendor->name ?? 'N/A' }}</span>
                    </div>
                    <div class="pb-2 border-b">
                        <span class="info-label">Received Date:</span>
                        <span class="text-gray-800">{{ $grn->received_date->format('F d, Y') }}</span>
                    </div>
                    @if($grn->delivery_note_number)
                    <div class="pb-2 border-b">
                        <span class="info-label">Vendor DN #:</span>
                        <span class="text-gray-800">{{ $grn->delivery_note_number }}</span>
                    </div>
                    @endif
                    <div class="pb-2 border-b">
                        <span class="info-label">Status:</span>
                        <span class="status-badge status-{{ $grn->status }}">{{ ucfirst($grn->status) }}</span>
                    </div>
                    @if($grn->notes)
                    <div class="pb-2 border-b">
                        <span class="info-label">Notes:</span>
                        <span class="text-gray-800">{{ $grn->notes }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-3">
                <h3 class="text-lg font-semibold text-white">Summary</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex justify-between pb-2 border-b">
                        <span class="font-semibold">Total Items:</span>
                        <span>{{ $grn->items->count() }}</span>
                    </div>
                    <div class="flex justify-between pb-2 border-b">
                        <span class="font-semibold">Total Received Value:</span>
                        <span class="font-bold text-green-600">UGX {{ number_format($grn->items->sum('total_cost'), 2) }}</span>
                    </div>
                    <div class="flex justify-between pb-2 border-b">
                        <span class="font-semibold">Created By:</span>
                        <span>{{ $grn->createdBy->name ?? 'Procurement department' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-3">
            <h3 class="text-lg font-semibold text-white">Received Items</h3>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 border text-left">Item</th>
                        <th class="p-3 border text-center">Ordered</th>
                        <th class="p-3 border text-center">Received</th>
                        <th class="p-3 border text-center">Accepted</th>
                        <th class="p-3 border text-center">Rejected</th>
                        <th class="p-3 border text-center">Unit Cost (UGX)</th>
                        <th class="p-3 border text-center">Total (UGX)</th>
                        <th class="p-3 border text-left">Rejection Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grn->items as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 border font-semibold">{{ $item->inventoryItem->name ?? 'N/A' }}</td>
                        <td class="p-3 border text-center">{{ number_format($item->quantity_ordered, 2) }}</td>
                        <td class="p-3 border text-center">{{ number_format($item->quantity_received, 2) }}</td>
                        <td class="p-3 border text-center font-semibold text-green-600">{{ number_format($item->quantity_accepted, 2) }}</td>
                        <td class="p-3 border text-center text-red-600">{{ number_format($item->quantity_rejected, 2) }}</td>
                        <td class="p-3 border text-center">{{ number_format($item->unit_cost, 2) }}</td>
                        <td class="p-3 border text-center">{{ number_format($item->total_cost, 2) }}</td>
                        <td class="p-3 border">{{ $item->rejection_reason ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td colspan="6" class="p-3 border text-right">Total Value Received:</td>
                        <td class="p-3 border text-center text-green-600">UGX {{ number_format($grn->items->sum('total_cost'), 2) }}</td>
                        <td class="p-3 border"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
