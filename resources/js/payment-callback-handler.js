/**
 * Midtrans Payment Callback Handler
 *
 * File ini menangani callback dari Midtrans setelah proses pembayaran
 * dan mengecek status pembayaran melalui API.
 */

console.log('🔄 Payment Callback Handler loaded successfully');

// Variabel global untuk order ID dan status transaksi
let globalOrderId = null;
let globalTransactionStatus = null;
let retryCount = 0;
const maxRetries = 3;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Payment callback handler loaded - redirecting immediately');
    
    // Ambil order_id dan transaction_status dari URL jika ada
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('order_id');
    const transactionStatus = urlParams.get('transaction_status');

    // Simpan ke variabel global
    globalOrderId = orderId;
    globalTransactionStatus = transactionStatus;

    // Periksa apakah parameter yang diperlukan ada
    if (!orderId) {
        // Coba cari parameter lain yang mungkin berisi order ID
        const possibleOrderIdParams = ['order_id', 'orderId', 'id', 'booking_id'];
        for (const param of possibleOrderIdParams) {
            const value = urlParams.get(param);
            if (value) {
                globalOrderId = value;
                break;
            }
        }
    }

    // Jika status pembayaran sudah berhasil dari URL, langsung redirect
    if (transactionStatus === 'capture' || transactionStatus === 'settlement') {
        console.log('Payment successful from URL params, redirecting immediately to dashboard');
        window.location.href = '/user/bookings';
        return;
    }

    // Langsung cek status pembayaran tanpa menampilkan UI
    checkPaymentStatusAndRedirect();



    // Fungsi untuk memeriksa status pembayaran dan langsung redirect
    async function checkPaymentStatusAndRedirect() {
        console.log('=== checkPaymentStatusAndRedirect function called ===');
        console.log('Current URL:', window.location.href);
        console.log('Global variables:', { globalOrderId, globalTransactionStatus });
        
        try {
            // Jika tidak ada orderId, coba cari dari parameter lain atau gunakan fallback
            let orderIdToUse = globalOrderId;
            console.log('Initial orderIdToUse:', orderIdToUse);

            if (!orderIdToUse) {
                // Coba cari dari parameter lain
                const urlParams = new URLSearchParams(window.location.search);
                const possibleOrderIdParams = ['order_id', 'orderId', 'id', 'booking_id', 'transaction_id'];

                for (const param of possibleOrderIdParams) {
                    const value = urlParams.get(param);
                    if (value) {
                        orderIdToUse = value;
                        globalOrderId = value;
                        break;
                    }
                }

                // Jika masih tidak ada, cek apakah ada di path URL
                if (!orderIdToUse) {
                    const pathParts = window.location.pathname.split('/');
                    const lastPart = pathParts[pathParts.length - 1];

                    if (lastPart && lastPart !== 'callback') {
                        orderIdToUse = lastPart;
                        globalOrderId = lastPart;
                    }
                }

                // Jika masih tidak ada, langsung redirect ke dashboard
                if (!orderIdToUse) {
                    console.log('Order ID tidak ditemukan, redirect ke dashboard');
                    window.location.href = '/user/bookings';
                    return;
                }
            }

            // Panggil API untuk memeriksa status pembayaran
            const apiUrl = `/api/payment/status?order_id=${orderIdToUse}`;
            console.log('Making API call to:', apiUrl);
            
            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            console.log('API response status:', response.status, response.statusText);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('API response data:', JSON.stringify(data, null, 2));

            // Validasi data respons
            if (!data) {
                throw new Error('Respons API tidak berisi data');
            }

            // Redirect berdasarkan status pembayaran - LANGSUNG tanpa delay
            if (data.success && data.data && data.data.payment) {
                const payment = data.data.payment;
                const booking = data.data.booking;

                console.log('Payment status check:', {
                    status: payment.status,
                    gateway_status: payment.gateway_status,
                    payment_id: payment.id,
                    booking_id: booking ? booking.id : 'N/A'
                });

                // Status berhasil: payment_status 'Paid' atau gateway_status 'capture'/'settlement'
                if (payment.status === 'Paid' || 
                    payment.gateway_status === 'capture' || 
                    payment.gateway_status === 'settlement') {
                    console.log('Payment confirmed, redirecting to user bookings');
                    window.location.href = '/user/bookings';
                    return;
                }
                // Status gagal: jika payment_status adalah 'Failed'
                else if (payment.status === 'Failed' ||
                         payment.gateway_status === 'deny' ||
                         payment.gateway_status === 'cancel' ||
                         payment.gateway_status === 'expire' ||
                         payment.gateway_status === 'failure') {
                    console.log('Payment failed, redirecting to error page');
                    const errorUrl = window.paymentCallbackRoutes?.error || '/payment/error';
                    window.location.href = `${errorUrl}?order_id=${orderIdToUse}`;
                    return;
                }
                // Status pending atau unpaid: redirect ke dashboard dengan pesan
                else {
                    console.log('Payment pending/unpaid, redirecting to dashboard');
                    window.location.href = '/user/bookings';
                    return;
                }
            } 
            // Fallback: cek payment_status dari data.data langsung
            else if (data.success && data.data && data.data.payment_status === 'Paid') {
                console.log('Payment confirmed (legacy), redirecting to user bookings');
                window.location.href = '/user/bookings';
                return;
            } 
            // Jika tidak ada data payment yang valid, redirect ke dashboard
            else {
                console.log('No valid payment data found, redirecting to dashboard');
                window.location.href = '/user/bookings';
                return;
            }

        } catch (error) {
            console.log('Error checking payment status:', error.message);
            // Jika ada error, langsung redirect ke dashboard
            window.location.href = '/user/bookings';
        }
    }

});

console.log('🔄 Payment Callback Handler initialized - will redirect immediately on callback');
