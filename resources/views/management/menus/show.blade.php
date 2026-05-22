{{-- resources/views/management/menus/show.blade.php --}}

@extends('layouts.management')

@section('title', 'Menu Details')

@section('page-title', 'Menu Details')

@section('content')
<style>
    :root {
        --primary-orange: #f97316;
        --primary-orange-dark: #ea580c;
        --primary-orange-light: #fff7ed;
    }

    .info-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .info-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .info-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 0.75rem 1.25rem;
        border-bottom: 2px solid #e5e7eb;
        font-weight: 600;
        color: #1e293b;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
    }
    .info-body {
        padding: 1rem 1.25rem;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #64748b;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-value {
        font-weight: 500;
        color: #0f172a;
        font-size: 0.8rem;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .status-active {
        background: #d1fae5;
        color: #065f46;
    }
    .status-active::before {
        content: "●";
        font-size: 0.45rem;
        color: #10b981;
    }
    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }
    .status-inactive::before {
        content: "●";
        font-size: 0.45rem;
        color: #ef4444;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8rem;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.85rem 1rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .data-table td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        transition: background-color 0.2s ease;
        font-size: 0.8rem;
    }
    .data-table tbody tr {
        transition: all 0.2s ease;
    }
    .data-table tbody tr:hover {
        background: linear-gradient(90deg, #fff7ed 0%, #ffffff 100%);
    }
    .btn-back, .btn-manage-items {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.9rem;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        letter-spacing: 0.3px;
    }
    .btn-back {
        background: #64748b;
        color: white;
    }
    .btn-back:hover {
        background: #475569;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(100, 116, 139, 0.3);
    }
    .btn-manage-items {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
    }
    .btn-manage-items:hover {
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        transform: translateY(-1px);
        box-shadow: 0 2px 12px rgba(249, 115, 22, 0.3);
    }
    .badge-category {
        background: #fef3c7;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 500;
        color: #92400e;
        display: inline-block;
    }
    .stat-card {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-radius: 12px;
        padding: 0.85rem;
        text-align: center;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
    }
    .stat-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: #9a3412;
        line-height: 1.2;
    }
    .stat-label {
        font-size: 0.65rem;
        font-weight: 600;
        color: #78350f;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }
    .price-tag {
        font-weight: 700;
        color: #ea580c;
        background: #fff7ed;
        padding: 0.2rem 0.5rem;
        border-radius: 8px;
        display: inline-block;
        font-size: 0.75rem;
    }

    /* Compact Menu Information Card */
    .compact-info-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .compact-info-card:hover {
        border-color: #f97316;
        box-shadow: 0 2px 8px rgba(249, 115, 22, 0.08);
    }
    .compact-info-header {
        background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%);
        padding: 0.6rem 1rem;
        border-bottom: 1px solid #fde68a;
        font-weight: 600;
        color: #854d0e;
        font-size: 0.75rem;
        letter-spacing: 0.3px;
    }
    .compact-info-body {
        padding: 0.75rem 1rem;
    }
    .compact-info-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: 0.35rem 0;
        border-bottom: 1px dashed #f1f5f9;
    }
    .compact-info-row:last-child {
        border-bottom: none;
    }
    .compact-info-label {
        font-weight: 600;
        color: #64748b;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .compact-info-value {
        font-weight: 500;
        color: #1e293b;
        font-size: 0.7rem;
    }

    /* Search and filter bar for items */
    .items-toolbar {
        padding: 0.75rem 1.25rem;
        background: #fafbfc;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .items-search {
        position: relative;
        flex: 1;
        max-width: 280px;
    }
    .items-search i {
        position: absolute;
        left: 0.7rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.7rem;
    }
    .items-search input {
        width: 100%;
        padding: 0.4rem 0.7rem 0.4rem 1.8rem;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.75rem;
        transition: all 0.2s ease;
    }
    .items-search input:focus {
        outline: none;
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    }
    .items-counter {
        font-size: 0.7rem;
        color: #64748b;
        background: #f1f5f9;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
    }

    /* Modal for viewing menu item details */
    .item-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .item-modal.active {
        display: flex;
    }
    .item-modal-content {
        background: white;
        border-radius: 20px;
        max-width: 800px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        animation: modalSlideIn 0.3s ease;
    }
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .item-modal-header {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .item-modal-body {
        padding: 1.25rem;
    }
    .item-detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.6rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .item-detail-label {
        font-weight: 600;
        color: #64748b;
        font-size: 0.7rem;
    }
    .item-detail-value {
        font-weight: 500;
        color: #0f172a;
        font-size: 0.8rem;
    }

    /* Recipe table styling */
    .recipe-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    .recipe-table th,
    .recipe-table td {
        padding: 8px 6px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.7rem;
    }
    .recipe-table th {
        background-color: #f9fafb;
        font-weight: 600;
        font-size: 0.65rem;
        color: #374151;
    }

    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 2rem;
        background: #fafbfc;
    }
    .empty-state-icon {
        width: 50px;
        height: 50px;
        background: #f1f5f9;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.75rem auto;
    }

    .spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #f97316;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .view-item-btn {
        background: none;
        border: none;
        color: #3b82f6;
        cursor: pointer;
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .view-item-btn:hover {
        color: #f97316 !important;
        background-color: #fff7ed;
        transform: scale(1.05);
    }
