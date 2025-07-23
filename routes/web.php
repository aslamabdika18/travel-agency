<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;

// Rute halaman utama
Route::get('/', [PageController::class, 'home'])->name('home');

// Rute halaman informasi
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

// Rute paket perjalanan
Route::get('/travelpackages', [PageController::class, 'travelPackages'])->name('travel-packages');
Route::get('/travel-packages/{slug}', [PageController::class, 'travelPackageDetail'])->name('travel-package-detail');

// Rute autentikasi
Route::get('/auth', [PageController::class, 'login'])->name('auth');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/unauthorized', [PageController::class, 'unauthorized'])->name('unauthorized');

// Rute booking (memerlukan autentikasi)
Route::middleware('auth')->group(function () {
    // Redirect ke halaman detail paket untuk booking
    Route::get('/booking', [PageController::class, 'booking'])->name('booking');
    // Proses penyimpanan booking
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    // Lihat detail booking (API)
    Route::get('/api/booking/{id}', [BookingController::class, 'show'])->name('booking.show');
    // Lihat detail booking (Web)
    Route::get('/booking/{id}', [PageController::class, 'bookingDetail'])->name('booking.detail');
    // Lihat daftar booking pengguna
    Route::get('/user/bookings', [PageController::class, 'userBookings'])->name('user-bookings');
    // Halaman refund
    Route::get('/refund', [PageController::class, 'refund'])->name('refund');
});

// Rute pembayaran
Route::prefix('payment')->group(function () {
    Route::get('/error', [PageController::class, 'paymentError'])->name('payment-error');
    Route::get('/callback', [PageController::class, 'paymentCallback'])->name('payment-callback');

    // Route untuk retry dan continue payment dari notifikasi email
    Route::get('/retry/{payment}', [PageController::class, 'paymentRetry'])->name('payment.retry');
    Route::get('/continue/{payment}', [PageController::class, 'paymentContinue'])->name('payment.continue');
    
    // Route untuk download invoice PDF
    Route::middleware(['auth', 'invoice.logger'])->get('/invoice/{payment}', [PageController::class, 'downloadInvoice'])->name('payment.invoice');
});

// Notifications routes
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [PageController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{notification}/mark-as-read', [PageController::class, 'markNotificationAsRead'])->name('notifications.mark-as-read');
});

// Rute contoh toast notification
require __DIR__.'/toast.php';
