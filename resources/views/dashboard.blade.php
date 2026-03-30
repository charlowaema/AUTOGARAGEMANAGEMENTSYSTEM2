@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Overview of garage operations')

@section('page-actions')
    <a href="{{ route('services.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Service
    </a>
@endsection

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    @php
        $statCards = [
            ['label' => 'Total Vehicles',   'value' => $stats['total_vehicles'],   'color' => 'text-brand-400',  'bg' => 'bg-brand-500/10',  'icon' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10l2-2zM13 6l3 4h3a1 1 0 011 1v4l-2 2h-1'],
            ['label' => 'Customers',        'value' => $stats['total_customers'],  'color' => 'text-sky-400',    'bg' => 'bg-sky-500/10',    'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label' => 'Active Services',  'value' => $stats['open_services'],    'color' => 'text-amber-400',  'bg' => 'bg-amber-500/10',  'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
            ['label' => 'Closed Today',     'value' => $stats['closed_today'],     'color' => 'text-green-400',  'bg' => 'bg-green-500/10',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Low Stock Parts',  'value' => $stats['low_stock_parts'],  'color' => 'text-red-400',    'bg' => 'bg-red-500/10',    'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ];
    @endphp

    @foreach($statCards as $card)
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 {{ $card['bg'] }} rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 {{ $card['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-condensed font-bold text-white">{{ $card['value'] }}</p>
        <p class="text-xs text-slate-500 mt-1 font-medium">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Recent Services --}}
    <div class="lg:col-span-2 card">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h2 class="font-condensed font-bold text-base text-white">Recent Services</h2>
            <a href="{{ route('services.index') }}" class="text-xs text-brand-400 hover:text-brand-300 font-medium">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-800">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Entry No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Vehicle</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentServices as $service)
                    <tr class="table-row">
                        <td class="px-6 py-3">
                            <a href="{{ route('services.show', $service) }}" class="font-mono text-xs text-brand-400 hover:text-brand-300">{{ $service->garage_entry_no }}</a>
                        </td>
                        <td class="px-6 py-3">
                            <p class="font-semibold text-white">{{ $service->vehicle->plate_number }}</p>
                            <p class="text-xs text-slate-500">{{ $service->customer->name }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <span class="{{ $service->service_type === 'Regular' ? 'badge-regular' : 'badge-full' }}">{{ $service->service_type }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="{{ $service->status === 'Open' ? 'badge-open' : ($service->status === 'In Progress' ? 'badge-progress' : 'badge-closed') }}">{{ $service->status }}</span>
                        </td>
                        <td class="px-6 py-3 text-slate-400 text-xs">{{ $service->service_date->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-slate-600">No service records yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Upcoming Services --}}
    <div class="card">
        <div class="px-6 py-4 border-b border-slate-800">
            <h2 class="font-condensed font-bold text-base text-white">Due for Service (30 days)</h2>
        </div>
        <div class="divide-y divide-slate-800">
            @forelse($upcomingServices as $service)
            <div class="px-6 py-4">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-semibold text-white text-sm">{{ $service->vehicle->plate_number }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $service->customer->name }}</p>
                        <p class="text-xs text-slate-600 mt-1">Next: {{ number_format($service->next_service_mileage) }} km</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs font-semibold text-amber-400">{{ $service->next_service_date->format('d M') }}</p>
                        <p class="text-xs text-slate-600">{{ $service->next_service_date->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-slate-600 text-sm">No upcoming services in 30 days.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
