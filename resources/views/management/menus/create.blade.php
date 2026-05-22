{{-- resources/views/management/menus/create.blade.php --}}

@extends('layouts.management')

@section('title', 'Create Menu')

@section('page-title', 'Create New Menu')

@section('content')
<div class="max-w-4xl mx-auto px-4 pb-16">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Menu</h1>
            <p class="text-sm text-gray-500 mt-0.5">Define menu details. You can add dishes later.</p>
        </div>
        <div>
            <a href="{{ route('management.menus.index') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Back to Menus
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gray-50">
            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-orange-100 text-orange-600 text-xs font-bold">1</span>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Menu Details</h2>
                <p class="text-xs text-gray-400">Basic information about this menu</p>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('management.menus.store') }}" id="mainForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Menu Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="field_name"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                               value="{{ old('name') }}" placeholder="e.g., Breakfast Menu, Wine List" required>
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <select name="department_id" id="field_department_id"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                                required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                        <textarea name="description" id="field_description" rows="2"
                                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition resize-none"
                                  placeholder="Brief description of this menu...">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sort Order</label>
                        <input type="number" name="sort_order" id="field_sort_order"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                               value="{{ old('sort_order', 0) }}">
                        <p class="text-gray-400 text-xs mt-1">Lower numbers appear first</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                        <div class="flex items-center gap-6 mt-2">
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="accent-orange-500">
                                <span>Active</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }} class="accent-orange-500">
                                <span>Inactive</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <a href="{{ route('management.menus.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times text-xs"></i> Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-save"></i> Create Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const DRAFT_KEY = 'menu_create_draft_v2';

    function saveDraft() {
        let draft = {
            name: $('#field_name').val(),
            department_id: $('#field_department_id').val(),
            description: $('#field_description').val(),
            sort_order: $('#field_sort_order').val(),
            is_active: $('input[name="is_active"]:checked').val() || '1'
        };
        localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
    }

    function loadDraft() {
        let draft = localStorage.getItem(DRAFT_KEY);
        if (draft) {
            let data = JSON.parse(draft);
            if (!$('#field_name').val() && data.name) $('#field_name').val(data.name);
            if (!$('#field_department_id').val() && data.department_id) $('#field_department_id').val(data.department_id);
            if (!$('#field_description').val() && data.description) $('#field_description').val(data.description);
            if (data.sort_order !== undefined) $('#field_sort_order').val(data.sort_order);
            if (data.is_active !== undefined) $(`input[name="is_active"][value="${data.is_active}"]`).prop('checked', true);
        }
    }

    function clearDraft() {
        localStorage.removeItem(DRAFT_KEY);
    }

    $('#field_name, #field_description, #field_sort_order, #field_department_id').on('change keyup', saveDraft);
    $('input[name="is_active"]').on('change', saveDraft);

    loadDraft();

    $('#mainForm').on('submit', function() {
        clearDraft();
    });
});
</script>
@endpush
