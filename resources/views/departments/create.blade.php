{{-- resources/views/departments/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Department')

@section('content')
<div class="min-h-screen bg-slate-50 p-6">

    {{-- Page Header --}}
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('departments.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Create Department</h1>
            <p class="mt-0.5 text-sm text-slate-500">Add a new department to your organization</p>
        </div>
    </div>

    <form action="{{ route('departments.store') }}" method="POST">
        @csrf
        
        {{-- Display Validation Errors --}}
        @if($errors->any())
            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-red-700">
                        <strong class="block mb-1">Please fix the following errors:</strong>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Department Information --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-sm font-semibold text-slate-800 uppercase tracking-wider mb-5 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-indigo-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </span>
                        Department Details
                    </h2>

                    <div class="space-y-5">
                        {{-- Code --}}
                        <div>
                            <label for="code" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Department Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="code" name="code"
                                   value="{{ old('code') }}"
                                   placeholder="e.g., KIT-001, BAR-002, MKT-001"
                                   class="w-full rounded-xl border @error('code') border-red-400 bg-red-50 @else border-slate-200 bg-slate-50 @enderror px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 transition"
                                   required>
                            @error('code')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-slate-400">Unique identifier for the department</p>
                        </div>

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Department Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name"
                                   value="{{ old('name') }}"
                                   placeholder="e.g., Main Kitchen, Bar Section, Marketing"
                                   class="w-full rounded-xl border @error('name') border-red-400 bg-red-50 @else border-slate-200 bg-slate-50 @enderror px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 transition"
                                   required>
                            @error('name')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Location --}}
                        <div>
                            <label for="location" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Location
                            </label>
                            <input type="text" id="Location" name="Location"
                                   value="{{ old('Location') }}"
                                   placeholder="e.g., 2nd Floor, Building A, Downtown Branch"
                                   class="w-full rounded-xl border @error('Location') border-red-400 bg-red-50 @else border-slate-200 bg-slate-50 @enderror px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 transition">
                            @error('Location')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-slate-400">Physical location or address of the department</p>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Description
                            </label>
                            <textarea id="Description" name="Description" rows="4"
                                      placeholder="Brief Description of the department and its responsibilities..."
                                      class="w-full rounded-xl border @error('Description') border-red-400 bg-red-50 @else border-slate-200 bg-slate-50 @enderror px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 transition resize-none">{{ old('Description') }}</textarea>
                            @error('Description')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Actions --}}
            <div class="space-y-4">
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sticky top-6">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Actions</h3>
                    <div class="space-y-3">
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-500 transition-colors duration-150 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Create Department
                        </button>
                        <a href="{{ route('departments.index') }}"
                           class="w-full flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors duration-150">
                            Cancel
                        </a>
                    </div>
                    <div class="mt-5 pt-5 border-t border-slate-100">
                        <p class="text-xs text-slate-400 leading-relaxed">Fields marked with <span class="text-red-500 font-semibold">*</span> are required. The department code must be unique.</p>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection