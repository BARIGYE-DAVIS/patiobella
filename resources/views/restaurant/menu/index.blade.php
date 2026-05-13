{{-- resources/views/restaurant/menu/index.blade.php --}}

@extends('layouts.restaurant')

@section('title', 'Menu Management')

@section('page-title', 'Menu Management')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .stat-card h3 { font-size: 0.7rem; text-transform: uppercase; color: #6b7280; margin-bottom: 0.5rem; letter-spacing: 0.5px; }
    .stat-card .value { font-size: 1.5rem; font-weight: bold; }

    .badge-active { background: #10b981; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; }
    .badge-inactive { background: #ef4444; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; }
    .badge-category {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.6rem;
        font-weight: 500;
    }
    .category-Appetizer { background: #fef3c7; color: #92400e; }
    .category-Main { background: #dbeafe; color: #1e40af; }
    .category-Dessert { background: #fce7f3; color: #9d174d; }
    .category-Beverage { background: #d1fae5; color: #065f46; }
    .category-Side { background: #e0e7ff; color: #3730a3; }

    .data-table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
    .data-table th { background: #f8fafc; padding: 0.75rem; text-align: left; font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0; }
    .data-table td { padding: 0.75rem; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    .data-table tr:hover { background: #f8fafc; }
    .text-right { text-align: right; }

    .filter-card {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    .filter-input {
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.75rem;
        width: 100%;
    }
    .btn-action {
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.7rem;
        transition: all 0.2s;
    }
    .btn-edit { background: #3b82f6; color: white; }
    .btn-edit:hover { background: #2563eb; }
    .btn-delete { background: #ef4444; color: white; }
    .btn-delete:hover { background: #dc2626; }
    .btn-toggle {
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
        font-size: 0.65rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-toggle-active { background: #10b981; color: white; }
    .btn-toggle-inactive { background: #ef4444; color: white; }

    .category-tabs {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 0.5rem;
    }
    .category-tab {
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        background: #f3f4f6;
        color: #6b7280;
        text-decoration: none;
        transition: all 0.2s;
    }
    .category-tab:hover { background: #e5e7eb; }
    .category-tab.active {
        background: #ea580c;
        color: white;
    }
    .category-tab .count {
        background: rgba(0,0,0,0.1);
        border-radius: 20px;
        padding: 0 6px;
        margin-left: 5px;
        font-size: 0.65rem;
    }
    .category-tab.active .count { background: rgba(255,255,255,0.2); }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <h3><i class="fas fa-utensils mr-1"></i> Total Items</h3>
            <div class="value">{{ $menuItems->total() }}</div>
            <p class="text-xs text-gray-500 mt-1">Menu items in database</p>
        </div>
        <div class="stat-card" style="border-left-color: #10b981;">
            <h3><i class="fas fa-check-circle mr-1"></i> Active Items</h3>
            <div class="value">{{ $menuItems->where('is_active', true)->count() }}</div>
            <p class="text-xs text-gray-500 mt-1">Visible on POS</p>
        </div>
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <h3><i class="fas fa-chart-line mr-1"></i> Categories</h3>
            <div class="value">{{ count($categories) }}</div>
            <p class="text-xs text-gray-500 mt-1">Menu categories</p>
        </div>
        <div class="stat-card" style="border-left-color: #8b5cf6;">
            <h3><i class="fas fa-tag mr-1"></i> Avg Price</h3>
            <div class="value">UGX {{ number_format($menuItems->avg('selling_price') ?? 0, 0) }}</div>
            <p class="text-xs text-gray-500 mt-1">Average menu price</p>
        </div>
    </div>

    {{-- Category Tabs --}}
    <div class="category-tabs">
        <a href="{{ route('restaurant.menu.index') }}"
           class="category-tab {{ !request('category') ? 'active' : '' }}">
            All <span class="count">{{ $menuItems->total() }}</span>
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('restaurant.menu.index', ['category' => $cat]) }}"
           class="category-tab {{ request('category') == $cat ? 'active' : '' }}">
            {{ $cat }} <span class="count">{{ $categoryCounts[$cat] ?? 0 }}</span>
        </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('restaurant.menu.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="hidden" name="category" value="{{ request('category') }}">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" class="filter-input" placeholder="Item name or description..." value="{{ request('search') }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="filter-input">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="flex items-end">
                <div class="flex gap-2">
                    <button type="submit" class="bg-orange-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-orange-700">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                    <a href="{{ route('restaurant.menu.index') }}" class="bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-xs hover:bg-gray-400">
                        <i class="fas fa-times mr-1"></i> Clear
                    </a>
                </div>
            </div>
            <div class="flex justify-end">
                <a href="{{ route('restaurant.menu.create') }}" class="bg-green-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-green-700">
                    <i class="fas fa-plus mr-1"></i> New Menu Item
                </a>
            </div>
        </form>
    </div>

    {{-- Menu Items Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%">Status</th>
                        <th style="width: 25%">Item Name</th>
                        <th style="width: 15%">Category</th>
                        <th style="width: 10%">Price</th>
                        <th style="width: 10%">Prep Time</th>
                        <th style="width: 20%">Inventory Link</th>
                        <th style="width: 15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menuItems as $item)
                    <tr>
                        <td class="text-center">
                            <button class="btn-toggle {{ $item->is_active ? 'btn-toggle-active' : 'btn-toggle-inactive' }} toggle-status"
                                    data-id="{{ $item->id }}"
                                    data-active="{{ $item->is_active ? '1' : '0' }}">
                                <i class="fas {{ $item->is_active ? 'fa-check-circle' : 'fa-ban' }} mr-1"></i>
                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td>
                            <div class="font-medium text-gray-800">{{ $item->name }}</div>
                            @if($item->description)
                                <div class="text-xs text-gray-500 truncate max-w-xs">{{ Str::limit($item->description, 60) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge-category category-{{ $item->category }}">
                                <i class="fas
                                    {{ $item->category == 'Appetizer' ? 'fa-bread-slice' : '' }}
                                    {{ $item->category == 'Main' ? 'fa-utensils' : '' }}
                                    {{ $item->category == 'Dessert' ? 'fa-ice-cream' : '' }}
                                    {{ $item->category == 'Beverage' ? 'fa-mug-hot' : '' }}
                                    {{ $item->category == 'Side' ? 'fa-french-fries' : '' }}
                                mr-1"></i>
                                {{ $item->category }}
                            </span>
                        </td>
                        <td class="font-semibold text-gray-800">UGX {{ number_format($item->selling_price, 2) }}</td>
                        <td class="text-gray-600">{{ $item->preparation_time ? $item->preparation_time . ' min' : '—' }}</td>
                        <td>
                            @if($item->inventoryItem)
                                <span class="text-xs text-blue-600">
                                    <i class="fas fa-boxes mr-1"></i> {{ $item->inventoryItem->name }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Not linked</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="{{ route('restaurant.menu.edit', $item->id) }}" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn-action btn-delete delete-item" data-id="{{ $item->id }}" data-name="{{ $item->name }}">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-500 py-8">
                            <i class="fas fa-utensils text-4xl mb-2 block"></i>
                            No menu items found. Click "New Menu Item" to add your first item.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $menuItems->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Confirm Delete</h3>
        </div>
        <div class="p-4">
            <p class="text-gray-600">Are you sure you want to delete <strong id="deleteItemName"></strong>?</p>
            <p class="text-xs text-red-500 mt-2">This action cannot be undone.</p>
        </div>
        <div class="p-4 border-t border-gray-200 flex justify-end gap-2">
            <button id="cancelDeleteBtn" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-400">Cancel</button>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Status
        const toggleBtns = document.querySelectorAll('.toggle-status');
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const itemId = this.dataset.id;
                const isActive = this.dataset.active === '1';
                const newStatus = !isActive;

                fetch(`/restaurant/menu/${itemId}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ is_active: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to update status: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            });
        });

        // Delete Modal
        const deleteModal = document.getElementById('deleteModal');
        const deleteButtons = document.querySelectorAll('.delete-item');
        const deleteForm = document.getElementById('deleteForm');
        const deleteItemName = document.getElementById('deleteItemName');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const itemId = this.dataset.id;
                const itemName = this.dataset.name;
                deleteItemName.textContent = itemName;
                deleteForm.action = `/restaurant/menu/${itemId}`;
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            });
        });

        function closeModal() {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        }

        cancelDeleteBtn.addEventListener('click', closeModal);
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) closeModal();
        });
    });
</script>
@endsection
