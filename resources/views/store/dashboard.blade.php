@extends('layouts.store')

@section('title', 'Store Dashboard')

@section('page-title', 'Store Dashboard')

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

    .alert-card {
        background: #fef2f2;
        border-left: 4px solid #ef4444;
        border-radius: 10px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
    }
    .alert-warning {
        background: #fffbeb;
        border-left-color: #f59e0b;
    }
    .alert-info {
        background: #eff6ff;
        border-left-color: #3b82f6;
    }

    .badge-urgent { background: #dc2626; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; }
    .badge-pending { background: #f59e0b; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; }
    .badge-approved { background: #10b981; color: white; padding: 2px 8px; border-radius: 20px; font-size: 0.6rem; }

    .quick-action-btn {
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.75rem;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
    }
    .quick-action-btn:hover {
        background: #e5e7eb;
        transform: translateY(-2px);
    }

    .data-table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
    .data-table th { background: #f8fafc; padding: 0.75rem; text-align: left; font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0; }
    .data-table td { padding: 0.75rem; border-bottom: 1px solid #e2e8f0; }
    .data-table tr:hover { background: #f8fafc; }
    .text-right { text-align: right; }

    .trend-up { color: #10b981; }
    .trend-down { color: #ef4444; }

    .kpi-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 1rem;
    }

    .stock-health { width: 100%; background: #e5e7eb; border-radius: 10px; height: 8px; overflow: hidden; }
    .stock-health-fill { height: 100%; border-radius: 10px; transition: width 0.3s; }
    .health-good { background: #10b981; }
    .health-warning { background: #f59e0b; }
    .health-critical { background: #ef4444; }
</style>

<div class="space-y-6">

    {{-- Welcome Section --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">Welcome back, {{ Auth::user()->first_name ?? 'Store Manager' }}!</h2>
                <p class="text-blue-100 mt-1">{{ now()->format('l, F d, Y') }} | {{ now()->format('h:i A') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm">Today's Activity</p>
                <p class="text-2xl font-bold">{{ $todayIssuesCount ?? 0 }} Issues | {{ $todayReturnsCount ?? 0 }} Returns</p>
            </div>
        </div>
    </div>

    {{-- KPI Cards Row --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <h3>Total Stock Value</h3>
            <div class="value">UGX {{ number_format($totalStockValue ?? 0, 2) }}</div>
            <div class="trend {{ ($stockValueChange ?? 0) >= 0 ? 'trend-up' : 'trend-down' }}">
                {{ ($stockValueChange ?? 0) >= 0 ? '↑' : '↓' }} {{ number_format(abs($stockValueChange ?? 0), 1) }}% from yesterday
            </div>
        </div>
        <div class="stat-card" style="border-left-color: #10b981;">
            <h3>Stock IN Today</h3>
            <div class="value">{{ number_format($stockInToday ?? 0) }} units</div>
            <div class="trend">+{{ number_format($stockInGrowth ?? 0) }}% vs yesterday</div>
        </div>
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <h3>Stock OUT Today</h3>
            <div class="value">{{ number_format($stockOutToday ?? 0) }} units</div>
            <div class="trend">{{ ($stockOutGrowth ?? 0) >= 0 ? '+' : '' }}{{ number_format($stockOutGrowth ?? 0) }}% vs yesterday</div>
        </div>
        <div class="stat-card" style="border-left-color: #8b5cf6;">
            <h3>Net Change Today</h3>
            <div class="value {{ ($netChangeToday ?? 0) >= 0 ? 'trend-up' : 'trend-down' }}">
                {{ ($netChangeToday ?? 0) >= 0 ? '+' : '' }}{{ number_format($netChangeToday ?? 0) }} units
            </div>
            <div class="trend">Stock {{ ($netChangeToday ?? 0) >= 0 ? 'increased' : 'decreased' }} today</div>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN: Alerts & Pending --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- LOW STOCK ALERTS --}}
            @if(($lowStockItems ?? collect())->count() > 0)
            <div class="bg-white rounded-xl border border-red-200 overflow-hidden">
                <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                    <h3 class="font-semibold text-red-700">⚠️ Low Stock Alerts (Reorder Required)</h3>
                </div>
                <div class="p-4">
                    @foreach($lowStockItems ?? [] as $item)
                    <div class="alert-card">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-medium">{{ $item->name }}</span>
                                <span class="text-xs text-gray-500 ml-2">Code: {{ $item->item_code }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-red-600">{{ number_format($item->current_stock, 2) }}</span>
                                <span class="text-xs text-gray-500"> / Min: {{ number_format($item->minimum_stock, 2) }}</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <div class="stock-health">
                                <div class="stock-health-fill health-critical" style="width: {{ min(100, ($item->current_stock / $item->minimum_stock) * 100) }}%"></div>
                            </div>
                        </div>
                        <div class="mt-2 flex justify-end">
                            <a href="{{ route('store.inventory.adjust', $item->id) }}" class="text-xs text-blue-600 hover:underline">Reorder Now →</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- OUT OF STOCK ALERTS --}}
            @if(($outOfStockItems ?? collect())->count() > 0)
            <div class="bg-white rounded-xl border border-red-400 overflow-hidden">
                <div class="bg-red-100 px-4 py-3 border-b border-red-300">
                    <h3 class="font-semibold text-red-800">❌ Out of Stock Items (Critical)</h3>
                </div>
                <div class="p-4">
                    <div class="grid gap-2">
                        @foreach($outOfStockItems ?? [] as $item)
                        <div class="flex justify-between items-center p-2 bg-red-50 rounded-lg">
                            <div>
                                <span class="font-medium">{{ $item->name }}</span>
                                <span class="text-xs text-gray-500 ml-2">Code: {{ $item->item_code }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-bold text-red-700">0 units left</span>
                                <a href="{{ route('store.inventory.adjust', $item->id) }}" class="ml-3 text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Order Now</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- PENDING REQUISITIONS --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-700">📋 Pending Requisitions</h3>
                    <a href="{{ route('store.department-requisitions.index') }}" class="text-xs text-blue-600 hover:underline">View All →</a>
                </div>
                <div class="p-0">
                    <table class="data-table">
                        <thead><tr><th>Date</th><th>Requisition #</th><th>Department</th><th>Items</th><th>Date Needed</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody>
                            @forelse($pendingRequisitions ?? [] as $req)
                            <tr>
                                <td>{{ $req->created_at->format('Y-m-d') }}</td>
                                <td class="font-mono">{{ $req->requisition_number }}</td>
                                <td><span class="badge-pending">{{ $req->department->name ?? 'N/A' }}</span></td>
                                <td>{{ $req->items->count() }} items</td>
                                <td class="{{ $req->date_needed && $req->date_needed <= now() ? 'text-red-600 font-bold' : '' }}">
                                    {{ $req->date_needed ? $req->date_needed->format('Y-m-d') : 'Not set' }}
                                    @if($req->date_needed && $req->date_needed <= now()) <span class="badge-urgent ml-1">URGENT</span> @endif
                                </td>
                                <td><span class="badge-pending">Pending Approval</span></td>
                                <td><a href="{{ route('store.department-requisitions.show', $req->id) }}" class="text-blue-600 text-xs hover:underline">View</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-gray-500 py-4">No pending requisitions</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- URGENT REQUESTS (Date needed today/tomorrow) --}}
            @if(($urgentRequests ?? collect())->count() > 0)
            <div class="bg-white rounded-xl border border-orange-200 overflow-hidden">
                <div class="bg-orange-50 px-4 py-3 border-b border-orange-200">
                    <h3 class="font-semibold text-orange-700">🚨 Urgent Requests (Date Needed Today/Tomorrow)</h3>
                </div>
                <div class="p-0">
                    <table class="data-table">
                        <thead><tr><th>Dept</th><th>Requisition #</th><th>Item</th><th>Qty</th><th>Date Needed</th><th>Action</th></tr></thead>
                        <tbody>
                            @foreach($urgentRequests ?? [] as $req)
                            @foreach($req->items as $item)
                            <tr>
                                <td>{{ $req->department->name ?? 'N/A' }}</td>
                                <td class="font-mono">{{ $req->requisition_number }}</td>
                                <td>{{ $item->inventoryItem->name ?? 'N/A' }}</td>
                                <td>{{ number_format($item->quantity_requested, 2) }} {{ $item->metrics ?? 'units' }}</td>
                                <td><span class="badge-urgent">{{ $req->date_needed->format('Y-m-d') }}</span></td>
                                <td><a href="{{ route('store.department-requisitions.show', $req->id) }}" class="bg-orange-600 text-white px-2 py-1 rounded text-xs hover:bg-orange-700">Process Now</a></td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- TODAY'S ACTIVITY --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-700">🔄 Today's Issues & Returns</h3>
                </div>
                <div class="p-0">
                    <table class="data-table">
                        <thead><tr><th>Time</th><th>Type</th><th>Department</th><th>Item</th><th>Qty</th><th>Taken/Returned By</th><th>Reference</th></tr></thead>
                        <tbody>
                            @forelse($todayActivities ?? [] as $activity)
                            <tr>
                                <td>{{ $activity->created_at->format('h:i A') }}</td>
                                <td>
                                    @if($activity->movementType && $activity->movementType->sign == '-')
                                        <span class="badge-approved">ISSUE</span>
                                    @else
                                        <span class="badge-pending">RETURN</span>
                                    @endif
                                </td>
                                <td>{{ $activity->department->name ?? 'N/A' }}</td>
                                <td>{{ $activity->inventoryItem->name ?? 'N/A' }}</td>
                                <td class="text-right">{{ number_format($activity->quantity_in_base_unit ?? 0, 2) }}</td>
                                <td>{{ $activity->taken_by ?? $activity->returned_by ?? $activity->createdBy->name ?? 'System' }}</td>
                                <td class="font-mono text-xs">{{ $activity->movement_number }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-gray-500 py-4">No activities today</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Charts & Quick Actions --}}
        <div class="space-y-6">

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-700 mb-3">⚡ Quick Actions</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('store.inventory.adjust', 0) }}" class="quick-action-btn">
                        <div class="text-2xl">➕</div>
                        <div class="text-xs font-medium">Stock Take</div>
                    </a>
                    <a href="{{ route('store.department-requisitions.index') }}?status=approved" class="quick-action-btn">
                        <div class="text-2xl">📦</div>
                        <div class="text-xs font-medium">Quick Issue</div>
                    </a>
                    <a href="{{ route('store.department-requisitions.index') }}?status=issued" class="quick-action-btn">
                        <div class="text-2xl">🔄</div>
                        <div class="text-xs font-medium">Quick Return</div>
                    </a>
                    <a href="{{ route('store.inventory.index') }}" class="quick-action-btn">
                        <div class="text-2xl">📊</div>
                        <div class="text-xs font-medium">Inventory Report</div>
                    </a>
                </div>
            </div>

            {{-- Today's Activity Pie Chart --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-700 mb-3 text-center">Today's Activity</h3>
                <canvas id="todayActivityChart" height="200"></canvas>
                <div class="text-center mt-3">
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div><span class="inline-block w-3 h-3 rounded-full bg-green-500"></span> Issues: {{ $todayIssuesCount ?? 0 }}</div>
                        <div><span class="inline-block w-3 h-3 rounded-full bg-orange-500"></span> Returns: {{ $todayReturnsCount ?? 0 }}</div>
                        <div><span class="inline-block w-3 h-3 rounded-full bg-blue-500"></span> Stock IN: {{ $todayStockInCount ?? 0 }}</div>
                    </div>
                </div>
            </div>

            {{-- Department Request Volume --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-700 mb-3 text-center">Department Request Volume</h3>
                <canvas id="deptRequestChart" height="200"></canvas>
                <div class="mt-3 text-xs text-center text-gray-500">
                    Pending requisitions by department
                </div>
            </div>

            {{-- Stock Health Gauge --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-700 mb-3 text-center">Stock Health Overview</h3>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span>Healthy Stock</span>
                            <span>{{ $healthyStockPercent ?? 0 }}%</span>
                        </div>
                        <div class="stock-health"><div class="stock-health-fill health-good" style="width: {{ $healthyStockPercent ?? 0 }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span>Low Stock</span>
                            <span>{{ $lowStockPercent ?? 0 }}%</span>
                        </div>
                        <div class="stock-health"><div class="stock-health-fill health-warning" style="width: {{ $lowStockPercent ?? 0 }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span>Out of Stock</span>
                            <span>{{ $outOfStockPercent ?? 0 }}%</span>
                        </div>
                        <div class="stock-health"><div class="stock-health-fill health-critical" style="width: {{ $outOfStockPercent ?? 0 }}%"></div></div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t text-center">
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div><span class="font-bold text-green-600">{{ $healthyStockCount ?? 0 }}</span><br>Healthy</div>
                        <div><span class="font-bold text-orange-600">{{ $lowStockCount ?? 0 }}</span><br>Low</div>
                        <div><span class="font-bold text-red-600">{{ $outOfStockCount ?? 0 }}</span><br>Out</div>
                    </div>
                </div>
            </div>

            {{-- Top Moving Items Today --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-700 mb-3">🏆 Top Moving Items Today</h3>
                <div class="space-y-2">
                    @forelse($topItemsToday ?? [] as $item)
                    <div class="flex justify-between items-center">
                        <div class="text-sm">{{ $item['name'] }}</div>
                        <div class="text-sm font-bold text-blue-600">{{ $item['quantity'] }} units</div>
                    </div>
                    <div class="stock-health"><div class="stock-health-fill health-good" style="width: {{ min(100, ($item['quantity'] / max($topItemsTodayMax ?? 1, 1)) * 100) }}%"></div></div>
                    @empty
                    <div class="text-center text-gray-500 py-2">No activity yet today</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Today's Activity Pie Chart
    new Chart(document.getElementById('todayActivityChart'), {
        type: 'pie',
        data: {
            labels: ['Issues', 'Returns', 'Stock IN'],
            datasets: [{ data: [{{ $todayIssuesCount ?? 0 }}, {{ $todayReturnsCount ?? 0 }}, {{ $todayStockInCount ?? 0 }}], backgroundColor: ['#10b981', '#f59e0b', '#3b82f6'] }]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Department Request Volume Bar Chart
    new Chart(document.getElementById('deptRequestChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($deptRequestLabels ?? []) !!},
            datasets: [{ label: 'Pending Requests', data: {!! json_encode($deptRequestData ?? []) !!}, backgroundColor: '#8b5cf6', borderRadius: 8 }]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } } }
    });
});
</script>
@endsection
