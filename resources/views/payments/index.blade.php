@extends('layouts.app')
@section('title', 'Payment History')
@section('page-title', 'Payment History')
@section('breadcrumb', 'Finance / Payments')

@section('content')

{{-- Summary Totals --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="card p-5 flex items-center gap-4">
        <div style="width:2.75rem;height:2.75rem;border-radius:0.75rem;background:rgba(34,197,94,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg style="width:1.25rem;height:1.25rem;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div>
            <p style="font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);">Cash Collected</p>
            <p style="font-family:'Barlow Condensed',sans-serif;font-weight:800;font-size:1.375rem;color:var(--text-primary);margin-top:0.125rem;">KES {{ number_format($totals['cash'], 2) }}</p>
        </div>
    </div>
    <div class="card p-5 flex items-center gap-4">
        <div style="width:2.75rem;height:2.75rem;border-radius:0.75rem;background:rgba(0,150,64,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:900;color:#009640;font-size:0.5rem;letter-spacing:-0.02em;line-height:1.1;text-align:center;">M<br>PESA</div>
        <div>
            <p style="font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);">M-Pesa Collected</p>
            <p style="font-family:'Barlow Condensed',sans-serif;font-weight:800;font-size:1.375rem;color:var(--text-primary);margin-top:0.125rem;">KES {{ number_format($totals['mpesa'], 2) }}</p>
        </div>
    </div>
    <div class="card p-5 flex items-center gap-4" style="border-color:rgba(249,115,22,0.3);">
        <div style="width:2.75rem;height:2.75rem;border-radius:0.75rem;background:rgba(249,115,22,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg style="width:1.25rem;height:1.25rem;color:var(--brand);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p style="font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);">Total Revenue</p>
            <p style="font-family:'Barlow Condensed',sans-serif;font-weight:800;font-size:1.375rem;color:var(--brand);margin-top:0.125rem;">KES {{ number_format($totals['total'], 2) }}</p>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card p-4 mb-6">
    <form method="GET" action="{{ route('payments.index') }}" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:10rem;">
            <label class="form-label">Method</label>
            <select name="method" class="form-input">
                <option value="">All Methods</option>
                <option value="Cash"   {{ request('method') === 'Cash'   ? 'selected' : '' }}>Cash</option>
                <option value="M-Pesa" {{ request('method') === 'M-Pesa' ? 'selected' : '' }}>M-Pesa</option>
            </select>
        </div>
        <div style="flex:1;min-width:10rem;">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">All Statuses</option>
                <option value="Paid"    {{ request('status') === 'Paid'    ? 'selected' : '' }}>Paid</option>
                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Failed"  {{ request('status') === 'Failed'  ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        <a href="{{ route('payments.index') }}" class="btn-secondary">Clear</a>
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:1px solid var(--border);">
                    <th style="padding:0.75rem 1.25rem;text-align:left;font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-muted);">Receipt</th>
                    <th style="padding:0.75rem 1.25rem;text-align:left;font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-muted);">Customer</th>
                    <th style="padding:0.75rem 1.25rem;text-align:left;font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-muted);">Service</th>
                    <th style="padding:0.75rem 1.25rem;text-align:left;font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-muted);">Method</th>
                    <th style="padding:0.75rem 1.25rem;text-align:right;font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-muted);">Amount</th>
                    <th style="padding:0.75rem 1.25rem;text-align:left;font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-muted);">Status</th>
                    <th style="padding:0.75rem 1.25rem;text-align:left;font-size:0.625rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--text-muted);">Date</th>
                    <th style="padding:0.75rem 1.25rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr class="table-row">
                    <td style="padding:0.75rem 1.25rem;">
                        <span style="font-family:'JetBrains Mono',monospace;font-size:0.75rem;color:var(--brand);">{{ $payment->receipt_number }}</span>
                    </td>
                    <td style="padding:0.75rem 1.25rem;font-weight:500;color:var(--text-primary);">{{ $payment->customer->name ?? '—' }}</td>
                    <td style="padding:0.75rem 1.25rem;">
                        <span style="font-family:'JetBrains Mono',monospace;font-size:0.75rem;color:var(--text-secondary);">{{ $payment->serviceRecord->garage_entry_no ?? '—' }}</span>
                    </td>
                    <td style="padding:0.75rem 1.25rem;">
                        @if($payment->isMpesa())
                            <span style="display:inline-flex;align-items:center;gap:0.375rem;font-size:0.75rem;font-weight:700;color:#009640;">
                                <span style="width:1.125rem;height:1.125rem;background:#009640;border-radius:0.2rem;display:inline-flex;align-items:center;justify-content:center;font-weight:900;color:white;font-size:0.4rem;line-height:1.1;text-align:center;flex-shrink:0;">M<br>P</span>
                                M-Pesa
                            </span>
                            @if($payment->mpesa_reference)
                            <p style="font-family:'JetBrains Mono',monospace;font-size:0.6875rem;color:var(--text-muted);margin-top:0.125rem;">{{ $payment->mpesa_reference }}</p>
                            @endif
                        @else
                            <span style="display:inline-flex;align-items:center;gap:0.375rem;font-size:0.75rem;font-weight:600;color:var(--text-secondary);">
                                <svg style="width:0.875rem;height:0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Cash
                            </span>
                        @endif
                    </td>
                    <td style="padding:0.75rem 1.25rem;text-align:right;font-weight:700;font-family:'Barlow Condensed',sans-serif;font-size:1rem;color:var(--text-primary);">
                        KES {{ number_format($payment->amount, 2) }}
                    </td>
                    <td style="padding:0.75rem 1.25rem;">
                        @if($payment->isPaid())
                            <span style="display:inline-flex;align-items:center;padding:0.125rem 0.625rem;border-radius:9999px;font-size:0.6875rem;font-weight:700;background:rgba(34,197,94,0.1);color:#16a34a;border:1px solid rgba(34,197,94,0.2);">Paid</span>
                        @elseif($payment->isPending())
                            <span style="display:inline-flex;align-items:center;padding:0.125rem 0.625rem;border-radius:9999px;font-size:0.6875rem;font-weight:700;background:rgba(234,179,8,0.1);color:#ca8a04;border:1px solid rgba(234,179,8,0.2);">Pending</span>
                        @else
                            <span style="display:inline-flex;align-items:center;padding:0.125rem 0.625rem;border-radius:9999px;font-size:0.6875rem;font-weight:700;background:rgba(239,68,68,0.1);color:#dc2626;border:1px solid rgba(239,68,68,0.2);">Failed</span>
                        @endif
                    </td>
                    <td style="padding:0.75rem 1.25rem;font-size:0.75rem;color:var(--text-muted);">
                        {{ $payment->paid_at?->format('d M Y, H:i') ?? $payment->created_at->format('d M Y') }}
                    </td>
                    <td style="padding:0.75rem 1.25rem;">
                        @if($payment->isPaid())
                        <a href="{{ route('payments.receipt', $payment) }}" class="btn-secondary" style="padding:0.25rem 0.625rem;font-size:0.75rem;">Receipt</a>
                        @elseif($payment->isPending())
                        <a href="{{ route('payments.mpesa.pending', $payment) }}" class="btn-secondary" style="padding:0.25rem 0.625rem;font-size:0.75rem;">Check</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding:3rem;text-align:center;color:var(--text-muted);font-size:0.875rem;">No payments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--border);">{{ $payments->links() }}</div>
    @endif
</div>
@endsection
