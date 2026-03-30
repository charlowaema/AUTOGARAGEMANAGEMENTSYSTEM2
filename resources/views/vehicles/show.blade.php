@extends('layouts.app')
@section('title', $vehicle->plate_number)
@section('page-title', $vehicle->plate_number)
@section('breadcrumb', 'Vehicles / ' . $vehicle->plate_number)

@section('page-actions')
    <a href="{{ route('services.create', ['plate' => $vehicle->plate_number]) }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Service
    </a>
    <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn-secondary">Edit</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Vehicle Info --}}
    <div class="card p-6">
        <h3 class="font-condensed font-bold text-base text-white mb-4">Vehicle Details</h3>
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Plate No.</dt><dd class="font-mono font-bold text-brand-400">{{ $vehicle->plate_number }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Make</dt><dd class="text-white">{{ $vehicle->make->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Model</dt><dd class="text-white">{{ $vehicle->vehicleModel->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Category</dt><dd class="text-white">{{ $vehicle->category }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Chassis No.</dt><dd class="font-mono text-xs text-slate-400">{{ $vehicle->chassis_number ?? '—' }}</dd></div>
            <div class="flex justify-between border-t border-slate-800 pt-3 mt-3"><dt class="text-slate-500">Current Mileage</dt><dd class="font-mono font-bold text-white">{{ number_format($vehicle->current_mileage) }} km</dd></div>
        </dl>
    </div>

    {{-- Owner Info --}}
    <div class="card p-6">
        <h3 class="font-condensed font-bold text-base text-white mb-4">Owner / Driver</h3>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-sky-500/10 rounded-full flex items-center justify-center">
                <span class="text-sky-400 font-bold text-sm">{{ strtoupper(substr($vehicle->customer->name, 0, 2)) }}</span>
            </div>
            <div>
                <p class="font-semibold text-white">{{ $vehicle->customer->name }}</p>
                <p class="text-xs text-slate-500">{{ $vehicle->customer->phone }}</p>
            </div>
        </div>
        <a href="{{ route('customers.show', $vehicle->customer) }}" class="btn-secondary w-full justify-center text-xs">View Customer Profile</a>
    </div>

    {{-- Service Summary --}}
    <div class="card p-6">
        <h3 class="font-condensed font-bold text-base text-white mb-4">Service Summary</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-slate-500">Total Services</span><span class="text-white font-bold">{{ $vehicle->serviceRecords->count() }}</span></div>
            @if($vehicle->latestService)
            <div class="flex justify-between"><span class="text-slate-500">Last Service</span><span class="text-white">{{ $vehicle->latestService->service_date->format('d M Y') }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Next Due</span><span class="text-amber-400 font-semibold">{{ $vehicle->latestService->next_service_date->format('d M Y') }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Next Mileage</span><span class="font-mono text-white">{{ number_format($vehicle->latestService->next_service_mileage) }} km</span></div>
            @else
            <p class="text-slate-600 text-xs">No service history.</p>
            @endif
        </div>
    </div>
</div>

{{-- Service History --}}
<div class="card mt-6">
    <div class="px-6 py-4 border-b border-slate-800">
        <h3 class="font-condensed font-bold text-base text-white">Service History</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Entry No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mileage</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicle->serviceRecords as $record)
                <tr class="table-row">
                    <td class="px-6 py-3 font-mono text-xs text-brand-400">{{ $record->garage_entry_no }}</td>
                    <td class="px-6 py-3 text-slate-300">{{ $record->service_date->format('d M Y') }}</td>
                    <td class="px-6 py-3"><span class="{{ $record->service_type === 'Regular' ? 'badge-regular' : 'badge-full' }}">{{ $record->service_type }}</span></td>
                    <td class="px-6 py-3 font-mono text-sm text-white">{{ number_format($record->mileage_at_service) }} km</td>
                    <td class="px-6 py-3"><span class="{{ $record->status === 'Closed' ? 'badge-closed' : 'badge-open' }}">{{ $record->status }}</span></td>
                    <td class="px-6 py-3"><a href="{{ route('services.show', $record) }}" class="text-xs text-brand-400 hover:text-brand-300">View →</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-slate-600">No service records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
