@extends('layouts.app')
@section('title', 'Vehicles')
@section('page-title', 'Vehicles')
@section('breadcrumb', 'Registered fleet')

@section('page-actions')
    <a href="{{ route('vehicles.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Vehicle
    </a>
@endsection

@section('content')
<div class="card">
    {{-- Search --}}
    <div class="px-6 py-4 border-b border-slate-800">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by plate, chassis or driver…" class="form-input max-w-xs">
            <button type="submit" class="btn-secondary">Search</button>
            @if(request('search'))
                <a href="{{ route('vehicles.index') }}" class="btn-secondary">Clear</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Plate No.</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Make / Model</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mileage</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Owner / Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Last Service</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $vehicle)
                <tr class="table-row">
                    <td class="px-6 py-3">
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="font-mono font-bold text-brand-400 hover:text-brand-300">{{ $vehicle->plate_number }}</a>
                    </td>
                    <td class="px-6 py-3">
                        <p class="text-white font-medium">{{ $vehicle->make->name }}</p>
                        <p class="text-xs text-slate-500">{{ $vehicle->vehicleModel->name }}</p>
                    </td>
                    <td class="px-6 py-3 text-slate-400">{{ $vehicle->category }}</td>
                    <td class="px-6 py-3 font-mono text-sm text-white">{{ number_format($vehicle->current_mileage) }} km</td>
                    <td class="px-6 py-3">
                        <a href="{{ route('customers.show', $vehicle->customer) }}" class="text-sky-400 hover:text-sky-300 text-sm">{{ $vehicle->customer->name }}</a>
                    </td>
                    <td class="px-6 py-3 text-slate-500 text-xs">
                        @if($vehicle->latestService)
                            {{ $vehicle->latestService->service_date->format('d M Y') }}
                        @else
                            <span class="text-slate-700">Never serviced</span>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('services.create', ['plate' => $vehicle->plate_number]) }}" class="btn-primary text-xs py-1.5 px-3">Service</a>
                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn-secondary text-xs py-1.5 px-3">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-slate-600">No vehicles found. <a href="{{ route('vehicles.create') }}" class="text-brand-400 hover:underline">Add one</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($vehicles->hasPages())
    <div class="px-6 py-4 border-t border-slate-800">
        {{ $vehicles->links() }}
    </div>
    @endif
</div>
@endsection
