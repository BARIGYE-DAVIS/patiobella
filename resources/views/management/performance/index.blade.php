{{-- resources/views/management/performance/index.blade.php --}}

@extends('layouts.management')

@section('title', 'Performance Reports')
@section('page-title', 'Performance Reports')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Stock Take Records</h2>
                <p class="text-sm text-gray-500 mt-1">Track department stock takes and performance</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('management.performance.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> New Stock Take
                </a>
                <a href="{{ route('management.performance.summaries.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-chart-bar"></i> View Summaries
                </a>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('management.performance.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Department</label>
                <select name="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition w-full">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Info Message --}}
    <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-yellow-600 mr-3"></i>
            <p class="text-sm text-yellow-700">
                Stock take records are saved. You can view them here once implemented.
                <br>To record a new stock take, click <strong>"New Stock Take"</strong> above.
            </p>
        </div>
    </div>

</div>
@endsection
