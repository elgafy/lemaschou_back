<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Active Payment Gateway
    |--------------------------------------------------------------------------
    |
    | The payment gateway driver to use. Supported: "edfapay".
    | To add a new gateway, implement PaymentGatewayInterface and add it here.
    |
    */
    'gateway' => env('PAYMENT_GATEWAY', 'edfapay'),

    /*
    |--------------------------------------------------------------------------
    | Gateway Config Map
    |--------------------------------------------------------------------------
    |
    | Maps gateway names to their config file keys.
    |
    */
    'gateways' => [
        'edfapay' => [
            'base_url' => env('EDFAPAY_BASE_URL'),
            'api_key' => env('EDFAPAY_API_KEY'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend Redirect URLs
    |--------------------------------------------------------------------------
    |
    | Where the customer is redirected after payment success or failure.
    | These are passed to the gateway as callback URLs.
    |
    */
    'success_url' => env('PAYMENT_SUCCESS_URL', 'https://lemaschou.gafystudio.com/en/payment/success'),
    'failure_url' => env('PAYMENT_FAILURE_URL', 'https://lemaschou.gafystudio.com/en/payment/failure'),
];
