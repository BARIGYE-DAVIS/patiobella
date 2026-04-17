@extends('layouts.procurement')

@section('title', 'Procurement Dashboard')

@section('page-title', 'Procurement Dashboard')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Procurement Dashboard</h1>
        <p class="text-gray-500">Welcome to the Procurement Management Module</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Pending Requisitions</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $pendingRequisitions ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Pending POs</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $pendingPurchaseOrders ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Active Vendors</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $activeVendors ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Pending Approvals</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $pendingApprovals ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Requisitions --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Recent Requisitions</h3>
            <a href="{{ route('procurement.requisitions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b border-gray-200">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requisition No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Store</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Needed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested By</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentRequisitions ?? [] as $requisition)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm font-semibold text-blue-600">
                                {{ $requisition->requisition_number }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $requisition->store ? $requisition->store->name : '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $requisition->date_needed ? $requisition->date_needed->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'fulfilled' => 'bg-blue-100 text-blue-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$requisition->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($requisition->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $requisition->requestedBy ? $requisition->requestedBy->first_name . ' ' . $requisition->requestedBy->last_name : '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('procurement.requisitions.show', $requisition->id) }}" 
                               class="text-blue-600 hover:text-blue-800">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No requisitions found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection