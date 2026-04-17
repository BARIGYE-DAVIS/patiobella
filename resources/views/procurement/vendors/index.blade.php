@extends('layouts.procurement')

@section('title', 'Vendors')

@section('page-title', 'Vendor Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <p class="text-gray-600">Manage all your suppliers and vendors</p>
    </div>
    <a href="{{ route('procurement.vendors.create') }}" 
       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add New Vendor
    </a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-lg shadow-sm mb-6 p-4">
    <form method="GET" action="{{ route('procurement.vendors.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search by name, code or contact..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="blacklisted" {{ request('status') == 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
            </select>
        </div>
        
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Filter
        </button>
        <a href="{{ route('procurement.vendors.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
            Reset
        </a>
    </form>
</div>

{{-- Vendors Table --}}
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr class="border-b border-gray-200">
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact Person</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($vendors as $vendor)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <span class="font-mono text-sm text-gray-600">{{ $vendor->vendor_code }}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-800">{{ $vendor->name }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $vendor->contact_person ?? '—' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $vendor->phone ?? '—' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $vendor->email ?? '—' }}
                </td>
                <td class="px-6 py-4 text-center">
                    @php
                        $statusColors = [
                            'active' => 'bg-green-100 text-green-800',
                            'inactive' => 'bg-gray-100 text-gray-800',
                            'blacklisted' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$vendor->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($vendor->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('procurement.vendors.show', $vendor->id) }}" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                    <a href="{{ route('procurement.vendors.edit', $vendor->id) }}" class="text-amber-600 hover:text-amber-800">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    No vendors found.
                    <a href="{{ route('procurement.vendors.create') }}" class="text-blue-600 hover:underline ml-2">Add your first vendor</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $vendors->appends(request()->query())->links() }}
</div>
@endsection