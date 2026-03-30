@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User — ' . $user->name)
@section('breadcrumb', 'Admin / Users / Edit')

@section('content')
<div class="max-w-lg">
    <div class="card p-6">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Role *</label>
                    <select name="role" class="form-input" required>
                        <option value="admin"        {{ old('role', $user->role) === 'admin'        ? 'selected' : '' }}>Administrator</option>
                        <option value="technician"   {{ old('role', $user->role) === 'technician'   ? 'selected' : '' }}>Technician</option>
                        <option value="receptionist" {{ old('role', $user->role) === 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Account Status</label>
                    <select name="is_active" class="form-input">
                        <option value="1" {{ old('is_active', $user->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $user->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive (blocked from login)</option>
                    </select>
                </div>
                <div class="border-t border-slate-800 pt-4">
                    <p class="text-xs text-slate-500 mb-3">Leave blank to keep current password.</p>
                    <div class="space-y-3">
                        <div>
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-input" placeholder="Minimum 8 characters">
                        </div>
                        <div>
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-input">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3 border-t border-slate-800 pt-5">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="{{ route('users.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
