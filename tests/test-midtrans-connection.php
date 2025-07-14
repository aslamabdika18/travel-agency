<?php

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        echo "Error: .env file not found\n";
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) continue;
        
        // Parse line
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remove quotes if present
            if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
                $value = substr($value, 1, -1);
            }
            
            // Set environment variable
            putenv("$name=$value");
        }
    }
    return true;
}

// Test environment (local or production)
$env = isset($argv[1]) ? $argv[1] : 'local';
$isProduction = ($env === 'production');

// Load environment variables
if (!loadEnv(__DIR__ . '/.env')) {
    exit(1);
}

// Get Midtrans configuration
$serverKey = getenv('MIDTRANS_SERVER_KEY');
$clientKey = getenv('MIDTRANS_CLIENT_KEY');

if (empty($serverKey) || empty($clientKey)) {
    echo "Error: Midtrans keys not found in environment variables\n";
    exit(1);
}

// Clean up server key (remove any spaces)
$serverKey = str_replace(' ', '', $serverKey);

// Test connection to Midtrans API
function testMidtransConnection($serverKey, $isProduction, $verifySSL = true) {
    $apiUrl = $isProduction 
        ? 'https://app.midtrans.com/snap/v1/transactions'
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    
    echo "\nTesting connection to: $apiUrl\n";
    echo "Verify SSL: " . ($verifySSL ? "Yes" : "No") . "\n";
    
    // Create a minimal transaction request
    $transactionData = json_encode([
        'transaction_details' => [
            'order_id' => 'test-' . time(),
            'gross_amount' => 10000
        ]
    ]);
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode($serverKey . ':')
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $transactionData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Set SSL verification options
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verifySSL ? 2 : 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifySSL);
    
    // Enable verbose output for debugging
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
    // Execute request
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $info = curl_getinfo($ch);
    
    // Get verbose information
    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);
    
    echo "\nVerbose information:\n";
    echo $verboseLog;
    
    echo "\nResponse code: " . $info['http_code'] . "\n";
    
    if ($error) {
        echo "cURL Error: $error\n";
    } else {
        echo "Response:\n";
        $responseData = json_decode($response, true);
        echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    }
    
    curl_close($ch);
}

echo "\n=== Midtrans Connection Test ===\n";
echo "Environment: " . ($isProduction ? "Production" : "Sandbox") . "\n";
echo "Server Key: " . substr($serverKey, 0, 10) . "..." . "\n";

// Test with SSL verification
echo "\n--- Testing with SSL verification ---\n";
testMidtransConnection($serverKey, $isProduction, true);

// Test without SSL verification
echo "\n--- Testing without SSL verification ---\n";
testMidtransConnection($serverKey, $isProduction, false);
