<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RajaOngkir API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for RajaOngkir Shipping API integration.
    | Get your API key from: https://rajaongkir.com/
    |
    */

    'api_key' => env('RAJAONGKIR_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Account Type
    |--------------------------------------------------------------------------
    |
    | Account types:
    | - starter: Basic account (limited features)
    | - basic: More features
    | - pro: Full features
    |
    */
    'account_type' => env('RAJAONGKIR_ACCOUNT_TYPE', 'starter'),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | Base URL changes based on account type
    |
    */
    'base_url' => env('RAJAONGKIR_ACCOUNT_TYPE', 'starter') === 'starter' 
        ? 'https://api.rajaongkir.com/starter' 
        : 'https://api.rajaongkir.com/basic',

    /*
    |--------------------------------------------------------------------------
    | Supported Couriers
    |--------------------------------------------------------------------------
    |
    | List of supported couriers based on account type
    |
    */
    'couriers' => [
        'starter' => ['jne', 'pos', 'tiki'],
        'basic' => ['jne', 'pos', 'tiki', 'rpx', 'pcp', 'esl', 'pandu', 'wahana', 'sicepat', 'jnt', 'pahala', 'sap', 'jet', 'indah', 'dse', 'slis', 'first', 'ncs', 'star'],
        'pro' => ['jne', 'pos', 'tiki', 'rpx', 'pcp', 'esl', 'pandu', 'wahana', 'sicepat', 'jnt', 'pahala', 'sap', 'jet', 'indah', 'dse', 'slis', 'first', 'ncs', 'star'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Origin City
    |--------------------------------------------------------------------------
    |
    | Default city ID for shipping calculation (your warehouse location)
    | Example: 154 for Jakarta Pusat
    |
    */
    'origin_city_id' => env('RAJAONGKIR_ORIGIN_CITY_ID', '154'),
];
