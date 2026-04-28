@extends('layouts.management')
@section('title', 'Management Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="space-y-6">
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6 stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Purchase Orders</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_pos']) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Sent POs</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['sent_pos']) }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Received POs</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['received_pos']) }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6 stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Vendors</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_vendors']) }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Financial Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between pb-2 border-b">
                    <span class="text-gray-600">Total PO Value:</span>
                    <span class="font-bold text-gray-800">UGX {{ number_format($stats['total_po_value'], 2) }}</span>
                </div>
                <div class="flex justify-between pb-2 border-b">
                    <span class="text-gray-600">Total GRN Value:</span>
                    <span class="font-bold text-green-600">UGX {{ number_format($stats['total_grn_value'], 2) }}</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-gray-600">Variance:</span>
                    <span class="font-bold {{ $stats['total_po_value'] - $stats['total_grn_value'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                        UGX {{ number_format($stats['total_po_value'] - $stats['total_grn_value'], 2) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Quick Stats</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_grns']) }}</p>
                    <p class="text-sm text-gray-500">Goods Received Notes</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['received_pos']) }}</p>
                    <p class="text-sm text-gray-500">Completed POs</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Purchase Orders --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-6 py-4">
            <h3 class="text-lg font-semibold text-white">Recent Purchase Orders</h3>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">PO Number</th>
                        <th class="p-3 text-left">Vendor</th>
                        <th class="p-3 text-center">Date</th>
                        <th class="p-3 text-right">Amount</th>
                        <th class="p-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPos as $po)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-semibold">{{ $po->po_number }}</td>
                        <td class="p-3">{{ $po->vendor->name ?? 'N/A' }}</td>
                        <td class="p-3 text-center">{{ $po->created_at->format('Y-m-d') }}</td>
                        <td class="p-3 text-right">UGX {{ number_format($po->total_amount, 2) }}</td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                {{ ucfirst($po->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection