@extends('layouts.app')
@section('title', 'Add Customer')
@section('page-title', 'Register Customer')
@section('breadcrumb', 'Customers / New')

@section('content')
<div class="max-w-lg">
    <div class="card p-6">
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Phone Number *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="+254 7xx xxx xxx" required>
                </div>
                <div>
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="optional">
                </div>
            </div>
            <div class="mt-6 flex items-center gap-3 border-t border-slate-800 pt-5">
                <button type="submit" class="btn-primary">Register Customer</button>
                <a href="{{ route('customers.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
