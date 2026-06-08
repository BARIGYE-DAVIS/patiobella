@extends('layouts.store')

@section('title', 'Store Dashboard')
@section('page-title', 'Store Dashboard')

@section('content')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .chart-container {
        position: relative;
        height: 280px;
        width: 100%;
    }
</style>

<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('store.dashboard') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Date Range</label>
                <select name="date_range" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all" {{ $dateRange == 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ $dateRange == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ $dateRange == 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ $dateRange == 'year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                <select name="category_id" class="rounded-lg border-gray-300 text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $categoryFilter == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Department</label>
                <select name="department_id" class="rounded-lg border-gray-300 text-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ $departmentFilter == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
                <a href="{{ route('store.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors ml-2">
                    <i class="fas fa-undo-alt mr-2"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- KPI Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Inventory Value</p>
                    <p class="text-2xl font-bold text-blue-600">UGX {{ $financialMetrics->total_inventory_cost_formatted }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Stock Turnover</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $performanceKpis->stock_turnover_rate }}x</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-sync-alt text-purple-600 text-lg"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Fulfillment Rate</p>
                    <p class="text-2xl font-bold text-green-600">{{ $performanceKpis->issue_fulfillment_rate }}%</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Items</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stockStatus->total_items }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-boxes text-gray-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Stock Status Pie Chart -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-chart-pie text-blue-500"></i> Stock Status
            </h4>
            <div class="chart-container">
                <canvas id="stockStatusPieChart"></canvas>
            </div>
            <div class="mt-3 text-center">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-700 mr-2">In Stock: {{ $stockStatus->in_stock }}</span>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-700 mr-2">Out: {{ $stockStatus->out_of_stock }}</span>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Low: {{ $stockStatus->low_stock }}</span>
            </div>
        </div>

        <!-- Stock Value by Category Pie Chart -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-chart-pie text-green-500"></i> Value by Category
            </h4>
            <div class="chart-container">
                <canvas id="categoryPieChart"></canvas>
            </div>
        </div>

        <!-- Movement Types Pie Chart -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-chart-pie text-purple-500"></i> Movement Types
            </h4>
            <div class="chart-container">
                <canvas id="movementPieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Trends Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Stock Value Trend -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-500"></i> Stock Value Trend (Last 12 Months)
            </h4>
            <div class="chart-container">
                <canvas id="valueTrendChart"></canvas>
            </div>
        </div>

        <!-- Movement Trend -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-chart-line text-orange-500"></i> Movement Trend (Last 30 Days)
            </h4>
            <div class="chart-container">
                <canvas id="movementTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Department Consumption Pie Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-chart-pie text-teal-500"></i> Department Consumption
            </h4>
            <div class="chart-container">
                <canvas id="deptConsumptionPieChart"></canvas>
            </div>
        </div>

        <!-- Movement Summary -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-exchange-alt text-indigo-500"></i> Movement Summary
            </h4>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <i class="fas fa-arrow-down text-green-600 text-2xl mb-2 block"></i>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($movementAnalytics->stock_in_quantity, 2) }}</p>
                    <p class="text-sm text-gray-600">Stock IN</p>
                    <p class="text-xs text-gray-400">{{ $movementAnalytics->stock_in_count }} movements</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-xl">
                    <i class="fas fa-arrow-up text-red-600 text-2xl mb-2 block"></i>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($movementAnalytics->stock_out_quantity, 2) }}</p>
                    <p class="text-sm text-gray-600">Stock OUT</p>
                    <p class="text-xs text-gray-400">{{ $movementAnalytics->stock_out_count }} movements</p>
                </div>
            </div>
            <div class="mt-4 p-3 bg-blue-50 rounded-xl text-center">
                <p class="text-sm">Net Change:
                    <span class="font-bold {{ $movementAnalytics->net_change >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                        {{ $movementAnalytics->net_change >= 0 ? '+' : '' }}{{ number_format($movementAnalytics->net_change, 2) }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="fas fa-bell text-yellow-500"></i> Low Stock Alerts
            @if($lowStockAlerts->low_stock_count > 0)
                <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">{{ $lowStockAlerts->low_stock_count }} items</span>
            @endif
        </h4>

        @if($lowStockAlerts->critical_count > 0)
        <div class="mb-4">
            <p class="text-sm font-semibold text-red-600 mb-2">Critical (Out of Stock)</p>
            <div class="flex flex-wrap gap-2">
                @foreach($lowStockAlerts->critical_stock as $item)
                <span class="bg-red-100 text-red-700 text-xs px-2.5 py-1 rounded-full">{{ $item->name }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @if($lowStockAlerts->low_stock_count > 0)
        <div>
            <p class="text-sm font-semibold text-yellow-600 mb-2">Low Stock (Below Minimum)</p>
            <div class="space-y-2">
                @foreach($lowStockAlerts->low_stock as $item)
                <div class="flex justify-between items-center text-sm py-1 border-b border-gray-100">
                    <span class="text-gray-700">{{ $item->name }}</span>
                    <div class="flex items-center gap-3">
                        <div class="w-32 bg-gray-200 rounded-full h-1.5">
                            @php $percentage = ($item->current_stock / max($item->minimum_stock, 1)) * 100; @endphp
                            <div class="bg-yellow-500 h-1.5 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                        <span class="text-red-600 font-semibold">{{ number_format($item->current_stock ?? 0, 2) }}</span>
                        <span class="text-gray-400 text-xs">/ {{ number_format($item->minimum_stock, 2) }} {{ $item->unit_of_measurement }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($lowStockAlerts->low_stock_count == 0 && $lowStockAlerts->critical_count == 0)
        <div class="text-center py-6 text-gray-500">
            <i class="fas fa-check-circle text-green-500 text-2xl mb-2 block"></i>
            <p class="text-sm">All items are at adequate stock levels</p>
        </div>
        @endif
    </div>

    <!-- Recent Activities Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Stock Movements -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-history text-gray-500"></i> Recent Stock Movements
                </h4>
            </div>
            <div class="space-y-2 max-h-80 overflow-y-auto">
                @forelse($recentMovements as $movement)
                <div class="flex justify-between items-center text-sm border-b border-gray-100 pb-2">
                    <div>
                        <span class="font-medium text-gray-800">{{ $movement->inventoryItem->name ?? 'N/A' }}</span>
                        <span class="text-gray-400 text-xs ml-2">{{ $movement->movement_number }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $movement->movementType && $movement->movementType->sign == '+' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $movement->movementType->name ?? 'N/A' }}
                        </span>
                        <span class="font-semibold {{ $movement->movementType && $movement->movementType->sign == '+' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $movement->movementType && $movement->movementType->sign == '+' ? '+' : '-' }}{{ number_format($movement->quantity, 2) }}
                        </span>
                        <span class="text-gray-400 text-xs">{{ $movement->movement_date->format('Y-m-d') }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-gray-400">No movements recorded</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Imports -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-import text-emerald-500"></i> Recent Imports
                </h4>
            </div>
            <div class="space-y-2 max-h-80 overflow-y-auto">
                @forelse($recentImports as $import)
                <div class="flex justify-between items-center text-sm border-b border-gray-100 pb-2">
                    <div>
                        <span class="font-medium text-gray-800">{{ $import->inventoryItem->name ?? 'N/A' }}</span>
                        <span class="text-gray-400 text-xs ml-2">{{ $import->batch_number }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-blue-600 font-semibold">{{ number_format($import->initial_quantity, 2) }}</span>
                        <span class="text-gray-400 text-xs">{{ $import->unit_of_measurement }}</span>
                        <span class="text-gray-400 text-xs">{{ $import->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-gray-400">No imports recorded</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Stock Status Pie Chart
    const stockStatusCtx = document.getElementById('stockStatusPieChart').getContext('2d');
    new Chart(stockStatusCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($chartData->stock_status_pie['labels']) !!},
            datasets: [{
                data: {!! json_encode($chartData->stock_status_pie['data']) !!},
                backgroundColor: {!! json_encode($chartData->stock_status_pie['colors']) !!},
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 2. Category Pie Chart
    if ({!! json_encode(count($chartData->category_pie['labels'])) !!} > 0) {
        const categoryCtx = document.getElementById('categoryPieChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($chartData->category_pie['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData->category_pie['data']) !!},
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // 3. Movement Types Pie Chart
    if ({!! json_encode(count($chartData->movement_pie['labels'])) !!} > 0) {
        const movementCtx = document.getElementById('movementPieChart').getContext('2d');
        new Chart(movementCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($chartData->movement_pie['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData->movement_pie['data']) !!},
                    backgroundColor: ['#10b981', '#ef4444', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // 4. Stock Value Trend Line Chart
    const valueTrendCtx = document.getElementById('valueTrendChart').getContext('2d');
    new Chart(valueTrendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData->value_trend['labels']) !!},
            datasets: [{
                label: 'Stock Value (UGX)',
                data: {!! json_encode($chartData->value_trend['data']) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'UGX ' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'UGX ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // 5. Movement Trend Line Chart
    const movementTrendCtx = document.getElementById('movementTrendChart').getContext('2d');
    new Chart(movementTrendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData->movement_trend['labels']) !!},
            datasets: [
                {
                    label: 'Stock IN',
                    data: {!! json_encode($chartData->movement_trend['in']) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Stock OUT',
                    data: {!! json_encode($chartData->movement_trend['out']) !!},
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });

    // 6. Department Consumption Pie Chart
    if ({!! json_encode(count($chartData->dept_consumption_pie['labels'])) !!} > 0) {
        const deptCtx = document.getElementById('deptConsumptionPieChart').getContext('2d');
        new Chart(deptCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($chartData->dept_consumption_pie['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData->dept_consumption_pie['data']) !!},
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>
@endpush
