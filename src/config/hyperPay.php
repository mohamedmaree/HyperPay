<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HyperPay Mode
    |--------------------------------------------------------------------------
    |
    | Mode only values: "test" or "live"
    |
    */

    "mode" => "test" ,

    /*
    |--------------------------------------------------------------------------
    | HyperPay currency
    |--------------------------------------------------------------------------
    | EGP , SAR , USD, .. etc
    */

    "currency" => "SAR",

    /*
    |--------------------------------------------------------------------------
    | Access Token
    |--------------------------------------------------------------------------
    |
    | Your access token to enable integration with hyperpay
    |
    */

    "token" =>  "",

    /*
     |--------------------------------------------------------------------------
     | Brands [VisaMaster,StcPay,Mada,Amex,Apple] and entity Id's
     |--------------------------------------------------------------------------
     |
     | You must put entityId your own for each Brand
     |
     */

    'entityIds' => [
        'VISA MASTER' => "",
        'STC_PAY'     => "",
        'MADA'        => "",
        'APPLEPAY'    => "",
        'AMEX'        => ""
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Request url
    |--------------------------------------------------------------------------
    */

    "request_test_url" => "https://test.oppwa.com/v1/checkouts",
    "request_live_url" => "https://oppwa.com/v1/checkouts",
    
    "response_test_url" => "https://test.oppwa.com/",
    "response_live_url" => "https://oppwa.com/",

    'view_test_url' => 'https://test.oppwa.com/v1/paymentWidgets.js?checkoutId=',
    'view_live_url' => 'https://oppwa.com/v1/paymentWidgets.js?checkoutId=',

    'direct_checkout_test_url' => 'https://eu-test.oppwa.com/v1/payments',
    'direct_checkout_live_url' => 'https://oppwa.com/v1/payments',

];