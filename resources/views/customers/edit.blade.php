@extends('layouts.app')
@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')
@section('breadcrumb', 'Customers / Edit')

@section('content')
<div class="max-w-lg">
    <div class="card p-6">
        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Phone Number *</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="form-input">
                </div>
            </div>
            <div class="mt-6 flex items-center gap-3 border-t border-slate-800 pt-5">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="{{ route('customers.show', $customer) }}" class="btn-secondary">Cancel</a>
                <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="ml-auto" onsubmit="return confirm('Delete this customer?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger">Delete</button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection
