<?php
/**
 * Script untuk testing konfigurasi Midtrans
 * Usage: php scripts/test-midtrans-config.php [environment]
 * Environment: local, staging, production
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Midtrans\Config;
use Midtrans\Snap;

// Load environment
$environment = $argv[1] ?? 'local';

echo "=== MIDTRANS CONFIGURATION TEST ===\n";
echo "Environment: {$environment}\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

// Load environment file
$envFile = __DIR__ . "/../.env.{$environment}";
if (!file_exists($envFile)) {
    $envFile = __DIR__ . '/../.env';
}

if (file_exists($envFile)) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname($envFile), basename($envFile));
    $dotenv->load();
    echo "✓ Environment file loaded: {$envFile}\n";
} else {
    echo "✗ Environment file not found\n";
    exit(1);
}

// Set konfigurasi Midtrans
$serverKey = $_ENV['MIDTRANS_SERVER_KEY'] ?? null;
$clientKey = $_ENV['MIDTRANS_CLIENT_KEY'] ?? null;
$isProduction = filter_var($_ENV['MIDTRANS_IS_PRODUCTION'] ?? false, FILTER_VALIDATE_BOOLEAN);
$appEnv = $_ENV['APP_ENV'] ?? 'local';

if (!$serverKey || !$clientKey) {
    echo "✗ Midtrans keys not configured\n";
    exit(1);
}

echo "✓ Midtrans keys configured\n";

// Configure Midtrans
Config::$serverKey = $serverKey;
Config::$isProduction = $isProduction;
Config::$isSanitized = true;
Config::$is3ds = true;

// Set SSL configuration based on environment
if ($appEnv === 'local' || $appEnv === 'development') {
    Config::$curlOptions = [
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ];
    echo "✓ SSL verification disabled for development\n";
} else {
    Config::$curlOptions = [
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ];
    
    if (isset($_ENV['MIDTRANS_SSL_CERT_PATH']) && $_ENV['MIDTRANS_SSL_CERT_PATH']) {
        Config::$curlOptions[CURLOPT_CAINFO] = $_ENV['MIDTRANS_SSL_CERT_PATH'];
        echo "✓ Custom SSL certificate configured\n";
    } else {
        echo "✓ SSL verification enabled (using system CA bundle)\n";
    }
}

echo "\n=== CONFIGURATION SUMMARY ===\n";
echo "Server Key: " . substr($serverKey, 0, 10) . "..." . substr($serverKey, -5) . "\n";
echo "Client Key: " . substr($clientKey, 0, 10) . "..." . substr($clientKey, -5) . "\n";
echo "Is Production: " . ($isProduction ? 'true' : 'false') . "\n";
echo "Environment: {$appEnv}\n";
echo "SSL Verification: " . (Config::$curlOptions[CURLOPT_SSL_VERIFYPEER] ? 'enabled' : 'disabled') . "\n";

// Test API connection
echo "\n=== API CONNECTION TEST ===\n";

$testParams = [
    'transaction_details' => [
        'order_id' => 'TEST-' . strtoupper($environment) . '-' . time(),
        'gross_amount' => 100000
    ],
    'customer_details' => [
        'first_name' => 'Test User',
        'last_name' => 'Config',
        'email' => 'test@example.com',
        'phone' => '+62812345678'
    ],
    'item_details' => [
        [
            'id' => 'test-item',
            'price' => 100000,
            'quantity' => 1,
            'name' => 'Test Configuration Item'
        ]
    ]
];

try {
    echo "Testing Snap transaction creation...\n";
    $snapResponse = Snap::createTransaction($testParams);
    
    echo "✓ API connection successful!\n";
    echo "✓ Snap token created: " . substr($snapResponse->token, 0, 10) . "...\n";
    echo "✓ Redirect URL: " . $snapResponse->redirect_url . "\n";
    
    // Verify environment
    $expectedEnv = $isProduction ? 'app.midtrans.com' : 'app.sandbox.midtrans.com';
    if (strpos($snapResponse->redirect_url, $expectedEnv) !== false) {
        echo "✓ Environment verification passed\n";
    } else {
        echo "⚠ Environment mismatch detected\n";
    }
    
} catch (Exception $e) {
    echo "✗ API connection failed\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    
    // Provide troubleshooting hints
    if (strpos($e->getMessage(), 'SSL') !== false || strpos($e->getMessage(), 'certificate') !== false) {
        echo "\n=== SSL TROUBLESHOOTING ===\n";
        echo "This appears to be an SSL-related issue.\n";
        echo "For production, ensure your server has valid SSL certificates.\n";
        echo "For development, SSL verification is disabled automatically.\n";
    }
    
    if (strpos($e->getMessage(), 'Access denied') !== false || $e->getCode() == 401) {
        echo "\n=== AUTHENTICATION TROUBLESHOOTING ===\n";
        echo "This appears to be an authentication issue.\n";
        echo "Please verify your Midtrans server key is correct.\n";
        echo "Ensure you're using the right key for the environment (sandbox/production).\n";
    }
    
    exit(1);
}

echo "\n=== TEST COMPLETED SUCCESSFULLY ===\n";
echo "Your Midtrans configuration is working correctly!\n";