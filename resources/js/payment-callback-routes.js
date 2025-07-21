// Define routes for the payment callback JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Make routes available globally
    window.paymentCallbackRoutes = {
        success: typeof paymentSuccessRoute !== 'undefined' ? paymentSuccessRoute : '/payment/success',
        error: typeof paymentErrorRoute !== 'undefined' ? paymentErrorRoute : '/payment/error'
    };
});