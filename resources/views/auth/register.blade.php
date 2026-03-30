@extends('layouts.app')
@section('title', 'Create User')
@section('page-title', 'Create New User')
@section('breadcrumb', 'Users / New')

@section('content')
<div class="max-w-lg">
    <div class="card p-6">
        <p class="text-sm text-slate-500 mb-6">Create a login account for a garage staff member. They will use these credentials to sign in to AGMS.</p>

        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" placeholder="e.g. John Kamau" required>
                </div>
                <div>
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="john@garage.com" required>
                </div>
                <div>
                    <label class="form-label">Role *</label>
                    <select name="role" class="form-input" required>
                        <option value="">— Select Role —</option>
                        <option value="admin"        {{ old('role') === 'admin'        ? 'selected' : '' }}>Administrator — Full access including user management</option>
                        <option value="technician"   {{ old('role') === 'technician'   ? 'selected' : '' }}>Technician — Service bay & checklists</option>
                        <option value="receptionist" {{ old('role') === 'receptionist' ? 'selected' : '' }}>Receptionist — Customer, vehicle & service entry</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-input" placeholder="Minimum 8 characters" required>
                </div>
                <div>
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Repeat password" required>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3 border-t border-slate-800 pt-5">
                <button type="submit" class="btn-primary">Create Account</button>
                <a href="{{ route('users.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
