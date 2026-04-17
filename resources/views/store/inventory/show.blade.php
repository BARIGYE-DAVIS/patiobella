@extends('layouts.store')

@section('title', 'Inventory Item Details')

@section('page-title', 'Inventory Item Details')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">{{ $item->name }}</h3>
            <p class="text-sm text-gray-500">Item Code: {{ $item->item_code ?? $item->code ?? 'N/A' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('store.inventory.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            <a href="{{ route('store.inventory.edit', $item->id) }}" class="text-amber-600 hover:text-amber-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
        </div>
    </div>

    <div class="p-6">
        {{-- Status Badge --}}
        <div class="mb-6">
            @if($item->is_active)
                <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">Active</span>
            @else
                <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-800">Inactive</span>
            @endif
            
            @if($item->is_perishable)
                <span class="ml-2 px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">Perishable</span>
            @endif
            
            @if($item->is_taxable)
                <span class="ml-2 px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">Taxable</span>
            @endif
        </div>

        {{-- Two Column Layout --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Left Column --}}
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-3 border-b pb-2">Basic Information</h4>
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Item Name:</span>
                        <span class="text-sm text-gray-800 font-medium">{{ $item->name }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Item Code:</span>
                        <span class="text-sm font-mono text-gray-800">{{ $item->item_code ?? $item->code ?? 'N/A' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Barcode:</span>
                        <span class="text-sm text-gray-800">{{ $item->barcode ?? '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Category:</span>
                        <span class="text-sm text-gray-800">{{ $item->category ? $item->category->name : '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Sub Category:</span>
                        <span class="text-sm text-gray-800">{{ $item->subCategory ? $item->subCategory->name : '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Unit of Measure:</span>
                        <span class="text-sm text-gray-800">{{ $item->unitOfMeasure ? $item->unitOfMeasure->name : '—' }}</span>
                    </div>
                </div>

                <h4 class="text-sm font-medium text-gray-500 mb-3 mt-6 border-b pb-2">Stock Information</h4>
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Current Stock:</span>
                        <span class="text-sm font-semibold {{ ($item->current_stock ?? 0) <= ($item->minimum_stock ?? 0) ? 'text-red-600' : 'text-gray-800' }}">
                            {{ number_format($item->current_stock ?? 0, 2) }}
                        </span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Minimum Stock:</span>
                        <span class="text-sm text-gray-800">{{ number_format($item->minimum_stock ?? 0, 2) }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Maximum Stock:</span>
                        <span class="text-sm text-gray-800">{{ number_format($item->maximum_stock ?? 0, 2) }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Reorder Quantity:</span>
                        <span class="text-sm text-gray-800">{{ number_format($item->reorder_quantity ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-3 border-b pb-2">Pricing Information</h4>
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Unit Cost:</span>
                        <span class="text-sm text-gray-800">{{ number_format($item->unit_cost ?? 0, 2) }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Last Purchase Price:</span>
                        <span class="text-sm text-gray-800">{{ number_format($item->last_purchase_price ?? 0, 2) }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Selling Price:</span>
                        <span class="text-sm text-gray-800">{{ number_format($item->selling_price ?? 0, 2) }}</span>
                    </div>
                </div>

                <h4 class="text-sm font-medium text-gray-500 mb-3 mt-6 border-b pb-2">Additional Information</h4>
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Manufacturer:</span>
                        <span class="text-sm text-gray-800">{{ $item->manufacturer ?? '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Brand:</span>
                        <span class="text-sm text-gray-800">{{ $item->brand ?? '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Shelf Life (days):</span>
                        <span class="text-sm text-gray-800">{{ $item->shelf_life_days ?? '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Storage Conditions:</span>
                        <span class="text-sm text-gray-800">{{ $item->storage_conditions ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Description --}}
        @if($item->description)
        <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Description</h4>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $item->description }}</p>
            </div>
        </div>
        @endif

        {{-- Notes --}}
        @if($item->notes)
        <div class="mt-4">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Notes</h4>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $item->notes }}</p>
            </div>
        </div>
        @endif

        {{-- Audit Information --}}
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="grid grid-cols-2 gap-4 text-xs text-gray-500">
                <div>
                    <span class="font-medium">Created By:</span> 
                    {{ $item->creator ? $item->creator->first_name . ' ' . $item->creator->last_name : 'System' }}
                    <br>
                    <span class="font-medium">Created At:</span> 
                    {{ $item->created_at ? $item->created_at->format('M d, Y H:i') : 'N/A' }}
                </div>
                <div>
                    <span class="font-medium">Last Updated By:</span> 
                    {{ $item->updater ? $item->updater->first_name . ' ' . $item->updater->last_name : 'Never' }}
                    <br>
                    <span class="font-medium">Updated At:</span> 
                    {{ $item->updated_at ? $item->updated_at->format('M d, Y H:i') : 'N/A' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection