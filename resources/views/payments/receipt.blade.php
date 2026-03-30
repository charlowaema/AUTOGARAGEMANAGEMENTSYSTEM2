@extends('layouts.app')
@section('title', 'Receipt — ' . $payment->receipt_number)
@section('page-title', 'Payment Receipt')
@section('breadcrumb', 'Payments / Receipt')

@section('page-actions')
    <button onclick="window.print()" class="btn-secondary no-print">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Receipt
    </button>
    <a href="{{ route('services.report', $payment->serviceRecord) }}" class="btn-secondary no-print">Service Report</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- ── RECEIPT CARD ── --}}
    <div class="card overflow-hidden" id="receipt-paper">

        {{-- Header --}}
        <div style="background:var(--brand);padding:1.5rem 2rem;" class="print-header">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:0.875rem;">
                    <div style="width:2.75rem;height:2.75rem;background:rgba(255,255,255,0.2);border-radius:0.5rem;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:1.375rem;height:1.375rem;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h10l2-2zM13 6l3 4h3a1 1 0 011 1v4l-2 2h-1"/></svg>
                    </div>
                    <div>
                        <p style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:1.25rem;color:white;line-height:1;">AGMS</p>
                        <p style="font-size:0.6875rem;color:rgba(255,255,255,0.7);">Auto Garage Management System</p>
                    </div>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:0.6875rem;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:0.05em;">Payment Receipt</p>
                    <p style="font-family:'JetBrains Mono',monospace;font-weight:700;font-size:1rem;color:white;margin-top:0.25rem;">{{ $payment->receipt_number }}</p>
                </div>
            </div>
        </div>

        {{-- Status Banner --}}
        @if($payment->isPaid())
        <div style="background:rgba(34,197,94,0.08);border-bottom:1px solid rgba(34,197,94,0.2);padding:0.875rem 2rem;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:0.5rem;color:#16a34a;font-weight:700;font-size:0.875rem;">
                <svg style="width:1.125rem;height:1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                PAYMENT SUCCESSFUL
            </div>
            <span style="font-size:0.75rem;color:var(--text-muted);">{{ $payment->paid_at?->format('d M Y, h:i A') }}</span>
        </div>
        @endif

        <div style="padding:1.75rem 2rem;">

            {{-- Two column info --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
                <div>
                    <p style="font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:0.75rem;">Customer Details</p>
                    <p style="font-weight:600;font-size:0.9375rem;color:var(--text-primary);">{{ $payment->customer->name }}</p>
                    <p style="font-size:0.8125rem;color:var(--text-secondary);margin-top:0.25rem;">{{ $payment->customer->phone }}</p>
                    <p style="font-size:0.8125rem;color:var(--text-secondary);">{{ $payment->customer->email }}</p>
                </div>
                <div>
                    <p style="font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:0.75rem;">Service Details</p>
                    <p style="font-weight:600;font-size:0.9375rem;color:var(--text-primary);">{{ $payment->serviceRecord->garage_entry_no }}</p>
                    <p style="font-size:0.8125rem;color:var(--text-secondary);margin-top:0.25rem;">
                        {{ $payment->serviceRecord->vehicle->plate_number }} —
                        {{ $payment->serviceRecord->vehicle->make->name }}
                        {{ $payment->serviceRecord->vehicle->model->name }}
                    </p>
                    <p style="font-size:0.8125rem;color:var(--text-secondary);">{{ ucfirst($payment->serviceRecord->service_type) }} Service</p>
                </div>
            </div>

            {{-- Line items --}}
            <div style="border-radius:0.5rem;overflow:hidden;border:1px solid var(--border);margin-bottom:1.5rem;">
                <table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">
                    <thead>
                        <tr style="background:var(--bg-elevated);border-bottom:1px solid var(--border);">
                            <th style="padding:0.625rem 1rem;text-align:left;font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);">Description</th>
                            <th style="padding:0.625rem 1rem;text-align:right;font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payment->serviceRecord->serviceParts as $sp)
                        <tr style="border-bottom:1px solid var(--border);">
                            <td style="padding:0.625rem 1rem;color:var(--text-primary);">{{ $sp->part->name ?? 'Part' }} <span style="color:var(--text-muted);font-size:0.75rem;">(×{{ $sp->quantity_used }})</span></td>
                            <td style="padding:0.625rem 1rem;text-align:right;color:var(--text-primary);">KES {{ number_format($sp->quantity_used * $sp->unit_price_at_service, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr style="border-bottom:1px solid var(--border);">
                            <td style="padding:0.625rem 1rem;color:var(--text-primary);">Labour Cost</td>
                            <td style="padding:0.625rem 1rem;text-align:right;color:var(--text-primary);">KES {{ number_format($payment->serviceRecord->total_labour_cost, 2) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background:var(--bg-elevated);">
                            <td style="padding:0.875rem 1rem;font-weight:700;color:var(--text-primary);">TOTAL</td>
                            <td style="padding:0.875rem 1rem;text-align:right;font-family:'Barlow Condensed',sans-serif;font-weight:800;font-size:1.125rem;color:var(--brand);">KES {{ number_format($payment->serviceRecord->grand_total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Payment method block --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;">
                <div style="padding:1rem;border-radius:0.5rem;border:1px solid var(--border);background:var(--bg-elevated);">
                    <p style="font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:0.5rem;">Payment Method</p>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        @if($payment->isMpesa())
                        <div style="width:1.75rem;height:1.75rem;background:#009640;border-radius:0.25rem;display:flex;align-items:center;justify-content:center;font-weight:900;color:white;font-size:0.5rem;letter-spacing:-0.02em;line-height:1.1;text-align:center;">M<br>PESA</div>
                        <span style="font-weight:600;color:var(--text-primary);">M-Pesa</span>
                        @else
                        <svg style="width:1.25rem;height:1.25rem;color:var(--text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span style="font-weight:600;color:var(--text-primary);">Cash</span>
                        @endif
                    </div>
                </div>
                <div style="padding:1rem;border-radius:0.5rem;border:1px solid var(--border);background:var(--bg-elevated);">
                    <p style="font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--text-muted);margin-bottom:0.5rem;">
                        @if($payment->isMpesa()) M-Pesa Reference @else Amount Received @endif
                    </p>
                    <p style="font-weight:700;font-family:'JetBrains Mono',monospace;color:var(--text-primary);">
                        @if($payment->isMpesa())
                            {{ $payment->mpesa_reference ?? '—' }}
                        @else
                            KES {{ number_format($payment->amount, 2) }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- Signature lines --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-top:2rem;padding-top:1.5rem;border-top:1px dashed var(--border);">
                <div style="text-align:center;">
                    <div style="height:2.5rem;border-bottom:1px solid var(--border-input);margin-bottom:0.5rem;"></div>
                    <p style="font-size:0.6875rem;color:var(--text-muted);">Cashier Signature</p>
                </div>
                <div style="text-align:center;">
                    <div style="height:2.5rem;border-bottom:1px solid var(--border-input);margin-bottom:0.5rem;"></div>
                    <p style="font-size:0.6875rem;color:var(--text-muted);">Customer Signature</p>
                </div>
            </div>

            <p style="text-align:center;font-size:0.6875rem;color:var(--text-muted);margin-top:1.5rem;">
                Thank you for your business! — AGMS Auto Garage Management System
            </p>
        </div>
    </div>

    <div class="flex gap-3 justify-center mt-6 no-print">
        <a href="{{ route('payments.index') }}" class="btn-secondary">All Payments</a>
        <a href="{{ route('services.show', $payment->serviceRecord) }}" class="btn-secondary">Back to Service</a>
    </div>
</div>
@endsection
