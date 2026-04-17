{{-- resources/views/departments/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Department: ' . $department->name)

@section('content')
<div class="min-h-screen bg-slate-50 p-6">

    {{-- Page Header --}}
    <div class="mb-8 flex items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('departments.index') }}"
               class="flex items-center justify-center w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition shadow-sm flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $department->name }}</h1>
                </div>
                <p class="mt-0.5 text-sm text-slate-500">
                    <span class="font-mono text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded text-xs">{{ $department->code }}</span>
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('departments.edit', $department) }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-medium text-amber-700 hover:bg-amber-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>

            <form action="{{ route('departments.destroy', $department) }}" method="POST" class="inline" onsubmit="return confirmDelete()">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-medium text-red-700 hover:bg-red-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Basic Information --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-indigo-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                        </svg>
                    </span>
                    <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Department Information</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    <div class="flex px-6 py-3.5">
                        <span class="w-32 text-sm text-slate-500 flex-shrink-0">Department Code</span>
                        <span class="font-mono text-sm font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $department->code }}</span>
                    </div>
                    <div class="flex px-6 py-3.5">
                        <span class="w-32 text-sm text-slate-500 flex-shrink-0">Department Name</span>
                        <span class="text-sm font-semibold text-slate-800">{{ $department->name }}</span>
                    </div>
                    <div class="flex px-6 py-3.5">
                        <span class="w-32 text-sm text-slate-500 flex-shrink-0">Location</span>
                        <span class="text-sm text-slate-700">{{ $department->Location ?? '—' }}</span>
                    </div>
                    <div class="flex px-6 py-3.5">
                        <span class="w-32 text-sm text-slate-500 flex-shrink-0">Description</span>
                        <span class="text-sm text-slate-700">{{ $department->Description ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Audit Info --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-slate-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Audit Trail</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    <div class="px-5 py-3.5">
                        <p class="text-xs font-medium text-slate-400 mb-1">Created By</p>
                        @if($department->createdBy)
                            <p class="text-sm font-medium text-slate-700">{{ $department->createdBy->first_name }} {{ $department->createdBy->last_name }}</p>
                            <p class="text-xs text-slate-400">{{ $department->created_at?->format('M d, Y H:i') ?? 'N/A' }}</p>
                        @else
                            <p class="text-sm text-slate-400 italic">Unknown</p>
                        @endif
                    </div>
                    <div class="px-5 py-3.5">
                        <p class="text-xs font-medium text-slate-400 mb-1">Last Updated By</p>
                        @if($department->updatedBy)
                            <p class="text-sm font-medium text-slate-700">{{ $department->updatedBy->first_name }} {{ $department->updatedBy->last_name }}</p>
                            <p class="text-xs text-slate-400">{{ $department->updated_at?->format('M d, Y H:i') ?? 'N/A' }}</p>
                        @else
                            <p class="text-sm text-slate-400 italic">Not updated yet</p>
                        @endif
                    </div>
                    @if($department->deleted_at)
                    <div class="px-5 py-3.5 bg-red-50">
                        <p class="text-xs font-medium text-red-400 mb-1">Deleted At</p>
                        <p class="text-sm font-medium text-red-700">{{ $department->deleted_at->format('M d, Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-red-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-red-100 bg-red-50">
                    <h2 class="text-sm font-semibold text-red-700 uppercase tracking-wider">Danger Zone</h2>
                </div>
                <div class="p-5">
                    <p class="text-xs text-slate-500 mb-4">Deleting this department is reversible by an administrator. Proceed with caution.</p>
                    <form action="{{ route('departments.destroy', $department) }}" method="POST" onsubmit="return confirmDelete()">
                        @csrf 
                        @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-500 transition-colors duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Department
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this department? This action can be undone by an administrator.');
    }
</script>
@endsection