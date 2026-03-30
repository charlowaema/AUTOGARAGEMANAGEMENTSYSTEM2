<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'service_record_id',
        'customer_id',
        'amount',
        'method',
        'status',
        'phone_number',
        'mpesa_reference',
        'checkout_request_id',
        'merchant_request_id',
        'mpesa_response',
        'receipt_number',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount'  => 'decimal:2',
    ];

    // ── Boot: auto-generate receipt number ───────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            $payment->receipt_number = 'RCP-' . strtoupper(substr(uniqid(), -6)) . '-' . date('ymd');
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────
    public function serviceRecord(): BelongsTo
    {
        return $this->belongsTo(ServiceRecord::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    public function isPaid(): bool
    {
        return $this->status === 'Paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'Failed';
    }

    public function isMpesa(): bool
    {
        return $this->method === 'M-Pesa';
    }

    public function isCash(): bool
    {
        return $this->method === 'Cash';
    }

    /**
     * Format phone for Daraja API: 07XXXXXXXX → 2547XXXXXXXX
     */
    public static function formatPhoneForDaraja(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }
        if (str_starts_with($phone, '+')) {
            $phone = ltrim($phone, '+');
        }
        return $phone;
    }
}
