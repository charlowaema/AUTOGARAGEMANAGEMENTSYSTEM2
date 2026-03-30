@extends('layouts.app')
@section('title', 'Service — ' . $service->garage_entry_no)
@section('page-title', $service->garage_entry_no)
@section('breadcrumb', 'Services / ' . $service->garage_entry_no)

@section('page-actions')
    @if($service->status !== 'Closed')
        <a href="{{ route('services.bay', $service) }}" class="btn-primary">Open Bay →</a>
    @else
        <a href="{{ route('services.report', $service) }}" class="btn-primary">Print Report</a>
    @endif
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="card p-6">
        <h3 class="font-condensed font-bold text-base text-white mb-4">Service Info</h3>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Entry No.</dt><dd class="font-mono text-brand-400 font-bold">{{ $service->garage_entry_no }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Type</dt><dd><span class="{{ $service->service_type === 'Regular' ? 'badge-regular' : 'badge-full' }}">{{ $service->service_type }}</span></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd><span class="{{ $service->status === 'Closed' ? 'badge-closed' : 'badge-open' }}">{{ $service->status }}</span></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Date</dt><dd class="text-white">{{ $service->service_date->format('d M Y') }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Mileage</dt><dd class="font-mono text-white">{{ number_format($service->mileage_at_service) }} km</dd></div>
            <div class="flex justify-between border-t border-slate-800 pt-2 mt-2"><dt class="text-slate-500">Next Due</dt><dd class="text-amber-400 font-semibold">{{ $service->next_service_date->format('d M Y') }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Next Mileage</dt><dd class="font-mono text-amber-400">{{ number_format($service->next_service_mileage) }} km</dd></div>
        </dl>
    </div>

    <div class="card p-6">
        <h3 class="font-condensed font-bold text-base text-white mb-4">Vehicle</h3>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Plate</dt><dd class="font-mono font-bold text-brand-400">{{ $service->vehicle->plate_number }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Make</dt><dd class="text-white">{{ $service->vehicle->make->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Model</dt><dd class="text-white">{{ $service->vehicle->vehicleModel->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Driver</dt><dd class="text-white">{{ $service->customer->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Phone</dt><dd class="text-slate-400">{{ $service->customer->phone }}</dd></div>
        </dl>
    </div>

    <div class="card p-6">
        <h3 class="font-condensed font-bold text-base text-white mb-4">Costs</h3>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Parts</dt><dd class="font-mono text-white">KES {{ number_format($service->total_parts_amount, 2) }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Labour</dt><dd class="font-mono text-white">KES {{ number_format($service->total_labour_cost, 2) }}</dd></div>
            <div class="flex justify-between border-t border-slate-800 pt-2 mt-2 font-bold"><dt class="text-white">Total</dt><dd class="font-mono text-brand-400 text-lg">KES {{ number_format($service->grand_total, 2) }}</dd></div>
        </dl>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <div class="card">
        <div class="px-6 py-4 border-b border-slate-800"><h3 class="font-condensed font-bold text-base text-white">Checklist</h3></div>
        <div class="divide-y divide-slate-800">
            @foreach($service->checklistItems->sortBy('sort_order') as $item)
            <div class="px-6 py-3 flex items-center gap-3">
                <span class="w-5 h-5 rounded flex items-center justify-center text-xs flex-shrink-0 {{ $item->status === 'Done' ? 'bg-green-500/20 text-green-400' : ($item->status === 'N/A' ? 'bg-slate-700 text-slate-500' : 'bg-amber-500/20 text-amber-400') }}">
                    {{ $item->status === 'Done' ? '✓' : ($item->status === 'N/A' ? '—' : '…') }}
                </span>
                <div>
                    <p class="text-sm text-white">{{ $item->item_name }}</p>
                    @if($item->remarks)<p class="text-xs text-slate-500">{{ $item->remarks }}</p>@endif
                </div>
                <span class="ml-auto text-xs {{ $item->status === 'Done' ? 'text-green-400' : ($item->status === 'N/A' ? 'text-slate-500' : 'text-amber-400') }}">{{ $item->status }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="px-6 py-4 border-b border-slate-800"><h3 class="font-condensed font-bold text-base text-white">Parts Used</h3></div>
        <div class="divide-y divide-slate-800">
            @forelse($service->serviceParts as $sp)
            <div class="px-6 py-3 flex justify-between items-center">
                <div><p class="text-sm text-white">{{ $sp->part->name }}</p><p class="text-xs text-slate-500">{{ $sp->quantity_used }} × KES {{ number_format($sp->unit_price_at_service, 2) }}</p></div>
                <p class="font-mono font-bold text-white">KES {{ number_format($sp->line_amount, 2) }}</p>
            </div>
            @empty
            <div class="px-6 py-6 text-center text-slate-600 text-sm">No parts used.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
