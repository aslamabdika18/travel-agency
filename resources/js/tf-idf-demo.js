/**
 * TF-IDF Demo Interactive Features
 * Menambahkan animasi, tooltip, dan elemen interaktif ke halaman demo TF-IDF
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeTfIdfDemo();
});

function initializeTfIdfDemo() {
    setupAnimations();
    setupInteractiveElements();
    setupTooltips();
    setupProgressBars();
    setupScrollAnimations();
    setupTableInteractions();
    injectAdditionalCSS();
}

// Setup animasi
function setupAnimations() {
    // Fade in elements saat page load
    const elements = document.querySelectorAll('.animate-fade-in');
    elements.forEach((el, index) => {
        setTimeout(() => {
            el.classList.add('opacity-100');
            el.classList.remove('opacity-0');
        }, index * 100);
    });

    // Stagger animation untuk cards
    const cards = document.querySelectorAll('.bg-white.rounded-lg.shadow');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('animate-fade-in-up');
    });
}

// Setup elemen interaktif
function setupInteractiveElements() {
    // Hover effects untuk recommendation cards
    const recommendationCards = document.querySelectorAll('[data-recommendation-card]');
    recommendationCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.classList.add('transform', 'scale-105', 'shadow-lg');
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.classList.remove('transform', 'scale-105', 'shadow-lg');
        });
    });

    // Click effects untuk buttons
    const buttons = document.querySelectorAll('button');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.add('pulse-glow');
            setTimeout(() => {
                this.classList.remove('pulse-glow');
            }, 600);
        });
    });
}

// Setup tooltips
function setupTooltips() {
    const tooltipTriggers = document.querySelectorAll('[data-tooltip]');
    
    tooltipTriggers.forEach(trigger => {
        const tooltip = document.createElement('div');
        tooltip.className = 'absolute z-50 px-3 py-2 text-sm text-white bg-gray-900 rounded-lg shadow-lg opacity-0 pointer-events-none transition-opacity duration-300';
        tooltip.textContent = trigger.getAttribute('data-tooltip');
        
        trigger.appendChild(tooltip);
        
        trigger.addEventListener('mouseenter', () => {
            tooltip.classList.remove('opacity-0');
            tooltip.classList.add('opacity-100');
        });
        
        trigger.addEventListener('mouseleave', () => {
            tooltip.classList.add('opacity-0');
            tooltip.classList.remove('opacity-100');
        });
    });
}

// Progress bars untuk similarity scores
function setupProgressBars() {
    const progressBars = document.querySelectorAll('[data-progress]');
    
    progressBars.forEach(bar => {
        const percentage = parseFloat(bar.getAttribute('data-progress'));
        const progressFill = bar.querySelector('.progress-fill');
        
        if (progressFill) {
            setTimeout(() => {
                progressFill.style.width = `${percentage}%`;
                progressFill.style.transition = 'width 1s ease-in-out';
            }, 500);
        }
    });
}

// Scroll animations
function setupScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    const animatedElements = document.querySelectorAll('.scroll-animate');
    animatedElements.forEach(el => observer.observe(el));
}

// Table interactions
function setupTableInteractions() {
    const tableRows = document.querySelectorAll('tbody tr');
    
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.classList.add('bg-blue-50', 'transform', 'scale-102');
        });
        
        row.addEventListener('mouseleave', function() {
            this.classList.remove('bg-blue-50', 'transform', 'scale-102');
        });
        
        // Highlight terms on click
        row.addEventListener('click', function() {
            const term = this.querySelector('td:first-child')?.textContent;
            if (term) {
                highlightTermInContent(term);
            }
        });
    });
}

// Highlight terms dalam content
function highlightTermInContent(term) {
    const contentElements = document.querySelectorAll('.package-content');
    
    contentElements.forEach(element => {
        const text = element.textContent;
        const regex = new RegExp(`\\b${term}\\b`, 'gi');
        
        if (regex.test(text)) {
            const highlightedText = text.replace(regex, `<span class="highlighted-term">${term}</span>`);
            element.innerHTML = highlightedText;
            
            // Remove highlight setelah 3 detik
            setTimeout(() => {
                element.innerHTML = text;
            }, 3000);
        }
    });
}

// Utility functions
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
    }
}

function exportDemoData() {
    const demoData = {
        timestamp: new Date().toISOString(),
        selectedPackage: document.querySelector('[data-selected-package]')?.textContent || 'N/A',
        recommendations: [],
        tfIdfAnalysis: {}
    };
    
    // Collect recommendation data
    const recommendationCards = document.querySelectorAll('[data-recommendation-card]');
    recommendationCards.forEach(card => {
        const title = card.querySelector('h4')?.textContent || '';
        const score = card.querySelector('[data-similarity-score]')?.textContent || '';
        demoData.recommendations.push({ title, score });
    });
    
    // Collect TF-IDF analysis data
    const analysisTable = document.querySelector('#tf-idf-analysis-table');
    if (analysisTable) {
        const rows = analysisTable.querySelectorAll('tbody tr');
        demoData.tfIdfAnalysis.terms = [];
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 4) {
                demoData.tfIdfAnalysis.terms.push({
                    term: cells[0].textContent,
                    tf: cells[1].textContent,
                    idf: cells[2].textContent,
                    tfIdf: cells[3].textContent
                });
            }
        });
    }
    
    // Download sebagai JSON
    const blob = new Blob([JSON.stringify(demoData, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `tf-idf-demo-${Date.now()}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// Make functions globally available
window.exportDemoData = exportDemoData;
window.scrollToSection = scrollToSection;

// Inject additional CSS untuk animations
function injectAdditionalCSS() {
    const style = document.createElement('style');
    style.textContent = `
        .highlighted-term {
            background-color: #FEF3C7;
            color: #92400E;
            padding: 2px 4px;
            border-radius: 4px;
            font-weight: 600;
            animation: highlight-pulse 0.5s ease-in-out;
        }
        
        @keyframes highlight-pulse {
            0% { background-color: #FEF3C7; }
            50% { background-color: #FCD34D; }
            100% { background-color: #FEF3C7; }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .pulse-glow {
            animation: pulseGlow 0.6s ease-in-out;
        }
        
        @keyframes pulseGlow {
            0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }
        
        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .scale-102 {
            transform: scale(1.02);
        }
        
        .transition-all {
            transition: all 0.3s ease;
        }
    `;
    document.head.appendChild(style);
}
