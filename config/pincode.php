<?php

return [

    /*
    |--------------------------------------------------------------------------
    | India Post PIN lookup API
    |--------------------------------------------------------------------------
    |
    | Uses the public postalpincode.in API (India Post directory).
    | Results are cached to limit external calls.
    |
    */

    'api_url' => env('PINCODE_API_URL', 'https://api.postalpincode.in/pincode/{pincode}'),

    'timeout_seconds' => (int) env('PINCODE_API_TIMEOUT', 10),

    /*
     * Their TLS certificate has been expired at times; disable verify only if needed.
     */
    'verify_ssl' => env('PINCODE_API_VERIFY_SSL', false),

    'cache_days' => (int) env('PINCODE_CACHE_DAYS', 30),

];
