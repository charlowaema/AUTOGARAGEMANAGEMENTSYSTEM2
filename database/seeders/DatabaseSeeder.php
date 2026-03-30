<?php

namespace Database\Seeders;

use App\Models\ChecklistTemplate;
use App\Models\Part;
use App\Models\User;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Default Admin User ---
        User::create([
            'name'      => 'Administrator',
            'email'     => 'admin@agms.local',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // --- Vehicle Makes & Models ---
        $data = [
            'Toyota'    => ['Corolla', 'Premio', 'Axio', 'Prado', 'Land Cruiser', 'Hilux', 'Hiace'],
            'Isuzu'     => ['D-Max', 'Forward', 'FSR', 'FRR', 'NMR', 'NQR', 'FVR'],
            'Mitsubishi'=> ['Canter', 'Fuso', 'Pajero', 'Outlander', 'L200'],
            'Mercedes'  => ['Sprinter', 'Actros', 'Atego', 'C-Class', 'E-Class'],
            'Nissan'    => ['Navara', 'Patrol', 'Caravan', 'UD', 'Note', 'March'],
            'Ford'      => ['Ranger', 'Explorer', 'Transit', 'Everest'],
            'Volkswagen'=> ['Polo', 'Golf', 'Crafter', 'Transporter'],
            'Hino'      => ['500 Series', '700 Series', '300 Series', 'Ranger'],
            'Scania'    => ['P-Series', 'G-Series', 'R-Series', 'S-Series'],
            'Volvo'     => ['FM', 'FH', 'FE', 'FL'],
        ];

        foreach ($data as $makeName => $models) {
            $make = VehicleMake::create(['name' => $makeName]);
            foreach ($models as $modelName) {
                VehicleModel::create(['vehicle_make_id' => $make->id, 'name' => $modelName]);
            }
        }

        // --- Checklist Templates ---
        foreach (ChecklistTemplate::regularItems() as $i => $item) {
            ChecklistTemplate::create(['service_type' => 'Regular', 'item_name' => $item, 'sort_order' => $i]);
        }
        foreach (ChecklistTemplate::fullItems() as $i => $item) {
            ChecklistTemplate::create(['service_type' => 'Full', 'item_name' => $item, 'sort_order' => $i]);
        }

        // --- Common Parts Inventory ---
        $parts = [
            ['name' => 'Engine Oil (5W-30)',      'part_number' => 'OIL-5W30',    'category' => 'Oil',       'quantity_in_stock' => 50,  'unit_price' => 850,   'unit' => 'litres'],
            ['name' => 'Engine Oil (15W-40)',      'part_number' => 'OIL-15W40',   'category' => 'Oil',       'quantity_in_stock' => 50,  'unit_price' => 750,   'unit' => 'litres'],
            ['name' => 'Oil Filter (Toyota)',      'part_number' => 'FLT-OIL-TY',  'category' => 'Filter',    'quantity_in_stock' => 30,  'unit_price' => 450,   'unit' => 'pcs'],
            ['name' => 'Oil Filter (Isuzu)',       'part_number' => 'FLT-OIL-IZ',  'category' => 'Filter',    'quantity_in_stock' => 20,  'unit_price' => 550,   'unit' => 'pcs'],
            ['name' => 'Air Filter (Universal)',   'part_number' => 'FLT-AIR-UNV', 'category' => 'Filter',    'quantity_in_stock' => 25,  'unit_price' => 800,   'unit' => 'pcs'],
            ['name' => 'Fuel Filter',              'part_number' => 'FLT-FUEL',     'category' => 'Filter',    'quantity_in_stock' => 20,  'unit_price' => 600,   'unit' => 'pcs'],
            ['name' => 'Spark Plugs (set of 4)',   'part_number' => 'PLUG-4SET',    'category' => 'Ignition',  'quantity_in_stock' => 15,  'unit_price' => 1200,  'unit' => 'set'],
            ['name' => 'Brake Fluid (DOT 4)',      'part_number' => 'FLD-BRK-D4',  'category' => 'Fluid',     'quantity_in_stock' => 30,  'unit_price' => 350,   'unit' => 'litres'],
            ['name' => 'Coolant',                  'part_number' => 'FLD-CLT',      'category' => 'Fluid',     'quantity_in_stock' => 40,  'unit_price' => 400,   'unit' => 'litres'],
            ['name' => 'Transmission Fluid',       'part_number' => 'FLD-TRN',      'category' => 'Fluid',     'quantity_in_stock' => 20,  'unit_price' => 950,   'unit' => 'litres'],
            ['name' => 'Power Steering Fluid',     'part_number' => 'FLD-PSF',      'category' => 'Fluid',     'quantity_in_stock' => 15,  'unit_price' => 500,   'unit' => 'litres'],
            ['name' => 'Washer Fluid',             'part_number' => 'FLD-WSH',      'category' => 'Fluid',     'quantity_in_stock' => 20,  'unit_price' => 150,   'unit' => 'litres'],
            ['name' => 'Wiper Blades (pair)',      'part_number' => 'WPR-PR',       'category' => 'Wipers',    'quantity_in_stock' => 20,  'unit_price' => 800,   'unit' => 'pcs'],
            ['name' => 'Drive Belt',               'part_number' => 'BLT-DRV',      'category' => 'Belt',      'quantity_in_stock' => 10,  'unit_price' => 1500,  'unit' => 'pcs'],
            ['name' => 'Timing Belt',              'part_number' => 'BLT-TIM',      'category' => 'Belt',      'quantity_in_stock' => 8,   'unit_price' => 3500,  'unit' => 'pcs'],
            ['name' => 'Battery (NS60)',           'part_number' => 'BAT-NS60',     'category' => 'Electrical','quantity_in_stock' => 5,   'unit_price' => 8500,  'unit' => 'pcs'],
            ['name' => 'Grease (Chassis)',         'part_number' => 'GRS-CHAS',     'category' => 'Lubricant', 'quantity_in_stock' => 10,  'unit_price' => 200,   'unit' => 'kg'],
        ];

        foreach ($parts as $part) {
            Part::create($part);
        }
    }
}
