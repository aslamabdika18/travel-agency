<?php

namespace App\Services;

use App\Models\TravelPackage;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ContentBasedRecommendationService
{
    /**
     * Cache untuk menyimpan hasil perhitungan similarity
     */
    private static $similarityCache = [];
    
    /**
     * Mendapatkan rekomendasi paket perjalanan berdasarkan content similarity
     * menggunakan TF-IDF (Term Frequency-Inverse Document Frequency)
     */
    public function getRecommendations(int $packageId, int $limit = 5): Collection
    {
        try {
            // Check cache first
            $cacheKey = "recommendations_{$packageId}_{$limit}";
            if (isset(self::$similarityCache[$cacheKey])) {
                return self::$similarityCache[$cacheKey];
            }
            
            // Ambil paket referensi
            $referencePackage = TravelPackage::with(['category', 'travelIncludes', 'travelExcludes'])->find($packageId);
            
            if (!$referencePackage) {
                return collect();
            }

            // Ambil paket lain dengan batasan untuk menghindari over processing
            $allPackages = TravelPackage::with(['category', 'travelIncludes', 'travelExcludes'])
                ->where('id', '!=', $packageId)
                ->where('is_active', true)
                ->limit(50) // Batasi untuk menghindari over processing
                ->get();

            if ($allPackages->isEmpty()) {
                return collect();
            }

            // Hitung similarity score untuk setiap paket dengan optimasi
            $similarities = collect();
            foreach ($allPackages as $package) {
                $score = $this->calculateSimilarityScore($referencePackage, $package);
                
                // Skip paket dengan score terlalu rendah untuk menghemat memori
                if ($score > 0.1) {
                    $similarities->push([
                        'package' => $package,
                        'similarity_score' => $score,
                        'explanation' => $this->generateExplanation($referencePackage, $package, $score)
                    ]);
                }
            }

            // Filter dan urutkan hasil
            $result = $similarities
                ->filter(function ($item) {
                    return $item['similarity_score'] > 0;
                })
                ->sortByDesc('similarity_score')
                ->take($limit)
                ->values();
                
            // Cache hasil untuk request berikutnya
            self::$similarityCache[$cacheKey] = $result;
            
            return $result;

        } catch (\Exception $e) {
            Log::error('Error in ContentBasedRecommendationService: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Menghitung similarity score antara dua paket perjalanan
     */
    private function calculateSimilarityScore(TravelPackage $package1, TravelPackage $package2): float
    {
        $score = 0;
        $maxScore = 0;

        // 1. Category similarity (bobot: 25%)
        $categoryWeight = 0.25;
        $maxScore += $categoryWeight;
        if ($package1->category_id === $package2->category_id) {
            $score += $categoryWeight;
        }

        // 2. Price range similarity (bobot: 15%)
        $priceWeight = 0.15;
        $maxScore += $priceWeight;
        $priceSimilarity = $this->calculatePriceSimilarity($package1->price, $package2->price);
        $score += $priceSimilarity * $priceWeight;

        // 3. Duration similarity (bobot: 10%)
        $durationWeight = 0.10;
        $maxScore += $durationWeight;
        $durationSimilarity = $this->calculateDurationSimilarity($package1->duration, $package2->duration);
        $score += $durationSimilarity * $durationWeight;

        // 4. Text content similarity menggunakan TF-IDF (bobot: 50%)
        $textWeight = 0.50;
        $maxScore += $textWeight;
        $textSimilarity = $this->calculateTextSimilarity($package1, $package2);
        $score += $textSimilarity * $textWeight;

        // Normalisasi score ke range 0-1
        return $maxScore > 0 ? $score / $maxScore : 0;
    }

    /**
     * Menghitung similarity berdasarkan harga
     */
    private function calculatePriceSimilarity($price1, $price2): float
    {
        // Konversi ke float untuk memastikan operasi numerik berjalan dengan baik
        $price1 = (float) $price1;
        $price2 = (float) $price2;
        
        $maxPrice = max($price1, $price2);
        $minPrice = min($price1, $price2);
        
        if ($maxPrice == 0) return 1;
        
        $difference = abs($price1 - $price2);
        $similarity = 1 - ($difference / $maxPrice);
        
        return max(0, $similarity);
    }

    /**
     * Menghitung similarity berdasarkan durasi
     */
    private function calculateDurationSimilarity($duration1, $duration2): float
    {
        // Konversi duration string ke integer (ambil angka dari string seperti "3 Hari")
        $days1 = $this->extractDaysFromDuration($duration1);
        $days2 = $this->extractDaysFromDuration($duration2);
        
        $difference = abs($days1 - $days2);
        
        // Jika selisih <= 1 hari, similarity tinggi
        if ($difference <= 1) return 1;
        
        // Jika selisih <= 3 hari, similarity sedang
        if ($difference <= 3) return 0.7;
        
        // Jika selisih <= 7 hari, similarity rendah
        if ($difference <= 7) return 0.3;
        
        // Selisih > 7 hari, similarity sangat rendah
        return 0.1;
    }
    
    /**
     * Ekstrak jumlah hari dari string duration
     */
    private function extractDaysFromDuration($duration): int
    {
        if (is_numeric($duration)) {
            return (int) $duration;
        }
        
        // Ekstrak angka dari string seperti "3 Hari", "5 Days", dll
        preg_match('/\d+/', (string) $duration, $matches);
        return isset($matches[0]) ? (int) $matches[0] : 1;
    }

    /**
     * Menghitung text similarity menggunakan TF-IDF
     */
    private function calculateTextSimilarity(TravelPackage $package1, TravelPackage $package2): float
    {
        // Gabungkan teks dari berbagai field
        $text1 = $this->extractTextFeatures($package1);
        $text2 = $this->extractTextFeatures($package2);

        // Tokenisasi dan preprocessing
        $tokens1 = $this->tokenizeText($text1);
        $tokens2 = $this->tokenizeText($text2);

        if (empty($tokens1) || empty($tokens2)) {
            return 0;
        }

        // Hitung TF-IDF vectors
        $allTokens = array_unique(array_merge($tokens1, $tokens2));
        $vector1 = $this->calculateTfIdfVector($tokens1, $allTokens, [$tokens1, $tokens2]);
        $vector2 = $this->calculateTfIdfVector($tokens2, $allTokens, [$tokens1, $tokens2]);

        // Hitung cosine similarity
        return $this->cosineSimilarity($vector1, $vector2);
    }

    /**
     * Ekstrak fitur teks dari paket perjalanan
     */
    private function extractTextFeatures(TravelPackage $package): string
    {
        $features = [];
        
        // Nama paket (bobot lebih tinggi dengan pengulangan)
        $features[] = str_repeat($package->name . ' ', 3);
        
        // Deskripsi
        $features[] = $package->description;
        
        // Kategori (jika ada)
        if ($package->category) {
            $features[] = str_repeat($package->category->name . ' ', 2);
        }
        
        // Inclusions menggunakan relasi yang benar
        if ($package->relationLoaded('travelIncludes') && $package->travelIncludes->isNotEmpty()) {
            $inclusions = $package->travelIncludes->pluck('name')->implode(' ');
            $features[] = $inclusions;
        }
        
        // Exclusions menggunakan relasi yang benar
        if ($package->relationLoaded('travelExcludes') && $package->travelExcludes->isNotEmpty()) {
            $exclusions = $package->travelExcludes->pluck('name')->implode(' ');
            $features[] = $exclusions;
        }

        return implode(' ', $features);
    }

    /**
     * Tokenisasi teks
     */
    private function tokenizeText(string $text): array
    {
        // Konversi ke lowercase
        $text = strtolower($text);
        
        // Hapus karakter khusus dan angka
        $text = preg_replace('/[^a-z\s]/', ' ', $text);
        
        // Split menjadi kata-kata
        $tokens = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        // Filter kata-kata pendek dan stopwords
        $stopwords = ['dan', 'atau', 'yang', 'di', 'ke', 'dari', 'untuk', 'dengan', 'pada', 'dalam', 'adalah', 'akan', 'dapat', 'tidak', 'ada', 'ini', 'itu', 'the', 'and', 'or', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'is', 'are', 'was', 'were', 'be', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'must', 'can', 'a', 'an', 'as', 'if', 'it', 'its', 'this', 'that', 'these', 'those'];
        
        return array_filter($tokens, function($token) use ($stopwords) {
            return strlen($token) >= 3 && !in_array($token, $stopwords);
        });
    }

    /**
     * Cache untuk IDF values
     */
    private static $idfCache = [];
    
    /**
     * Menghitung TF-IDF vector untuk dokumen
     */
    private function calculateTfIdfVector(array $tokens, array $allTokens, array $allDocuments): array
    {
        $vector = [];
        $docLength = count($tokens);
        $totalDocs = count($allDocuments);
        
        // Pre-calculate term frequencies untuk menghindari multiple array_count_values calls
        $termFreqs = array_count_values($tokens);
        
        foreach ($allTokens as $token) {
            // Term Frequency (TF)
            $tf = $termFreqs[$token] ?? 0;
            $tf = $docLength > 0 ? $tf / $docLength : 0;
            
            // Check IDF cache first
            $idfKey = md5($token . '_' . serialize($allDocuments));
            if (!isset(self::$idfCache[$idfKey])) {
                // Document Frequency (DF) - optimized
                $df = 0;
                foreach ($allDocuments as $doc) {
                    if (in_array($token, $doc, true)) { // strict comparison
                        $df++;
                    }
                }
                
                // Inverse Document Frequency (IDF)
                self::$idfCache[$idfKey] = $df > 0 ? log($totalDocs / $df) : 0;
            }
            
            $idf = self::$idfCache[$idfKey];
            
            // TF-IDF
            $vector[] = $tf * $idf;
        }
        
        return $vector;
    }

    /**
     * Menghitung cosine similarity antara dua vector
     */
    private function cosineSimilarity(array $vector1, array $vector2): float
    {
        if (count($vector1) !== count($vector2)) {
            return 0;
        }
        
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;
        
        for ($i = 0; $i < count($vector1); $i++) {
            $dotProduct += $vector1[$i] * $vector2[$i];
            $magnitude1 += $vector1[$i] * $vector1[$i];
            $magnitude2 += $vector2[$i] * $vector2[$i];
        }
        
        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);
        
        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        }
        
        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    /**
     * Generate penjelasan mengapa paket direkomendasikan
     */
    private function generateExplanation(TravelPackage $reference, TravelPackage $recommended, float $score): array
    {
        $reasons = [];
        
        // Category similarity
        if ($reference->category_id === $recommended->category_id) {
            $reasons[] = "Kategori yang sama: {$reference->category->name}";
        }
        
        // Price similarity
        $priceDiff = abs($reference->price - $recommended->price);
        $pricePercentDiff = $reference->price > 0 ? ($priceDiff / $reference->price) * 100 : 0;
        if ($pricePercentDiff <= 20) {
            $reasons[] = "Harga yang serupa (selisih " . number_format($pricePercentDiff, 1) . "%)";
        }
        
        // Duration similarity
        $refDays = $this->extractDaysFromDuration($reference->duration);
        $recDays = $this->extractDaysFromDuration($recommended->duration);
        $durationDiff = abs($refDays - $recDays);
        if ($durationDiff <= 2) {
            $reasons[] = "Durasi perjalanan yang mirip ({$recDays} hari)";
        }
        
        // Content similarity
        if ($score >= 0.7) {
            $reasons[] = "Konten dan deskripsi yang sangat mirip";
        } elseif ($score >= 0.5) {
            $reasons[] = "Konten dan deskripsi yang cukup mirip";
        }
        
        return [
            'similarity_score' => round($score * 100, 1),
            'reasons' => $reasons,
            'confidence' => $this->getConfidenceLevel($score)
        ];
    }

    /**
     * Mendapatkan level confidence berdasarkan similarity score
     */
    private function getConfidenceLevel(float $score): string
    {
        if ($score >= 0.8) return 'Sangat Tinggi';
        if ($score >= 0.6) return 'Tinggi';
        if ($score >= 0.4) return 'Sedang';
        if ($score >= 0.2) return 'Rendah';
        return 'Sangat Rendah';
    }

    /**
     * Mendapatkan rekomendasi berdasarkan kategori
     */
    public function getRecommendationsByCategory(int $categoryId, int $limit = 10): Collection
    {
        return TravelPackage::with('category')
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mendapatkan rekomendasi berdasarkan price range
     */
    public function getRecommendationsByPriceRange(float $minPrice, float $maxPrice, int $limit = 10): Collection
    {
        return TravelPackage::with('category')
            ->whereBetween('price', [$minPrice, $maxPrice])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}