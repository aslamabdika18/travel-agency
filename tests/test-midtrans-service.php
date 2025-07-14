<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Services\MidtransService;
use App\Models\TravelPackage;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;

echo "=== TESTING MIDTRANS SERVICE ===\n";

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo "Laravel environment loaded\n";

try {
    // Test MidtransService
    $midtransService = new MidtransService();
    echo "MidtransService created successfully\n\n";
    
    echo "=== TESTING MIDTRANS CONFIG ===\n";
    echo "Server Key: " . (config('midtrans.server_key') ? 'Set' : 'Not Set') . "\n";
    echo "Client Key: " . (config('midtrans.client_key') ? 'Set' : 'Not Set') . "\n";
    echo "Environment: " . config('app.env') . "\n";
    echo "Is Production: " . (config('midtrans.is_production') ? 'Yes' : 'No') . "\n\n";
    
    echo "=== TESTING DIRECT SNAP TRANSACTION ===\n";
    
    // Test data untuk transaksi
    $testTransactionData = [
        'transaction_details' => [
            'order_id' => 'TEST-DIRECT-' . time(),
            'gross_amount' => 100000
        ],
        'customer_details' => [
            'first_name' => 'Test User',
            'email' => 'test@example.com',
        ],
        'item_details' => [
            [
                'id' => 'TEST-ITEM',
                'price' => 100000,
                'quantity' => 1,
                'name' => 'Test Travel Package'
            ]
        ]
    ];
    
    echo "Creating test Snap transaction...\n";
    $snapResponse = $midtransService->createSnapTransaction($testTransactionData);
    
    if (isset($snapResponse->token)) {
        echo "Snap transaction created successfully!\n";
        echo "Token: {$snapResponse->token}\n";
        echo "Redirect URL: {$snapResponse->redirect_url}\n";
    } else {
        echo "Failed to create Snap transaction\n";
        echo "Response: " . json_encode($snapResponse, JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    echo "File: {$e->getFile()}:{$e->getLine()}\n";
    echo "Stack trace: {$e->getTraceAsString()}\n";
    exit(1);
}

echo "\n=== TEST COMPLETED SUCCESSFULLY ===\n";