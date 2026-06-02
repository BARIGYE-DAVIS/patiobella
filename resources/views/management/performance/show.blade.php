{{-- resources/views/management/performance/show.blade.php --}}

@extends('layouts.management')

@section('title', 'Performance Summary')
@section('page-title', 'Performance Summary')

@section('content')
<div class="space-y-4" id="printArea">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Performance Summary</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $summary->department->name ?? 'All Departments' }} &middot;
                    {{ \Carbon\Carbon::parse($summary->period_start)->format('d M Y') }} -
                    {{ \Carbon\Carbon::parse($summary->period_end)->format('d M Y') }}
                </p>
            </div>
            <div class="flex gap-2 no-print">
                <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="{{ route('management.performance.summaries.pdf', $summary->id) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
                <a href="{{ route('management.performance.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase mb-1"><i class="fas fa-chart-line mr-1"></i> Total Sales</p>
            <p class="text-2xl font-bold text-gray-800">UGX {{ number_format($summary->total_sales_amount, 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase mb-1"><i class="fas fa-boxes mr-1"></i> COGS</p>
            <p class="text-2xl font-bold text-red-600">UGX {{ number_format($summary->total_cogs, 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase mb-1"><i class="fas fa-chart-simple mr-1"></i> Profit</p>
            <p class="text-2xl font-bold text-green-600">UGX {{ number_format($summary->total_profit, 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase mb-1"><i class="fas fa-percent mr-1"></i> COGS %</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($summary->cogs_percentage, 1) }}%</p>
        </div>
    </div>

    {{-- Profit Margin Card --}}
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-green-600 uppercase tracking-wide font-semibold">Profit Margin</p>
                <p class="text-3xl font-bold text-green-700">{{ number_format($summary->profit_margin, 1) }}%</p>
                <p class="text-xs text-green-500 mt-1">Profit = Sales - COGS</p>
            </div>
            <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center">
                <i class="fas fa-chart-pie text-green-600 text-3xl"></i>
            </div>
        </div>
    </div>

    {{-- Top & Bottom Selling Items --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Top 10 Sellers --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 bg-green-50">
                <h3 class="text-sm font-semibold text-green-700 flex items-center gap-2">
                    <i class="fas fa-arrow-up text-green-600"></i> Top 10 Selling Items
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">#</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Item</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Qty Sold</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Sales</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">% of Sales</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($summary->topItems as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-bold text-gray-500">{{ $item->rank_position }}.</td>
                            <td class="px-4 py-2 font-medium text-gray-800">{{ $item->menuItem->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-center">{{ number_format($item->quantity_sold, 0) }}</td>
                            <td class="px-4 py-2 text-right">UGX {{ number_format($item->sales_amount, 0) }}</td>
                            <td class="px-4 py-2 text-right font-semibold text-green-600">{{ number_format($item->percentage_of_total_sales, 1) }}%</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No data available</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bottom 10 Sellers --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 bg-red-50">
                <h3 class="text-sm font-semibold text-red-700 flex items-center gap-2">
                    <i class="fas fa-arrow-down text-red-600"></i> Bottom 10 Selling Items
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">#</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Item</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Qty Sold</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Sales</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">% of Sales</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($summary->bottomItems as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-bold text-gray-500">{{ $item->rank_position }}.</td>
                            <td class="px-4 py-2 font-medium text-gray-800">{{ $item->menuItem->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-center">{{ number_format($item->quantity_sold, 0) }}</td>
                            <td class="px-4 py-2 text-right">UGX {{ number_format($item->sales_amount, 0) }}</td>
                            <td class="px-4 py-2 text-right font-semibold text-red-600">{{ number_format($item->percentage_of_total_sales, 1) }}%</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No data available</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Detailed Items Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-table"></i> Detailed Item Performance
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Item</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Qty Sold</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Sales</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">COGS</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Profit</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Profit Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($summary->allItems as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $item->menuItem->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($item->quantity_sold, 0) }}</td>
                        <td class="px-4 py-3 text-right">UGX {{ number_format($item->sales_amount, 0) }}</td>
                        <td class="px-4 py-3 text-right text-red-600">UGX {{ number_format($item->cogs, 0) }}</td>
                        <td class="px-4 py-3 text-right text-green-600">UGX {{ number_format($item->profit, 0) }}</td>
                        <td class="px-4 py-3 text-right font-semibold {{ $item->profit_margin >= 50 ? 'text-green-600' : ($item->profit_margin >= 30 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($item->profit_margin, 1) }}%
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">No data available</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200 font-semibold">
                    <tr>
                        <td class="px-4 py-3 text-right">TOTALS:</td>
                        <td class="px-4 py-3 text-center">{{ number_format($summary->total_quantity_sold, 0) }}</td>
                        <td class="px-4 py-3 text-right">UGX {{ number_format($summary->total_sales_amount, 0) }}</td>
                        <td class="px-4 py-3 text-right text-red-600">UGX {{ number_format($summary->total_cogs, 0) }}</td>
                        <td class="px-4 py-3 text-right text-green-600">UGX {{ number_format($summary->total_profit, 0) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($summary->profit_margin, 1) }}%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Signatures Section --}}
    <div class="mt-6 pt-4 border-t-2 border-dashed border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Prepared By Signature --}}
            <div class="text-center p-4 bg-gray-50 rounded-xl border border-gray-200">
                <i class="fas fa-user-check text-gray-400 text-2xl mb-2"></i>
                <div class="border-t border-gray-300 w-32 mx-auto my-3"></div>
                <div class="font-semibold text-gray-800 text-sm">{{ Auth::user()->first_name ?? 'N/A' }} {{ Auth::user()->last_name ?? '' }}</div>
                <div class="text-xs text-gray-500">Prepared By</div>
                <div class="text-xs text-gray-400 mt-1">{{ now()->format('F d, Y') }}</div>
            </div>

            {{-- Approved By Signature --}}
            <div class="text-center p-4 bg-gray-50 rounded-xl border border-gray-200">
                <i class="fas fa-check-circle text-gray-400 text-2xl mb-2"></i>
                <div class="border-t border-gray-300 w-32 mx-auto my-3"></div>
                <div class="border-b border-gray-400 w-32 mx-auto mb-2" style="border-bottom: 1px solid #9ca3af; min-height: 30px;"></div>
                <div class="text-xs text-gray-400 italic mb-2">(Sign here)</div>
                <div class="font-semibold text-gray-800 text-sm">_________________________</div>
                <div class="text-xs text-gray-500">Approved By (Management)</div>
                <div class="text-xs text-gray-400 mt-1">Date: _____________</div>
            </div>
        </div>
    </div>

    {{-- Status Badge --}}
    @if($summary->status === 'finalized')
    <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
        <i class="fas fa-check-circle text-green-600 mr-1"></i>
        <span class="text-sm text-green-700">This report has been finalized and approved.</span>
    </div>
    @else
    <div class="flex justify-end gap-3 no-print">
        <a href="{{ route('management.performance.summaries.finalize', $summary->id) }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2"
           onclick="return confirm('Finalize this performance summary? This action cannot be undone.')">
            <i class="fas fa-check-circle"></i> Finalize Report
        </a>
        <a href="{{ route('management.performance.summaries.regenerate', $summary->id) }}"
           class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
            <i class="fas fa-sync-alt"></i> Regenerate
        </a>
    </div>
    @endif
</div>
@endsection
