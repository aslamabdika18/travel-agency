# Perbaikan Payment Callback Handler

## Masalah yang Ditemukan

Sebelumnya, ketika transaksi GoPay gagal di dashboard Midtrans sandbox, aplikasi tetap redirect ke halaman sukses dengan status pembayaran "unpaid". Ini terjadi karena beberapa masalah:

### 1. Bug di PaymentController.php
- **Lokasi**: `app/Http/Controllers/PaymentController.php` baris 425
- **Masalah**: `gateway_status` menggunakan fallback ke `transaction_status` yang bisa null
- **Perbaikan**: Menghapus fallback dan hanya menggunakan `gateway_status`

```php
// SEBELUM (BERMASALAH)
'gateway_status' => $payment->gateway_status ?? $payment->transaction_status,

// SESUDAH (DIPERBAIKI)
'gateway_status' => $payment->gateway_status,
```

### 2. Logika Redirect yang Tidak Lengkap
- **Lokasi**: `resources/js/payment-callback-handler.js`
- **Masalah**: Hanya memeriksa status 'paid', 'settlement', 'capture' untuk success
- **Perbaikan**: Menambahkan pengecekan eksplisit untuk status failed

## Perbaikan yang Dilakukan

### 1. Perbaikan Status Mapping

**File**: `app/Http/Controllers/PaymentController.php`
- Menghapus fallback `transaction_status` yang bermasalah
- Menambahkan `transaction_id` ke response untuk debugging

### 2. Perbaikan Logika Redirect

**File**: `resources/js/payment-callback-handler.js`

#### Status Success (Redirect ke halaman sukses):
- `payment.status === 'Paid'`
- `payment.gateway_status === 'settlement'`
- `payment.gateway_status === 'capture'`

#### Status Failed (Redirect ke halaman error):
- `payment.status === 'Failed'`
- `payment.gateway_status === 'deny'`
- `payment.gateway_status === 'cancel'`
- `payment.gateway_status === 'expire'`
- `payment.gateway_status === 'failure'`

#### Status Pending (Retry mechanism):
- Untuk status lainnya, sistem akan retry maksimal 3 kali
- Setiap retry menunggu 5 detik
- Setelah 3 kali retry, redirect ke halaman error

### 3. Penambahan Logging

Ditambahkan console.log untuk debugging:
- Status payment yang diterima
- Keputusan redirect yang diambil
- Informasi retry

## Cara Testing

### 1. Testing Payment Failed

1. **Buat transaksi GoPay baru**
2. **Di Midtrans Dashboard Sandbox**:
   - Buka menu "Transactions"
   - Cari transaksi yang baru dibuat
   - Klik "Actions" > "Update Status"
   - Pilih status "failure", "deny", "cancel", atau "expire"
3. **Kembali ke aplikasi**:
   - Refresh halaman payment callback
   - Sistem seharusnya redirect ke halaman error

### 2. Testing Payment Success

1. **Buat transaksi GoPay baru**
2. **Di Midtrans Dashboard Sandbox**:
   - Update status ke "settlement" atau "capture"
3. **Kembali ke aplikasi**:
   - Sistem seharusnya redirect ke halaman sukses

### 3. Testing Payment Pending

1. **Buat transaksi GoPay baru**
2. **Biarkan status tetap "pending"**
3. **Di aplikasi**:
   - Sistem akan retry 3 kali (setiap 5 detik)
   - Setelah 3 kali retry, redirect ke halaman error

## Monitoring dan Debugging

### 1. Browser Console
Buka Developer Tools > Console untuk melihat log:
```javascript
// Log yang akan muncul:
Payment status check: {status: "Failed", gateway_status: "failure"}
Redirecting to error page - payment failed
```

### 2. Laravel Logs
Cek file `storage/logs/laravel.log` untuk:
- Midtrans notification processing
- Payment status updates
- Error handling

### 3. Database Check
```sql
-- Cek status payment di database
SELECT id, payment_status, gateway_status, transaction_id 
FROM payments 
WHERE booking_id = [BOOKING_ID];

-- Cek booking status
SELECT id, status, payment_status 
FROM bookings 
WHERE id = [BOOKING_ID];
```

## Status Mapping Reference

| Midtrans Status | Payment Status | Gateway Status | Action |
|----------------|----------------|----------------|---------|
| settlement | Paid | settlement | Success Page |
| capture | Paid | capture | Success Page |
| pending | Unpaid | pending | Retry/Wait |
| deny | Failed | deny | Error Page |
| cancel | Failed | cancel | Error Page |
| expire | Failed | expire | Error Page |
| failure | Failed | failure | Error Page |

## Catatan Penting

1. **Sandbox Environment**: Pastikan menggunakan server key sandbox
2. **Webhook URL**: Pastikan webhook Midtrans sudah dikonfigurasi dengan benar
3. **HTTPS**: Untuk production, pastikan menggunakan HTTPS
4. **Timeout**: Sistem memiliki timeout 30 detik untuk callback

## Troubleshooting

### Jika masih redirect ke success padahal failed:
1. Cek browser console untuk error JavaScript
2. Cek response API `/api/payment/status`
3. Pastikan `gateway_status` tidak null di database
4. Cek Laravel logs untuk error processing

### Jika stuck di halaman callback:
1. Cek network tab untuk failed API calls
2. Pastikan order_id ada di URL
3. Cek database apakah payment record ada
4. Restart browser untuk clear cache