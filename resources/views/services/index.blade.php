@extends('layouts.app')
@section('title', 'Service Records')
@section('page-title', 'Service Records')
@section('breadcrumb', 'All garage service jobs')

@section('page-actions')
    <a href="{{ route('services.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Service
    </a>
@endsection

@section('content')
<div class="card">
    <div class="px-6 py-4 border-b border-slate-800 flex flex-wrap gap-3">
        <form method="GET" class="flex gap-3 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search entry no, plate or customer…" class="form-input max-w-xs">
            <select name="status" class="form-input max-w-[150px]">
                <option value="">All Status</option>
                <option value="Open" {{ request('status') === 'Open' ? 'selected' : '' }}>Open</option>
                <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                <option value="Closed" {{ request('status') === 'Closed' ? 'selected' : '' }}>Closed</option>
            </select>
            <button type="submit" class="btn-secondary">Filter</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('services.index') }}" class="btn-secondary">Clear</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Entry No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Vehicle / Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mileage</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                <tr class="table-row">
                    <td class="px-6 py-3 font-mono text-xs text-brand-400">{{ $service->garage_entry_no }}</td>
                    <td class="px-6 py-3">
                        <p class="font-bold text-white">{{ $service->vehicle->plate_number }}</p>
                        <p class="text-xs text-slate-500">{{ $service->customer->name }}</p>
                    </td>
                    <td class="px-6 py-3"><span class="{{ $service->service_type === 'Regular' ? 'badge-regular' : 'badge-full' }}">{{ $service->service_type }}</span></td>
                    <td class="px-6 py-3 text-slate-300">{{ $service->service_date->format('d M Y') }}</td>
                    <td class="px-6 py-3 font-mono text-sm text-white">{{ number_format($service->mileage_at_service) }}</td>
                    <td class="px-6 py-3"><span class="{{ $service->status === 'Open' ? 'badge-open' : ($service->status === 'In Progress' ? 'badge-progress' : 'badge-closed') }}">{{ $service->status }}</span></td>
                    <td class="px-6 py-3">
                        <div class="flex gap-2">
                            @if($service->status !== 'Closed')
                                <a href="{{ route('services.bay', $service) }}" class="btn-primary text-xs py-1.5 px-3">Bay →</a>
                            @else
                                <a href="{{ route('services.report', $service) }}" class="btn-secondary text-xs py-1.5 px-3">Report</a>
                            @endif
                            <a href="{{ route('services.show', $service) }}" class="btn-secondary text-xs py-1.5 px-3">View</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-slate-600">No service records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($services->hasPages())
    <div class="px-6 py-4 border-t border-slate-800">{{ $services->links() }}</div>
    @endif
</div>
@endsection
