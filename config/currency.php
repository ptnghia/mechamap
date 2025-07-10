<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Currency Configuration
    |--------------------------------------------------------------------------
    |
    | This option controls the default currency used throughout the application.
    | You can set this to any currency code (USD, VND, EUR, etc.)
    |
    */

    'default' => env('APP_CURRENCY', 'VND'),

    /*
    |--------------------------------------------------------------------------
    | Supported Currencies
    |--------------------------------------------------------------------------
    |
    | List of currencies supported by the application
    |
    */

    'supported' => [
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'code' => 'USD',
            'decimals' => 2,
            'format' => 'symbol_before', // $100.00
        ],
        'VND' => [
            'name' => 'Vietnamese Dong',
            'symbol' => '₫',
            'code' => 'VND',
            'decimals' => 0,
            'format' => 'symbol_after', // 100,000₫
        ],
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'code' => 'EUR',
            'decimals' => 2,
            'format' => 'symbol_before', // €100.00
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency Display Settings
    |--------------------------------------------------------------------------
    |
    | Settings for how currencies are displayed
    |
    */

    'display' => [
        'show_symbol' => true,
        'show_code' => false,
        'decimal_separator' => '.',
        'thousands_separator' => ',',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exchange Rates (if needed for multi-currency)
    |--------------------------------------------------------------------------
    |
    | Base currency is USD
    |
    */

    'exchange_rates' => [
        'USD' => 1.0,
        'VND' => 24000.0, // 1 USD = 24,000 VND (approximate)
        'EUR' => 0.85,    // 1 USD = 0.85 EUR (approximate)
    ],
];
