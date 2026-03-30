@extends('layouts.app')
@section('title', 'Change Password')
@section('page-title', 'Change Password')
@section('breadcrumb', 'Account / Change Password')

@section('content')
<div class="max-w-md">
    <div class="card p-6">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="form-label">Current Password *</label>
                    <input type="password" name="current_password" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">New Password *</label>
                    <input type="password" name="password" class="form-input" placeholder="Minimum 8 characters" required>
                </div>
                <div>
                    <label class="form-label">Confirm New Password *</label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3 border-t border-slate-800 pt-5">
                <button type="submit" class="btn-primary">Update Password</button>
                <a href="{{ route('dashboard') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
