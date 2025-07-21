document.addEventListener('DOMContentLoaded', function() {
    // Ensure page starts from top on load (best practice)
    if (history.scrollRestoration) {
        history.scrollRestoration = 'manual';
    }
    window.scrollTo(0, 0);
});

// toggleFaq functionality is handled globally in app.js
// No additional JavaScript needed for this page