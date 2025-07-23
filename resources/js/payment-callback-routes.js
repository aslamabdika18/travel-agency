/**
 * Payment Callback Routes Configuration
 * 
 * File ini berisi konfigurasi rute untuk callback pembayaran
 * yang diperlukan oleh payment-callback-handler.js
 */

// Tunggu sampai DOM siap
document.addEventListener('DOMContentLoaded', function() {
    // Definisikan rute global untuk callback pembayaran
    window.paymentCallbackRoutes = {
        success: '/payment/success',
        error: '/payment/error'
    };
    
    console.log('Payment callback routes initialized:', window.paymentCallbackRoutes);
});