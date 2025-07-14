document.addEventListener('DOMContentLoaded', function() {
    // Check if current URL contains logout
    if (window.location.pathname.includes('/logout')) {
        // Redirect to home page after logout
        setTimeout(function() {
            window.location.href = '/';
        }, 500);
    }
});