@extends('layouts.management')

@section('title', 'Performance Report Details')
@section('page-title', 'Performance Report Details')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-600"></i>
                {{ $report->report_number }}
            </h3>
            <p class="text-xs text-gray-500 mt-1">
                {{ $report->department->name ?? 'N/A' }} | {{ $report->report_date->format('d M Y') }} | Created by {{ $report->createdBy->first_name ?? 'N/A' }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('management.performance.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('management.performance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="fas fa-plus"></i> New Stock Take
            </a>
        </div>
    </div>

    <div class="p-4 sm:p-6">
        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl p-4 border border-emerald-200">
                <p class="text-xs text-emerald-600 uppercase font-semibold">Total Sales</p>
                <p class="text-2xl font-bold text-emerald-800">{{ number_format($report->total_sales, 0) }} UGX</p>
            </div>
            <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-xl p-4 border border-red-200">
                <p class="text-xs text-red-600 uppercase font-semibold">Total COGS</p>
                <p class="text-2xl font-bold text-red-800">{{ number_format($report->total_cogs, 0) }} UGX</p>
            </div>
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                <p class="text-xs text-blue-600 uppercase font-semibold">Total Profit</p>
                <p class="text-2xl font-bold text-blue-800">{{ number_format($report->total_profit, 0) }} UGX</p>
            </div>
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                <p class="text-xs text-purple-600 uppercase font-semibold">Profit Margin</p>
                <p class="text-2xl font-bold text-purple-800">{{ number_format($report->profit_margin, 1) }}%</p>
            </div>
        </div>

        {{-- Items Table --}}
        <h4 class="font-semibold text-gray-800 mb-3">Performance Items</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600">Menu Item</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600">Opening</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600">Added</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600">Sold</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600">Closing</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600">Selling Price</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600">Unit Cost</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600">COGS</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600">Sales</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600">Profit</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600">Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->items as $item)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-800">{{ $item->menuItem->name ?? 'N/A' }}</td>
                        <td class="px-3 py-2 text-center">{{ number_format($item->opening_quantity, 0) }}</td>
                        <td class="px-3 py-2 text-center">{{ number_format($item->added_quantity, 0) }}</td>
                        <td class="px-3 py-2 text-center font-semibold text-blue-600">{{ number_format($item->quantity_sold, 0) }}</td>
                        <td class="px-3 py-2 text-center">{{ number_format($item->closing_quantity, 0) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($item->selling_price, 0) }} UGX</td>
                        <td class="px-3 py-2 text-right">{{ number_format($item->unit_cost, 0) }} UGX</td>
                        <td class="px-3 py-2 text-right text-red-600">{{ number_format($item->cogs, 0) }} UGX</td>
                        <td class="px-3 py-2 text-right text-emerald-600">{{ number_format($item->sales_amount, 0) }} UGX</td>
                        <td class="px-3 py-2 text-right font-semibold">{{ number_format($item->profit, 0) }} UGX</td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $item->profit_margin >= 50 ? 'bg-emerald-100 text-emerald-700' : ($item->profit_margin >= 30 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ number_format($item->profit_margin, 1) }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 border-t-2 border-gray-200 font-semibold">
                    <tr>
                        <td colspan="4" class="px-3 py-3 text-right">TOTALS:</td>
                        <td class="px-3 py-2 text-center">{{ number_format($report->items->sum('closing_quantity'), 0) }}</td>
                        <td class="px-3 py-2 text-right">-</td>
                        <td class="px-3 py-2 text-right">-</td>
                        <td class="px-3 py-2 text-right text-red-600">{{ number_format($report->total_cogs, 0) }} UGX</td>
                        <td class="px-3 py-2 text-right text-emerald-600">{{ number_format($report->total_sales, 0) }} UGX</td>
                        <td class="px-3 py-2 text-right font-bold">{{ number_format($report->total_profit, 0) }} UGX</td>
                        <td class="px-3 py-2 text-center">{{ number_format($report->profit_margin, 1) }}%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
