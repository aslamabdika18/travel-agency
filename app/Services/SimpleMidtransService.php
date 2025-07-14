<?php

namespace App\Services;

use Exception;

class SimpleMidtransService
{
    private $serverKey;
    private $clientKey;
    private $isProduction;
    
    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->clientKey = config('midtrans.client_key');
        $this->isProduction = config('midtrans.is_production', false);
    }
    
    public function createSnapTransaction($params)
    {
        $url = $this->isProduction 
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
            
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . base64_encode($this->serverKey . ':')
            ],
            // Nonaktifkan SSL verification untuk development
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($result === false) {
            throw new Exception('CURL Error: ' . $error);
        }
        
        $response = json_decode($result, true);
        
        if ($httpCode >= 400) {
            throw new Exception('Midtrans API Error: ' . $result, $httpCode);
        }
        
        return $response;
    }
    
    public function calculatePrice($basePrice, $quantity, $tax = 0.025)
    {
        $subtotal = $basePrice * $quantity;
        $taxAmount = $subtotal * $tax;
        return $subtotal + $taxAmount;
    }
}