@extends('layouts.kitchen')

@section('title', 'Stock Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 pb-8">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $inventoryItem->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Stock details and consumption history</p>
        </div>
        <a href="{{ route('kitchen.stock.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition">
            <i class="fas fa-arrow-left"></i> Back to Stock
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Item Code</p>
                    <p class="text-lg font-bold text-gray-800">{{ $inventoryItem->item_code ?? 'N/A' }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-barcode text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Unit of Measure</p>
                    <p class="text-lg font-bold text-gray-800">{{ ucfirst($inventoryItem->base_unit ?? 'Piece') }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-ruler text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Current Stock</p>
                    <p class="text-lg font-bold {{ $currentStock > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ number_format($currentStock, 2) }} {{ ucfirst($inventoryItem->base_unit ?? 'units') }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-database text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 text-center">
            <p class="text-xs text-gray-500">Total Issued</p>
            <p class="text-xl font-bold text-green-600">{{ number_format($totalIssued, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 text-center">
            <p class="text-xs text-gray-500">Total Consumed</p>
            <p class="text-xl font-bold text-orange-600">{{ number_format($totalConsumed, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 text-center">
            <p class="text-xs text-gray-500">Total Sold</p>
            <p class="text-xl font-bold text-blue-600">{{ number_format($totalSold, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 text-center">
            <p class="text-xs text-gray-500">Total Returned</p>
            <p class="text-xl font-bold text-yellow-600">{{ number_format($totalReturned, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-amber-50">
            <h2 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-history text-orange-500 mr-2"></i>
                Requisition History
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">All requisitions for this item</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Requisition #</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Issued</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Consumed</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Sold</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Returned</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($requisitionItems as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-mono font-medium text-gray-800">
                            {{ $item->departmentRequisition->requisition_number ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $item->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right font-semibold text-green-600">
                            {{ number_format($item->issued_total_pieces ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right text-orange-600">
                            {{ number_format($item->quantity_consumed ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right text-blue-600">
                            {{ number_format($item->quantity_sold ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right text-yellow-600">
                            {{ number_format($item->returned_total_pieces ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $remaining = ($item->issued_total_pieces ?? 0) - (($item->quantity_consumed ?? 0) + ($item->quantity_sold ?? 0) + ($item->returned_total_pieces ?? 0));
                            @endphp
                            @if($remaining <= 0)
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Completed</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Active</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-history text-3xl mb-2 block opacity-30"></i>
                            <p class="text-sm">No requisition history found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
