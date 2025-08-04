<?php

namespace App\Http\Controllers;

use App\Models\TravelPackage;
use App\Services\RecommendationService;
use App\Services\ContentBasedRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
    protected $recommendationService;
    protected $contentBasedService;

    public function __construct(
        RecommendationService $recommendationService,
        ContentBasedRecommendationService $contentBasedService
    ) {
        $this->recommendationService = $recommendationService;
        $this->contentBasedService = $contentBasedService;
    }

    /**
     * Get personalized recommendations for authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPersonalizedRecommendations(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $limit = $request->get('limit', 5);
            $limit = min(max($limit, 1), 20); // Limit between 1-20

            $recommendations = $this->recommendationService
                ->getContentBasedRecommendations($user, $limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'recommendations' => $recommendations->map(function ($package) {
                        return [
                            'id' => $package->id,
                            'name' => $package->name,
                            'slug' => $package->slug,
                            'description' => $package->description,
                            'price' => $package->price,
                            'formatted_price' => $package->formatted_price,
                            'duration' => $package->duration,
                            'thumbnail_url' => $package->thumbnail_url,
                            'average_rating' => round($package->average_rating, 1),
                            'review_count' => $package->review_count,
                        ];
                    }),
                    'total' => $recommendations->count(),
                    'algorithm' => 'Content-Based Filtering with TF-IDF'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting personalized recommendations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recommendations'
            ], 500);
        }
    }

    /**
     * Get similar packages based on a specific package
     *
     * @param Request $request
     * @param int $packageId
     * @return JsonResponse
     */
    public function getSimilarPackages(Request $request, int $packageId): JsonResponse
    {
        try {
            $package = TravelPackage::with('category')->find($packageId);

            if (!$package) {
                return response()->json([
                    'success' => false,
                    'message' => 'Package not found'
                ], 404);
            }

            $limit = $request->get('limit', 5);
            $limit = min(max($limit, 1), 20); // Limit between 1-20
            $includeExplanation = $request->get('include_explanation', false);

            // Gunakan ContentBasedRecommendationService untuk mendapatkan rekomendasi
            $recommendations = $this->contentBasedService->getRecommendations($packageId, $limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'base_package' => [
                        'id' => $package->id,
                        'name' => $package->name,
                        'slug' => $package->slug,
                        'category' => $package->category ? $package->category->name : null
                    ],
                    'similar_packages' => $recommendations->map(function ($item) use ($includeExplanation) {
                        $packageData = [
                            'id' => $item['package']->id,
                            'name' => $item['package']->name,
                            'slug' => $item['package']->slug,
                            'description' => $item['package']->description,
                            'price' => $item['package']->price,
                            'formatted_price' => $item['package']->formatted_price,
                            'duration' => $item['package']->duration,
                            'thumbnail_url' => $item['package']->thumbnail_url,
                            'category' => $item['package']->category ? $item['package']->category->name : null,
                            'similarity_score' => round($item['similarity_score'] * 100, 1)
                        ];
                        
                        if ($includeExplanation) {
                            $packageData['explanation'] = $item['explanation'];
                        }
                        
                        return $packageData;
                    }),
                    'total' => $recommendations->count(),
                    'algorithm' => 'Content-Based Filtering with TF-IDF'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting similar packages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get similar packages'
            ], 500);
        }
    }

    /**
     * Get recommendations for homepage (public)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getHomepageRecommendations(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 6);
            $limit = min(max($limit, 1), 20);

            // For non-authenticated users, show popular packages
            $packages = TravelPackage::withCount(['bookings', 'reviews'])
                ->withAvg('reviews', 'rating')
                ->where('price', '>', 0)
                ->orderByDesc('bookings_count')
                ->orderByDesc('reviews_avg_rating')
                ->take($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'recommendations' => $packages->map(function ($package) {
                        return [
                            'id' => $package->id,
                            'name' => $package->name,
                            'slug' => $package->slug,
                            'description' => $package->description,
                            'price' => $package->price,
                            'formatted_price' => $package->formatted_price,
                            'duration' => $package->duration,
                            'thumbnail_url' => $package->thumbnail_url,
                            'average_rating' => round($package->reviews_avg_rating ?? 0, 1),
                            'review_count' => $package->reviews_count ?? 0,
                            'booking_count' => $package->bookings_count ?? 0,
                        ];
                    }),
                    'total' => $packages->count(),
                    'algorithm' => 'Popularity-Based'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting homepage recommendations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recommendations'
            ], 500);
        }
    }

    /**
     * Get trending packages with optional category filter
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTrendingPackages(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 6);
            $limit = min(max($limit, 1), 20);
            $filter = $request->get('filter', 'all'); // all, adventure, beach, culture, family

            $query = TravelPackage::withCount(['bookings', 'reviews'])
                ->withAvg('reviews', 'rating')
                ->with('category')
                ->where('price', '>', 0);

            // Apply category filter based on description and name content
            if ($filter !== 'all') {
                switch ($filter) {
                    case 'adventure':
                        $query->where(function ($q) {
                            $q->whereRaw('LOWER(name) LIKE ?', ['%adventure%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%adventure%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%hiking%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%climbing%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%trekking%']);
                        });
                        break;
                    case 'beach':
                        $query->where(function ($q) {
                            $q->whereRaw('LOWER(name) LIKE ?', ['%beach%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%beach%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%island%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%diving%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%snorkeling%']);
                        });
                        break;
                    case 'culture':
                        $query->where(function ($q) {
                            $q->whereRaw('LOWER(name) LIKE ?', ['%culture%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%culture%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%traditional%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%heritage%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%temple%']);
                        });
                        break;
                    case 'family':
                        $query->where(function ($q) {
                            $q->whereRaw('LOWER(name) LIKE ?', ['%family%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%family%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%kids%'])
                              ->orWhereRaw('LOWER(description) LIKE ?', ['%children%'])
                              ->orWhere('duration', '<=', 3); // Short trips are family-friendly
                        });
                        break;
                }
            }

            // Order by trending criteria: recent bookings + high ratings
            $packages = $query->orderByDesc('bookings_count')
                ->orderByDesc('reviews_avg_rating')
                ->orderByDesc('created_at') // Recent packages get priority
                ->take($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'trending_packages' => $packages->map(function ($package) {
                        return [
                            'id' => $package->id,
                            'name' => $package->name,
                            'slug' => $package->slug,
                            'description' => $package->description,
                            'price' => $package->price,
                            'formatted_price' => $package->formatted_price,
                            'duration' => $package->duration,
                            'thumbnail_url' => $package->thumbnail_url,
                            'average_rating' => round($package->reviews_avg_rating ?? 0, 1),
                            'review_count' => $package->reviews_count ?? 0,
                            'booking_count' => $package->bookings_count ?? 0,
                            'category' => $package->category ? $package->category->name : null,
                        ];
                    }),
                    'total' => $packages->count(),
                    'filter' => $filter,
                    'algorithm' => 'Trending-Based with Category Filter'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting trending packages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get trending packages'
            ], 500);
        }
    }

    /**
     * Get recommendations by category
     *
     * @param Request $request
     * @param int $categoryId
     * @return JsonResponse
     */
    public function getRecommendationsByCategory(Request $request, int $categoryId): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $limit = min(max($limit, 1), 20);

            $recommendations = $this->contentBasedService->getRecommendationsByCategory($categoryId, $limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'recommendations' => $recommendations->map(function ($package) {
                        return [
                            'id' => $package->id,
                            'name' => $package->name,
                            'slug' => $package->slug,
                            'description' => $package->description,
                            'price' => $package->price,
                            'formatted_price' => $package->formatted_price,
                            'duration' => $package->duration,
                            'thumbnail_url' => $package->thumbnail_url,
                            'category' => $package->category ? $package->category->name : null,
                        ];
                    }),
                    'total' => $recommendations->count(),
                    'category_id' => $categoryId,
                    'algorithm' => 'Category-Based Filtering'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recommendations by category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recommendations'
            ], 500);
        }
    }

    /**
     * Get recommendations by price range
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getRecommendationsByPriceRange(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'min_price' => 'required|numeric|min:0',
                'max_price' => 'required|numeric|gt:min_price'
            ]);

            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');
            $limit = $request->get('limit', 10);
            $limit = min(max($limit, 1), 20);

            $recommendations = $this->contentBasedService->getRecommendationsByPriceRange($minPrice, $maxPrice, $limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'recommendations' => $recommendations->map(function ($package) {
                        return [
                            'id' => $package->id,
                            'name' => $package->name,
                            'slug' => $package->slug,
                            'description' => $package->description,
                            'price' => $package->price,
                            'formatted_price' => $package->formatted_price,
                            'duration' => $package->duration,
                            'thumbnail_url' => $package->thumbnail_url,
                            'category' => $package->category ? $package->category->name : null,
                        ];
                    }),
                    'total' => $recommendations->count(),
                    'price_range' => [
                        'min' => $minPrice,
                        'max' => $maxPrice
                    ],
                    'algorithm' => 'Price Range Filtering'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recommendations by price range: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recommendations'
            ], 500);
        }
    }

    /**
     * Get recommendation explanation for debugging
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getRecommendationExplanation(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get user's booking and review history
            $bookings = $user->bookings()
                ->with('travelPackage')
                ->whereHas('travelPackage')
                ->get();

            $reviews = $user->reviews()
                ->with('travelPackage')
                ->where('rating', '>=', 4)
                ->whereHas('travelPackage')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'user_profile' => [
                        'total_bookings' => $bookings->count(),
                        'total_positive_reviews' => $reviews->count(),
                        'booked_packages' => $bookings->map(function ($booking) {
                            return [
                                'id' => $booking->travelPackage->id,
                                'name' => $booking->travelPackage->name,
                                'booking_date' => $booking->booking_date,
                            ];
                        }),
                        'reviewed_packages' => $reviews->map(function ($review) {
                            return [
                                'id' => $review->travelPackage->id,
                                'name' => $review->travelPackage->name,
                                'rating' => $review->rating,
                                'review_date' => $review->created_at,
                            ];
                        }),
                    ],
                    'algorithm_info' => [
                        'name' => 'Content-Based Filtering with TF-IDF',
                        'description' => 'Menganalisis konten paket travel (deskripsi, fasilitas, itinerary) untuk menemukan kesamaan dengan preferensi user berdasarkan riwayat booking dan review positif.',
                        'features_analyzed' => [
                            'Deskripsi paket',
                            'Fasilitas yang disertakan',
                            'Fasilitas yang tidak disertakan',
                            'Aktivitas dalam itinerary',
                            'Durasi perjalanan',
                            'Kategori harga'
                        ],
                        'tf_idf_info' => [
                            'description' => 'TF-IDF (Term Frequency-Inverse Document Frequency) menganalisis frekuensi kata dalam deskripsi paket dan memberikan bobot lebih tinggi pada kata-kata yang unik dan relevan.',
                            'similarity_calculation' => [
                                'Category similarity' => '30%',
                                'Price range similarity' => '20%',
                                'Duration similarity' => '15%',
                                'Text content similarity (TF-IDF)' => '35%'
                            ]
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recommendation explanation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get explanation'
            ], 500);
        }
    }

    /**
     * Get packages for TF-IDF demo
     *
     * @return JsonResponse
     */
    public function getDemoPackages(): JsonResponse
    {
        try {
            $packages = TravelPackage::select('id', 'name', 'category_id', 'price', 'duration')
                ->with('category:id,name')
                ->get()
                ->map(function ($package) {
                    return [
                        'id' => $package->id,
                        'name' => $package->name,
                        'category' => $package->category->name ?? 'Unknown',
                        'price' => $package->price,
                        'duration' => $package->duration,
                    ];
                });

            return response()->json([
                'success' => true,
                'packages' => $packages
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting demo packages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get packages'
            ], 500);
        }
    }

    /**
     * Analyze TF-IDF for a specific package
     *
     * @param int $id
     * @return JsonResponse
     */
    public function analyzeTfIdf(int $id): JsonResponse
    {
        try {
            $selectedPackage = TravelPackage::with(['category', 'travelIncludes', 'travelExcludes', 'itineraries'])
                ->find($id);

            if (!$selectedPackage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Package not found'
                ], 404);
            }

            // Get recommendations using the content-based service
            $recommendations = $this->contentBasedService->getRecommendations($id, 5);

            // Get all packages for analysis with complete relations
        $allPackages = TravelPackage::with(['category', 'travelIncludes', 'travelExcludes', 'itineraries'])->get();

            // Calculate detailed TF-IDF analysis (simplified for demo)
            $tfidfAnalysis = $this->calculateDemoTfIdfAnalysis($selectedPackage, $allPackages);

            // Create similarity matrix (sample data)
            $similarityMatrix = $this->createDemoSimilarityMatrix($selectedPackage, $recommendations);

            // Enhance recommendations with detailed score breakdown
            $enhancedRecommendations = [];
            foreach ($recommendations as $rec) {
                $similarity = $rec['similarity_score'] ?? 0; // Fix: use correct key
                $similarity = is_numeric($similarity) ? (float)$similarity : 0; // Ensure numeric
                
                $enhancedRecommendations[] = [
                    'package' => [
                        'id' => $rec['package']->id,
                        'name' => $rec['package']->name,
                        'category' => $rec['package']->category->name ?? 'Unknown',
                        'price' => $rec['package']->price ?? 0,
                        'duration' => $rec['package']->duration ?? 0,
                    ],
                    'similarity' => $similarity,
                    'text_similarity' => $similarity * 0.7, // Simulate text component
                    'category_similarity' => $selectedPackage->category_id === $rec['package']->category_id ? 1.0 : 0.3,
                    'price_similarity' => $this->calculatePriceSimilarity($selectedPackage->price ?? 0, $rec['package']->price ?? 0),
                    'duration_similarity' => $this->calculateDurationSimilarity($selectedPackage->duration ?? 0, $rec['package']->duration ?? 0),
                ];
            }

            return response()->json([
                'success' => true,
                'selected_package' => [
                    'id' => $selectedPackage->id,
                    'name' => $selectedPackage->name,
                    'category' => $selectedPackage->category->name ?? 'Unknown',
                    'price' => $selectedPackage->price ?? 0,
                    'duration' => $selectedPackage->duration ?? 0,
                ],
                'recommendations' => $enhancedRecommendations,
                'tfidf_analysis' => $tfidfAnalysis,
                'similarity_matrix' => $similarityMatrix
            ]);

        } catch (\Exception $e) {
            Log::error('Error analyzing TF-IDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze TF-IDF'
            ], 500);
        }
    }

    /**
     * Calculate detailed TF-IDF analysis using real data
     */
    private function calculateDemoTfIdfAnalysis($selectedPackage, $allPackages): array
    {
        // Get first 3 packages for the demo table (including selected package)
        $demoPackages = collect([$selectedPackage])
            ->concat($allPackages->where('id', '!=', $selectedPackage->id)->take(2))
            ->take(3);

        // Ensure all packages have the required relations loaded
        $demoPackages = $demoPackages->map(function($package) {
            if (!$package->relationLoaded('itineraries')) {
                $package->load('itineraries');
            }
            return $package;
        });

        // Extract text features for each package
        $packageTexts = [];
        $packageNames = [];
        foreach ($demoPackages as $package) {
            $packageTexts[] = $this->extractTextFeaturesForAnalysis($package);
            $packageNames[] = $package->name;
        }

        // Get all unique terms from all packages
        $allTerms = [];
        $packageTokens = [];
        foreach ($packageTexts as $index => $text) {
            $tokens = $this->tokenizeTextForAnalysis($text);
            $packageTokens[$index] = $tokens;
            $allTerms = array_merge($allTerms, $tokens);
        }
        
        // Get unique meaningful terms
        $uniqueTerms = array_unique($allTerms);
        $meaningfulTerms = array_filter($uniqueTerms, function($term) {
            return strlen($term) > 2;
        });
        
        // Limit to top terms by frequency across all documents
        $termFreqGlobal = array_count_values($allTerms);
        arsort($termFreqGlobal);
        $topTerms = array_slice(array_keys($termFreqGlobal), 0, 5);
        
        // Calculate TF-IDF table data
        $tfidfTableData = [];
        $totalDocs = count($packageTexts);
        
        foreach ($topTerms as $term) {
            if (strlen($term) > 2) {
                $row = ['term' => ucfirst($term)];
                
                // Calculate TF for each package (raw frequency)
                $tfValues = [];
                foreach ($packageTokens as $index => $tokens) {
                    $termCount = array_count_values($tokens)[$term] ?? 0;
                    $tfValues[] = $termCount;
                    $row['tf_package_' . chr(65 + $index)] = $termCount; // Raw frequency
                }
                
                // Calculate DF (Document Frequency) - jumlah dokumen yang mengandung term
                $df = 0;
                foreach ($packageTokens as $tokens) {
                    $termCounts = array_count_values($tokens);
                    if (isset($termCounts[$term]) && $termCounts[$term] > 0) {
                        $df++;
                    }
                }
                $df = max(1, $df); // Hindari pembagian dengan 0
                
                // Calculate IDF menggunakan formula: log(N/df)
                $idf = $df < $totalDocs ? log($totalDocs / $df, 10) : 0;
                $row['idf'] = round($idf, 1);
                
                // Calculate TF-IDF for each package
                foreach ($tfValues as $index => $tf) {
                    $tfidf = $tf * $idf;
                    $row['tfidf_package_' . chr(65 + $index)] = round($tfidf, 1);
                }
                
                $tfidfTableData[] = $row;
            }
        }

        // Get selected package text for summary
        $selectedText = $this->extractTextFeaturesForAnalysis($selectedPackage);
        $selectedTokens = $this->tokenizeTextForAnalysis($selectedText);
        
        return [
            'statistics' => [
                'total_tokens' => count($selectedTokens),
                'unique_tokens' => count(array_unique($selectedTokens)),
                'total_documents' => count($packageTexts),
                'top_terms' => array_slice($topTerms, 0, 10)
            ],
            'packageNames' => $packageNames,
            'tfidfTableData' => $tfidfTableData
        ];
    }

    /**
     * Create similarity matrix using real TF-IDF calculations
     */
    private function createDemoSimilarityMatrix($selectedPackage, $recommendations): array
    {
        $matrix = [];
        $packages = collect([$selectedPackage])->concat(collect($recommendations)->pluck('package'));
        
        // Get all packages for document corpus
        $allPackages = TravelPackage::with(['category', 'travelIncludes', 'travelExcludes'])->get();
        
        // Extract text features for all packages in the matrix
        $packageTexts = [];
        foreach ($packages as $package) {
            $packageTexts[] = $this->extractTextFeaturesForAnalysis($package);
        }
        
        // Calculate TF-IDF vectors for each package
        $tfidfVectors = [];
        foreach ($packageTexts as $text) {
            $tokens = $this->tokenizeTextForAnalysis($text);
            $tfidfVectors[] = $this->calculateTfIdfVector($tokens, $packageTexts);
        }
        
        // Create similarity matrix
        foreach ($packages as $i => $pkg1) {
            $row = [];
            foreach ($packages as $j => $pkg2) {
                if ($i === $j) {
                    $row[] = 1.0;
                } else {
                    // Calculate text similarity using cosine similarity
                    $textSim = $this->calculateCosineSimilarity($tfidfVectors[$i], $tfidfVectors[$j]);
                    
                    // Calculate other similarity components
                    $categorySim = $pkg1->category_id === $pkg2->category_id ? 0.8 : 0.3;
                    $priceSim = $this->calculatePriceSimilarity($pkg1->price ?? 0, $pkg2->price ?? 0);
                    $durationSim = $this->calculateDurationSimilarity($pkg1->duration ?? 0, $pkg2->duration ?? 0);
                    
                    // Ensure all values are numeric
                    $textSim = is_numeric($textSim) ? (float)$textSim : 0;
                    $categorySim = is_numeric($categorySim) ? (float)$categorySim : 0.3;
                    $priceSim = is_numeric($priceSim) ? (float)$priceSim : 0;
                    $durationSim = is_numeric($durationSim) ? (float)$durationSim : 0;
                    
                    // Weighted combination (same as ContentBasedRecommendationService)
                    $combinedSim = ($textSim * 0.5) + ($categorySim * 0.25) + ($priceSim * 0.15) + ($durationSim * 0.1);
                    
                    $row[] = round($combinedSim, 3);
                }
            }
            $matrix[] = $row;
        }
        
        return [
            'labels' => $packages->pluck('name')->toArray(),
            'matrix' => $matrix
        ];
    }

    /**
     * Calculate price similarity
     */
    private function calculatePriceSimilarity($price1, $price2): float
    {
        // Ensure values are numeric
        $price1 = is_numeric($price1) ? (float)$price1 : 0;
        $price2 = is_numeric($price2) ? (float)$price2 : 0;
        
        $diff = abs($price1 - $price2);
        $maxPrice = max($price1, $price2);
        return $maxPrice > 0 ? max(0, 1 - ($diff / $maxPrice)) : 1;
    }

    /**
     * Calculate duration similarity
     */
    private function calculateDurationSimilarity($duration1, $duration2): float
    {
        // Ensure values are numeric
        $duration1 = is_numeric($duration1) ? (float)$duration1 : 0;
        $duration2 = is_numeric($duration2) ? (float)$duration2 : 0;
        
        $diff = abs($duration1 - $duration2);
        return max(0, 1 - ($diff / 10)); // Normalize by 10 days
    }

    /**
     * Extract text features for analysis
     * Mengekstrak fitur teks dari berbagai komponen paket wisata dengan bobot berbeda
     */
    private function extractTextFeaturesForAnalysis($package): string
    {
        // Handle string input (for sample demo)
        if (is_string($package)) {
            return $package;
        }

        $features = [];

        // Package name (weight: 3) - Nama paket paling penting
        if (isset($package->name) && $package->name) {
            $features[] = str_repeat($package->name, 3);
        }

        // Category (weight: 2) - Kategori cukup penting untuk klasifikasi
        if (isset($package->category) && $package->category && isset($package->category->name) && $package->category->name) {
            $features[] = str_repeat($package->category->name, 2);
        }

        // Description (weight: 1) - Deskripsi detail paket
        if (isset($package->description) && $package->description) {
            $features[] = $package->description;
        }

        // Travel Includes (weight: 1) - Fasilitas yang disertakan
        if (isset($package->travelIncludes) && $package->travelIncludes->isNotEmpty()) {
            $inclusions = $package->travelIncludes->pluck('name')->implode(' ');
            $features[] = $inclusions;
        }
        
        // Travel Excludes (weight: 1) - Fasilitas yang tidak disertakan
        if (isset($package->travelExcludes) && $package->travelExcludes->isNotEmpty()) {
            $exclusions = $package->travelExcludes->pluck('name')->implode(' ');
            $features[] = $exclusions;
        }

        // Itineraries (weight: 1) - Aktivitas dalam perjalanan
        if (isset($package->itineraries) && $package->itineraries->isNotEmpty()) {
            $itineraryTexts = [];
            foreach ($package->itineraries as $itinerary) {
                if ($itinerary->activity) {
                    $itineraryTexts[] = $itinerary->activity;
                }
                if ($itinerary->note) {
                    $itineraryTexts[] = $itinerary->note;
                }
            }
            if (!empty($itineraryTexts)) {
                $features[] = implode(' ', $itineraryTexts);
            }
        }

        return implode(' ', $features);
    }

    /**
     * Tokenize text for analysis
     */
    private function tokenizeTextForAnalysis(string $text): array
    {
        // Convert to lowercase and remove special characters
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        
        // Split into words
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        // Remove common stop words (Indonesian and English)
        $stopWords = [
            'dan', 'atau', 'yang', 'di', 'ke', 'dari', 'untuk', 'dengan', 'pada', 'dalam', 'adalah', 'akan', 'dapat', 'ini', 'itu', 'ada', 'juga', 'tidak', 'ya', 'sudah', 'bisa', 'hanya', 'lebih', 'sangat', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'is', 'are', 'was', 'were', 'be', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'must', 'can', 'this', 'that', 'these', 'those', 'a', 'an'
        ];
        
        // Filter out stop words and short words
        $filteredWords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });
        
        return array_values($filteredWords);
    }

    /**
     * Calculate TF-IDF vector
     */
    private function calculateTfIdfVector(array $tokens, array $documents): array
    {
        $termFreq = array_count_values($tokens);
        $totalTerms = count($tokens);
        $totalDocs = count($documents);

        $tfidfVector = [];

        foreach ($termFreq as $term => $freq) {
            // Calculate TF (Term Frequency)
            $tf = $freq / $totalTerms;

            // Calculate DF (Document Frequency)
            $df = 0;
            foreach ($documents as $doc) {
                if (stripos($doc, $term) !== false) {
                    $df++;
                }
            }

            // Calculate IDF (Inverse Document Frequency)
            $idf = $df > 0 ? log($totalDocs / $df) : 0;

            // Calculate TF-IDF
            $tfidfVector[$term] = $tf * $idf;
        }

        return $tfidfVector;
    }

    /**
     * Calculate cosine similarity between two TF-IDF vectors
     */
    private function calculateCosineSimilarity(array $vector1, array $vector2): float
    {
        // Get all unique terms from both vectors
        $allTerms = array_unique(array_merge(array_keys($vector1), array_keys($vector2)));
        
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;
        
        foreach ($allTerms as $term) {
            $val1 = $vector1[$term] ?? 0;
            $val2 = $vector2[$term] ?? 0;
            
            $dotProduct += $val1 * $val2;
            $magnitude1 += $val1 * $val1;
            $magnitude2 += $val2 * $val2;
        }
        
        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);
        
        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        }
        
        return $dotProduct / ($magnitude1 * $magnitude2);
    }
}