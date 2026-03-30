<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected string $baseUrl;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $shortcode;
    protected string $passkey;
    protected string $callbackUrl;
    protected string $transactionType;

    public function __construct()
    {
        $env               = config('mpesa.env', 'sandbox');
        $this->baseUrl     = config("mpesa.base_url.{$env}");
        $this->consumerKey    = config('mpesa.consumer_key');
        $this->consumerSecret = config('mpesa.consumer_secret');
        $this->shortcode      = config('mpesa.shortcode');
        $this->passkey        = config('mpesa.passkey');
        $this->callbackUrl    = config('mpesa.callback_url');
        $this->transactionType = config('mpesa.transaction_type');
    }

    // ── Step 1: Get OAuth Access Token ───────────────────────────────────────
    public function getAccessToken(): ?string
    {
        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->get($this->baseUrl . config('mpesa.endpoints.oauth'));

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('M-Pesa OAuth failed', ['response' => $response->body()]);
            return null;

        } catch (\Exception $e) {
            Log::error('M-Pesa OAuth exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // ── Step 2: Initiate STK Push ─────────────────────────────────────────────
    public function stkPush(string $phone, float $amount, string $accountRef, string $description = 'Service Payment'): array
    {
        $token = $this->getAccessToken();

        if (! $token) {
            return ['success' => false, 'message' => 'Failed to get M-Pesa access token. Check your Daraja credentials.'];
        }

        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

        try {
            $response = Http::withToken($token)
                ->post($this->baseUrl . config('mpesa.endpoints.stk_push'), [
                    'BusinessShortCode' => $this->shortcode,
                    'Password'          => $password,
                    'Timestamp'         => $timestamp,
                    'TransactionType'   => $this->transactionType,
                    'Amount'            => (int) ceil($amount), // M-Pesa requires integer
                    'PartyA'            => $phone,
                    'PartyB'            => $this->shortcode,
                    'PhoneNumber'       => $phone,
                    'CallBackURL'       => $this->callbackUrl,
                    'AccountReference'  => $accountRef,
                    'TransactionDesc'   => $description,
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['CheckoutRequestID'])) {
                return [
                    'success'              => true,
                    'checkout_request_id'  => $data['CheckoutRequestID'],
                    'merchant_request_id'  => $data['MerchantRequestID'],
                    'response_description' => $data['ResponseDescription'] ?? 'STK Push sent',
                    'raw'                  => $data,
                ];
            }

            Log::error('M-Pesa STK Push failed', ['response' => $data]);
            return [
                'success' => false,
                'message' => $data['errorMessage'] ?? $data['ResponseDescription'] ?? 'STK Push failed. Check your Daraja credentials.',
                'raw'     => $data,
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Network error: ' . $e->getMessage()];
        }
    }

    // ── Step 3: Query STK Push Status ─────────────────────────────────────────
    public function queryStkStatus(string $checkoutRequestId): array
    {
        $token = $this->getAccessToken();
        if (! $token) {
            return ['success' => false, 'message' => 'Failed to get access token'];
        }

        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

        try {
            $response = Http::withToken($token)
                ->post($this->baseUrl . config('mpesa.endpoints.query'), [
                    'BusinessShortCode' => $this->shortcode,
                    'Password'          => $password,
                    'Timestamp'         => $timestamp,
                    'CheckoutRequestID' => $checkoutRequestId,
                ]);

            return ['success' => true, 'data' => $response->json()];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ── Step 4: Parse Callback ────────────────────────────────────────────────
    /**
     * Parse the Daraja callback body and return normalised data.
     * Called in MpesaController@callback
     */
    public static function parseCallback(array $body): array
    {
        $stkCallback = $body['Body']['stkCallback'] ?? null;

        if (! $stkCallback) {
            return ['success' => false, 'message' => 'Invalid callback body'];
        }

        $resultCode = $stkCallback['ResultCode'];

        if ($resultCode !== 0) {
            return [
                'success'             => false,
                'checkout_request_id' => $stkCallback['CheckoutRequestID'],
                'message'             => $stkCallback['ResultDesc'],
                'result_code'         => $resultCode,
            ];
        }

        // Extract metadata items
        $items = collect($stkCallback['CallbackMetadata']['Item'] ?? [])
            ->keyBy('Name')
            ->map(fn($i) => $i['Value'] ?? null);

        return [
            'success'             => true,
            'checkout_request_id' => $stkCallback['CheckoutRequestID'],
            'merchant_request_id' => $stkCallback['MerchantRequestID'],
            'mpesa_reference'     => $items->get('MpesaReceiptNumber'),
            'amount'              => $items->get('Amount'),
            'phone_number'        => $items->get('PhoneNumber'),
            'transaction_date'    => $items->get('TransactionDate'),
        ];
    }
}
