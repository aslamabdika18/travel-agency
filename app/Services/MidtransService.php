<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Midtrans\Notification;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Exception;

class MidtransService
{
    public function __construct()
    {
        // Set konfigurasi Midtrans sesuai dokumentasi resmi
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = config('midtrans.is_3ds', true);
        
        // Set CURL options berdasarkan environment
        // Untuk production, SSL verification harus diaktifkan untuk keamanan
        if (config('app.env') === 'local' || config('app.env') === 'development') {
            // Kosongkan curl options untuk development agar tidak ada konflik
            // Library Midtrans akan menggunakan default settings
            Config::$curlOptions = [];
            
        } else {
            // Production: aktifkan SSL verification untuk keamanan
            Config::$curlOptions = [
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ];
        }
    }

    // Snap token method removed - only using Snap redirect method

    /**
     * Membuat URL redirect Snap sesuai dokumentasi resmi Midtrans
     */
    public function createSnapRedirectUrl(Booking $booking, Payment $payment): string
    {
        // Pastikan relasi travelPackage dimuat
        if (!$booking->relationLoaded('travelPackage')) {
            $booking->load('travelPackage');
        }
        try {
            $orderId = 'BOOKING-' . $booking->id . '-' . time();

            $params = $this->createTransactionParams($booking, $payment, $orderId);

            // Menggunakan metode createSnapTransaction yang sudah diperbaiki
            $snapResponse = $this->createSnapTransaction($params);

            // Update payment with transaction details
            $payment->update([
                'gateway_transaction_id' => $orderId,
                'gateway_status' => 'pending'
            ]);

            Log::info('Snap redirect URL created', [
                'booking_id' => $booking->id,
                'order_id' => $orderId,
                'redirect_url' => $snapResponse->redirect_url
            ]);

            return $snapResponse->redirect_url;
        } catch (Exception $e) {
            Log::error('Midtrans Snap Redirect URL Error: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            throw new Exception('Midtrans API Error: ' . $e->getMessage());
        }
    }

    /**
     * Create Snap transaction
     */
    public function createSnapTransaction($params)
    {
        try {
            // Gunakan direct cURL untuk menghindari masalah dengan library Midtrans
            if (config('app.env') === 'local' || config('app.env') === 'development') {
                return $this->createSnapTransactionDirect($params);
            }
            
            // Untuk production, gunakan library Midtrans
            $snapToken = Snap::createTransaction($params);
            return $snapToken;
        } catch (Exception $e) {
            throw new Exception('Failed to create Snap transaction: ' . $e->getMessage());
        }
    }
    
    /**
     * Create Snap transaction using direct cURL (untuk development)
     */
    private function createSnapTransactionDirect($params)
    {
        $url = Config::$isProduction 
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
                'Authorization: Basic ' . base64_encode(Config::$serverKey . ':')
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
        
        $response = json_decode($result);
        
        if ($httpCode >= 400) {
            throw new Exception('Midtrans API Error: ' . $result, $httpCode);
        }
        
        return $response;
    }

    /**
     * Build transaction parameters for Midtrans
     */
    private function createTransactionParams(Booking $booking, Payment $payment, string $orderId): array
    {
        // Pastikan relasi travelPackage dimuat
        if (!$booking->relationLoaded('travelPackage')) {
            $booking->load('travelPackage');
        }
        $grossAmount = (int) $booking->total_price;

        return [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount
            ],
            'customer_details' => [
                'first_name' => $booking->user->name,
                'email' => $booking->user->email,
                'phone' => $booking->user->contact ?? ''
            ],
            'item_details' => [
                [
                    'id' => 'travel-package-' . $booking->travel_package_id,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => $booking->travel_package_id ? $this->getTravelPackageName($booking) : 'Travel Package',
                    'category' => 'Travel Package'
                ]
            ],
            'callbacks' => [
                'finish' => config('app.url') . '/payment/success?order_id=' . $orderId,
                'unfinish' => config('app.url') . '/payment/error?order_id=' . $orderId,
                'error' => config('app.url') . '/payment/error?order_id=' . $orderId
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'minutes',
                'duration' => 60 * 24 // 24 hours
            ],
            'custom_field1' => 'booking_id:' . $booking->id,
            'custom_field2' => 'user_id:' . $booking->user_id,
            'custom_field3' => 'travel_package_id:' . $booking->travel_package_id
        ];
    }

