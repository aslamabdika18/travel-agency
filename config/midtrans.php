<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY', null),
    'client_key' => env('MIDTRANS_CLIENT_KEY', null),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
];
