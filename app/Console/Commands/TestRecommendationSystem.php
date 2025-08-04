<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\TravelPackage;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\DB;

class TestRecommendationSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recommendation:test {--user-id= : Test for specific user ID} {--package-id= : Test similar packages for specific package ID} {--limit=5 : Number of recommendations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the recommendation system with TF-IDF algorithm';

    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        parent::__construct();
        $this->recommendationService = $recommendationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Testing Recommendation System with TF-IDF');
        $this->newLine();

        $userId = $this->option('user-id');
        $packageId = $this->option('package-id');
        $limit = (int) $this->option('limit');

        if ($userId) {
            $this->testUserRecommendations($userId, $limit);
        } elseif ($packageId) {
            $this->testSimilarPackages($packageId, $limit);
        } else {
            $this->showSystemOverview();
            $this->testRandomUser($limit);
        }

        $this->newLine();
        $this->info('✅ Testing completed!');
    }

    /**
     * Test recommendations for a specific user
     */
    private function testUserRecommendations(int $userId, int $limit)
    {
        $user = User::with(['bookings.travelPackage', 'reviews.travelPackage'])->find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return;
        }

        $this->info("📊 Testing recommendations for user: {$user->name} (ID: {$userId})");
        $this->newLine();

        // Show user's history
        $this->showUserHistory($user);

        // Get recommendations
        $startTime = microtime(true);
        $recommendations = $this->recommendationService->getContentBasedRecommendations($user, $limit);
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        $this->info("🎯 Recommendations (Generated in {$executionTime}ms):");
        $this->displayRecommendations($recommendations);
    }

    /**
     * Test similar packages for a specific package
     */
    private function testSimilarPackages(int $packageId, int $limit)
    {
        $package = TravelPackage::with(['travelIncludes', 'travelExcludes', 'itineraries'])->find($packageId);
        
        if (!$package) {
            $this->error("Package with ID {$packageId} not found!");
            return;
        }

        $this->info("📦 Testing similar packages for: {$package->name} (ID: {$packageId})");
        $this->newLine();

        // Show package details
        $this->showPackageDetails($package);

        // Get similar packages
        $startTime = microtime(true);
        $similarPackages = $this->recommendationService->getSimilarPackages($package, $limit);
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        $this->info("🔍 Similar packages (Generated in {$executionTime}ms):");
        $this->displayRecommendations($similarPackages);
    }

    /**
     * Test with a random user who has booking/review history
     */
    private function testRandomUser(int $limit)
    {
        $user = User::whereHas('bookings')
            ->orWhereHas('reviews')
            ->with(['bookings.travelPackage', 'reviews.travelPackage'])
            ->inRandomOrder()
            ->first();

        if (!$user) {
            $this->warn('No users with booking/review history found. Testing with popular packages instead.');
            $this->testPopularPackages($limit);
            return;
        }

        $this->info("🎲 Testing with random user: {$user->name} (ID: {$user->id})");
        $this->testUserRecommendations($user->id, $limit);
    }

    /**
     * Test popular packages fallback
     */
    private function testPopularPackages(int $limit)
    {
        $packages = TravelPackage::withCount(['bookings', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->where('price', '>', 0)
            ->orderByDesc('bookings_count')
            ->orderByDesc('reviews_avg_rating')
            ->take($limit)
            ->get();

        $this->info('📈 Popular packages (fallback):');
        $this->displayRecommendations($packages);
    }

    /**
     * Show system overview
     */
    private function showSystemOverview()
    {
        $totalPackages = TravelPackage::count();
        $totalUsers = User::count();
        $totalBookings = DB::table('bookings')->count();
        $totalReviews = DB::table('reviews')->count();
        $usersWithHistory = User::whereHas('bookings')->orWhereHas('reviews')->count();

        $this->info('📈 System Overview:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Travel Packages', $totalPackages],
                ['Total Users', $totalUsers],
                ['Total Bookings', $totalBookings],
                ['Total Reviews', $totalReviews],
                ['Users with History', $usersWithHistory],
            ]
        );
        $this->newLine();
    }

    /**
     * Show user's booking and review history
     */
    private function showUserHistory(User $user)
    {
        $bookings = $user->bookings->where('travelPackage', '!=', null);
        $reviews = $user->reviews->where('travelPackage', '!=', null)->where('rating', '>=', 4);

        $this->info('📚 User History:');
        
        if ($bookings->count() > 0) {
            $this->info('Bookings:');
            foreach ($bookings->take(5) as $booking) {
                $this->line("  • {$booking->travelPackage->name} (Booked: {$booking->booking_date})");
            }
        }

        if ($reviews->count() > 0) {
            $this->info('Positive Reviews (4+ stars):');
            foreach ($reviews->take(5) as $review) {
                $this->line("  • {$review->travelPackage->name} ({$review->rating}⭐)");
            }
        }

        if ($bookings->count() === 0 && $reviews->count() === 0) {
            $this->warn('  No booking or positive review history found.');
        }

        $this->newLine();
    }

    /**
     * Show package details
     */
    private function showPackageDetails(TravelPackage $package)
    {
        $this->info('📋 Package Details:');
        $this->line("  Name: {$package->name}");
        $this->line("  Price: {$package->formatted_price}");
        $this->line("  Duration: {$package->duration}");
        $this->line("  Description: " . substr($package->description, 0, 100) . '...');
        
        if ($package->travelIncludes->count() > 0) {
            $includes = $package->travelIncludes->pluck('name')->take(3)->implode(', ');
            $this->line("  Includes: {$includes}" . ($package->travelIncludes->count() > 3 ? '...' : ''));
        }
        
        $this->newLine();
    }

    /**
     * Display recommendations in a formatted table
     */
    private function displayRecommendations($recommendations)
    {
        if ($recommendations->isEmpty()) {
            $this->warn('No recommendations found.');
            return;
        }

        $tableData = [];
        foreach ($recommendations as $index => $package) {
            $tableData[] = [
                $index + 1,
                $package->id,
                substr($package->name, 0, 30) . (strlen($package->name) > 30 ? '...' : ''),
                $package->formatted_price,
                $package->duration,
                round($package->average_rating, 1) . '⭐ (' . $package->review_count . ')',
            ];
        }

        $this->table(
            ['#', 'ID', 'Package Name', 'Price', 'Duration', 'Rating'],
            $tableData
        );
    }
}