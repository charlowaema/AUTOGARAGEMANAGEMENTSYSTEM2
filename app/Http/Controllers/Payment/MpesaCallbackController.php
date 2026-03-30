<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaCallbackController extends Controller
{
    /**
     * Safaricom will POST the payment result to this endpoint.
     * URL: /api/mpesa/callback
     * This route must be excluded from CSRF verification.
     */
    public function callback(Request $request)
    {
        Log::info('M-Pesa Callback received', ['body' => $request->all()]);

        $body   = $request->all();
        $result = MpesaService::parseCallback($body);

        if (empty($result['checkout_request_id'])) {
            Log::warning('M-Pesa Callback: missing CheckoutRequestID');
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        $payment = Payment::where('checkout_request_id', $result['checkout_request_id'])->first();

        if (! $payment) {
            Log::warning('M-Pesa Callback: no payment found for CheckoutRequestID ' . $result['checkout_request_id']);
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        if ($result['success']) {
            $payment->update([
                'status'          => 'Paid',
                'mpesa_reference' => $result['mpesa_reference'],
                'paid_at'         => now(),
                'mpesa_response'  => json_encode($body),
            ]);

            Log::info('M-Pesa payment confirmed', [
                'payment_id'      => $payment->id,
                'mpesa_reference' => $result['mpesa_reference'],
                'amount'          => $result['amount'],
            ]);
        } else {
            $payment->update([
                'status'         => 'Failed',
                'notes'          => $result['message'] ?? 'Payment declined',
                'mpesa_response' => json_encode($body),
            ]);

            Log::info('M-Pesa payment failed', [
                'payment_id'  => $payment->id,
                'result_code' => $result['result_code'] ?? null,
                'message'     => $result['message'] ?? null,
            ]);
        }

        // Always return 200 with this body to Safaricom
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
}
