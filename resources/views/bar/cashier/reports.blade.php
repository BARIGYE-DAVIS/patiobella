{{-- resources/views/bar/cashier/reports.blade.php --}}

@extends('layouts.bar-cashier')

@section('title', 'Bar Reports')

@section('page-title', 'Sales Reports')

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
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        margin-top: 0.5rem;
    }
    .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #6b7280;
    }
    .filter-bar {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
    }
    .data-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .data-table tr:hover {
        background: #fef3c7;
    }
    .btn-export {
        background: #10b981;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-export:hover {
        background: #059669;
    }
    .btn-view {
        background: #3b82f6;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.7rem;
        text-decoration: none;
    }
    .btn-view:hover {
        background: #2563eb;
    }
    .tabs {
        display: flex;
        gap: 0.5rem;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }
    .tab-btn {
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 500;
        background: transparent;
        border: none;
        cursor: pointer;
        color: #6b7280;
        transition: all 0.2s;
        border-bottom: 2px solid transparent;
    }
    .tab-btn.active {
        color: #ea580c;
        border-bottom-color: #ea580c;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
</style>

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Sales Reports
                </h2>
                <p class="text-blue-100 mt-1">View and export your sales data</p>
            </div>
            <div>
                <p class="text-sm"><i class="fas fa-user mr-1"></i> Cashier: {{ Auth::user()->first_name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tabs">
        <button class="tab-btn active" data-tab="daily">
            <i class="fas fa-calendar-day mr-2"></i> Daily Report
        </button>
        <button class="tab-btn" data-tab="monthly">
            <i class="fas fa-calendar-month mr-2"></i> Monthly Report
        </button>
    </div>

    {{-- Tab 1: Daily Report --}}
    <div id="tab-daily" class="tab-content active">
        <div class="filter-bar">
            <form method="GET" action="{{ route('bar.cashier.daily-summary') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Select Date</label>
                    <input type="date" name="date" id="dailyDate" class="p-2 border rounded-lg text-sm" value="{{ today()->format('Y-m-d') }}">
                </div>
                <div>
                    <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-search mr-1"></i> View Report
                    </button>
                </div>
                <div class="flex gap-2 ml-auto">
                    <a href="{{ route('bar.cashier.reports.export.excel', ['type' => 'daily']) }}" class="btn-export">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <a href="{{ route('bar.cashier.reports.export.pdf', ['type' => 'daily']) }}" class="btn-export">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-chart-line mr-2 text-blue-600"></i> Daily Sales Summary
                </h3>
            </div>
            <div class="p-4 text-center text-gray-500" id="dailyContent">
                <i class="fas fa-calendar-day text-4xl mb-2 block"></i>
                <p>Select a date to view daily sales report</p>
            </div>
        </div>
    </div>

    {{-- Tab 2: Monthly Report --}}
    <div id="tab-monthly" class="tab-content">
        <div class="filter-bar">
            <form method="GET" action="{{ route('bar.cashier.reports.monthly') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Select Month</label>
                    <input type="month" name="month" id="monthlyMonth" class="p-2 border rounded-lg text-sm" value="{{ now()->format('Y-m') }}">
                </div>
                <div>
                    <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-search mr-1"></i> View Report
                    </button>
                </div>
                <div class="flex gap-2 ml-auto">
                    <a href="{{ route('bar.cashier.reports.export.excel', ['type' => 'monthly']) }}" class="btn-export">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <a href="{{ route('bar.cashier.reports.export.pdf', ['type' => 'monthly']) }}" class="btn-export">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-chart-line mr-2 text-blue-600"></i> Monthly Sales Summary
                </h3>
            </div>
            <div class="p-4 text-center text-gray-500" id="monthlyContent">
                <i class="fas fa-calendar-month text-4xl mb-2 block"></i>
                <p>Select a month to view monthly sales report</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.dataset.tab;

            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            btn.classList.add('active');
            document.getElementById(`tab-${tabId}`).classList.add('active');
        });
    });

    // Load daily report when date changes
    const dailyDate = document.getElementById('dailyDate');
    if (dailyDate) {
        dailyDate.addEventListener('change', function() {
            const date = this.value;
            if (date) {
                fetch(`{{ url("bar/cashier/daily-summary") }}?date=${date}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('dailyContent').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('dailyContent').innerHTML = '<div class="text-center py-8 text-red-500">Failed to load report</div>';
                });
            }
        });
    }

    // Load monthly report when month changes
    const monthlyMonth = document.getElementById('monthlyMonth');
    if (monthlyMonth) {
        monthlyMonth.addEventListener('change', function() {
            const month = this.value;
            if (month) {
                fetch(`{{ url("bar/cashier/reports/monthly") }}?month=${month}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('monthlyContent').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('monthlyContent').innerHTML = '<div class="text-center py-8 text-red-500">Failed to load report</div>';
                });
            }
        });
    }
</script>
@endsection
