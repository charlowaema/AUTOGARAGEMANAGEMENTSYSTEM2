@extends('layouts.app')
@section('title', 'Edit Vehicle')
@section('page-title', 'Edit Vehicle — ' . $vehicle->plate_number)
@section('breadcrumb', 'Vehicles / ' . $vehicle->plate_number . ' / Edit')

@section('content')
<div class="max-w-2xl">
    <div class="card p-6">
        <form method="POST" action="{{ route('vehicles.update', $vehicle) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-5">
                <div class="col-span-2">
                    <label class="form-label">Plate Number *</label>
                    <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" class="form-input font-mono uppercase" required>
                </div>

                <div>
                    <label class="form-label">Make *</label>
                    <select name="vehicle_make_id" id="make-select" class="form-input" required>
                        <option value="">— Select Make —</option>
                        @foreach($makes as $make)
                            <option value="{{ $make->id }}" {{ old('vehicle_make_id', $vehicle->vehicle_make_id) == $make->id ? 'selected' : '' }}>{{ $make->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Model *</label>
                    <select name="vehicle_model_id" id="model-select" class="form-input" required>
                        <option value="">— Select Model —</option>
                        @foreach($makes as $make)
                            @foreach($make->models as $model)
                                <option value="{{ $model->id }}" data-make="{{ $make->id }}" {{ old('vehicle_model_id', $vehicle->vehicle_model_id) == $model->id ? 'selected' : '' }}>{{ $model->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Category *</label>
                    <select name="category" class="form-input" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category', $vehicle->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Chassis Number</label>
                    <input type="text" name="chassis_number" value="{{ old('chassis_number', $vehicle->chassis_number) }}" class="form-input font-mono">
                </div>

                <div>
                    <label class="form-label">Current Mileage (km) *</label>
                    <input type="number" name="current_mileage" value="{{ old('current_mileage', $vehicle->current_mileage) }}" class="form-input font-mono" min="0" required>
                </div>

                <div>
                    <label class="form-label">Owner / Primary Driver *</label>
                    <select name="customer_id" class="form-input" required>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id', $vehicle->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3 border-t border-slate-800 pt-5">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="{{ route('vehicles.show', $vehicle) }}" class="btn-secondary">Cancel</a>
                <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" class="ml-auto" onsubmit="return confirm('Delete this vehicle?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger">Delete</button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const makeSelect = document.getElementById('make-select');
    const modelSelect = document.getElementById('model-select');
    const allOptions = Array.from(modelSelect.querySelectorAll('option[data-make]'));
    function filterModels(makeId) {
        allOptions.forEach(opt => { opt.style.display = (!makeId || opt.dataset.make === makeId) ? '' : 'none'; });
    }
    makeSelect.addEventListener('change', () => filterModels(makeSelect.value));
    filterModels(makeSelect.value);
</script>
@endpush
