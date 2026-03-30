@extends('layouts.app')
@section('title', 'Vehicle Makes & Models')
@section('page-title', 'Vehicle Makes & Models')
@section('breadcrumb', 'Admin / Makes & Models')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Left: Add Make + Makes List --}}
    <div class="xl:col-span-1 space-y-6">

        {{-- Add New Make --}}
        <div class="card p-6">
            <h3 class="font-condensed font-bold text-base text-white mb-4">Add New Make</h3>
            <form method="POST" action="{{ route('admin.makes.store') }}" class="flex gap-2">
                @csrf
                <input type="text" name="name" placeholder="e.g. Foton, JAC, BYD…" class="form-input" required>
                <button type="submit" class="btn-primary whitespace-nowrap">Add</button>
            </form>
        </div>

        {{-- Makes List --}}
        <div class="card">
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <h3 class="font-condensed font-bold text-base text-white">All Makes</h3>
                <span class="text-xs text-slate-500">{{ $makes->count() }} total</span>
            </div>
            <div class="divide-y divide-slate-800">
                @forelse($makes as $make)
                <div class="px-6 py-3 flex items-center gap-3 group" id="make-{{ $make->id }}">
                    {{-- View mode --}}
                    <div class="flex-1 flex items-center justify-between view-mode-{{ $make->id }}">
                        <div>
                            <button onclick="selectMake({{ $make->id }}, '{{ addslashes($make->name) }}')"
                                class="text-sm font-semibold text-left transition-colors hover:text-brand-400
                                {{ request()->get('make') == $make->id ? 'text-brand-400' : 'text-white' }}">
                                {{ $make->name }}
                            </button>
                            <p class="text-xs text-slate-600">{{ $make->models_count }} model(s)</p>
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="toggleEdit({{ $make->id }})" class="p-1.5 rounded hover:bg-slate-700 text-slate-500 hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('admin.makes.destroy', $make) }}" onsubmit="return confirm('Delete {{ $make->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded hover:bg-red-500/20 text-slate-500 hover:text-red-400 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    {{-- Edit mode --}}
                    <form method="POST" action="{{ route('admin.makes.update', $make) }}" class="flex-1 hidden edit-mode-{{ $make->id }} flex gap-2">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $make->name }}" class="form-input text-sm py-1.5" required>
                        <button type="submit" class="btn-primary text-xs py-1.5 px-3">Save</button>
                        <button type="button" onclick="toggleEdit({{ $make->id }})" class="btn-secondary text-xs py-1.5 px-2">✕</button>
                    </form>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-slate-600 text-sm">No makes yet. Add one above.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right: Models Panel --}}
    <div class="xl:col-span-2 card" id="models-panel">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-condensed font-bold text-base text-white" id="models-panel-title">
                Select a make to manage its models →
            </h3>
        </div>

        {{-- Add model form (hidden until make selected) --}}
        <div id="add-model-form" class="px-6 py-4 border-b border-slate-800 hidden">
            <form method="POST" id="model-store-form" class="flex gap-2">
                @csrf
                <input type="text" name="name" id="new-model-name" placeholder="e.g. Ranger, Transit, BJ40…" class="form-input" required>
                <button type="submit" class="btn-primary whitespace-nowrap">Add Model</button>
            </form>
        </div>

        {{-- Models list (populated via JS / page reload) --}}
        <div id="models-list">
            @if(request()->has('make'))
                @php $selectedMake = $makes->firstWhere('id', request('make')); @endphp
                @if($selectedMake)
                    @php $models = \App\Models\VehicleModel::where('vehicle_make_id', $selectedMake->id)->orderBy('name')->get(); @endphp
                    <div class="divide-y divide-slate-800">
                        @forelse($models as $model)
                        <div class="px-6 py-3 flex items-center gap-3 group">
                            <div class="flex-1 flex items-center justify-between view-model-{{ $model->id }}">
                                <span class="text-sm text-white">{{ $model->name }}</span>
                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick="toggleEditModel({{ $model->id }})" class="p-1.5 rounded hover:bg-slate-700 text-slate-500 hover:text-white transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.models.destroy', [$selectedMake, $model]) }}" onsubmit="return confirm('Delete {{ $model->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded hover:bg-red-500/20 text-slate-500 hover:text-red-400 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.models.update', [$selectedMake, $model]) }}" class="flex-1 hidden edit-model-{{ $model->id }} flex gap-2">
                                @csrf @method('PUT')
                                <input type="text" name="name" value="{{ $model->name }}" class="form-input text-sm py-1.5" required>
                                <button type="submit" class="btn-primary text-xs py-1.5 px-3">Save</button>
                                <button type="button" onclick="toggleEditModel({{ $model->id }})" class="btn-secondary text-xs py-1.5 px-2">✕</button>
                            </form>
                        </div>
                        @empty
                        <div class="px-6 py-8 text-center text-slate-600 text-sm">No models yet for {{ $selectedMake->name }}. Add one above.</div>
                        @endforelse
                    </div>
                @endif
            @else
            <div class="px-6 py-16 text-center">
                <svg class="w-12 h-12 text-slate-800 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10l2-2zM13 6l3 4h3a1 1 0 011 1v4l-2 2h-1"/></svg>
                <p class="text-slate-600 text-sm">Click on a make on the left to view and manage its models.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectMake(makeId, makeName) {
    window.location.href = '{{ route('admin.makes.index') }}?make=' + makeId;
}

function toggleEdit(makeId) {
    document.querySelector('.view-mode-' + makeId).classList.toggle('hidden');
    document.querySelector('.edit-mode-' + makeId).classList.toggle('hidden');
}

function toggleEditModel(modelId) {
    document.querySelector('.view-model-' + modelId).classList.toggle('hidden');
    document.querySelector('.edit-model-' + modelId).classList.toggle('hidden');
}

// Set up add-model form action based on selected make
@if(request()->has('make'))
    document.getElementById('add-model-form').classList.remove('hidden');
    document.getElementById('model-store-form').action = '{{ route('admin.models.store', request('make')) }}';
    document.getElementById('models-panel-title').textContent = 'Models for {{ $selectedMake->name ?? '' }}';
    // Highlight selected make
    document.querySelectorAll('[onclick^="selectMake"]').forEach(btn => {
        if (btn.getAttribute('onclick').includes('{{ request('make') }}')) {
            btn.classList.add('text-brand-400');
        }
    });
@endif
</script>
@endpush
