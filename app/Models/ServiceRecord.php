<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRecord extends Model
{
    protected $fillable = [
        'garage_entry_no', 'vehicle_id', 'customer_id', 'service_type',
        'mileage_at_service', 'next_service_mileage', 'service_date',
        'next_service_date', 'status', 'notes', 'total_labour_cost',
    ];

    protected $casts = [
        'service_date'      => 'date',
        'next_service_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (ServiceRecord $record) {
            $record->garage_entry_no = 'GE-' . strtoupper(uniqid());
        });
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ServiceChecklistItem::class);
    }

    public function serviceParts(): HasMany
    {
        return $this->hasMany(ServicePart::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function isPaid(): bool
    {
        return $this->payments()->where('status', 'Paid')->exists();
    }

    public function getPaymentStatusAttribute(): string
    {
        $payment = $this->payments()->latest()->first();
        if (! $payment) return 'Unpaid';
        return $payment->status;
    }

    public function getTotalPartsAmountAttribute(): float
    {
        return $this->serviceParts->sum(fn($sp) => $sp->quantity_used * $sp->unit_price_at_service);
    }

    public function getGrandTotalAttribute(): float
    {
        return $this->total_labour_cost + $this->total_parts_amount;
    }
}
