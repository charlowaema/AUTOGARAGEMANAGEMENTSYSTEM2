@extends('layouts.app')
@section('title', 'Service Bay — ' . $service->garage_entry_no)
@section('page-title', 'Service Bay')
@section('breadcrumb', 'Services / ' . $service->garage_entry_no . ' / Bay')

@section('page-actions')
    <span class="{{ $service->service_type === 'Regular' ? 'badge-regular' : 'badge-full' }} text-sm px-3 py-1">{{ $service->service_type }} Service</span>
    <span class="badge-progress text-sm px-3 py-1">{{ $service->status }}</span>
@endsection

@section('content')
{{-- Vehicle Header --}}
<div class="card p-5 mb-6">
    <div class="flex flex-wrap items-center gap-6 text-sm">
        <div>
            <p class="text-xs text-slate-500 mb-0.5">Entry No.</p>
            <p class="font-mono font-bold text-brand-400">{{ $service->garage_entry_no }}</p>
        </div>
        <div>
            <p class="text-xs text-slate-500 mb-0.5">Vehicle</p>
            <p class="font-bold text-white">{{ $service->vehicle->plate_number }}</p>
            <p class="text-xs text-slate-500">{{ $service->vehicle->make->name }} {{ $service->vehicle->vehicleModel->name }}</p>
        </div>
        <div>
            <p class="text-xs text-slate-500 mb-0.5">Driver</p>
            <p class="font-semibold text-white">{{ $service->customer->name }}</p>
            <p class="text-xs text-slate-500">{{ $service->customer->phone }}</p>
        </div>
        <div>
            <p class="text-xs text-slate-500 mb-0.5">Mileage</p>
            <p class="font-mono font-bold text-white">{{ number_format($service->mileage_at_service) }} km</p>
        </div>
        <div>
            <p class="text-xs text-slate-500 mb-0.5">Service Date</p>
            <p class="text-white">{{ $service->service_date->format('d M Y') }}</p>
        </div>
        <div>
            <p class="text-xs text-slate-500 mb-0.5">Next Service</p>
            <p class="text-amber-400 font-semibold">{{ $service->next_service_date->format('d M Y') }}</p>
            <p class="text-xs text-slate-500">@ {{ number_format($service->next_service_mileage) }} km</p>
        </div>
        @if($service->notes)
        <div class="flex-1">
            <p class="text-xs text-slate-500 mb-0.5">Notes</p>
            <p class="text-slate-300 text-xs">{{ $service->notes }}</p>
        </div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
    {{-- Checklist (3/5) --}}
    <div class="xl:col-span-3 card">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-condensed font-bold text-base text-white">Service Checklist</h3>
            <div class="flex gap-2 text-xs">
                <span class="px-2 py-1 rounded bg-green-500/10 text-green-400 font-medium">Done: {{ $service->checklistItems->where('status','Done')->count() }}</span>
                <span class="px-2 py-1 rounded bg-slate-700 text-slate-400 font-medium">Total: {{ $service->checklistItems->count() }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('services.checklist.update', $service) }}">
            @csrf
            <div class="divide-y divide-slate-800">
                @foreach($service->checklistItems->sortBy('sort_order') as $i => $item)
                <div class="px-6 py-3 flex items-start gap-4">
                    <input type="hidden" name="checklist[{{ $i }}][id]" value="{{ $item->id }}">
                    <div class="flex-1">
                        <p class="text-sm text-white font-medium">{{ $item->item_name }}</p>
                        <input type="text" name="checklist[{{ $i }}][remarks]" value="{{ $item->remarks }}" placeholder="Remarks…" class="mt-1.5 w-full bg-slate-800 border border-slate-700 text-slate-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-brand-500">
                    </div>
                    <select name="checklist[{{ $i }}][status]" class="bg-slate-800 border border-slate-700 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500 {{ $item->status === 'Done' ? 'text-green-400' : ($item->status === 'N/A' ? 'text-slate-500' : 'text-amber-400') }}">
                        <option value="Pending" {{ $item->status === 'Pending' ? 'selected' : '' }} class="text-amber-400">⏳ Pending</option>
                        <option value="Done"    {{ $item->status === 'Done'    ? 'selected' : '' }} class="text-green-400">✅ Done</option>
                        <option value="N/A"     {{ $item->status === 'N/A'     ? 'selected' : '' }} class="text-slate-400">— N/A</option>
                    </select>
                </div>
                @endforeach
            </div>

            <div class="px-6 py-4 border-t border-slate-800">
                <button type="submit" class="btn-primary">Save Checklist</button>
            </div>
        </form>
    </div>

    {{-- Parts & Close Service (2/5) --}}
    <div class="xl:col-span-2 space-y-6">
        {{-- Parts Used --}}
        <div class="card">
            <div class="px-6 py-4 border-b border-slate-800">
                <h3 class="font-condensed font-bold text-base text-white">Parts Used</h3>
            </div>

            {{-- Add Part --}}
            <form method="POST" action="{{ route('services.parts.add', $service) }}" class="px-6 py-4 border-b border-slate-800 space-y-3">
                @csrf
                <div>
                    <label class="form-label">Select Part</label>
                    <select name="part_id" class="form-input text-sm" required>
                        <option value="">— Select Part —</option>
                        @foreach($parts as $part)
                            <option value="{{ $part->id }}">{{ $part->name }} ({{ $part->quantity_in_stock }} {{ $part->unit }} in stock) — KES {{ number_format($part->unit_price, 2) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity_used" value="1" min="1" class="form-input font-mono" required>
                </div>
                <button type="submit" class="btn-secondary w-full justify-center text-sm">Add Part</button>
            </form>

            {{-- Parts List --}}
            <div class="divide-y divide-slate-800">
                @forelse($service->serviceParts as $sp)
                <div class="px-6 py-3 flex items-center justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-white font-medium truncate">{{ $sp->part->name }}</p>
                        <p class="text-xs text-slate-500">{{ $sp->quantity_used }} × KES {{ number_format($sp->unit_price_at_service, 2) }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <p class="font-mono text-sm font-bold text-white">KES {{ number_format($sp->line_amount, 2) }}</p>
                        <form method="POST" action="{{ route('services.parts.remove', [$service, $sp]) }}" onsubmit="return confirm('Remove part?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="px-6 py-4 text-center text-slate-600 text-xs">No parts added yet.</div>
                @endforelse
            </div>

            @if($service->serviceParts->count())
            <div class="px-6 py-3 border-t border-slate-800 flex justify-between text-sm font-bold">
                <span class="text-slate-400">Parts Total</span>
                <span class="font-mono text-white">KES {{ number_format($service->total_parts_amount, 2) }}</span>
            </div>
            @endif
        </div>

        {{-- Close Service --}}
        @if($service->status !== 'Closed')
        <div class="card p-6">
            <h3 class="font-condensed font-bold text-base text-white mb-4">Close Service</h3>
            <form method="POST" action="{{ route('services.close', $service) }}" onsubmit="return confirm('Close this service record?')">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Labour Cost (KES)</label>
                    <input type="number" name="total_labour_cost" value="0" min="0" step="0.01" class="form-input font-mono" required>
                </div>
                <button type="submit" class="btn-primary w-full justify-center">Close & Print Report</button>
            </form>
        </div>
        @else
        <div class="card p-6 text-center">
            <span class="badge-closed text-sm px-4 py-2">Service Closed</span>
            <div class="mt-4">
                <a href="{{ route('services.report', $service) }}" class="btn-primary w-full justify-center">Print Report</a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
