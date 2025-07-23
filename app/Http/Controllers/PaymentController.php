<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\MidtransService;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }
    /**
     * Get payment history for the authenticated user.
     */
    public function getHistory(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Get payments through user's bookings
            $payments = Payment::whereHas('booking', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with([
                'booking:id,booking_reference,travel_package_id,total_price,booking_date',
                'booking.travelPackage:id,name,slug'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'payment_reference' => $payment->payment_reference,
                    'booking' => [
                        'id' => $payment->booking->id,
                        'booking_reference' => $payment->booking->booking_reference,
                        'travel_package' => [
                            'id' => $payment->booking->travelPackage->id,
                            'name' => $payment->booking->travelPackage->name,
                            'slug' => $payment->booking->travelPackage->slug,
                        ],
                        'travel_date' => $payment->booking->booking_date,
                        'total_price' => $payment->booking->total_price,
                    ],
                    'amount' => $payment->total_price,

                    'payment_status' => $payment->payment_status,
                    'transaction_id' => $payment->transaction_id,
                    'payment_date' => $payment->payment_date,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment history retrieved successfully',
                'data' => $payments
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch payment history', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment history',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Handle Midtrans notification webhook sesuai dokumentasi resmi
     */
    public function handleNotification(Request $request)
    {
        try {
            // Log incoming notification untuk debugging
            Log::info('Received Midtrans webhook notification', [
                'headers' => $request->headers->all(),
                'body' => $request->all(),
                'ip' => $request->ip()
            ]);
            
            // Validasi bahwa request berasal dari Midtrans
            $this->validateMidtransRequest($request);
            
            $result = $this->midtransService->handleNotification();
            
            Log::info('Midtrans notification processed successfully', [
                'result' => $result
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Notification processed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling Midtrans notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process notification'
            ], 500);
        }
    }

    /**
     * Validate that the request comes from Midtrans
     */
    private function validateMidtransRequest(Request $request): void
    {
        // Validasi basic untuk memastikan request memiliki data yang diperlukan
        $requiredFields = ['order_id', 'transaction_status', 'transaction_id'];
        
        foreach ($requiredFields as $field) {
            if (!$request->has($field)) {
                throw new \Exception("Missing required field: {$field}");
            }
        }
        
        // Validasi format order_id
        $orderId = $request->input('order_id');
        if (!preg_match('/^BOOK-\d+-\d+$/', $orderId)) {
            throw new \Exception("Invalid order_id format: {$orderId}");
        }
    }

    /**
     * Map transaction status from payment gateway to internal payment status.
     */
    private function mapTransactionStatusToPaymentStatus(string $transactionStatus): string
    {
        return match ($transactionStatus) {
            'capture', 'settlement' => 'completed',
            'pending' => 'pending',
            'deny', 'cancel', 'expire', 'failure' => 'failed',
            default => 'pending'
        };
    }

    /**
     * Validate payment data (used by frontend before processing).
     */
    public function validatePaymentData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'total_price' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $booking = Booking::where('id', $request->booking_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found or access denied'
                ], 404);
            }

            // Check if booking amount matches
            if (abs($booking->total_price - $request->total_price) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount does not match booking total',
                    'expected_amount' => $booking->total_price,
                    'provided_amount' => $request->total_price
                ], 400);
            }

            // Check if booking is in valid state for payment
            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot process payment for cancelled booking'
                ], 400);
            }

            if ($booking->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking has already been paid'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment data is valid',
                'data' => [
                    'booking_id' => $booking->id,
                    'booking_reference' => $booking->booking_reference,
                    'total_price' => $booking->total_price,

                    'is_valid' => true
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to validate payment data', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to validate payment data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    // Snap token method removed - only using Snap redirect method

    /**
     * Create Snap redirect URL for existing booking sesuai dokumentasi resmi Midtrans
     */
    public function createSnapRedirectUrl(Request $request, $bookingId)
    {
        try {
            $user = $request->user();
            
            // Get booking
            $booking = Booking::where('id', $bookingId)
                ->where('user_id', $user->id)
                ->with('travelPackage')
                ->firstOrFail();
            
            // Check if booking is already paid
            if ($booking->payment_status === 'Paid') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking is already paid'
                ], 400);
            }
            
            // Find or create payment
            $payment = Payment::firstOrCreate(
                ['booking_id' => $booking->id],
                [
                    'payment_reference' => 'PAY-' . $booking->id . '-' . time(),
                    'transaction_id' => 'TXN-' . $booking->id . '-' . time(),
                    'total_price' => $booking->total_price,
                    'payment_status' => 'Unpaid',
                    'payment_date' => now()
                ]
            );
            
            // Get Snap redirect URL menggunakan MidtransService
            $redirectUrl = $this->midtransService->createSnapRedirectUrl($booking, $payment);
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'redirect_url' => $redirectUrl,
                    'payment_reference' => $payment->payment_reference,
                    'total_price' => $booking->total_price
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Create Snap redirect URL error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create payment redirect URL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment by reference
     */
    public function getByReference(Request $request): JsonResponse
    {
        try {
            $reference = $request->query('reference');
            
            if (!$reference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment reference is required'
                ], 400);
            }
            
            $payment = Payment::byPaymentReference($reference)->first();
            
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }
            
            // Jika user terautentikasi, pastikan booking milik user tersebut
            $user = Auth::user();
            if ($user && $payment->booking->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'payment' => [
                        'id' => $payment->id,
                        'payment_reference' => $payment->payment_reference,
                        'transaction_id' => $payment->transaction_id,
                        'status' => $payment->payment_status,
                        'gateway_status' => $payment->gateway_status,
                        'amount' => $payment->total_price,
                        'payment_date' => $payment->payment_date
                    ],
                    'booking' => [
                        'id' => $payment->booking->id,
                        'status' => $payment->booking->status
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get payment by reference error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'reference' => $request->query('reference'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    
    /**
     * Get payment status for a specific booking
     * 
     * This method can be called from the payment callback page without authentication
     * or from the authenticated user's dashboard
     */
    public function getStatus(Request $request): JsonResponse
    {
        try {
            $orderId = $request->query('order_id');
            
            if (!$orderId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order ID is required'
                ], 400);
            }
            
            // Ekstrak booking_id dari order_id jika format adalah 'BOOK-{id}-{timestamp}'
            $bookingId = $orderId;
            if (strpos($orderId, 'BOOK-') === 0) {
                $parts = explode('-', $orderId);
                if (count($parts) >= 2) {
                    $bookingId = $parts[1];
                }
            }
            
            // Cari booking tanpa memeriksa user_id untuk callback Midtrans
            $booking = Booking::where('id', $bookingId)->first();
                
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }
            
            // Jika user terautentikasi, pastikan booking milik user tersebut
            $user = Auth::user();
            if ($user && $booking->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            $payment = Payment::where('booking_id', $booking->id)->first();
            
            $response = [
                'success' => true,
                'data' => [
                    'booking_id' => $booking->id,
                    'booking_status' => $booking->status,
                    'payment_status' => $booking->getPaymentStatus()
                ]
            ];
            
            if ($payment) {
                $response['data']['payment'] = [
                    'id' => $payment->id,
                    'status' => $payment->payment_status,
                    'gateway_status' => $payment->gateway_status,
                    'transaction_id' => $payment->transaction_id,
                    'amount' => $payment->total_price,
                    'payment_date' => $payment->payment_date
                ];
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Get payment status error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'order_id' => $request->query('order_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}