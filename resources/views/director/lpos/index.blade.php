@extends('layouts.director')
@section('title', 'Local Purchase Orders')
@section('page-title', 'Local Purchase Orders')

@section('content')
<div class="space-y-6">
    {{-- Tabs --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('director.lpos.index') }}?tab=pending" 
                   class="px-6 py-3 text-sm font-medium {{ request('tab', 'pending') == 'pending' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Pending Approval
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">
                        {{ $pendingCount }}
                    </span>
                </a>
                <a href="{{ route('director.lpos.index') }}?tab=approved" 
                   class="px-6 py-3 text-sm font-medium {{ request('tab') == 'approved' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Approved
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">
                        {{ $approvedCount }}
                    </span>
                </a>
                <a href="{{ route('director.lpos.index') }}?tab=rejected" 
                   class="px-6 py-3 text-sm font-medium {{ request('tab') == 'rejected' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Rejected
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-800">
                        {{ $rejectedCount }}
                    </span>
                </a>
            </nav>
        </div>
    </div>

    {{-- Results Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-red-800 to-red-700 px-6 py-4">
            <h2 class="text-xl font-bold text-white">
                @if(request('tab', 'pending') == 'pending') LPOs Pending Approval
                @elseif(request('tab') == 'approved') Approved LPOs
                @elseif(request('tab') == 'rejected') Rejected LPOs
                @endif
            </h2>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 border text-left">LPO Number</th>
                        <th class="p-3 border text-left">Requisition #</th>
                        <th class="p-3 border text-left">Vendor</th>
                        <th class="p-3 border text-center">LPO Date</th>
                        <th class="p-3 border text-right">Total Amount</th>
                        <th class="p-3 border text-center">Status</th>
                        <th class="p-3 border text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lpos as $lpo)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 border font-mono font-semibold">{{ $lpo->lpo_number }}</td>
                        <td class="p-3 border">{{ $lpo->requisition->requisition_number ?? 'N/A' }}</td>
                        <td class="p-3 border">{{ $lpo->vendor->name ?? 'N/A' }}</td>
                        <td class="p-3 border text-center">{{ $lpo->lpo_date->format('Y-m-d') }}</td>
                        <td class="p-3 border text-right font-semibold text-green-600">UGX {{ number_format($lpo->total_amount, 2) }}</td>
                        <td class="p-3 border text-center">
                            @if($lpo->status == 'pending_director')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($lpo->status == 'director_approved')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Approved</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Rejected</span>
                            @endif
                        </td>
                        <td class="p-3 border text-center">
                            <a href="{{ route('director.lpos.show', $lpo->id) }}" class="text-blue-600 hover:text-blue-800">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">No LPOs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $lpos->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection