<?php

namespace App\Services;

use App\Models\TravelPackage;
use App\Models\User;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    /**
     * Generate content-based recommendations using TF-IDF
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getContentBasedRecommendations(User $user, int $limit = 5): Collection
    {
        try {
            // Get user's interaction history
            $userProfile = $this->buildUserProfile($user);
            
            if (empty($userProfile)) {
                // If no history, return popular packages
                return $this->getPopularPackages($limit);
            }

            // Get all available packages
            $allPackages = TravelPackage::with(['travelIncludes', 'travelExcludes', 'itineraries'])
                ->where('price', '>', 0)
                ->where('is_active', true)
                ->get();

            // Calculate TF-IDF scores
            $recommendations = $this->calculateTfIdfScores($userProfile, $allPackages);

            // Sort by similarity score and return top recommendations
            return $recommendations->sortByDesc('similarity_score')
                ->take($limit)
                ->pluck('package');

        } catch (\Exception $e) {
            Log::error('Error generating content-based recommendations: ' . $e->getMessage());
            return $this->getPopularPackages($limit);
        }
    }

    /**
     * Build user profile based on interaction history
     *
     * @param User $user
     * @return array
     */
    private function buildUserProfile(User $user): array
    {
        $profile = [];

        // Get packages from bookings
        $bookedPackages = $user->bookings()
            ->with(['travelPackage.travelIncludes', 'travelPackage.travelExcludes', 'travelPackage.itineraries'])
            ->whereHas('travelPackage')
            ->get()
            ->pluck('travelPackage')
            ->filter();

        // Get packages from reviews
        $reviewedPackages = $user->reviews()
            ->with(['travelPackage.travelIncludes', 'travelPackage.travelExcludes', 'travelPackage.itineraries'])
            ->where('rating', '>=', 4) // Only consider positive reviews
            ->whereHas('travelPackage')
            ->get()
            ->pluck('travelPackage')
            ->filter();

        // Combine all packages user has interacted with
        $userPackages = $bookedPackages->merge($reviewedPackages)->unique('id');

        // Extract features from user's preferred packages
        foreach ($userPackages as $package) {
            $features = $this->extractPackageFeatures($package);
            foreach ($features as $feature) {
                $profile[$feature] = ($profile[$feature] ?? 0) + 1;
            }
        }

        return $profile;
    }

    /**
     * Extract features from a travel package for TF-IDF analysis
     *
     * @param TravelPackage $package
     * @return array
     */
    private function extractPackageFeatures(TravelPackage $package): array
    {
        $features = [];

        // Extract from description
        $description = strtolower($package->description ?? '');
        $descriptionWords = $this->extractKeywords($description);
        $features = array_merge($features, $descriptionWords);

        // Extract from includes
        if ($package->relationLoaded('travelIncludes')) {
            foreach ($package->travelIncludes as $include) {
                $includeWords = $this->extractKeywords(strtolower($include->name));
                $features = array_merge($features, $includeWords);
            }
        }

        // Extract from excludes
        if ($package->relationLoaded('travelExcludes')) {
            foreach ($package->travelExcludes as $exclude) {
                $excludeWords = $this->extractKeywords(strtolower($exclude->name));
                $features = array_merge($features, $excludeWords);
            }
        }

        // Extract from itineraries
        if ($package->relationLoaded('itineraries')) {
            foreach ($package->itineraries as $itinerary) {
                $activityWords = $this->extractKeywords(strtolower($itinerary->activity ?? ''));
                $noteWords = $this->extractKeywords(strtolower($itinerary->note ?? ''));
                $features = array_merge($features, $activityWords, $noteWords);
            }
        }

        // Add duration as feature
        if ($package->duration) {
            $features[] = 'duration_' . strtolower(str_replace(' ', '_', $package->duration));
        }

        // Add price range as feature
        $priceRange = $this->getPriceRange($package->price);
        $features[] = 'price_' . $priceRange;

        return array_unique($features);
    }

    /**
     * Extract keywords from text
     *
     * @param string $text
     * @return array
     */
    private function extractKeywords(string $text): array
    {
        // Remove special characters and split into words
        $text = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Filter out common stop words
        $stopWords = [
            'dan', 'atau', 'yang', 'di', 'ke', 'dari', 'untuk', 'dengan', 'pada', 'dalam',
            'adalah', 'akan', 'dapat', 'tidak', 'ada', 'ini', 'itu', 'juga', 'sudah',
            'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of',
            'with', 'by', 'is', 'are', 'was', 'were', 'be', 'been', 'have', 'has', 'had'
        ];

        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array(strtolower($word), $stopWords);
        });

        return array_values($keywords);
    }

    /**
     * Get price range category
     *
     * @param float $price
     * @return string
     */
    private function getPriceRange(float $price): string
    {
        if ($price < 500000) {
            return 'budget';
        } elseif ($price < 1500000) {
            return 'mid_range';
        } elseif ($price < 3000000) {
            return 'premium';
        } else {
            return 'luxury';
        }
    }

    /**
     * Calculate TF-IDF scores for recommendations
     *
     * @param array $userProfile
     * @param Collection $packages
     * @return Collection
     */
    private function calculateTfIdfScores(array $userProfile, Collection $packages): Collection
    {
        $recommendations = collect();
        $totalPackages = $packages->count();

        // Calculate document frequency for each term
        $documentFrequency = [];
        foreach ($packages as $package) {
            $features = $this->extractPackageFeatures($package);
            $uniqueFeatures = array_unique($features);
            foreach ($uniqueFeatures as $feature) {
                $documentFrequency[$feature] = ($documentFrequency[$feature] ?? 0) + 1;
            }
        }

        foreach ($packages as $package) {
            $packageFeatures = $this->extractPackageFeatures($package);
            $similarity = $this->calculateCosineSimilarity($userProfile, $packageFeatures, $documentFrequency, $totalPackages);

            $recommendations->push([
                'package' => $package,
                'similarity_score' => $similarity
            ]);
        }

        return $recommendations;
    }

    /**
     * Calculate cosine similarity between user profile and package features
     *
     * @param array $userProfile
     * @param array $packageFeatures
     * @param array $documentFrequency
     * @param int $totalDocuments
     * @return float
     */
    private function calculateCosineSimilarity(array $userProfile, array $packageFeatures, array $documentFrequency, int $totalDocuments): float
    {
        // Calculate TF-IDF vectors
        $userVector = $this->calculateTfIdfVector($userProfile, $documentFrequency, $totalDocuments);
        $packageVector = $this->calculateTfIdfVector(array_count_values($packageFeatures), $documentFrequency, $totalDocuments);

        // Calculate cosine similarity
        $dotProduct = 0;
        $userMagnitude = 0;
        $packageMagnitude = 0;

        $allTerms = array_unique(array_merge(array_keys($userVector), array_keys($packageVector)));

        foreach ($allTerms as $term) {
            $userTfIdf = $userVector[$term] ?? 0;
            $packageTfIdf = $packageVector[$term] ?? 0;

            $dotProduct += $userTfIdf * $packageTfIdf;
            $userMagnitude += $userTfIdf * $userTfIdf;
            $packageMagnitude += $packageTfIdf * $packageTfIdf;
        }

        $userMagnitude = sqrt($userMagnitude);
        $packageMagnitude = sqrt($packageMagnitude);

        if ($userMagnitude == 0 || $packageMagnitude == 0) {
            return 0;
        }

        return $dotProduct / ($userMagnitude * $packageMagnitude);
    }

    /**
     * Calculate TF-IDF vector for a document
     *
     * @param array $termFrequency
     * @param array $documentFrequency
     * @param int $totalDocuments
     * @return array
     */
    private function calculateTfIdfVector(array $termFrequency, array $documentFrequency, int $totalDocuments): array
    {
        $tfIdfVector = [];
        $totalTerms = array_sum($termFrequency);

        foreach ($termFrequency as $term => $frequency) {
            // Calculate TF (Term Frequency)
            $tf = $frequency / $totalTerms;

            // Calculate IDF (Inverse Document Frequency)
            $df = $documentFrequency[$term] ?? 1;
            $idf = log($totalDocuments / $df);

            // Calculate TF-IDF
            $tfIdfVector[$term] = $tf * $idf;
        }

        return $tfIdfVector;
    }

    /**
     * Get popular packages as fallback
     *
     * @param int $limit
     * @return Collection
     */
    private function getPopularPackages(int $limit): Collection
    {
        return TravelPackage::withCount(['bookings', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->where('price', '>', 0)
            ->where('is_active', true)
            ->orderByDesc('bookings_count')
            ->orderByDesc('reviews_avg_rating')
            ->take($limit)
            ->get();
    }

    /**
     * Get similar packages based on a specific package
     *
     * @param TravelPackage $package
     * @param int $limit
     * @return Collection
     */
    public function getSimilarPackages(TravelPackage $package, int $limit = 5): Collection
    {
        try {
            $targetFeatures = $this->extractPackageFeatures($package);
            $targetProfile = array_count_values($targetFeatures);

            $allPackages = TravelPackage::with(['travelIncludes', 'travelExcludes', 'itineraries'])
                ->where('id', '!=', $package->id)
                ->where('price', '>', 0)
                ->get();

            $recommendations = $this->calculateTfIdfScores($targetProfile, $allPackages);

            return $recommendations->sortByDesc('similarity_score')
                ->take($limit)
                ->pluck('package');

        } catch (\Exception $e) {
            Log::error('Error generating similar packages: ' . $e->getMessage());
            return collect();
        }
    }
}