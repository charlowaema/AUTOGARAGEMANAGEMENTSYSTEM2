<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePart extends Model
{
    protected $fillable = [
        'service_record_id', 'part_id', 'quantity_used', 'unit_price_at_service',
    ];

    public function serviceRecord(): BelongsTo
    {
        return $this->belongsTo(ServiceRecord::class);
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function getLineAmountAttribute(): float
    {
        return $this->quantity_used * $this->unit_price_at_service;
    }
}
