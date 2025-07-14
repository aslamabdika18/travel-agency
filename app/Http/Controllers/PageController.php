<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function paymentSuccess(Request $request)
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
        
        // Prioritas 3: Ambil booking terakhir user yang sedang login
        if (!$booking && Auth::check()) {
            $booking = \App\Models\Booking::with([
                'travelPackage.media',
                'payment',
                'user'
            ])
            ->where('user_id', Auth::id())
            ->whereHas('payment', function($q) {
                $q->where('payment_status', 'Paid');
            })
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
            
            // Hapus session last_booking setelah digunakan
            if ($lastBookingId) {
                session()->forget('last_booking');
            }
        }
        
        return view('pages.payment-success', [
            'booking' => $booking,
            'payment' => $payment,
            'redirectUrl' => $redirectUrl
        ]);
    }

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
        }
        
        return view('pages.payment-callback', [
            'status' => $status,
            'booking' => $booking,
            'payment' => $payment
        ]);
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
}