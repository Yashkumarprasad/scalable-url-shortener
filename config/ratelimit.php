<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Limits (Requests Per Minute)
    |--------------------------------------------------------------------------
    |
    | Define the maximum number of requests allowed per minute across
    | distinct functional areas of the platform.
    |
    */

    'auth' => (int) env('RATE_LIMIT_AUTH_PER_MINUTE', 10),
    'urls_create' => (int) env('RATE_LIMIT_URL_CREATE_PER_MINUTE', 60),
    'redirect' => (int) env('RATE_LIMIT_REDIRECT_PER_MINUTE', 120),
    'analytics' => (int) env('RATE_LIMIT_ANALYTICS_PER_MINUTE', 30),
    'api' => (int) env('RATE_LIMIT_API_PER_MINUTE', 60),
];
