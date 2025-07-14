<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorPaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:monitor {--payment-id= : Specific payment ID to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor and sync payment status with Midtrans';

    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        parent::__construct();
        $this->midtransService = $midtransService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $paymentId = $this->option('payment-id');
        
        if ($paymentId) {
            $this->checkSpecificPayment($paymentId);
        } else {
            $this->checkPendingPayments();
        }
        
        return 0;
    }
    
    /**
     * Check specific payment status
     */
    private function checkSpecificPayment($paymentId)
    {
        $payment = Payment::find($paymentId);
        
        if (!$payment) {
            $this->error("Payment with ID {$paymentId} not found.");
            return;
        }
        
        $this->info("Checking payment ID: {$payment->id}");
        $this->line("Current status: {$payment->payment_status}");
        $this->line("Gateway status: {$payment->gateway_status}");
        $this->line("Gateway transaction ID: {$payment->gateway_transaction_id}");
        
        if ($payment->gateway_transaction_id) {
            try {
                $status = $this->midtransService->getTransactionStatus($payment->gateway_transaction_id);
                $this->line("Midtrans status: {$status['transaction_status']}");
                
                // Cek apakah ada ketidaksesuaian
                if ($this->hasStatusMismatch($payment, $status)) {
                    $this->warn("Status mismatch detected!");
                    $this->askToSync($payment, $status);
                }
            } catch (\Exception $e) {
                $this->error("Error checking Midtrans status: {$e->getMessage()}");
            }
        } else {
            $this->warn("No gateway transaction ID found.");
        }
    }
    
    /**
     * Check all pending payments
     */
    private function checkPendingPayments()
    {
        $this->info('Checking pending payments...');
        
        $pendingPayments = Payment::where('payment_status', 'Unpaid')
            ->whereNotNull('gateway_transaction_id')
            ->where('created_at', '>', now()->subDays(7)) // Only check payments from last 7 days
            ->get();
            
        if ($pendingPayments->isEmpty()) {
            $this->info('No pending payments to check.');
            return;
        }
        
        $this->info("Found {$pendingPayments->count()} pending payments to check.");
        
        $bar = $this->output->createProgressBar($pendingPayments->count());
        $bar->start();
        
        $syncedCount = 0;
        
        foreach ($pendingPayments as $payment) {
            try {
                $status = $this->midtransService->getTransactionStatus($payment->gateway_transaction_id);
                
                if ($this->hasStatusMismatch($payment, $status)) {
                    $this->line("\nSyncing payment ID {$payment->id} (status: {$status['transaction_status']})");
                    $this->syncPaymentStatus($payment, $status);
                    $syncedCount++;
                }
            } catch (\Exception $e) {
                Log::error("Error checking payment status", [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->line("");
        $this->info("Monitoring completed. Synced {$syncedCount} payments.");
    }
    
    /**
     * Check if there's a status mismatch
     */
    private function hasStatusMismatch($payment, $midtransStatus)
    {
        $transactionStatus = $midtransStatus['transaction_status'];
        
        // Mapping Midtrans status to expected payment status
        $expectedStatus = match($transactionStatus) {
            'capture', 'settlement' => 'Paid',
            'pending' => 'Unpaid',
            'deny', 'cancel', 'expire', 'failure' => 'Failed',
            'refund', 'partial_refund' => 'Refunded',
            default => null
        };
        
        return $expectedStatus && $payment->payment_status !== $expectedStatus;
    }
    
    /**
     * Ask user if they want to sync the status
     */
    private function askToSync($payment, $midtransStatus)
    {
        if ($this->confirm("Do you want to sync this payment status?")) {
            $this->syncPaymentStatus($payment, $midtransStatus);
        }
    }
    
    /**
     * Sync payment status with Midtrans
     */
    private function syncPaymentStatus($payment, $midtransStatus)
    {
        try {
            // Simulate notification to update status
            $notification = [
                'order_id' => "BOOK-{$payment->booking_id}-" . time(),
                'transaction_status' => $midtransStatus['transaction_status'],
                'fraud_status' => $midtransStatus['fraud_status'] ?? null,
                'transaction_id' => $payment->gateway_transaction_id
            ];
            
            // Use reflection to call private method
            $reflection = new \ReflectionClass($this->midtransService);
            $method = $reflection->getMethod('updatePaymentStatus');
            $method->setAccessible(true);
            $method->invoke(
                $this->midtransService, 
                $payment, 
                $midtransStatus['transaction_status'],
                $midtransStatus['fraud_status'] ?? null,
                $notification
            );
            
            $this->info("Payment status synced successfully.");
            
            Log::info('Payment status manually synced', [
                'payment_id' => $payment->id,
                'old_status' => $payment->payment_status,
                'new_status' => $midtransStatus['transaction_status']
            ]);
            
        } catch (\Exception $e) {
            $this->error("Error syncing payment status: {$e->getMessage()}");
        }
    }
}