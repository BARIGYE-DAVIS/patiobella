@extends('layouts.store')

@section('title', 'Inventory Items')

@section('page-title', 'Inventory Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <p class="text-gray-600">Manage your store inventory items</p>
    </div>
    <a href="{{ route('store.inventory.create') }}" 
       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add New Item
    </a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-lg shadow-sm mb-6 p-4">
    <form method="GET" action="{{ route('store.inventory.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search by name or code..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <select name="category_id" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <select name="is_active" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Filter
        </button>
        <a href="{{ route('store.inventory.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
            Reset
        </a>
    </form>
</div>

{{-- Inventory Table --}}
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr class="border-b border-gray-200">
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Current Stock</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Cost</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($items as $item)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <span class="font-mono text-sm text-gray-600">{{ $item->item_code ?? $item->code ?? 'N/A' }}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-800">{{ $item->name }}</div>
                    @if($item->description)
                        <div class="text-xs text-gray-500 truncate max-w-xs">{{ $item->description }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $item->category ? $item->category->name : '—' }}
                </td>
                <td class="px-6 py-4 text-right">
                    <span class="text-sm font-semibold {{ ($item->current_stock ?? 0) <= ($item->minimum_stock ?? 0) ? 'text-red-600' : 'text-gray-800' }}">
                        {{ number_format($item->current_stock ?? 0, 2) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right text-sm text-gray-600">
                    {{ number_format($item->unit_cost ?? 0, 2) }}
                </td>
                <td class="px-6 py-4 text-center">
                    @if($item->is_active)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('store.inventory.show', $item->id) }}" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                    <a href="{{ route('store.inventory.edit', $item->id) }}" class="text-amber-600 hover:text-amber-800">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    No inventory items found.
                    <a href="{{ route('store.inventory.create') }}" class="text-blue-600 hover:underline ml-2">Add your first item</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $items->appends(request()->query())->links() }}
</div>
@endsection