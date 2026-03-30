@extends('layouts.app')
@section('title', 'New Service')
@section('page-title', 'Garage Entry')
@section('breadcrumb', 'Services / New')

@section('content')
<div class="max-w-3xl">

    {{-- Step 1: Plate Search --}}
    <div class="card p-6 mb-6">
        <h3 class="font-condensed font-bold text-base text-white mb-4">
            <span class="text-brand-400 mr-2">①</span> Search Vehicle by Plate Number
        </h3>
        <form method="GET" action="{{ route('services.create') }}" class="flex gap-3">
            <input type="text" name="plate" value="{{ request('plate') }}" placeholder="Enter plate number e.g. KDD 123A" class="form-input font-mono uppercase flex-1">
            <button type="submit" class="btn-primary px-6">Search</button>
        </form>
    </div>

    @if(request('plate') && !$vehicle)
        <div class="card p-6 mb-6 border-amber-500/20 bg-amber-500/5">
            <p class="text-amber-400 text-sm font-medium">No vehicle found with plate "{{ strtoupper(request('plate')) }}".</p>
            <p class="text-slate-500 text-sm mt-1">This appears to be a new customer. Please <a href="{{ route('vehicles.create') }}" class="text-brand-400 hover:underline">register the vehicle</a> first.</p>
        </div>
    @endif

    @if($vehicle)
    {{-- Returning Customer Info --}}
    <div class="card p-5 mb-6 border-green-500/20 bg-green-500/5">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-green-400 font-semibold text-sm">Returning Customer — Vehicle Found</p>
                <div class="flex flex-wrap gap-x-6 gap-y-1 mt-1 text-xs text-slate-400">
                    <span>🚗 <strong class="text-white">{{ $vehicle->plate_number }}</strong> — {{ $vehicle->make->name }} {{ $vehicle->vehicleModel->name }} ({{ $vehicle->category }})</span>
                    <span>👤 <strong class="text-white">{{ $vehicle->customer->name }}</strong> · {{ $vehicle->customer->phone }}</span>
                    <span>📍 <strong class="text-white">{{ number_format($vehicle->current_mileage) }} km</strong> current mileage</span>
                    @if($vehicle->latestService)
                    <span>🔧 Last serviced <strong class="text-white">{{ $vehicle->latestService->service_date->diffForHumans() }}</strong> — next due {{ $vehicle->latestService->next_service_date->format('d M Y') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Step 2: Service Details --}}
    <div class="card p-6">
        <h3 class="font-condensed font-bold text-base text-white mb-5">
            <span class="text-brand-400 mr-2">②</span> Service Details
        </h3>
        <form method="POST" action="{{ route('services.store') }}">
            @csrf
            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
            <input type="hidden" name="customer_id" value="{{ $vehicle->customer_id }}">

            <div class="grid grid-cols-2 gap-5">
                <div class="col-span-2">
                    <label class="form-label">Service Type *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="service_type" value="Regular" class="peer sr-only" {{ old('service_type', 'Regular') === 'Regular' ? 'checked' : '' }}>
                            <div class="p-4 rounded-xl border-2 border-slate-700 peer-checked:border-sky-500 peer-checked:bg-sky-500/5 transition-all">
                                <p class="font-semibold text-white text-sm">Regular Service</p>
                                <p class="text-xs text-slate-500 mt-1">Every 5,000 km or 90 days — 5 checklist items</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="service_type" value="Full" class="peer sr-only" {{ old('service_type') === 'Full' ? 'checked' : '' }}>
                            <div class="p-4 rounded-xl border-2 border-slate-700 peer-checked:border-purple-500 peer-checked:bg-purple-500/5 transition-all">
                                <p class="font-semibold text-white text-sm">Full Service</p>
                                <p class="text-xs text-slate-500 mt-1">Every 10,000 km or 180 days — 19 checklist items</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="form-label">Mileage at Service (km) *</label>
                    <input type="number" name="mileage_at_service" value="{{ old('mileage_at_service', $vehicle->current_mileage) }}" class="form-input font-mono" min="0" required>
                </div>

                <div>
                    <label class="form-label">Service Date *</label>
                    <input type="date" name="service_date" value="{{ old('service_date', date('Y-m-d')) }}" class="form-input" required>
                </div>

                <div class="col-span-2">
                    <label class="form-label">Notes / Complaints</label>
                    <textarea name="notes" rows="3" class="form-input" placeholder="Customer complaints, observations…">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3 border-t border-slate-800 pt-5">
                <button type="submit" class="btn-primary">Open Service & Print Checklist</button>
                <a href="{{ route('services.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    @endif

    @if(!request('plate'))
    <div class="mt-6 flex items-center gap-4">
        <div class="h-px flex-1 bg-slate-800"></div>
        <span class="text-xs text-slate-600 uppercase tracking-widest">Or</span>
        <div class="h-px flex-1 bg-slate-800"></div>
    </div>
    <div class="mt-6 text-center">
        <p class="text-slate-500 text-sm">New vehicle? <a href="{{ route('vehicles.create') }}" class="text-brand-400 hover:underline">Register it first →</a></p>
    </div>
    @endif
</div>
@endsection
