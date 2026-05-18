{{-- resources/views/bar/requisitions/index.blade.php --}}

@extends('layouts.bar')

@section('title', 'My Requisitions')

@section('page-title', 'My Requisitions')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
        transition: all 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .stat-card h3 { font-size: 0.7rem; text-transform: uppercase; color: #6b7280; margin-bottom: 0.5rem; letter-spacing: 0.5px; }
    .stat-card .value { font-size: 1.5rem; font-weight: bold; }

    .badge-pending { background: #f59e0b; color: white; padding: 2px 10px; border-radius: 20px; font-size: 0.7rem; }
    .badge-approved { background: #10b981; color: white; padding: 2px 10px; border-radius: 20px; font-size: 0.7rem; }
    .badge-issued { background: #3b82f6; color: white; padding: 2px 10px; border-radius: 20px; font-size: 0.7rem; }
    .badge-returned { background: #8b5cf6; color: white; padding: 2px 10px; border-radius: 20px; font-size: 0.7rem; }
    .badge-rejected { background: #ef4444; color: white; padding: 2px 10px; border-radius: 20px; font-size: 0.7rem; }
    .badge-cancelled { background: #6b7280; color: white; padding: 2px 10px; border-radius: 20px; font-size: 0.7rem; }

    .data-table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
    .data-table th { background: #f8fafc; padding: 0.75rem; text-align: left; font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0; }
    .data-table td { padding: 0.75rem; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    .data-table tr:hover { background: #f8fafc; }
    .text-right { text-align: right; }

    .filter-card {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    .filter-input {
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.75rem;
        width: 100%;
    }
    .btn-new {
        background: #ea580c;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        transition: all 0.2s;
    }
    .btn-new:hover {
        background: #c2410c;
        color: white;
    }
</style>

<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <h3><i class="fas fa-clock mr-1"></i> Pending</h3>
            <div class="value">{{ $requisitions->where('status', 'pending')->count() }}</div>
            <p class="text-xs text-gray-500 mt-1">Awaiting store approval</p>
        </div>
        <div class="stat-card" style="border-left-color: #10b981;">
            <h3><i class="fas fa-check-circle mr-1"></i> Approved</h3>
            <div class="value">{{ $requisitions->where('status', 'approved')->count() }}</div>
            <p class="text-xs text-gray-500 mt-1">Ready for pickup</p>
        </div>
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <h3><i class="fas fa-boxes mr-1"></i> Issued</h3>
            <div class="value">{{ $requisitions->where('status', 'issued')->count() + $requisitions->where('status', 'partially_issued')->count() }}</div>
            <p class="text-xs text-gray-500 mt-1">Items received</p>
        </div>
        <div class="stat-card" style="border-left-color: #ef4444;">
            <h3><i class="fas fa-times-circle mr-1"></i> Rejected/Cancelled</h3>
            <div class="value">{{ $requisitions->where('status', 'rejected')->count() + $requisitions->where('status', 'cancelled')->count() }}</div>
            <p class="text-xs text-gray-500 mt-1">Not fulfilled</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('bar.requisitions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" class="filter-input" placeholder="Requisition #" value="{{ request('search') }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="filter-input">
                    <option value="">All Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <div class="flex gap-2">
                    <button type="submit" class="bg-orange-600 text-white px-3 py-2 rounded-lg text-xs hover:bg-orange-700">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                    <a href="{{ route('bar.requisitions.index') }}" class="bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-xs hover:bg-gray-400">
                        <i class="fas fa-times mr-1"></i> Clear
                    </a>
                </div>
            </div>
            <div class="flex justify-end">
                <a href="{{ route('bar.requisitions.create') }}" class="btn-new">
                    <i class="fas fa-plus mr-1"></i> New Requisition
                </a>
            </div>
        </form>
    </div>

    {{-- Requisitions Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Requisition #</th>
                        <th>Date</th>
                        <th>Date Needed</th>
                        <th class="text-right">Items</th>
                        <th class="text-right">Total Qty</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requisitions as $req)
                    @php
                        $totalQuantity = $req->items->sum('quantity_requested');
                    @endphp
                    <tr>
                        <td class="font-mono font-medium">{{ $req->requisition_number }}</td>
                        <td>{{ $req->created_at->format('Y-m-d') }}</td>
                        <td>{{ $req->date_needed ? \Carbon\Carbon::parse($req->date_needed)->format('Y-m-d') : 'Not set' }}</td>
                        <td class="text-right">{{ $req->items->count() }}</td>
                        <td class="text-right">{{ number_format($totalQuantity, 2) }}</td>
                        <td>
                            @if($req->status == 'pending')
                                <span class="badge-pending"><i class="fas fa-clock mr-1"></i> Pending</span>
                            @elseif($req->status == 'approved')
                                <span class="badge-approved"><i class="fas fa-check-circle mr-1"></i> Approved</span>
                            @elseif($req->status == 'issued')
                                <span class="badge-issued"><i class="fas fa-boxes mr-1"></i> Issued</span>
                            @elseif($req->status == 'partially_issued')
                                <span class="badge-issued"><i class="fas fa-boxes mr-1"></i> Partially Issued</span>
                            @elseif($req->status == 'returned')
                                <span class="badge-returned"><i class="fas fa-undo-alt mr-1"></i> Returned</span>
                            @elseif($req->status == 'partially_returned')
                                <span class="badge-returned"><i class="fas fa-undo-alt mr-1"></i> Partially Returned</span>
                            @elseif($req->status == 'rejected')
                                <span class="badge-rejected"><i class="fas fa-times-circle mr-1"></i> Rejected</span>
                            @elseif($req->status == 'cancelled')
                                <span class="badge-cancelled"><i class="fas fa-ban mr-1"></i> Cancelled</span>
                            @else
                                <span class="badge-pending">{{ ucfirst($req->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('bar.requisitions.show', $req->id) }}" class="text-blue-600 hover:underline text-sm">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                            @if($req->status == 'pending')
                                <button class="text-red-600 hover:underline text-sm ml-2 cancel-requisition" data-id="{{ $req->id }}" data-number="{{ $req->requisition_number }}">
                                    <i class="fas fa-ban mr-1"></i> Cancel
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 py-8">
                                <i class="fas fa-clipboard-list text-4xl mb-2 block"></i>
                                No requisitions found. Click "New Requisition" to request items from the store.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $requisitions->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- Cancel Confirmation Modal --}}
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Confirm Cancellation</h3>
        </div>
        <div class="p-4">
            <p class="text-gray-600">Are you sure you want to cancel requisition <strong id="cancelRequisitionNumber"></strong>?</p>
            <p class="text-xs text-red-500 mt-2">This action cannot be undone.</p>
        </div>
        <div class="p-4 border-t border-gray-200 flex justify-end gap-2">
            <button id="closeCancelModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-400">Close</button>
            <form id="cancelForm" method="POST" action="">
                @csrf
                @method('PUT')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">Yes, Cancel</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Cancel Modal
    const cancelModal = document.getElementById('cancelModal');
    const cancelForm = document.getElementById('cancelForm');
    const cancelRequisitionNumber = document.getElementById('cancelRequisitionNumber');
    const closeCancelModal = document.getElementById('closeCancelModal');

    document.querySelectorAll('.cancel-requisition').forEach(btn => {
        btn.addEventListener('click', function() {
            const requisitionId = this.dataset.id;
            const requisitionNumber = this.dataset.number;
            cancelRequisitionNumber.textContent = requisitionNumber;
            cancelForm.action = `/bar/requisitions/${requisitionId}/cancel`;
            cancelModal.classList.remove('hidden');
            cancelModal.classList.add('flex');
        });
    });

    function closeModal() {
        cancelModal.classList.add('hidden');
        cancelModal.classList.remove('flex');
    }

    closeCancelModal.addEventListener('click', closeModal);
    cancelModal.addEventListener('click', function(e) {
        if (e.target === cancelModal) closeModal();
    });
</script>
@endsection
