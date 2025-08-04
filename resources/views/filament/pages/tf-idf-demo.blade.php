<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-8 text-white mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">🔍 Content-Based Filtering dengan TF-IDF</h1>
                    <p class="text-blue-100 text-lg">Sistem Rekomendasi Paket Wisata Berbasis Konten</p>
                    
                    <!-- Quick Navigation -->
                    <div class="mt-4 flex space-x-3">
                        <button onclick="scrollToSection('form-section')" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded-lg text-sm transition-all duration-200">
                            📝 Form
                        </button>
                        <button onclick="scrollToSection('analysis-section')" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded-lg text-sm transition-all duration-200">
                            📊 Analisis
                        </button>
                        <button onclick="scrollToSection('recommendations-section')" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded-lg text-sm transition-all duration-200">
                            🎯 Rekomendasi
                        </button>
                        <button onclick="scrollToSection('algorithm-section')" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded-lg text-sm transition-all duration-200">
                            🧮 Algoritma
                        </button>
                    </div>
                </div>
                <div class="text-right">
                    <div class="bg-white/20 rounded-lg p-4 mb-3">
                        <div class="text-2xl font-bold">{{ count($this->getPackages()) }}</div>
                        <div class="text-sm text-blue-100">Total Paket</div>
                    </div>
                    
                    <!-- Export Button -->
                    @if($demoResults)
                    <button onclick="exportDemoData()" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm transition-all duration-200 flex items-center space-x-2">
                        <span>📥</span>
                        <span>Export Data</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div id="form-section" class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6">
                {{ $this->form }}
            </div>
        </div>

        @if($demoResults)
            <!-- Results Section -->
            <div class="space-y-6">
                <!-- Selected Package Info -->
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-6 border border-emerald-200">
                    <div class="flex items-start space-x-4">
                        <div class="bg-emerald-500 p-3 rounded-lg">
                            <x-heroicon-o-map-pin class="w-6 h-6 text-white" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Paket Terpilih</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <p class="text-sm text-gray-600">Nama Paket</p>
                                    <p class="font-semibold text-gray-900">{{ $demoResults['selectedPackage']['name'] }}</p>
                                </div>
                                @if(isset($demoResults['selectedPackage']['category']))
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <p class="text-sm text-gray-600">Kategori</p>
                                    <p class="font-semibold text-gray-900">{{ $demoResults['selectedPackage']['category'] }}</p>
                                </div>
                                @endif
                                @if(isset($demoResults['selectedPackage']['price']))
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <p class="text-sm text-gray-600">Harga</p>
                                    <p class="font-semibold text-gray-900">Rp {{ number_format($demoResults['selectedPackage']['price'], 0, ',', '.') }}</p>
                                </div>
                                @endif
                                @if(isset($demoResults['selectedPackage']['duration']))
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <p class="text-sm text-gray-600">Durasi</p>
                                    <p class="font-semibold text-gray-900">{{ $demoResults['selectedPackage']['duration'] }} hari</p>
                                </div>
                                @endif
                            </div>
                            @if(isset($demoResults['selectedPackage']['description']))
                            <div class="mt-4 bg-white rounded-lg p-4 shadow-sm">
                                <p class="text-sm text-gray-600 mb-2">Deskripsi</p>
                                <p class="text-gray-900">{{ $demoResults['selectedPackage']['description'] }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if(isset($demoResults['weightingData']))
                <!-- Weighting Components -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="bg-blue-500 p-2 rounded-lg">
                                <x-heroicon-o-scale class="w-5 h-5 text-white" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Komponen Bobot CBF</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($demoResults['weightingData'] as $weight)
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-100">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900">{{ $weight['component'] }}</h4>
                                    <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-sm font-medium">{{ $weight['weight_percentage'] }}%</span>
                                </div>
                                <p class="text-sm text-gray-600">{{ $weight['description'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                @if(isset($demoResults['tfidfAnalysis']) || isset($demoResults['tfidfProcess']))
                <!-- TF-IDF Analysis -->
                <div id="analysis-section" class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="bg-purple-500 p-2 rounded-lg">
                                <x-heroicon-o-calculator class="w-5 h-5 text-white" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Analisis TF-IDF</h3>
                        </div>

                        @php
                            $analysis = $demoResults['tfidfAnalysis'] ?? $demoResults['tfidfProcess'];
                        @endphp

                        <!-- Statistics -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-4 text-center border border-purple-100">
                                <div class="text-2xl font-bold text-purple-600">{{ $analysis['tokenCount'] }}</div>
                                <div class="text-sm text-gray-600">Total Token</div>
                            </div>
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 text-center border border-green-100">
                                <div class="text-2xl font-bold text-green-600">{{ $analysis['uniqueTokens'] }}</div>
                                <div class="text-sm text-gray-600">Token Unik</div>
                            </div>
                            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg p-4 text-center border border-blue-100">
                                <div class="text-2xl font-bold text-blue-600">{{ $analysis['totalDocuments'] }}</div>
                                <div class="text-sm text-gray-600">Total Dokumen</div>
                            </div>
                            <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-lg p-4 text-center border border-orange-100">
                                <div class="text-2xl font-bold text-orange-600">{{ count($analysis['topTerms']) }}</div>
                                <div class="text-sm text-gray-600">Top Terms</div>
                            </div>
                        </div>

                        <!-- Top Terms -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 mb-3">Top 10 Terms dengan Skor TF-IDF Tertinggi</h4>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                @foreach($analysis['topTerms'] as $term)
                                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-3 border border-indigo-100">
                                    <div class="font-medium text-gray-900">{{ $term['term'] }}</div>
                                    <div class="text-sm text-indigo-600 font-semibold">{{ $term['tfidf_score'] }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        @if(isset($analysis['tfidfTableData']))
                        <!-- Detailed TF-IDF Table -->
                        <div class="overflow-hidden">
                            <h4 class="font-semibold text-gray-900 mb-3">Detail Perhitungan TF-IDF</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Freq</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TF</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DF</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IDF</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TF-IDF</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($analysis['tfidfTableData'] as $row)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row['term'] }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $row['frequency'] }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $row['tf'] }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $row['df'] }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $row['idf'] }}</td>
                                            <td class="px-4 py-3 text-sm font-semibold text-purple-600">{{ $row['tfidf'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Recommendations -->
                <div id="recommendations-section" class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="bg-green-500 p-2 rounded-lg">
                                <x-heroicon-o-star class="w-5 h-5 text-white" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Hasil Rekomendasi</h3>
                        </div>

                        <div class="space-y-4">
                            @foreach($demoResults['recommendations'] as $index => $rec)
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</span>
                                            <h4 class="font-semibold text-gray-900">{{ $rec['name'] ?? $rec['title'] ?? $rec['document'] }}</h4>
                                        </div>
                                        
                                        @if(isset($rec['category']))
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                                            <div class="text-sm">
                                                <span class="text-gray-600">Kategori:</span>
                                                <span class="font-medium text-gray-900">{{ $rec['category'] }}</span>
                                            </div>
                                            @if(isset($rec['price']))
                                            <div class="text-sm">
                                                <span class="text-gray-600">Harga:</span>
                                                <span class="font-medium text-gray-900">Rp {{ number_format($rec['price'], 0, ',', '.') }}</span>
                                            </div>
                                            @endif
                                            @if(isset($rec['duration']))
                                            <div class="text-sm">
                                                <span class="text-gray-600">Durasi:</span>
                                                <span class="font-medium text-gray-900">{{ $rec['duration'] }} hari</span>
                                            </div>
                                            @endif
                                            <div class="text-sm">
                                                <span class="text-gray-600">Skor:</span>
                                                <span class="font-bold text-blue-600">{{ $rec['similarity_score'] }}</span>
                                            </div>
                                        </div>
                                        @else
                                        <div class="flex items-center space-x-4 mb-3">
                                            <div class="text-sm">
                                                <span class="text-gray-600">Skor Similarity:</span>
                                                <span class="font-bold text-blue-600">{{ $rec['similarity_score'] }}</span>
                                            </div>
                                        </div>
                                        @endif

                                        @if(isset($rec['score_breakdown']))
                                        <!-- Score Breakdown -->
                                        <div class="bg-white rounded-lg p-3 mb-3">
                                            <h5 class="text-sm font-semibold text-gray-900 mb-2">Breakdown Skor Similarity</h5>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                                <div class="text-xs">
                                                    <span class="text-gray-600">Text:</span>
                                                    <span class="font-medium text-purple-600">{{ round($rec['score_breakdown']['text_similarity'], 3) }}</span>
                                                </div>
                                                <div class="text-xs">
                                                    <span class="text-gray-600">Category:</span>
                                                    <span class="font-medium text-green-600">{{ round($rec['score_breakdown']['category_similarity'], 3) }}</span>
                                                </div>
                                                <div class="text-xs">
                                                    <span class="text-gray-600">Price:</span>
                                                    <span class="font-medium text-blue-600">{{ round($rec['score_breakdown']['price_similarity'], 3) }}</span>
                                                </div>
                                                <div class="text-xs">
                                                    <span class="text-gray-600">Duration:</span>
                                                    <span class="font-medium text-orange-600">{{ round($rec['score_breakdown']['duration_similarity'], 3) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if(isset($rec['explanation']))
                                        <div class="bg-blue-50 rounded-lg p-3">
                                            <p class="text-sm text-blue-800">
                                                <span class="font-medium">Penjelasan:</span> {{ $rec['explanation'] }}
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Similarity Score Badge -->
                                    <div class="ml-4">
                                        <div class="bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg px-3 py-2 text-center">
                                            <div class="text-lg font-bold">{{ $rec['similarity_score'] }}</div>
                                            <div class="text-xs opacity-90">Similarity</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Algorithm Explanation -->
                <div id="algorithm-section" class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-6 border border-indigo-200">
                    <div class="flex items-start space-x-4">
                        <div class="bg-indigo-500 p-3 rounded-lg">
                            <x-heroicon-o-academic-cap class="w-6 h-6 text-white" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cara Kerja Algoritma TF-IDF</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                        <x-heroicon-o-calculator class="w-4 h-4 mr-2 text-blue-500" />
                                        Rumus TF-IDF
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="bg-gray-50 p-2 rounded font-mono text-xs">
                                            TF(t,d) = (jumlah term t dalam dokumen d) / (total term dalam dokumen d)
                                        </div>
                                        <div class="bg-gray-50 p-2 rounded font-mono text-xs">
                                            IDF(t,D) = log(|D| / |{d ∈ D : t ∈ d}|)
                                        </div>
                                        <div class="bg-blue-50 p-2 rounded font-mono text-xs font-semibold">
                                            TF-IDF(t,d,D) = TF(t,d) × IDF(t,D)
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                        <x-heroicon-o-cog class="w-4 h-4 mr-2 text-green-500" />
                                        Langkah Algoritma
                                    </h4>
                                    <ol class="space-y-2 text-sm">
                                        <li class="flex items-start">
                                            <span class="bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mr-2 mt-0.5 flex-shrink-0">1</span>
                                            <span>Tokenisasi dan preprocessing teks</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mr-2 mt-0.5 flex-shrink-0">2</span>
                                            <span>Hitung Term Frequency (TF)</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mr-2 mt-0.5 flex-shrink-0">3</span>
                                            <span>Hitung Inverse Document Frequency (IDF)</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mr-2 mt-0.5 flex-shrink-0">4</span>
                                            <span>Kalikan TF × IDF untuk setiap term</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mr-2 mt-0.5 flex-shrink-0">5</span>
                                            <span>Hitung cosine similarity antar dokumen</span>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        /* Custom animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }
        
        /* Hover effects */
        .hover-scale:hover {
            transform: scale(1.02);
            transition: transform 0.2s ease-in-out;
        }
        
        /* Progress bar animation */
        .progress-bar {
            background: linear-gradient(90deg, #3B82F6, #8B5CF6);
            height: 4px;
            border-radius: 2px;
            animation: progress 2s ease-in-out;
        }
        
        @keyframes progress {
            from { width: 0%; }
            to { width: 100%; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scroll behavior
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
            
            // Add fade-in animation to result sections
            const resultSections = document.querySelectorAll('.space-y-6 > div');
            resultSections.forEach((section, index) => {
                section.style.animationDelay = `${index * 0.1}s`;
                section.classList.add('animate-fade-in-up');
            });
        });
    </script>
    
    <!-- TF-IDF Demo Interactive JavaScript -->
    @vite('resources/js/tf-idf-demo.js')
    
    <script>
        // Livewire hooks untuk integrasi dengan JavaScript
        document.addEventListener('livewire:load', function () {
            // Reinitialize interactive features setelah Livewire update
            if (window.tfIdfDemo) {
                window.tfIdfDemo.initializeDemo();
            }
        });
        
        document.addEventListener('livewire:update', function () {
            // Reinitialize setelah update komponen
            setTimeout(() => {
                if (window.tfIdfDemo) {
                    window.tfIdfDemo.initializeDemo();
                }
            }, 100);
        });
        
        // Export functionality
        function exportDemoData() {
            if (window.tfIdfDemo) {
                window.tfIdfDemo.exportDemoData();
            }
        }
        
        // Smooth scroll ke section tertentu
        function scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element && window.tfIdfDemo) {
                window.tfIdfDemo.smoothScrollTo(element);
            }
        }
    </script>
</x-filament-panels::page>