</style>

<div class="mb-5 flex justify-between items-center flex-wrap gap-3">
    <a href="{{ route('management.menus.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Back to Menus
    </a>
    <a href="{{ route('management.menus.items', $menu->id) }}" class="btn-manage-items">
        <i class="fas fa-utensils"></i> Manage Menu Items
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    {{-- Menu Information - Compact Design with Nice Border --}}
    <div class="lg:col-span-2">
        <div class="compact-info-card">
            <div class="compact-info-header">
                <i class="fas fa-info-circle mr-2" style="color: #f97316;"></i> Menu Information
            </div>
            <div class="compact-info-body">
                <div class="compact-info-row">
                    <span class="compact-info-label"><i class="fas fa-tag mr-1 text-gray-400"></i> Menu Name</span>
                    <span class="compact-info-value font-semibold text-gray-800">{{ $menu->name }}</span>
                </div>
                <div class="compact-info-row">
                    <span class="compact-info-label"><i class="fas fa-building mr-1 text-gray-400"></i> Department</span>
                    <span class="compact-info-value">
                        @php
                            $department = is_object($menu->department) ? $menu->department : null;
                        @endphp
                        {{ $department ? $department->name : ($menu->department ?? 'N/A') }}
                    </span>
                </div>
                <div class="compact-info-row">
                    <span class="compact-info-label"><i class="fas fa-align-left mr-1 text-gray-400"></i> Description</span>
                    <span class="compact-info-value">{{ $menu->description ?? 'No description provided' }}</span>
                </div>
                <div class="compact-info-row">
                    <span class="compact-info-label"><i class="fas fa-sort-numeric-down mr-1 text-gray-400"></i> Sort Order</span>
                    <span class="compact-info-value">{{ $menu->sort_order ?? 0 }}</span>
                </div>
                <div class="compact-info-row">
                    <span class="compact-info-label"><i class="fas fa-power-off mr-1 text-gray-400"></i> Status</span>
                    <span class="compact-info-value">
                        @if($menu->is_active)
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-inactive">Inactive</span>
                        @endif
                    </span>
                </div>
                <div class="compact-info-row">
                    <span class="compact-info-label"><i class="fas fa-user mr-1 text-gray-400"></i> Created By</span>
                    <span class="compact-info-value">
                        @php
                            $creator = is_object($menu->creator) ? $menu->creator : null;
                        @endphp
                        {{ $creator ? ($creator->first_name . ' ' . $creator->last_name) : 'N/A' }}
                    </span>
                </div>
                <div class="compact-info-row">
                    <span class="compact-info-label"><i class="fas fa-calendar-plus mr-1 text-gray-400"></i> Created At</span>
                    <span class="compact-info-value">{{ $menu->created_at ? $menu->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                </div>
                @if($menu->updated_by)
                <div class="compact-info-row">
                    <span class="compact-info-label"><i class="fas fa-user-edit mr-1 text-gray-400"></i> Last Updated By</span>
                    <span class="compact-info-value">
                        @php
                            $updater = is_object($menu->updater) ? $menu->updater : null;
                        @endphp
                        {{ $updater ? ($updater->first_name . ' ' . $updater->last_name) : 'N/A' }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div>
        <div class="info-card">
            <div class="info-header">
                <i class="fas fa-chart-line mr-2" style="color: #f97316;"></i> Statistics
            </div>
            <div class="info-body space-y-2">
                <div class="stat-card">
                    <div class="stat-number">{{ $menu->items_count ?? 0 }}</div>
                    <div class="stat-label">Total Items</div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="stat-card" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); padding: 0.7rem;">
                        <div class="stat-number" style="font-size: 1.3rem; color: #065f46;">{{ $menu->active_items_count ?? 0 }}</div>
                        <div class="stat-label" style="color: #065f46;">Active</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); padding: 0.7rem;">
                        <div class="stat-number" style="font-size: 1.3rem; color: #991b1b;">{{ ($menu->items_count ?? 0) - ($menu->active_items_count ?? 0) }}</div>
                        <div class="stat-label" style="color: #991b1b;">Inactive</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Menu Items Table --}}
