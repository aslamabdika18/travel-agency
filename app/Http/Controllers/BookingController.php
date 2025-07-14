<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\TravelPackage;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }
    /**
     * Display a listing of user's bookings.
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $bookings = Booking::where('user_id', $user->id)
                ->with(['travelPackage:id,name,slug,price,duration'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'booking_reference' => $booking->booking_reference,
                        'travel_package' => [
                            'id' => $booking->travelPackage->id,
                            'name' => $booking->travelPackage->name,
                            'slug' => $booking->travelPackage->slug,
                            'price' => $booking->travelPackage->price,
                            'duration' => $booking->travelPackage->duration,
                        ],
                        'travel_date' => $booking->travel_date,
                        'person_count' => $booking->person_count,
                        'total_price' => $booking->total_price,
                        'status' => $booking->status,
                        'payment_status' => $booking->payment_status,
                        'created_at' => $booking->created_at,
                        'updated_at' => $booking->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bookings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        Log::info('=== BOOKING FORM SUBMITTED ===', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'headers' => $request->headers->all()
        ]);

        Log::info('=== STARTING VALIDATION ===', [
            'request_data' => $request->all()
        ]);
        
        $validator = Validator::make($request->all(), [
            'travel_package_id' => 'required|exists:travel_packages,id',
            'booking_date' => 'required|date|after:today',
            'person_count' => 'required|integer|min:1|max:12',
            'special_requests' => 'nullable|string|max:1000',
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'terms' => 'required|accepted',
        ]);
        
        Log::info('=== VALIDATION COMPLETED ===', [
            'validation_passed' => !$validator->fails(),
            'errors' => $validator->errors()->toArray()
        ]);

        if ($validator->fails()) {
            // Check if request expects JSON (API call)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Web form submission - redirect back with errors
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('toast_error', 'Please check your form data and try again.');
        }

        try {
            $user = Auth::user();
            $travelPackage = TravelPackage::findOrFail($request->travel_package_id);
            
            // Calculate pricing breakdown using TravelPackage method
            $priceData = $travelPackage->calculatePrice($request->person_count);
            
            $basePrice = $priceData['base_price'];
            $additionalPrice = $priceData['additional_price'];
            $taxAmount = $priceData['tax_amount'];
            $totalPrice = $priceData['total_price'];
            
            // Generate unique booking reference
            $bookingReference = 'BK-' . strtoupper(Str::random(8)) . '-' . date('Ymd');
            
            $booking = Booking::create([
                'user_id' => $user->id,
                'travel_package_id' => $request->travel_package_id,
                'booking_reference' => $bookingReference,
                'booking_date' => $request->booking_date,
                'person_count' => $request->person_count,
                'base_price' => $basePrice,
                'additional_price' => $additionalPrice,
                'tax_amount' => $taxAmount,
                'total_price' => $totalPrice,
                'special_requests' => $request->special_requests,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Muat relasi travelPackage dengan eager loading
            $booking->load(['travelPackage' => function($query) {
                $query->select('id', 'name', 'slug', 'price', 'duration');
            }]);

            // Buat payment record
            $payment = $booking->createPayment([
                'payment_status' => 'Unpaid',
                'total_price' => $booking->total_price,
                'payment_reference' => 'BOOK-' . $booking->id . '-' . time()
            ]);

            // Get Snap redirect URL from Midtrans sesuai dokumentasi resmi
            Log::info('=== CREATING SNAP REDIRECT URL ===', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'total_price' => $booking->total_price
            ]);
            
            try {
                $snapRedirectUrl = $this->midtransService->createSnapRedirectUrl($booking, $payment);
                
                Log::info('=== SNAP REDIRECT URL CREATED ===', [
                    'booking_id' => $booking->id,
                    'snap_url' => $snapRedirectUrl
                ]);
            } catch (\Exception $e) {
                Log::error('=== SNAP REDIRECT URL FAILED ===', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to create payment URL: ' . $e->getMessage()
                    ], 500);
                }
                
                return redirect()->back()
                    ->with('toast_error', 'Failed to create payment URL. Please try again.')
                    ->withInput();
            }

            if (!$snapRedirectUrl) {
                Log::error('=== SNAP REDIRECT URL EMPTY ===', [
                    'booking_id' => $booking->id
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to create payment URL'
                    ], 500);
                }
                
                return redirect()->back()
                    ->with('toast_error', 'Failed to create payment URL. Please contact support.')
                    ->withInput();
            }

            // Check if request expects JSON (API call)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully',
                    'data' => [
                        'booking' => $booking,
                        'payment' => $payment,
                        'snap_redirect_url' => $snapRedirectUrl,
                        'payment_reference' => $payment->payment_reference
                    ]
                ], 201);
            }
            
            // Web form submission - redirect to payment page
            // Simpan informasi booking di session untuk ditampilkan jika user kembali
            session(['last_booking' => $booking->id]);
            
            Log::info('=== REDIRECTING TO SNAP URL ===', [
                'booking_id' => $booking->id,
                'snap_url' => $snapRedirectUrl,
                'session_booking' => session('last_booking')
            ]);
            
            return redirect()->to($snapRedirectUrl);
        } catch (\Exception $e) {
            // Check if request expects JSON (API call)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create booking',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            // Web form submission - redirect back with error
            return redirect()->back()
                ->with('toast_error', 'Failed to create booking. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified booking.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $booking = Booking::where('id', $id)
                ->where('user_id', $user->id)
                ->with(['travelPackage:id,name,slug,price,duration,description'])
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $booking->id,
                    'booking_reference' => $booking->booking_reference,
                    'travel_package' => [
                        'id' => $booking->travelPackage->id,
                        'name' => $booking->travelPackage->name,
                        'slug' => $booking->travelPackage->slug,
                        'price' => $booking->travelPackage->price,
                        'duration' => $booking->travelPackage->duration,
                        'description' => $booking->travelPackage->description,
                    ],
                    'travel_date' => $booking->travel_date,
                    'person_count' => $booking->person_count,
                    'total_price' => $booking->total_price,
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'special_requests' => $booking->special_requests,
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified booking (mainly for cancellation).
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:cancelled',
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
            
            $booking = Booking::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            // Check if booking can be cancelled
            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking is already cancelled'
                ], 400);
            }

            if ($booking->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel completed booking'
                ], 400);
            }

            $booking->update([
                'status' => 'cancelled',
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully',
                'data' => [
                    'id' => $booking->id,
                    'status' => $booking->status,
                    'updated_at' => $booking->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get booking draft for current user.
     */
    public function getDraft(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Get the most recent pending booking as draft
            $draft = Booking::where('user_id', $user->id)
                ->where('status', 'pending')
                ->where('payment_status', 'pending')
                ->with(['travelPackage:id,name,slug,price'])
                ->latest()
                ->first();

            if (!$draft) {
                return response()->json([
                    'success' => true,
                    'data' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $draft->id,
                    'booking_reference' => $draft->booking_reference,
                    'travel_package' => [
                        'id' => $draft->travelPackage->id,
                        'name' => $draft->travelPackage->name,
                        'slug' => $draft->travelPackage->slug,
                        'price' => $draft->travelPackage->price,
                    ],
                    'travel_date' => $draft->travel_date,
                    'person_count' => $draft->person_count,
                    'total_price' => $draft->total_price,
                    'created_at' => $draft->created_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch booking draft',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}