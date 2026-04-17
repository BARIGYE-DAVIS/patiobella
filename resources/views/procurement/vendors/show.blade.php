@extends('layouts.procurement')

@section('title', 'Vendor Details')

@section('page-title', 'Vendor Details')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">{{ $vendor->name }}</h3>
            <p class="text-sm text-gray-500">Vendor Code: {{ $vendor->vendor_code }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('procurement.vendors.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            <a href="{{ route('procurement.vendors.edit', $vendor->id) }}" class="text-amber-600 hover:text-amber-800">
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
            @php
                $statusColors = [
                    'active' => 'bg-green-100 text-green-800',
                    'inactive' => 'bg-gray-100 text-gray-800',
                    'blacklisted' => 'bg-red-100 text-red-800',
                ];
            @endphp
            <span class="px-3 py-1 text-sm rounded-full {{ $statusColors[$vendor->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($vendor->status) }}
            </span>
        </div>

        {{-- Two Column Layout --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Left Column --}}
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-3 border-b pb-2">Contact Information</h4>
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Contact Person:</span>
                        <span class="text-sm text-gray-800">{{ $vendor->contact_person ?? '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Phone:</span>
                        <span class="text-sm text-gray-800">{{ $vendor->phone ?? '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Alternative Phone:</span>
                        <span class="text-sm text-gray-800">{{ $vendor->alternative_phone ?? '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Email:</span>
                        <span class="text-sm text-gray-800">{{ $vendor->email ?? '—' }}</span>
                    </div>
                </div>

                <h4 class="text-sm font-medium text-gray-500 mb-3 mt-6 border-b pb-2">Address Information</h4>
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Address:</span>
                        <span class="text-sm text-gray-800">{{ $vendor->address ?? '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">City:</span>
                        <span class="text-sm text-gray-800">{{ $vendor->city ?? '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Country:</span>
                        <span class="text-sm text-gray-800">{{ $vendor->country ?? 'Uganda' }}</span>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-3 border-b pb-2">Financial Information</h4>
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Tax ID (TIN):</span>
                        <span class="text-sm text-gray-800">{{ $vendor->tax_id ?? '—' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Payment Method:</span>
                        <span class="text-sm text-gray-800">{{ ucfirst($vendor->payment_method ?? 'cash') }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Credit Limit:</span>
                        <span class="text-sm text-gray-800">{{ $vendor->credit_limit ? number_format($vendor->credit_limit, 2) : '—' }}</span>
                    </div>
                </div>

                <h4 class="text-sm font-medium text-gray-500 mb-3 mt-6 border-b pb-2">Additional Information</h4>
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-32 text-sm text-gray-500">Notes:</span>
                        <span class="text-sm text-gray-800">{{ $vendor->notes ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Audit Information --}}
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="grid grid-cols-2 gap-4 text-xs text-gray-500">
                <div>
                    <span class="font-medium">Created By:</span> 
                    {{ $vendor->creator ? $vendor->creator->first_name . ' ' . $vendor->creator->last_name : 'System' }}
                    <br>
                    <span class="font-medium">Created At:</span> 
                    {{ $vendor->created_at ? $vendor->created_at->format('M d, Y H:i') : 'N/A' }}
                </div>
                <div>
                    <span class="font-medium">Last Updated By:</span> 
                    {{ $vendor->updater ? $vendor->updater->first_name . ' ' . $vendor->updater->last_name : 'Never' }}
                    <br>
                    <span class="font-medium">Updated At:</span> 
                    {{ $vendor->updated_at ? $vendor->updated_at->format('M d, Y H:i') : 'N/A' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection