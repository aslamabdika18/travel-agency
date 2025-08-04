/**
 * Homepage Recommendations System
 * Handles trending packages with category filters for non-authenticated users
 */
class HomepageRecommendations {
    constructor() {
        this.container = null;
        this.currentFilter = 'all';
        this.isLoading = false;
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {
        this.container = document.getElementById('homepage-recommendations');
        if (!this.container) {
            console.warn('Homepage recommendations container not found');
            return;
        }

        this.renderRecommendationSection();
        this.loadTrendingPackages();
    }

    renderRecommendationSection() {
        this.container.innerHTML = `
            <div class="bg-neutral py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Header -->
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-primary-dark mb-4">Trending Packages This Week</h2>
                        <p class="text-lg text-secondary max-w-2xl mx-auto">
                            Discover the most popular travel destinations chosen by our community
                        </p>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex justify-center mb-8">
                        <div class="bg-neutral-dark p-1 rounded-lg inline-flex space-x-1" role="tablist">
                            <button class="filter-tab px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 active" 
                                    data-filter="all" role="tab">
                                All Packages
                            </button>
                            <button class="filter-tab px-4 py-2 text-sm font-medium rounded-md transition-all duration-200" 
                                    data-filter="adventure" role="tab">
                                Adventure
                            </button>
                            <button class="filter-tab px-4 py-2 text-sm font-medium rounded-md transition-all duration-200" 
                                    data-filter="beach" role="tab">
                                Beach & Island
                            </button>
                            <button class="filter-tab px-4 py-2 text-sm font-medium rounded-md transition-all duration-200" 
                                    data-filter="culture" role="tab">
                                Culture
                            </button>
                            <button class="filter-tab px-4 py-2 text-sm font-medium rounded-md transition-all duration-200" 
                                    data-filter="family" role="tab">
                                Family
                            </button>
                        </div>
                    </div>

                    <!-- Packages Grid -->
                    <div id="trending-packages-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Loading state -->
                        <div class="col-span-full flex justify-center py-12">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                        </div>
                    </div>

                    <!-- Call to Action for Login -->
                    <div class="text-center mt-12 p-6 bg-gradient-to-r from-accent-light to-primary-light rounded-lg border border-primary">
                        <h3 class="text-xl font-semibold text-secondary-dark mb-2">Want Personalized Recommendations?</h3>
                        <p class="text-secondary mb-4">Login to get travel suggestions tailored just for you based on your preferences and booking history.</p>
                        <a href="/login" class="inline-flex items-center px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Login for Personal Recommendations
                        </a>
                    </div>
                </div>
            </div>
        `;

        this.attachEventListeners();
    }

    attachEventListeners() {
        // Filter tab clicks
        const filterTabs = this.container.querySelectorAll('.filter-tab');
        filterTabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                const filter = e.target.dataset.filter;
                this.setActiveFilter(filter);
                this.loadTrendingPackages(filter);
            });
        });
    }

    setActiveFilter(filter) {
        this.currentFilter = filter;
        
        // Update tab states
        const filterTabs = this.container.querySelectorAll('.filter-tab');
        filterTabs.forEach(tab => {
            if (tab.dataset.filter === filter) {
                tab.classList.add('active', 'bg-white', 'text-primary', 'shadow-sm');
                tab.classList.remove('text-secondary', 'hover:text-secondary-dark');
            } else {
                tab.classList.remove('active', 'bg-white', 'text-primary', 'shadow-sm');
                tab.classList.add('text-secondary', 'hover:text-secondary-dark');
            }
        });
    }

    async loadTrendingPackages(filter = 'all') {
        if (this.isLoading) return;
        
        this.isLoading = true;
        const grid = this.container.querySelector('#trending-packages-grid');
        
        // Show loading state
        grid.innerHTML = `
            <div class="col-span-full flex justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            </div>
        `;

        try {
            const response = await fetch(`/api/recommendations/trending?filter=${filter}&limit=6`);
            const data = await response.json();

            if (data.success && data.data.trending_packages) {
                this.renderPackages(data.data.trending_packages);
            } else {
                this.renderError('Failed to load trending packages');
            }
        } catch (error) {
            console.error('Error loading trending packages:', error);
            this.renderError('Something went wrong. Please try again.');
        } finally {
            this.isLoading = false;
        }
    }

    renderPackages(packages) {
        const grid = this.container.querySelector('#trending-packages-grid');
        
        if (packages.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <div class="text-secondary-light mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.562M15 6.306a7.962 7.962 0 00-6 0m6 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v1.306m8 0V7a2 2 0 012 2v6.414l-1.293-1.293a1 1 0 00-1.414 0L12 17.414l-2.293-2.293a1 1 0 00-1.414 0L7 16.414V9a2 2 0 012-2h8a2 2 0 012 2v6.414z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-secondary-dark mb-2">No packages found</h3>
                    <p class="text-secondary">Try selecting a different category or check back later.</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = packages.map(pkg => this.getPackageCardHTML(pkg)).join('');
        
        // Add scroll animation
        this.initScrollAnimation();
    }

    getPackageCardHTML(pkg) {
        const rating = pkg.average_rating || 0;
        const reviewCount = pkg.review_count || 0;
        const stars = this.generateStarRating(rating);
        
        return `
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 animate-on-scroll">
                <div class="relative">
                    <img src="${pkg.thumbnail_url || '/images/placeholder-package.jpg'}" 
                         alt="${pkg.name}" 
                         class="w-full h-48 object-cover">
                    <div class="absolute top-4 right-4">
                        <span class="bg-primary text-white px-2 py-1 rounded-full text-xs font-medium">
                            ${pkg.duration} days
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-secondary-dark mb-2 line-clamp-2">
                        ${pkg.name}
                    </h3>
                    
                    <p class="text-secondary text-sm mb-4 line-clamp-3">
                        ${pkg.description}
                    </p>
                    
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-1">
                            ${stars}
                            <span class="text-sm text-secondary-light ml-2">
                                ${rating.toFixed(1)} (${reviewCount} reviews)
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold text-primary">
                            ${pkg.formatted_price}
                        </div>
                        <a href="/travel-packages/${pkg.slug}" 
                           class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors duration-200 text-sm font-medium">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    generateStarRating(rating) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
        
        let stars = '';
        
        // Full stars
        for (let i = 0; i < fullStars; i++) {
            stars += '<svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>';
        }
        
        // Half star
        if (hasHalfStar) {
            stars += '<svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><defs><linearGradient id="half"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#e5e7eb"/></linearGradient></defs><path fill="url(#half)" d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>';
        }
        
        // Empty stars
        for (let i = 0; i < emptyStars; i++) {
            stars += '<svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>';
        }
        
        return stars;
    }

    renderError(message) {
        const grid = this.container.querySelector('#trending-packages-grid');
        grid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <div class="text-accent-dark mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-secondary-dark mb-2">Oops! Something went wrong</h3>
                <p class="text-secondary mb-4">${message}</p>
                <button onclick="window.location.reload()" 
                        class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors duration-200">
                    Try Again
                </button>
            </div>
        `;
    }

    initScrollAnimation() {
        const cards = this.container.querySelectorAll('.animate-on-scroll');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 100);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    }
}

// Initialize when DOM is ready
if (typeof window !== 'undefined') {
    window.HomepageRecommendations = HomepageRecommendations;
    
    // Auto-initialize if we're on the homepage
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('homepage-recommendations')) {
                new HomepageRecommendations();
            }
        });
    } else {
        if (document.getElementById('homepage-recommendations')) {
            new HomepageRecommendations();
        }
    }
}