<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceChecklistItem extends Model
{
    protected $fillable = [
        'service_record_id', 'item_name', 'status', 'remarks', 'sort_order',
    ];

    public function serviceRecord(): BelongsTo
    {
        return $this->belongsTo(ServiceRecord::class);
    }
}
