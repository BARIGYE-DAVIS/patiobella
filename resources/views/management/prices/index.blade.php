{{-- resources/views/management/prices/index.blade.php --}}

@extends('layouts.management')

@section('title', 'Price Management')
@section('page-title', 'Price Management')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
        margin-bottom: 1rem;
    }
    .stat-card h3 { font-size: 0.7rem; text-transform: uppercase; color: #6b7280; margin-bottom: 0.5rem; }
    .stat-card .value { font-size: 1.5rem; font-weight: bold; }
    .stat-menu { border-left-color: #ea580c; }
    .stat-sellable { border-left-color: #10b981; }
    .stat-nonsellable { border-left-color: #6b7280; }

    .tabs { display: flex; gap: 0.5rem; border-bottom: 2px solid #e5e7eb; flex-wrap: wrap; }
    .tab-btn {
        padding: 0.75rem 1.5rem; font-size: 0.875rem; font-weight: 500;
        background: transparent; border: none; border-bottom: 2px solid transparent;
        cursor: pointer; transition: all 0.2s; color: #6b7280; margin-bottom: -2px;
    }
    .tab-btn:hover { color: #374151; }
    .tab-btn.active { color: #ea580c; border-bottom-color: #ea580c; }
    .tab-content { display: none; padding: 1.5rem 0; }
    .tab-content.active { display: block; }

    .data-table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
    .data-table th {
        background: #f8fafc; padding: 0.75rem; text-align: left;
        font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0;
        position: sticky; top: 0;
    }
    .data-table td { padding: 0.75rem; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    .data-table tr:hover { background: #fef3c7; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }

    .price-input {
        width: 120px; padding: 0.35rem 0.5rem; border: 1px solid #d1d5db;
        border-radius: 6px; font-size: 0.75rem; text-align: right;
    }
    .price-input:focus { outline: none; border-color: #ea580c; box-shadow: 0 0 0 2px rgba(234,88,12,0.1); }
    .price-display { font-weight: 600; color: #ea580c; }

    .btn-save {
        background: #ea580c; color: white; padding: 0.25rem 0.75rem;
        border-radius: 6px; font-size: 0.7rem; border: none; cursor: pointer; transition: all 0.2s;
    }
    .btn-save:hover { background: #c2410c; }
    .btn-save:disabled { background: #9ca3af; cursor: not-allowed; }

    .btn-toggle {
        padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.7rem;
        font-weight: 500; border: none; cursor: pointer; transition: all 0.2s; white-space: nowrap;
    }
    .btn-toggle-on { background: #10b981; color: white; }
    .btn-toggle-on:hover { background: #dc2626; }
    .btn-toggle-on:hover::after { content: ' (Click to remove)'; }
    .btn-toggle-off { background: #9ca3af; color: white; }
    .btn-toggle-off:hover { background: #059669; }

    .badge-category {
        display: inline-block; padding: 0.2rem 0.6rem;
        border-radius: 20px; font-size: 0.65rem; font-weight: 500;
    }
    .badge-appetizer { background: #fef3c7; color: #92400e; }
    .badge-main { background: #dbeafe; color: #1e40af; }
    .badge-dessert { background: #fce7f3; color: #9d174d; }
    .badge-beverage { background: #d1fae5; color: #065f46; }
    .badge-side { background: #e0e7ff; color: #3730a3; }

    .search-box {
        padding: 0.5rem 0.75rem; border: 1px solid #d1d5db;
        border-radius: 8px; font-size: 0.75rem; width: 250px;
    }
    .filter-bar {
        background: #f9fafb; border-radius: 12px; padding: 1rem;
        margin-bottom: 1.5rem; border: 1px solid #e5e7eb;
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 1rem;
    }
    .bulk-actions { display: flex; gap: 0.5rem; align-items: center; }
    .btn-bulk {
        background: #3b82f6; color: white; padding: 0.4rem 1rem;
        border-radius: 6px; font-size: 0.7rem; border: none; cursor: pointer;
    }
    .btn-bulk:hover { background: #2563eb; }
    .btn-bulk-danger { background: #ef4444; }
    .btn-bulk-danger:hover { background: #dc2626; }
    .btn-bulk-success { background: #10b981; }
    .btn-bulk-success:hover { background: #059669; }

    .saved-badge { color: #10b981; font-size: 0.65rem; margin-left: 0.5rem; }
    .table-wrapper { overflow-x: auto; }
</style>

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-tag mr-2"></i> Price & Sellable Management
                </h2>
                <p class="text-orange-100 mt-1">Manage selling prices and mark items as sellable to customers</p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card stat-menu">
            <h3><i class="fas fa-utensils mr-1"></i> Menu Items</h3>
            <div class="value">{{ $menuItems->count() }}</div>
            <p class="text-xs text-gray-500 mt-1">Prepared dishes (require cooking)</p>
        </div>
        <div class="stat-card stat-sellable">
            <h3><i class="fas fa-box-open mr-1"></i> Sellable Items</h3>
            <div class="value">{{ $readyToSellItems->count() }}</div>
            <p class="text-xs text-gray-500 mt-1">Ready to sell (water, soda, beer, snacks)</p>
        </div>
        <div class="stat-card stat-nonsellable">
            <h3><i class="fas fa-box mr-1"></i> Non-Sellable Items</h3>
            <div class="value">{{ $nonSellableItems->count() }}</div>
            <p class="text-xs text-gray-500 mt-1">Can be marked as sellable</p>
        </div>
    </div>

    {{-- TABS --}}
    <div class="tabs-container">
        <div class="tabs">
            <button class="tab-btn active" data-tab="tab-menu">
                <i class="fas fa-utensils mr-2"></i> Menu Items
            </button>
            <button class="tab-btn" data-tab="tab-sellable">
                <i class="fas fa-box-open mr-2"></i> Sellable Items
            </button>
            <button class="tab-btn" data-tab="tab-nonsellable">
                <i class="fas fa-box mr-2"></i> Make Sellable
            </button>
        </div>

        {{-- ===================== TAB 1: MENU ITEMS ===================== --}}
        <div id="tab-menu" class="tab-content active">
            <div class="filter-bar">
                <input type="text" id="menuSearch" class="search-box" placeholder="  Search menu items..." >
            </div>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="table-wrapper">
                    <table class="data-table" id="menuTable">
                        <thead>
                            <tr>
                                <th style="width:5%">#</th>
                                <th style="width:35%">Item Name</th>
                                <th style="width:15%">Category</th>
                                <th style="width:18%" class="text-right">Current Price (UGX)</th>
                                <th style="width:18%" class="text-right">New Price (UGX)</th>
                                <th style="width:9%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menuItems as $index => $item)
                            <tr data-id="{{ $item->id }}" data-type="menu">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="font-medium text-gray-800">
                                    {{ $item->name }}
                                    @if($item->description)
                                        <div class="text-xs text-gray-500">{{ Str::limit($item->description, 50) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-category badge-{{ strtolower($item->category) }}">
                                        {{ $item->category }}
                                    </span>
                                </td>
                                <td class="text-right price-current">
                                    <span class="price-display">UGX {{ number_format($item->selling_price, 2) }}</span>
                                </td>
                                <td class="text-right">
                                    <input type="number" class="price-input new-price" step="0.01"
                                           placeholder="New price" value="{{ $item->selling_price }}">
                                </td>
                                <td class="text-center">
                                    <button class="btn-save save-price"
                                            data-id="{{ $item->id }}" data-type="menu">
                                        <i class="fas fa-save mr-1"></i> Save
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-500 py-8">No menu items found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ===================== TAB 2: SELLABLE ITEMS ===================== --}}
        <div id="tab-sellable" class="tab-content">
            <div class="filter-bar">
                <input type="text" id="sellableSearch" class="search-box" placeholder="Search sellable items...">
                <div class="bulk-actions">
                    <button id="bulkRemoveSellableBtn" class="btn-bulk btn-bulk-danger">
                        <i class="fas fa-ban mr-1"></i> Bulk Remove from Sellable
                    </button>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="table-wrapper">
                    <table class="data-table" id="sellableTable">
                        <thead>
                            <tr>
                                <th style="width:4%">
                                    <input type="checkbox" id="selectAllSellable">
                                </th>
                                <th style="width:4%">#</th>
                                <th style="width:24%">Item Name</th>
                                <th style="width:9%">Unit</th>
                                <th style="width:14%" class="text-right">Unit Cost</th>
                                <th style="width:15%" class="text-right">Current Price</th>
                                <th style="width:15%" class="text-right">New Price</th>
                                <th style="width:8%" class="text-center">Save</th>
                                <th style="width:7%" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($readyToSellItems as $index => $item)
                            <tr data-id="{{ $item->id }}">
                                <td class="text-center">
                                    <input type="checkbox" class="item-checkbox" data-id="{{ $item->id }}">
                                </td>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="font-medium text-gray-800">
                                    {{ $item->name }}
                                    @if($item->item_code)
                                        <div class="text-xs text-gray-500">Code: {{ $item->item_code }}</div>
                                    @endif
                                </td>
                                <td>{{ $item->base_unit ?? 'pcs' }}</td>
                                <td class="text-right">UGX {{ number_format($item->unit_cost ?? 0, 2) }}</td>
                                <td class="text-right price-current">
                                    <span class="price-display">UGX {{ number_format($item->selling_price ?? 0, 2) }}</span>
                                </td>
                                <td class="text-right">
                                    <input type="number" class="price-input new-price" step="0.01"
                                           placeholder="New price" value="{{ $item->selling_price ?? 0 }}">
                                </td>
                                <td class="text-center">
                                    <button class="btn-save save-price"
                                            data-id="{{ $item->id }}" data-type="inventory">
                                        <i class="fas fa-save mr-1"></i> Save
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button class="btn-toggle btn-toggle-on toggle-sellable"
                                            data-id="{{ $item->id }}"
                                            title="Click to remove from sellable">
                                        <i class="fas fa-check-circle mr-1"></i> Sellable
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-gray-500 py-8">No sellable items found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ===================== TAB 3: NON-SELLABLE ===================== --}}
        <div id="tab-nonsellable" class="tab-content">
            <div class="filter-bar">
                <input type="text" id="nonsellableSearch" class="search-box" placeholder=" Search items...">
                <div class="bulk-actions">
                    <button id="bulkMakeSellableBtn" class="btn-bulk btn-bulk-success">
                        <i class="fas fa-check-circle mr-1"></i> Bulk Make Sellable
                    </button>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="table-wrapper">
                    <table class="data-table" id="nonsellableTable">
                        <thead>
                            <tr>
                                <th style="width:5%">
                                    <input type="checkbox" id="selectAllNonSellable">
                                </th>
                                <th style="width:5%">#</th>
                                <th style="width:30%">Item Name</th>
                                <th style="width:10%">Unit</th>
                                <th style="width:18%" class="text-right">Unit Cost</th>
                                <th style="width:18%" class="text-right">Selling Price</th>
                                <th style="width:14%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nonSellableItems as $index => $item)
                            <tr data-id="{{ $item->id }}">
                                <td class="text-center">
                                    <input type="checkbox" class="item-checkbox-non" data-id="{{ $item->id }}">
                                </td>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="font-medium text-gray-800">
                                    {{ $item->name }}
                                    @if($item->item_code)
                                        <div class="text-xs text-gray-500">Code: {{ $item->item_code }}</div>
                                    @endif
                                </td>
                                <td>{{ $item->base_unit ?? 'pcs' }}</td>
                                <td class="text-right">UGX {{ number_format($item->unit_cost ?? 0, 2) }}</td>
                                <td class="text-right">
                                    <input type="number" class="price-input sellable-price" step="0.01"
                                           placeholder="Set selling price" value="{{ $item->selling_price ?? 0 }}">
                                </td>
                                <td class="text-center">
                                    <button class="btn-toggle btn-toggle-off make-sellable"
                                            data-id="{{ $item->id }}">
                                        <i class="fas fa-plus-circle mr-1"></i> Make Sellable
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-gray-500 py-8">No non-sellable items found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ══════════════════════════════════════════
    // TAB SWITCHING
    // ══════════════════════════════════════════
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.getAttribute('data-tab');
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // ══════════════════════════════════════════
    // LIVE SEARCH (instant, no Enter needed)
    // ══════════════════════════════════════════
    function initSearch(inputId, tableId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        ['input', 'keyup', 'search'].forEach(evt => {
            input.addEventListener(evt, function () {
                const term = this.value.toLowerCase().trim();
                document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
                    // skip empty-state rows
                    if (row.querySelector('td[colspan]')) return;
                    row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
                });
            });
        });
    }
    initSearch('menuSearch',        'menuTable');
    initSearch('sellableSearch',    'sellableTable');
    initSearch('nonsellableSearch', 'nonsellableTable');

    // ══════════════════════════════════════════
    // SAVE PRICE (menu & inventory)
    // ══════════════════════════════════════════
    document.querySelectorAll('.save-price').forEach(btn => {
        btn.addEventListener('click', async function () {
            const id   = this.dataset.id;
            const type = this.dataset.type;
            const row  = this.closest('tr');
            const newPriceInput = row.querySelector('.new-price');
            const newPrice      = newPriceInput.value;
            const priceDisplay  = row.querySelector('.price-current .price-display');

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            const url = type === 'menu'
                ? `/management/prices/menu/${id}`
                : `/management/prices/inventory/${id}`;

            try {
                const res  = await fetch(url, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ selling_price: newPrice })
                });
                const data = await res.json();

                if (data.success) {
                    priceDisplay.textContent = `UGX ${data.new_price}`;
                    const badge = document.createElement('span');
                    badge.className = 'saved-badge';
                    badge.innerHTML = ' <i class="fas fa-check-circle"></i> Saved';
                    row.querySelector('.price-current').appendChild(badge);
                    setTimeout(() => badge.remove(), 2500);
                } else {
                    alert('Failed: ' + data.message);
                }
            } catch {
                alert('Network error. Please try again.');
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-save mr-1"></i> Save';
            }
        });
    });

    // ══════════════════════════════════════════
    // TOGGLE SELLABLE (single row in Sellable tab)
    // ══════════════════════════════════════════
    document.querySelectorAll('.toggle-sellable').forEach(btn => {
        btn.addEventListener('click', async function () {
            const id = this.dataset.id;

            if (!confirm('Remove this item from the sellable list?')) return;

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const res  = await fetch(`/management/prices/toggle-sellable/${id}`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();

                if (data.success) {
                    // Remove row from sellable table immediately (no full reload)
                    this.closest('tr').remove();
                    // Update stats counter
                    const statEl = document.querySelector('.stat-sellable .value');
                    if (statEl) statEl.textContent = parseInt(statEl.textContent) - 1;
                    alert(data.message);
                } else {
                    alert('Failed: ' + data.message);
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Sellable';
                }
            } catch {
                alert('Network error.');
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Sellable';
            }
        });
    });

    // ══════════════════════════════════════════
    // MAKE SELLABLE (single row in Non-Sellable tab)
    // ══════════════════════════════════════════
    document.querySelectorAll('.make-sellable').forEach(btn => {
        btn.addEventListener('click', async function () {
            const id    = this.dataset.id;
            const row   = this.closest('tr');
            const price = row.querySelector('.sellable-price').value;

            if (!price || parseFloat(price) <= 0) {
                alert('Please set a selling price greater than 0 first.');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                // Step 1: save price
                const priceRes = await fetch(`/management/prices/inventory/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ selling_price: price })
                });
                if (!priceRes.ok) throw new Error('Price save failed');

                // Step 2: toggle sellable
                const toggleRes  = await fetch(`/management/prices/toggle-sellable/${id}`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await toggleRes.json();

                if (data.success) {
                    row.remove();
                    const statEl = document.querySelector('.stat-nonsellable .value');
                    if (statEl) statEl.textContent = parseInt(statEl.textContent) - 1;
                    const sellableEl = document.querySelector('.stat-sellable .value');
                    if (sellableEl) sellableEl.textContent = parseInt(sellableEl.textContent) + 1;
                    alert(data.message);
                } else {
                    alert('Failed: ' + data.message);
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-plus-circle mr-1"></i> Make Sellable';
                }
            } catch {
                alert('Network error.');
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-plus-circle mr-1"></i> Make Sellable';
            }
        });
    });

    // ══════════════════════════════════════════
    // SELECT ALL CHECKBOXES
    // ══════════════════════════════════════════
    const selectAllSellable = document.getElementById('selectAllSellable');
    if (selectAllSellable) {
        selectAllSellable.addEventListener('change', function () {
            document.querySelectorAll('#sellableTable .item-checkbox')
                .forEach(cb => cb.checked = this.checked);
        });
    }

    const selectAllNonSellable = document.getElementById('selectAllNonSellable');
    if (selectAllNonSellable) {
        selectAllNonSellable.addEventListener('change', function () {
            document.querySelectorAll('#nonsellableTable .item-checkbox-non')
                .forEach(cb => cb.checked = this.checked);
        });
    }

    // ══════════════════════════════════════════
    // BULK REMOVE FROM SELLABLE
    // ══════════════════════════════════════════
    const bulkRemoveBtn = document.getElementById('bulkRemoveSellableBtn');
    if (bulkRemoveBtn) {
        bulkRemoveBtn.addEventListener('click', async function () {
            const selected = document.querySelectorAll('#sellableTable .item-checkbox:checked');
            if (!selected.length) { alert('Please select at least one item.'); return; }
            if (!confirm(`Remove ${selected.length} item(s) from sellable list?`)) return;

            const items = Array.from(selected).map(cb => ({ id: cb.dataset.id }));
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processing...';

            try {
                const res  = await fetch('/management/prices/bulk-remove-sellable', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ items })
                });
                const data = await res.json();

                if (data.success) {
                    // Remove checked rows without reloading
                    selected.forEach(cb => cb.closest('tr').remove());
                    alert(data.message);
                } else {
                    alert('Failed: ' + data.message);
                }
            } catch {
                alert('Network error.');
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-ban mr-1"></i> Bulk Remove from Sellable';
                document.getElementById('selectAllSellable').checked = false;
            }
        });
    }

    // ══════════════════════════════════════════
    // BULK MAKE SELLABLE
    // ══════════════════════════════════════════
    const bulkMakeBtn = document.getElementById('bulkMakeSellableBtn');
    if (bulkMakeBtn) {
        bulkMakeBtn.addEventListener('click', async function () {
            const selected = document.querySelectorAll('#nonsellableTable .item-checkbox-non:checked');
            if (!selected.length) { alert('Please select at least one item.'); return; }
            if (!confirm(`Make ${selected.length} item(s) sellable?`)) return;

            const items = Array.from(selected).map(cb => ({ id: cb.dataset.id }));
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Processing...';

            try {
                const res  = await fetch('/management/prices/bulk-make-sellable', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ items })
                });
                const data = await res.json();

                if (data.success) {
                    selected.forEach(cb => cb.closest('tr').remove());
                    alert(data.message);
                } else {
                    alert('Failed: ' + data.message);
                }
            } catch {
                alert('Network error.');
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Bulk Make Sellable';
                document.getElementById('selectAllNonSellable').checked = false;
            }
        });
    }

});
</script>
@endsection
