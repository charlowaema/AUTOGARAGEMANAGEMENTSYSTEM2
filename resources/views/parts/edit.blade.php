@extends('layouts.app')
@section('title', 'Edit Part')
@section('page-title', 'Edit Part — ' . $part->name)
@section('breadcrumb', 'Parts / Edit')

@section('content')
<div class="max-w-lg">
    <div class="card p-6">
        <form method="POST" action="{{ route('parts.update', $part) }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="form-label">Part Name *</label>
                    <input type="text" name="name" value="{{ old('name', $part->name) }}" class="form-input" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Part Number</label>
                        <input type="text" name="part_number" value="{{ old('part_number', $part->part_number) }}" class="form-input font-mono">
                    </div>
                    <div>
                        <label class="form-label">Category</label>
                        <input type="text" name="category" value="{{ old('category', $part->category) }}" class="form-input">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Qty in Stock *</label>
                        <input type="number" name="quantity_in_stock" value="{{ old('quantity_in_stock', $part->quantity_in_stock) }}" class="form-input font-mono" min="0" required>
                    </div>
                    <div>
                        <label class="form-label">Unit Price (KES) *</label>
                        <input type="number" name="unit_price" value="{{ old('unit_price', $part->unit_price) }}" class="form-input font-mono" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label class="form-label">Unit *</label>
                        <select name="unit" class="form-input" required>
                            @foreach(['pcs','litres','metres','kg','set'] as $u)
                            <option value="{{ $u }}" {{ old('unit', $part->unit) === $u ? 'selected' : '' }}>{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex items-center gap-3 border-t border-slate-800 pt-5">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="{{ route('parts.index') }}" class="btn-secondary">Cancel</a>
                <form method="POST" action="{{ route('parts.destroy', $part) }}" class="ml-auto" onsubmit="return confirm('Delete this part?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger">Delete</button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection
