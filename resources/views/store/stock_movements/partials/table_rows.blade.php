@forelse($movements as $item)
@php
    $movement = $item->movement;
    $stockBefore = $item->stock_before;
    $stockAfter = $item->stock_after;
    $isIn = $movement->movementType && $movement->movementType->sign == '+';
    $receivingUnit = $movement->inventoryItem->default_unit_of_measure_id ?? 'units';
    $baseUnit = $movement->base_unit ?? $movement->inventoryItem->base_unit ?? 'units';

    // Format breakdown display
    $breakdownMain = '';
    $breakdownSub = '';

    if ($movement->pack_type) {
        // Bulk item (carton, box, crate, etc.)
        $breakdownMain = number_format($movement->number_of_packs) . ' ' . ucfirst($movement->pack_type);
        if ($movement->pack_size) {
            $breakdownSub = '× ' . number_format($movement->pack_size) . ' ' . $baseUnit . '/' . $movement->pack_type;
        } else {
            $breakdownSub = '<span class="text-red-400">(pack size not recorded)</span>';
        }
    } else {
        // Direct entry (kg, litres, pcs)
        $breakdownMain = number_format($movement->quantity, 2) . ' ' . $receivingUnit;
        $breakdownSub = '';
    }
@endphp
<tr class="border-b border-gray-100 hover:bg-gray-50">
    <td class="px-2 py-2">
        <span class="font-mono text-xs font-semibold text-gray-600">{{ $movement->movement_number }}</span>
    </td>
    <td class="px-2 py-2">
        <div class="font-medium text-gray-800 text-xs">{{ Str::limit($movement->inventoryItem->name ?? 'N/A', 25) }}</div>
        <div class="text-xs text-gray-400">{{ $movement->inventoryItem->item_code ?? '' }}</div>
    </td>
    <td class="px-2 py-2">
        @if($isIn)
            <span class="badge-sm badge-in">📥 Stock In</span>
        @else
            <span class="badge-sm badge-out">📤 Stock Out</span>
        @endif
    </td>
    <td class="px-2 py-2 text-center">
        @if($movement->pack_type)
            <span class="qty-pack">{{ number_format($movement->number_of_packs) }}</span>
        @else
            <span class="qty-direct">{{ number_format($movement->quantity, 2) }}</span>
        @endif
    </td>
    <td class="px-2 py-2">
        <div class="text-xs font-medium text-amber-700">{{ $breakdownMain }}</div>
        @if($breakdownSub)
            <div class="text-xs text-gray-400">{!! $breakdownSub !!}</div>
        @endif
    </td>
    <td class="px-2 py-2 text-center">
        <span class="stock-before">{{ number_format($stockBefore, 2) }}</span>
        <div class="text-xs text-gray-400">{{ $baseUnit }}</div>
    </td>
    <td class="px-2 py-2 text-center">
        <span class="stock-after">{{ number_format($stockAfter, 2) }}</span>
        <div class="text-xs text-gray-400">{{ $baseUnit }}</div>
    </td>
    <td class="px-2 py-2 text-right">
        <span class="text-xs">UGX {{ number_format($movement->unit_cost ?? 0, 2) }}</span>
        <div class="text-xs text-gray-400">/{{ $baseUnit }}</div>
    </td>
    <td class="px-2 py-2 text-right">
        <span class="text-xs font-semibold text-emerald-600">UGX {{ number_format($movement->total_value ?? 0, 2) }}</span>
    </td>
    <td class="px-2 py-2 text-gray-500 text-xs whitespace-nowrap">
        {{ $movement->movement_date->format('d M Y') }}
    </td>
    <td class="px-2 py-2 text-center">
        <a href="{{ route('store.stock-movements.show', $movement->id) }}" class="view-link">View</a>
    </td>
</tr>
@empty
<tr>
    <td colspan="11" class="text-center py-8 text-gray-400 text-xs">
        No stock movements found
    </tr>
</tr>
@endforelse
