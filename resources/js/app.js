import './bootstrap';
import './toast';

/**
 * Main JavaScript file for Sumatra Tour Travel
 */

document.addEventListener('DOMContentLoaded', function() {
    // Animation Observer for scroll animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.animate-on-scroll').forEach(item => {
        // Check if element is already in viewport and make it visible immediately
        const rect = item.getBoundingClientRect();
        const isInViewport = rect.top < window.innerHeight && rect.bottom > 0;
        
        if (isInViewport) {
            item.classList.add('visible');
        }
        
        observer.observe(item);
        
        // Pastikan link, button, dan form input di dalam animate-on-scroll tetap bisa diinteraksi
        item.querySelectorAll('a, button, input, select, textarea').forEach(interactive => {
            interactive.style.pointerEvents = 'auto';
        });
    });

    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                navbar.classList.add('bg-neutral', 'bg-opacity-95', 'backdrop-blur-sm', 'shadow-lg', 'py-2');
                navbar.classList.remove('bg-transparent', 'py-4');
            } else {
                if (window.location.pathname === '/' || window.location.pathname === '/home') {
                    navbar.classList.remove('bg-neutral', 'bg-opacity-95', 'backdrop-blur-sm', 'shadow-lg', 'py-2');
                    navbar.classList.add('bg-transparent', 'py-4');
                }
            }
        });

        // Trigger scroll event on page load to set initial navbar state
        window.dispatchEvent(new Event('scroll'));
    }

    // Navbar functionality has been moved to navbar.js

    // FAQ toggle functionality
    window.toggleFaq = function(index) {
        const answer = document.getElementById(`faq-answer-${index}`);
        const icon = document.getElementById(`faq-icon-${index}`);
        
        if (answer && icon) {
            // Toggle the answer visibility
            answer.classList.toggle('hidden');
            
            // Toggle the icon between plus and minus
            if (answer.classList.contains('hidden')) {
                icon.classList.remove('fa-minus');
                icon.classList.add('fa-plus');
            } else {
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
            }
        }
    };

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80, // Adjust for navbar height
                    behavior: 'smooth'
                });
            }
        });
    });
});