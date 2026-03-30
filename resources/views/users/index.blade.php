@extends('layouts.app')
@section('title', 'User Management')
@section('page-title', 'User Management')
@section('breadcrumb', 'Admin / Users')

@section('page-actions')
    <a href="{{ route('register') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add User
    </a>
@endsection

@section('content')
<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="table-row">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background:{{ $user->isAdmin() ? 'rgb(249 115 22 / 0.15)' : 'rgb(100 116 139 / 0.15)' }}">
                                <span class="text-xs font-bold" style="color:{{ $user->isAdmin() ? '#f97316' : '#94a3b8' }}">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-white">{{ $user->name }}</p>
                                @if($user->id === auth()->id())
                                    <p class="text-xs text-brand-400">You</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-slate-400">{{ $user->email }}</td>
                    <td class="px-6 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $user->role === 'admin' ? 'bg-orange-500/10 text-orange-400 border border-orange-500/20' :
                               ($user->role === 'technician' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' :
                               'bg-slate-500/10 text-slate-400 border border-slate-500/20') }}">
                            {{ $user->role_label }}
                        </span>
                    </td>
                    <td class="px-6 py-3">
                        @if($user->is_active)
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-400 inline-block"></span> Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span> Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-slate-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('users.edit', $user) }}" class="btn-secondary text-xs py-1.5 px-3">Edit</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger text-xs py-1.5 px-3">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())<div class="px-6 py-4 border-t border-slate-800">{{ $users->links() }}</div>@endif
</div>
@endsection
