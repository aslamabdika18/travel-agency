<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\InvoiceLogger;

class PageController extends Controller
{
    /**
     * Menampilkan halaman utama
     */
    public function home()
    {
        // Ambil 3 travel package terbaru untuk ditampilkan di halaman home
        // Eager load semua relasi yang dibutuhkan untuk menghindari N+1 query
        $travelPackages = \App\Models\TravelPackage::with([
            'media',
            'reviews', // Untuk averageRating dan reviewCount accessor
            'travelIncludes' // Untuk includesList accessor
        ])
            ->latest()
            ->take(3)
            ->get();

        return view('pages.home', compact('travelPackages'));
    }

    /**
     * Menampilkan halaman tentang kami
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * Menampilkan halaman kontak
     */
    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * Menampilkan halaman paket perjalanan
     */
    public function travelPackages()
    {
        // Ambil semua travel packages dengan eager loading untuk menghindari N+1 query
        $travelPackages = \App\Models\TravelPackage::with([
            'media',
            'reviews', // Untuk averageRating dan reviewCount accessor
            'travelIncludes', // Untuk includesList accessor
            'travelExcludes' // Untuk excludesList accessor
        ])
            ->paginate(12); // Pagination dengan 12 item per halaman

        return view('pages.travel-packages', compact('travelPackages'));
    }

    /**
     * Menampilkan halaman detail paket perjalanan
     */
    public function travelPackageDetail($slug)
    {
        // Cari travel package berdasarkan slug
        $travelPackage = \App\Models\TravelPackage::with([
            'media',
            'itineraries',
            'travelIncludes',
            'travelExcludes',
            'reviews.user'
        ])->where('slug', $slug)->first();

        // Cek apakah ada hash #booking di URL atau session booking_intent
        $bookingIntent = false;
        if (request()->has('booking') || session('booking_intent')) {
            $bookingIntent = true;
            // Hapus session booking_intent setelah digunakan
            session()->forget('booking_intent');
        }

        // Kirim data travel package dan slug ke view
        return view('pages.travel-package-detail', [
            'travelPackage' => $travelPackage,
            'slug' => $slug,
            'bookingIntent' => $bookingIntent
        ]);
    }

    /**
     * Redirect ke halaman detail paket wisata untuk booking
     */
    public function booking(Request $request)
    {
        // Ambil travel package berdasarkan slug dari parameter
        $packageSlug = $request->get('package', 'sabang-island-explorer'); // Default package
        $travelPackage = \App\Models\TravelPackage::where('slug', $packageSlug)->first();

        // Jika package tidak ditemukan, gunakan package pertama yang tersedia
        if (!$travelPackage) {
            $travelPackage = \App\Models\TravelPackage::first();
        }

        // Redirect ke halaman detail paket dengan pesan untuk mengarahkan ke form booking
        return redirect()->route('travel-package-detail', $travelPackage->slug)
            ->with('booking_intent', true)
            ->with('toast_info', 'Please complete the booking form below to continue with your reservation.');
    }

    /**
     * Menampilkan halaman booking pengguna
     */
    public function userBookings()
    {
        $user = Auth::user();

        // Ambil semua booking milik user yang sedang login dengan eager loading dan pagination
        $bookings = \App\Models\Booking::with([
            'travelPackage.media',
            'payment'
        ])
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->paginate(10); // Pagination dengan 10 item per halaman

        return view('pages.user-bookings', compact('bookings', 'user'));
    }

    /**
     * Menampilkan halaman detail booking
     */
    public function bookingDetail($id)
    {
        $user = Auth::user();

        // Ambil booking dengan semua relasi yang diperlukan
        $booking = \App\Models\Booking::with([
            'travelPackage.media',
            'travelPackage.itineraries',
            'travelPackage.travelIncludes',
            'travelPackage.travelExcludes',
            'payment',
            'user'
        ])
        ->where('id', $id)
        ->where('user_id', $user->id)
        ->first();

        // Jika booking tidak ditemukan atau bukan milik user
        if (!$booking) {
            return redirect()->route('user-bookings')
                ->with('toast_error', 'Booking not found or you do not have permission to view it.');
        }

        return view('pages.booking-detail', compact('booking'));
    }

    /**
     * Menampilkan halaman syarat dan ketentuan
     */
    public function terms()
    {
        return view('pages.terms');
    }

    /**
     * Menampilkan halaman kebijakan privasi
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * Menampilkan halaman sukses pembayaran
     */


    /**
     * Menampilkan halaman error pembayaran
     */
    public function paymentError(Request $request)
    {
        $booking = null;
        $payment = null;
        $redirectUrl = route('home');

        // Coba ambil data dari berbagai sumber
        $orderId = $request->get('order_id');
        $lastBookingId = session('last_booking');

        // Prioritas 1: Cari berdasarkan order_id dari parameter
        if ($orderId) {
            // Order ID format: BOOKING-{booking_id}-{timestamp}
            if (preg_match('/BOOKING-(\d+)-/', $orderId, $matches)) {
                $bookingId = $matches[1];
                $booking = \App\Models\Booking::with([
                    'travelPackage.media',
                    'payment',
                    'user'
                ])->find($bookingId);
            }
        }

        // Prioritas 2: Cari berdasarkan session last_booking
        if (!$booking && $lastBookingId) {
            $booking = \App\Models\Booking::with([
                'travelPackage.media',
                'payment',
                'user'
            ])->find($lastBookingId);
        }

        // Prioritas 3: Ambil booking terakhir user yang sedang login (termasuk yang failed)
        if (!$booking && Auth::check()) {
            $booking = \App\Models\Booking::with([
                'travelPackage.media',
                'payment',
                'user'
            ])
            ->where('user_id', Auth::id())
            ->latest()
            ->first();
        }

        // Jika booking ditemukan
        if ($booking) {
            $payment = $booking->payment;

            // Set URL redirect
            if ($booking->travelPackage) {
                $redirectUrl = route('travel-package-detail', ['slug' => $booking->travelPackage->slug]);
            }
        }

        return view('pages.payment-error', [
            'booking' => $booking,
            'payment' => $payment,
            'redirectUrl' => $redirectUrl
        ]);
    }

    /**
     * Menampilkan halaman callback pembayaran
     */
    public function paymentCallback(Request $request)
    {
        $status = $request->query('status', 'finish');
        $booking = null;
        $payment = null;

        // Coba ambil data dari berbagai sumber
        $orderId = $request->get('order_id');
        $transactionStatus = $request->get('transaction_status');
        $lastBookingId = session('last_booking');

        // Prioritas 1: Cari berdasarkan order_id dari parameter
        if ($orderId) {
            // Order ID format: BOOKING-{booking_id}-{timestamp}
            if (preg_match('/BOOKING-(\d+)-/', $orderId, $matches)) {
                $bookingId = $matches[1];
                $booking = \App\Models\Booking::with([
                    'travelPackage.media',
                    'payment',
                    'user'
                ])->find($bookingId);
            }
        }

        // Prioritas 2: Cari berdasarkan session last_booking
        if (!$booking && $lastBookingId) {
            $booking = \App\Models\Booking::with([
                'travelPackage.media',
                'payment',
                'user'
            ])->find($lastBookingId);
        }

        // Prioritas 3: Ambil booking terakhir user yang sedang login
        if (!$booking && Auth::check()) {
            $booking = \App\Models\Booking::with([
                'travelPackage.media',
                'payment',
                'user'
            ])
            ->where('user_id', Auth::id())
            ->latest()
            ->first();
        }

        // Jika booking ditemukan
        if ($booking) {
            $payment = $booking->payment;

            // Jika ada transaction_status dari URL dan payment masih unpaid,
            // update status berdasarkan callback (fallback jika webhook tidak berfungsi)
            if ($payment && $transactionStatus && $payment->payment_status === 'Unpaid') {
                $this->updatePaymentFromCallback($payment, $transactionStatus, $request->all());

                // Refresh payment data setelah update
                $payment->refresh();
            }
        }

        return view('pages.payment-callback', [
            'status' => $status,
            'booking' => $booking,
            'payment' => $payment
        ]);
    }

