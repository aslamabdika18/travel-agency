document.addEventListener('DOMContentLoaded', function() {
    // Ensure page starts from top on load (best practice)
    if (history.scrollRestoration) {
        history.scrollRestoration = 'manual';
    }
    window.scrollTo(0, 0);
    
    // Ambil order_id dari URL
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('order_id');
    
    // Fungsi untuk memeriksa status pembayaran real-time
    async function checkPaymentStatus() {
        if (!orderId) {
            console.log('No order_id found, skipping status check');
            return;
        }
        
        try {
            const response = await fetch(`/api/payment/status?order_id=${orderId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.data && data.data.payment) {
                const payment = data.data.payment;
                
                // Jika status bukan 'Paid', redirect ke callback untuk pengecekan lebih lanjut
                 if (payment.status !== 'Paid') {
                     console.log('Payment status is not Paid, redirecting to callback for real-time check');
                     console.log('Current payment status:', payment.status);
                     window.location.href = `/payment/callback?order_id=${orderId}`;
                     return;
                 }
                 
                 // Jika status sudah 'Paid', lanjutkan dengan confetti
                 if (payment.status === 'Paid') {
                     console.log('Payment confirmed as Paid, launching confetti');
                     launchConfetti();
                 }
            }
        } catch (error) {
            console.error('Error checking payment status:', error);
            // Jika ada error, tetap tampilkan confetti (fallback)
            launchConfetti();
        }
    }
    
    // Confetti animation for success page
    function launchConfetti() {
        const duration = 3000;
        const end = Date.now() + duration;
        
        (function frame() {
            // Launch a few confetti from the left edge
            confetti({
                particleCount: 7,
                angle: 60,
                spread: 55,
                origin: { x: 0, y: 0.6 }
            });
            // and launch a few from the right edge
            confetti({
                particleCount: 7,
                angle: 120,
                spread: 55,
                origin: { x: 1, y: 0.6 }
            });
            
            // Keep going until we are out of time
            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        }());
    }
    
    // Check if confetti library is loaded
    if (typeof confetti !== 'undefined') {
        // Periksa status pembayaran terlebih dahulu
        checkPaymentStatus();
    } else {
        // Jika confetti tidak tersedia, langsung periksa status
        checkPaymentStatus();
    }
});