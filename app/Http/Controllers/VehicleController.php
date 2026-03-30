<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with(['make', 'vehicleModel', 'customer', 'latestService']);

        if ($search = $request->get('search')) {
            $query->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('chassis_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $vehicles = $query->latest()->paginate(15)->withQueryString();

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $makes     = VehicleMake::with('models')->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $categories = ['Car', 'Van', 'Mini Truck', 'Truck', 'Trailer'];

        return view('vehicles.create', compact('makes', 'customers', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number'     => 'required|string|unique:vehicles,plate_number',
            'vehicle_make_id'  => 'required|exists:vehicle_makes,id',
            'vehicle_model_id' => 'required|exists:vehicle_models,id',
            'category'         => 'required|in:Car,Van,Mini Truck,Truck,Trailer',
            'chassis_number'   => 'nullable|string',
            'current_mileage'  => 'required|integer|min:0',
            'customer_id'      => 'required|exists:customers,id',
        ]);

        $vehicle = Vehicle::create($validated);

        return redirect()->route('vehicles.show', $vehicle)
                         ->with('success', 'Vehicle registered successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['make', 'vehicleModel', 'customer', 'serviceRecords' => fn($q) => $q->latest()]);
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $makes      = VehicleMake::with('models')->orderBy('name')->get();
        $customers  = Customer::orderBy('name')->get();
        $categories = ['Car', 'Van', 'Mini Truck', 'Truck', 'Trailer'];

        return view('vehicles.edit', compact('vehicle', 'makes', 'customers', 'categories'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number'     => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'vehicle_make_id'  => 'required|exists:vehicle_makes,id',
            'vehicle_model_id' => 'required|exists:vehicle_models,id',
            'category'         => 'required|in:Car,Van,Mini Truck,Truck,Trailer',
            'chassis_number'   => 'nullable|string',
            'current_mileage'  => 'required|integer|min:0',
            'customer_id'      => 'required|exists:customers,id',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.show', $vehicle)
                         ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')
                         ->with('success', 'Vehicle removed.');
    }

    // AJAX endpoint to get models for a given make
    public function modelsByMake(VehicleMake $make)
    {
        return response()->json($make->models()->orderBy('name')->get(['id', 'name']));
    }
}
