@extends('layouts.app')
@section('title', 'Parts Inventory')
@section('page-title', 'Parts Inventory')
@section('breadcrumb', 'Stock management')

@section('page-actions')
    <a href="{{ route('parts.index', ['low_stock' => 1]) }}" class="btn-secondary">⚠ Low Stock</a>
    <a href="{{ route('parts.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Part
    </a>
@endsection

@section('content')
<div class="card">
    <div class="px-6 py-4 border-b border-slate-800 flex flex-wrap gap-3">
        <form method="GET" class="flex gap-3 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, part no. or category…" class="form-input max-w-xs">
            @if(request('low_stock'))
                <input type="hidden" name="low_stock" value="1">
                <span class="flex items-center px-3 py-2 rounded-lg bg-red-500/10 text-red-400 text-xs font-medium border border-red-500/20">Showing low stock only</span>
            @endif
            <button type="submit" class="btn-secondary">Search</button>
            @if(request()->hasAny(['search','low_stock']))<a href="{{ route('parts.index') }}" class="btn-secondary">Clear</a>@endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Part</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Part No.</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Unit Price</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($parts as $part)
                <tr class="table-row">
                    <td class="px-6 py-3 font-semibold text-white">{{ $part->name }}</td>
                    <td class="px-6 py-3 font-mono text-xs text-slate-400">{{ $part->part_number ?? '—' }}</td>
                    <td class="px-6 py-3 text-slate-400">{{ $part->category ?? '—' }}</td>
                    <td class="px-6 py-3">
                        <span class="font-mono font-bold {{ $part->quantity_in_stock < 5 ? 'text-red-400' : 'text-green-400' }}">
                            {{ $part->quantity_in_stock }}
                        </span>
                        <span class="text-slate-500 text-xs ml-1">{{ $part->unit }}</span>
                        @if($part->quantity_in_stock < 5)
                            <span class="ml-2 text-xs text-red-400 font-medium">⚠ Low</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 font-mono text-white">KES {{ number_format($part->unit_price, 2) }}</td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            {{-- Restock --}}
                            <form method="POST" action="{{ route('parts.restock', $part) }}" class="flex gap-1.5">
                                @csrf
                                <input type="number" name="quantity" value="10" min="1" class="w-16 bg-slate-800 border border-slate-700 text-white text-xs rounded px-2 py-1.5 font-mono focus:outline-none focus:ring-1 focus:ring-brand-500">
                                <button type="submit" class="btn-secondary text-xs py-1.5 px-2.5">Restock</button>
                            </form>
                            <a href="{{ route('parts.edit', $part) }}" class="btn-secondary text-xs py-1.5 px-2.5">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-slate-600">No parts found. <a href="{{ route('parts.create') }}" class="text-brand-400 hover:underline">Add one</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($parts->hasPages())<div class="px-6 py-4 border-t border-slate-800">{{ $parts->links() }}</div>@endif
</div>
@endsection
