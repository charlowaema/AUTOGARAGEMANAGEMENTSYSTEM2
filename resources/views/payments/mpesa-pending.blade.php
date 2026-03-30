@extends('layouts.app')
@section('title', 'Awaiting M-Pesa Payment')
@section('page-title', 'Awaiting M-Pesa Payment')
@section('breadcrumb', 'Services / Payment / M-Pesa')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="card p-8 text-center">

        {{-- Animated spinner --}}
        <div id="status-icon" style="width:5rem;height:5rem;margin:0 auto 1.5rem;position:relative;">
            <div style="width:5rem;height:5rem;border-radius:9999px;background:rgba(0,150,64,0.1);display:flex;align-items:center;justify-content:center;">
                <div id="spinner" style="position:absolute;inset:0;border-radius:9999px;border:3px solid transparent;border-top-color:#009640;animation:spin 1s linear infinite;"></div>
                <span style="font-weight:900;color:#009640;font-size:0.75rem;letter-spacing:-0.03em;line-height:1.1;text-align:center;">M<br>PESA</span>
            </div>
        </div>

        <h2 id="status-title" style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:1.5rem;color:var(--text-primary);">Waiting for Customer</h2>
        <p id="status-message" class="mt-2 text-sm" style="color:var(--text-muted);">
            An M-Pesa prompt has been sent to <strong style="color:var(--text-primary);">{{ substr($payment->phone_number, 0, 6) }}XXXXXX</strong>.<br>
            Ask the customer to enter their PIN.
        </p>

        <div class="mt-6 p-4 rounded-xl" style="background:var(--bg-elevated);border:1px solid var(--border);">
            <div class="grid grid-cols-2 gap-3 text-left text-sm">
                <div>
                    <p class="text-xs font-semibold uppercase" style="color:var(--text-muted)">Amount</p>
                    <p class="font-bold mt-0.5" style="color:var(--text-primary)">KES {{ number_format($payment->amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase" style="color:var(--text-muted)">Reference</p>
                    <p class="font-bold mt-0.5 font-mono" style="color:var(--text-primary)">{{ $payment->serviceRecord->garage_entry_no }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase" style="color:var(--text-muted)">Customer</p>
                    <p class="font-bold mt-0.5" style="color:var(--text-primary)">{{ $payment->serviceRecord->customer->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase" style="color:var(--text-muted)">Status</p>
                    <p id="badge-status" class="font-bold mt-0.5" style="color:#f59e0b;">Pending…</p>
                </div>
            </div>
        </div>

        {{-- Manual entry fallback --}}
        <div class="mt-6 pt-6" style="border-top:1px solid var(--border);" id="manual-confirm-section">
            <p class="text-xs mb-3" style="color:var(--text-muted)">Customer confirmed payment on their phone? Enter the M-Pesa reference code:</p>
            <form method="POST" action="{{ route('payments.mpesa.confirm', $payment) }}" style="display:flex;gap:0.5rem;">
                @csrf
                <input type="text" name="mpesa_reference" class="form-input" placeholder="e.g. QKH7HJ83JK"
                    maxlength="20" style="text-transform:uppercase;letter-spacing:0.05em;" required>
                <button type="submit" class="btn-primary whitespace-nowrap" style="background:#009640;" onmouseover="this.style.background='#007a34'" onmouseout="this.style.background='#009640'">Confirm</button>
            </form>
        </div>

        <div class="mt-4 flex gap-3 justify-center">
            <a href="{{ route('payments.show', $payment->service_record_id) }}" class="btn-secondary text-sm">← Try Again</a>
            <a href="{{ route('services.show', $payment->service_record_id) }}" class="btn-secondary text-sm">Back to Service</a>
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@push('scripts')
<script>
const paymentId  = {{ $payment->id }};
const pollUrl    = '{{ route('payments.mpesa.poll', $payment) }}';
let   pollCount  = 0;
const maxPolls   = 24; // ~2 minutes at 5s intervals

function poll() {
    if (pollCount >= maxPolls) {
        document.getElementById('status-title').textContent   = 'Timed Out';
        document.getElementById('status-message').textContent = 'No confirmation received. Enter the reference manually if paid.';
        document.getElementById('spinner').style.display = 'none';
        document.getElementById('badge-status').textContent = 'Timed out';
        document.getElementById('badge-status').style.color = '#ef4444';
        return;
    }

    pollCount++;
    fetch(pollUrl)
        .then(r => r.json())
        .then(data => {
            if (data.status === 'Paid') {
                document.getElementById('status-title').textContent   = 'Payment Confirmed!';
                document.getElementById('status-message').textContent = 'M-Pesa payment received. Redirecting to receipt…';
                document.getElementById('spinner').style.borderTopColor = '#16a34a';
                document.getElementById('badge-status').textContent = 'Paid ✓';
                document.getElementById('badge-status').style.color = '#16a34a';
                setTimeout(() => window.location.href = data.redirect, 1500);
            } else if (data.status === 'Failed') {
                document.getElementById('status-title').textContent   = 'Payment Failed';
                document.getElementById('status-message').textContent = 'The payment was declined or cancelled. Please try again.';
                document.getElementById('spinner').style.borderTopColor = '#ef4444';
                document.getElementById('badge-status').textContent = 'Failed';
                document.getElementById('badge-status').style.color = '#ef4444';
            } else {
                setTimeout(poll, 5000); // poll every 5 seconds
            }
        })
        .catch(() => setTimeout(poll, 5000));
}

// Start polling after 3 seconds
setTimeout(poll, 3000);
</script>
@endpush
@endsection
