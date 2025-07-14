/**
 * Midtrans Payment Callback Handler
 * 
 * File ini menangani callback dari Midtrans setelah proses pembayaran
 * dan mengecek status pembayaran melalui API.
 */

// Variabel global untuk order ID dan status transaksi
let globalOrderId = null;
let globalTransactionStatus = null;

document.addEventListener('DOMContentLoaded', function() {
    // Ambil order_id dan transaction_status dari URL jika ada
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('order_id');
    const transactionStatus = urlParams.get('transaction_status');
    
    // Simpan ke variabel global
    globalOrderId = orderId;
    globalTransactionStatus = transactionStatus;
    
    // Log untuk debugging
    
    // Log semua parameter URL untuk debugging
    
    
    // Periksa apakah parameter yang diperlukan ada
    if (!orderId) {
        
        
        // Coba cari parameter lain yang mungkin berisi order ID
        const possibleOrderIdParams = ['order_id', 'orderId', 'id', 'booking_id'];
        for (const param of possibleOrderIdParams) {
            const value = urlParams.get(param);
            if (value) {
                
            }
        }
    }
    
    // Elemen UI
    const progressBar = document.querySelector('.progress-bar');
    const progressText = document.querySelector('.progress-text');
    
    // Langkah-langkah progress
    const progressSteps = [
        { width: '20%', text: 'Menghubungkan ke gateway pembayaran...' },
        { width: '40%', text: 'Memeriksa status transaksi...' },
        { width: '60%', text: 'Memverifikasi pembayaran...' },
        { width: '80%', text: 'Mengonfirmasi booking...' },
        { width: '100%', text: 'Pembayaran dikonfirmasi! Mengalihkan...' }
    ];
    
    let currentStep = 0;
    
    // Fungsi untuk memperbarui progress
    function updateProgress() {
        if (currentStep < progressSteps.length) {
            const step = progressSteps[currentStep];
            
            
            progressBar.style.width = step.width;
            progressText.textContent = step.text;
            
            // Tambahkan animasi smooth untuk progress bar
            progressBar.style.transition = 'width 0.5s ease-in-out';
            
            currentStep++;
        } else {
            
            clearInterval(progressInterval);
            checkPaymentStatus();
        }
    }
    
    // Mulai pembaruan progress
    updateProgress();
    const progressInterval = setInterval(function() {
        if (currentStep < progressSteps.length - 1) { // Jangan update ke langkah terakhir secara otomatis
            updateProgress();
        } else {
            clearInterval(progressInterval);
        }
    }, 1500);
    
    // Fungsi untuk menampilkan detail pembayaran
    function displayPaymentDetails(data) {
        
        
        // Buat container untuk detail pembayaran
        const detailsContainer = document.createElement('div');
        detailsContainer.className = 'mt-8 p-4 bg-white border border-gray-200 rounded-md shadow-sm';
        
        // Siapkan data yang akan ditampilkan
        const payment = data.payment || {};
        const booking = data.booking || {};
        
        // Format tanggal jika ada
        const formatDate = (dateString) => {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        };
        
        // Tentukan status pembayaran dalam bahasa Indonesia
        let statusText = 'Menunggu Pembayaran';
        let statusClass = 'text-yellow-600 bg-yellow-50';
        
        if (payment.status === 'paid' || payment.gateway_status === 'settlement' || payment.gateway_status === 'capture') {
            statusText = 'Pembayaran Berhasil';
            statusClass = 'text-green-600 bg-green-50';
        } else if (payment.gateway_status === 'deny' || payment.gateway_status === 'cancel' || payment.gateway_status === 'expire') {
            statusText = 'Pembayaran Gagal';
            statusClass = 'text-red-600 bg-red-50';
        }
        
        // Buat HTML untuk detail pembayaran
        detailsContainer.innerHTML = `
            <h3 class="text-lg font-semibold mb-4">Detail Pembayaran</h3>
            
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-600">Status:</span>
                <span class="px-3 py-1 rounded-full ${statusClass} font-medium">${statusText}</span>
            </div>
            
            <div class="grid grid-cols-1 gap-3 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">ID Booking:</span>
                    <span class="font-medium">${booking.id || payment.booking_id || '-'}</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Jumlah:</span>
                    <span class="font-medium">Rp ${new Intl.NumberFormat('id-ID').format(payment.total_price || 0)}</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Waktu Pembayaran:</span>
                    <span class="font-medium">${formatDate(payment.paid_at)}</span>
                </div>
                
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">ID Transaksi:</span>
                    <span class="font-medium">${payment.transaction_id || globalOrderId || '-'}</span>
                </div>
            </div>
            
            <div class="mt-4 text-sm text-gray-500">
                <p>Anda akan dialihkan dalam beberapa detik...</p>
            </div>
        `;
        
        // Tambahkan ke halaman
        const container = document.querySelector('.progress-bar').closest('div').parentNode;
        container.appendChild(detailsContainer);
    }
    
    // Fungsi untuk memeriksa status pembayaran
    async function checkPaymentStatus() {
        try {
            // Jika tidak ada orderId, coba cari dari parameter lain atau gunakan fallback
            let orderIdToUse = globalOrderId;
            
            if (!orderIdToUse) {
                
                
                // Coba cari dari parameter lain
                const urlParams = new URLSearchParams(window.location.search);
                const possibleOrderIdParams = ['orderId', 'id', 'booking_id', 'transaction_id'];
                
                for (const param of possibleOrderIdParams) {
                    const value = urlParams.get(param);
                    if (value) {
                        
                        orderIdToUse = value;
                        globalOrderId = value; // Update global variable
                        break;
                    }
                }
                
                // Jika masih tidak ada, cek apakah ada di path URL
                if (!orderIdToUse) {
                    const pathParts = window.location.pathname.split('/');
                    const lastPart = pathParts[pathParts.length - 1];
                    
                    if (lastPart && lastPart !== 'callback') {
                        
                        orderIdToUse = lastPart;
                        globalOrderId = lastPart; // Update global variable
                    }
                }
                
                // Jika masih tidak ada, kita tidak bisa melanjutkan
                if (!orderIdToUse) {
                    
                    handleError('Order ID tidak ditemukan dalam URL');
                    return;
                }
            }
            
            
            
            // Panggil API untuk memeriksa status pembayaran
            const response = await fetch(`/api/payment/status?order_id=${orderIdToUse}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            // Tambahkan timeout untuk menghindari permintaan yang terlalu lama
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Permintaan API timeout setelah 10 detik')), 10000);
            });
            
            // Gunakan Promise.race untuk menerapkan timeout
            const responseWithTimeout = await Promise.race([
                response,
                timeoutPromise
            ]);
            
            
            
            if (!responseWithTimeout.ok) {
                throw new Error(`HTTP error! status: ${responseWithTimeout.status}`);
            }
            
            const data = await responseWithTimeout.json();
            
            // Validasi data respons
            if (!data) {
                throw new Error('Respons API tidak berisi data');
            }
            
            
            // Perbarui ke langkah terakhir
            progressBar.style.width = progressSteps[progressSteps.length - 1].width;
            progressText.textContent = progressSteps[progressSteps.length - 1].text;
            
            // Tampilkan detail pembayaran jika tersedia
            if (data.success && data.data) {
                displayPaymentDetails(data.data);
            }
            
            // Tunggu sebentar sebelum redirect
            setTimeout(() => {
                // Redirect berdasarkan status pembayaran
                if (data.success && data.data && data.data.payment) {
                    const payment = data.data.payment;
                    
                    
                    if (payment.status === 'paid' || 
                        payment.gateway_status === 'settlement' || 
                        payment.gateway_status === 'capture') {
                        
                        window.location.href = window.paymentCallbackRoutes.success;
                    } else {
                        
                        window.location.href = window.paymentCallbackRoutes.error;
                    }
                } else if (data.success && data.data && data.data.payment_status === 'paid') {
                    
                    window.location.href = window.paymentCallbackRoutes.success;
                } else {
                    
                    window.location.href = window.paymentCallbackRoutes.error;
                }
            }, 1500);
            
        } catch (error) {
            
            handleError('Terjadi kesalahan saat memeriksa status pembayaran: ' + error.message);
        }
    }
    
    // Fungsi untuk menangani error
    function handleError(message) {
        clearInterval(progressInterval);
        const errorMessage = message || 'Terjadi kesalahan saat memproses pembayaran';
        
        
        
        // Update UI
        progressText.textContent = errorMessage;
        progressText.classList.add('text-red-600');
        progressBar.classList.remove('bg-blue-600');
        progressBar.classList.add('bg-red-600');
        
        // Tambahkan pesan error tambahan di halaman
        const errorContainer = document.createElement('div');
        errorContainer.className = 'mt-4 p-3 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm';
        errorContainer.innerHTML = `
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Terjadi kesalahan</span>
            </div>
            <p>${errorMessage}</p>
            <p class="mt-2">Anda akan dialihkan ke halaman error dalam 5 detik...</p>
        `;
        
        // Tambahkan ke halaman
        const container = document.querySelector('.progress-bar').closest('div').parentNode;
        container.appendChild(errorContainer);
        
        // Tunggu 5 detik sebelum redirect ke halaman error
        setTimeout(() => {
            
            window.location.href = window.paymentCallbackRoutes.error;
        }, 5000);
    }
    
    // Mulai pemeriksaan status setelah beberapa detik
    setTimeout(checkPaymentStatus, 3000);
    
    // Timeout setelah 30 detik jika tidak ada respons
    setTimeout(function() {
        if (currentStep < progressSteps.length) {
            handleError('Pemrosesan pembayaran memakan waktu lebih lama dari yang diharapkan');
        }
    }, 30000);
});