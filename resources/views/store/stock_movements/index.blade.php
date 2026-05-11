@extends('layouts.store')

@section('title', 'Stock Movements')

@section('page-title', 'Stock Movements History')

@section('content')
<style>
    .filter-card {
        background-color: #f9fafb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .movement-in {
        color: #10b981;
    }
    .movement-out {
        color: #ef4444;
    }
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-purchase {
        background-color: #dbeafe;
        color: #1e40af;
    }
    .badge-sale {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .badge-adjustment {
        background-color: #fef3c7;
        color: #92400e;
    }
    .badge-transfer {
        background-color: #e0e7ff;
        color: #3730a3;
    }
    .badge-grn {
        background-color: #d1fae5;
        color: #065f46;
    }
    .view-btn {
        color: #3b82f6;
        transition: color 0.2s;
    }
    .view-btn:hover {
        color: #2563eb;
    }
    .pack-display {
        font-weight: 600;
        color: #d97706;
    }
    .direct-display {
        color: #059669;
        font-weight: 500;
    }
</style>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">Stock Movements</h3>
        <p class="text-sm text-gray-500">Track all stock in and out movements</p>
    </div>

    {{-- Filter Section --}}
    <div class="filter-card mx-6 mt-4">
        <form method="GET" action="{{ route('store.stock-movements.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                <select name="item_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">-- All Items --</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }} ({{ $item->item_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Movement Type</label>
                <select name="movement_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">-- All Types --</option>
                    @foreach($movementTypes as $type)
                        <option value="{{ $type->id }}" {{ request('movement_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                <a href="{{ route('store.stock-movements.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Reset</a>
            </div>
        </form>
    </div>

    {{-- Results Table --}}
    <div class="p-6 overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg">
            <thead class="bg-gray-50">
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Movement #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity Received</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Pieces</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Cost</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Value</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($movements as $movement)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-mono">{{ $movement->movement_number }}</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="font-semibold">{{ $movement->inventoryItem->name ?? 'N/A' }}</span>
                        <br>
                        <span class="text-xs text-gray-500">{{ $movement->inventoryItem->item_code ?? '' }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @php
                            $typeClass = 'badge-adjustment';
                            if (str_contains($movement->movementType->name ?? '', 'Purchase')) $typeClass = 'badge-purchase';
                            elseif (str_contains($movement->movementType->name ?? '', 'Sale')) $typeClass = 'badge-sale';
                            elseif (str_contains($movement->movementType->name ?? '', 'GRN')) $typeClass = 'badge-grn';
                            elseif (str_contains($movement->movementType->name ?? '', 'Transfer')) $typeClass = 'badge-transfer';
                        @endphp
                        <span class="badge {{ $typeClass }}">{{ $movement->movementType->name ?? 'N/A' }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-center">
                        @if($movement->pack_type)
                            <div class="pack-display">
                                {{ number_format($movement->number_of_packs) }}
                                {{ ucfirst($movement->pack_type) }}(s)
                                <span class="text-gray-500 text-xs">× {{ number_format($movement->pack_size) }} pcs</span>
                            </div>
                        @else
                            <div class="direct-display">
                                {{ number_format($movement->quantity, 2) }}
                                <span class="text-gray-500 text-xs">{{ $movement->inventoryItem->default_unit_of_measure_id ?? 'units' }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-center font-bold text-blue-600">
                        {{ number_format($movement->quantity_in_base_unit, 2) }}
                        <span class="text-xs text-gray-500">pieces</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-right">UGX {{ number_format($movement->unit_cost ?? 0, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right">UGX {{ number_format($movement->total_value ?? 0, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-center">{{ $movement->movement_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('store.stock-movements.show', $movement->id) }}"
                           class="view-btn inline-flex items-center gap-1" title="View Details">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">No stock movements found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $movements->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
