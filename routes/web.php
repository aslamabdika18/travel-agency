<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;
use App\Filament\Pages\TfIdfDemo;

// Rute halaman utama
Route::get('/', [PageController::class, 'home'])->name('home');

// Rute halaman informasi
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

// Rute demo TF-IDF (tanpa autentikasi)
Route::get('/tf-idf-demo', [PageController::class, 'tfIdfDemo'])->name('tf-idf-demo');

// Rute paket perjalanan
Route::get('/travelpackages', [PageController::class, 'travelPackages'])->name('travel-packages');
Route::get('/travel-packages/{slug}', [PageController::class, 'travelPackageDetail'])->name('travel-package-detail');

// Rute autentikasi
Route::get('/auth', [PageController::class, 'login'])->name('auth');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/unauthorized', [PageController::class, 'unauthorized'])->name('unauthorized');

// Email Verification Routes
Route::get('/email/verify', [App\Http\Controllers\EmailVerificationController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\EmailVerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [App\Http\Controllers\EmailVerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// Rute booking (memerlukan autentikasi dan email verification)
Route::middleware(['auth', 'verified'])->group(function () {
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



// Demo TF-IDF Routes
Route::get('/demo/tf-idf', function () {
    try {
        // Load packages from database
        $packages = \App\Models\TravelPackage::with('category')->get();
        
        // If no packages found, provide sample data
        if ($packages->isEmpty()) {
            $packages = collect([
                [
                    'id' => 1,
                    'name' => 'Bali Adventure Tour',
                    'description' => 'Explore the beautiful beaches and temples of Bali with exciting water sports and cultural experiences.',
                    'category' => 'Adventure',
                    'price' => 2500000
                ],
                [
                    'id' => 2,
                    'name' => 'Jakarta City Tour',
                    'description' => 'Discover the vibrant city life of Jakarta with modern shopping centers and historical landmarks.',
                    'category' => 'City Tour',
                    'price' => 1500000
                ],
                [
                    'id' => 3,
                    'name' => 'Yogyakarta Cultural Heritage',
                    'description' => 'Experience the rich cultural heritage of Yogyakarta with traditional arts and ancient temples.',
                    'category' => 'Cultural',
                    'price' => 1800000
                ],
                [
                    'id' => 4,
                    'name' => 'Lombok Beach Paradise',
                    'description' => 'Relax on pristine beaches of Lombok with crystal clear waters and amazing sunset views.',
                    'category' => 'Beach',
                    'price' => 2200000
                ],
                [
                    'id' => 5,
                    'name' => 'Bandung Mountain Escape',
                    'description' => 'Enjoy cool mountain air in Bandung with tea plantations and volcanic landscapes.',
                    'category' => 'Mountain',
                    'price' => 1700000
                ]
            ]);
        } else {
            // Convert to array format for consistency
            $packages = $packages->map(function($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'description' => $package->description ?? 'No description available',
                    'category' => $package->category->name ?? 'Uncategorized',
                    'price' => $package->price
                ];
            });
        }
        
        return view('pages.tf-idf-demo', compact('packages'));
    } catch (\Exception $e) {
        // Fallback to sample data if database error
        $packages = collect([
            [
                'id' => 1,
                'name' => 'Bali Adventure Tour',
                'description' => 'Explore the beautiful beaches and temples of Bali with exciting water sports and cultural experiences.',
                'category' => 'Adventure',
                'price' => 2500000
            ],
            [
                'id' => 2,
                'name' => 'Jakarta City Tour',
                'description' => 'Discover the vibrant city life of Jakarta with modern shopping centers and historical landmarks.',
                'category' => 'City Tour',
                'price' => 1500000
            ]
        ]);
        
        return view('pages.tf-idf-demo', compact('packages'));
    }
})->name('demo.tf-idf');

Route::post('/demo/tf-idf', function (\Illuminate\Http\Request $request) {
    try {
        // Load packages from database
        $packages = \App\Models\TravelPackage::with('category')->get();
        
        // If no packages found, provide sample data
        if ($packages->isEmpty()) {
            $packages = collect([
                [
                    'id' => 1,
                    'name' => 'Bali Adventure Tour',
                    'description' => 'Explore the beautiful beaches and temples of Bali with exciting water sports and cultural experiences.',
                    'category' => 'Adventure',
                    'price' => 2500000
                ],
                [
                    'id' => 2,
                    'name' => 'Jakarta City Tour',
                    'description' => 'Discover the vibrant city life of Jakarta with modern shopping centers and historical landmarks.',
                    'category' => 'City Tour',
                    'price' => 1500000
                ],
                [
                    'id' => 3,
                    'name' => 'Yogyakarta Cultural Heritage',
                    'description' => 'Experience the rich cultural heritage of Yogyakarta with traditional arts and ancient temples.',
                    'category' => 'Cultural',
                    'price' => 1800000
                ],
                [
                    'id' => 4,
                    'name' => 'Lombok Beach Paradise',
                    'description' => 'Relax on pristine beaches of Lombok with crystal clear waters and amazing sunset views.',
                    'category' => 'Beach',
                    'price' => 2200000
                ],
                [
                    'id' => 5,
                    'name' => 'Bandung Mountain Escape',
                    'description' => 'Enjoy cool mountain air in Bandung with tea plantations and volcanic landscapes.',
                    'category' => 'Mountain',
                    'price' => 1700000
                ]
            ]);
        } else {
            // Convert to array format for consistency
            $packages = $packages->map(function($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'description' => $package->description ?? 'No description available',
                    'category' => $package->category->name ?? 'Uncategorized',
                    'price' => $package->price
                ];
            });
        }
        
        $selectedPackageId = $request->input('package_id');
        $selectedPackage = $packages->firstWhere('id', $selectedPackageId);
        
        if (!$selectedPackage) {
            return redirect()->back()->with('error', 'Paket tidak ditemukan');
        }
        
        // Calculate TF-IDF for table display
        $tfidfTableData = TfIdfHelper::calculateTfIdfForTable($packages->toArray(), $selectedPackage);
        
        return view('pages.tf-idf-demo', compact('packages', 'selectedPackage', 'tfidfTableData'));
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
})->name('demo.tf-idf.process');

// Helper class for TF-IDF calculation to avoid function redeclaration
if (!class_exists('TfIdfHelper')) {
    class TfIdfHelper {
        public static function calculateTfIdfForTable($packages, $selectedPackage) {
            $documents = [];
            $allTerms = [];
            
            // Prepare documents and extract terms
            foreach ($packages as $package) {
                $text = strtolower($package['name'] . ' ' . $package['description'] . ' ' . $package['category']);
                // Remove special characters and numbers, keep only letters and spaces
                $text = preg_replace('/[^a-z\s]/', '', $text);
                // Split into terms and filter out short terms
                $terms = array_filter(explode(' ', $text), function($term) {
                    return strlen(trim($term)) > 2;
                });
                $documents[] = $terms;
                $allTerms = array_merge($allTerms, $terms);
            }
            
            $allTerms = array_unique($allTerms);
            $totalDocuments = count($documents);
            
            // Find selected document index
            $selectedDocIndex = -1;
            foreach ($packages as $index => $package) {
                if ($package['id'] == $selectedPackage['id']) {
                    $selectedDocIndex = $index;
                    break;
                }
            }
            
            if ($selectedDocIndex === -1) {
                return [];
            }
            
            $selectedDoc = $documents[$selectedDocIndex];
            $termFreq = array_count_values($selectedDoc);
            $totalTermsInDoc = count($selectedDoc);
            
            $tfidfScores = [];
            
            foreach ($allTerms as $term) {
                // Calculate TF (Term Frequency)
                $tf = isset($termFreq[$term]) ? $termFreq[$term] / $totalTermsInDoc : 0;
                
                // Calculate IDF (Inverse Document Frequency)
                $docCount = 0;
                foreach ($documents as $doc) {
                    if (in_array($term, $doc)) {
                        $docCount++;
                    }
                }
                
                $idf = $docCount > 0 ? log($totalDocuments / $docCount) : 0;
                $tfidfScore = $tf * $idf;
                
                if ($tfidfScore > 0) {
                    $tfidfScores[] = [
                        'term' => $term,
                        'tf' => round($tf, 4),
                        'idf' => round($idf, 4),
                        'tfidf' => round($tfidfScore, 4)
                    ];
                }
            }
            
            // Sort by TF-IDF score descending and limit to top 15
            usort($tfidfScores, function($a, $b) {
                return $b['tfidf'] <=> $a['tfidf'];
            });
            
            return array_slice($tfidfScores, 0, 15);
        }
    }
}

// TF-IDF Demo API Routes
Route::middleware(['web'])->group(function () {
    Route::get('/admin/tf-idf-demo/packages', [TfIdfDemo::class, 'getPackages'])
        ->name('tf-idf-demo.packages');
});

// Rute contoh toast notification
require __DIR__.'/toast.php';
