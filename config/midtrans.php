<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Midtrans Configuration
     |--------------------------------------------------------------------------
     |
     | Berikut adalah konfigurasi yang diperlukan untuk integrasi dengan Midtrans.
     |
     */

    'server_key' => env('MIDTRANS_SERVER_KEY'), // Ambil dari .env
    'client_key' => env('MIDTRANS_CLIENT_KEY'), // Ambil dari .env
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false), // false untuk sandbox, true untuk production
];
