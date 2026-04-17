@extends('layouts.procurement')
@section('title', 'Purchase Orders')
@section('page-title', 'Purchase Orders')

@section('content')
@if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger mb-4">{{ session('error') }}</div>
@endif

<div class="mb-4 flex justify-between items-center">
    <a href="{{ route('procurement.purchase-orders.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        Create Purchase Order
    </a>
</div>

<div class="bg-white rounded shadow-sm p-4 overflow-x-auto">
    <table class="min-w-full table-auto border border-gray-200">
        <thead>
            <tr class="bg-gray-50">
                <th class="px-4 py-2 border">PO Number</th>
                <th class="px-4 py-2 border">Vendor</th>
                <th class="px-4 py-2 border">PO Date</th>
                <th class="px-4 py-2 border">Expected Delivery</th>
                <th class="px-4 py-2 border">Status</th>
                <th class="px-4 py-2 border">Total Amount</th>
                <th class="px-4 py-2 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrders as $po)
                <tr>
                    <td class="px-4 py-2 border font-mono">{{ $po->po_number }}</td>
                    <td class="px-4 py-2 border">{{ $po->vendor->name ?? '—' }}</td>
                    <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($po->po_date)->format('Y-m-d') }}</td>
                    <td class="px-4 py-2 border">
                        {{ $po->expected_delivery_date ? \Carbon\Carbon::parse($po->expected_delivery_date)->format('Y-m-d') : '—' }}
                    </td>
                    <td class="px-4 py-2 border">
                        <span class="inline-block px-2 py-1 text-xs rounded
                            @if($po->status=='draft') bg-yellow-100 text-yellow-800
                            @elseif($po->status=='approved') bg-green-100 text-green-800
                            @elseif($po->status=='cancelled') bg-red-100 text-red-800
                            @elseif($po->status=='sent') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($po->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 border text-right font-mono">{{ number_format($po->total_amount, 2) }}</td>
                    <td class="px-4 py-2 border">
                        <a href="{{ route('procurement.purchase-orders.show', $po->id) }}" class="text-blue-600 hover:underline">View</a>
                        <a href="{{ route('procurement.purchase-orders.edit', $po->id) }}" class="ml-1 text-green-600 hover:underline">Edit</a>
                        <form action="{{ route('procurement.purchase-orders.destroy', $po->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this PO?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ml-1 text-red-600 hover:underline bg-transparent border-none p-0">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 border text-center text-gray-500">No purchase orders found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $purchaseOrders->links() }}
    </div>
</div>
@endsection