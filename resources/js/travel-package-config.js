// Define configuration for the travel package detail JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Make configuration available globally
    window.travelPackageFullConfig = {
        ...window.travelPackageConfig,
        ...window.travelPackageData
    };
});