    /**
     * Helper method to safely get travel package name
     */
    private function getTravelPackageName(Booking $booking): string
    {
        try {
            // Try to get from relation first
            if ($booking->relationLoaded('travelPackage') && $booking->getRelation('travelPackage') !== null) {
                $travelPackage = $booking->getRelation('travelPackage');
                if ($travelPackage && isset($travelPackage->name)) {
                    return $travelPackage->name;
                }
            }
            
            // If relation not loaded or null, try to get directly from database
            if ($booking->travel_package_id) {
                $travelPackage = \App\Models\TravelPackage::find($booking->travel_package_id);
                if ($travelPackage && isset($travelPackage->name)) {
                    // Cache the relation
                    $booking->setRelation('travelPackage', $travelPackage);
                    return $travelPackage->name;
                }
            }
            
            // Fallback
            return 'Travel Package #' . $booking->travel_package_id;
        } catch (\Exception $e) {
            // Log error but don't crash
            \Illuminate\Support\Facades\Log::error('Error getting travel package name: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'travel_package_id' => $booking->travel_package_id
            ]);
            return 'Travel Package #' . $booking->travel_package_id;
        }
    }
    
    /**
     * Memproses notifikasi webhook dari Midtrans sesuai dokumentasi resmi
     */
    public function handleNotification(): array
    {
        try {
            // Notification class akan otomatis membaca dari php://input
            $notification = new Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;

            // Extract booking ID from order ID
            $bookingId = $this->extractBookingIdFromOrderId($orderId);

            // Cari payment berdasarkan booking_id dan gateway_transaction_id
            $payment = Payment::where('booking_id', $bookingId)
                ->byGatewayTransactionId($orderId)
                ->first();
                
            // Jika tidak ditemukan, coba cari berdasarkan booking_id saja
            if (!$payment) {
                $payment = Payment::where('booking_id', $bookingId)->first();
            }

            if (!$payment) {
                throw new Exception('Payment not found for order: ' . $orderId);
            }

            // Update payment status based on transaction status
            $this->updatePaymentStatus($payment, $transactionStatus, $fraudStatus, (array) $notification);

            Log::info('Midtrans notification processed', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_id' => $payment->id
            ]);

            return (array) $notification;
        } catch (Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mendapatkan status transaksi dari Midtrans sesuai dokumentasi resmi
     */
    public function getTransactionStatus(string $orderId): object
    {
        try {
            $status = Transaction::status($orderId);
            return (object) $status;
        } catch (Exception $e) {
            Log::error('Failed to get transaction status from Midtrans: ' . $e->getMessage());
            throw new Exception('Failed to get transaction status from Midtrans');
        }
    }

    /**
     * Update payment status based on Midtrans transaction status sesuai dokumentasi resmi
     */
    private function updatePaymentStatus(Payment $payment, string $transactionStatus, ?string $fraudStatus, array $notification): void
    {
        // Mapping sesuai dokumentasi resmi Midtrans
        switch ($transactionStatus) {
            case 'capture':
                // Untuk credit card, cek fraud status
                if ($fraudStatus === 'challenge') {
                    $payment->update([
                        'payment_status' => 'Unpaid',
                        'gateway_status' => 'challenge'
                    ]);
                } elseif ($fraudStatus === 'accept') {
                    $payment->markAsPaid();
                    $payment->update([
                        'gateway_status' => 'capture',
                        'payment_date' => now()
                    ]);
                } else {
                    // Default untuk capture
                    $payment->markAsPaid();
                    $payment->update([
                        'gateway_status' => 'capture',
                        'payment_date' => now()
                    ]);
                }
                break;

            case 'settlement':
                $payment->markAsPaid();
                $payment->update([
                    'gateway_status' => 'settlement',
                    'payment_date' => now()
                ]);
                break;

            case 'pending':
                $payment->update([
                    'payment_status' => 'Unpaid',
                    'gateway_status' => 'pending'
                ]);
                break;

            case 'deny':
            case 'cancel':
            case 'expire':
            case 'failure':
                $payment->markAsFailed();
                $payment->update([
                    'gateway_status' => $transactionStatus
                ]);
                break;

            case 'refund':
            case 'partial_refund':
                $payment->update([
                    'payment_status' => 'Refunded',
                    'gateway_status' => $transactionStatus
                ]);
                break;

            default:
                Log::warning('Unknown Midtrans transaction status: ' . $transactionStatus, [
                    'payment_id' => $payment->id,
                    'notification' => $notification
                ]);
        }
    }

    /**
     * Extract booking ID from order ID
     */
    private function extractBookingIdFromOrderId(string $orderId): int
    {
        // Order ID format: BOOKING-{booking_id}-{timestamp}
        $parts = explode('-', $orderId);

        if (count($parts) < 3 || $parts[0] !== 'BOOKING') {
            throw new Exception('Invalid order ID format: ' . $orderId);
        }

        return (int) $parts[1];
    }

    /**
     * Cancel transaction sesuai dokumentasi resmi
     */
    public function cancelTransaction(string $orderId): object
    {
        try {
            $result = Transaction::cancel($orderId);
            return (object) $result;
        } catch (Exception $e) {
            Log::error('Failed to cancel transaction: ' . $e->getMessage());
            throw new Exception('Failed to cancel transaction');
        }
    }

    /**
     * Refund transaction sesuai dokumentasi resmi
     */
    public function refundTransaction(string $orderId, ?int $refundAmount = null): object
    {
        try {
            $params = [];
            if ($refundAmount) {
                $params['amount'] = $refundAmount; // Midtrans API menggunakan 'amount'
            }

            $result = Transaction::refund($orderId, $params);
            return (object) $result;
        } catch (Exception $e) {
            Log::error('Failed to refund transaction: ' . $e->getMessage());
            throw new Exception('Failed to refund transaction');
        }
    }
}
