<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // Sandbox setting yang direkomendasikan Midtrans:
    'is_sanitized' => true,
    'is_3ds' => true,
];
