@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="min-h-screen bg-slate-50 p-6">

    {{-- Page Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Departments</h1>
            <p class="mt-1 text-sm text-slate-500">Manage all departments across your organization</p>
        </div>
        <a href="{{ route('departments.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors duration-150">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Department
        </a>
    </div>

    {{-- Filter Card --}}
    <div class="mb-6 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <form method="GET" action="{{ route('departments.index') }}">
            <div class="flex flex-wrap gap-3">
                <div class="relative flex-1 min-w-[200px]">
                    <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text" name="search"
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-4 text-sm text-slate-700 placeholder-slate-400 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 transition"
                           placeholder="Search by code, name or location..."
                           value="{{ request('search') }}">
                </div>

                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500 transition-colors duration-150">
                    Filter
                </button>
                <a href="{{ route('departments.index') }}"
                   class="rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors duration-150">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50/70">
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-600 text-xs uppercase tracking-wider">Code</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-600 text-xs uppercase tracking-wider">Name</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-600 text-xs uppercase tracking-wider">Location</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-600 text-xs uppercase tracking-wider">Description</th>
                    <th class="px-5 py-3.5 text-center font-semibold text-slate-600 text-xs uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($departments as $department)
                    <tr class="hover:bg-slate-50/60 transition-colors duration-100">
                        <td class="px-5 py-4">
                            <span class="font-mono text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md">
                                {{ $department->code }}
                            </span>
                        </td>
                        <td class="px-5 py-4 font-medium text-slate-800">{{ $department->name }}</td>
                        <td class="px-5 py-4 text-slate-600">
                            {{ $department->Location ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-slate-600 max-w-xs truncate">
                            {{ $department->Description ?? '—' }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('departments.show', $department) }}"
                                   class="rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 ring-1 ring-slate-200 hover:bg-slate-100 transition">
                                    View
                                </a>
                                <a href="{{ route('departments.edit', $department) }}"
                                   class="rounded-lg px-3 py-1.5 text-xs font-medium text-amber-700 ring-1 ring-amber-200 bg-amber-50 hover:bg-amber-100 transition">
                                    Edit
                                </a>
                                <button type="button" onclick="confirmDelete({{ $department->id }})"
                                        class="rounded-lg px-3 py-1.5 text-xs font-medium text-red-700 ring-1 ring-red-200 bg-red-50 hover:bg-red-100 transition">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h6M9 11h6M9 15h4"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-slate-500 font-medium">No departments found</p>
                                <a href="{{ route('departments.create') }}" class="text-sm text-indigo-600 hover:underline">Create your first department</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($departments->hasPages())
        <div class="border-t border-slate-200 px-5 py-4 bg-slate-50/50">
            {{ $departments->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Delete Form --}}
<form id="delete-form" action="" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this department?')) {
            const form = document.getElementById('delete-form');
            form.action = '/departments/' + id;
            form.submit();
        }
    }
</script>
@endsection