// Define routes for the payment callback JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Make routes available globally
    window.paymentCallbackRoutes = {
        success: paymentSuccessRoute,
        error: paymentErrorRoute
    };
});