{{-- resources/views/management/menus/edit.blade.php --}}

@extends('layouts.management')

@section('title', 'Edit Menu')

@section('page-title', 'Edit Menu: ' . $menu->name)

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
            <h1 class="text-2xl font-bold text-gray-900">Edit Menu: {{ $menu->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Edit menu details. Manage dishes from the menu items page.</p>
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
            <form method="POST" action="{{ route('management.menus.update', $menu->id) }}" id="mainForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Menu Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                               value="{{ old('name', $menu->name) }}" required>
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <select name="department_id"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                                required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $menu->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                        <textarea name="description" rows="2"
                                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition resize-none"
                                  placeholder="Brief description of this menu...">{{ old('description', $menu->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sort Order</label>
                        <input type="number" name="sort_order"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                               value="{{ old('sort_order', $menu->sort_order) }}">
                        <p class="text-gray-400 text-xs mt-1">Lower numbers appear first</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                        <div class="flex items-center gap-6 mt-2">
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', $menu->is_active ? '1' : '0') == '1' ? 'checked' : '' }} class="accent-orange-500">
                                <span>Active</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="radio" name="is_active" value="0" {{ old('is_active', $menu->is_active ? '1' : '0') == '0' ? 'checked' : '' }} class="accent-orange-500">
                                <span>Inactive</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-between items-center gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" id="deleteMenuBtn"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-red-500 border border-red-500 rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-trash text-xs"></i> Delete Menu
                    </button>
                    <div class="flex gap-3">
                        <a href="{{ route('management.menus.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times text-xs"></i> Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                            <i class="fas fa-save"></i> Update Menu
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-red-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-red-500 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Delete Menu</h3>
                    <p class="text-sm text-gray-500">This action cannot be undone</p>
                </div>
            </div>
            <button type="button" class="closeDeleteModal text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">
                Are you sure you want to delete menu <strong>{{ $menu->name }}</strong>?
            </p>
            <p class="text-xs text-red-500 mb-4">
                <i class="fas fa-warning mr-1"></i>
                This will also delete all menu items and recipes under this menu. This action cannot be undone.
            </p>

            <form id="deleteForm" method="POST" action="{{ route('management.menus.destroy', $menu->id) }}">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-3">
                    <button type="button" class="closeDeleteModal px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-lg transition-colors">
                        <i class="fas fa-trash mr-1"></i> Delete Menu
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
    $('#deleteMenuBtn').on('click', function() {
        $('#deleteModal').removeClass('hidden').addClass('flex');
    });

    $('.closeDeleteModal').on('click', function() {
        $('#deleteModal').addClass('hidden').removeClass('flex');
    });

    $(window).on('click', function(e) {
        if ($(e.target).is('#deleteModal')) {
            $('#deleteModal').addClass('hidden').removeClass('flex');
        }
    });
});
</script>
@endpush
