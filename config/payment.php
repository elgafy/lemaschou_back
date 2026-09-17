<?php

$frontendUrl = rtrim(env('FRONTEND_URL', 'https://lemaschou.gafystudio.com'), '/');

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
            'checkout_url' => env('EDFAPAY_CHECKOUT_URL', 'https://app.edfapay.com/pay/checkout'),
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
    | These are templates: {locale} and {reservation_id} are replaced at
    | runtime by the gateway. Override them with PAYMENT_SUCCESS_URL /
    | PAYMENT_FAILURE_URL if the frontend routes change.
    |
    */
    'success_url' => env(
        'PAYMENT_SUCCESS_URL',
        $frontendUrl.'/{locale}/reservation/{reservation_id}/confirmation'
    ),
    'failure_url' => env(
        'PAYMENT_FAILURE_URL',
        $frontendUrl.'/{locale}/reservation/{reservation_id}/payment-failed'
    ),
];
