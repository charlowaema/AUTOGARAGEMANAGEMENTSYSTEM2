<?php

namespace App\Http\Controllers;

use App\Models\ChecklistTemplate;
use App\Models\Customer;
use App\Models\Part;
use App\Models\ServiceChecklistItem;
use App\Models\ServicePart;
use App\Models\ServiceRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ServiceRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceRecord::with(['vehicle', 'customer']);

        if ($search = $request->get('search')) {
            $query->where('garage_entry_no', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', fn($q) => $q->where('plate_number', 'like', "%{$search}%"))
                  ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $services = $query->latest()->paginate(15)->withQueryString();

        return view('services.index', compact('services'));
    }

    /**
     * Step 1: Show the garage entry form — search by plate or start new
     */
    public function create(Request $request)
    {
        $vehicle   = null;
        $customers = Customer::orderBy('name')->get();

        if ($plate = $request->get('plate')) {
            $vehicle = Vehicle::with(['make', 'vehicleModel', 'customer', 'latestService'])
                ->where('plate_number', strtoupper(trim($plate)))
                ->first();
        }

        return view('services.create', compact('vehicle', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'       => 'required|exists:vehicles,id',
            'customer_id'      => 'required|exists:customers,id',
            'service_type'     => 'required|in:Regular,Full',
            'mileage_at_service' => 'required|integer|min:0',
            'service_date'     => 'required|date',
            'notes'            => 'nullable|string',
        ]);

        // Calculate next service
        $mileage = $validated['mileage_at_service'];
        if ($validated['service_type'] === 'Regular') {
            $validated['next_service_mileage'] = $mileage + 5000;
            $validated['next_service_date']    = now()->addDays(90)->toDateString();
        } else {
            $validated['next_service_mileage'] = $mileage + 10000;
            $validated['next_service_date']    = now()->addDays(180)->toDateString();
        }

        $service = ServiceRecord::create($validated);

        // Update vehicle current mileage
        Vehicle::find($validated['vehicle_id'])->update([
            'current_mileage' => $mileage,
        ]);

        // Seed checklist from template
        $items = $validated['service_type'] === 'Regular'
            ? ChecklistTemplate::regularItems()
            : ChecklistTemplate::fullItems();

        foreach ($items as $i => $item) {
            ServiceChecklistItem::create([
                'service_record_id' => $service->id,
                'item_name'         => $item,
                'status'            => 'Pending',
                'sort_order'        => $i,
            ]);
        }

        return redirect()->route('services.bay', $service)
                         ->with('success', 'Service record opened. Proceed to Service Bay.');
    }

    /**
     * Service Bay — manage checklist & parts
     */
    public function bay(ServiceRecord $service)
    {
        $service->load(['vehicle.make', 'vehicle.vehicleModel', 'customer', 'checklistItems', 'serviceParts.part']);
        $parts = Part::orderBy('name')->get();

        return view('services.bay', compact('service', 'parts'));
    }

    public function updateChecklist(Request $request, ServiceRecord $service)
    {
        $request->validate([
            'checklist'         => 'required|array',
            'checklist.*.id'    => 'required|exists:service_checklist_items,id',
            'checklist.*.status'=> 'required|in:Pending,Done,N/A',
            'checklist.*.remarks' => 'nullable|string',
        ]);

        foreach ($request->checklist as $item) {
            ServiceChecklistItem::where('id', $item['id'])
                ->where('service_record_id', $service->id)
                ->update([
                    'status'  => $item['status'],
                    'remarks' => $item['remarks'] ?? null,
                ]);
        }

        return back()->with('success', 'Checklist updated.');
    }

    public function addPart(Request $request, ServiceRecord $service)
    {
        $request->validate([
            'part_id'        => 'required|exists:parts,id',
            'quantity_used'  => 'required|integer|min:1',
        ]);

        $part = Part::findOrFail($request->part_id);

        if ($part->quantity_in_stock < $request->quantity_used) {
            return back()->with('error', "Insufficient stock. Only {$part->quantity_in_stock} {$part->unit}(s) available.");
        }

        ServicePart::create([
            'service_record_id'    => $service->id,
            'part_id'              => $part->id,
            'quantity_used'        => $request->quantity_used,
            'unit_price_at_service'=> $part->unit_price,
        ]);

        $part->decrement('quantity_in_stock', $request->quantity_used);

        return back()->with('success', "Part '{$part->name}' added.");
    }

    public function removePart(ServiceRecord $service, ServicePart $part)
    {
        // Restore stock
        $part->part->increment('quantity_in_stock', $part->quantity_used);
        $part->delete();

        return back()->with('success', 'Part removed.');
    }

    public function closeService(Request $request, ServiceRecord $service)
    {
        $request->validate([
            'total_labour_cost' => 'required|numeric|min:0',
        ]);

        $service->update([
            'status'            => 'Closed',
            'total_labour_cost' => $request->total_labour_cost,
        ]);

        return redirect()->route('payments.show', $service)
                         ->with('success', 'Service closed. Please collect payment.');
    }

    public function show(ServiceRecord $service)
    {
        $service->load(['vehicle.make', 'vehicle.vehicleModel', 'customer', 'checklistItems', 'serviceParts.part']);
        return view('services.show', compact('service'));
    }

    public function report(ServiceRecord $service)
    {
        $service->load(['vehicle.make', 'vehicle.vehicleModel', 'customer', 'checklistItems', 'serviceParts.part']);
        return view('services.report', compact('service'));
    }
}
