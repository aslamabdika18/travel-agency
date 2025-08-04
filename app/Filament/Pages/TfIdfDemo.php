<?php

namespace App\Filament\Pages;

use App\Models\TravelPackage;
use App\Services\ContentBasedRecommendationService;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class TfIdfDemo extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static string $view = 'filament.pages.tf-idf-demo';
    protected static ?string $title = 'TF-IDF Demo';
    protected static ?string $navigationLabel = 'TF-IDF Demo';
    protected static ?string $navigationGroup = 'Analytics';
    protected static ?int $navigationSort = 10;
    protected static ?string $slug = 'tf-idf-demo';

    public static function canAccess(): bool
    {
        return true; // Allow all users to access this demo page
    }

    public function getPackages(): JsonResponse
     {
         try {
             $packages = TravelPackage::with('category')
                 ->select('id', 'name', 'description', 'category_id', 'price', 'duration')
                 ->get()
                 ->map(function ($package) {
                     return [
                         'id' => $package->id,
                         'title' => $package->name, // Map name to title for frontend compatibility
                         'name' => $package->name,
                         'description' => $package->description,
                         'location' => $package->category ? $package->category->name : 'Indonesia', // Use category as location fallback
                         'duration' => $package->duration,
                         'price' => $package->price,
                         'category_id' => $package->category_id,
                         'category' => $package->category ? [
                             'id' => $package->category->id,
                             'name' => $package->category->name
                         ] : null
                     ];
                 });

             return response()->json([
                 'success' => true,
                 'packages' => $packages
             ]);
         } catch (\Exception $e) {
             Log::error('Error fetching packages for TF-IDF demo: ' . $e->getMessage());

             return response()->json([
                 'success' => false,
                 'message' => 'Gagal memuat data paket travel'
             ], 500);
         }
     }

    public ?array $data = [];
    public ?int $selectedPackageId = null;
    public ?array $demoResults = null;
    public ?array $packages = null;
    public bool $showSampleDemo = true;

    public function mount(): void
    {
        $this->packages = TravelPackage::select('id', 'name', 'category_id', 'price', 'duration')
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
            })
            ->toArray();

        // Jalankan demo sample otomatis
        $this->runSampleDemo();
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Demo TF-IDF Content-Based Filtering')
                    ->description('Demonstrasi algoritma TF-IDF untuk sistem rekomendasi paket perjalanan')
                    ->schema([
                        Select::make('selectedPackageId')
                            ->label('Pilih Paket Perjalanan (Opsional)')
                            ->options(
                                TravelPackage::with('category')
                                    ->get()
                                    ->mapWithKeys(function ($package) {
                                        return [
                                            $package->id => $package->name . ' (' . ($package->category->name ?? 'Unknown') . ')'
                                        ];
                                    })
                            )
                            ->searchable()
                            ->placeholder('Pilih paket untuk demo spesifik atau lihat demo sample di bawah...')
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->selectedPackageId = $state;
                                if ($state) {
                                    $this->showSampleDemo = false;
                                    $this->runDemo();
                                } else {
                                    $this->showSampleDemo = true;
                                    $this->runSampleDemo();
                                }
                            })
                    ])
            ])
            ->statePath('data');
    }

    public function runDemo(): void
    {
        if (!$this->selectedPackageId) {
            $this->demoResults = null;
            return;
        }

        try {
            $service = new ContentBasedRecommendationService();
            $selectedPackage = TravelPackage::with(['category', 'travelIncludes', 'travelExcludes'])->find($this->selectedPackageId);

            if (!$selectedPackage) {
                Notification::make()
                    ->title('Error')
                    ->body('Paket tidak ditemukan')
                    ->danger()
                    ->send();
                return;
            }

            // Get recommendations using the actual service
            $recommendations = $service->getRecommendations($this->selectedPackageId, 5);

            // Get all packages for TF-IDF calculation
            $allPackages = TravelPackage::with(['category', 'travelIncludes', 'travelExcludes'])->get();

            // Calculate detailed TF-IDF analysis
            $tfidfAnalysis = $this->calculateDetailedTfIdfAnalysis($selectedPackage, $allPackages);

            // Enhance recommendations with detailed score breakdown
            $enhancedRecommendations = [];
            foreach ($recommendations as $rec) {
                $recPackage = $allPackages->firstWhere('id', $rec['package']->id);
                if ($recPackage) {
                    // Calculate individual similarity components
                    $textSimilarity = $this->calculateTextSimilarity($selectedPackage, $recPackage, $allPackages);
                    $categorySimilarity = $this->calculateCategorySimilarity($selectedPackage, $recPackage);
                    $priceSimilarity = $this->calculatePriceSimilarity($selectedPackage, $recPackage);
                    $durationSimilarity = $this->calculateDurationSimilarity($selectedPackage, $recPackage);
                    
                    $enhancedRecommendations[] = [
                        'id' => $rec['package']->id,
                        'name' => $rec['package']->name,
                        'category' => $rec['package']->category->name ?? 'Unknown',
                        'price' => $rec['package']->price,
                        'duration' => $rec['package']->duration,
                        'similarity_score' => round($rec['similarity_score'], 4),
                        'score_breakdown' => [
                            'text_similarity' => $textSimilarity,
                            'category_similarity' => $categorySimilarity,
                            'price_similarity' => $priceSimilarity,
                            'duration_similarity' => $durationSimilarity
                        ],
                        'explanation' => $this->generateExplanation($textSimilarity, $categorySimilarity, $priceSimilarity, $durationSimilarity)
                    ];
                }
            }

            // Prepare weighting table data (showing the actual weights used in CBF)
            $weightingData = [
                [
                    'component' => 'Text Content (TF-IDF)',
                    'weight_percentage' => 50,
                    'description' => 'Kemiripan konten teks menggunakan TF-IDF dari nama, deskripsi, inclusions, exclusions'
                ],
                [
                    'component' => 'Category Similarity',
                    'weight_percentage' => 25,
                    'description' => 'Kemiripan kategori paket travel (exact match atau berbeda)'
                ],
                [
                    'component' => 'Price Range Similarity',
                    'weight_percentage' => 15,
                    'description' => 'Kemiripan rentang harga berdasarkan selisih harga absolut'
                ],
                [
                    'component' => 'Duration Similarity',
                    'weight_percentage' => 10,
                    'description' => 'Kemiripan durasi perjalanan dalam hari'
                ]
            ];

            $this->demoResults = [
                'selectedPackage' => [
                    'id' => $selectedPackage->id,
                    'name' => $selectedPackage->name,
                    'category' => $selectedPackage->category->name ?? 'Unknown',
                    'price' => $selectedPackage->price,
                    'duration' => $selectedPackage->duration,
                    'description' => $selectedPackage->description,
                ],
                'weightingData' => $weightingData,
                'tfidfAnalysis' => $tfidfAnalysis,
                'recommendations' => $enhancedRecommendations
            ];

            Notification::make()
                ->title('Demo Berhasil')
                ->body('Analisis CBF TF-IDF telah selesai dihitung')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function runSampleDemo(): void
    {
        // Data sample untuk demo TF-IDF
        $sampleDocuments = [
            'Paket Wisata Bali Beach Resort - Nikmati keindahan pantai Bali dengan resort mewah tepi laut. Paket termasuk akomodasi hotel bintang 5, aktivitas snorkeling, diving, dan sunset dinner.',
            'Tour Gunung Bromo Sunrise - Petualangan mendaki gunung berapi aktif untuk menyaksikan sunrise spektakuler. Termasuk jeep tour, guide profesional, dan camping equipment.',
            'Wisata Budaya Yogyakarta - Jelajahi warisan budaya Jawa dengan mengunjungi Candi Borobudur, Prambanan, dan Keraton. Paket budaya lengkap dengan guide sejarah.',
            'Paket Diving Raja Ampat - Eksplorasi bawah laut terbaik dunia dengan biodiversitas laut yang menakjubkan. Termasuk boat diving, equipment lengkap, dan underwater photography.',
            'City Tour Jakarta Modern - Tur kota metropolitan dengan mengunjungi landmark modern, shopping mall, kuliner street food, dan nightlife Jakarta.'
        ];

        $selectedText = $sampleDocuments[0]; // Gunakan dokumen pertama sebagai query

        // Tokenisasi
        $tokens = $this->tokenizeText($selectedText);

        // Hitung TF-IDF
        $tfidfVector = $this->calculateTfIdfVector($tokens, $sampleDocuments);

        // Hitung similarity dengan dokumen lain
        $similarities = [];
        for ($i = 1; $i < count($sampleDocuments); $i++) {
            $docTokens = $this->tokenizeText($sampleDocuments[$i]);
            $docTfidf = $this->calculateTfIdfVector($docTokens, $sampleDocuments);

            $similarity = $this->calculateCosineSimilarity($tfidfVector, $docTfidf);
            $similarities[] = [
                'document' => 'Dokumen ' . ($i + 1),
                'title' => substr($sampleDocuments[$i], 0, 50) . '...',
                'similarity_score' => round($similarity, 4)
            ];
        }

        // Sort by similarity
        usort($similarities, function($a, $b) {
            return $b['similarity_score'] <=> $a['similarity_score'];
        });

        // Get top terms
        $topTerms = collect($tfidfVector)
            ->sortDesc()
            ->take(10)
            ->map(function ($score, $term) {
                return [
                    'term' => $term,
                    'tfidf_score' => round($score, 4)
                ];
            })
            ->values()
            ->toArray();

        $this->demoResults = [
            'selectedPackage' => [
                'name' => 'Sample: Paket Wisata Bali Beach Resort',
                'category' => 'Beach & Resort',
                'description' => $selectedText
            ],
            'tfidfProcess' => [
                'selectedPackageText' => $selectedText,
                'tokenCount' => count($tokens),
                'uniqueTokens' => count(array_unique($tokens)),
                'topTerms' => $topTerms,
                'totalDocuments' => count($sampleDocuments)
            ],
            'recommendations' => $similarities
        ];
    }

    private function calculateCosineSimilarity(array $vector1, array $vector2): float
    {
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        $allTerms = array_unique(array_merge(array_keys($vector1), array_keys($vector2)));

        foreach ($allTerms as $term) {
            $val1 = $vector1[$term] ?? 0;
            $val2 = $vector2[$term] ?? 0;

            $dotProduct += $val1 * $val2;
            $magnitude1 += $val1 * $val1;
            $magnitude2 += $val2 * $val2;
        }

        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        }

        return $dotProduct / (sqrt($magnitude1) * sqrt($magnitude2));
    }

    private function calculateTextSimilarity($package1, $package2, $allPackages): float
    {
        $text1 = $this->extractTextFeaturesForAnalysis($package1);
        $text2 = $this->extractTextFeaturesForAnalysis($package2);
        
        $tokens1 = $this->tokenizeTextForAnalysis($text1);
        $tokens2 = $this->tokenizeTextForAnalysis($text2);
        
        $documents = $allPackages->map(function ($package) {
            return $this->extractTextFeaturesForAnalysis($package);
        })->toArray();
        
        $tfidf1 = $this->calculateTfIdfVector($tokens1, $documents);
        $tfidf2 = $this->calculateTfIdfVector($tokens2, $documents);
        
        return $this->calculateCosineSimilarity($tfidf1, $tfidf2);
    }

    private function calculateCategorySimilarity($package1, $package2): float
    {
        $cat1 = $package1->category->name ?? '';
        $cat2 = $package2->category->name ?? '';
        
        return $cat1 === $cat2 ? 1.0 : 0.0;
    }

    private function calculatePriceSimilarity($package1, $package2): float
    {
        $price1 = $package1->price ?? 0;
        $price2 = $package2->price ?? 0;
        
        if ($price1 == 0 || $price2 == 0) return 0.0;
        
        $maxPrice = max($price1, $price2);
        $priceDiff = abs($price1 - $price2);
        
        return 1 - ($priceDiff / $maxPrice);
    }

    private function calculateDurationSimilarity($package1, $package2): float
    {
        $duration1 = $package1->duration ?? 0;
        $duration2 = $package2->duration ?? 0;
        
        if ($duration1 == 0 || $duration2 == 0) return 0.0;
        
        $maxDuration = max($duration1, $duration2);
        $durationDiff = abs($duration1 - $duration2);
        
        return 1 - ($durationDiff / $maxDuration);
    }

    private function generateExplanation($textSim, $catSim, $priceSim, $durationSim): string
    {
        $explanations = [];
        
        if ($textSim > 0.5) {
            $explanations[] = 'Konten teks sangat mirip';
        } elseif ($textSim > 0.3) {
            $explanations[] = 'Konten teks cukup mirip';
        }
        
        if ($catSim == 1.0) {
            $explanations[] = 'Kategori sama';
        }
        
        if ($priceSim > 0.8) {
            $explanations[] = 'Harga sangat mirip';
        } elseif ($priceSim > 0.6) {
            $explanations[] = 'Harga cukup mirip';
        }
        
        if ($durationSim > 0.8) {
            $explanations[] = 'Durasi sangat mirip';
        } elseif ($durationSim > 0.6) {
            $explanations[] = 'Durasi cukup mirip';
        }
        
        return empty($explanations) ? 'Kemiripan rendah' : implode(', ', $explanations);
    }

    private function calculateDetailedTfIdfAnalysis($selectedPackage, $allPackages): array
    {
        // Extract text features from selected package (same as ContentBasedRecommendationService)
        $selectedText = $this->extractTextFeaturesForAnalysis($selectedPackage);

        // Get all documents (packages) text
        $documents = $allPackages->map(function ($package) {
            return $this->extractTextFeaturesForAnalysis($package);
        })->toArray();

        // Tokenize selected package text
        $tokens = $this->tokenizeTextForAnalysis($selectedText);

        // Calculate TF-IDF for selected package
        $tfidfVector = $this->calculateTfIdfVector($tokens, $documents);

        // Prepare detailed TF-IDF table data
        $tfidfTableData = [];
        $termFreq = array_count_values($tokens);
        $totalTerms = count($tokens);
        $totalDocs = count($documents);

        foreach ($termFreq as $term => $freq) {
            if (strlen($term) > 2) { // Only include meaningful terms
                // Calculate TF
                $tf = $freq / $totalTerms;
                
                // Calculate DF
                $df = 0;
                foreach ($documents as $doc) {
                    if (stripos($doc, $term) !== false) {
                        $df++;
                    }
                }
                $df = max(1, $df); // Avoid division by zero
                
                // Calculate IDF
                $idf = log($totalDocs / $df);
                
                // Calculate TF-IDF
                $tfidf = $tf * $idf;

                $tfidfTableData[] = [
                    'term' => $term,
                    'frequency' => $freq,
                    'tf' => round($tf, 4),
                    'df' => $df,
                    'idf' => round($idf, 4),
                    'tfidf' => round($tfidf, 4)
                ];
            }
        }

        // Sort by TF-IDF score descending and limit to top 15
        usort($tfidfTableData, function($a, $b) {
            return $b['tfidf'] <=> $a['tfidf'];
        });
        $tfidfTableData = array_slice($tfidfTableData, 0, 15);

        // Get top terms for summary
        $topTerms = collect($tfidfVector)
            ->sortDesc()
            ->take(10)
            ->map(function ($score, $term) {
                return [
                    'term' => $term,
                    'tfidf_score' => round($score, 4)
                ];
            })
            ->values()
            ->toArray();

        return [
            'selectedPackageText' => $selectedText,
            'tokenCount' => count($tokens),
            'uniqueTokens' => count(array_unique($tokens)),
            'topTerms' => $topTerms,
            'totalDocuments' => count($documents),
            'tfidfTableData' => $tfidfTableData,
            'tfidf_vector' => $tfidfVector
        ];
    }

    private function extractTextFeaturesForAnalysis($package): string
    {
        // Handle string input (for sample demo)
        if (is_string($package)) {
            return $package;
        }

        $features = [];

        // Package name (weight: 3) - same as ContentBasedRecommendationService
        if (isset($package->name) && $package->name) {
            $features[] = str_repeat($package->name, 3);
        }

        // Category (weight: 2)
        if (isset($package->category) && $package->category && isset($package->category->name) && $package->category->name) {
            $features[] = str_repeat($package->category->name, 2);
        }

        // Description (weight: 1)
        if (isset($package->description) && $package->description) {
            $features[] = $package->description;
        }

        // Add inclusions using the correct relationship
        if (isset($package->travelIncludes) && $package->travelIncludes->isNotEmpty()) {
            $inclusions = $package->travelIncludes->pluck('name')->implode(' ');
            $features[] = $inclusions;
        }
        
        // Add exclusions using the correct relationship
        if (isset($package->travelExcludes) && $package->travelExcludes->isNotEmpty()) {
            $exclusions = $package->travelExcludes->pluck('name')->implode(' ');
            $features[] = $exclusions;
        }

        return implode(' ', $features);
    }

    private function tokenizeTextForAnalysis(string $text): array
    {
        // Convert to lowercase
        $text = strtolower($text);

        // Remove special characters and numbers
        $text = preg_replace('/[^a-z\s]/', ' ', $text);

        // Split into words
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Remove stopwords (same as ContentBasedRecommendationService)
        $stopwords = [
            // Indonesian stopwords
            'dan', 'atau', 'yang', 'di', 'ke', 'dari', 'untuk', 'dengan', 'pada', 'adalah', 'ini', 'itu', 
            'akan', 'telah', 'sudah', 'belum', 'tidak', 'bukan', 'juga', 'saja', 'hanya', 'dapat', 'bisa',
            'ada', 'anda', 'kita', 'kami', 'mereka', 'dia', 'ia', 'nya', 'mu', 'ku', 'se', 'ter', 'ber',
            // English stopwords
            'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 
            'as', 'is', 'are', 'was', 'were', 'be', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 
            'will', 'would', 'could', 'should', 'may', 'might', 'must', 'can', 'this', 'that', 'these', 
            'those', 'i', 'you', 'he', 'she', 'it', 'we', 'they', 'me', 'him', 'her', 'us', 'them'
        ];

        return array_filter($words, function($word) use ($stopwords) {
            return strlen($word) > 2 && !in_array($word, $stopwords);
        });
    }

    private function extractTextFeatures($package): string
    {
        // Handle string input (for sample demo)
        if (is_string($package)) {
            return $package;
        }

        $features = [];

        // Package name (weight: 3)
        if (isset($package->name) && $package->name) {
            $features[] = str_repeat($package->name, 3);
        }

        // Category (weight: 2)
        if (isset($package->category) && $package->category && isset($package->category->name) && $package->category->name) {
            $features[] = str_repeat($package->category->name, 2);
        }

        // Description (weight: 1)
        if (isset($package->description) && $package->description) {
            $features[] = $package->description;
        }

        return implode(' ', $features);
    }

    private function tokenizeText(string $text): array
    {
        // Convert to lowercase
        $text = strtolower($text);

        // Remove special characters and numbers
        $text = preg_replace('/[^a-z\s]/', ' ', $text);

        // Split into words
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Remove stopwords
        $stopwords = ['dan', 'atau', 'yang', 'di', 'ke', 'dari', 'untuk', 'dengan', 'pada', 'adalah', 'ini', 'itu', 'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'as', 'is', 'are', 'was', 'were', 'be', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should'];

        return array_filter($words, function($word) use ($stopwords) {
            return strlen($word) > 2 && !in_array($word, $stopwords);
        });
    }

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

    private function calculateTermFrequency(string $term, string $text): float
    {
        $tokens = $this->tokenizeText($text);
        $termCount = array_count_values($tokens);
        $totalTerms = count($tokens);

        return isset($termCount[$term]) ? $termCount[$term] / $totalTerms : 0;
    }

    private function calculateInverseDocumentFrequency(string $term, array $documents): float
    {
        $totalDocs = count($documents);
        $docsContainingTerm = 0;

        foreach ($documents as $doc) {
            if (stripos($doc, $term) !== false) {
                $docsContainingTerm++;
            }
        }

        return $docsContainingTerm > 0 ? log($totalDocs / $docsContainingTerm) : 0;
    }

}
