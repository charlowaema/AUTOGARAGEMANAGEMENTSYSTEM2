<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ServiceRecord;
use App\Models\Vehicle;
use App\Models\Part;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_vehicles'  => Vehicle::count(),
            'total_customers' => Customer::count(),
            'open_services'   => ServiceRecord::whereIn('status', ['Open', 'In Progress'])->count(),
            'closed_today'    => ServiceRecord::where('status', 'Closed')
                ->whereDate('updated_at', today())->count(),
            'low_stock_parts' => Part::where('quantity_in_stock', '<', 5)->count(),
        ];

        $recentServices = ServiceRecord::with(['vehicle', 'customer'])
            ->latest()
            ->take(8)
            ->get();

        $upcomingServices = ServiceRecord::with(['vehicle', 'customer'])
            ->where('status', 'Closed')
            ->where('next_service_date', '>=', today())
            ->where('next_service_date', '<=', today()->addDays(30))
            ->orderBy('next_service_date')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentServices', 'upcomingServices'));
    }
}
