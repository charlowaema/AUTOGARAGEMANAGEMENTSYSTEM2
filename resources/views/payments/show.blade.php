@extends('layouts.app')
@section('title', 'Payment')
@section('page-title', 'Payment — ' . $service->garage_entry_no)
@section('breadcrumb', 'Services / Payment')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Service Summary Card --}}
    <div class="card p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest" style="color:var(--text-muted)">Service Entry</p>
                <p class="font-mono text-xl font-bold mt-0.5" style="color:var(--brand)">{{ $service->garage_entry_no }}</p>
            </div>
            <div class="text-right">
                @if($service->isPaid())
                    <span style="display:inline-flex;align-items:center;gap:0.375rem;background:rgba(34,197,94,0.1);color:#16a34a;border:1px solid rgba(34,197,94,0.2);padding:0.375rem 0.875rem;border-radius:9999px;font-size:0.75rem;font-weight:700;">
                        <svg style="width:0.875rem;height:0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        PAID
                    </span>
                @else
                    <span style="display:inline-flex;align-items:center;gap:0.375rem;background:rgba(249,115,22,0.1);color:#f97316;border:1px solid rgba(249,115,22,0.2);padding:0.375rem 0.875rem;border-radius:9999px;font-size:0.75rem;font-weight:700;">
                        AWAITING PAYMENT
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-5 pt-5" style="border-top:1px solid var(--border)">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Customer</p>
                <p class="text-sm font-semibold mt-1" style="color:var(--text-primary)">{{ $service->customer->name }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Vehicle</p>
                <p class="text-sm font-semibold mt-1" style="color:var(--text-primary)">{{ $service->vehicle->plate_number }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Parts Total</p>
                <p class="text-sm font-semibold mt-1" style="color:var(--text-primary)">KES {{ number_format($service->total_parts_amount, 2) }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Labour</p>
                <p class="text-sm font-semibold mt-1" style="color:var(--text-primary)">KES {{ number_format($service->total_labour_cost, 2) }}</p>
            </div>
        </div>

        {{-- Grand Total --}}
        <div class="mt-4 pt-4 flex items-center justify-between rounded-xl px-5 py-4" style="background:rgba(249,115,22,0.08);border:1px solid rgba(249,115,22,0.2);">
            <p class="font-bold text-base" style="color:var(--text-primary)">TOTAL AMOUNT DUE</p>
            <p style="font-family:'Barlow Condensed',sans-serif;font-weight:800;font-size:1.75rem;color:var(--brand);">KES {{ number_format($service->grand_total, 2) }}</p>
        </div>
    </div>

    @if($service->isPaid())
    {{-- Already paid --}}
    <div class="card p-8 text-center">
        <div style="width:4rem;height:4rem;background:rgba(34,197,94,0.1);border-radius:9999px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <svg style="width:2rem;height:2rem;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3 style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:1.5rem;color:var(--text-primary);">Payment Complete</h3>
        <p class="mt-2 text-sm" style="color:var(--text-muted)">This service has already been paid.</p>
        <div class="flex justify-center gap-3 mt-6">
            @if($existingPayment)
            <a href="{{ route('payments.receipt', $existingPayment) }}" class="btn-primary">View Receipt</a>
            @endif
            <a href="{{ route('services.report', $service) }}" class="btn-secondary">Service Report</a>
        </div>
    </div>
    @else

    {{-- Payment Method Tabs --}}
    <div class="card overflow-hidden">
        {{-- Tab Buttons --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;border-bottom:1px solid var(--border);">
            <button id="tab-cash" onclick="switchTab('cash')"
                class="tab-btn active-tab py-4 text-sm font-bold uppercase tracking-wider transition-all"
                style="border-right:1px solid var(--border);">
                <span style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Cash
                </span>
            </button>
            <button id="tab-mpesa" onclick="switchTab('mpesa')"
                class="tab-btn py-4 text-sm font-bold uppercase tracking-wider transition-all">
                <span style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    M-Pesa
                </span>
            </button>
        </div>

        {{-- CASH TAB --}}
        <div id="panel-cash" class="p-6">
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;padding:1rem;border-radius:0.75rem;background:rgba(34,197,94,0.06);border:1px solid rgba(34,197,94,0.15);">
                <svg style="width:1.25rem;height:1.25rem;color:#16a34a;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm" style="color:var(--text-secondary)">Record cash received from customer. A receipt will be generated immediately.</p>
            </div>

            <form method="POST" action="{{ route('payments.cash', $service) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Amount Received (KES) *</label>
                        <input type="number" name="amount" step="0.01" min="0"
                            value="{{ $service->grand_total }}"
                            class="form-input text-lg font-bold" required>
                        <p class="text-xs mt-1" style="color:var(--text-muted)">Total due: KES {{ number_format($service->grand_total, 2) }}</p>
                    </div>
                    <div>
                        <label class="form-label">Notes (optional)</label>
                        <input type="text" name="notes" class="form-input" placeholder="e.g. Received exact amount, partial payment…">
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="submit" class="btn-primary flex-1 justify-center py-3 text-base">
                        <svg style="width:1.125rem;height:1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Confirm Cash Payment
                    </button>
                    <a href="{{ route('services.show', $service) }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        {{-- MPESA TAB --}}
        <div id="panel-mpesa" class="p-6 hidden">
            {{-- M-Pesa branding header --}}
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;padding:1rem 1.25rem;border-radius:0.75rem;background:rgba(0,150,64,0.06);border:1px solid rgba(0,150,64,0.2);">
                <div style="width:2.75rem;height:2.75rem;background:#009640;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:900;color:white;font-size:0.625rem;letter-spacing:-0.03em;line-height:1.1;text-align:center;">M<br>PESA</div>
                <div>
                    <p class="text-sm font-bold" style="color:var(--text-primary)">Lipa Na M-Pesa</p>
                    <p class="text-xs" style="color:var(--text-muted)">Customer will receive a payment prompt on their phone.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('payments.mpesa.initiate', $service) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Customer Phone Number *</label>
                        <input type="tel" name="phone_number" class="form-input" placeholder="e.g. 0712345678 or 254712345678" required
                            value="{{ $service->customer->phone ?? '' }}">
                        <p class="text-xs mt-1" style="color:var(--text-muted)">Safaricom number registered with M-Pesa</p>
                    </div>
                    <div>
                        <label class="form-label">Amount (KES) *</label>
                        <input type="number" name="amount" step="1" min="1"
                            value="{{ ceil($service->grand_total) }}"
                            class="form-input text-lg font-bold" required>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="btn-primary flex-1 justify-center py-3 text-base" style="background:#009640;" onmouseover="this.style.background='#007a34'" onmouseout="this.style.background='#009640'">
                        <svg style="width:1.125rem;height:1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Send STK Push
                    </button>
                    <a href="{{ route('services.show', $service) }}" class="btn-secondary">Cancel</a>
                </div>
            </form>

            {{-- Manual confirmation fallback --}}
            @if($existingPayment && $existingPayment->isMpesa() && $existingPayment->isPending())
            <div class="mt-6 pt-6" style="border-top:1px solid var(--border);">
                <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:var(--text-muted)">Already sent? Enter reference manually</p>
                <form method="POST" action="{{ route('payments.mpesa.confirm', $existingPayment) }}" style="display:flex;gap:0.5rem;">
                    @csrf
                    <input type="text" name="mpesa_reference" class="form-input" placeholder="e.g. QKH7HJ83JK" maxlength="20" style="text-transform:uppercase;">
                    <button type="submit" class="btn-secondary whitespace-nowrap">Confirm</button>
                </form>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function switchTab(tab) {
    const panels = ['cash', 'mpesa'];
    panels.forEach(t => {
        document.getElementById('panel-' + t).classList.toggle('hidden', t !== tab);
        const btn = document.getElementById('tab-' + t);
        if (t === tab) {
            btn.style.background = 'rgba(249,115,22,0.1)';
            btn.style.color = '#f97316';
            btn.style.borderBottom = '2px solid #f97316';
        } else {
            btn.style.background = '';
            btn.style.color = 'var(--text-muted)';
            btn.style.borderBottom = '';
        }
    });
}
// Init
switchTab('cash');
</script>
@endpush
