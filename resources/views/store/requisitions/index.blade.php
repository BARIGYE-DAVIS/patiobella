@extends('layouts.store')

@section('title', 'Requisitions')

@section('page-title', 'Requisitions')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <p class="text-gray-600">Manage your store requisitions</p>
    </div>
    <a href="{{ route('store.requisitions.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Requisition
    </a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-lg shadow-sm mb-6 p-4">
    <form method="GET" action="{{ route('store.requisitions.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by requisition number..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Filter
        </button>
        <a href="{{ route('store.requisitions.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
            Reset
        </a>
    </form>
</div>

{{-- Requisitions Table --}}
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr class="border-b border-gray-200">
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requisition No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Needed</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested By</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($requisitions as $requisition)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <span class="font-mono text-sm font-semibold text-blue-600">
                        {{ $requisition->requisition_number }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $requisition->created_at->format('d/m/Y') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $requisition->date_needed ? $requisition->date_needed->format('d/m/Y') : '—' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $requisition->items->count() }} items
                </td>
                <td class="px-6 py-4">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            'fulfilled' => 'bg-blue-100 text-blue-800',
                        ];
                        $statusText = [
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            'fulfilled' => 'Fulfilled',
                        ];
                    @endphp
                    <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$requisition->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $statusText[$requisition->status] ?? ucfirst($requisition->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $requisition->requestedBy ? $requisition->requestedBy->first_name . ' ' . $requisition->requestedBy->last_name : '—' }}
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('store.requisitions.show', $requisition->id) }}"
                       class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    No requisitions found.
                    <a href="{{ route('store.requisitions.create') }}" class="text-blue-600 hover:underline ml-2">Create your first requisition</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $requisitions->appends(request()->query())->links() }}
</div>
@endsection
