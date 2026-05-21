{{-- resources/views/management/menus/index.blade.php --}}

@extends('layouts.management')

@section('title', 'Manage Menus')

@section('page-title', 'Menu Management')

@section('content')
<style>
    /* ============================================
       STATUS BADGES
    ============================================ */
    .status-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .status-active {
        background: #d1fae5;
        color: #065f46;
    }
    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    /* ============================================
       TABLES
    ============================================ */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.75rem;
    }
    .data-table th {
        background: #f8fafc;
        padding: 0.75rem;
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

    /* ============================================
       TEXT ALIGNMENT
    ============================================ */
    .text-left {
        text-align: left;
    }
    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
    }

    /* ============================================
       ACTION BUTTONS - HIDDEN BY DEFAULT, SHOW ON HOVER
    ============================================ */
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
    }

    .data-table tr:hover .action-buttons {
        opacity: 1;
        visibility: visible;
    }

    /* For browsers that don't support opacity/visibility transition well */
    .action-buttons.force-visible {
        opacity: 1;
        visibility: visible;
    }

    /* ============================================
       BUTTONS
    ============================================ */
    .btn-view {
        background: #3b82f6;
        color: white;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.7rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: background 0.2s;
    }
    .btn-view:hover {
        background: #2563eb;
    }
    .btn-edit {
        background: #f59e0b;
        color: white;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.7rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: background 0.2s;
    }
    .btn-edit:hover {
        background: #d97706;
    }
    .btn-delete {
        background: #ef4444;
        color: white;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.7rem;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: background 0.2s;
    }
    .btn-delete:hover {
        background: #dc2626;
    }
    .btn-create {
        background: #10b981;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.2s;
    }
    .btn-create:hover {
        background: #059669;
    }
    .btn-toggle {
        background: #8b5cf6;
        color: white;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.7rem;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: background 0.2s;
    }
    .btn-toggle:hover {
        background: #7c3aed;
    }

    /* ============================================
       HOVER HINT
    ============================================ */
    .hover-hint {
        display: inline-block;
        margin-left: 0.5rem;
        font-size: 0.65rem;
        color: #9ca3af;
        cursor: help;
    }

    /* Optional: Show a subtle indicator that actions are available on hover */
    .data-table td:last-child {
        position: relative;
    }

    .data-table tr:not(:hover) td:last-child::after {
        content: "↳ Hover for actions";
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.6rem;
        color: #cbd5e1;
        font-style: italic;
        white-space: nowrap;
    }

    /* Hide the hint on smaller screens where hover might not work well */
    @media (max-width: 768px) {
        .data-table tr:not(:hover) td:last-child::after {
            display: none;
        }
        .action-buttons {
            opacity: 1;
            visibility: visible;
        }
    }
</style>

<div class="space-y-6">

    {{-- ============================================
         HEADER SECTION
    ============================================ --}}
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl p-5 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">
                    <i class="fas fa-utensils mr-2"></i>
                    Menu Management
                </h2>
                <p class="text-emerald-100 mt-1">Create and manage menus for different departments</p>
            </div>
            <a href="{{ route('management.menus.create') }}" class="btn-create">
                <i class="fas fa-plus mr-1"></i> Create New Menu
            </a>
        </div>
    </div>

    {{-- ============================================
         MENUS TABLE
    ============================================ --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-left">#</th>
                        <th class="text-left">Menu Name</th>
                        <th class="text-left">Department</th>
                        <th class="text-left">Description</th>
                        <th class="text-center">Items</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">
                            Actions
                            <span class="hover-hint" title="Actions appear when you hover over a row">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @forelse($menus as $menu)
                    <tr class="menu-row">
                        <td class="text-left">{{ $counter++ }}</td>
                        <td class="text-left font-medium text-gray-800">{{ $menu->name }}</td>
                        <td class="text-left">{{ $menu->department->name ?? 'N/A' }}</td>
                        <td class="text-left text-gray-500">{{ \Str::limit($menu->description, 50) ?? '—' }}</td>
                        <td class="text-center">
                            <span class="bg-gray-100 px-2 py-1 rounded-full text-xs">
                                {{ $menu->items_count }} items
                            </span>
                        </td>
                        <td class="text-center">
                            @if($menu->is_active)
                                <span class="status-badge status-active">Active</span>
                            @else
                                <span class="status-badge status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <a href="{{ route('management.menus.show', $menu->id) }}" class="btn-view">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('management.menus.edit', $menu->id) }}" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" onclick="toggleStatus({{ $menu->id }})" class="btn-toggle">
                                    <i class="fas fa-power-off"></i>
                                    {{ $menu->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                @if($menu->items_count == 0)
                                <form method="POST" action="{{ route('management.menus.destroy', $menu->id) }}"
                                      class="inline-form"
                                      onsubmit="return confirm('Delete this menu? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="7" class="text-center py-8 text-gray-400">
                            <i class="fas fa-utensils text-4xl mb-2 block"></i>
                            No menus found. Click "Create New Menu" to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ============================================
     SCRIPTS
============================================ --}}
<script>
    /**
     * Toggle menu status (Activate/Deactivate)
     * @param {number} id - The menu ID
     */
    function toggleStatus(id) {
        const token = document.querySelector('meta[name="csrf-token"]');

        if (!token) {
            console.error('CSRF token not found');
            alert('Error: Security token not found. Please refresh the page.');
            return;
        }

        fetch(`/management/menus/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token.content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            console.error('Toggle status error:', error);
            alert('Error: Unable to update menu status. Please try again.');
        });
    }

    // Optional: For touch devices, ensure actions are visible
    if ('ontouchstart' in window || navigator.maxTouchPoints) {
        // On touch devices, always show actions
        document.querySelectorAll('.action-buttons').forEach(buttons => {
            buttons.classList.add('force-visible');
        });
    }
</script>
@endsection
