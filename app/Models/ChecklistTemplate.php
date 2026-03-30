<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistTemplate extends Model
{
    protected $fillable = ['service_type', 'item_name', 'sort_order'];

    /**
     * Get checklist items for a given service type from the database.
     * Falls back to hardcoded defaults if the table is empty (e.g. fresh install).
     */
    public static function itemsFor(string $serviceType): array
    {
        $items = static::where('service_type', $serviceType)
            ->orderBy('sort_order')
            ->pluck('item_name')
            ->toArray();

        if (! empty($items)) {
            return $items;
        }

        // Fallback defaults (used only if DB has no templates yet)
        return $serviceType === 'Regular'
            ? static::defaultRegularItems()
            : static::defaultFullItems();
    }

    public static function regularItems(): array
    {
        return static::itemsFor('Regular');
    }

    public static function fullItems(): array
    {
        return static::itemsFor('Full');
    }

    // ── Hardcoded defaults (fallback only) ────────────────────────────────────

    public static function defaultRegularItems(): array
    {
        return [
            'Change oil & filter',
            'Check all fluids',
            'Check tires (pressure & condition)',
            'Check all belts & hoses',
            'Lubricate chassis',
        ];
    }

    public static function defaultFullItems(): array
    {
        return [
            'Check oil & filter',
            'Check all fluids',
            'Check tires (pressure & condition)',
            'Check all belts & hoses',
            'Lubricate chassis',
            'Check brakes & wheel bearings',
            'Check temperature for engine thermostat',
            'Replace plugs',
            'Inspect cooling system hoses & fluid',
            'Check for leaks or other problems',
            'Change air filter',
            'Check battery',
            'Check brake fluid',
            'Washer fluid',
            'Wiper blades',
            'Lights',
            'Exhaust',
            'Shock absorbers',
            'Transmission fluid',
        ];
    }
}
