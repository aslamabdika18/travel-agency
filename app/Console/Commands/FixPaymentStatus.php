<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixPaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:fix-status {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix inconsistent payment and booking statuses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Checking for payment status inconsistencies...');
        
        // 1. Cari payment yang paid tapi booking masih pending
        $paidPaymentsWithPendingBookings = Payment::where('payment_status', 'Paid')
            ->whereHas('booking', function($query) {
                $query->where('status', 'pending')
                      ->orWhere('payment_status', 'unpaid');
            })
            ->with('booking')
            ->get();
            
        if ($paidPaymentsWithPendingBookings->count() > 0) {
            $this->warn("Found {$paidPaymentsWithPendingBookings->count()} paid payments with pending bookings:");
            
            foreach ($paidPaymentsWithPendingBookings as $payment) {
                $this->line("- Payment ID: {$payment->id}, Booking ID: {$payment->booking->id}");
                
                if (!$dryRun) {
                    $payment->booking->update([
                        'status' => 'confirmed',
                        'payment_status' => 'paid'
                    ]);
                    $this->info("  ✓ Fixed booking status");
                }
            }
        }
        
        // 2. Cari payment yang unpaid tapi booking sudah confirmed
        $unpaidPaymentsWithConfirmedBookings = Payment::where('payment_status', 'Unpaid')
            ->whereHas('booking', function($query) {
                $query->where('status', 'confirmed')
                      ->orWhere('payment_status', 'paid');
            })
            ->with('booking')
            ->get();
            
        if ($unpaidPaymentsWithConfirmedBookings->count() > 0) {
            $this->warn("Found {$unpaidPaymentsWithConfirmedBookings->count()} unpaid payments with confirmed bookings:");
            
            foreach ($unpaidPaymentsWithConfirmedBookings as $payment) {
                $this->line("- Payment ID: {$payment->id}, Booking ID: {$payment->booking->id}");
                
                if (!$dryRun) {
                    // Cek apakah ini seharusnya paid berdasarkan gateway status
                    if (in_array($payment->gateway_status, ['capture', 'settlement'])) {
                        $payment->markAsPaid();
                        $this->info("  ✓ Marked payment as paid based on gateway status");
                    } else {
                        $payment->booking->update([
                            'status' => 'pending',
                            'payment_status' => 'unpaid'
                        ]);
                        $this->info("  ✓ Reverted booking to pending status");
                    }
                }
            }
        }
        
        // 3. Cari payment tanpa gateway_transaction_id tapi sudah paid
        $paidPaymentsWithoutGatewayId = Payment::where('payment_status', 'Paid')
            ->whereNull('gateway_transaction_id')
            ->get();
            
        if ($paidPaymentsWithoutGatewayId->count() > 0) {
            $this->warn("Found {$paidPaymentsWithoutGatewayId->count()} paid payments without gateway transaction ID:");
            
            foreach ($paidPaymentsWithoutGatewayId as $payment) {
                $this->line("- Payment ID: {$payment->id}");
                $this->warn("  ⚠ This payment may need manual verification");
            }
        }
        
        // 4. Cari booking tanpa payment record
        $bookingsWithoutPayment = Booking::whereDoesntHave('payment')
            ->where('status', '!=', 'cancelled')
            ->get();
            
        if ($bookingsWithoutPayment->count() > 0) {
            $this->warn("Found {$bookingsWithoutPayment->count()} bookings without payment records:");
            
            foreach ($bookingsWithoutPayment as $booking) {
                $this->line("- Booking ID: {$booking->id}, Reference: {$booking->booking_reference}");
                
                if (!$dryRun) {
                    // Buat payment record dengan status unpaid
                    Payment::create([
                        'booking_id' => $booking->id,
                        'total_price' => $booking->total_price,
                        'payment_status' => 'Unpaid',
                        'payment_reference' => 'PAY-' . $booking->id . '-' . time()
                    ]);
                    $this->info("  ✓ Created payment record");
                }
            }
        }
        
        if ($dryRun) {
            $this->info('\nDry run completed. Use --no-dry-run to apply fixes.');
        } else {
            $this->info('\nPayment status fixes completed.');
            Log::info('Payment status fix command executed', [
                'paid_with_pending_bookings' => $paidPaymentsWithPendingBookings->count(),
                'unpaid_with_confirmed_bookings' => $unpaidPaymentsWithConfirmedBookings->count(),
                'paid_without_gateway_id' => $paidPaymentsWithoutGatewayId->count(),
                'bookings_without_payment' => $bookingsWithoutPayment->count()
            ]);
        }
        
        return 0;
    }
}