<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessAutomaticRefunds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refund:process-automatic 
                            {--dry-run : Run without making actual changes}
                            {--days=7 : Number of days before departure to process automatic refunds}
                            {--limit=50 : Maximum number of bookings to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automatic refunds for bookings that are eligible based on cancellation policies';

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
        $dryRun = $this->option('dry-run');
        $daysThreshold = (int) $this->option('days');
        $limit = (int) $this->option('limit');

        $this->info('Starting automatic refund processing...');
        $this->info('Dry run: ' . ($dryRun ? 'Yes' : 'No'));
        $this->info('Days threshold: ' . $daysThreshold);
        $this->info('Limit: ' . $limit);
        $this->newLine();

        // Find bookings that might need automatic refunds
        $bookings = $this->getEligibleBookings($daysThreshold, $limit);

        if ($bookings->isEmpty()) {
            $this->info('No bookings found that require automatic refund processing.');
            return 0;
        }

        $this->info("Found {$bookings->count()} bookings to process.");
        $this->newLine();

        $processed = 0;
        $errors = 0;
        $skipped = 0;

        foreach ($bookings as $booking) {
            $result = $this->processBookingRefund($booking, $dryRun);
            
            switch ($result['status']) {
                case 'processed':
                    $processed++;
                    $this->line("✓ Processed: {$booking->booking_reference} - {$result['message']}");
                    break;
                case 'error':
                    $errors++;
                    $this->error("✗ Error: {$booking->booking_reference} - {$result['message']}");
                    break;
                case 'skipped':
                    $skipped++;
                    $this->warn("⚠ Skipped: {$booking->booking_reference} - {$result['message']}");
                    break;
            }
        }

        $this->newLine();
        $this->info('Processing completed:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Processed', $processed],
                ['Errors', $errors],
                ['Skipped', $skipped],
                ['Total', $bookings->count()]
            ]
        );

        // Log summary
        Log::info('Automatic refund processing completed', [
            'dry_run' => $dryRun,
            'days_threshold' => $daysThreshold,
            'total_bookings' => $bookings->count(),
            'processed' => $processed,
            'errors' => $errors,
            'skipped' => $skipped
        ]);

        return $errors > 0 ? 1 : 0;
    }

    /**
     * Get bookings eligible for automatic refund processing
     */
    private function getEligibleBookings(int $daysThreshold, int $limit)
    {
        $cutoffDate = Carbon::now()->addDays($daysThreshold);

        return Booking::with(['payment', 'travelPackage', 'user'])
            ->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->whereHas('payment', function ($query) {
                $query->where('payment_status', 'Paid');
            })
            ->whereHas('travelPackage', function ($query) use ($cutoffDate) {
                // Assuming travel packages have a departure_date field
                // If not, you might need to adjust this based on your schema
                $query->where('departure_date', '<=', $cutoffDate);
            })
            ->orderBy('booking_date', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Process refund for a single booking
     */
    private function processBookingRefund(Booking $booking, bool $dryRun): array
    {
        try {
            // Check if booking can be refunded
            if (!$booking->canBeRefunded()) {
                return [
                    'status' => 'skipped',
                    'message' => 'Not eligible for refund based on policy'
                ];
            }

            // Check if already refunded
            if ($booking->status === 'refunded') {
                return [
                    'status' => 'skipped',
                    'message' => 'Already refunded'
                ];
            }

            // Get refund details
            $refundDetails = $booking->getRefundPolicyDetails();
            
            if (!$refundDetails['can_refund']) {
                return [
                    'status' => 'skipped',
                    'message' => $refundDetails['message']
                ];
            }

            if ($dryRun) {
                return [
                    'status' => 'processed',
                    'message' => "Would refund {$refundDetails['formatted_refund_amount']} ({$refundDetails['refund_percentage']}%)"
                ];
            }

            // Process the actual refund
            $refundResult = $booking->processRefund('Automatic refund due to cancellation policy');

            if (!$refundResult['success']) {
                return [
                    'status' => 'error',
                    'message' => $refundResult['message']
                ];
            }

            // Process Midtrans refund if applicable
            if ($booking->payment && $booking->payment->gateway_transaction_id) {
                try {
                    $refundAmount = (int) $refundResult['refund_amount'];
                    $midtransRefund = $this->midtransService->refundTransaction(
                        $booking->payment->gateway_transaction_id,
                        $refundAmount
                    );

                    Log::info('Automatic Midtrans refund initiated', [
                        'booking_id' => $booking->id,
                        'transaction_id' => $booking->payment->gateway_transaction_id,
                        'refund_amount' => $refundAmount,
                        'midtrans_response' => $midtransRefund
                    ]);

                } catch (\Exception $e) {
                    Log::error('Failed to process automatic Midtrans refund', [
                        'booking_id' => $booking->id,
                        'transaction_id' => $booking->payment->gateway_transaction_id,
                        'error' => $e->getMessage()
                    ]);
                    
                    // Don't fail the entire process if Midtrans refund fails
                    // The booking status has already been updated
                }
            }

            return [
                'status' => 'processed',
                'message' => "Refunded {$refundResult['formatted_refund_amount']} ({$refundDetails['refund_percentage']}%)"
            ];

        } catch (\Exception $e) {
            Log::error('Error processing automatic refund', [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }
}