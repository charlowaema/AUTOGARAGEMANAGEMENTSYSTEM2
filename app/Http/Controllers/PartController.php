<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;

class PartController extends Controller
{
    public function index(Request $request)
    {
        $query = Part::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('part_number', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
        }

        if ($request->get('low_stock')) {
            $query->where('quantity_in_stock', '<', 5);
        }

        $parts = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('parts.index', compact('parts'));
    }

    public function create()
    {
        return view('parts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'part_number'       => 'nullable|string|max:100',
            'category'          => 'nullable|string|max:100',
            'quantity_in_stock' => 'required|integer|min:0',
            'unit_price'        => 'required|numeric|min:0',
            'unit'              => 'required|string|max:20',
        ]);

        Part::create($validated);

        return redirect()->route('parts.index')->with('success', 'Part added to inventory.');
    }

    public function edit(Part $part)
    {
        return view('parts.edit', compact('part'));
    }

    public function update(Request $request, Part $part)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'part_number'       => 'nullable|string|max:100',
            'category'          => 'nullable|string|max:100',
            'quantity_in_stock' => 'required|integer|min:0',
            'unit_price'        => 'required|numeric|min:0',
            'unit'              => 'required|string|max:20',
        ]);

        $part->update($validated);

        return redirect()->route('parts.index')->with('success', 'Part updated.');
    }

    public function destroy(Part $part)
    {
        $part->delete();
        return redirect()->route('parts.index')->with('success', 'Part removed.');
    }

    public function restock(Request $request, Part $part)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $part->increment('quantity_in_stock', $request->quantity);

        return back()->with('success', "Restocked {$request->quantity} {$part->unit}(s) of {$part->name}.");
    }
}
