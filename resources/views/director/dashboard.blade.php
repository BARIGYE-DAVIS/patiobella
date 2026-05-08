@extends('layouts.director')
@section('title', 'Director Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
        <p class="text-sm text-yellow-600">Pending LPOs</p>
        <p class="text-3xl font-bold text-yellow-800">{{ $pendingCount }}</p>
    </div>
    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
        <p class="text-sm text-green-600">Approved LPOs</p>
        <p class="text-3xl font-bold text-green-800">{{ $approvedCount }}</p>
    </div>
    <div class="bg-red-50 rounded-lg p-6 border border-red-200">
        <p class="text-sm text-red-600">Rejected LPOs</p>
        <p class="text-3xl font-bold text-red-800">{{ $rejectedCount }}</p>
    </div>
    <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
        <p class="text-sm text-blue-600">Total LPOs</p>
        <p class="text-3xl font-bold text-blue-800">{{ $pendingCount + $approvedCount + $rejectedCount }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Pending LPOs for Approval</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b border-gray-200">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">LPO Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentLpos->where('status', 'pending_director') as $lpo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-mono">{{ $lpo->lpo_number }}</td>
                        <td class="px-6 py-4 text-sm">{{ $lpo->vendor->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-center">UGX {{ number_format($lpo->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('director.lpos.show', $lpo->id) }}" class="text-blue-600 hover:text-blue-800">Review</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No pending LPOs</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Recent LPO Activity</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b border-gray-200">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">LPO Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentLpos->take(5) as $lpo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-mono">{{ $lpo->lpo_number }}</td>
                        <td class="px-6 py-4 text-sm">{{ $lpo->vendor->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($lpo->status == 'pending_director')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($lpo->status == 'director_approved')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Approved</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Rejected</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-center">{{ $lpo->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No LPOs found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">Quick Actions</h3>
    </div>
    <div class="p-6">
        <a href="{{ route('director.lpos.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            View All Pending LPOs
        </a>
    </div>
</div>
@endsection