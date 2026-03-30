<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends Model
{
    protected $fillable = [
        'name', 'part_number', 'category',
        'quantity_in_stock', 'unit_price', 'unit',
    ];

    public function serviceParts(): HasMany
    {
        return $this->hasMany(ServicePart::class);
    }
}
