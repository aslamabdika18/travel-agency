<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\MidtransService;
use App\Http\Requests\RefundRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Get refund policy details for a booking
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getRefundPolicy(Request $request): JsonResponse
    {
        // Check if refund system is enabled
        if (!$this->isRefundEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Refund service is temporarily disabled'
            ], 503);
        }

        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer|exists:bookings,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $booking = Booking::with(['payment', 'travelPackage'])->findOrFail($request->booking_id);

            // Check if user owns this booking (if authenticated)
            if (Auth::check() && $booking->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to booking'
                ], 403);
            }

            $refundDetails = $booking->getRefundPolicyDetails();

            return response()->json([
                'success' => true,
                'data' => [
                    'booking' => [
                        'id' => $booking->id,
                        'booking_reference' => $booking->booking_reference,
                        'status' => $booking->status,
                        'payment_status' => $booking->payment_status,
                        'booking_date' => $booking->booking_date?->format('Y-m-d'),
                        'total_price' => $booking->total_price,
                        'formatted_total_price' => formatRupiah($booking->total_price),
                    ],
                    'refund_policy' => $refundDetails,
                    'policy_explanation' => [
                        '30+ days before departure' => '100% refund',
                        '15-29 days before departure' => '50% refund',
                        '7-14 days before departure' => '25% refund',
                        'Less than 7 days before departure' => 'No refund'
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get refund policy', [
                'booking_id' => $request->booking_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve refund policy'
            ], 500);
        }
    }

    /**
     * Process refund request
     *
     * @param RefundRequest $request
     * @return JsonResponse
     */
    public function processRefund(RefundRequest $request): JsonResponse
    {
        // Check if refund system is enabled
        if (!$this->isRefundEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Refund service is temporarily disabled'
            ], 503);
        }

        try {
            $booking = Booking::with(['payment', 'travelPackage', 'user'])->findOrFail($request->booking_id);

            // Authorization and validation already handled by RefundRequest

            // Process the refund
            $refundResult = $booking->processRefund($request->reason);

            if (!$refundResult['success']) {
                return response()->json($refundResult, 400);
            }

            // If booking has payment with gateway_transaction_id, process Midtrans refund
            if ($booking->payment && $booking->payment->gateway_transaction_id) {
                try {
                    $refundAmount = (int) $refundResult['refund_amount'];
                    $midtransRefund = $this->midtransService->refundTransaction(
                        $booking->payment->gateway_transaction_id,
                        $refundAmount
                    );

                    Log::info('Midtrans refund initiated', [
                        'booking_id' => $booking->id,
                        'transaction_id' => $booking->payment->gateway_transaction_id,
                        'refund_amount' => $refundAmount,
                        'midtrans_response' => $midtransRefund
                    ]);

                    $refundResult['midtrans_refund'] = [
                        'initiated' => true,
                        'transaction_id' => $booking->payment->gateway_transaction_id,
                        'refund_key' => $midtransRefund->refund_key ?? null
                    ];

                } catch (\Exception $e) {
                    Log::error('Failed to process Midtrans refund', [
                        'booking_id' => $booking->id,
                        'transaction_id' => $booking->payment->gateway_transaction_id,
                        'error' => $e->getMessage()
                    ]);

                    $refundResult['midtrans_refund'] = [
                        'initiated' => false,
                        'error' => 'Failed to process payment gateway refund'
                    ];
                }
            }

            // Log successful refund
            Log::info('Refund request processed successfully', [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'user_id' => $booking->user_id,
                'refund_amount' => $refundResult['refund_amount'],
                'reason' => $request->reason,
                'processed_by' => Auth::id() ?? 'system',
                'processed_at' => now()
            ]);

            return response()->json($refundResult);

        } catch (\Exception $e) {
            Log::error('Failed to process refund request', [
                'booking_id' => $request->booking_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process refund request'
            ], 500);
        }
    }

    /**
     * Get list of bookings eligible for refund
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getEligibleBookings(Request $request): JsonResponse
    {
        // Check if refund system is enabled
        if (!$this->isRefundEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Refund service is temporarily disabled'
            ], 503);
        }

        try {
            $query = Booking::with(['payment', 'travelPackage'])
                ->eligibleForRefund();

            // Filter by user if authenticated
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            }

            $bookings = $query->orderBy('booking_date', 'asc')
                ->paginate($request->get('per_page', 10));

            $bookings->getCollection()->transform(function ($booking) {
                return [
                    'id' => $booking->id,
                    'booking_reference' => $booking->booking_reference,
                    'travel_package' => [
                        'name' => $booking->travelPackage?->name,
                        'slug' => $booking->travelPackage?->slug,
                    ],
                    'booking_date' => $booking->booking_date?->format('Y-m-d'),
                    'total_price' => $booking->total_price,
                    'formatted_total_price' => formatRupiah($booking->total_price),
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'refund_policy' => $booking->getRefundPolicyDetails()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get eligible bookings for refund', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve eligible bookings'
            ], 500);
        }
    }

    /**
     * Get refund history for a user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getRefundHistory(Request $request): JsonResponse
    {
        // Check if refund system is enabled
        if (!$this->isRefundEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Refund service is temporarily disabled'
            ], 503);
        }

        try {
            $query = Booking::with(['payment', 'travelPackage'])
                ->where('status', 'refunded');

            // Filter by user if authenticated
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            }

            $refunds = $query->orderBy('updated_at', 'desc')
                ->paginate($request->get('per_page', 10));

            $refunds->getCollection()->transform(function ($booking) {
                return [
                    'id' => $booking->id,
                    'booking_reference' => $booking->booking_reference,
                    'travel_package' => [
                        'name' => $booking->travelPackage?->name,
                        'slug' => $booking->travelPackage?->slug,
                    ],
                    'booking_date' => $booking->booking_date?->format('Y-m-d'),
                    'total_price' => $booking->total_price,
                    'formatted_total_price' => formatRupiah($booking->total_price),
                    'refunded_at' => $booking->updated_at?->format('Y-m-d H:i:s'),
                    'payment' => [
                        'payment_status' => $booking->payment?->payment_status,
                        'gateway_status' => $booking->payment?->gateway_status,
                        'gateway_transaction_id' => $booking->payment?->gateway_transaction_id,
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $refunds
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get refund history', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve refund history'
            ], 500);
        }
    }

    /**
     * Check if refund system is enabled
     *
     * @return bool
     */
    private function isRefundEnabled(): bool
    {
        return config('app.refund_enabled', env('REFUND_ENABLED', true));
    }
}
