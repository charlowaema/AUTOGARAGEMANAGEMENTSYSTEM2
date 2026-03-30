<?php

return [

    /*
    |--------------------------------------------------------------------------
    | M-Pesa Daraja API Configuration
    |--------------------------------------------------------------------------
    | Get these credentials from https://developer.safaricom.co.ke
    | After going live, switch MPESA_ENV to "production"
    */

    'env'              => env('MPESA_ENV', 'sandbox'),   // sandbox | production

    'consumer_key'     => env('MPESA_CONSUMER_KEY', ''),
    'consumer_secret'  => env('MPESA_CONSUMER_SECRET', ''),

    'shortcode'        => env('MPESA_SHORTCODE', '174379'),   // Paybill or Till number
    'passkey'          => env('MPESA_PASSKEY', ''),            // Lipa Na M-Pesa passkey

    'callback_url'     => env('MPESA_CALLBACK_URL', ''),      // Must be HTTPS publicly accessible
    'transaction_type' => env('MPESA_TRANSACTION_TYPE', 'CustomerPayBillOnline'), // or CustomerBuyGoodsOnline

    // API Base URLs
    'base_url' => [
        'sandbox'    => 'https://sandbox.safaricom.co.ke',
        'production' => 'https://api.safaricom.co.ke',
    ],

    // Endpoints
    'endpoints' => [
        'oauth'    => '/oauth/v1/generate?grant_type=client_credentials',
        'stk_push' => '/mpesa/stkpush/v1/processrequest',
        'query'    => '/mpesa/stkpushquery/v1/query',
    ],
];
