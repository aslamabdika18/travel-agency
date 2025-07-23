/**
 * Payment Callback Simple Handler
 * 
 * File ini menangani redirect langsung setelah callback pembayaran
 * tanpa menampilkan UI yang kompleks.
 */

// Langsung redirect tanpa menampilkan UI
document.addEventListener('DOMContentLoaded', function() {
    console.log('Payment callback - redirecting immediately');
    
    // Ambil parameter dari URL
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('order_id');
    const transactionStatus = urlParams.get('transaction_status');
    
    // Jika status pembayaran sudah berhasil dari URL, langsung redirect
    if (transactionStatus === 'capture' || transactionStatus === 'settlement') {
        console.log('Payment successful from URL params, redirecting to dashboard');
        window.location.href = '/user/bookings';
        return;
    }
    
    // Jika tidak ada parameter khusus, langsung redirect ke dashboard
    console.log('No specific payment status, redirecting to dashboard');
    setTimeout(function() {
        window.location.href = '/user/bookings';
    }, 1000); // Delay 1 detik untuk memastikan halaman ter-load
});