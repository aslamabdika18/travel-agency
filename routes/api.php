<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PublicTravelPackageController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\Api\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Authentication Routes
Route::prefix('auth')->group(function () {
    // Public routes (tidak memerlukan authentication)
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Check authentication status
    Route::get('/check', [AuthController::class, 'check']);

    // Protected routes (memerlukan authentication)
    Route::middleware('auth:web')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });
});
// Public Travel Package Routes (tidak memerlukan authentication)
Route::prefix('travel-packages')->group(function () {
    Route::get('/', [PublicTravelPackageController::class, 'index']); // Get all travel packages
    Route::get('/{id}', [PublicTravelPackageController::class, 'show'])->where('id', '[0-9]+'); // Get travel package by ID
    Route::get('/slug/{slug}', [PublicTravelPackageController::class, 'showBySlug']); // Get travel package by slug
    Route::post('/{id}/calculate-price', [PublicTravelPackageController::class, 'calculatePrice'])->where('id', '[0-9]+'); // Calculate price by ID
    Route::post('/calculate-price/{slug}', [PublicTravelPackageController::class, 'calculatePriceBySlug']); // Calculate price by slug
});

// Public Category Routes (tidak memerlukan authentication)
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']); // Get all categories
    Route::get('/{category}', [CategoryController::class, 'show']); // Get specific category
    Route::get('/{category}/travel-packages', [CategoryController::class, 'travelPackages']); // Get travel packages by category
});

// Public Recommendation Routes
Route::prefix('recommendations')->group(function () {
    Route::get('/homepage', [RecommendationController::class, 'getHomepageRecommendations']); // Get homepage recommendations
    Route::get('/trending', [RecommendationController::class, 'getTrendingPackages']); // Get trending packages
    Route::get('/similar/{packageId}', [RecommendationController::class, 'getSimilarPackages'])->where('packageId', '[0-9]+'); // Get similar packages
    Route::get('/category/{categoryId}', [RecommendationController::class, 'getRecommendationsByCategory'])->where('categoryId', '[0-9]+'); // Get recommendations by category
    Route::get('/price-range', [RecommendationController::class, 'getRecommendationsByPriceRange']); // Get recommendations by price range
});

// Public Payment Routes (for webhook notifications and status checks)
Route::prefix('payment')->middleware('throttle:10,1')->group(function () {
    // Payment gateway notification webhook (public access)
    Route::post('/notification', [PaymentController::class, 'handleNotification'])
        ->name('payment.notification')
        ->middleware(['midtrans.webhook'])
        ->withoutMiddleware(['throttle:api']);
    // Payment status check (public access for callback page)
    Route::get('/status', [PaymentController::class, 'getStatus']);
    // Get payment by reference (public access)
    Route::get('/reference', [PaymentController::class, 'getByReference']);
});

// Protected API routes
Route::middleware('auth:web')->group(function () {
    // User profile routes
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    // Booking routes
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']); // Get user bookings
        Route::post('/', [BookingController::class, 'store']); // Create new booking
        Route::get('/{id}', [BookingController::class, 'show']); // Get specific booking
        Route::put('/{id}', [BookingController::class, 'update']); // Update booking (cancel)
    });

    // Booking draft endpoint
    Route::get('/booking/draft', [BookingController::class, 'getDraft']);

    // Payment routes
    Route::prefix('payment')->group(function () {
        Route::get('/history', [PaymentController::class, 'getHistory']); // Get payment history
        Route::post('/validate', [PaymentController::class, 'validatePaymentData']); // Validate payment data
        // Only Snap redirect method is used
        Route::post('/create-snap-redirect/{bookingId}', [PaymentController::class, 'createSnapRedirectUrl']);
    });

    // Refund routes
    Route::prefix('refund')->group(function () {
        Route::get('/policy', [RefundController::class, 'getRefundPolicy']); // Get refund policy for booking
        Route::post('/process', [RefundController::class, 'processRefund']); // Process refund request
        Route::get('/eligible-bookings', [RefundController::class, 'getEligibleBookings']); // Get bookings eligible for refund
        Route::get('/history', [RefundController::class, 'getRefundHistory']); // Get refund history
    });

    // Personalized Recommendation routes (requires authentication)
    Route::prefix('recommendations')->group(function () {
        Route::get('/personalized', [RecommendationController::class, 'getPersonalizedRecommendations']); // Get personalized recommendations
        Route::get('/explanation', [RecommendationController::class, 'getRecommendationExplanation']); // Get recommendation explanation
    });
});

// TF-IDF Demo Routes (public access)
Route::prefix('tf-idf-demo')->group(function () {
    Route::get('/packages', [RecommendationController::class, 'getDemoPackages']); // Get packages for demo
    Route::get('/analyze/{id}', [RecommendationController::class, 'analyzeTfIdf'])->where('id', '[0-9]+'); // Analyze TF-IDF for package
});
