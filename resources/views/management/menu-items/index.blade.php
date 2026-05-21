{{-- resources/views/management/menu-items/index.blade.php --}}

@extends('layouts.management')

@section('title', 'Menu Items')

@section('page-title', 'All Menu Items')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .menu-item-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .menu-item-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        border-color: #f97316;
    }
    .badge-margin-high {
        background-color: #d1fae5;
        color: #065f46;
    }
    .badge-margin-medium {
        background-color: #fed7aa;
        color: #92400e;
    }
    .badge-margin-low {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .gradient-bg {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    }
    .modal-enter {
        animation: modalFadeIn 0.2s ease-out;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to   { opacity: 1; transform: scale(1); }
    }
    .filter-input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    }
    .spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #f97316;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .recipe-table {
        width: 100%;
        border-collapse: collapse;
    }
    .recipe-table th,
    .recipe-table td {
        padding: 10px 8px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }
    .recipe-table th {
        background-color: #f9fafb;
        font-weight: 600;
        font-size: 0.75rem;
        color: #374151;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    .info-label {
        font-weight: 600;
        color: #374151;
    }
    .info-value {
        color: #1f2937;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 pb-12">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Menu Items</h1>
            <p class="text-sm text-gray-500 mt-0.5">All dishes across all menus with their pricing and recipes</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchInput"
                       placeholder="Search items..."
                       class="pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 w-64 filter-input">
            </div>
            <select id="categoryFilter" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 bg-white filter-input">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <select id="menuFilter" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 bg-white filter-input">
                <option value="">All Menus</option>
                @foreach($menus as $menu)
                    <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                @endforeach
            </select>
            <a href="{{ route('management.menus.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                <i class="fas fa-plus"></i> New Menu
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Items Grid --}}
    <div id="itemsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($menuItems as $item)
            <div class="menu-item-card bg-white rounded-xl overflow-hidden shadow-sm"
                 data-item-id="{{ $item->id }}"
                 data-item-name="{{ $item->name }}"
                 data-category-id="{{ $item->menu_item_category_id }}"
                 data-menu-id="{{ $item->menu_id }}">

                {{-- Card Header with Gradient --}}
                <div class="gradient-bg px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-white/80 flex items-center justify-center">
                            <i class="fas fa-utensils text-orange-500 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm">{{ Str::limit($item->name, 30) }}</h3>
                            <p class="text-xs text-gray-500">{{ $item->menu->name ?? 'No Menu' }}</p>
                        </div>
                    </div>
                    <div>
                        @if($item->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                <i class="fas fa-circle text-[5px]"></i> Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                <i class="fas fa-circle text-[5px]"></i> Inactive
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-4 space-y-3">
                    {{-- Category & Price --}}
                    <div class="flex items-center justify-between">
                        {{-- Instead of $item->category->name, try this --}}
<span class="text-xs text-gray-500">
    @php
        $cat = \App\Models\MenuItemCategory::find($item->menu_item_category_id);
        echo $cat ? $cat->name : 'Uncategorized';
    @endphp
</span>
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-800">{{ number_format($item->selling_price, 0) }} UGX</p>
                            <p class="text-xs text-gray-400">Selling Price</p>
                        </div>
                    </div>

                    {{-- Material Cost & Margin --}}
                    <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100">
                        <div>
                            <p class="text-xs text-gray-400">Material Cost</p>
                            <p class="text-sm font-semibold text-gray-700">{{ number_format($item->m_cost ?? 0, 0) }} UGX</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Margin</p>
                            @php
                                $margin = $item->age_margins ?? 0;
                                $marginClass = $margin >= 50 ? 'badge-margin-high' : ($margin >= 30 ? 'badge-margin-medium' : 'badge-margin-low');
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $marginClass }}">
                                {{ number_format($margin, 1) }}%
                            </span>
                        </div>
                    </div>

                    {{-- Action Buttons (Only View Recipe - NO EDIT) --}}
                    <div class="pt-2">
                        <button type="button"
                                class="view-item w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-medium rounded-lg transition-colors"
                                data-id="{{ $item->id }}"
                                data-name="{{ $item->name }}">
                            <i class="fas fa-eye text-xs"></i> View Complete Recipe Details
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Empty State --}}
    @if($menuItems->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-12 text-center">
            <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-utensils text-3xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">No Menu Items Yet</h3>
            <p class="text-sm text-gray-500 mb-4">Create a menu first, then add items to it</p>
            <a href="{{ route('management.menus.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="fas fa-plus"></i> Create Menu
            </a>
        </div>
    @endif
</div>

{{-- View Recipe Modal (Complete Details) --}}
<div id="viewRecipeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-xl modal-enter">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-amber-50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center">
                    <i class="fas fa-utensils text-white text-sm"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Recipe & Pricing Details</h3>
                    <p id="modalItemName" class="text-sm text-gray-500"></p>
                </div>
            </div>
            <button type="button" class="closeRecipeModal text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto max-h-[75vh]" id="recipeContent">
            <div class="text-center text-gray-500 py-8">
                <div class="spinner mb-3"></div>
                Loading recipe details...
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
            <button type="button" class="closeRecipeModal px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {

    // ── Format Money ──────────────────────────────────────────────────
    function formatMoney(amount) {
        return Math.round(amount || 0).toLocaleString('en-UG') + ' UGX';
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, '&amp;')
                   .replace(/</g, '&lt;')
                   .replace(/>/g, '&gt;')
                   .replace(/"/g, '&quot;')
                   .replace(/'/g, '&#39;');
    }

    // ── Filter Items (Search, Category, Menu) ────────────────────────
    function filterItems() {
        let search = $('#searchInput').val().toLowerCase();
        let categoryId = $('#categoryFilter').val();
        let menuId = $('#menuFilter').val();

        $('.menu-item-card').each(function() {
            let card = $(this);
            let itemName = card.data('item-name').toLowerCase();
            let cardCategoryId = String(card.data('category-id') || '');
            let cardMenuId = String(card.data('menu-id') || '');

            let matchesSearch = search === '' || itemName.includes(search);
            let matchesCategory = categoryId === '' || cardCategoryId === categoryId;
            let matchesMenu = menuId === '' || cardMenuId === menuId;

            if (matchesSearch && matchesCategory && matchesMenu) {
                card.show();
            } else {
                card.hide();
            }
        });

        let visibleCount = $('.menu-item-card:visible').length;
        if (visibleCount === 0 && $('.menu-item-card').length > 0) {
            if ($('#noResultsMsg').length === 0) {
                $('#itemsGrid').after('<div id="noResultsMsg" class="text-center py-12 text-gray-400"><i class="fas fa-search text-3xl mb-2 block"></i>No items match your filters</div>');
            }
        } else {
            $('#noResultsMsg').remove();
        }
    }

    $('#searchInput, #categoryFilter, #menuFilter').on('change keyup', filterItems);

    // ── View Complete Recipe Modal ─────────────────────────────────────
    $('.view-item').on('click', function() {
        let btn = $(this);
        let itemId = btn.data('id');
        let itemName = btn.data('name');

        $('#modalItemName').text(itemName);
        $('#recipeContent').html('<div class="text-center text-gray-500 py-8"><div class="spinner mb-3"></div>Loading recipe details...</div>');
        $('#viewRecipeModal').removeClass('hidden').addClass('flex');

        let url = '/management/menu-items/' + itemId + '/recipe';

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    let item = response.item;
                    let ingredients = response.ingredients || [];

                    let html = `
                        <div class="space-y-6">
                            {{-- Item Details Section --}}
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-800 mb-3 border-b pb-2">📋 Item Information</h4>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="info-row">
                                        <span class="info-label">Item Name:</span>
                                        <span class="info-value">${escapeHtml(item.name)}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Selling Price:</span>
                                        <span class="info-value font-bold text-emerald-600">${formatMoney(item.selling_price)}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Material Cost:</span>
                                        <span class="info-value">${formatMoney(item.material_cost)}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Margin Percentage:</span>
                                        <span class="info-value">${item.margin.toFixed(1)}%</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Description:</span>
                                        <span class="info-value">${escapeHtml(item.description) || 'No description'}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Allergen Info:</span>
                                        <span class="info-value">${escapeHtml(item.allergen_info) || 'None specified'}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Glovo/Delivery Pricing Section --}}
                            <div class="bg-orange-50 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-800 mb-3 border-b pb-2">🛵 Delivery Platform Pricing (Glovo)</h4>
                                <div class="grid grid-cols-3 gap-3 text-sm">
                                    <div class="text-center">
                                        <p class="text-gray-500 text-xs">Glovo Selling Price</p>
                                        <p class="font-bold text-orange-600 text-lg">${formatMoney(item.glovo_selling_price)}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-gray-500 text-xs">Glovo Commission</p>
                                        <p class="font-bold text-red-600">${formatMoney(item.glovo_commission)}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-gray-500 text-xs">Final Margin</p>
                                        <p class="font-bold text-emerald-600">${formatMoney(item.final_margin)}</p>
                                    </div>
                                </div>
                            </div>
                    `;

                    // Ingredients Section
                    if (ingredients.length > 0) {
                        html += `
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-800 mb-3 border-b pb-2">🥕 Recipe Ingredients</h4>
                                <table class="recipe-table">
                                    <thead>
                                        <tr>
                                            <th>Ingredient</th>
                                            <th class="text-right">Quantity</th>
                                            <th class="text-right">Unit Cost</th>
                                            <th class="text-right">Wastage</th>
                                            <th class="text-right">Total Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        let totalCost = 0;
                        $.each(ingredients, function(i, ing) {
                            totalCost += ing.total_cost;
                            html += `
                                <tr>
                                    <td class="text-gray-800">${escapeHtml(ing.name)}</td>
                                    <td class="text-right">${parseFloat(ing.quantity).toFixed(3)} ${ing.unit}</td>
                                    <td class="text-right">${formatMoney(ing.unit_cost)}</td>
                                    <td class="text-right">${ing.wastage_percentage || 0}%</td>
                                    <td class="text-right font-semibold">${formatMoney(ing.total_cost)}</td>
                                </tr>
                            `;
                        });

                        html += `
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-yellow-50">
                                            <td colspan="4" class="text-right font-bold">Total Material Cost:</td>
                                            <td class="text-right font-bold text-emerald-600">${formatMoney(totalCost)}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <i class="fas fa-pepper-hot text-3xl text-gray-300 mb-2 block"></i>
                                <p class="text-gray-500">No ingredients added yet for this menu item.</p>
                                <p class="text-xs text-gray-400">Add ingredients when editing the menu.</p>
                            </div>
                        `;
                    }

                    html += `</div>`;
                    $('#recipeContent').html(html);
                } else {
                    $('#recipeContent').html('<div class="text-center text-red-500 py-8">Failed to load recipe details.</div>');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                let errorMsg = 'Failed to load recipe details.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#recipeContent').html('<div class="text-center text-red-500 py-8">' + errorMsg + '</div>');
            }
        });
    });

    // ── Close Modal ──────────────────────────────────────────────────
    $('.closeRecipeModal').on('click', function() {
        $('#viewRecipeModal').addClass('hidden').removeClass('flex');
    });

    $(window).on('click', function(e) {
        if ($(e.target).is('#viewRecipeModal')) {
            $('#viewRecipeModal').addClass('hidden').removeClass('flex');
        }
    });

});
</script>
@endpush
