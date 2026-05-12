@extends('layouts.procurement')

@section('title', 'Procurement Dashboard')

@section('page-title', 'Procurement Dashboard')

@section('content')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .stat-card h3 { font-size: 0.7rem; text-transform: uppercase; color: #6b7280; margin-bottom: 0.5rem; letter-spacing: 0.5px; }
    .stat-card .value { font-size: 1.5rem; font-weight: bold; }
    .stat-card .trend { font-size: 0.7rem; margin-top: 0.25rem; }

    .badge-urgent { background: #dc2626; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; font-weight: 500; }
    .badge-pending { background: #f59e0b; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; font-weight: 500; }
    .badge-approved { background: #10b981; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; font-weight: 500; }
    .badge-sent { background: #3b82f6; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; font-weight: 500; }
    .badge-overdue { background: #ef4444; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; font-weight: 500; }

    .quick-action-btn {
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.75rem;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
        display: block;
        text-decoration: none;
        color: #374151;
    }
    .quick-action-btn:hover {
        background: #e5e7eb;
        transform: translateY(-2px);
        text-decoration: none;
        color: #374151;
    }

    .data-table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
    .data-table th { background: #f8fafc; padding: 0.75rem; text-align: left; font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0; }
    .data-table td { padding: 0.75rem; border-bottom: 1px solid #e2e8f0; }
    .data-table tr:hover { background: #f8fafc; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }

    .trend-up { color: #10b981; }
    .trend-down { color: #ef4444; }

    .stock-health { width: 100%; background: #e5e7eb; border-radius: 10px; height: 8px; overflow: hidden; }
    .stock-health-fill { height: 100%; border-radius: 10px; transition: width 0.3s; }
    .vendor-bar { background: #8b5cf6; }

    .two-col { display: flex; gap: 1.5rem; margin-bottom: 1.5rem; }
    .col { flex: 1; }

    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }
    .chart-title {
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        text-align: center;
    }

    .font-mono { font-family: monospace; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.65rem; border-radius: 0.25rem; }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="space-y-6">

    {{-- Welcome Section --}}
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold"><i class="fas fa-chart-line mr-2"></i> Welcome, {{ Auth::user()->first_name ?? 'Procurement Officer' }}!</h2>
                <p class="text-emerald-100 mt-1">{{ now()->format('l, F d, Y') }} | {{ now()->format('h:i A') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm"><i class="fas fa-tasks mr-1"></i> Pending Actions</p>
                <p class="text-2xl font-bold">{{ ($pendingRequisitionsCount ?? 0) + ($pendingLposCount ?? 0) }}</p>
            </div>
        </div>
    </div>

    {{-- KPI Cards Row --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <h3><i class="fas fa-shopping-cart mr-1"></i> POs (This Month)</h3>
            <div class="value">{{ number_format($totalPOsThisMonth ?? 0) }}</div>
            <div class="trend {{ ($poGrowth ?? 0) >= 0 ? 'trend-up' : 'trend-down' }}">
                <i class="fas {{ ($poGrowth ?? 0) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-1"></i>
                {{ number_format(abs($poGrowth ?? 0), 1) }}% vs last month
            </div>
        </div>
        <div class="stat-card" style="border-left-color: #8b5cf6;">
            <h3><i class="fas fa-coins mr-1"></i> Total PO Value</h3>
            <div class="value">UGX {{ number_format($totalPOValue ?? 0, 2) }}</div>
            <div class="trend {{ ($valueGrowth ?? 0) >= 0 ? 'trend-up' : 'trend-down' }}">
                <i class="fas {{ ($valueGrowth ?? 0) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-1"></i>
                {{ number_format(abs($valueGrowth ?? 0), 1) }}%
            </div>
        </div>
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <h3><i class="fas fa-clock mr-1"></i> Pending Director Approval</h3>
            <div class="value">{{ number_format($pendingLposCount ?? 0) }}</div>
            <div class="trend"><i class="fas fa-hourglass-half mr-1"></i> LPOs awaiting</div>
        </div>
        <div class="stat-card" style="border-left-color: #ef4444;">
            <h3><i class="fas fa-exclamation-triangle mr-1"></i> Overdue Deliveries</h3>
            <div class="value">{{ number_format($overdueDeliveriesCount ?? 0) }}</div>
            <div class="trend trend-down"><i class="fas fa-calendar-times mr-1"></i> Past due date</div>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- REQUISITIONS FROM STORE --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-700"><i class="fas fa-clipboard-list mr-2"></i> Requisitions from Store</h3>
                    <a href="{{ route('procurement.requisitions.index') }}" class="text-xs text-blue-600 hover:underline"><i class="fas fa-eye mr-1"></i> View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Requisition #</th>
                                <th>Date</th>
                                <th>Department</th>
                                <th class="text-right">Items</th>
                                <th class="text-right">Qty</th>
                                <th>Priority</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requisitionsFromStore ?? [] as $req)
                            <tr>
                                <td class="font-mono">{{ $req->requisition_number }}</td>
                                <td>{{ $req->created_at->format('Y-m-d') }}</td>
                                <td><span class="badge-pending">{{ $req->department->name ?? 'N/A' }}</span></td>
                                <td class="text-right">{{ $req->items->count() }}</td>
                                <td class="text-right">{{ number_format($req->items->sum('quantity_requested'), 2) }}</td>
                                <td>
                                    @if($req->date_needed && $req->date_needed <= now())
                                        <span class="badge-urgent"><i class="fas fa-exclamation-circle mr-1"></i> URGENT</span>
                                    @elseif($req->created_at->diffInDays(now()) > 3)
                                        <span class="badge-urgent"><i class="fas fa-hourglass-end mr-1"></i> WAITING</span>
                                    @else
                                        <span class="badge-pending"><i class="fas fa-hourglass-half mr-1"></i> Normal</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('procurement.purchase-orders.create', $req->id) }}" class="bg-emerald-600 text-white px-2 py-1 rounded text-xs hover:bg-emerald-700">
                                        <i class="fas fa-file-invoice mr-1"></i> Create PO
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-gray-500 py-4"><i class="fas fa-check-circle mr-1"></i> No pending requisitions</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- OVERDUE DELIVERIES ALERT --}}
            @if(($overdueDeliveries ?? collect())->count() > 0)
            <div class="bg-white rounded-xl border border-red-200 overflow-hidden">
                <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                    <h3 class="font-semibold text-red-700"><i class="fas fa-exclamation-triangle mr-2"></i> Overdue Deliveries</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>PO Number</th><th>Vendor</th><th>Expected Date</th><th>Days Overdue</th><th>Action</th></tr></thead>
                        <tbody>
                            @foreach($overdueDeliveries ?? [] as $po)
                            @php $daysOverdue = max(1, round(now()->diffInDays($po->expected_delivery_date))); @endphp
                            <tr>
                                <td class="font-mono">{{ $po->po_number }}</td>
                                <td>{{ $po->vendor->name ?? 'N/A' }}</td>
                                <td>{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('Y-m-d') : 'N/A' }}</td>
                                <td class="text-red-600 font-bold">{{ $daysOverdue }} days</td>
                                <td><a href="{{ route('procurement.purchase-orders.show', $po->id) }}" class="text-blue-600 text-xs hover:underline"><i class="fas fa-eye mr-1"></i> Follow Up</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- PENDING GRNs (Awaiting Delivery) --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-700"><i class="fas fa-truck mr-2"></i> Awaiting Delivery (Sent to Vendor)</h3>
                    <a href="{{ route('procurement.goods-received.index') }}" class="text-xs text-blue-600 hover:underline"><i class="fas fa-eye mr-1"></i> View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>PO Number</th><th>Vendor</th><th>PO Date</th><th>Expected Date</th><th>Action</th></tr></thead>
                        <tbody>
                            @forelse($pendingGrns ?? [] as $po)
                            <tr>
                                <td class="font-mono">{{ $po->po_number }}</td>
                                <td>{{ $po->vendor->name ?? 'N/A' }}</td>
                                <td>{{ $po->po_date ? $po->po_date->format('Y-m-d') : 'N/A' }}</td>
                                <td class="{{ $po->expected_delivery_date && $po->expected_delivery_date < now() ? 'text-red-600' : '' }}">
                                    {{ $po->expected_delivery_date ? $po->expected_delivery_date->format('Y-m-d') : 'N/A' }}
                                </td>
                                <td>
                                    <a href="{{ route('procurement.goods-received.create', ['po_id' => $po->id]) }}" class="bg-emerald-600 text-white px-2 py-1 rounded text-xs hover:bg-emerald-700">
                                        <i class="fas fa-clipboard-check mr-1"></i> Record GRN
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-gray-500 py-4"><i class="fas fa-check-circle mr-1 text-green-500"></i> All caught up! No pending receipts.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PENDING LPOS (Director Approval) --}}
            @if(($pendingLpos ?? collect())->count() > 0)
            <div class="bg-white rounded-xl border border-orange-200 overflow-hidden">
                <div class="bg-orange-50 px-4 py-3 border-b border-orange-200">
                    <h3 class="font-semibold text-orange-700"><i class="fas fa-file-signature mr-2"></i> Pending Director Approval (LPOs)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>LPO Number</th><th>Vendor</th><th class="text-right">Amount</th><th>Date Sent</th><th>Action</th></tr></thead>
                        <tbody>
                            @foreach($pendingLpos ?? [] as $lpo)
                            <tr>
                                <td class="font-mono">{{ $lpo->lpo_number }}</td>
                                <td>{{ $lpo->vendor->name ?? 'N/A' }}</td>
                                <td class="text-right">UGX {{ number_format($lpo->total_amount, 2) }}</td>
                                <td>{{ $lpo->created_at->format('Y-m-d') }}</td>
                                <td><a href="{{ route('procurement.lpo.show', $lpo->id) }}" class="text-blue-600 text-xs hover:underline"><i class="fas fa-eye mr-1"></i> View</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="space-y-6">

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-700 mb-3"><i class="fas fa-bolt mr-2"></i> Quick Actions</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('procurement.requisitions.index') }}" class="quick-action-btn"><i class="fas fa-clipboard-list text-2xl mb-1 d-block"></i><div class="text-xs font-medium">Requisitions</div></a>
                    <a href="{{ route('procurement.purchase-orders.index') }}" class="quick-action-btn"><i class="fas fa-shopping-cart text-2xl mb-1 d-block"></i><div class="text-xs font-medium">Purchase Orders</div></a>
                    <a href="{{ route('procurement.goods-received.index') }}" class="quick-action-btn"><i class="fas fa-truck text-2xl mb-1 d-block"></i><div class="text-xs font-medium">Goods Received</div></a>
                    <a href="{{ route('procurement.vendors.index') }}" class="quick-action-btn"><i class="fas fa-building text-2xl mb-1 d-block"></i><div class="text-xs font-medium">Vendors</div></a>
                </div>
            </div>

            {{-- PO Status Pie Chart --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-700 mb-3 text-center"><i class="fas fa-chart-pie mr-1"></i> PO Status</h3>
                <canvas id="poStatusChart" height="200"></canvas>
                <div class="text-center mt-3 text-xs text-gray-500">
                    <div class="grid grid-cols-2 gap-2">
                        <div><i class="fas fa-circle text-orange-500 mr-1"></i> Draft: {{ $poDraftCount ?? 0 }}</div>
                        <div><i class="fas fa-circle text-green-500 mr-1"></i> Approved: {{ $poApprovedCount ?? 0 }}</div>
                        <div><i class="fas fa-circle text-blue-500 mr-1"></i> Sent: {{ $poSentCount ?? 0 }}</div>
                        <div><i class="fas fa-circle text-teal-500 mr-1"></i> Received: {{ $poFullyReceivedCount ?? 0 }}</div>
                    </div>
                </div>
            </div>

            {{-- Monthly PO Value Trend --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-700 mb-3 text-center"><i class="fas fa-chart-line mr-1"></i> Monthly PO Trend</h3>
                <canvas id="monthlyTrendChart" height="200"></canvas>
            </div>

            {{-- Top Vendors --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-700 mb-3"><i class="fas fa-trophy mr-1"></i> Top Vendors</h3>
                <div class="space-y-3">
                    @forelse($topVendors ?? [] as $index => $vendor)
                    <div>
                        <div class="flex justify-between text-xs mb-1"><span><span class="text-gray-500 mr-1">{{ $index + 1 }}.</span> {{ $vendor['name'] }}</span><span>UGX {{ number_format($vendor['total'], 2) }}</span></div>
                        <div class="stock-health"><div class="stock-health-fill vendor-bar" style="width: {{ ($vendor['total'] / max($topVendorsMax, 1)) * 100 }}%"></div></div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-2"><i class="fas fa-chart-simple"></i> No data</div>
                    @endforelse
                </div>
            </div>

            {{-- Requisitions by Department --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-700 mb-3 text-center"><i class="fas fa-chart-bar mr-1"></i> Requisitions by Dept</h3>
                <canvas id="deptRequisitionChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const poStatusCtx = document.getElementById('poStatusChart')?.getContext('2d');
    if (poStatusCtx) {
        new Chart(poStatusCtx, {
            type: 'pie',
            data: { labels: ['Draft', 'Approved', 'Sent', 'Fully Received'], datasets: [{ data: [{{ $poDraftCount ?? 0 }}, {{ $poApprovedCount ?? 0 }}, {{ $poSentCount ?? 0 }}, {{ $poFullyReceivedCount ?? 0 }}], backgroundColor: ['#f59e0b', '#10b981', '#3b82f6', '#06b6d4'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }
        });
    }

    const monthlyCtx = document.getElementById('monthlyTrendChart')?.getContext('2d');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: { labels: {!! json_encode($monthlyLabels ?? []) !!}, datasets: [{ label: 'PO Value (UGX)', data: {!! json_encode($monthlyValues ?? []) !!}, borderColor: '#8b5cf6', backgroundColor: 'rgba(139, 92, 246, 0.1)', fill: true, tension: 0.4, pointBackgroundColor: '#8b5cf6', pointBorderColor: '#fff', pointRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
        });
    }

    const deptCtx = document.getElementById('deptRequisitionChart')?.getContext('2d');
    if (deptCtx) {
        new Chart(deptCtx, {
            type: 'bar',
            data: { labels: {!! json_encode($deptRequisitionLabels ?? []) !!}, datasets: [{ label: 'Pending Requisitions', data: {!! json_encode($deptRequisitionData ?? []) !!}, backgroundColor: '#f59e0b', borderRadius: 8 }] },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } } }
        });
    }
});
</script>
@endsection
