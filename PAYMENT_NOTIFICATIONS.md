# Sistem Notifikasi Pembayaran

Sistem notifikasi pembayaran telah diperbarui untuk memberikan notifikasi yang akurat berdasarkan status pembayaran dari Midtrans.

## Fitur Utama

### 1. Notifikasi Email Otomatis
- **PaymentSuccessNotification**: Dikirim ketika pembayaran berhasil (`settlement`, `capture`)
- **PaymentPendingNotification**: Dikirim ketika pembayaran pending (`pending`, `challenge`)
- **PaymentFailedNotification**: Dikirim ketika pembayaran gagal (`deny`, `cancel`, `expire`, `failure`)

### 2. Status Pembayaran Berdasarkan Dokumentasi Midtrans

#### Status Berhasil:
- `settlement`: Pembayaran berhasil dan dana sudah masuk
- `capture`: Pembayaran berhasil (untuk kartu kredit)

#### Status Pending:
- `pending`: Pembayaran sedang diproses
- `challenge`: Pembayaran perlu verifikasi fraud (kartu kredit)

#### Status Gagal:
- `deny`: Pembayaran ditolak
- `cancel`: Pembayaran dibatalkan
- `expire`: Pembayaran kedaluwarsa
- `failure`: Pembayaran gagal

### 3. Notifikasi Email

Setiap notifikasi email berisi:
- Detail booking (referensi, paket wisata, total pembayaran)
- Status pembayaran yang jelas
- Tombol aksi sesuai status:
  - **Berhasil**: "Lihat Detail Booking"
  - **Pending**: "Lanjutkan Pembayaran"
  - **Gagal**: "Coba Bayar Lagi"

### 4. Route untuk Email Actions

```php
// Route untuk retry pembayaran dari email
Route::get('/payment/retry/{payment}', [PageController::class, 'paymentRetry'])
    ->name('payment.retry')
    ->middleware('auth');

// Route untuk melanjutkan pembayaran dari email
Route::get('/payment/continue/{payment}', [PageController::class, 'paymentContinue'])
    ->name('payment.continue')
    ->middleware('auth');
```

### 5. Callback Handler JavaScript

File `payment-callback-handler.js` telah diperbarui untuk:
- Menampilkan pesan status yang lebih informatif
- Menangani redirect berdasarkan status pembayaran
- Menampilkan `gateway_status` untuk informasi detail

## Implementasi

### MidtransService.php

Method `updatePaymentStatus()` telah diperbarui untuk:
1. Menangani semua status Midtrans sesuai dokumentasi
2. Mengirim notifikasi yang tepat berdasarkan status
3. Logging untuk monitoring

### Notifikasi Classes

1. **PaymentSuccessNotification**
   - Channel: mail, database
   - Action: Lihat Detail Booking

2. **PaymentPendingNotification**
   - Channel: mail, database
   - Action: Lanjutkan Pembayaran
   - Info: Batas waktu 24 jam

3. **PaymentFailedNotification**
   - Channel: mail, database
   - Action: Coba Bayar Lagi
   - Info: Alasan kegagalan berdasarkan `gateway_status`

### PageController Methods

1. **paymentRetry()**: Menangani retry pembayaran dari email
2. **paymentContinue()**: Menangani lanjutan pembayaran dari email

Kedua method memiliki:
- Validasi akses user
- Pengecekan status pembayaran
- Redirect yang sesuai dengan pesan toast

## Testing

File test `PaymentNotificationTest.php` tersedia untuk menguji:
- Pengiriman notifikasi berdasarkan status
- Validasi notifikasi yang tepat untuk setiap status
- Integrasi dengan Midtrans Service

## Cara Menjalankan Test

```bash
# Jalankan semua test notifikasi
php artisan test tests/Feature/PaymentNotificationTest.php

# Jalankan test spesifik
php artisan test --filter=it_sends_success_notification_for_settlement_status
```

## Konfigurasi Email

Pastikan konfigurasi email di `.env` sudah benar:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Travel Agency"
```

## Monitoring

Semua aktivitas notifikasi dicatat dalam log Laravel untuk monitoring dan debugging.

---

**Catatan**: Sistem ini mengikuti dokumentasi resmi Midtrans dan best practices Laravel untuk notifikasi email.
