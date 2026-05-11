@extends('layouts.store')

@section('title', $item->name)

@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm text-gray-400 mb-4">
    <a href="{{ route('store.inventory.index') }}" class="hover:text-blue-700 transition">Inventory</a>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-gray-600 font-medium">{{ $item->name }}</span>
</nav>

{{-- Page Header --}}
<div class="flex flex-wrap items-start justify-between gap-3 mb-6">
    <div>
        <div class="flex flex-wrap items-center gap-2 mb-1">
            <h2 class="text-xl font-semibold text-gray-800">{{ $item->name }}</h2>

            {{-- Stock status badge --}}
            @php
                $badgeClass = match($item->stock_status) {
                    'out_of_stock' => 'bg-red-100 text-red-700',
                    'low_stock'    => 'bg-yellow-100 text-yellow-700',
                    default        => 'bg-green-100 text-green-700',
                };
            @endphp
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $badgeClass }}">
                {{ ucfirst(str_replace('_', ' ', $item->stock_status)) }}
            </span>

            @unless($item->is_active)
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">Inactive</span>
            @endunless
        </div>
        <p class="text-sm text-gray-400">
            Code: <code class="font-mono text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded text-xs">{{ $item->item_code }}</code>
            @if($item->barcode)
                &nbsp;·&nbsp; Barcode: <code class="font-mono text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded text-xs">{{ $item->barcode }}</code>
            @endif
        </p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('store.inventory.edit', $item->id) }}"
           class="inline-flex items-center gap-1.5 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
        <button onclick="toggleAdjForm()"
                id="adjToggleBtn"
                class="inline-flex items-center gap-1.5 bg-blue-800 hover:bg-blue-900 text-white text-sm font-medium px-3 py-2 rounded-lg transition">
            <svg class="w-4 h-4" id="adjBtnIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span id="adjBtnText">Adjust Stock</span>
        </button>
    </div>
</div>

{{-- Stock Adjustment Form (hidden by default) --}}
<div id="adjFormWrap" class="hidden mb-6 bg-blue-50 border border-blue-100 rounded-xl p-5">
    <p class="text-sm font-semibold text-blue-900 mb-4">Stock Adjustment</p>
    <form action="{{ route('store.inventory.adjust', $item->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                <select name="adjustment_type"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="add">➕ Add stock</option>
                    <option value="subtract">➖ Subtract stock</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Quantity ({{ $item->base_unit_label }}s)
                </label>
                <input type="number" name="quantity" step="0.01" min="0.01" required
                       placeholder="e.g. 24"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-300"/>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Reason</label>
                <input type="text" name="reason" required
                       placeholder="e.g. Physical count, damaged…"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-300"/>
            </div>
            <div class="flex items-end">
                <button type="submit"
                        class="w-full bg-blue-800 hover:bg-blue-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                    Save Adjustment
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ── Stock Snapshot ──────────────────────────────────────────────── --}}
@php
    $maxRef   = $item->maximum_stock > 0 ? $item->maximum_stock : ($item->minimum_stock > 0 ? $item->minimum_stock * 2 : 1);
    $pct      = $maxRef > 0 ? min(100, ($item->current_stock / $maxRef) * 100) : 0;
    $barColor = match($item->stock_status) {
        'out_of_stock' => 'bg-red-500',
        'low_stock'    => 'bg-yellow-400',
        default        => 'bg-green-500',
    };
