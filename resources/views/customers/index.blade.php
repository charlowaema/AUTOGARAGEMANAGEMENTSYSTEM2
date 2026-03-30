@extends('layouts.app')
@section('title', 'Customers')
@section('page-title', 'Customers')
@section('breadcrumb', 'Registered drivers & owners')

@section('page-actions')
    <a href="{{ route('customers.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Customer
    </a>
@endsection

@section('content')
<div class="card">
    <div class="px-6 py-4 border-b border-slate-800">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone or email…" class="form-input max-w-xs">
            <button type="submit" class="btn-secondary">Search</button>
            @if(request('search'))<a href="{{ route('customers.index') }}" class="btn-secondary">Clear</a>@endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Vehicles</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr class="table-row">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-sky-500/10 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-sky-400 font-bold text-xs">{{ strtoupper(substr($customer->name, 0, 2)) }}</span>
                            </div>
                            <a href="{{ route('customers.show', $customer) }}" class="font-semibold text-white hover:text-brand-400 transition-colors">{{ $customer->name }}</a>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-slate-300">{{ $customer->phone }}</td>
                    <td class="px-6 py-3 text-slate-500">{{ $customer->email ?? '—' }}</td>
                    <td class="px-6 py-3"><span class="font-bold text-white">{{ $customer->vehicles_count }}</span></td>
                    <td class="px-6 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('customers.show', $customer) }}" class="btn-secondary text-xs py-1.5 px-3">View</a>
                            <a href="{{ route('customers.edit', $customer) }}" class="btn-secondary text-xs py-1.5 px-3">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-slate-600">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())<div class="px-6 py-4 border-t border-slate-800">{{ $customers->links() }}</div>@endif
</div>
@endsection
