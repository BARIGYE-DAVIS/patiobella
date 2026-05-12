@extends('layouts.kitchen')

@section('title', 'My Requisitions')

@section('page-title', 'My Requisitions')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Kitchen Requisitions</h3>
            <p class="text-sm text-gray-500">Track all your requests to the store</p>
        </div>
        <a href="{{ route('kitchen.requisitions.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Requisition
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="p-4 border-b border-gray-200 bg-gray-50">
        <form method="GET" action="{{ route('kitchen.requisitions.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" class="form-select border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                    <option value="partially_issued" {{ request('status') == 'partially_issued' ? 'selected' : '' }}>Partially Issued</option>
                    <option value="partially_returned" {{ request('status') == 'partially_returned' ? 'selected' : '' }}>Partially Returned</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                <a href="{{ route('kitchen.requisitions.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Reset</a>
            </div>
        </form>
    </div>

    <div class="p-6 overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg">
            <thead class="bg-gray-50">
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requisition #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Needed</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Items</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Qty</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($requisitions as $req)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-mono font-semibold text-gray-800">{{ $req->requisition_number }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $req->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $req->date_needed ? date('Y-m-d', strtotime($req->date_needed)) : 'Not set' }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ $req->items->count() }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ number_format($req->items->sum('quantity_requested'), 2) }}</td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-blue-100 text-blue-800',
                                'issued' => 'bg-green-100 text-green-800',
                                'partially_issued' => 'bg-orange-100 text-orange-800',
                                'partially_returned' => 'bg-purple-100 text-purple-800',
                                'returned' => 'bg-gray-100 text-gray-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'cancelled' => 'bg-gray-100 text-gray-500',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$req->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('kitchen.requisitions.show', $req->id) }}"
                           class="text-blue-600 hover:text-blue-800">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        No requisitions found. Click "New Requisition" to create one.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $requisitions->links() }}
        </div>
    </div>
</div>
@endsection
