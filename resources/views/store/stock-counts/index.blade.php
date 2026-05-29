{{-- resources/views/store/stock-counts/index.blade.php --}}

@extends('layouts.store')

@section('title', 'Stock Counts')
@section('page-title', 'Stock Counts')

@section('content')
<div class="space-y-4">

    {{-- Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('store.stock-counts.index', ['type' => 'store']) }}"
               class="{{ $type === 'store' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-warehouse mr-2"></i> Store Counts
            </a>
            <a href="{{ route('store.stock-counts.index', ['type' => 'department']) }}"
               class="{{ $type === 'department' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-building mr-2"></i> Department Counts
            </a>
        </nav>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('store.stock-counts.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="hidden" name="type" value="{{ $type }}">

            @if($type === 'department')
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                <select name="department_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    <option value="">All Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="{{ route('store.stock-counts.index', ['type' => $type]) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Create Button --}}
    <div class="flex justify-end">
        <a href="{{ route('store.stock-counts.create', ['type' => $type]) }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-plus mr-1"></i> New Stock Count
        </a>
    </div>

    {{-- Stock Counts Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Count #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Location</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Count Date</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Items</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Variance</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Created By</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stockCounts as $count)
                        @php
                            $totalVariance = $count->getTotalVarianceAttribute();
                            $varianceClass = $totalVariance < 0 ? 'text-red-600' : ($totalVariance > 0 ? 'text-green-600' : 'text-gray-400');
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <span class="font-mono font-medium text-gray-800">{{ $count->count_number }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($type === 'store')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                        <i class="fas fa-warehouse text-xs"></i> Main Store
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700">
                                        <i class="fas fa-building text-xs"></i> {{ $count->location->name ?? 'N/A' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ \Carbon\Carbon::parse($count->count_date)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $count->items->count() }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold {{ $varianceClass }}">
                                    {{ number_format($totalVariance, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-700',
                                        'in_progress' => 'bg-yellow-100 text-yellow-700',
                                        'completed' => 'bg-green-100 text-green-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                    ];
                                    $statusLabels = [
                                        'draft' => 'Draft',
                                        'in_progress' => 'In Progress',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$count->status] }}">
                                    {{ $statusLabels[$count->status] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">
                                {{ $count->creator->first_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('store.stock-counts.show', $count->id) }}"
                                       class="text-blue-600 hover:text-blue-800 transition" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                <i class="fas fa-clipboard-list text-3xl mb-2 block"></i>
                                No stock counts found.
                                <a href="{{ route('store.stock-counts.create', ['type' => $type]) }}" class="text-orange-600 hover:underline ml-1">
                                    Create your first stock count
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
             </table>
        </div>

        @if($stockCounts->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $stockCounts->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
