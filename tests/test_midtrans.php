<?php

require_once 'vendor/autoload.php';

use Midtrans\Config;
use Midtrans\Snap;

// Set konfigurasi Midtrans
Config::$serverKey = 'SB-Mid-server-m2l_tBOj4qXrSNARb3lGuWVW';
Config::$isProduction = false;
Config::$isSanitized = true;
Config::$is3ds = true;

// Set CURL options untuk mengatasi masalah SSL di development
Config::$curlOptions = [
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
];

echo "Testing Midtrans Configuration...\n";
echo "Server Key: " . Config::$serverKey . "\n";
echo "Is Production: " . (Config::$isProduction ? 'true' : 'false') . "\n";

// Test parameter sederhana
$params = [
    'transaction_details' => [
        'order_id' => 'TEST-' . time(),
        'gross_amount' => 100000
    ],
    'customer_details' => [
        'first_name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '+62812345678'
    ],
    'item_details' => [
        [
            'id' => 'test-item',
            'price' => 100000,
            'quantity' => 1,
            'name' => 'Test Item'
        ]
    ]
];

try {
    echo "\nCreating Snap transaction...\n";
    $snapResponse = Snap::createTransaction($params);
    
    echo "Success!\n";
    echo "Redirect URL: " . $snapResponse->redirect_url . "\n";
    echo "Token: " . $snapResponse->token . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}