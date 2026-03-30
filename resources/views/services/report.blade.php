@extends('layouts.app')
@section('title', 'Service Report — ' . $service->garage_entry_no)
@section('page-title', 'Service Report')
@section('breadcrumb', 'Services / ' . $service->garage_entry_no . ' / Report')

@section('page-actions')
    <button onclick="window.print()" class="btn-primary no-print">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Report
    </button>
    <a href="{{ route('services.index') }}" class="btn-secondary no-print">Back</a>
@endsection

@section('content')
{{-- Print-optimised report --}}
<div class="max-w-3xl mx-auto" id="report">
    {{-- Header --}}
    <div class="card p-8 mb-6">
        <div class="flex items-start justify-between mb-6 pb-6 border-b border-slate-800">
            <div>
                <h2 class="font-condensed font-bold text-2xl text-white">AUTO GARAGE MANAGEMENT SYSTEM</h2>
                <p class="text-slate-500 text-sm mt-1">Service Completion Report</p>
            </div>
            <div class="text-right">
                <p class="font-mono text-brand-400 font-bold text-lg">{{ $service->garage_entry_no }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $service->service_date->format('d F Y') }}</p>
                <span class="{{ $service->service_type === 'Regular' ? 'badge-regular' : 'badge-full' }} mt-2">{{ $service->service_type }} Service</span>
            </div>
        </div>

        {{-- Vehicle & Customer --}}
        <div class="grid grid-cols-2 gap-8 mb-6">
            <div>
                <h3 class="font-condensed font-bold text-sm text-slate-400 uppercase tracking-wider mb-3">Vehicle Information</h3>
                <dl class="space-y-1.5 text-sm">
                    <div class="flex gap-4"><dt class="text-slate-500 w-28">Plate No.</dt><dd class="font-mono font-bold text-brand-400">{{ $service->vehicle->plate_number }}</dd></div>
                    <div class="flex gap-4"><dt class="text-slate-500 w-28">Make / Model</dt><dd class="text-white">{{ $service->vehicle->make->name }} {{ $service->vehicle->vehicleModel->name }}</dd></div>
                    <div class="flex gap-4"><dt class="text-slate-500 w-28">Category</dt><dd class="text-white">{{ $service->vehicle->category }}</dd></div>
                    <div class="flex gap-4"><dt class="text-slate-500 w-28">Chassis No.</dt><dd class="font-mono text-xs text-slate-400">{{ $service->vehicle->chassis_number ?? '—' }}</dd></div>
                    <div class="flex gap-4"><dt class="text-slate-500 w-28">Mileage In</dt><dd class="font-mono text-white">{{ number_format($service->mileage_at_service) }} km</dd></div>
                </dl>
            </div>
            <div>
                <h3 class="font-condensed font-bold text-sm text-slate-400 uppercase tracking-wider mb-3">Driver / Customer</h3>
                <dl class="space-y-1.5 text-sm">
                    <div class="flex gap-4"><dt class="text-slate-500 w-24">Name</dt><dd class="text-white font-semibold">{{ $service->customer->name }}</dd></div>
                    <div class="flex gap-4"><dt class="text-slate-500 w-24">Phone</dt><dd class="text-white">{{ $service->customer->phone }}</dd></div>
                    <div class="flex gap-4"><dt class="text-slate-500 w-24">Email</dt><dd class="text-slate-400">{{ $service->customer->email ?? '—' }}</dd></div>
                    <div class="flex gap-4 mt-4"><dt class="text-slate-500 w-24">Next Service</dt><dd class="text-amber-400 font-bold">{{ $service->next_service_date->format('d M Y') }}</dd></div>
                    <div class="flex gap-4"><dt class="text-slate-500 w-24">Next Mileage</dt><dd class="font-mono text-amber-400 font-bold">{{ number_format($service->next_service_mileage) }} km</dd></div>
                </dl>
            </div>
        </div>

        @if($service->notes)
        <div class="bg-slate-800 rounded-lg px-4 py-3 text-sm text-slate-300 mb-6">
            <span class="text-slate-500 font-medium">Notes: </span>{{ $service->notes }}
        </div>
        @endif
    </div>

    {{-- Checklist --}}
    <div class="card p-6 mb-6">
        <h3 class="font-condensed font-bold text-base text-white mb-4">Service Checklist</h3>
        <div class="grid grid-cols-2 gap-2">
            @foreach($service->checklistItems->sortBy('sort_order') as $item)
            <div class="flex items-start gap-3 py-2 border-b border-slate-800">
                <span class="mt-0.5 flex-shrink-0 w-5 h-5 rounded flex items-center justify-center text-xs
                    {{ $item->status === 'Done' ? 'bg-green-500/20 text-green-400' : ($item->status === 'N/A' ? 'bg-slate-700 text-slate-500' : 'bg-amber-500/20 text-amber-400') }}">
                    {{ $item->status === 'Done' ? '✓' : ($item->status === 'N/A' ? '—' : '!') }}
                </span>
                <div class="flex-1">
                    <p class="text-xs text-white">{{ $item->item_name }}</p>
                    @if($item->remarks)<p class="text-xs text-slate-500 italic mt-0.5">{{ $item->remarks }}</p>@endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Parts & Costs --}}
    <div class="card p-6">
        <h3 class="font-condensed font-bold text-base text-white mb-4">Parts Replaced & Costs</h3>

        @if($service->serviceParts->count())
        <table class="w-full text-sm mb-4">
            <thead>
                <tr class="border-b border-slate-800">
                    <th class="py-2 text-left text-xs font-semibold text-slate-500 uppercase">Part</th>
                    <th class="py-2 text-right text-xs font-semibold text-slate-500 uppercase">Qty</th>
                    <th class="py-2 text-right text-xs font-semibold text-slate-500 uppercase">Unit Price</th>
                    <th class="py-2 text-right text-xs font-semibold text-slate-500 uppercase">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @foreach($service->serviceParts as $sp)
                <tr>
                    <td class="py-2 text-white">{{ $sp->part->name }}</td>
                    <td class="py-2 text-right font-mono text-slate-400">{{ $sp->quantity_used }}</td>
                    <td class="py-2 text-right font-mono text-slate-400">{{ number_format($sp->unit_price_at_service, 2) }}</td>
                    <td class="py-2 text-right font-mono font-semibold text-white">KES {{ number_format($sp->line_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-slate-600 text-sm mb-4">No parts replaced.</p>
        @endif

        {{-- Totals --}}
        <div class="border-t border-slate-800 pt-4 space-y-2">
            <div class="flex justify-between text-sm"><span class="text-slate-400">Parts Subtotal</span><span class="font-mono text-white">KES {{ number_format($service->total_parts_amount, 2) }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-slate-400">Labour</span><span class="font-mono text-white">KES {{ number_format($service->total_labour_cost, 2) }}</span></div>
            <div class="flex justify-between font-bold text-base border-t border-slate-700 pt-2 mt-2"><span class="text-white">GRAND TOTAL</span><span class="font-mono text-brand-400 text-lg">KES {{ number_format($service->grand_total, 2) }}</span></div>
        </div>

        {{-- Signature lines --}}
        <div class="grid grid-cols-2 gap-8 mt-10 pt-6 border-t border-slate-800">
            <div>
                <div class="border-b border-slate-600 pb-1 mb-2 h-8"></div>
                <p class="text-xs text-slate-500">Technician Signature</p>
            </div>
            <div>
                <div class="border-b border-slate-600 pb-1 mb-2 h-8"></div>
                <p class="text-xs text-slate-500">Customer Signature</p>
            </div>
        </div>
    </div>
</div>
@endsection
