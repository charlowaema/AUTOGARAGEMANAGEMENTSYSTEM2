<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChecklistTemplate;
use Illuminate\Http\Request;

class ChecklistTemplateController extends Controller
{
    public function index()
    {
        $regularItems = ChecklistTemplate::where('service_type', 'Regular')
            ->orderBy('sort_order')->get();

        $fullItems = ChecklistTemplate::where('service_type', 'Full')
            ->orderBy('sort_order')->get();

        return view('admin.checklist.index', compact('regularItems', 'fullItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_type' => 'required|in:Regular,Full',
            'item_name'    => 'required|string|max:255',
        ]);

        // Add at the end of the list
        $maxOrder = ChecklistTemplate::where('service_type', $request->service_type)
            ->max('sort_order') ?? -1;

        ChecklistTemplate::create([
            'service_type' => $request->service_type,
            'item_name'    => $request->item_name,
            'sort_order'   => $maxOrder + 1,
        ]);

        return back()->with('success', "Checklist item added to {$request->service_type} service.");
    }

    public function update(Request $request, ChecklistTemplate $template)
    {
        $request->validate(['item_name' => 'required|string|max:255']);
        $template->update(['item_name' => $request->item_name]);
        return back()->with('success', 'Checklist item updated.');
    }

    public function destroy(ChecklistTemplate $template)
    {
        $name = $template->item_name;
        $template->delete();

        // Re-sequence sort_order after deletion
        ChecklistTemplate::where('service_type', $template->service_type)
            ->orderBy('sort_order')
            ->get()
            ->each(fn($item, $i) => $item->update(['sort_order' => $i]));

        return back()->with('success', "'{$name}' removed from checklist.");
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items'   => 'required|array',
            'items.*' => 'exists:checklist_templates,id',
        ]);

        foreach ($request->items as $order => $id) {
            ChecklistTemplate::where('id', $id)->update(['sort_order' => $order]);
        }

        return response()->json(['success' => true]);
    }
}
