@extends('layouts.app')
@section('title', $customer->name)
@section('page-title', $customer->name)
@section('breadcrumb', 'Customers / ' . $customer->name)

@section('page-actions')
    <a href="{{ route('customers.edit', $customer) }}" class="btn-secondary">Edit</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="card p-6">
        <div class="flex items-center gap-4 mb-5">
            <div class="w-12 h-12 bg-sky-500/10 rounded-full flex items-center justify-center">
                <span class="text-sky-400 font-bold text-lg">{{ strtoupper(substr($customer->name, 0, 2)) }}</span>
            </div>
            <div>
                <h3 class="font-bold text-white">{{ $customer->name }}</h3>
                <p class="text-sm text-slate-500">Customer</p>
            </div>
        </div>
        <dl class="space-y-2 text-sm">
            <div class="flex gap-3"><dt class="text-slate-500 w-16">Phone</dt><dd class="text-white">{{ $customer->phone }}</dd></div>
            <div class="flex gap-3"><dt class="text-slate-500 w-16">Email</dt><dd class="text-slate-400">{{ $customer->email ?? '—' }}</dd></div>
            <div class="flex gap-3"><dt class="text-slate-500 w-16">Since</dt><dd class="text-slate-400">{{ $customer->created_at->format('M Y') }}</dd></div>
        </dl>
    </div>

    <div class="card p-6">
        <h3 class="font-condensed font-bold text-base text-white mb-4">Vehicles ({{ $customer->vehicles->count() }})</h3>
        <div class="space-y-2">
            @forelse($customer->vehicles as $vehicle)
            <a href="{{ route('vehicles.show', $vehicle) }}" class="flex items-center justify-between p-3 rounded-lg bg-slate-800 hover:bg-slate-700 transition-colors">
                <div>
                    <p class="font-mono font-bold text-brand-400 text-sm">{{ $vehicle->plate_number }}</p>
                    <p class="text-xs text-slate-500">{{ $vehicle->make->name }} {{ $vehicle->vehicleModel->name }}</p>
                </div>
                <span class="text-xs text-slate-600">{{ $vehicle->category }}</span>
            </a>
            @empty
            <p class="text-slate-600 text-sm">No vehicles registered.</p>
            @endforelse
        </div>
    </div>

    <div class="card p-6">
        <h3 class="font-condensed font-bold text-base text-white mb-4">Stats</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-slate-500">Total Services</span><span class="font-bold text-white">{{ $customer->serviceRecords->count() }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Vehicles</span><span class="font-bold text-white">{{ $customer->vehicles->count() }}</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="px-6 py-4 border-b border-slate-800"><h3 class="font-condensed font-bold text-base text-white">Service History</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-slate-800">
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Entry No</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Vehicle</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3"></th>
            </tr></thead>
            <tbody>
                @forelse($customer->serviceRecords as $service)
                <tr class="table-row">
                    <td class="px-6 py-3 font-mono text-xs text-brand-400">{{ $service->garage_entry_no }}</td>
                    <td class="px-6 py-3 font-mono font-bold text-white">{{ $service->vehicle->plate_number }}</td>
                    <td class="px-6 py-3"><span class="{{ $service->service_type === 'Regular' ? 'badge-regular' : 'badge-full' }}">{{ $service->service_type }}</span></td>
                    <td class="px-6 py-3 text-slate-300">{{ $service->service_date->format('d M Y') }}</td>
                    <td class="px-6 py-3"><span class="{{ $service->status === 'Closed' ? 'badge-closed' : 'badge-open' }}">{{ $service->status }}</span></td>
                    <td class="px-6 py-3"><a href="{{ route('services.show', $service) }}" class="text-xs text-brand-400 hover:text-brand-300">View →</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-slate-600">No service history.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
