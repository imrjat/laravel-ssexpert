<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SSExpertSystem API Base URL
    |--------------------------------------------------------------------------
    |
    | The base endpoint URL for the SSExpertSystem SMS & Template Gateway API.
    | Default: http://api.ssexpertsystem.com
    |
    */
    'base_url' => env('SSEXPERT_BASE_URL', 'http://api.ssexpertsystem.com'),

    /*
    |--------------------------------------------------------------------------
    | SSExpertSystem Credentials
    |--------------------------------------------------------------------------
    |
    | The ApiKey and ClientId assigned to your SSExpertSystem account.
    |
    */
    'api_key' => env('SSEXPERT_API_KEY', ''),
    'client_id' => env('SSEXPERT_CLIENT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Principal Entity ID (DLT PEID)
    |--------------------------------------------------------------------------
    |
    | Your DLT Principal Entity ID used for compliance with TRAI regulations.
    |
    */
    'principle_entity_id' => env('SSEXPERT_PEID', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Sender ID
    |--------------------------------------------------------------------------
    |
    | Default approved 6-character sender ID / header (e.g., ORPATG).
    |
    */
    'sender_id' => env('SSEXPERT_SENDER_ID', 'ORPATG'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Request Settings
    |--------------------------------------------------------------------------
    |
    | Timeout in seconds and retry configuration for transient network failures.
    |
    */
    'timeout' => env('SSEXPERT_TIMEOUT', 15),

    'retry' => [
        'times' => env('SSEXPERT_RETRY_TIMES', 3),
        'sleep' => env('SSEXPERT_RETRY_SLEEP', 100), // milliseconds
    ],
];