    /**
     * Update payment status berdasarkan callback URL (fallback jika webhook tidak berfungsi)
     */
    private function updatePaymentFromCallback($payment, $transactionStatus, $callbackData)
    {
        try {
            Log::info('Updating payment status from callback', [
                'payment_id' => $payment->id,
                'transaction_status' => $transactionStatus,
                'callback_data' => $callbackData
            ]);

            // Update status berdasarkan transaction_status dari callback
            switch ($transactionStatus) {
                case 'capture':
                case 'settlement':
                    $payment->markAsPaid();
                    $payment->update([
                        'gateway_status' => $transactionStatus,
                        'payment_date' => now(),
                        'gateway_response' => $callbackData
                    ]);
                    Log::info('Payment marked as paid from callback', [
                        'payment_id' => $payment->id,
                        'transaction_status' => $transactionStatus
                    ]);
                    break;

                case 'pending':
                    $payment->update([
                        'payment_status' => 'Unpaid',
                        'gateway_status' => 'pending',
                        'gateway_response' => $callbackData
                    ]);
                    Log::info('Payment status updated to pending from callback', [
                        'payment_id' => $payment->id
                    ]);
                    break;

                case 'deny':
                case 'cancel':
                case 'expire':
                case 'failure':
                    $payment->markAsFailed();
                    $payment->update([
                        'gateway_status' => $transactionStatus,
                        'gateway_response' => $callbackData
                    ]);
                    Log::info('Payment marked as failed from callback', [
                        'payment_id' => $payment->id,
                        'reason' => $transactionStatus
                    ]);
                    break;

                default:
                    Log::warning('Unknown transaction status from callback', [
                        'payment_id' => $payment->id,
                        'transaction_status' => $transactionStatus
                    ]);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error updating payment from callback', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Menampilkan halaman login
     */
    public function login(Request $request)
    {
        // Jika ada parameter intended URL, simpan ke session
        if ($request->has('intended')) {
            $intended = $request->get('intended');
            session(['url.intended' => $intended]);

            // Jika intended URL mengandung #booking, set session booking_intent
            if (strpos($intended, '#booking') !== false) {
                session(['booking_intent' => true]);
            }
        }

        return view('pages.login');
    }

    /**
     * Menampilkan halaman tidak diizinkan
     */
    public function unauthorized()
    {
        return view('pages.unauthorized');
    }

    /**
     * Menampilkan halaman refund
     */
    public function refund()
    {
        return view('pages.refund');
    }

    /**
     * Redirect untuk retry pembayaran dari notifikasi email
     */
    public function paymentRetry(\App\Models\Payment $payment)
    {
        // Pastikan user memiliki akses ke payment ini
        if (!Auth::check() || $payment->booking->user_id !== Auth::id()) {
            return redirect()->route('auth')
                ->with('toast_error', 'Anda perlu login untuk mengakses halaman ini.');
        }

        // Jika payment sudah berhasil, redirect ke detail booking
        if ($payment->payment_status === 'Paid') {
            return redirect()->route('booking.detail', $payment->booking_id)
                ->with('toast_success', 'Pembayaran sudah berhasil diproses.');
        }

        // Redirect ke halaman detail paket dengan intent booking untuk retry payment
        return redirect()->route('travel-package-detail', $payment->booking->travelPackage->slug)
            ->with('retry_payment', $payment->id)
            ->with('toast_info', 'Silakan coba lakukan pembayaran ulang untuk booking Anda.');
    }

    /**
     * Redirect untuk melanjutkan pembayaran dari notifikasi email
     */
    public function paymentContinue(\App\Models\Payment $payment)
    {
        // Pastikan user memiliki akses ke payment ini
        if (!Auth::check() || $payment->booking->user_id !== Auth::id()) {
            return redirect()->route('auth')
                ->with('toast_error', 'Anda perlu login untuk mengakses halaman ini.');
        }

        // Jika payment sudah berhasil, redirect ke detail booking
        if ($payment->payment_status === 'Paid') {
            return redirect()->route('booking.detail', $payment->booking_id)
                ->with('toast_success', 'Pembayaran sudah berhasil diproses.');
        }

        // Jika payment sudah gagal, redirect ke retry
        if ($payment->payment_status === 'Failed') {
            return $this->paymentRetry($payment);
        }

        // Untuk payment yang masih pending, redirect ke halaman callback untuk monitoring
        return redirect()->route('payment-callback')
            ->with('continue_payment', $payment->id)
            ->with('toast_info', 'Silakan selesaikan pembayaran Anda.');
    }

    /**
     * Display notifications page
     */
    public function notifications()
    {
        $notifications = Auth::user()->notifications()->paginate(10);

        return view('pages.notifications', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
    
    /**
     * Download invoice PDF
     */
    public function downloadInvoice(\App\Models\Payment $payment)
    {
        $downloadStartTime = microtime(true);
        
        InvoiceLogger::logDownloadStart($payment);
        
        // Pastikan user memiliki akses ke payment ini
        if (!Auth::check()) {
            abort(401, 'Authentication required.');
        }
        
        if ($payment->booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to invoice.');
        }
        
        // Pastikan payment sudah berhasil
        if (!$payment->isPaid()) {
            return redirect()->back()
                ->with('toast_error', 'Invoice hanya tersedia untuk pembayaran yang sudah berhasil.');
        }
        
        try {
            $invoiceService = new \App\Services\InvoiceService();
            
            // Check service configuration
            if (!$invoiceService->isConfigured()) {
                return redirect()->back()
                    ->with('toast_error', 'Layanan invoice tidak tersedia saat ini.');
            }
            
            // Cek apakah invoice sudah ada
            $existingPath = $invoiceService->getInvoiceFilePath($payment);
            
            if ($existingPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($existingPath)) {
                $filePath = $existingPath;
            } else {
                // Generate invoice baru jika belum ada
                $filePath = $invoiceService->generateInvoicePdf($payment);
            }
            
            if (!$filePath) {
                return redirect()->back()
                    ->with('toast_error', 'Gagal menggenerate invoice PDF.');
            }
            
            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
                return redirect()->back()
                    ->with('toast_error', 'File invoice tidak ditemukan.');
            }
            
            $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($filePath);
            $fileName = 'Invoice-' . $payment->booking->booking_reference . '.pdf';
            $fileSize = \Illuminate\Support\Facades\Storage::disk('public')->size($filePath);
            
            // Verify physical file exists
            if (!file_exists($fullPath)) {
                return redirect()->back()
                    ->with('toast_error', 'File invoice tidak dapat diakses.');
            }
            
            // Check file size
            if ($fileSize === 0) {
                return redirect()->back()
                    ->with('toast_error', 'File invoice kosong atau rusak.');
            }
            
            $downloadEndTime = microtime(true);
            $duration = $downloadEndTime - $downloadStartTime;
            
            InvoiceLogger::logDownloadSuccess($payment, $fullPath, $duration);
            
            return response()->download($fullPath, $fileName, [
                'Content-Type' => 'application/pdf',
            ]);
            
        } catch (\Exception $e) {
            InvoiceLogger::logDownloadError($payment, $e);
            
            return redirect()->back()
                ->with('toast_error', 'Terjadi kesalahan saat mengunduh invoice.');
        }
    }
}
