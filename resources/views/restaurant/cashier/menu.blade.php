{{-- resources/views/restaurant/cashier/menu.blade.php --}}

@extends('layouts.cashier')

@section('title', 'Menu')

@section('page-title', 'Menu')

@section('content')
<style>
    .search-box {
        padding: 0.5rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.75rem;
        width: 300px;
    }
    .search-box:focus {
        outline: none;
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
        position: sticky;
        top: 0;
    }
    .data-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .data-table tr:hover {
        background: #fef3c7;
    }
    .text-right {
        text-align: right;
    }
    .badge-category {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .badge-appetizer { background: #fef3c7; color: #92400e; }
    .badge-main { background: #dbeafe; color: #1e40af; }
    .badge-dessert { background: #fce7f3; color: #9d174d; }
    .badge-beverage { background: #d1fae5; color: #065f46; }
    .badge-side { background: #e0e7ff; color: #3730a3; }
    .highlight {
        background-color: #fef3c7;
        font-weight: bold;
    }
    .result-badge {
        font-size: 0.7rem;
        color: #6b7280;
    }
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #e5e7eb;
        border-top-color: #ea580c;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Tooltip Styles */
    .tooltip-cell {
        position: relative;
        cursor: help;
        max-width: 200px;
    }
    .tooltip-cell .truncated-text {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .tooltip-cell .tooltip-content {
        visibility: hidden;
        position: absolute;
        z-index: 100;
        bottom: 125%;
        left: 0;
        background-color: #1f2937;
        color: #fff;
        text-align: left;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.7rem;
        width: 250px;
        word-wrap: break-word;
        white-space: normal;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        opacity: 0;
        transition: opacity 0.2s;
    }
    .tooltip-cell:hover .tooltip-content {
        visibility: visible;
        opacity: 1;
    }
    .tooltip-cell .tooltip-content::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 10px;
        border-width: 5px;
        border-style: solid;
        border-color: #1f2937 transparent transparent transparent;
    }

    /* Allergen tooltip */
    .allergen-cell {
        position: relative;
        cursor: help;
    }
    .allergen-badge {
        background: #fef3c7;
        color: #92400e;
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        font-size: 0.65rem;
        display: inline-block;
    }
    .allergen-tooltip {
        visibility: hidden;
        position: absolute;
        z-index: 100;
        bottom: 125%;
        left: 0;
        background-color: #991b1b;
        color: white;
        text-align: left;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.7rem;
        width: 200px;
        word-wrap: break-word;
        white-space: normal;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .allergen-cell:hover .allergen-tooltip {
        visibility: visible;
        opacity: 1;
    }
    .allergen-cell .allergen-tooltip::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 10px;
        border-width: 5px;
        border-style: solid;
        border-color: #991b1b transparent transparent transparent;
    }
</style>

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-utensils mr-2"></i>
                    Restaurant Menu
                </h2>
                <p class="text-orange-100 mt-1">Hover over description or allergens to see full details</p>
            </div>
            <div class="text-right">
                <p class="text-sm"><i class="fas fa-clock mr-1"></i> Updated Today</p>
                <p class="text-2xl font-bold" id="totalItemsCount">{{ $menuItems->count() }} items</p>
            </div>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 flex justify-between items-center flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <i class="fas fa-search text-gray-400"></i>
            <input type="text" id="liveSearch" class="search-box" placeholder="Search by name, category, or price..." autocomplete="off">
            <span id="searchResultCount" class="result-badge"></span>
            <div id="loadingIndicator" class="loading-spinner" style="display: none;"></div>
        </div>
        <div>
            <select id="categoryFilter" class="search-box w-auto">
                <option value="">All Categories</option>
                <option value="Appetizer">Appetizer</option>
                <option value="Main">Main</option>
                <option value="Dessert">Dessert</option>
                <option value="Beverage">Beverage</option>
                <option value="Side">Side</option>
            </select>
        </div>
    </div>

    {{-- Menu Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table" id="menuTable">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 22%">Item Name</th>
                        <th style="width: 12%">Category</th>
                        <th style="width: 10%" class="text-right">Price (UGX)</th>
                        <th style="width: 8%">Prep Time</th>
                        <th style="width: 28%">Description <i class="fas fa-info-circle text-gray-400 text-xs"></i></th>
                        <th style="width: 15%">Allergens <i class="fas fa-exclamation-triangle text-yellow-500 text-xs"></i></th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @php $counter = 1; @endphp
                    @foreach($menuItems as $item)
                    <tr data-name="{{ strtolower($item->name) }}"
                        data-category="{{ $item->category }}"
                        data-price="{{ $item->selling_price }}"
                        data-description="{{ strtolower($item->description ?? '') }}"
                        data-allergen="{{ strtolower($item->allergen_info ?? '') }}">
                        <td class="text-center">{{ $counter++ }}</td>
                        <td class="font-medium text-gray-800">{{ $item->name }}
                            @if($item->preparation_time)
                                <div class="text-xs text-green-600 mt-0.5">
                                    <i class="fas fa-clock"></i> {{ $item->preparation_time }} min
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge-category badge-{{ strtolower($item->category) }}">
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
                        <td class="text-right font-semibold text-orange-600">UGX {{ number_format($item->selling_price, 2) }}</td>
                        <td class="text-center">{{ $item->preparation_time ? $item->preparation_time . ' min' : '—' }}</td>

                        {{-- Description with Hover Tooltip --}}
                        <td class="tooltip-cell">
                            <span class="truncated-text">{{ Str::limit($item->description ?? '—', 50) }}</span>
                            @if($item->description && strlen($item->description) > 50)
                            <span class="tooltip-content">
                                <i class="fas fa-align-left mr-1"></i> Description:<br>
                                {{ $item->description }}
                            </span>
                            @endif
                        </td>

                        {{-- Allergens with Hover Tooltip --}}
                        <td class="allergen-cell">
                            @if($item->allergen_info)
                                <span class="allergen-badge">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ Str::limit($item->allergen_info, 20) }}
                                </span>
                                <span class="allergen-tooltip">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Allergen Information:<br>
                                    {{ $item->allergen_info }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">None</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="noResultsRow" class="hidden p-8 text-center text-gray-500">
            <i class="fas fa-search text-4xl mb-2 block"></i>
            No menu items found matching your search.
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('liveSearch');
        const categoryFilter = document.getElementById('categoryFilter');
        const tableBody = document.getElementById('tableBody');
        const searchResultCount = document.getElementById('searchResultCount');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const noResultsRow = document.getElementById('noResultsRow');

        let searchTimeout;
        let originalRows = [];

        // Store original rows data
        function storeOriginalRows() {
            originalRows = [];
            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(row => {
                originalRows.push({
                    name: row.dataset.name || '',
                    category: row.dataset.category || '',
                    price: parseFloat(row.dataset.price) || 0,
                    description: row.dataset.description || '',
                    allergen: row.dataset.allergen || ''
                });
            });
        }
        storeOriginalRows();

        // Walk all TEXT_NODEs inside an element, skipping script/style
        function walkTextNodes(el, cb) {
            const walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, {
                acceptNode: function(n) {
                    const tag = n.parentNode.tagName;
                    return (tag === 'SCRIPT' || tag === 'STYLE')
                        ? NodeFilter.FILTER_REJECT
                        : NodeFilter.FILTER_ACCEPT;
                }
            });
            const nodes = [];
            let n;
            while ((n = walker.nextNode())) nodes.push(n);
            // Collect first, then mutate — safe DOM walk
            nodes.forEach(cb);
        }

        // Highlight matching text by operating on text nodes only — never touches HTML tags
        function highlightText(row, term) {
            const regex = new RegExp(term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
            walkTextNodes(row, function(node) {
                if (!node.nodeValue.trim()) return;
                if (!regex.test(node.nodeValue)) { regex.lastIndex = 0; return; }
                regex.lastIndex = 0;

                const frag = document.createDocumentFragment();
                let last = 0;
                node.nodeValue.replace(regex, function(match, offset) {
                    frag.appendChild(document.createTextNode(node.nodeValue.slice(last, offset)));
                    const mark = document.createElement('span');
                    mark.className = 'highlight';
                    mark.dataset.hl = '1';
                    mark.textContent = match;
                    frag.appendChild(mark);
                    last = offset + match.length;
                });
                frag.appendChild(document.createTextNode(node.nodeValue.slice(last)));
                node.parentNode.replaceChild(frag, node);
            });
        }

        // Remove highlights by unwrapping all highlight spans
        function removeHighlight(row) {
            row.querySelectorAll('span[data-hl]').forEach(function(span) {
                span.replaceWith(document.createTextNode(span.textContent));
            });
        }

        // Perform live search
        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categoryFilter.value;

            let visibleCount = 0;
            const rows = tableBody.querySelectorAll('tr');

            rows.forEach(function(row, index) {
                const original = originalRows[index];
                if (!original) return;

                const matchesSearch = searchTerm === '' ||
                    original.name.includes(searchTerm) ||
                    original.category.toLowerCase().includes(searchTerm) ||
                    original.price.toString().includes(searchTerm) ||
                    original.description.includes(searchTerm) ||
                    original.allergen.includes(searchTerm);

                const matchesCategory = selectedCategory === '' || original.category === selectedCategory;

                if (matchesSearch && matchesCategory) {
                    row.style.display = '';
                    visibleCount++;
                    removeHighlight(row);
                    if (searchTerm !== '') {
                        highlightText(row, searchTerm);
                    }
                } else {
                    removeHighlight(row);
                    row.style.display = 'none';
                }
            });

            searchResultCount.textContent = visibleCount + ' results found';

            if (visibleCount === 0 && rows.length > 0) {
                noResultsRow.classList.remove('hidden');
                tableBody.style.display = 'none';
            } else {
                noResultsRow.classList.add('hidden');
                tableBody.style.display = 'table-row-group';
            }
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            loadingIndicator.style.display = 'inline-block';
            searchTimeout = setTimeout(function() {
                performSearch();
                loadingIndicator.style.display = 'none';
            }, 300);
        });

        categoryFilter.addEventListener('change', function() {
            performSearch();
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout);
                performSearch();
                loadingIndicator.style.display = 'none';
            }
        });
    });
</script>
@endsection
