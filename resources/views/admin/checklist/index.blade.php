@extends('layouts.app')
@section('title', 'Service Checklists')
@section('page-title', 'Service Checklist Templates')
@section('breadcrumb', 'Admin / Checklists')

@section('content')

<div class="mb-6 p-4 rounded-xl border border-blue-500/20 bg-blue-500/5 text-sm text-blue-300">
    <strong>ℹ How this works:</strong> These templates define the checklist items automatically generated when a new service is opened. Changes here apply to <em>future</em> services only — existing service records are not affected.
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Regular Service --}}
    <div class="card">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-condensed font-bold text-base text-white">Regular Service</h3>
                <p class="text-xs text-slate-500 mt-0.5">Every 5,000 km or 90 days — {{ $regularItems->count() }} items</p>
            </div>
            <span class="badge-regular">Regular</span>
        </div>

        {{-- Add item --}}
        <form method="POST" action="{{ route('admin.checklist.store') }}" class="px-6 py-4 border-b border-slate-800 flex gap-2">
            @csrf
            <input type="hidden" name="service_type" value="Regular">
            <input type="text" name="item_name" placeholder="Add new checklist item…" class="form-input text-sm" required>
            <button type="submit" class="btn-primary text-xs whitespace-nowrap px-3">+ Add</button>
        </form>

        {{-- Items list --}}
        <ul id="regular-list" class="divide-y divide-slate-800">
            @forelse($regularItems as $item)
            <li class="px-6 py-3 flex items-center gap-3 group checklist-item" data-id="{{ $item->id }}">
                {{-- Drag handle --}}
                <span class="cursor-grab text-slate-700 hover:text-slate-500 transition-colors drag-handle select-none" title="Drag to reorder">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 100-4 2 2 0 000 4zM16 6a2 2 0 100-4 2 2 0 000 4zM8 14a2 2 0 100-4 2 2 0 000 4zM16 14a2 2 0 100-4 2 2 0 000 4zM8 22a2 2 0 100-4 2 2 0 000 4zM16 22a2 2 0 100-4 2 2 0 000 4z"/></svg>
                </span>

                {{-- View mode --}}
                <span class="flex-1 text-sm text-white view-item-{{ $item->id }}">{{ $item->item_name }}</span>

                {{-- Edit mode --}}
                <form method="POST" action="{{ route('admin.checklist.update', $item) }}" class="flex-1 hidden edit-item-{{ $item->id }} flex gap-2">
                    @csrf @method('PUT')
                    <input type="text" name="item_name" value="{{ $item->item_name }}" class="form-input text-sm py-1" required>
                    <button type="submit" class="btn-primary text-xs py-1 px-2">Save</button>
                    <button type="button" onclick="toggleEditItem({{ $item->id }})" class="btn-secondary text-xs py-1 px-2">✕</button>
                </form>

                {{-- Action buttons --}}
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity view-item-{{ $item->id }}">
                    <button onclick="toggleEditItem({{ $item->id }})" class="p-1.5 rounded hover:bg-slate-700 text-slate-500 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form method="POST" action="{{ route('admin.checklist.destroy', $item) }}" onsubmit="return confirm('Remove this checklist item?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded hover:bg-red-500/20 text-slate-500 hover:text-red-400 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </li>
            @empty
            <li class="px-6 py-8 text-center text-slate-600 text-sm">No items yet. Add one above.</li>
            @endforelse
        </ul>
    </div>

    {{-- Full Service --}}
    <div class="card">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-condensed font-bold text-base text-white">Full Service</h3>
                <p class="text-xs text-slate-500 mt-0.5">Every 10,000 km or 180 days — {{ $fullItems->count() }} items</p>
            </div>
            <span class="badge-full">Full</span>
        </div>

        {{-- Add item --}}
        <form method="POST" action="{{ route('admin.checklist.store') }}" class="px-6 py-4 border-b border-slate-800 flex gap-2">
            @csrf
            <input type="hidden" name="service_type" value="Full">
            <input type="text" name="item_name" placeholder="Add new checklist item…" class="form-input text-sm" required>
            <button type="submit" class="btn-primary text-xs whitespace-nowrap px-3">+ Add</button>
        </form>

        {{-- Items list --}}
        <ul id="full-list" class="divide-y divide-slate-800">
            @forelse($fullItems as $item)
            <li class="px-6 py-3 flex items-center gap-3 group checklist-item" data-id="{{ $item->id }}">
                <span class="cursor-grab text-slate-700 hover:text-slate-500 transition-colors drag-handle select-none" title="Drag to reorder">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 100-4 2 2 0 000 4zM16 6a2 2 0 100-4 2 2 0 000 4zM8 14a2 2 0 100-4 2 2 0 000 4zM16 14a2 2 0 100-4 2 2 0 000 4zM8 22a2 2 0 100-4 2 2 0 000 4zM16 22a2 2 0 100-4 2 2 0 000 4z"/></svg>
                </span>

                <span class="flex-1 text-sm text-white view-item-{{ $item->id }}">{{ $item->item_name }}</span>

                <form method="POST" action="{{ route('admin.checklist.update', $item) }}" class="flex-1 hidden edit-item-{{ $item->id }} flex gap-2">
                    @csrf @method('PUT')
                    <input type="text" name="item_name" value="{{ $item->item_name }}" class="form-input text-sm py-1" required>
                    <button type="submit" class="btn-primary text-xs py-1 px-2">Save</button>
                    <button type="button" onclick="toggleEditItem({{ $item->id }})" class="btn-secondary text-xs py-1 px-2">✕</button>
                </form>

                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity view-item-{{ $item->id }}">
                    <button onclick="toggleEditItem({{ $item->id }})" class="p-1.5 rounded hover:bg-slate-700 text-slate-500 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form method="POST" action="{{ route('admin.checklist.destroy', $item) }}" onsubmit="return confirm('Remove this checklist item?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded hover:bg-red-500/20 text-slate-500 hover:text-red-400 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </li>
            @empty
            <li class="px-6 py-8 text-center text-slate-600 text-sm">No items yet. Add one above.</li>
            @endforelse
        </ul>
    </div>

</div>
@endsection

@push('scripts')
<script>
function toggleEditItem(id) {
    document.querySelector('.view-item-' + id).classList.toggle('hidden');
    document.querySelector('.edit-item-' + id).classList.toggle('hidden');
    // Also toggle action buttons
    const actions = document.querySelectorAll('.view-item-' + id + ' ~ div, .view-item-' + id);
    document.querySelectorAll('.view-item-' + id).forEach(el => el.classList.toggle('hidden'));
}

// ── Drag-to-reorder ───────────────────────────────────────────────────────────
function initSortable(listId) {
    const list = document.getElementById(listId);
    if (!list) return;

    let dragSrc = null;

    list.querySelectorAll('.checklist-item').forEach(item => {
        const handle = item.querySelector('.drag-handle');

        handle.addEventListener('mousedown', () => { item.draggable = true; });
        handle.addEventListener('mouseup',   () => { item.draggable = false; });

        item.addEventListener('dragstart', function(e) {
            dragSrc = this;
            e.dataTransfer.effectAllowed = 'move';
            setTimeout(() => this.style.opacity = '0.4', 0);
        });

        item.addEventListener('dragend', function() {
            this.style.opacity = '';
            this.draggable = false;
            saveOrder(listId);
        });

        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            if (this !== dragSrc) {
                const rect  = this.getBoundingClientRect();
                const after = (e.clientY - rect.top) > rect.height / 2;
                list.insertBefore(dragSrc, after ? this.nextSibling : this);
            }
        });
    });
}

function saveOrder(listId) {
    const list  = document.getElementById(listId);
    const items = Array.from(list.querySelectorAll('.checklist-item')).map(el => el.dataset.id);

    fetch('{{ route('admin.checklist.reorder') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ items }),
    });
}

initSortable('regular-list');
initSortable('full-list');
</script>
@endpush
