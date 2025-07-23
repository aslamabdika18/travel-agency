<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Midtrans\Notification;
use App\Models\Payment;
use App\Models\Booking;
use App\Notifications\PaymentSuccessNotification;
use App\Notifications\PaymentPendingNotification;
use App\Notifications\PaymentFailedNotification;
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
            // Tambahkan DNS resolver untuk mengatasi masalah DNS
            CURLOPT_DNS_SERVERS => '8.8.8.8,8.8.4.4',
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            // Fallback jika DNS gagal
            CURLOPT_RESOLVE => [
                'app.sandbox.midtrans.com:443:147.139.240.90',
                'app.sandbox.midtrans.com:443:147.139.179.86'
            ],
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
                'finish' => config('app.url') . '/payment/callback?order_id=' . $orderId,
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

            Log::info('Received Midtrans notification', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus
            ]);

            // Validasi order ID format
            if (!$orderId) {
                Log::error('Missing order_id in Midtrans notification');
                throw new Exception('Missing order_id in notification');
            }

            // Extract booking ID from order ID
            $bookingId = $this->extractBookingIdFromOrderId($orderId);

            Log::info('Extracted booking ID from order ID', [
                'order_id' => $orderId,
                'booking_id' => $bookingId
            ]);

            // Cari payment dengan eager loading booking untuk menghindari query N+1
            $payment = Payment::with('booking.user')->where('booking_id', $bookingId)->first();

            if (!$payment) {
                Log::error('Payment not found for booking', [
                    'booking_id' => $bookingId,
                    'order_id' => $orderId
                ]);
                throw new Exception('Payment not found for order: ' . $orderId);
            }

            // Update gateway_transaction_id dan transaction_id jika belum ada atau berbeda
            $updateData = [];
            
            if (!$payment->gateway_transaction_id || $payment->gateway_transaction_id !== $orderId) {
                $updateData['gateway_transaction_id'] = $orderId;
            }
            
            // Update transaction_id dengan transaction_id dari Midtrans jika ada
            if (isset($notification->transaction_id) && $notification->transaction_id) {
                if (!$payment->transaction_id || $payment->transaction_id !== $notification->transaction_id) {
                    $updateData['transaction_id'] = $notification->transaction_id;
                }
            }
            
            if (!empty($updateData)) {
                Log::info('Updating payment transaction IDs', [
                    'payment_id' => $payment->id,
                    'old_gateway_transaction_id' => $payment->gateway_transaction_id,
                    'new_gateway_transaction_id' => $orderId,
                    'old_transaction_id' => $payment->transaction_id,
                    'new_transaction_id' => $notification->transaction_id ?? null,
                    'update_data' => $updateData
                ]);
                
                $payment->update($updateData);
            }

            // Update payment status based on transaction status
            $this->updatePaymentStatus($payment, $transactionStatus, $fraudStatus, (array) $notification);

            Log::info('Midtrans notification processed successfully', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_id' => $payment->id,
                'booking_id' => $bookingId
            ]);

            return (array) $notification;
        } catch (Exception $e) {
            Log::error('Error processing Midtrans notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
    public function updatePaymentStatus(Payment $payment, string $transactionStatus, ?string $fraudStatus, array $notification): void
    {
        // Log notification untuk debugging
        Log::info('Processing Midtrans notification', [
            'payment_id' => $payment->id,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
            'order_id' => $notification['order_id'] ?? null
        ]);

        // Simpan gateway response untuk audit trail
        $payment->update([
            'gateway_response' => $notification
        ]);

        // Mapping sesuai dokumentasi resmi Midtrans
        switch ($transactionStatus) {
            case 'capture':
                // Untuk credit card, cek fraud status
                if ($fraudStatus === 'challenge') {
                    $payment->update([
                        'payment_status' => 'Unpaid',
                        'gateway_status' => 'challenge'
                    ]);
                    Log::warning('Payment marked as challenge due to fraud detection', [
                        'payment_id' => $payment->id,
                        'fraud_status' => $fraudStatus
                    ]);
                    // Kirim notifikasi pending untuk challenge
                    $this->sendPaymentNotification($payment, 'pending');
                } elseif ($fraudStatus === 'accept') {
                    $payment->markAsPaid();
                    $payment->update([
                        'gateway_status' => 'capture',
                        'payment_date' => now()
                    ]);
                    Log::info('Payment marked as paid (capture with fraud accept)', [
                        'payment_id' => $payment->id
                    ]);
                    // Kirim notifikasi sukses
                    $this->sendPaymentNotification($payment, 'success');
                } else {
                    // Default untuk capture (non-credit card atau fraud status null)
                    $payment->markAsPaid();
                    $payment->update([
                        'gateway_status' => 'capture',
                        'payment_date' => now()
                    ]);
                    Log::info('Payment marked as paid (capture)', [
                        'payment_id' => $payment->id
                    ]);
                    // Kirim notifikasi sukses
                    $this->sendPaymentNotification($payment, 'success');
                }
                break;

            case 'settlement':
                $payment->markAsPaid();
                $payment->update([
                    'gateway_status' => 'settlement',
                    'payment_date' => now()
                ]);
                Log::info('Payment marked as paid (settlement)', [
                    'payment_id' => $payment->id
                ]);
                // Kirim notifikasi sukses
                $this->sendPaymentNotification($payment, 'success');
                break;

            case 'pending':
                $payment->update([
                    'payment_status' => 'unpaid',
                    'gateway_status' => 'pending'
                ]);
                Log::info('Payment status updated to pending', [
                    'payment_id' => $payment->id
                ]);
                // Kirim notifikasi pending
                $this->sendPaymentNotification($payment, 'pending');
                break;

            case 'deny':
            case 'cancel':
            case 'expire':
            case 'failure':
                $payment->markAsFailed();
                $payment->update([
                    'gateway_status' => $transactionStatus
                ]);
                Log::info('Payment marked as failed', [
                    'payment_id' => $payment->id,
                    'reason' => $transactionStatus
                ]);
                // Kirim notifikasi gagal
                $this->sendPaymentNotification($payment, 'failed', $transactionStatus);
                break;

            case 'refund':
            case 'partial_refund':
                $payment->markAsRefunded();
                $payment->update([
                    'gateway_status' => $transactionStatus
                ]);
                Log::info('Payment marked as refunded', [
                    'payment_id' => $payment->id,
                    'type' => $transactionStatus
                ]);
                // Untuk refund, gunakan notifikasi yang sudah ada
                break;

            default:
                Log::warning('Unknown Midtrans transaction status', [
                    'payment_id' => $payment->id,
                    'transaction_status' => $transactionStatus,
                    'notification' => $notification
                ]);
                // Jangan update status untuk status yang tidak dikenal
                break;
        }
    }

    /**
     * Send payment notification based on status
     */
    private function sendPaymentNotification(Payment $payment, string $status, ?string $reason = null): void
    {
        try {
            // Optimasi: gunakan relasi yang sudah dimuat untuk menghindari query N+1
            $booking = $payment->relationLoaded('booking') ? $payment->getRelation('booking') : $payment->booking;
            
            if (!$booking) {
                Log::warning('Booking not found for payment notification', [
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->booking_id
                ]);
                return;
            }
            
            $user = $booking->relationLoaded('user') ? $booking->getRelation('user') : $booking->user;

            if (!$user) {
                Log::warning('User not found for payment notification', [
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->booking_id
                ]);
                return;
            }

            switch ($status) {
                case 'success':
                    $user->notify(new PaymentSuccessNotification($payment));
                    Log::info('Payment success notification sent', [
                        'payment_id' => $payment->id,
                        'user_id' => $user->id
                    ]);
                    break;

                case 'pending':
                    $user->notify(new PaymentPendingNotification($payment));
                    Log::info('Payment pending notification sent', [
                        'payment_id' => $payment->id,
                        'user_id' => $user->id
                    ]);
                    break;

                case 'failed':
                    $user->notify(new PaymentFailedNotification($payment, $reason));
                    Log::info('Payment failed notification sent', [
                        'payment_id' => $payment->id,
                        'user_id' => $user->id,
                        'reason' => $reason
                    ]);
                    break;

                default:
                    Log::warning('Unknown notification status', [
                        'payment_id' => $payment->id,
                        'status' => $status
                    ]);
                    break;
            }
        } catch (Exception $e) {
            Log::error('Failed to send payment notification', [
                'payment_id' => $payment->id,
                'status' => $status,
                'error' => $e->getMessage()
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

        $bookingId = (int) $parts[1];
        
        if ($bookingId <= 0) {
            throw new Exception('Invalid booking ID in order ID: ' . $orderId);
        }

        return $bookingId;
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