@endphp

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">
            Current Stock ({{ $item->base_unit_label }}s)
        </p>
        <p class="text-2xl font-bold text-gray-800 tabular-nums">
            {{ number_format($item->current_stock, 2) }}
        </p>
        <div class="w-full h-1.5 bg-gray-100 rounded-full mt-2 overflow-hidden">
            <div class="{{ $barColor }} h-full rounded-full transition-all" style="width: {{ $pct }}%"></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Min Stock</p>
        <p class="text-2xl font-bold text-yellow-600 tabular-nums">{{ number_format($item->minimum_stock, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Max Stock</p>
        <p class="text-2xl font-bold text-blue-700 tabular-nums">{{ number_format($item->maximum_stock, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Reorder Qty</p>
        <p class="text-2xl font-bold text-gray-700 tabular-nums">{{ number_format($item->reorder_quantity, 2) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Left: detail panels (2/3 width) ────────────────────────── --}}
    <div class="lg:col-span-2 flex flex-col gap-5">

        {{-- Item Details --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Item Details</p>
            </div>
            <div class="divide-y divide-gray-50">
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Name</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $item->name }}</span>
                </div>
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Item Code</span>
                    <code class="text-sm font-mono text-gray-700">{{ $item->item_code }}</code>
                </div>
                @if($item->barcode)
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Barcode</span>
                    <code class="text-sm font-mono text-gray-700">{{ $item->barcode }}</code>
                </div>
                @endif
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Category</span>
                    <span class="text-sm text-gray-700">{{ $item->category->name ?? '—' }}</span>
                </div>
                @if($item->subCategory)
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Sub-category</span>
                    <span class="text-sm text-gray-700">{{ $item->subCategory->name }}</span>
                </div>
                @endif
                @if($item->description)
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Description</span>
                    <span class="text-sm text-gray-700">{{ $item->description }}</span>
                </div>
                @endif
                @if($item->manufacturer)
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Manufacturer</span>
                    <span class="text-sm text-gray-700">{{ $item->manufacturer }}</span>
                </div>
                @endif
                @if($item->brand)
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Brand</span>
                    <span class="text-sm text-gray-700">{{ $item->brand }}</span>
                </div>
                @endif
                @if($item->notes)
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Notes</span>
                    <span class="text-sm text-gray-500 italic">{{ $item->notes }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Units & Pricing --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Units &amp; Pricing</p>
            </div>
            <div class="divide-y divide-gray-50">
                <div class="flex px-5 py-3 gap-4 items-center">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Receiving Unit</span>
                    <div class="flex items-center gap-2">
                        <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-1 rounded-md">
                            {{ ucfirst($item->default_unit_of_measure_id ?? '—') }}
                        </span>
                        @if($item->is_bulk_item)
                            <span class="text-xs text-gray-400">Bulk / pack type</span>
                        @else
                            <span class="text-xs text-gray-400">Direct unit</span>
                        @endif
                    </div>
                </div>
                <div class="flex px-5 py-3 gap-4 items-center">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Base (Sell) Unit</span>
                    <span class="bg-orange-50 text-orange-700 text-xs font-medium px-2.5 py-1 rounded-md">
                        {{ ucfirst($item->base_unit_label) }}
                    </span>
                </div>
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Stock Display</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $item->stock_display }}</span>
                </div>
                @if($item->is_bulk_item)
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Receiving Display</span>
                    <span class="text-sm text-gray-500">{{ ucfirst($item->receiving_unit_display) }}</span>
                </div>
                @endif
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Unit Cost</span>
                    <span class="text-sm text-gray-800">
                        UGX {{ number_format($item->unit_cost, 2) }}
                        <span class="text-gray-400">/ {{ $item->base_unit_label }}</span>
                    </span>
                </div>
                @if($item->last_purchase_price)
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Last Purchase Price</span>
                    <span class="text-sm text-gray-700">UGX {{ number_format($item->last_purchase_price, 2) }}</span>
                </div>
                @endif
                @if($item->selling_price)
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Selling Price</span>
                    <span class="text-sm font-semibold text-green-700">
                        UGX {{ number_format($item->selling_price, 2) }}
                        <span class="font-normal text-gray-400">/ {{ $item->base_unit_label }}</span>
                    </span>
                </div>
                @endif
            </div>
        </div>

        {{-- Properties / Flags --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Properties</p>
            </div>
            <div class="divide-y divide-gray-50">
                @php
                    $yesClass = 'bg-green-100 text-green-700';
                    $noClass  = 'bg-gray-100 text-gray-500';
                @endphp
                <div class="flex px-5 py-3 gap-4 items-center">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Active</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $item->is_active ? $yesClass : $noClass }}">
                        {{ $item->is_active ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div class="flex px-5 py-3 gap-4 items-center">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Perishable</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $item->is_perishable ? $yesClass : $noClass }}">
                        {{ $item->is_perishable ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div class="flex px-5 py-3 gap-4 items-center">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Taxable</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $item->is_taxable ? $yesClass : $noClass }}">
                        {{ $item->is_taxable ? 'Yes' : 'No' }}
                    </span>
                </div>
                @if($item->shelf_life_days)
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Shelf Life</span>
                    <span class="text-sm text-gray-700">{{ $item->shelf_life_days }} days</span>
                </div>
                @endif
                @if($item->storage_conditions)
                <div class="flex px-5 py-3 gap-4">
                    <span class="text-sm text-gray-400 w-44 shrink-0">Storage Conditions</span>
                    <span class="text-sm text-gray-700">{{ $item->storage_conditions }}</span>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ── Right: metadata + stock movements (1/3 width) ─────────── --}}
    <div class="flex flex-col gap-5">

        {{-- Record info --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Record Info</p>
            </div>
            <div class="divide-y divide-gray-50">
                <div class="flex flex-col px-5 py-3 gap-0.5">
                    <span class="text-xs text-gray-400">Created by</span>
                    <span class="text-sm text-gray-700 font-medium">{{ $item->creator->name ?? '—' }}</span>
                </div>
                <div class="flex flex-col px-5 py-3 gap-0.5">
                    <span class="text-xs text-gray-400">Created at</span>
                    <span class="text-sm text-gray-700">{{ $item->created_at->format('d M Y, H:i') }}</span>
                </div>
                @if($item->updater)
                <div class="flex flex-col px-5 py-3 gap-0.5">
                    <span class="text-xs text-gray-400">Last updated by</span>
                    <span class="text-sm text-gray-700 font-medium">{{ $item->updater->name }}</span>
                </div>
                <div class="flex flex-col px-5 py-3 gap-0.5">
                    <span class="text-xs text-gray-400">Updated at</span>
                    <span class="text-sm text-gray-700">{{ $item->updated_at->format('d M Y, H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Recent Stock Movements --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock Movements</p>
                <span class="text-xs text-gray-400">Last {{ $stockMovements->count() }}</span>
            </div>

            <div class="divide-y divide-gray-50">
                @forelse($stockMovements as $mv)
                @php
                    $isIn      = in_array($mv->movement_type_id, [1, 2]);
                    $dotColor  = $isIn ? 'bg-green-500' : 'bg-red-400';
                    $qtyColor  = $isIn ? 'text-green-700' : 'text-red-600';
                    $qtyPrefix = $isIn ? '+' : '−';
                    $mvDate    = $mv->movement_date
                        ? \Carbon\Carbon::parse($mv->movement_date)->format('d M Y, H:i')
                        : $mv->created_at->format('d M Y, H:i');
                @endphp
                <div class="flex items-start gap-3 px-5 py-3">
                    <span class="mt-1.5 w-2 h-2 rounded-full {{ $dotColor }} shrink-0"></span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 leading-tight">
                            {{ $mv->movementType->name ?? ($isIn ? 'Stock In' : 'Stock Out') }}
                        </p>
                        <p class="text-xs font-mono text-gray-400 mt-0.5">{{ $mv->movement_number }}</p>

                        {{-- Pack breakdown --}}
                        @if($mv->pack_type)
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $mv->number_of_packs }} {{ $mv->pack_type }}(s)
                                × {{ $mv->pack_size }}
                                = {{ number_format($mv->quantity_in_base_unit, 2) }} {{ $mv->base_unit }}(s)
                            </p>
                        @endif

                        <p class="text-xs text-gray-400 mt-0.5">{{ $mvDate }}</p>
                    </div>
                    <span class="text-sm font-bold {{ $qtyColor }} tabular-nums whitespace-nowrap">
                        {{ $qtyPrefix }}{{ number_format($mv->quantity_in_base_unit, 2) }}
                        <span class="text-xs font-normal text-gray-400">{{ $mv->base_unit }}</span>
                    </span>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">
                    No stock movements yet.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="bg-white rounded-xl border border-red-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-red-50 border-b border-red-100">
                <p class="text-xs font-semibold text-red-500 uppercase tracking-wider">Danger Zone</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs text-gray-500 mb-3">
                    Deleting this item is permanent and cannot be undone.
                    Items with stock movements cannot be deleted.
                </p>
                <form action="{{ route('store.inventory.destroy', $item->id) }}" method="POST"
                      onsubmit="return confirm('Delete \'{{ addslashes($item->name) }}\'? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-sm font-medium px-3 py-2 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Item
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function toggleAdjForm() {
    var wrap = document.getElementById('adjFormWrap');
    var text = document.getElementById('adjBtnText');
    var icon = document.getElementById('adjBtnIcon');
    var isOpen = !wrap.classList.contains('hidden');
    if (isOpen) {
        wrap.classList.add('hidden');
        text.textContent = 'Adjust Stock';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>';
    } else {
        wrap.classList.remove('hidden');
        text.textContent = 'Cancel';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
    }
}
</script>

@endsection
