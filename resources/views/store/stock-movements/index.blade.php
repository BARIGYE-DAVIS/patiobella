@extends('layouts.store')

@section('title', 'Stock Movements')

@section('page-title', 'Stock Movements')

@section('content')
<div class="mb-6">
    <p class="text-gray-600">View all stock movements in the store</p>
</div>

{{-- Filters --}}
<div class="bg-white rounded-lg shadow-sm mb-6 p-4">
    <form method="GET" action="{{ route('store.stock-movements.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search by movement number..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <select name="movement_type" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Types</option>
                <option value="in" {{ request('movement_type') == 'in' ? 'selected' : '' }}>Stock In</option>
                <option value="out" {{ request('movement_type') == 'out' ? 'selected' : '' }}>Stock Out</option>
                <option value="adjustment" {{ request('movement_type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                <option value="transfer" {{ request('movement_type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
            </select>
        </div>
        
        <div>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Filter
        </button>
        <a href="{{ route('store.stock-movements.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
            Reset
        </a>
    </form>
</div>

{{-- Stock Movements Table --}}
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr class="border-b border-gray-200">
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Movement No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($movements as $movement)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <span class="font-mono text-sm text-gray-600">{{ $movement->movement_number }}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-800">
                        {{ $movement->inventoryItem ? $movement->inventoryItem->name : 'Item not found' }}
                    </div>
                </td>
                <td class="px-6 py-4">
                    @php
                        $typeColors = [
                            'in' => 'bg-green-100 text-green-800',
                            'out' => 'bg-red-100 text-red-800',
                            'adjustment' => 'bg-yellow-100 text-yellow-800',
                            'transfer' => 'bg-blue-100 text-blue-800',
                        ];
                        $typeLabels = [
                            'in' => 'Stock In',
                            'out' => 'Stock Out',
                            'adjustment' => 'Adjustment',
                            'transfer' => 'Transfer',
                        ];
                    @endphp
                    <span class="px-2 py-1 text-xs rounded-full {{ $typeColors[$movement->movement_type] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $typeLabels[$movement->movement_type] ?? ucfirst($movement->movement_type) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <span class="text-sm font-semibold {{ $movement->movement_type == 'out' ? 'text-red-600' : 'text-green-600' }}">
                        {{ $movement->movement_type == 'out' ? '-' : '+' }}{{ number_format($movement->quantity, 2) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$movement->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($movement->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $movement->created_at ? $movement->created_at->format('d/m/Y H:i') : '—' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $movement->createdBy ? $movement->createdBy->first_name . ' ' . $movement->createdBy->last_name : '—' }}
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('store.stock-movements.show', $movement->id) }}" class="text-blue-600 hover:text-blue-800">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                    No stock movements found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $movements->appends(request()->query())->links() }}
</div>
@endsection