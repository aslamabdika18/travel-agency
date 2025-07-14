<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Services\SimpleMidtransService;
use App\Models\TravelPackage;
use App\Models\User;

echo "=== DEBUGGING SIMPLE MIDTRANS SERVICE ===\n";

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo "Laravel environment loaded\n";

try {
    // Test SimpleMidtransService
    $midtransService = new SimpleMidtransService();
    echo "SimpleMidtransService created successfully\n\n";
    
    echo "=== TESTING MIDTRANS CONFIG ===\n";
    echo "Server Key: " . (config('midtrans.server_key') ? 'Set' : 'Not Set') . "\n";
    echo "Client Key: " . (config('midtrans.client_key') ? 'Set' : 'Not Set') . "\n";
    echo "Environment: " . config('app.env') . "\n";
    echo "Is Production: " . (config('midtrans.is_production') ? 'Yes' : 'No') . "\n\n";
    
    echo "=== TESTING TRAVEL PACKAGE ===\n";
    $travelPackage = TravelPackage::first();
    if ($travelPackage) {
        $packageName = $travelPackage->title ?? $travelPackage->name ?? 'Travel Package #' . $travelPackage->id;
        echo "Travel package found: {$packageName}\n";
        echo "Price: {$travelPackage->price}\n";
        
        // Test price calculation
        $quantity = 2;
        $totalPrice = $midtransService->calculatePrice($travelPackage->price, $quantity);
        echo "Price calculation successful\n";
        echo "Total price: {$totalPrice}\n\n";
    } else {
        echo "No travel package found\n\n";
        exit(1);
    }
    
    echo "=== TESTING USER ===\n";
    $user = User::first();
    if ($user) {
        echo "User found: {$user->email}\n\n";
    } else {
        echo "No user found\n\n";
        exit(1);
    }
    
    echo "=== TESTING SNAP TRANSACTION ===\n";
    echo "Creating Snap transaction...\n";
    
    // Prepare transaction data
     $itemPrice = (int) $travelPackage->price;
     $itemTotal = $itemPrice * $quantity;
     $taxAmount = (int) ($itemTotal * 0.025);
     $grossAmount = $itemTotal + $taxAmount;
     
     $transactionData = [
         'transaction_details' => [
             'order_id' => 'TEST-' . time(),
             'gross_amount' => $grossAmount
         ],
         'customer_details' => [
             'first_name' => $user->name,
             'email' => $user->email,
         ],
         'item_details' => [
             [
                 'id' => $travelPackage->id,
                 'price' => $itemPrice,
                 'quantity' => $quantity,
                 'name' => $packageName
             ],
             [
                 'id' => 'TAX',
                 'price' => $taxAmount,
                 'quantity' => 1,
                 'name' => 'Tax (2.5%)'
             ]
         ]
     ];
     
     echo "Item total: {$itemTotal}\n";
     echo "Tax amount: {$taxAmount}\n";
     echo "Gross amount: {$grossAmount}\n";
    
    // Create Snap transaction
    $snapResponse = $midtransService->createSnapTransaction($transactionData);
    
    if (isset($snapResponse['token'])) {
        echo "Snap transaction created successfully!\n";
        echo "Token: {$snapResponse['token']}\n";
        echo "Redirect URL: {$snapResponse['redirect_url']}\n";
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

echo "\n=== DEBUG COMPLETED SUCCESSFULLY ===\n";