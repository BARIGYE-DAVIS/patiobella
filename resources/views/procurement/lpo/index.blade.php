@extends('layouts.procurement')

@section('title', 'Local Purchase Orders')

@section('page-title', 'Local Purchase Orders (LPOs)')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Local Purchase Orders</h3>
            <p class="text-sm text-gray-500">Manage LPOs waiting for Director approval</p>
        </div>
    </div>

    <div class="p-6 overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg">
            <thead class="bg-gray-50">
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">LPO Number</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requisition #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">LPO Date</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($lpos as $lpo)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-mono font-semibold text-gray-800">
                        {{ $lpo->lpo_number }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $lpo->requisition->requisition_number ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800">
                        {{ $lpo->vendor->name ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 text-center">
                        {{ $lpo->lpo_date->format('Y-m-d') }}
                    </td>
                    <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">
                        UGX {{ number_format($lpo->total_amount, 2) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $statusColors = [
                                'pending_director' => 'bg-yellow-100 text-yellow-800',
                                'director_approved' => 'bg-green-100 text-green-800',
                                'director_rejected' => 'bg-red-100 text-red-800',
                                'issued' => 'bg-blue-100 text-blue-800',
                            ];
                            $statusText = [
                                'pending_director' => 'Pending Director',
                                'director_approved' => 'Director Approved',
                                'director_rejected' => 'Director Rejected',
                                'issued' => 'Issued to Vendor',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$lpo->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusText[$lpo->status] ?? ucfirst($lpo->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('procurement.lpo.show', $lpo->id) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            View Details
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        No Local Purchase Orders found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $lpos->links() }}
        </div>
    </div>
</div>
@endsection