<div class="info-card">
    <div class="info-header">
        <i class="fas fa-boxes mr-2" style="color: #f97316;"></i> Menu Items
    </div>

    <div class="items-toolbar">
        <div class="items-search">
            <i class="fas fa-search"></i>
            <input type="text" id="searchItemInput" placeholder="Search items...">
        </div>
        <div class="items-counter">
            <span id="visibleItemsCount">{{ $menu->items->count() }}</span> items
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 35%">Item Name</th>
                    <th style="width: 20%">Category</th>
                    <th style="width: 20%">Price</th>
                    <th style="width: 10%">Status</th>
                    <th style="width: 10%">Action</th>
                </tr>
            </thead>
            <tbody id="menuItemsBody">
                @php $counter = 1; @endphp
                @forelse($menu->items as $item)
                <tr class="menu-item-row"
                    data-item-id="{{ $item->id }}"
                    data-item-name="{{ strtolower($item->name) }}"
                    data-item-category="{{ strtolower($item->menu_item_category_id ?? '') }}">
                    <td class="text-center">{{ $counter++ }}</td>
                    <td class="font-semibold text-gray-800">{{ $item->name }}</td>
                    <td>
                        @php
                            $category = \App\Models\MenuItemCategory::find($item->menu_item_category_id);
                        @endphp
                        @if($category)
                            <span class="badge-category">
                                <i class="fas fa-folder-open mr-1" style="font-size: 0.55rem;"></i>
                                {{ $category->name }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="price-tag">
                            UGX {{ number_format($item->selling_price, 0) }}
                        </span>
                    </td>
                    <td>
                        @if($item->is_active)
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-inactive">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <button type="button" class="view-item-btn" data-item-id="{{ $item->id }}" data-item-name="{{ $item->name }}">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-utensils text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-gray-500 text-sm mb-2">No menu items added yet</p>
                        <a href="{{ route('management.menus.items', $menu->id) }}" class="text-orange-500 hover:underline text-xs">
                            Click here to add items →
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Item Details Modal --}}
<div id="viewRecipeModal" class="item-modal">
    <div class="item-modal-content">
        <div class="item-modal-header">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-orange-500 flex items-center justify-center">
                    <i class="fas fa-utensils text-white text-xs"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Recipe Details</h3>
                    <p id="modalItemName" class="text-xs text-gray-500"></p>
                </div>
            </div>
            <button type="button" class="closeRecipeModal text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="item-modal-body" id="recipeContent">
            <div class="text-center text-gray-500 py-6">
                <div class="spinner mb-2"></div>
                <p class="text-sm">Loading recipe details...</p>
            </div>
        </div>
        <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 flex justify-end">
            <button type="button" class="closeRecipeModal px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-xs font-medium transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function formatMoney(amount) {
        return 'UGX ' + Math.round(amount || 0).toLocaleString('en-UG');
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, '&amp;')
                   .replace(/</g, '&lt;')
                   .replace(/>/g, '&gt;')
                   .replace(/"/g, '&quot;')
                   .replace(/'/g, '&#39;');
    }

    // Search functionality
    const searchInput = document.getElementById('searchItemInput');
    const rows = document.querySelectorAll('.menu-item-row');
    const visibleCountSpan = document.getElementById('visibleItemsCount');

    function filterItems() {
        const searchTerm = searchInput.value.toLowerCase();
        let visibleCount = 0;

        rows.forEach(row => {
            const itemName = row.getAttribute('data-item-name') || '';

            if (searchTerm === '' || itemName.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (visibleCountSpan) {
            visibleCountSpan.textContent = visibleCount;
        }
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', filterItems);
    }

    // View recipe modal
    const modal = document.getElementById('viewRecipeModal');
    const modalItemName = document.getElementById('modalItemName');
    const recipeContent = document.getElementById('recipeContent');

    function closeModal() {
        modal.classList.remove('active');
    }

    document.querySelectorAll('.view-item-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            const itemName = this.getAttribute('data-item-name');

            modalItemName.textContent = itemName;
            recipeContent.innerHTML = '<div class="text-center text-gray-500 py-6"><div class="spinner mb-2"></div><p class="text-sm">Loading recipe details...</p></div>';
            modal.classList.add('active');

            const url = '/management/menu-items/' + itemId + '/recipe';

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    const item = response.item;
                    const ingredients = response.ingredients || [];

                    let html = `
                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <h4 class="font-semibold text-gray-800 text-sm mb-2 border-b pb-1">📋 Item Information</h4>
                                <div class="space-y-1 text-sm">
                                    <div class="item-detail-row">
                                        <span class="item-detail-label">Item Name:</span>
                                        <span class="item-detail-value">${escapeHtml(item.name)}</span>
                                    </div>
                                    <div class="item-detail-row">
                                        <span class="item-detail-label">Selling Price:</span>
                                        <span class="item-detail-value font-bold text-emerald-600">${formatMoney(item.selling_price)}</span>
                                    </div>
                                    <div class="item-detail-row">
                                        <span class="item-detail-label">Material Cost:</span>
                                        <span class="item-detail-value">${formatMoney(item.material_cost)}</span>
                                    </div>
                                    <div class="item-detail-row">
                                        <span class="item-detail-label">Margin:</span>
                                        <span class="item-detail-value">${item.margin.toFixed(1)}%</span>
                                    </div>
                                    <div class="item-detail-row">
                                        <span class="item-detail-label">Description:</span>
                                        <span class="item-detail-value">${escapeHtml(item.description) || 'No description'}</span>
                                    </div>
                                    <div class="item-detail-row">
                                        <span class="item-detail-label">Allergens:</span>
                                        <span class="item-detail-value">${escapeHtml(item.allergen_info) || 'None specified'}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-orange-50 rounded-lg p-3">
                                <h4 class="font-semibold text-gray-800 text-sm mb-2 border-b pb-1">🛵 Delivery Pricing (Glovo)</h4>
                                <div class="grid grid-cols-3 gap-2 text-center">
                                    <div>
                                        <p class="text-gray-500 text-xs">Selling Price</p>
                                        <p class="font-bold text-orange-600 text-sm">${formatMoney(item.glovo_selling_price)}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs">Commission</p>
                                        <p class="font-bold text-red-600 text-sm">${formatMoney(item.glovo_commission)}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs">Final Margin</p>
                                        <p class="font-bold text-emerald-600 text-sm">${formatMoney(item.final_margin)}</p>
                                    </div>
                                </div>
                            </div>
                    `;

                    if (ingredients.length > 0) {
                        html += `
                            <div class="bg-white border border-gray-200 rounded-lg p-3">
                                <h4 class="font-semibold text-gray-800 text-sm mb-2 border-b pb-1">🥕 Recipe Ingredients</h4>
                                <table class="recipe-table">
                                    <thead>
                                        <tr>
                                            <th>Ingredient</th>
                                            <th class="text-right">Qty</th>
                                            <th class="text-right">Unit Cost</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        let totalCost = 0;
                        ingredients.forEach(ing => {
                            totalCost += ing.total_cost;
                            html += `
                                <tr>
                                    <td class="text-gray-700">${escapeHtml(ing.name)}</td>
                                    <td class="text-right">${parseFloat(ing.quantity).toFixed(2)} ${ing.unit}</td>
                                    <td class="text-right">${formatMoney(ing.unit_cost)}</td>
                                    <td class="text-right font-semibold">${formatMoney(ing.total_cost)}</td>
                                </tr>
                            `;
                        });

                        html += `
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-yellow-50">
                                            <td colspan="3" class="text-right font-semibold text-sm">Total Cost:</td>
                                            <td class="text-right font-bold text-emerald-600">${formatMoney(totalCost)}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <i class="fas fa-pepper-hot text-2xl text-gray-300 mb-1 block"></i>
                                <p class="text-gray-500 text-xs">No ingredients added yet</p>
                            </div>
                        `;
                    }

                    html += `</div>`;
                    recipeContent.innerHTML = html;
                } else {
                    recipeContent.innerHTML = '<div class="text-center text-red-500 py-6">Failed to load recipe details</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                recipeContent.innerHTML = '<div class="text-center text-red-500 py-6">Error loading recipe details</div>';
            });
        });
    });

    document.querySelectorAll('.closeRecipeModal').forEach(btn => {
        btn.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
});
</script>
@endsection
