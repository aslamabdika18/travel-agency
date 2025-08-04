/**
 * Sistem Rekomendasi Travel Package - Vanilla JavaScript
 * Implementasi sederhana tanpa framework untuk kemudahan pemahaman
 */

class RecommendationSystem {
    constructor() {
        this.apiBaseUrl = '/api/recommendations';
        this.currentPackageId = null;
        this.currentUserId = null;
        this.isLoading = false;
        this.requestCache = new Map(); // Cache untuk request
        this.debounceTimer = null;
        this.activeRequest = null; // Track active request
        this.lastTabSwitch = 0; // Throttling untuk tab switching
        
        this.init();
    }

    /**
     * Inisialisasi sistem rekomendasi
     */
    init() {
        // Ambil data dari window object
        if (window.travelPackageConfig) {
            this.currentPackageId = window.travelPackageConfig.packageId;
            this.currentUserId = window.travelPackageConfig.currentUser;
        }

        // Tunggu DOM siap
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.render());
        } else {
            this.render();
        }
    }

    /**
     * Render komponen rekomendasi
     */
    render() {
        const container = document.getElementById('recommendation-section');
        if (!container) {
            console.warn('Container rekomendasi tidak ditemukan');
            return;
        }

        // Buat struktur HTML
        container.innerHTML = this.getRecommendationHTML();
        
        // Bind event listeners
        this.bindEvents();
        
        // Load data rekomendasi
        this.loadRecommendations();
    }

    /**
     * Template HTML untuk section rekomendasi
     */
    getRecommendationHTML() {
        return `
            <div class="recommendation-container">
                <div class="text-center mb-6 sm:mb-8">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-primary-dark mb-2 sm:mb-3">
                        Rekomendasi Paket Travel
                    </h2>
                    <p class="text-secondary text-sm sm:text-base">
                        Powered by TF-IDF Content-Based Filtering
                    </p>
                </div>

                <!-- Tab Navigation -->
                <div class="flex justify-center mb-6 sm:mb-8">
                    <div class="bg-white rounded-lg p-1 shadow-md">
                        <button id="tab-similar" class="tab-button active px-4 sm:px-6 py-2 sm:py-3 rounded-md font-medium text-sm sm:text-base transition-all duration-300">
                            Paket Serupa
                        </button>
                        ${this.currentUserId ? `
                        <button id="tab-personalized" class="tab-button px-4 sm:px-6 py-2 sm:py-3 rounded-md font-medium text-sm sm:text-base transition-all duration-300">
                            Rekomendasi Personal
                        </button>
                        ` : ''}
                    </div>
                </div>

                <!-- Loading State -->
                <div id="loading-state" class="text-center py-8 hidden">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    <p class="mt-2 text-secondary">Memuat rekomendasi...</p>
                </div>

                <!-- Error State -->
                <div id="error-state" class="text-center py-8 hidden">
                    <div class="text-red-500 mb-2">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                    <p class="text-secondary">Gagal memuat rekomendasi. <button id="retry-btn" class="text-primary hover:underline">Coba lagi</button></p>
                </div>

                <!-- Recommendations Grid -->
                <div id="recommendations-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    <!-- Recommendations akan diisi di sini -->
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="text-center py-8 hidden">
                    <div class="text-gray-400 mb-2">
                        <i class="fas fa-search text-2xl"></i>
                    </div>
                    <p class="text-secondary">Belum ada rekomendasi tersedia.</p>
                </div>
            </div>
        `;
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Tab navigation
        const tabSimilar = document.getElementById('tab-similar');
        const tabPersonalized = document.getElementById('tab-personalized');
        const retryBtn = document.getElementById('retry-btn');

        if (tabSimilar) {
            tabSimilar.addEventListener('click', () => this.switchTab('similar'));
        }

        if (tabPersonalized) {
            tabPersonalized.addEventListener('click', () => this.switchTab('personalized'));
        }

        if (retryBtn) {
            retryBtn.addEventListener('click', () => this.loadRecommendations());
        }
    }

    /**
     * Switch tab rekomendasi dengan throttling
     */
    switchTab(tabType) {
        // Prevent rapid tab switching
        if (this.lastTabSwitch && Date.now() - this.lastTabSwitch < 500) {
            return;
        }
        this.lastTabSwitch = Date.now();
        
        // Update active tab
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active', 'bg-primary', 'text-white');
            btn.classList.add('text-gray-600', 'hover:text-primary');
        });

        const activeTab = document.getElementById(`tab-${tabType}`);
        if (activeTab) {
            activeTab.classList.add('active', 'bg-primary', 'text-white');
            activeTab.classList.remove('text-gray-600', 'hover:text-primary');
        }

        // Load recommendations for selected tab
        this.loadRecommendations(tabType);
    }

    /**
     * Load data rekomendasi dari API dengan optimasi
     */
    async loadRecommendations(type = 'similar') {
        // Prevent multiple simultaneous requests
        if (this.isLoading || this.activeRequest) {
            if (this.activeRequest) {
                this.activeRequest.abort();
            }
            return;
        }
        
        // Debounce untuk mencegah request berlebihan
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }
        
        this.debounceTimer = setTimeout(async () => {
            await this._performLoadRecommendations(type);
        }, 300);
    }
    
    /**
     * Perform actual loading with caching
     */
    async _performLoadRecommendations(type = 'similar') {
        this.isLoading = true;
        this.showLoading();

        try {
            let url;
            
            if (type === 'personalized' && this.currentUserId) {
                url = `${this.apiBaseUrl}/personalized`;
            } else if (type === 'similar' && this.currentPackageId) {
                url = `${this.apiBaseUrl}/similar/${this.currentPackageId}`;
            } else {
                url = `${this.apiBaseUrl}/homepage`;
            }
            
            // Check cache first
            const cacheKey = `${type}_${this.currentPackageId || 'default'}`;
            if (this.requestCache.has(cacheKey)) {
                const cachedData = this.requestCache.get(cacheKey);
                this.renderRecommendations(cachedData);
                return;
            }

            // Create AbortController untuk cancel request jika diperlukan
            const controller = new AbortController();
            this.activeRequest = controller;
            
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: controller.signal
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            
            // Handle different API response structures
            let recommendations = [];
            if (data.data && data.data.similar_packages) {
                recommendations = data.data.similar_packages;
            } else if (data.similar_packages) {
                recommendations = data.similar_packages;
            } else if (Array.isArray(data.data)) {
                recommendations = data.data;
            } else if (Array.isArray(data)) {
                recommendations = data;
            }
            
            // Cache hasil untuk request berikutnya (max 10 cache entries)
            if (this.requestCache.size >= 10) {
                const firstKey = this.requestCache.keys().next().value;
                this.requestCache.delete(firstKey);
            }
            this.requestCache.set(cacheKey, recommendations);
            
            this.renderRecommendations(recommendations);
            
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Error loading recommendations:', error);
                this.showError();
            }
        } finally {
            this.isLoading = false;
            this.activeRequest = null;
            this.hideLoading();
        }
    }

    /**
     * Render daftar rekomendasi
     */
    renderRecommendations(recommendations) {
        const grid = document.getElementById('recommendations-grid');
        const emptyState = document.getElementById('empty-state');
        
        if (!recommendations || recommendations.length === 0) {
            grid.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        
        grid.innerHTML = recommendations.map(pkg => this.getPackageCardHTML(pkg)).join('');
        
        // Tambahkan animasi scroll
        this.initScrollAnimations();
    }

    /**
     * Template HTML untuk card paket travel
     */
    getPackageCardHTML(pkg) {
        const price = pkg.price ? new Intl.NumberFormat('id-ID').format(pkg.price) : 'Hubungi kami';
        const rating = pkg.average_rating || 0;
        const reviewCount = pkg.review_count || 0;
        const thumbnail = pkg.thumbnail || '/images/placeholder-travel.jpg';
        
        return `
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 animate-on-scroll">
                <div class="relative">
                    <img src="${thumbnail}" 
                         alt="${pkg.name}" 
                         class="w-full h-48 object-cover"
                         onerror="this.src='/images/placeholder-travel.jpg'">
                    <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-md text-xs font-medium text-primary">
                        ${pkg.duration} Hari
                    </div>
                </div>
                
                <div class="p-4 sm:p-6">
                    <h3 class="font-bold text-secondary-dark mb-2 text-sm sm:text-base line-clamp-2">
                        ${pkg.name}
                    </h3>
                    
                    <p class="text-secondary text-xs sm:text-sm mb-3 line-clamp-2">
                        ${pkg.description ? pkg.description.replace(/<[^>]*>/g, '').substring(0, 100) + '...' : 'Deskripsi tidak tersedia'}
                    </p>
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="flex text-yellow-400 text-xs">
                                ${this.generateStarRating(rating)}
                            </div>
                            <span class="text-xs text-secondary ml-1">(${reviewCount})</span>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-secondary">Mulai dari</div>
                            <div class="font-bold text-primary text-sm">Rp ${price}</div>
                        </div>
                    </div>
                    
                    <a href="/travel-packages/${pkg.slug}" 
                       class="block w-full bg-primary hover:bg-primary-dark text-white text-center font-medium py-2 px-4 rounded-md transition duration-300 text-xs sm:text-sm">
                        Lihat Detail
                    </a>
                </div>
            </div>
        `;
    }

    /**
     * Generate star rating HTML
     */
    generateStarRating(rating) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
        
        let stars = '';
        
        // Full stars
        for (let i = 0; i < fullStars; i++) {
            stars += '<i class="fas fa-star"></i>';
        }
        
        // Half star
        if (hasHalfStar) {
            stars += '<i class="fas fa-star-half-alt"></i>';
        }
        
        // Empty stars
        for (let i = 0; i < emptyStars; i++) {
            stars += '<i class="far fa-star"></i>';
        }
        
        return stars;
    }

    /**
     * Show loading state
     */
    showLoading() {
        document.getElementById('loading-state').classList.remove('hidden');
        document.getElementById('error-state').classList.add('hidden');
        document.getElementById('recommendations-grid').classList.add('hidden');
        document.getElementById('empty-state').classList.add('hidden');
    }

    /**
     * Hide loading state
     */
    hideLoading() {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('recommendations-grid').classList.remove('hidden');
    }

    /**
     * Show error state
     */
    showError() {
        document.getElementById('error-state').classList.remove('hidden');
        document.getElementById('recommendations-grid').classList.add('hidden');
        document.getElementById('empty-state').classList.add('hidden');
    }

    /**
     * Inisialisasi animasi scroll untuk cards
     */
    initScrollAnimations() {
        const cards = document.querySelectorAll('.animate-on-scroll');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        cards.forEach(card => {
            observer.observe(card);
        });
    }
}

// Inisialisasi sistem rekomendasi
new RecommendationSystem();

// Export untuk penggunaan di tempat lain jika diperlukan
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RecommendationSystem;
}