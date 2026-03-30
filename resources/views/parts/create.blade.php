@extends('layouts.app')
@section('title', 'Add Part')
@section('page-title', 'Add Part to Inventory')
@section('breadcrumb', 'Parts / New')

@section('content')
<div class="max-w-lg">
    <div class="card p-6">
        <form method="POST" action="{{ route('parts.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="form-label">Part Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" placeholder="e.g. Engine Oil Filter" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Part Number</label>
                        <input type="text" name="part_number" value="{{ old('part_number') }}" class="form-input font-mono" placeholder="optional">
                    </div>
                    <div>
                        <label class="form-label">Category</label>
                        <input type="text" name="category" value="{{ old('category') }}" class="form-input" placeholder="e.g. Filter, Oil, Belt">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Qty in Stock *</label>
                        <input type="number" name="quantity_in_stock" value="{{ old('quantity_in_stock', 0) }}" class="form-input font-mono" min="0" required>
                    </div>
                    <div>
                        <label class="form-label">Unit Price (KES) *</label>
                        <input type="number" name="unit_price" value="{{ old('unit_price', 0) }}" class="form-input font-mono" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label class="form-label">Unit *</label>
                        <select name="unit" class="form-input" required>
                            <option value="pcs" {{ old('unit') === 'pcs' ? 'selected' : '' }}>pcs</option>
                            <option value="litres" {{ old('unit') === 'litres' ? 'selected' : '' }}>litres</option>
                            <option value="metres" {{ old('unit') === 'metres' ? 'selected' : '' }}>metres</option>
                            <option value="kg" {{ old('unit') === 'kg' ? 'selected' : '' }}>kg</option>
                            <option value="set" {{ old('unit') === 'set' ? 'selected' : '' }}>set</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex items-center gap-3 border-t border-slate-800 pt-5">
                <button type="submit" class="btn-primary">Add to Inventory</button>
                <a href="{{ route('parts.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
