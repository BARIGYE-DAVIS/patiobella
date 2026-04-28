@extends('layouts.procurement')
@section('title', 'Goods Received Notes')
@section('page-title', 'Goods Received Notes')

@section('content')
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-green-800 to-green-700 px-6 py-4 flex justify-between items-center">
        <h2 class="text-xl font-bold text-white">Goods Received Notes</h2>
        <a href="{{ route('procurement.goods-received.create') }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Goods Received
        </a>
    </div>
    <div class="p-6 overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-lg">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 border text-left">GRN Number</th>
                    <th class="p-3 border text-left">PO Number</th>
                    <th class="p-3 border text-left">Vendor</th>
                    <th class="p-3 border text-center">Received Date</th>
                    <th class="p-3 border text-center">Status</th>
                    <th class="p-3 border text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grns as $grn)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 border font-semibold">{{ $grn->grn_number }}</td>
                    <td class="p-3 border">{{ $grn->purchaseOrder->po_number ?? 'N/A' }}</td>
                    <td class="p-3 border">{{ $grn->vendor->name ?? 'N/A' }}</td>
                    <td class="p-3 border text-center">{{ $grn->received_date->format('Y-m-d') }}</td>
                    <td class="p-3 border text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            {{ ucfirst($grn->status) }}
                        </span>
                    </td>
                    <td class="p-3 border text-center">
                        <a href="{{ route('procurement.goods-received.show', $grn->id) }}" 
                           class="text-blue-600 hover:text-blue-800">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-500">No Goods Received Notes found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $grns->links() }}
        </div>
    </div>
</div>
@endsection