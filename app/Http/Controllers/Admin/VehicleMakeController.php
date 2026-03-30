<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Http\Request;

class VehicleMakeController extends Controller
{
    public function index()
    {
        $makes = VehicleMake::withCount('models')->orderBy('name')->get();
        return view('admin.makes.index', compact('makes'));
    }

    // ── Makes ──────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:vehicle_makes,name']);
        VehicleMake::create(['name' => $request->name]);
        return back()->with('success', "Make '{$request->name}' added.");
    }

    public function updateMake(Request $request, VehicleMake $make)
    {
        $request->validate(['name' => 'required|string|max:100|unique:vehicle_makes,name,' . $make->id]);
        $make->update(['name' => $request->name]);
        return back()->with('success', "Make updated to '{$request->name}'.");
    }

    public function destroyMake(VehicleMake $make)
    {
        if ($make->models()->count() > 0) {
            return back()->with('error', "Cannot delete '{$make->name}' — it has " . $make->models()->count() . " model(s) attached. Delete models first.");
        }
        $make->delete();
        return back()->with('success', "Make '{$make->name}' deleted.");
    }

    // ── Models ─────────────────────────────────────────────────────────────────

    public function storeModel(Request $request, VehicleMake $make)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:vehicle_models,name,NULL,id,vehicle_make_id,' . $make->id,
        ]);

        VehicleModel::create([
            'vehicle_make_id' => $make->id,
            'name'            => $request->name,
        ]);

        return back()->with('success', "Model '{$request->name}' added to {$make->name}.");
    }

    public function updateModel(Request $request, VehicleMake $make, VehicleModel $model)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $model->update(['name' => $request->name]);
        return back()->with('success', "Model updated to '{$request->name}'.");
    }

    public function destroyModel(VehicleMake $make, VehicleModel $model)
    {
        if ($model->vehicles()->count() > 0) {
            return back()->with('error', "Cannot delete '{$model->name}' — it is assigned to " . $model->vehicles()->count() . " vehicle(s).");
        }
        $model->delete();
        return back()->with('success', "Model '{$model->name}' deleted.");
    }
}
