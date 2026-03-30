<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ServiceRecord;
use App\Services\MpesaService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // ── Show payment screen ───────────────────────────────────────────────────
    public function show(ServiceRecord $service)
    {
        $service->load(['vehicle.make', 'vehicle.model', 'customer', 'serviceParts', 'payments']);
        $existingPayment = $service->payments()->latest()->first();

        return view('payments.show', compact('service', 'existingPayment'));
    }

    // ── Process Cash Payment ──────────────────────────────────────────────────
    public function payCash(Request $request, ServiceRecord $service)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'notes'  => 'nullable|string|max:500',
        ]);

        // Cancel any pending M-Pesa attempts for this service
        $service->payments()->where('status', 'Pending')->update(['status' => 'Failed']);

        $payment = Payment::create([
            'service_record_id' => $service->id,
            'customer_id'       => $service->customer_id,
            'amount'            => $request->amount,
            'method'            => 'Cash',
            'status'            => 'Paid',
            'paid_at'           => now(),
            'notes'             => $request->notes,
        ]);

        return redirect()->route('payments.receipt', $payment)
                         ->with('success', 'Cash payment of KES ' . number_format($request->amount, 2) . ' recorded.');
    }

    // ── Initiate M-Pesa STK Push ──────────────────────────────────────────────
    public function initiateMpesa(Request $request, ServiceRecord $service)
    {
        $request->validate([
            'phone_number' => ['required', 'string', 'regex:/^(?:254|\+254|0)([17]\d{8})$/'],
            'amount'       => 'required|numeric|min:1',
        ]);

        $phone = Payment::formatPhoneForDaraja($request->phone_number);

        // Cancel stale pending attempts
        $service->payments()->where('status', 'Pending')->where('method', 'M-Pesa')->update(['status' => 'Failed']);

        // Create a pending payment record immediately
        $payment = Payment::create([
            'service_record_id' => $service->id,
            'customer_id'       => $service->customer_id,
            'amount'            => $request->amount,
            'method'            => 'M-Pesa',
            'status'            => 'Pending',
            'phone_number'      => $phone,
        ]);

        // Attempt STK Push
        $mpesa  = new MpesaService();
        $result = $mpesa->stkPush(
            phone:      $phone,
            amount:     $request->amount,
            accountRef: $service->garage_entry_no,
            description: 'AGMS Service ' . $service->garage_entry_no,
        );

        if ($result['success']) {
            $payment->update([
                'checkout_request_id'  => $result['checkout_request_id'],
                'merchant_request_id'  => $result['merchant_request_id'],
                'mpesa_response'       => json_encode($result['raw']),
            ]);

            return redirect()->route('payments.mpesa.pending', $payment)
                             ->with('success', 'STK Push sent to ' . $request->phone_number . '. Ask customer to enter M-Pesa PIN.');
        }

        // STK Push failed — mark payment as failed
        $payment->update(['status' => 'Failed', 'notes' => $result['message']]);

        return back()->with('error', 'M-Pesa Error: ' . $result['message']);
    }

    // ── M-Pesa Pending / Polling Screen ──────────────────────────────────────
    public function mpesaPending(Payment $payment)
    {
        $payment->load('serviceRecord.vehicle', 'serviceRecord.customer');
        return view('payments.mpesa-pending', compact('payment'));
    }

    // ── Poll STK Push status (AJAX) ───────────────────────────────────────────
    public function pollMpesaStatus(Payment $payment)
    {
        // Already resolved
        if ($payment->status !== 'Pending') {
            return response()->json([
                'status'      => $payment->status,
                'redirect'    => $payment->isPaid()
                    ? route('payments.receipt', $payment)
                    : route('payments.show', $payment->service_record_id),
            ]);
        }

        // Query Daraja for status
        if ($payment->checkout_request_id) {
            $mpesa  = new MpesaService();
            $result = $mpesa->queryStkStatus($payment->checkout_request_id);

            if ($result['success']) {
                $data       = $result['data'];
                $resultCode = $data['ResultCode'] ?? null;

                if ($resultCode === '0' || $resultCode === 0) {
                    $payment->update([
                        'status'          => 'Paid',
                        'mpesa_reference' => $data['MpesaReceiptNumber'] ?? null,
                        'paid_at'         => now(),
                        'mpesa_response'  => json_encode($data),
                    ]);
                    return response()->json([
                        'status'   => 'Paid',
                        'redirect' => route('payments.receipt', $payment),
                    ]);
                }

                if (isset($resultCode) && $resultCode !== '1032') {
                    // 1032 = request cancelled — otherwise mark failed
                    $payment->update(['status' => 'Failed', 'notes' => $data['ResultDesc'] ?? 'Failed']);
                    return response()->json([
                        'status'   => 'Failed',
                        'redirect' => route('payments.show', $payment->service_record_id),
                    ]);
                }
            }
        }

        return response()->json(['status' => 'Pending']);
    }

    // ── Manual M-Pesa confirmation (enter reference manually) ────────────────
    public function confirmMpesa(Request $request, Payment $payment)
    {
        $request->validate([
            'mpesa_reference' => 'required|string|min:6|max:20',
        ]);

        $payment->update([
            'status'          => 'Paid',
            'mpesa_reference' => strtoupper($request->mpesa_reference),
            'paid_at'         => now(),
        ]);

        return redirect()->route('payments.receipt', $payment)
                         ->with('success', 'M-Pesa payment confirmed with reference ' . strtoupper($request->mpesa_reference));
    }

    // ── Receipt ───────────────────────────────────────────────────────────────
    public function receipt(Payment $payment)
    {
        $payment->load('serviceRecord.vehicle.make', 'serviceRecord.vehicle.model', 'serviceRecord.customer', 'serviceRecord.serviceParts');
        return view('payments.receipt', compact('payment'));
    }

    // ── Payment history (admin) ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $payments = Payment::with(['serviceRecord', 'customer'])
            ->when($request->method, fn($q) => $q->where('method', $request->method))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(25);

        $totals = [
            'cash'  => Payment::where('status', 'Paid')->where('method', 'Cash')->sum('amount'),
            'mpesa' => Payment::where('status', 'Paid')->where('method', 'M-Pesa')->sum('amount'),
            'total' => Payment::where('status', 'Paid')->sum('amount'),
        ];

        return view('payments.index', compact('payments', 'totals'));
    }
}
