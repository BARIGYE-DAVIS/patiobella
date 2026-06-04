@extends('layouts.management')

@section('title', 'Performance Reports')
@section('page-title', 'Performance Reports')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-600"></i>
                Performance Stock Take Reports
            </h3>
            <p class="text-xs text-gray-500 mt-1">Track daily sales, COGS, and profit margins by department</p>
        </div>
        <a href="{{ route('management.performance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
            <i class="fas fa-plus"></i> New Stock Take
        </a>
    </div>

    <div class="p-4 sm:p-6">
        <form method="GET" action="{{ route('management.performance.index') }}" class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Department</label>
                    <select name="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
            </div>
            <div class="mt-3 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Report #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Total Sales</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Total COGS</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Profit</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Margin</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $report->report_number }}</td>
                        <td class="px-4 py-3">{{ $report->department->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $report->report_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-emerald-600">{{ number_format($report->total_sales, 0) }} UGX</td>
                        <td class="px-4 py-3 text-right text-red-600">{{ number_format($report->total_cogs, 0) }} UGX</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($report->total_profit, 0) }} UGX</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $report->profit_margin >= 50 ? 'bg-emerald-100 text-emerald-700' : ($report->profit_margin >= 30 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ number_format($report->profit_margin, 1) }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('management.performance.show', $report->id) }}" class="text-blue-500 hover:text-blue-700" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('management.performance.destroy', $report->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this report?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-chart-line text-3xl mb-2 block"></i>
                            No performance reports found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $reports->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
